<?php

namespace Modules\Inventory\Repositories\Sale;

use App\Repositories\Base\BaseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\Sale\Sale;
use Modules\Inventory\Repositories\Sale\SaleInterface;
use Modules\Inventory\Resources\Sale\SaleResource;
use Modules\Inventory\Repositories\SaleDetail\SaleDetailRepository;
use Modules\Inventory\Repositories\ProductWarehouse\ProductWarehouseRepository;
use Modules\Inventory\Repositories\Unit\UnitRepository;
use Modules\Inventory\Repositories\Product\ProductRepository;
use Modules\Core\Repositories\Warehouse\WarehouseRepository;
use Modules\Inventory\Models\PaymentSale\PaymentSale;

class SaleRepository extends BaseRepository implements SaleInterface
{
    public function __construct(
        private UnitRepository $unitRepository,
        private ProductRepository $productRepository,
        private ProductWarehouseRepository $productWarehouseRepository,
        private WarehouseRepository $warehouseRepository,
        private SaleDetailRepository $saleDetailsRepository,
    ) {}

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Sale();
    }

    protected function getResourceClass(): string
    {
        return SaleResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Sales';
    }

    protected function getSingularName(): string
    {
        return 'Sale';
    }

    public function show($id, array $with = [])
    {
        $sale = $this->getModel()->with(['saleDetails', 'client', 'warehouse', 'user', 'paymentSales'])->findOrFail($id);

        $warehouses = $this->warehouseRepository->getModel()->whereNull('deleted_at')->get();
        $clients    = \Modules\Inventory\Models\Client\Client::whereNull('deleted_at')->get(['id', 'name']);

        $details = [];
        foreach ($sale->saleDetails as $detail) {
            $product = $detail->product;
            $unit    = $detail->unit ? $detail->unit->shortName : null;

            $pw = $this->productWarehouseRepository->getModel()
                ->whereNull('deleted_at')
                ->where('warehouse_id', $sale->warehouse_id)
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
                'sale_id'            => $detail->sale_id,
                'product_id'         => $detail->product_id,
                'product_variant_id' => $detail->product_variant_id,
                'price'              => $detail->price,
                'sale_unit_id'       => $detail->sale_unit_id,
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
            ->isOk(__('Sale Data'))
            ->setData([
                'sale'       => new SaleResource($sale),
                'clients'    => $clients,
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

            $this->validateStockForSale($data['warehouse_id'], $request->details);

            $sale = $this->getModel()->create($data);

            $this->storeSaleDetails($sale, $request->details);

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

            $sale       = $this->getModel()->findOrFail($id);
            $oldDetails = $this->saleDetailsRepository->getModel()->where('sale_id', $id)->get();

            // Revert old quantities if sale was completed
            $this->revertSaleQuantity($sale, $oldDetails);

            // Delete old details
            $this->saleDetailsRepository->getModel()->where('sale_id', $id)->delete();

            $data            = $request->validated();
            $data['items']   = sizeof($request['details']);

            $this->validateStockForSale($data['warehouse_id'], $request->details);

            // Recalculate payment status
            $due = $data['GrandTotal'] - $sale->paid_amount;
            if ($due == 0 || $due < 0) {
                $data['payment_status'] = 'paid';
            } elseif ($due != $data['GrandTotal']) {
                $data['payment_status'] = 'partial';
            } else {
                $data['payment_status'] = 'unpaid';
            }

            $sale->update($data);

            $this->storeSaleDetails($sale, $request->details);

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

            $sale       = $this->getModel()->findOrFail($id);
            $oldDetails = $this->saleDetailsRepository->getModel()->where('sale_id', $id)->get();

            // Revert quantities if sale was completed
            $this->revertSaleQuantity($sale, $oldDetails);

            $this->saleDetailsRepository->getModel()->where('sale_id', $id)->delete();

            $sale->delete();

            // Soft delete related payments
            PaymentSale::where('sale_id', $id)->delete();

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

    private function storeSaleDetails($sale, $details)
    {
        foreach ($details as $value) {
            $unit = null;
            if (!empty($value['sale_unit_id'])) {
                $unit = $this->unitRepository->getModel()->where('id', $value['sale_unit_id'])->first();
            } else {
                $product_unit = $this->productRepository->getModel()->with('unitSale')->where('id', $value['product_id'])->first();
                if ($product_unit && $product_unit->unitSale) {
                    $unit = $this->unitRepository->getModel()->where('id', $product_unit->unitSale->id)->first();
                }
            }

            // For sales, decrease inventory when status is "completed"
            if ($sale->status == "completed") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $productWarehouse = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $sale->warehouse_id)
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
                        ->where('warehouse_id', $sale->warehouse_id)
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

            $saleDetails = [
                'sale_id'            => $sale->id,
                'product_id'         => $value['product_id'],
                'product_variant_id' => isset($value['product_variant_id']) ? $value['product_variant_id'] : null,
                'price'              => $value['price'] ?? 0,
                'sale_unit_id'       => $value['sale_unit_id'] ?? null,
                'TaxNet'             => $value['TaxNet'] ?? 0,
                'tax_method'         => $value['tax_method'] ?? '1',
                'discount'           => $value['discount'] ?? 0,
                'discount_method'    => $value['discount_method'] ?? '1',
                'quantity'           => $value['quantity'],
                'total'              => $value['total'] ?? 0,
            ];

            $this->saleDetailsRepository->getModel()->create($saleDetails);
        }
    }

    private function validateStockForSale($warehouseId, $details, $oldDetails = [])
    {
        foreach ($details as $detail) {
            $productId = $detail['product_id'];
            $productVariantId = $detail['product_variant_id'] ?? null;
            $requestedQty = $detail['quantity'];
            $saleUnitId = $detail['sale_unit_id'] ?? null;

            $productWarehouse = $this->productWarehouseRepository->getModel()
                ->whereNull('deleted_at')
                ->where('warehouse_id', $warehouseId)
                ->where('product_id', $productId);

            if ($productVariantId) {
                $productWarehouse->where('product_variant_id', $productVariantId);
            } else {
                $productWarehouse->whereNull('product_variant_id');
            }

            $pwRecord = $productWarehouse->first();
            $availableBaseQty = $pwRecord ? $pwRecord->qty : 0;

            // Convert requested qty to base unit
            $unit = null;
            if (!empty($saleUnitId)) {
                $unit = $this->unitRepository->getModel()->where('id', $saleUnitId)->first();
            } else {
                $product_unit = $this->productRepository->getModel()->with('unitSale')->where('id', $productId)->first();
                if ($product_unit && $product_unit->unitSale) {
                    $unit = $product_unit->unitSale;
                }
            }

            $requestedBaseQty = $requestedQty;
            if ($unit) {
                if ($unit->operator == '/') {
                    $requestedBaseQty = $requestedQty / $unit->operator_value;
                } else {
                    $requestedBaseQty = $requestedQty * $unit->operator_value;
                }
            }

            if ($availableBaseQty <= 0) {
                $product = $this->productRepository->getModel()->find($productId);
                $productName = $product ? $product->name : 'Product';
                throw new \Exception(__(":product has no available stock in this warehouse.", ['product' => $productName]));
            }

            if ($requestedBaseQty > $availableBaseQty) {
                $product = $this->productRepository->getModel()->find($productId);
                $productName = $product ? $product->name : 'Product';
                throw new \Exception(__("Insufficient stock for :product. Available: :available, Requested: :requested.", [
                    'product' => $productName,
                    'available' => $availableBaseQty,
                    'requested' => $requestedBaseQty,
                ]));
            }
        }
    }

    private function revertSaleQuantity($sale, $oldDetails)
    {
        foreach ($oldDetails as $value) {
            $unit = null;
            if (!empty($value['sale_unit_id'])) {
                $unit = $this->unitRepository->getModel()->where('id', $value['sale_unit_id'])->first();
            } else {
                $product_unit = $this->productRepository->getModel()->with('unitSale')->where('id', $value['product_id'])->first();
                if ($product_unit && $product_unit->unitSale) {
                    $unit = $this->unitRepository->getModel()->where('id', $product_unit->unitSale->id)->first();
                }
            }

            // For sales, reverting means adding back to inventory
            if ($sale->status == "completed") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $productWarehouse = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $sale->warehouse_id)
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
                        ->where('warehouse_id', $sale->warehouse_id)
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
