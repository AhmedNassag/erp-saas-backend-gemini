<?php

namespace Modules\Inventory\Repositories\Purchase;

use App\Repositories\Base\BaseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\Purchase\Purchase;
use Modules\Inventory\Repositories\Purchase\PurchaseInterface;
use Modules\Inventory\Resources\Purchase\PurchaseResource;
use Modules\Inventory\Repositories\PurchaseDetail\PurchaseDetailRepository;
use Modules\Inventory\Repositories\ProductWarehouse\ProductWarehouseRepository;
use Modules\Inventory\Repositories\Unit\UnitRepository;
use Modules\Inventory\Repositories\Product\ProductRepository;
use Modules\Core\Repositories\Warehouse\WarehouseRepository;
use Modules\Inventory\Models\PaymentPurchase\PaymentPurchase;

class PurchaseRepository extends BaseRepository implements PurchaseInterface
{
    public function __construct(
        private UnitRepository $unitRepository,
        private ProductRepository $productRepository,
        private ProductWarehouseRepository $productWarehouseRepository,
        private WarehouseRepository $warehouseRepository,
        private PurchaseDetailRepository $purchaseDetailsRepository,
    ) {}

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Purchase();
    }

    protected function getResourceClass(): string
    {
        return PurchaseResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Purchases';
    }

    protected function getSingularName(): string
    {
        return 'Purchase';
    }

    

    public function show($id, array $with = [])
    {
        $purchase = $this->getModel()->with(['purchaseDetails', 'provider', 'warehouse', 'user', 'paymentPurchases'])->findOrFail($id);

        $warehouses = $this->warehouseRepository->getModel()->whereNull('deleted_at')->get();
        $providers  = \Modules\Inventory\Models\Provider\Provider::whereNull('deleted_at')->get(['id', 'name']);

        $details = [];
        foreach ($purchase->purchaseDetails as $detail) {
            $product = $detail->product;
            $unit    = $detail->unit ? $detail->unit->shortName : null;

            $pw = $this->productWarehouseRepository->getModel()
                ->whereNull('deleted_at')
                ->where('warehouse_id', $purchase->warehouse_id)
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
                'purchase_id'        => $detail->purchase_id,
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
            ->isOk(__('Purchase Data'))
            ->setData([
                'purchase'   => new PurchaseResource($purchase),
                'providers'  => $providers,
                'warehouses' => $warehouses,
                'details'    => $details,
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

            $purchase = $this->getModel()->create($data);

            $this->storePurchaseDetails($purchase, $request->details);

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Stored Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }

    public function update($id, $request)
    {
        try {
            DB::beginTransaction();

            $purchase   = $this->getModel()->findOrFail($id);
            $oldDetails = $this->purchaseDetailsRepository->getModel()->where('purchase_id', $id)->get();

            // Revert old quantities if purchase was received
            $this->revertPurchaseQuantity($purchase, $oldDetails);

            // Delete old details
            $this->purchaseDetailsRepository->getModel()->where('purchase_id', $id)->delete();

            $data            = $request->validated();
            $data['items']   = sizeof($request['details']);

            // Recalculate payment status
            $due = $data['GrandTotal'] - $purchase->paid_amount;
            if ($due == 0 || $due < 0) {
                $data['payment_status'] = 'paid';
            } elseif ($due != $data['GrandTotal']) {
                $data['payment_status'] = 'partial';
            } else {
                $data['payment_status'] = 'unpaid';
            }

            $purchase->update($data);

            $this->storePurchaseDetails($purchase, $request->details);

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Updated Successfully'))
                ->build();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $purchase   = $this->getModel()->findOrFail($id);
            $oldDetails = $this->purchaseDetailsRepository->getModel()->where('purchase_id', $id)->get();

            // Revert quantities if purchase was received
            $this->revertPurchaseQuantity($purchase, $oldDetails);

            $this->purchaseDetailsRepository->getModel()->where('purchase_id', $id)->delete();

            $purchase->delete();

            // Soft delete related payments
            PaymentPurchase::where('purchase_id', $id)->delete();

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

    private function storePurchaseDetails($purchase, $details)
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

            if ($purchase->status == "received") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $productWarehouse = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $purchase->warehouse_id)
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
                        ->where('warehouse_id', $purchase->warehouse_id)
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

            $purchaseDetails = [
                'purchase_id'        => $purchase->id,
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

            $this->purchaseDetailsRepository->getModel()->create($purchaseDetails);
        }
    }

    private function revertPurchaseQuantity($purchase, $oldDetails)
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

            if ($purchase->status == "received") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $productWarehouse = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $purchase->warehouse_id)
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
                        ->where('warehouse_id', $purchase->warehouse_id)
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
        }
    }
}
