<?php

namespace Modules\Inventory\Repositories\Transfer;

use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\Transfer\Transfer;
use Modules\Inventory\Repositories\Transfer\TransferInterface;
use Modules\Inventory\Resources\Transfer\TransferResource;
use Modules\Core\Repositories\Warehouse\WarehouseRepository;
use Modules\Inventory\Repositories\TransferDetail\TransferDetailRepository;
use Modules\Inventory\Repositories\ProductWarehouse\ProductWarehouseRepository;
use Modules\Inventory\Repositories\Product\ProductRepository;
use Modules\Inventory\Repositories\Unit\UnitRepository;

class TransferRepository extends BaseRepository implements TransferInterface
{
    public function __construct(
        private UnitRepository $unitRepository,
        private ProductRepository $productRepository,
        private ProductWarehouseRepository $productWarehouseRepository,
        private WarehouseRepository $warehouseRepository,
        private TransferDetailRepository $transferDetailsRepository,
    ) {}

    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Transfer();
    }

    protected function getResourceClass(): string
    {
        return TransferResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Transfers';
    }

    protected function getSingularName(): string
    {
        return 'Transfer';
    }

    public function show($id, array $with = [])
    {
        $transfer   = $this->getModel()->with(['transferDetails', 'fromWarehouse', 'toWarehouse', 'user'])->findOrFail($id);
        $warehouses = $this->warehouseRepository->getModel()->whereNull('deleted_at')->get();

        $details = [];
        foreach ($transfer->transferDetails as $detail) {
            $product = $detail->product;
            $unit    = $detail->unit ? $detail->unit->shortName : null;

            $details[] = [
                'id'                 => $detail->id,
                'transfer_id'        => $detail->transfer_id,
                'product_id'         => $detail->product_id,
                'product_variant_id' => $detail->product_variant_id,
                'cost'               => $detail->cost,
                'TaxNet'             => $detail->TaxNet,
                'tax_method'         => $detail->tax_method,
                'discount'           => $detail->discount,
                'discount_method'    => $detail->discount_method,
                'quantity'           => $detail->quantity,
                'purchase_unit_id'   => $detail->purchase_unit_id,
                'total'              => $detail->total,
                'name'               => $product ? $product->name : null,
                'code'               => $product ? $product->code : null,
                'unit'               => $unit,
            ];
        }

        return (new \App\Traits\API)
            ->isOk(__('Transfer Data'))
            ->setData([
                'transfer'   => new TransferResource($transfer),
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

            $transfer = $this->getModel()->create($data);

            $this->storeTransferDetails($transfer, $request->details);

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

            $transfer   = $this->getModel()->findOrFail($id);
            $oldDetails = $this->transferDetailsRepository->getModel()->where('transfer_id', $id)->get();

            $this->adjustWarehouseQuantity($transfer, $oldDetails);

            $this->transferDetailsRepository->getModel()->where('transfer_id', $id)->delete();

            $data            = $request->validated();
            $data['items']   = sizeof($request['details']);

            $transfer->update($data);

            $this->storeTransferDetails($transfer, $request->details);

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

            $transfer   = $this->getModel()->findOrFail($id);
            $oldDetails = $this->transferDetailsRepository->getModel()->where('transfer_id', $id)->get();

            $this->adjustWarehouseQuantity($transfer, $oldDetails);

            $this->transferDetailsRepository->getModel()->where('transfer_id', $id)->delete();

            $transfer->delete();

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
    private function storeTransferDetails($transfer, $details)
    {
        try {
            foreach ($details as $value) {
                $unit = $this->unitRepository->getModel()->where('id', $value['purchase_unit_id'])->first();
                if ($transfer->status == "completed") {
                    if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                        //decrease the quantity from warehouse (from_warehouse)
                        $product_warehouse_from = $this->productWarehouseRepository->getModel()
                            ->whereNull('deleted_at')
                            ->where('warehouse_id', $transfer->from_warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($unit && $product_warehouse_from) {
                            if ($unit->operator == '/') {
                                $product_warehouse_from->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_from->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_from->save();
                        }

                        //increase the quantity to warehouse (to_warehouse)
                        $product_warehouse_to = $this->productWarehouseRepository->getModel()
                            ->whereNull('deleted_at')
                            ->where('warehouse_id', $transfer->to_warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($unit && $product_warehouse_to) {
                            if ($unit->operator == '/') {
                                $product_warehouse_to->qty += $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_to->qty += $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_to->save();
                        }
                    } else {
                        //decrease the quantity from warehouse (from_warehouse)
                        $product_warehouse_from = $this->productWarehouseRepository->getModel()
                            ->whereNull('deleted_at')
                            ->where('warehouse_id', $transfer->from_warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();

                        if ($unit && $product_warehouse_from) {
                            if ($unit->operator == '/') {
                                $product_warehouse_from->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_from->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_from->save();
                        }

                        //increase the quantity to warehouse (to_warehouse)
                        $product_warehouse_to = $this->productWarehouseRepository->getModel()
                            ->whereNull('deleted_at')
                            ->where('warehouse_id', $transfer->to_warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();

                        if ($unit && $product_warehouse_to) {
                            if ($unit->operator == '/') {
                                $product_warehouse_to->qty += $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_to->qty += $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_to->save();
                        }
                    }
                } elseif ($transfer->status == "sent") {
                    if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                        $product_warehouse_from = $this->productWarehouseRepository->getModel()
                            ->whereNull('deleted_at')
                            ->where('warehouse_id', $transfer->from_warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($unit && $product_warehouse_from) {
                            if ($unit->operator == '/') {
                                $product_warehouse_from->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_from->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_from->save();
                        }
                    } else {
                        $product_warehouse_from = $this->productWarehouseRepository->getModel()
                            ->whereNull('deleted_at')
                            ->where('warehouse_id', $transfer->from_warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();

                        if ($unit && $product_warehouse_from) {
                            if ($unit->operator == '/') {
                                $product_warehouse_from->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_from->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_from->save();
                        }
                    }
                }

                $transferDetails['transfer_id']        = $transfer->id;
                $transferDetails['product_id']         = $value['product_id'];
                $transferDetails['product_variant_id'] = isset($value['product_variant_id']) ? $value['product_variant_id'] : null;
                $transferDetails['purchase_unit_id']   = $value['purchase_unit_id'] ?? null;
                $transferDetails['quantity']           = $value['quantity'];
                $transferDetails['cost']               = $value['cost'] ?? 0;
                $transferDetails['TaxNet']             = $value['TaxNet'] ?? 0;
                $transferDetails['tax_method']         = $value['tax_method'] ?? '1';
                $transferDetails['discount']           = $value['discount'] ?? 0;
                $transferDetails['discount_method']    = $value['discount_method'] ?? '1';
                $transferDetails['total']              = $value['total'] ?? 0;

                $this->transferDetailsRepository->getModel()->insert($transferDetails);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }



    private function adjustWarehouseQuantity($transfer, $oldDetails)
    {
        // Init Data with old Parametre
        foreach ($oldDetails as $key => $value) {
            //check if detail has purchase_unit_id Or Null
            if($value['purchase_unit_id'] !== null){
                $unit = $this->unitRepository->getModel()->where('id', $value['purchase_unit_id'])->first();
            } else {
                $product_unit_purchase_id = $this->productRepository->getModel()->with('unitPurchase')->where('id', $value['product_id'])->first();
                $unit                     = $this->unitRepository->getModel()->where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
            } 

            if ($transfer->status == "completed") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $warehouse_from_variant = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $transfer->from_warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->where('product_variant_id', $value['product_variant_id'])
                        ->first();

                    if ($unit && $warehouse_from_variant) {
                        if ($unit->operator == '/') {
                            $warehouse_from_variant->qty += $value['quantity'] / $unit->operator_value;
                        } else {
                            $warehouse_from_variant->qty += $value['quantity'] * $unit->operator_value;
                        }
                        $warehouse_from_variant->save();
                    }

                    $warehouse_to_variant = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $transfer->to_warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->where('product_variant_id', $value['product_variant_id'])
                        ->first();

                    if ($unit && $warehouse_to_variant) {
                        if ($unit->operator == '/') {
                            $warehouse_to_variant->qty -= $value['quantity'] / $unit->operator_value;
                        } else {
                            $warehouse_to_variant->qty -= $value['quantity'] * $unit->operator_value;
                        }
                        $warehouse_to_variant->save();
                    }

                } else {
                    $warehouse_from = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $transfer->from_warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->first();

                    if ($unit && $warehouse_from) {
                        if ($unit->operator == '/') {
                            $warehouse_from->qty += $value['quantity'] / $unit->operator_value;
                        } else {
                            $warehouse_from->qty += $value['quantity'] * $unit->operator_value;
                        }
                        $warehouse_from->save();
                    }

                    $warehouse_to = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $transfer->to_warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->first();

                    if ($unit && $warehouse_to) {
                        if ($unit->operator == '/') {
                            $warehouse_to->qty -= $value['quantity'] / $unit->operator_value;
                        } else {
                            $warehouse_to->qty -= $value['quantity'] * $unit->operator_value;
                        }
                        $warehouse_to->save();
                    }
                }

            } elseif ($transfer->status == "sent") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $sent_variant_to = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $transfer->from_warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->where('product_variant_id', $value['product_variant_id'])
                        ->first();

                    if ($unit && $sent_variant_to) {
                        if ($unit->operator == '/') {
                            $sent_variant_to->qty += $value['quantity'] / $unit->operator_value;
                        } else {
                            $sent_variant_to->qty += $value['quantity'] * $unit->operator_value;
                        }
                        $sent_variant_to->save();
                    }
                } else {
                    $sent_variant_from = $this->productWarehouseRepository->getModel()
                        ->whereNull('deleted_at')
                        ->where('warehouse_id', $transfer->from_warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->first();

                    if ($unit && $sent_variant_from) {
                        if ($unit->operator == '/') {
                            $sent_variant_from->qty += $value['quantity'] / $unit->operator_value;
                        } else {
                            $sent_variant_from->qty += $value['quantity'] * $unit->operator_value;
                        }
                        $sent_variant_from->save();
                    }
                }
            }
        }
    }
}
