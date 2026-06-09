<?php

namespace Modules\Inventory\Resources\Unit;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'status'         => $this->status ?? null,
            'name'           => $this->name,
            'code'           => $this->code ?? null,
            'shortName'      => $this->shortName ?? null,
            'base_unit'      => $this->base_unit,
            'base_unit_name' => $this->parent?->name ?? null,
            'operator'       => $this->operator ?? '*',
            'operator_value' => $this->operator_value ?? 1,
        ];
    }
}
