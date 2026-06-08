<?php

namespace Modules\Inventory\Resources\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'status'        => $this->status ?? null,
            'name'          => $this->name,
            'code'          => $this->code ?? null,
            'is_main'       => $this->is_main ?? false,
            'category_id'   => $this->category_id,
            'parent_name'   => $this->parent?->name ?? null,
            'image'         => $this->image ? $this->image->localUrl : null,
            'images'        => $this->images ?? null,
        ];
    }
}
