<?php

namespace Modules\Core\Resources\City;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'country_id'   => $this->country_id ?? null,
            'country_name' => $this->country?->name ?? null,
        ];
    }
}
