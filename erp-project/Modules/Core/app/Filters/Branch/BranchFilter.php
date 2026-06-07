<?php

namespace Modules\Core\Filters\Branch;
use App\Filters\Filters;


class BranchFilter extends Filters
{
    protected array $var_filters = [
        'search',
        'status',
        'code',
        'commercialRegistration',
        'taxCard',
        'mobile',
        'area_id',
        'address',
    ];



    public function address($address)
    {
        if($address != 'null' && $address != null) {
            return $this->builder->where('address',$address);
        }
    }



    public function area_id($area_id)
    {
        if($area_id != 'null' && $area_id != null) {
            return $this->builder->where('area_id',$area_id);
        }
    }



    public function mobile($mobile)
    {
        if($mobile != 'null' && $mobile != null) {
            return $this->builder->where('mobile',$mobile);
        }
    }



    public function taxCard($taxCard)
    {
        if($taxCard != 'null' && $taxCard != null) {
            return $this->builder->where('taxCard',$taxCard);
        }
    }



    public function commercialRegistration($commercialRegistration)
    {
        if($commercialRegistration != 'null' && $commercialRegistration != null) {
            return $this->builder->where('commercialRegistration',$commercialRegistration);
        }
    }



    public function code($code)
    {
        if($code != 'null' && $code != null) {
            return $this->builder->where('code',$code);
        }
    }



    public function status($status)
    {
        if($status != 'null' && $status != null) {
            return $this->builder->where('status',$status);
        }
    }


    
    public function search($search)
    {
        return $this->builder->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhereHas('area',function($q) use($search){
                $q->where('name', 'LIKE', '%' . $search . '%');
            });
        });
    }
}
