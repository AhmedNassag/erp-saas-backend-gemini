<?php

namespace Modules\Core\Filters\Area;

use App\Filters\Filters;


class AreaFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'city_id',
        'status'
    ];

    

    public function city_id($city_id)
    {
        if($city_id != 'null' && $city_id != null) {
            return $this->builder->where('city_id', $city_id);
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
            ->orWhereHas('city', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            });
        });
    }
}