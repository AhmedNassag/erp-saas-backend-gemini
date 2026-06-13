<?php

namespace Modules\Inventory\Filters\PaymentPurchaseReturn;

use App\Filters\Filters;

class PaymentPurchaseReturnFilter extends Filters
{
    protected array $var_filters = ['search', 'Ref', 'Reglement', 'date', 'purchase_return_id'];

    public function Ref($Ref)
    {
        if ($Ref != 'null' && $Ref != null) {
            return $this->builder->where('Ref', $Ref);
        }
    }

    public function Reglement($Reglement)
    {
        if ($Reglement != 'null' && $Reglement != null) {
            return $this->builder->where('Reglement', $Reglement);
        }
    }

    public function purchase_return_id($purchase_return_id)
    {
        if ($purchase_return_id != 'null' && $purchase_return_id != null) {
            return $this->builder->where('purchase_return_id', $purchase_return_id);
        }
    }

    public function date($date)
    {
        if ($date != 'null' && $date != null) {
            return $this->builder->whereDate('date', $date);
        }
    }

    public function search($search)
    {
        return $this->builder->where(function ($query) use ($search) {
            $query->where('Ref', 'LIKE', '%' . $search . '%')
                ->orWhere('Reglement', 'LIKE', '%' . $search . '%')
                ->orWhereHas('purchaseReturn', function ($q) use ($search) {
                    $q->where('Ref', 'LIKE', '%' . $search . '%');
                })
                ->orWhereHas('purchaseReturn.provider', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                });
        });
    }
}
