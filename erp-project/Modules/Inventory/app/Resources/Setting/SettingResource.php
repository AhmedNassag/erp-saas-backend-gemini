<?php

namespace Modules\Inventory\Resources\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'companyName'   => $this->companyName,
            'companyPhone'  => $this->companyPhone,
            'companyAdress' => $this->companyAdress,
            'developed_by'  => $this->developed_by,
            'footer'        => $this->footer,
            'currency_id'   => $this->currency_id,
            'currency_name' => $this->currency?->name ?? null,
            'client_id'     => $this->client_id,
            'client_name'   => $this->client?->name ?? null,
            'warehouse_id'  => $this->warehouse_id,
            'warehouse_name'=> $this->warehouse?->name ?? null,
            'image'         => $this->image ? $this->image->localUrl : null,
            'images'        => $this->images ?? null,
        ];
    }
}
