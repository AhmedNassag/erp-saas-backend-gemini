<?php

namespace Modules\Core\RoleAndPermission\App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'name_en' => trans($this->name, [], 'en'),
            'name_ar' => trans($this->name, [], 'ar'),
            'module'  => $this?->module,
        ];
    }
}
