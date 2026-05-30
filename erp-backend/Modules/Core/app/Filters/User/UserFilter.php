<?php

namespace Modules\Core\Filters\User;

use App\Filters\Filters;


class UserFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'status',
        'department_id',
        'branch_id',
    ];

    

    public function department_id($department_id)
    {
        if($department_id != 'null' && $department_id != null) {
            return $this->builder->where('department_id', $department_id);
        }
    }

    

    public function branch_id($branch_id)
    {
        if($branch_id != 'null' && $branch_id != null) {
            return $this->builder->where('branch_id', $branch_id);
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
            ->orWhereHas('department', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            })
            ->orWhereHas('branch', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            });
        });
    }
}