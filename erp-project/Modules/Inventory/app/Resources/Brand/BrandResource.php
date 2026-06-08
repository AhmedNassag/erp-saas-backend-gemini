<?php

namespace Modules\Inventory\Resources\Brand;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'     => $this->id,
            'status' => $this->status ?? null,
            'name'   => $this->name,
            'code'   => $this->code ?? null,
            'image'  => $this->image ? $this->image->localUrl : null,
            'images' => $this->images ?? null,
        ];
    }
}
