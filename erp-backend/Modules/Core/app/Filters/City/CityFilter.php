<?php

namespace Modules\Core\Filters\City;

use App\Filters\Filters;

class CityFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'country_id',
        'status',
    ];

    

    public function country_id($country_id)
    {
        if($country_id != 'null' && $country_id != null) {
            return $this->builder->where('country_id', $country_id);
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
            ->orWhereHas('country',function($q) use($search){
                $q->where('name', 'LIKE', '%' . $search . '%');
            });
        });
    }
}
