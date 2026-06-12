<?php

namespace Modules\Inventory\Repositories\Adjustment;

use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Adjustment\Adjustment;
use Modules\Inventory\Repositories\Adjustment\AdjustmentInterface;
use Modules\Inventory\Resources\Adjustment\AdjustmentResource;
use Modules\Core\Repositories\Warehouse\WarehouseRepository;
use Modules\Inventory\Repositories\AdjustmentDetail\AdjustmentDetailRepository;
use Modules\Inventory\Repositories\ProductWarehouse\ProductWarehouseRepository;

class AdjustmentRepository extends BaseRepository implements AdjustmentInterface
{
    protected function getModel(): \Illuminate\Database\Eloquent\Model
    {
        return new Adjustment();
    }

    protected function getResourceClass(): string
    {
        return AdjustmentResource::class;
    }

    protected function getPluralName(): string
    {
        return 'Adjustments';
    }

    protected function getSingularName(): string
    {
        return 'Adjustment';
    }

    //return used repositories
    protected function getAdjustmentDetailsRepository()    {
        return new AdjustmentDetailRepository();
    }

    protected function getProductWarehouseRepository()    {
        return new ProductWarehouseRepository();
    }

    protected function getWarehouseRepository()    {
        return new WarehouseRepository();
    }



    public function show($id, array $with = [])
    {
        $adjustment = $this->getModel()->with(['adjustmentDetails', 'warehouse', 'user'])->findOrFail($id);

        $warehouses = $this->getWarehouseRepository()->getModel()
            ->whereNull('deleted_at')->get();

        $details = [];
        foreach ($adjustment->adjustmentDetails as $detail) {
            $product = $detail->product;
            $unit    = $product && $product->unit ? $product->unit->ShortName : null;

            // Get current stock from product_warehouse for this warehouse
            $pw = $this->getProductWarehouseRepository()->getModel()
                ->whereNull('deleted_at')
                ->where('warehouse_id', $adjustment->warehouse_id)
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
                'adjustment_id'      => $detail->adjustment_id,
                'product_id'         => $detail->product_id,
                'product_variant_id' => $detail->product_variant_id,
                'quantity'           => $detail->quantity,
                'type'               => $detail->type,
                'name'               => $product ? $product->name : null,
                'code'               => $product ? $product->code : null,
                'unit'               => $unit,
                'current'            => $current,
            ];
        }

        return (new \App\Traits\API)
            ->isOk(__('Adjustment Data'))
            ->setData([
                'adjustment' => new AdjustmentResource($adjustment),
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

            $adjustment = $this->getModel()->create($data);

            // store the adjustment details and update product warehouse quantities
            $this->storeAdjustmentDetails($adjustment, $request->details);

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

            $adjustment = $this->getModel()->findOrFail($id);
            $oldDetails = $this->getAdjustmentDetailsRepository()->getModel()->where('adjustment_id', $id)->get();

            $newDetails = $request['details'];
            $length     = sizeof($newDetails);

            // collect IDs
            $newDetailIds = [];
            foreach ($newDetails as $detail) {
                $newDetailIds[] = $detail['id'] ?? 0;
            }

            $oldDetailIds = [];
            foreach ($oldDetails as $oldDetail) {
                $oldDetailIds[] = $oldDetail->id;
            }

            // revert old details (reverse quantity changes)
            foreach ($oldDetails as $oldDetail) {
                if ($oldDetail['type'] == 'add') {
                    $this->subInProductWarehouse($adjustment, $oldDetail->toArray());
                } else {
                    $this->addInProductWarehouse($adjustment, $oldDetail->toArray());
                }

                // Delete detail if not in new request
                if (!in_array($oldDetail->id, $newDetailIds)) {
                    $oldDetail->delete();
                }
            }

            // Apply new details
            foreach ($newDetails as $productDetail) {
                if ($productDetail['type'] == 'add') {
                    $this->addInProductWarehouse($adjustment, $productDetail);
                } else {
                    $this->subInProductWarehouse($adjustment, $productDetail);
                }

                $detailData = [
                    'adjustment_id'      => $id,
                    'quantity'           => $productDetail['quantity'],
                    'product_id'         => $productDetail['product_id'],
                    'product_variant_id' => isset($productDetail['product_variant_id']) ? $productDetail['product_variant_id'] : null,
                    'type'               => $productDetail['type'],
                ];

                if (empty($productDetail['id']) || $productDetail['id'] < 0 ||
                    !in_array($productDetail['id'], $oldDetailIds)) {
                    $this->getAdjustmentDetailsRepository()->getModel()->create($detailData);
                } else {
                    $this->getAdjustmentDetailsRepository()->getModel()
                        ->where('id', $productDetail['id'])->update($detailData);
                }
            }

            $adjustment->update([
                'warehouse_id' => $request['warehouse_id'],
                'notes'        => $request['notes'],
                'date'         => $request['date'],
                'items'        => $length,
            ]);

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

            $adjustment = $this->getModel()->findOrFail($id);
            $oldDetails = $this->getAdjustmentDetailsRepository()->getModel()->where('adjustment_id', $id)->get();

            // Reverse quantity changes before deleting
            foreach ($oldDetails as $oldDetail) {
                if ($oldDetail['type'] == 'add') {
                    $this->subInProductWarehouse($adjustment, $oldDetail->toArray());
                } else {
                    $this->addInProductWarehouse($adjustment, $oldDetail->toArray());
                }
            }

            // Delete adjustment details
            $this->getAdjustmentDetailsRepository()->getModel()->where('adjustment_id', $id)->delete();

            // Soft delete the adjustment
            $adjustment->delete();

            DB::commit();

            return (new \App\Traits\API)
                ->isOk(__('Destroyed Successfully'))
                ->build();
        }
        catch (\Exception $e) {
            DB::rollBack();
            return (new \App\Traits\API)
                ->isError('An Error occured')
                ->setStatus(500)
                ->build();
        }
    }



