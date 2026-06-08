<?php

namespace Modules\Core\Models\Branch;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Laravel\Scout\Searchable;
use App\Traits\ActivityLogTrait;
use Modules\Core\Filters\Branch\BranchFilter;
use Modules\Core\Models\Area\Area;
use Modules\Core\Models\Warehouse\Warehouse;

class Branch extends TenantBaseModel implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Searchable/*, HasTranslations*/;
    use Searchable {
        Searchable::search as parentSearch;
    }



    //this function use to make validation before destroy the record to refuse deleting if it has a related data in other tables
    protected static function boot()
    {
        parent::boot();
        // static::deleting(function ($model) {
        //     if (
        //         $model->branches()->count() > 0
        //     ) {
        //         throw new \Exception(__('Can Not Delete Beacause There Is A Related Data'));
        //     }
        // });
    }



    protected $fillable = [
        'name',
        'status',
        'code',
        'commercialRegistration',
        'taxCard',
        'mobile',
        'address',
        'area_id',
    ];



    protected $appends = [
        'image',
        'images'
    ];



    public function scopeStatus($query)
    {
        $query->where('status', 1);
    }



    public function toSearchableArray(): array
    {
        return [
            'name'           => $this->name,
            'code'           => $this->code,
            'commercialRegistration' => $this->commercialRegistration,
            'taxCard'        => $this->taxCard,
            'mobile'         => $this->mobile,
            'area_name'      => $this->area ? $this->area->name : null
        ];
    }



    public function scopeFilter($query, BranchFilter $filter)
    {
        return $filter->apply($query);
    }



    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->join('areas', 'branches.area_id', '=', 'areas.id')
                ->select(['areas.name', 'branches.*'])
                ->orderBy('branches.id', 'DESC');
        });
    }



    public function ordering($ordering = [])
    {
        if (empty($ordering) || empty($ordering['order_by'])) {
            return $this->orderBy('created_at', 'desc');
        }
        $order_by = $ordering["order_by"] ?? null;
        $order_type = (!empty($ordering["order_type"]) && in_array(strtolower($ordering["order_type"]), ["desc", "asc"])) ? $ordering["order_type"] : 'asc';
        if ($order_by == 'name') {
            return $this->orderBy($order_by, $order_type);
        }
        if ($order_by == 'code') {
            return $this->orderBy($order_by, $order_type);
        }
        if ($order_by == 'commercialRegistration') {
            return $this->orderBy($order_by, $order_type);
        }
        if ($order_by == 'taxCard') {
            return $this->orderBy($order_by, $order_type);
        }
        if (in_array($order_by, ['area_id'])) {
            $model = str_replace("y_id", "", $order_by);
            $models = $order_by == "area_id" ? $model . 'ies' : $model . 's';
            return $this->select("branches.*")->join($models, $models . ".id", "=", "branches." . $order_by)->orderBy($models . '.name', $order_type);
        }

        return $this->orderBy('created_at', 'desc');
    }



    ///////////////////////// start image /////////////////////////
    public function getImageAttribute()
    {
        $file = $this->getMedia('branch')->last();
        if ($file) {
            $file->id       = $this->getMedia('branch')->last()->id;
            $file->url      = $file->getUrl();
            $file->localUrl = app('url')->asset('storage/' . $file->id . '/' . $file->file_name);
        }
        return $file;
    }



    public function getImagesAttribute()
    {
        $files = $this->getMedia('branch_images');
        return $this->filesData($files);
    }



    public function filesData($data)
    {
        $urls = [];
        foreach ($data as $key => $file) {
            $urls[$key]['id']  = $file->id;
            $urls[$key]['url'] = $file->getFullUrl();
            $file->localUrl    = app('url')->asset('storage/' . $file->id . '/' . $file->file_name);
        }
        return ($urls);
    }
    ///////////////////////// end image /////////////////////////



    //start relations
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, 'branch_id', 'id');
    }
}