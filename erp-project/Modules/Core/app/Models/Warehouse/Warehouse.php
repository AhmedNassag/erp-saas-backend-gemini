<?php

namespace Modules\Core\Models\Warehouse;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Scout\Searchable;
use Modules\Core\Filters\Warehouse\WarehouseFilter;

class Warehouse extends TenantBaseModel implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Searchable;
    use Searchable {
        Searchable::search as parentSearch;
    }

    protected static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'name',
        'status',
        'mobile',
        'branch_id',
        'area_id',
        'address',
        'is_main',
    ];

    protected $appends = [
        'image',
        'images',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function scopeStatus($query)
    {
        $query->where('status', 1);
    }

    public function toSearchableArray(): array
    {
        return [
            'name'        => $this->name,
            'mobile'      => $this->mobile,
            'area_name'   => $this->area ? $this->area->name : null,
            'branch_name' => $this->branch ? $this->branch->name : null,
        ];
    }

    public function scopeFilter($query, WarehouseFilter $filter)
    {
        return $filter->apply($query);
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->join('areas', 'warehouses.area_id', '=', 'areas.id')
                ->select(['areas.name', 'warehouses.*'])
                ->orderBy('warehouses.id', 'DESC');
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
        if ($order_by == 'mobile') {
            return $this->orderBy($order_by, $order_type);
        }
        if ($order_by == 'is_main') {
            return $this->orderBy($order_by, $order_type);
        }
        if (in_array($order_by, ['area_id', 'branch_id'])) {
            $model = str_replace("y_id", "", $order_by);
            $models = in_array($order_by, ['area_id', 'branch_id']) ? ($order_by == 'area_id' ? 'areas' : 'branches') : $model . 's';
            return $this->select("warehouses.*")->join($models, $models . ".id", "=", "warehouses." . $order_by)->orderBy($models . '.name', $order_type);
        }

        return $this->orderBy('created_at', 'desc');
    }

    public function getImageAttribute()
    {
        $file = $this->getMedia('warehouse')->last();
        if ($file) {
            $file->id       = $this->getMedia('warehouse')->last()->id;
            $file->url      = $file->getUrl();
            $file->localUrl = app('url')->asset('storage/' . $file->id . '/' . $file->file_name);
        }
        return $file;
    }

    public function getImagesAttribute()
    {
        $files = $this->getMedia('warehouse_images');
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



    //start relations
    public function area()
    {
        return $this->belongsTo(\Modules\Core\Models\Area\Area::class, 'area_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\Core\Models\Branch\Branch::class, 'branch_id', 'id');
    }
}
