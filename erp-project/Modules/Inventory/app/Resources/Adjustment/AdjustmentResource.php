<?php

namespace Modules\Inventory\Resources\Adjustment;

use Illuminate\Http\Resources\Json\JsonResource;

class AdjustmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'date'           => $this->date,
            'Ref'            => $this->Ref,
            'items'          => $this->items,
            'notes'          => $this->notes,
            'warehouse_id'   => $this->warehouse_id,
            'warehouse_name' => $this->warehouse ? $this->warehouse->name : null,
            'user_id'        => $this->user_id,
            'user_name'      => $this->user ? $this->user->name : null,

            // 'adjustmentDetail_id'                   => $this->adjustmentDetail->id,
            // 'adjustmentDetail_quantity'             => $this->adjustmentDetail->quantity,
            // 'adjustmentDetail_type'                 => $this->adjustmentDetail->type,
            // 'adjustmentDetail_product_id'           => $this->adjustmentDetail->product_id,
            // 'adjustmentDetail_product_name'         => $this->adjustmentDetail->product ? $this->adjustmentDetail->product->name : null,
            // 'adjustmentDetail_product_variant_id'   => $this->adjustmentDetail->product_variant_id,
            // 'adjustmentDetail_product_variant_name' => $this->adjustmentDetail->productVariant ? $this->adjustmentDetail->productVariant->name : null,
        ];
    }
}
