<?php

namespace Modules\Core\Branch\App\resources;

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
            'name'         => $this->name,
            'name_en'      => $this->getTranslation('name' , 'en'),
            'name_ar'      => $this->getTranslation('name' , 'ar'),
            'code'         => $this->branch_code ?? null,
            'mobile'       => $this->mobile ?? null,
            'address'      => $this->address ?? null,
            'status'       => $this->status ?? null,
            'area_id'      => $this->area_id,
            'area_name'    => $this->area->name ?? null,
            'city_id'      => $this->area->city_id ?? null,
            'city_name'    => $this->area->city->name ?? null,
            'country_id'   => $this->area->city->country_id ?? null,
            'country_name' => $this->area->city->country->name ?? null,
        ];
    }
}
