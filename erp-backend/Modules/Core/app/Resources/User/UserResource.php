<?php

namespace Modules\Core\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Resources\RoleAndPermission\RoleResource;

class UserResource extends JsonResource
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
            'email'   => $this->email,
            'roles'   => RoleResource::collection($this->roles ?? []),
            // 'profile' => $this->whenLoaded('employeeProfile', function () {
            //     return new EmployeeProfileResource($this->employeeProfile);
            // }),
        ];
    }
}
