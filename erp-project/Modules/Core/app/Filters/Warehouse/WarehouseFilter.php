<?php

namespace Modules\Core\Filters\Warehouse;

use App\Filters\Filters;

class WarehouseFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'status',
        'mobile',
        'branch_id',
        'area_id',
        'address',
        'is_main',
    ];

    public function is_main($is_main)
    {
        if ($is_main != 'null' && $is_main != null) {
            return $this->builder->where('is_main', $is_main);
        }
    }

    public function address($address)
    {
        if($address != 'null' && $address != null) {
            return $this->builder->where('address', $address);
        }
    }

    public function area_id($area_id)
    {
        if($area_id != 'null' && $area_id != null) {
            return $this->builder->where('area_id', $area_id);
        }
    }

    public function branch_id($branch_id)
    {
        if($branch_id != 'null' && $branch_id != null) {
            return $this->builder->where('branch_id', $branch_id);
        }
    }

    public function mobile($mobile)
    {
        if($mobile != 'null' && $mobile != null) {
            return $this->builder->where('mobile', $mobile);
        }
    }

    public function status($status)
    {
        if($status != 'null' && $status != null) {
            return $this->builder->where('status', $status);
        }
    }

    public function search($search)
    {
        return $this->builder->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhereHas('area', function($q) use($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            })
            ->orWhereHas('branch', function($q) use($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            });
        });
    }
}
