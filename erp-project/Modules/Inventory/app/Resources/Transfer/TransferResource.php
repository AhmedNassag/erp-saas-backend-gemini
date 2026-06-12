<?php

namespace Modules\Inventory\Resources\Transfer;

use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'date'               => $this->date,
            'Ref'                => $this->Ref,
            'from_warehouse_id'  => $this->from_warehouse_id,
            'from_warehouse_name'=> $this->fromWarehouse ? $this->fromWarehouse->name : null,
            'to_warehouse_id'    => $this->to_warehouse_id,
            'to_warehouse_name'  => $this->toWarehouse ? $this->toWarehouse->name : null,
            'items'              => $this->items,
            'tax_rate'           => $this->tax_rate,
            'TaxNet'             => $this->TaxNet,
            'discount'           => $this->discount,
            'shipping'           => $this->shipping,
            'GrandTotal'         => $this->GrandTotal,
            'status'             => $this->status,
            'notes'              => $this->notes,
            'user_id'            => $this->user_id,
            'user_name'          => $this->user ? $this->user->name : null,
        ];
    }
}
