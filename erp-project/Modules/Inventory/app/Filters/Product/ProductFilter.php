<?php

namespace Modules\Inventory\Filters\Product;

use App\Filters\Filters;

class ProductFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'status',
        'code',
        'category_id',
        'brand_id',
    ];

    public function status($status)
    {
        if ($status != 'null' && $status != null) {
            return $this->builder->where('status', $status);
        }
    }

    public function code($code)
    {
        if ($code != 'null' && $code != null) {
            return $this->builder->where('code', $code);
        }
    }

    public function category_id($category_id)
    {
        if ($category_id != 'null' && $category_id != null) {
            return $this->builder->where('category_id', $category_id);
        }
    }

    public function brand_id($brand_id)
    {
        if ($brand_id != 'null' && $brand_id != null) {
            return $this->builder->where('brand_id', $brand_id);
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
