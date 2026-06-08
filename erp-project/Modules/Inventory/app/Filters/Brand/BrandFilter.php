<?php

namespace Modules\Inventory\Filters\Brand;

use App\Filters\Filters;

class BrandFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'status',
        'code',
    ];

    public function status($status)
    {
        if($status != 'null' && $status != null) {
            return $this->builder->where('status', $status);
        }
    }

    public function code($code)
    {
        if($code != 'null' && $code != null) {
            return $this->builder->where('code', $code);
        }
    }

    public function search($search)
    {
        return $this->builder->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('code', 'LIKE', '%' . $search . '%');
        });
    }
}
