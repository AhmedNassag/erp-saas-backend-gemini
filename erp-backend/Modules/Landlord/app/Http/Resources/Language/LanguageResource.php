<?php

namespace Modules\Landlord\Http\Resources\Language;

use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'code'        => $this->code,
            'name'        => $this->name,
            'native_name' => $this->native_name,
            'direction'   => $this->direction,
            'is_default'  => $this->is_default,
            'is_active'   => $this->is_active,
            'created_at'  => $this->created_at,
        ];
    }
}
