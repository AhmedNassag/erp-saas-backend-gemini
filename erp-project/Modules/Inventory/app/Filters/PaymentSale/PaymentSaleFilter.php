<?php

namespace Modules\Inventory\Filters\PaymentSale;

use App\Filters\Filters;

class PaymentSaleFilter extends Filters
{
    protected array $var_filters = ['search', 'Ref', 'sale_id', 'Reglement', 'date'];

    public function Ref($Ref)
    {
        if ($Ref != 'null' && $Ref != null) {
            return $this->builder->where('Ref', $Ref);
        }
    }

    public function sale_id($sale_id)
    {
        if ($sale_id != 'null' && $sale_id != null) {
            return $this->builder->where('sale_id', $sale_id);
        }
    }

    public function Reglement($Reglement)
    {
        if ($Reglement != 'null' && $Reglement != null) {
            return $this->builder->where('Reglement', $Reglement);
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
                ->orWhereHas('sale.client', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                });
        });
    }
}
