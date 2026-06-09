<?php

namespace Modules\Inventory\Resources\Currency;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'     => $this->id,
            'status' => $this->status ?? null,
            'name'   => $this->name,
            'code'   => $this->code ?? null,
            'symbol' => $this->symbol ?? null,
        ];
    }
}
