<?php

namespace Modules\Core\Resources\Branch;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status ?? null,
            'name'         => $this->name,
            'code'         => $this->branch_code ?? null,
            'mobile'       => $this->mobile ?? null,
            'address'      => $this->address ?? null,
            'area_id'      => $this->area_id,
            'area_name'    => $this->area?->name ?? null,
            'city_id'      => $this->area?->city_id ?? null,
            'city_name'    => $this->area?->city?->name ?? null,
            'country_id'   => $this->area?->city?->country_id ?? null,
            'country_name' => $this->area?->city?->country?->name ?? null,
            'image'        => $this->image ? $this->image->localUrl : null,
            'images'       => $this->images ?? null,
        ];
    }
}
