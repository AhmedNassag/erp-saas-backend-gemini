<?php

namespace Modules\Inventory\Resources\PaymentPurchase;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentPurchaseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'purchase_id'      => $this->purchase_id,
            'purchase_Ref'     => $this->purchase ? $this->purchase->Ref : null,
            'provider_name'    => $this->purchase && $this->purchase->provider ? $this->purchase->provider->name : null,
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
