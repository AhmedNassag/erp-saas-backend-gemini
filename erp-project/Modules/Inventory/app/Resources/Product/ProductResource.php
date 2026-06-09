<?php

namespace Modules\Inventory\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'code'               => $this->code,
            'Type_barcode'       => $this->Type_barcode,
            'name'               => $this->name,
            'status'             => $this->status ?? null,
            'cost'               => $this->cost,
            'price'              => $this->price,
            'category_id'        => $this->category_id,
            'category_name'      => $this->category?->name ?? null,
            'brand_id'           => $this->brand_id,
            'brand_name'         => $this->brand?->name ?? null,
            'unit_id'            => $this->unit_id,
            'unit_name'          => $this->unit?->name ?? null,
            'unit_sale_id'       => $this->unit_sale_id,
            'unit_sale_name'     => $this->unitSale?->name ?? null,
            'unit_purchase_id'   => $this->unit_purchase_id,
            'unit_purchase_name' => $this->unitPurchase?->name ?? null,
            'TaxNet'             => $this->TaxNet,
            'tax_method'         => $this->tax_method,
            'note'               => $this->note,
            'stock_alert'        => $this->stock_alert,
            'is_variant'         => $this->is_variant,
            'is_active'          => $this->is_active,
             'image'              => $this->image ? $this->image->localUrl : null,
            'images'             => $this->images ?? null,
            'variants'           => $this->variants()->whereNull('deleted_at')->get(['id', 'name', 'qty']),
        ];
    }
}
