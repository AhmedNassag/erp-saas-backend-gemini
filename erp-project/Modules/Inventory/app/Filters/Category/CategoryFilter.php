<?php

namespace Modules\Inventory\Filters\Category;

use App\Filters\Filters;

class CategoryFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'status',
        'is_main',
        'category_id',
        'code',
    ];

    public function status($status)
    {
        if($status != 'null' && $status != null) {
            return $this->builder->where('status', $status);
        }
    }

    public function is_main($is_main)
    {
        if($is_main != 'null' && $is_main != null) {
            return $this->builder->where('is_main', $is_main);
        }
    }

    public function category_id($category_id)
    {
        if($category_id != 'null' && $category_id != null) {
            return $this->builder->where('category_id', $category_id);
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
