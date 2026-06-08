<?php

namespace Modules\Inventory\Models\Category;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Scout\Searchable;
use Modules\Inventory\Filters\Category\CategoryFilter;

class Category extends TenantBaseModel implements HasMedia
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
        'code',
        'is_main',
        'category_id',
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
            'code'        => $this->code,
            'is_main'     => $this->is_main,
            'category_id' => $this->category_id,
        ];
    }

    public function scopeFilter($query, CategoryFilter $filter)
    {
        return $filter->apply($query);
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->orderBy('categories.id', 'DESC');
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
        $file = $this->getMedia('category')->last();
        if ($file) {
            $file->id       = $this->getMedia('category')->last()->id;
            $file->url      = $file->getUrl();
            $file->localUrl = app('url')->asset('storage/' . $file->id . '/' . $file->file_name);
        }
        return $file;
    }

    public function getImagesAttribute()
    {
        $files = $this->getMedia('category_images');
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

    public function parent()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'category_id', 'id');
    }
}
