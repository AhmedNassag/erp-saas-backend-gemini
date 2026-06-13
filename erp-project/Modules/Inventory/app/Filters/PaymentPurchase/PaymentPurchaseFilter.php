<?php

namespace Modules\Inventory\Filters\PaymentPurchase;

use App\Filters\Filters;

class PaymentPurchaseFilter extends Filters
{
    protected array $var_filters = ['search', 'Ref', 'Reglement', 'date', 'purchase_id'];

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

    public function purchase_id($purchase_id)
    {
        if ($purchase_id != 'null' && $purchase_id != null) {
            return $this->builder->where('purchase_id', $purchase_id);
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
                ->orWhereHas('purchase', function ($q) use ($search) {
                    $q->where('Ref', 'LIKE', '%' . $search . '%');
                })
                ->orWhereHas('purchase.provider', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                });
        });
    }
}
