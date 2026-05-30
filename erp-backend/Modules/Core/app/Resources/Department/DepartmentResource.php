<?php

namespace Modules\Core\Resources\Department;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
        ];
    }
}
