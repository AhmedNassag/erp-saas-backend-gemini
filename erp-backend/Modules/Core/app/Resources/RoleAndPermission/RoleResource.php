<?php

namespace Modules\Core\Resources\RoleAndPermission;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Resources\RoleAndPermission\PermissionResource;

class RoleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'permissions' => PermissionResource::collection($this->permissions),
        ];
    }
}
