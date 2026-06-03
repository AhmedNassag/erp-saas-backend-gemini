<?php

namespace Modules\Core\Models\Department;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Laravel\Scout\Searchable;
use App\Traits\ActivityLogTrait;
use Modules\Core\Filters\Department\DepartmentFilter;
use Modules\Core\Models\City\City;


class Department extends TenantBaseModel implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Searchable/*, HasTranslations*/;

    //this function use to make validation before destroy the record to refuse deleting if it has a related data in other tables
    protected static function boot()
    {
        parent::boot();

        // static::deleting(function($model) {
            // if
            // (
            //     $model->users()->count() > 0 
            // )
            // {
            //     throw new \Exception(__('Can Not Delete Beacause There Is A Related Data'));
            // }
        // });
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'status'
    ];

    public function scopeStatus($query)
    {
        $query->where('status' , 1);
    }



    public function scopeFilter($query, DepartmentFilter $filter)
    {
        return $filter->apply($query);
    }



    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }


    public function ordering($ordering=[])
    {
        if(empty($ordering) || empty($ordering['order_by'])) {
            return $this->orderBy('created_at', 'desc');
        }
        $order_by=$ordering["order_by"]??null;
        $order_type= (!empty($ordering["order_type"]) && in_array(strtolower($ordering["order_type"]),["desc","asc"]))?$ordering["order_type"]:'asc';
        if($order_by == 'name') {
            return $this->orderBy($order_by, $order_type);
        }
        if($order_by == 'status') {
            return $this->orderBy($order_by,$order_type);
        }
        return $this->orderBy('created_at', 'desc');
    }



    //start relations
    public function users()
    {
        // return $this->hasMany(\Modules\Core\Models\User\User::class, 'department_id'); 
    }
}
