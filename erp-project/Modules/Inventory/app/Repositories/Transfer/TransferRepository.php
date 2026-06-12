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

    protected function getTransferDetailsRepository()
    {
        return new TransferDetailRepository();
    }

    protected function getProductWarehouseRepository()
    {
        return new ProductWarehouseRepository();
    }

    protected function getWarehouseRepository()
    {
        return new WarehouseRepository();
    }

    protected function getUnitRepository()
    {
        return new UnitRepository();
    }

    public function show($id, array $with = [])
    {
        $transfer = $this->getModel()->with(['transferDetails', 'fromWarehouse', 'toWarehouse', 'user'])->findOrFail($id);

        $warehouses = $this->getWarehouseRepository()->getModel()
            ->whereNull('deleted_at')->get();

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

    protected function getProductRepository()
    {
        return new ProductRepository();
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
            $oldDetails = $this->getTransferDetailsRepository()->getModel()->where('transfer_id', $id)->get();

            //reverse stock movements for old details
            $this->updateTransferDetails($transfer, $oldDetails, $request);

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
            $oldDetails = $this->getTransferDetailsRepository()->getModel()->where('transfer_id', $id)->get();

            //reverse stock movements (same logic as revert in update)
            $this->adjustWarehouseQuantity($transfer, $oldDetails);

            $this->getTransferDetailsRepository()->getModel()->where('transfer_id', $id)->delete();
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
                $unit = $this->getUnitRepository()->getModel()->where('id', $value['purchase_unit_id'])->first();
                if ($transfer->status == "completed") {
                    if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                        //decrease the quantity from warehouse (from_warehouse)
                        $product_warehouse_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
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
                        $product_warehouse_to = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
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
                        $product_warehouse_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                            ->where('warehouse_id', $transfer->from_warehouse_id)
                            ->where('product_id', $value['product_id'])->first();

                        if ($unit && $product_warehouse_from) {
                            if ($unit->operator == '/') {
                                $product_warehouse_from->qty -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse_from->qty -= $value['quantity'] * $unit->operator_value;
                            }
                            $product_warehouse_from->save();
                        }

                        //increase the quantity to warehouse (to_warehouse)
                        $product_warehouse_to = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                            ->where('warehouse_id', $transfer->to_warehouse_id)
                            ->where('product_id', $value['product_id'])->first();

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
                        $product_warehouse_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
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
                        $product_warehouse_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                            ->where('warehouse_id', $transfer->from_warehouse_id)
                            ->where('product_id', $value['product_id'])->first();

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

                $this->getTransferDetailsRepository()->getModel()->insert($transferDetails);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }



    private function updateTransferDetails($transfer, $oldDetails, $newDetails)
    {
        try {
            $newDetails = $request['details'];
            $trans      = $request->transfer;

            // Get Ids details
            $new_products_id = [];
            foreach ($newDetails as $new_detail) {
                $new_products_id[] = $new_detail['id'];
            }

            // Init Data with old Parametre
            $old_products_id = [];
            foreach ($oldDetails as $key => $value) {
                //check if detail has purchase_unit_id Or Null
                if($value['purchase_unit_id'] !== null) {
                    $unit = $this->getUnitRepository()->getModel()->where('id', $value['purchase_unit_id'])->first();
                } else {
                    $product_unit_purchase_id = $this->getProductRepository()->getModel()->with('unitPurchase')->where('id', $value['product_id'])->first();
                    $unit                     = $this->getUnitRepository()->getModel()->where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
                }

                $old_products_id[] = $value->id;

                if($value['purchase_unit_id'] !== null) {
                    if ($transfer->status == "completed") {
                        if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                            $warehouse_from_variant = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
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

                            $warehouse_to_variant = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
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
                            $warehouse_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                                ->where('warehouse_id', $transfer->from_warehouse_id)
                                ->where('product_id', $value['product_id'])->first();

                            if ($unit && $warehouse_from) {
                                if ($unit->operator == '/') {
                                    $warehouse_from->qty += $value['quantity'] / $unit->operator_value;
                                } else {
                                    $warehouse_from->qty += $value['quantity'] * $unit->operator_value;
                                }
                                $warehouse_from->save();
                            }

                            $warehouse_To = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                                ->where('warehouse_id', $transfer->to_warehouse_id)
                                ->where('product_id', $value['product_id'])->first();

                            if ($unit && $warehouse_To) {
                                if ($unit->operator == '/') {
                                    $warehouse_To->qty -= $value['quantity'] / $unit->operator_value;
                                } else {
                                    $warehouse_To->qty -= $value['quantity'] * $unit->operator_value;
                                }
                                $warehouse_To->save();
                            }
                        }

                    } elseif ($transfer->status == "sent") {
                        if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {

                            $sent_variant_to = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
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
                            $sent_variant_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                                ->where('warehouse_id', $transfer->from_warehouse_id)
                                ->where('product_id', $value['product_id'])->first();

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

                    // Delete Detail
                    if (!in_array($old_products_id[$key], $new_products_id)) {
                        $transferDetail = $this->getTransferDetailsRepository()->getModel()->findOrFail($value->id);
                        $transferDetail->delete();
                    }
                }
            }

            // Update Data with New request
            foreach ($data as $key => $product_detail) {
                if($product_detail['no_unit'] !== 0){
                    $unit = $this->getUnitRepository()->getModel()->where('id', $product_detail['purchase_unit_id'])->first();
                    if ($Trans['status'] == "completed") {
                        if (isset($product_detail['product_variant_id']) && $product_detail['product_variant_id'] !== null) {
                            //decrease the quantity from warehousev (from_warehouse)
                            $product_warehouse_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                                ->where('warehouse_id', $Trans['from_warehouse_id'])
                                ->where('product_id', $product_detail['product_id'])
                                ->where('product_variant_id', $product_detail['product_variant_id'])
                                ->first();

                            if ($unit && $product_warehouse_from) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_from->save();
                            }

                            //increase the quantity to warehouse (to_warehouse)
                            $product_warehouse_to = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                                ->where('warehouse_id', $Trans['to_warehouse_id'])
                                ->where('product_id', $product_detail['product_id'])
                                ->where('product_variant_id', $product_detail['product_variant_id'])
                                ->first();

                            if ($unit && $product_warehouse_to) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_to->qty += $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_to->qty += $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_to->save();
                            }

                        } else {
                            //decrease the quantity from warehouse (from_warehouse)
                            $product_warehouse_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                                ->where('warehouse_id', $Trans['from_warehouse_id'])
                                ->where('product_id', $product_detail['product_id'])->first();

                            if ($unit && $product_warehouse_from) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_from->save();
                            }

                            //increase the quantity to warehouse (to_warehouse)
                            $product_warehouse_to = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                                ->where('warehouse_id', $Trans['to_warehouse_id'])
                                ->where('product_id', $product_detail['product_id'])->first();

                            if ($unit && $product_warehouse_to) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_to->qty += $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_to->qty += $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_to->save();
                            }
                        }
                    } elseif ($Trans['status'] == "sent") {
                        if (isset($product_detail['product_variant_id']) && $product_detail['product_variant_id'] !== null) {
                            $product_warehouse_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                                ->where('warehouse_id', $Trans['from_warehouse_id'])
                                ->where('product_id', $product_detail['product_id'])
                                ->where('product_variant_id', $product_detail['product_variant_id'])
                                ->first();

                            if ($unit && $product_warehouse_from) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_from->save();
                            }
                        } else {
                            $product_warehouse_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                                ->where('warehouse_id', $Trans['from_warehouse_id'])
                                ->where('product_id', $product_detail['product_id'])->first();

                            if ($unit && $product_warehouse_from) {
                                if ($unit->operator == '/') {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse_from->qty -= $product_detail['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse_from->save();
                            }
                        }
                    }

                    $transDetail['transfer_id']        = $id;
                    $transDetail['quantity']           = $product_detail['quantity'];
                    $transDetail['purchase_unit_id']   = $product_detail['purchase_unit_id'];
                    $transDetail['product_id']         = $product_detail['product_id'];
                    $transDetail['product_variant_id'] = $product_detail['product_variant_id'];
                    $transDetail['cost']               = $product_detail['Unit_cost'];
                    $transDetail['TaxNet']             = $product_detail['tax_percent'];
                    $transDetail['tax_method']         = $product_detail['tax_method'];
                    $transDetail['discount']           = $product_detail['discount'];
                    $transDetail['discount_method']    = $product_detail['discount_Method'];
                    $transDetail['total']              = $product_detail['subtotal'];

                    if (!in_array($product_detail['id'], $old_products_id)) {
                        $this->getTransferDetailsRepository->getModel()->create($transDetail);
                    } else {
                        $this->getTransferDetailsRepository->getModel()->where('id', $product_detail['id'])->update($transDetail);
                    }
                }
            }

            $transfer->update([
                'to_warehouse_id'   => $Trans['to_warehouse'],
                'from_warehouse_id' => $Trans['from_warehouse'],
                'date'              => $Trans['date'],
                'notes'             => $Trans['notes'],
                'statut'            => $Trans['statut'],
                'items'             => sizeof($request['details']),
                'tax_rate'          => $Trans['tax_rate'],
                'TaxNet'            => $Trans['TaxNet'],
                'discount'          => $Trans['discount'],
                'shipping'          => $Trans['shipping'],
                'GrandTotal'        => $request['GrandTotal'],
            ]);
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
                $unit = $this->getUnitRepository()->getModel()->where('id', $value['purchase_unit_id'])->first();
            } else {
                $product_unit_purchase_id = $this->getProductRepository()->getModel()->with('unitPurchase')->where('id', $value['product_id'])->first();
                $unit                     = $this->getUnitRepository()->getModel()->where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
            } 

            if ($transfer->status == "completed") {
                if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
                    $warehouse_from_variant = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
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

                    $warehouse_to_variant = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
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
                    $warehouse_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                        ->where('warehouse_id', $transfer->from_warehouse_id)
                        ->where('product_id', $value['product_id'])->first();

                    if ($unit && $warehouse_from) {
                        if ($unit->operator == '/') {
                            $warehouse_from->qty += $value['quantity'] / $unit->operator_value;
                        } else {
                            $warehouse_from->qty += $value['quantity'] * $unit->operator_value;
                        }
                        $warehouse_from->save();
                    }

                    $warehouse_to = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                        ->where('warehouse_id', $transfer->to_warehouse_id)
                        ->where('product_id', $value['product_id'])->first();

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
                    $sent_variant_to = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
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
                    $sent_variant_from = $this->getProductWarehouseRepository()->getModel()->whereNull('deleted_at')
                        ->where('warehouse_id', $transfer->from_warehouse_id)
                        ->where('product_id', $value['product_id'])->first();

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