    private function storeAdjustmentDetails($adjustment, $details)
    {
        try {
            $data = $details;
            $i = 0;
            foreach ($data as $key => $value) {
                $adjustmentDetails[] = [
                    'adjustment_id'      => $adjustment->id,
                    'quantity'           => $value['quantity'],
                    'product_id'         => $value['product_id'],
                    'product_variant_id' => isset($value['product_variant_id']) ? $value['product_variant_id'] : null,
                    'type'               => $value['type'],
                ];

                if ($value['type'] == "add") {
                    $this->addInProductWarehouse($adjustment, $value);
                } else {
                    $this->subInProductWarehouse($adjustment, $value);
                }
            }
            $this->getAdjustmentDetailsRepository()->getModel()->insert($adjustmentDetails);
        } catch (\Exception $e) {
            throw $e;
        }
    }



    private function addInProductWarehouse($adjustment, $value)
    {
        if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
            $productWarehouse = $this->getProductWarehouseRepository()->getModel()
            ->whereNull('deleted_at')
            ->where('warehouse_id', $adjustment->warehouse_id)
            ->where('product_id', $value['product_id'])
            ->where('product_variant_id', $value['product_variant_id'])
            ->first();

            if ($productWarehouse) {
                $productWarehouse->qty += $value['quantity'];
                $productWarehouse->save();
            }
        } else {
            $productWarehouse = $this->getProductWarehouseRepository()->getModel()
            ->whereNull('deleted_at')
            ->where('warehouse_id', $adjustment->warehouse_id)
            ->where('product_id', $value['product_id'])
            ->first();

            if ($productWarehouse) {
                $productWarehouse->qty += $value['quantity'];
                $productWarehouse->save();
            }
        }
    }



    private function subInProductWarehouse($adjustment, $value)
    {
        if (isset($value['product_variant_id']) && $value['product_variant_id'] !== null) {
            $productWarehouse = $this->getProductWarehouseRepository()->getModel()
            ->whereNull('deleted_at')
            ->where('warehouse_id', $adjustment->warehouse_id)
            ->where('product_id', $value['product_id'])
            ->where('product_variant_id', $value['product_variant_id'])
            ->first();

            if ($productWarehouse) {
                $productWarehouse->qty -= $value['quantity'];
                $productWarehouse->save();
            }

        } else {
            $productWarehouse = $this->getProductWarehouseRepository()->getModel()
            ->whereNull('deleted_at')
            ->where('warehouse_id', $adjustment->warehouse_id)
            ->where('product_id', $value['product_id'])
            ->first();

            if ($productWarehouse) {
                $productWarehouse->qty -= $value['quantity'];
                $productWarehouse->save();
            }
        }
    }
}
