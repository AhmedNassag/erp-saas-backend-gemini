<?php

namespace Modules\Inventory\Models\Brand;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Scout\Searchable;
use Modules\Inventory\Filters\Brand\BrandFilter;

class Brand extends TenantBaseModel implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Searchable;
    use Searchable {
        Searchable::search as parentSearch;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = static::getNumberOrder();
            }
        });
    }

    public static function getNumberOrder()
    {
        $last = static::withTrashed()->latest('id')->first();
        if ($last && !empty($last->code)) {
            $parts = explode('_', $last->code);
            $lastNumber = isset($parts[1]) ? (int)$parts[1] : 0;
            $newNumber = $lastNumber + 1;
            $code = 'BRD_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } else {
            $code = 'BRD_0001';
        }
        while (static::where('code', $code)->exists()) {
            $lastNumber = (int)explode('_', $code)[1];
            $newNumber = $lastNumber + 1;
            $code = 'BRD_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }
        return $code;
    }

    protected $fillable = [
        'name',
        'status',
        'code',
    ];

    protected $appends = [
        'image',
        'images',
    ];

    public function scopeStatus($query)
    {
        $query->where('status', 1);
    }

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
        ];
    }

    public function scopeFilter($query, BrandFilter $filter)
    {
        return $filter->apply($query);
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->orderBy('brands.id', 'DESC');
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

        return $this->orderBy('created_at', 'desc');
    }

    public function getImageAttribute()
    {
        $file = $this->getMedia('brand')->last();
        if ($file) {
            $file->id       = $this->getMedia('brand')->last()->id;
            $file->url      = $file->getUrl();
            $file->localUrl = app('url')->asset('storage/' . $file->id . '/' . $file->file_name);
        }
        return $file;
    }

    public function getImagesAttribute()
    {
        $files = $this->getMedia('brand_images');
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
}
