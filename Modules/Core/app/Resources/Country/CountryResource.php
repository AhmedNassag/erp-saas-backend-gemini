<?php

namespace Modules\Core\Resources\Country;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'      => $this->id,
            'status'  => $this->status ?? null,
            'name'    => $this->name,
            'image'   => $this->img ? $this->img->localUrl : '---',
        ];
    }
}
