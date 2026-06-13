<?php

namespace Modules\Inventory\Resources\Sale;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Inventory\Resources\PaymentSale\PaymentSaleResource;

class SaleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'date'           => $this->date,
            'Ref'            => $this->Ref,
            'client_id'      => $this->client_id,
            'client_name'    => $this->client ? $this->client->name : null,
            'warehouse_id'   => $this->warehouse_id,
            'warehouse_name' => $this->warehouse ? $this->warehouse->name : null,
            'tax_rate'       => $this->tax_rate,
            'TaxNet'         => $this->TaxNet,
            'discount'       => $this->discount,
            'shipping'       => $this->shipping,
            'GrandTotal'     => $this->GrandTotal,
            'paid_amount'    => $this->paid_amount,
            'payment_status' => $this->payment_status,
            'status'         => $this->status,
            'notes'          => $this->notes,
            'items'          => $this->items,
            'user_id'        => $this->user_id,
            'user_name'      => $this->user ? $this->user->name : null,
            'payment_sales'  => PaymentSaleResource::collection($this->whenLoaded('paymentSales')),
        ];
    }
}
