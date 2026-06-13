<?php

namespace Modules\Inventory\Filters\SaleReturn;

use App\Filters\Filters;

class SaleReturnFilter extends Filters
{
    protected array $var_filters = ['search', 'Ref', 'client_id', 'warehouse_id', 'status', 'payment_status', 'date'];

    public function Ref($Ref)
    {
        if ($Ref != 'null' && $Ref != null) {
            return $this->builder->where('Ref', $Ref);
        }
    }

    public function client_id($client_id)
    {
        if ($client_id != 'null' && $client_id != null) {
            return $this->builder->where('client_id', $client_id);
        }
    }

    public function warehouse_id($warehouse_id)
    {
        if ($warehouse_id != 'null' && $warehouse_id != null) {
            return $this->builder->where('warehouse_id', $warehouse_id);
        }
    }

    public function status($status)
    {
        if ($status != 'null' && $status != null) {
            return $this->builder->where('status', $status);
        }
    }

    public function payment_status($payment_status)
    {
        if ($payment_status != 'null' && $payment_status != null) {
            return $this->builder->where('payment_status', $payment_status);
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
                ->orWhereHas('client', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                })
                ->orWhereHas('warehouse', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                });
        });
    }
}
