<?php

namespace Modules\Inventory\Filters\Adjustment;

use App\Filters\Filters;

class AdjustmentFilter extends Filters
{
    protected array $var_filters = ['search', 'Ref', 'warehouse_id', 'date'];

    public function Ref($Ref)
    {
        if ($Ref != 'null' && $Ref != null) {
            return $this->builder->where('Ref', $Ref);
        }
    }

    public function warehouse_id($warehouse_id)
    {
        if ($warehouse_id != 'null' && $warehouse_id != null) {
            return $this->builder->where('warehouse_id', $warehouse_id);
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
                ->orWhereHas('warehouse', function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                });
        });
    }
}
