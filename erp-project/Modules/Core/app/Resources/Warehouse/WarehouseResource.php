<?php

namespace Modules\Core\Resources\Warehouse;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status ?? null,
            'name'         => $this->name,
            'mobile'       => $this->mobile ?? null,
            'branch_id'    => $this->branch_id,
            'branch_name'  => $this->branch?->name ?? null,
            'area_id'      => $this->area_id,
            'area_name'    => $this->area?->name ?? null,
            'city_id'      => $this->area?->city_id ?? null,
            'city_name'    => $this->area?->city?->name ?? null,
            'country_id'   => $this->area?->city?->country_id ?? null,
            'country_name' => $this->area?->city?->country?->name ?? null,
            'address'      => $this->address ?? null,
            'is_main'      => $this->is_main ?? false,
            'image'        => $this->image ? $this->image->localUrl : null,
            'images'       => $this->images ?? null,
        ];
    }
}
