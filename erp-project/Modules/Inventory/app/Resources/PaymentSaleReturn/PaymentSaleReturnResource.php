<?php

namespace Modules\Inventory\Resources\PaymentSaleReturn;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentSaleReturnResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'sale_return_id'   => $this->sale_return_id,
            'sale_return_Ref'  => $this->saleReturn ? $this->saleReturn->Ref : null,
            'client_name'      => $this->saleReturn && $this->saleReturn->client ? $this->saleReturn->client->name : null,
            'Ref'              => $this->Ref,
            'date'             => $this->date,
            'Reglement'        => $this->Reglement,
            'montant'          => $this->montant,
            'change'           => $this->change,
            'notes'            => $this->notes,
            'user_id'          => $this->user_id,
            'user_name'        => $this->user ? $this->user->name : null,
        ];
    }
}
