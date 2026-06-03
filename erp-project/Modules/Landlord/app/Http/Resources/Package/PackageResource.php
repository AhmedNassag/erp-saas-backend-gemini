<?php

namespace Modules\Landlord\Http\Resources\Package;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'slug'         => $this->slug,
            'price'        => $this->price,
            'limit_users'  => $this->limit_users,
            'limit_tenants' => $this->limit_tenants,
            'features'     => $this->features,
            'is_active'    => $this->is_active,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
