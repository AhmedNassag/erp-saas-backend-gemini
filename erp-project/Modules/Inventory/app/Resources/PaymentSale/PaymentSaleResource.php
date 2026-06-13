<?php

namespace Modules\Inventory\Resources\PaymentSale;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentSaleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'sale_id'       => $this->sale_id,
            'sale_Ref'      => $this->sale ? $this->sale->Ref : null,
            'client_name'   => $this->sale && $this->sale->client ? $this->sale->client->name : null,
            'Ref'           => $this->Ref,
            'date'          => $this->date,
            'Reglement'     => $this->Reglement,
            'montant'       => $this->montant,
            'change'        => $this->change,
            'notes'         => $this->notes,
            'user_id'       => $this->user_id,
            'user_name'     => $this->user ? $this->user->name : null,
        ];
    }
}
