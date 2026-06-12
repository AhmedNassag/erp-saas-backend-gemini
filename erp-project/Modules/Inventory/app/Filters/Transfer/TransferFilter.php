<?php

namespace Modules\Inventory\Filters\Transfer;

use App\Filters\Filters;

class TransferFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'Ref',
        'from_warehouse_id',
        'to_warehouse_id',
        'status',
        'date'
    ];

    public function Ref($Ref)
    {
        if ($Ref != 'null' && $Ref != null) {
            return $this->builder->where('Ref', $Ref);
        }
    }

    public function from_warehouse_id($from_warehouse_id)
    {
        if ($from_warehouse_id != 'null' && $from_warehouse_id != null) {
            return $this->builder->where('from_warehouse_id', $from_warehouse_id);
        }
    }

    public function to_warehouse_id($to_warehouse_id)
    {
        if ($to_warehouse_id != 'null' && $to_warehouse_id != null) {
            return $this->builder->where('to_warehouse_id', $to_warehouse_id);
        }
    }

    public function status($status)
    {
        if ($status != 'null' && $status != null) {
            return $this->builder->where('status', $status);
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
                ->orWhereHas('fromWarehouse', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                })
                ->orWhereHas('toWarehouse', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                });
        });
    }
}
