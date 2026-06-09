<?php

namespace Modules\Inventory\Filters\Setting;

use App\Filters\Filters;

class SettingFilter extends Filters
{
    protected array $var_filters = [
        'search',
    ];

    public function search($search)
    {
        return $this->builder->where(function ($query) use ($search) {
            $query->where('companyName', 'LIKE', '%' . $search . '%');
        });
    }
}
