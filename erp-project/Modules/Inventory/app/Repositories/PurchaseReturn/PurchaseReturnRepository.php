<?php

namespace Modules\Inventory\Repositories\PurchaseReturn;

use App\Repositories\Base\BaseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\PurchaseReturn\PurchaseReturn;
use Modules\Inventory\Repositories\PurchaseReturn\PurchaseReturnInterface;
use Modules\Inventory\Resources\PurchaseReturn\PurchaseReturnResource;
use Modules\Inventory\Repositories\PurchaseReturnDetail\PurchaseReturnDetailRepository;
use Modules\Inventory\Repositories\ProductWarehouse\ProductWarehouseRepository;
use Modules\Inventory\Repositories\Unit\UnitRepository;
use Modules\Inventory\Repositories\Product\ProductRepository;
use Modules\Core\Repositories\Warehouse\WarehouseRepository;
use Modules\Inventory\Models\PaymentPurchaseReturn\PaymentPurchaseReturn;

class PurchaseReturnRepository extends BaseRepository implements PurchaseReturnInterface
{
    public function __construct(
        private UnitRepository $unitRepository,
        private ProductRepository $productRepository,
        private ProductWarehouseRepository $productWarehouseRepository,
        private WarehouseRepository $warehouseRepository,
        private PurchaseReturnDetailRepository $purchaseReturnDetailsRepository,
    ) {}

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new PurchaseReturn();
    }

    protected function getResourceClass(): string
    {
        return PurchaseReturnResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Purchase Returns';
    }

    protected function getSingularName(): string
    {
        return 'Purchase Return';
    }

    public function show($id, array $with = [])
    {
        $purchaseReturn = $this->getModel()->with(['purchaseReturnDetails', 'provider', 'warehouse', 'user', 'paymentPurchaseReturns'])->findOrFail($id);

        $warehouses = $this->warehouseRepository->getModel()->whereNull('deleted_at')->get();
        $providers  = \Modules\Inventory\Models\Provider\Provider::whereNull('deleted_at')->get(['id', 'name']);

        $details = [];
        foreach ($purchaseReturn->purchaseReturnDetails as $detail) {
            $product = $detail->product;
            $unit    = $detail->unit ? $detail->unit->shortName : null;

            $pw = $this->productWarehouseRepository->getModel()
                ->whereNull('deleted_at')
                ->where('warehouse_id', $purchaseReturn->warehouse_id)
                ->where('product_id', $detail->product_id);

            if ($detail->product_variant_id) {
                $pw->where('product_variant_id', $detail->product_variant_id);
            } else {
                $pw->whereNull('product_variant_id');
            }

            $pwRecord = $pw->first();
            $current  = $pwRecord ? $pwRecord->qty : 0;

            $details[] = [
                'id'                 => $detail->id,
                'purchase_return_id' => $detail->purchase_return_id,
                'product_id'         => $detail->product_id,
                'product_variant_id' => $detail->product_variant_id,
                'cost'               => $detail->cost,
                'purchase_unit_id'   => $detail->purchase_unit_id,
                'TaxNet'             => $detail->TaxNet,
                'tax_method'         => $detail->tax_method,
                'discount'           => $detail->discount,
                'discount_method'    => $detail->discount_method,
                'quantity'           => $detail->quantity,
                'total'              => $detail->total,
                'name'               => $product ? $product->name : null,
                'code'               => $product ? $product->code : null,
                'unit'               => $unit,
                'current'            => $current,
            ];
        }

        return (new \App\Traits\API)
            ->isOk(__('Purchase Return Data'))
            ->setData([
                'purchase_return' => new PurchaseReturnResource($purchaseReturn),
                'providers'       => $providers,
                'warehouses'      => $warehouses,
                'details'         => $details,
            ])
            ->build();
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $data            = $request->validated();
            $data['items']   = sizeof($request['details']);
            $data['user_id'] = Auth::user()->id;
            $data['paid_amount'] = 0;
            $data['payment_status'] = 'unpaid';

            // Validate stock before creating
            if (($data['status'] ?? '') === 'received') {
                $this->validateStockAgainstDetails($request['warehouse_id'], $request['details']);
            }

            $purchaseReturn = $this->getModel()->create($data);

            $this->storePurchaseReturnDetails($purchaseReturn, $request->details);

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Stored Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError($e->getMessage())
                ->setStatus(422)
                ->build();
        }
    }

    public function update($id, $request)
    {
        try {
            DB::beginTransaction();

            $purchaseReturn = $this->getModel()->findOrFail($id);
            $oldDetails = $this->purchaseReturnDetailsRepository->getModel()->where('purchase_return_id', $id)->get();

            // Revert old quantities if return was received
            $this->revertPurchaseReturnQuantity($purchaseReturn, $oldDetails);

            // Validate stock before applying new details
            if (($purchaseReturn->status === 'received' || ($request['status'] ?? '') === 'received')) {
                $this->validateStockAgainstDetails($purchaseReturn->warehouse_id, $request['details']);
            }

            // Delete old details
            $this->purchaseReturnDetailsRepository->getModel()->where('purchase_return_id', $id)->delete();

            $data            = $request->validated();
            $data['items']   = sizeof($request['details']);

            // Recalculate payment status
            $due = $data['GrandTotal'] - $purchaseReturn->paid_amount;
            if ($due == 0 || $due < 0) {
                $data['payment_status'] = 'paid';
            } elseif ($due != $data['GrandTotal']) {
                $data['payment_status'] = 'partial';
            } else {
                $data['payment_status'] = 'unpaid';
            }

            $purchaseReturn->update($data);

            $this->storePurchaseReturnDetails($purchaseReturn, $request->details);

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Updated Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError($e->getMessage())
                ->setStatus(422)
                ->build();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $purchaseReturn = $this->getModel()->findOrFail($id);
            $oldDetails = $this->purchaseReturnDetailsRepository->getModel()->where('purchase_return_id', $id)->get();

            // Revert quantities if return was received
            $this->revertPurchaseReturnQuantity($purchaseReturn, $oldDetails);

            $this->purchaseReturnDetailsRepository->getModel()->where('purchase_return_id', $id)->delete();

            $purchaseReturn->delete();

            // Soft delete related payments
            PaymentPurchaseReturn::where('purchase_return_id', $id)->delete();

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Destroyed Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }

    // ==================== Private Helpers ====================

    private function storePurchaseReturnDetails($purchaseReturn, $details)
    {
        foreach ($details as $value) {
            $unit = null;
            if (!empty($value['purchase_unit_id'])) {
                $unit = $this->unitRepository->getModel()->where('id', $value['purchase_unit_id'])->first();
            } else {
                $product_unit = $this->productRepository->getModel()->with('unitPurchase')->where('id', $value['product_id'])->first();
                if ($product_unit && $product_unit->unitPurchase) {
                    $unit = $this->unitRepository->getModel()->where('id', $product_unit->unitPurchase->id)->first();
                }
            }

            // For purchase returns, decrease inventory when status is "received"
            if ($purchaseReturn->status == "received") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $productWarehouse = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $purchaseReturn->warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->where('product_variant_id', $value['product_variant_id'])
                        ->first();

                    if ($unit && $productWarehouse) {
                        if ($unit->operator == '/') {
                            $productWarehouse->qty -= $value['quantity'] / $unit->operator_value;
                        } else {
                            $productWarehouse->qty -= $value['quantity'] * $unit->operator_value;
                        }
                        $productWarehouse->save();
                    }
                } else {
                    $productWarehouse = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $purchaseReturn->warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->first();

                    if ($unit && $productWarehouse) {
                        if ($unit->operator == '/') {
                            $productWarehouse->qty -= $value['quantity'] / $unit->operator_value;
                        } else {
                            $productWarehouse->qty -= $value['quantity'] * $unit->operator_value;
                        }
                        $productWarehouse->save();
                    }
                }
            }

            $purchaseReturnDetails = [
                'purchase_return_id' => $purchaseReturn->id,
                'product_id'         => $value['product_id'],
                'product_variant_id' => isset($value['product_variant_id']) ? $value['product_variant_id'] : null,
                'cost'               => $value['cost'] ?? 0,
                'purchase_unit_id'   => $value['purchase_unit_id'] ?? null,
                'TaxNet'             => $value['TaxNet'] ?? 0,
                'tax_method'         => $value['tax_method'] ?? '1',
                'discount'           => $value['discount'] ?? 0,
                'discount_method'    => $value['discount_method'] ?? '1',
                'quantity'           => $value['quantity'],
                'total'              => $value['total'] ?? 0,
            ];

            $this->purchaseReturnDetailsRepository->getModel()->create($purchaseReturnDetails);
        }
    }

    private function validateStockAgainstDetails($warehouseId, $details)
    {
        $errors = [];
        foreach ($details as $index => $value) {
            $unit = null;
            if (!empty($value['purchase_unit_id'])) {
                $unit = $this->unitRepository->getModel()->where('id', $value['purchase_unit_id'])->first();
            } else {
                $product_unit = $this->productRepository->getModel()->with('unitPurchase')->where('id', $value['product_id'])->first();
                if ($product_unit && $product_unit->unitPurchase) {
                    $unit = $this->unitRepository->getModel()->where('id', $product_unit->unitPurchase->id)->first();
                }
            }

            $query = $this->productWarehouseRepository->getModel()
                ->whereNull('deleted_at')
                ->where('warehouse_id', $warehouseId)
                ->where('product_id', $value['product_id']);

            if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                $query->where('product_variant_id', $value['product_variant_id']);
            } else {
                $query->whereNull('product_variant_id');
            }

            $productWarehouse = $query->first();
            $availableQty = $productWarehouse ? $productWarehouse->qty : 0;

            if ($unit) {
                if ($unit->operator == '/') {
                    $baseQty = $value['quantity'] / $unit->operator_value;
                } else {
                    $baseQty = $value['quantity'] * $unit->operator_value;
                }
            } else {
                $baseQty = $value['quantity'];
            }

            if ($baseQty > $availableQty) {
                $product = $this->productRepository->getModel()->find($value['product_id']);
                $productName = $product ? $product->name : "Product #{$value['product_id']}";
                $errors[] = "{$productName}: requested return quantity ({$baseQty}) exceeds available stock ({$availableQty}) in the selected warehouse.";
            }
        }

        if (!empty($errors)) {
            throw new \Exception(implode(' ', $errors));
        }
    }

    private function revertPurchaseReturnQuantity($purchaseReturn, $oldDetails)
    {
        foreach ($oldDetails as $value) {
            $unit = null;
            if (!empty($value['purchase_unit_id'])) {
                $unit = $this->unitRepository->getModel()->where('id', $value['purchase_unit_id'])->first();
            } else {
                $product_unit = $this->productRepository->getModel()->with('unitPurchase')->where('id', $value['product_id'])->first();
                if ($product_unit && $product_unit->unitPurchase) {
                    $unit = $this->unitRepository->getModel()->where('id', $product_unit->unitPurchase->id)->first();
                }
            }

            // For purchase returns, reverting means adding back to inventory
            if ($purchaseReturn->status == "received") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $productWarehouse = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $purchaseReturn->warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->where('product_variant_id', $value['product_variant_id'])
                        ->first();

                    if ($unit && $productWarehouse) {
                        if ($unit->operator == '/') {
                            $productWarehouse->qty += $value['quantity'] / $unit->operator_value;
                        } else {
                            $productWarehouse->qty += $value['quantity'] * $unit->operator_value;
                        }
                        $productWarehouse->save();
                    }
                } else {
                    $productWarehouse = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $purchaseReturn->warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->first();

                    if ($unit && $productWarehouse) {
                        if ($unit->operator == '/') {
                            $productWarehouse->qty += $value['quantity'] / $unit->operator_value;
                        } else {
                            $productWarehouse->qty += $value['quantity'] * $unit->operator_value;
                        }
                        $productWarehouse->save();
                    }
                }
            }
        }
    }
}
