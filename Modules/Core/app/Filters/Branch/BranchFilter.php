<?php

namespace Modules\Core\Filters\Branch;
use App\Filters\Filters;


class BranchFilter extends Filters
{
    protected $var_filters=[
        'search','search_email','search_mobile','area_id','branchable_id','branchable_type','status'
    ];


    public function area_id($area_id)
    {
        if($area_id!='null' && $area_id!=null){
            return $this->builder->where('area_id',$area_id);
        }

    }


    public function status($status)
    {
        if($status!='null' && $status!=null){
            return $this->builder->where('status',$status);
        }

    }


    public function branchable_id($branchable_id)
    {
        if($branchable_id!='null' && $branchable_id!=null){
            return $this->builder->where('branchable_id',$branchable_id);
        }
    }
    
    public function branchable_type($branchable_type)
    {
        if($branchable_type!='null' && $branchable_type != null){
            return $this->builder->where('branchable_type',$branchable_type);
        }

    }
    
    /*public function subject_id($subject_id)
    {
        if($subject_id=='null'||$subject_id==null){
            return $this->builder->where('subject_id',null);
        }else{
            return $this->builder->where('subject_id',$subject_id);
        }

    }*/
    
    // public function search($search)
    // {
    //     return $this->builder->where(function ($query) use ($search) {
    //         foreach (config('myConfig.langs') as $locale) {
    //             $query->orWhere('name->' . $locale, 'LIKE', '%' . $search . '%');
    //         }
    //     });
    // }
    // public function search_email($search)
    // {
    //     return $this->builder->where('email', 'LIKE', '%' . $search . '%');

    // }
    // public function search_mobile($search)
    // {
    //     return $this->builder->where('mobile', 'LIKE', '%' . $search . '%');

    // }
    public function search($search)
    {
        return $this->builder->where(function ($query) use ($search) {
            $query->where('email','LIKE','%'.$search.'%')
            ->orWhere('mobile','LIKE','%'.$search.'%')
            ->orWhere('branch_code','LIKE','%'.$search.'%')
            ->orWhere('user_email','LIKE','%'.$search.'%')
            ->orWhere('user_mobile','LIKE','%'.$search.'%')
            ->orWhere('user_name','LIKE','%'.$search.'%')
            ->orWhere(function($q) use($search){
                foreach (config('myConfig.langs') as $i=>$locale) {
                    if($i==0)
                        $q->where('name->' . $locale, 'LIKE', '%' . $search . '%');
                    else
                        $q->orWhere('name->' . $locale, 'LIKE', '%' . $search . '%');
                }
            })
            ->orWhereHas('area',function($q) use($search){
                foreach (config('myConfig.langs') as $i=>$locale) {
                    if($i==0)
                        $q->where('name->' . $locale, 'LIKE', '%' . $search . '%');
                    else
                        $q->orWhere('name->' . $locale, 'LIKE', '%' . $search . '%');
                }
            });
        });
    }
   

}
