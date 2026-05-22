<?php

namespace Modules\Core\Area\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
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
            'city_id'      => $this->city_id,
            'city_name'    => $this->city->name ?? null,
            'country_id'   => $this->city->country_id ?? null,
            'country_name' => $this->city->country->name ?? null,
        ];
    }
}
