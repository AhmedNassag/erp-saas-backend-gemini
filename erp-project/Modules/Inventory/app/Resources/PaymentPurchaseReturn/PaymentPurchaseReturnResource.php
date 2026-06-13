<?php

namespace Modules\Inventory\Resources\PaymentPurchaseReturn;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentPurchaseReturnResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'purchase_return_id'  => $this->purchase_return_id,
            'purchase_return_Ref' => $this->purchaseReturn ? $this->purchaseReturn->Ref : null,
            'provider_name'       => $this->purchaseReturn && $this->purchaseReturn->provider ? $this->purchaseReturn->provider->name : null,
            'Ref'                 => $this->Ref,
            'date'                => $this->date,
            'Reglement'           => $this->Reglement,
            'montant'             => $this->montant,
            'change'              => $this->change,
            'notes'               => $this->notes,
            'user_id'             => $this->user_id,
            'user_name'           => $this->user ? $this->user->name : null,
        ];
    }
}
