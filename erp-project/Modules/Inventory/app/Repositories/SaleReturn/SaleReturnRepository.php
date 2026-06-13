<?php

namespace Modules\Inventory\Repositories\SaleReturn;

use App\Repositories\Base\BaseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\SaleReturn\SaleReturn;
use Modules\Inventory\Repositories\SaleReturn\SaleReturnInterface;
use Modules\Inventory\Resources\SaleReturn\SaleReturnResource;
use Modules\Inventory\Repositories\SaleReturnDetail\SaleReturnDetailRepository;
use Modules\Inventory\Repositories\ProductWarehouse\ProductWarehouseRepository;
use Modules\Inventory\Repositories\Unit\UnitRepository;
use Modules\Inventory\Repositories\Product\ProductRepository;
use Modules\Core\Repositories\Warehouse\WarehouseRepository;
use Modules\Inventory\Models\PaymentSaleReturn\PaymentSaleReturn;

class SaleReturnRepository extends BaseRepository implements SaleReturnInterface
{
    public function __construct(
        private UnitRepository $unitRepository,
        private ProductRepository $productRepository,
        private ProductWarehouseRepository $productWarehouseRepository,
        private WarehouseRepository $warehouseRepository,
        private SaleReturnDetailRepository $saleReturnDetailsRepository,
    ) {}

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new SaleReturn();
    }

    protected function getResourceClass(): string
    {
        return SaleReturnResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Sale Returns';
    }

    protected function getSingularName(): string
    {
        return 'Sale Return';
    }

    public function show($id, array $with = [])
    {
        $saleReturn = $this->getModel()->with(['saleReturnDetails', 'client', 'warehouse', 'user', 'paymentSaleReturns'])->findOrFail($id);

        $warehouses = $this->warehouseRepository->getModel()->whereNull('deleted_at')->get();
        $clients    = \Modules\Inventory\Models\Client\Client::whereNull('deleted_at')->get(['id', 'name']);

        $details = [];
        foreach ($saleReturn->saleReturnDetails as $detail) {
            $product = $detail->product;
            $unit    = $detail->unit ? $detail->unit->shortName : null;

            $pw = $this->productWarehouseRepository->getModel()
                ->whereNull('deleted_at')
                ->where('warehouse_id', $saleReturn->warehouse_id)
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
                'sale_return_id'     => $detail->sale_return_id,
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
            ->isOk(__('Sale Return Data'))
            ->setData([
                'sale_return' => new SaleReturnResource($saleReturn),
                'clients'     => $clients,
                'warehouses'  => $warehouses,
                'details'     => $details,
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

            $saleReturn = $this->getModel()->create($data);

            $this->storeSaleReturnDetails($saleReturn, $request->details);

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

            $saleReturn = $this->getModel()->findOrFail($id);
            $oldDetails = $this->saleReturnDetailsRepository->getModel()->where('sale_return_id', $id)->get();

            $this->revertSaleReturnQuantity($saleReturn, $oldDetails);

            $this->saleReturnDetailsRepository->getModel()->where('sale_return_id', $id)->delete();

            $data            = $request->validated();
            $data['items']   = sizeof($request['details']);

            $due = $data['GrandTotal'] - $saleReturn->paid_amount;
            if ($due == 0 || $due < 0) {
                $data['payment_status'] = 'paid';
            } elseif ($due != $data['GrandTotal']) {
                $data['payment_status'] = 'partial';
            } else {
                $data['payment_status'] = 'unpaid';
            }

            $saleReturn->update($data);

            $this->storeSaleReturnDetails($saleReturn, $request->details);

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

            $saleReturn = $this->getModel()->findOrFail($id);
            $oldDetails = $this->saleReturnDetailsRepository->getModel()->where('sale_return_id', $id)->get();

            $this->revertSaleReturnQuantity($saleReturn, $oldDetails);

            $this->saleReturnDetailsRepository->getModel()->where('sale_return_id', $id)->delete();

            $saleReturn->delete();

            PaymentSaleReturn::where('sale_return_id', $id)->delete();

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

    private function storeSaleReturnDetails($saleReturn, $details)
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

            if ($saleReturn->status == "received") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $productWarehouse = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $saleReturn->warehouse_id)
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
                        ->where('warehouse_id', $saleReturn->warehouse_id)
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

            $saleReturnDetails = [
                'sale_return_id'     => $saleReturn->id,
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

            $this->saleReturnDetailsRepository->getModel()->create($saleReturnDetails);
        }
    }

    private function revertSaleReturnQuantity($saleReturn, $oldDetails)
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

            if ($saleReturn->status == "received") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $productWarehouse = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $saleReturn->warehouse_id)
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
                        ->where('warehouse_id', $saleReturn->warehouse_id)
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
