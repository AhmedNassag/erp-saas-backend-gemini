<?php

namespace Modules\Core\Filters\City;

use App\Filters\Filters;

class CityFilter extends Filters
{
    protected $var_filters = [
        'search',
        'country_id',
        'status',
    ];

    

    public function country_id($country_id)
    {
        if ($country_id != 'null' && $country_id != null) {
            return $this->builder->where('country_id', $country_id);
        }
    }



    public function status($status)
    {
        if ($status != 'null' && $status != null) {
            return $this->builder->where('status', $status);
        }
    }



    public function search($search)
    {
        return $this->builder->where(function ($query) use ($search) {
            $query->whereHas('country', function ($q) use ($search) {
                foreach (config('myConfig.langs') as $i => $locale) {
                    if ($i == 0) {
                        $q->where('name->' . $locale, 'LIKE', '%' . $search . '%');
                    }
                    else {
                        $q->orWhere('name->' . $locale, 'LIKE', '%' . $search . '%');
                    }
                }
            });
            foreach (config('myConfig.langs') as $locale) {
                $query->orWhere('name->' . $locale, 'LIKE', '%' . $search . '%');
            }
        });
    }
}
