<?php

namespace Modules\Core\Filters\Department;

use App\Filters\Filters;

class DepartmentFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'status',
    ];



    public function status($status)
    {
        if($status != 'null' && $status != null) {
            return $this->builder->where('status', $status);
        }
    }


    
    public function search($search)
    {
        return $this->builder->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        });
    }
}