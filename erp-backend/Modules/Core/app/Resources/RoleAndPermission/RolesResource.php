<?php

namespace Modules\Core\Resources\RoleAndPermission;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\RoleAndPermission\App\resources\PermissionsResource;

class RolesResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'name_en'     => $this->getTranslation('name', 'en'),
            'name_ar'     => $this->getTranslation('name', 'ar'),
            'permissions' => PermissionsResource::collection($this->permissions),
        ];
    }
}
