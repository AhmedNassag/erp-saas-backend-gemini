<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'domain'               => $this->domain,
            'database'             => $this->database,
            'package'              => $this->whenLoaded('package', fn() => [
                'id'    => $this->package->id,
                'name'  => $this->package->name,
                'price' => $this->package->price,
            ]),
            'subscription_ends_at' => $this->subscription_ends_at,
            'status'               => $this->status,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
        ];
    }
}
