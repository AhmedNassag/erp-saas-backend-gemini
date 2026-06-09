<?php

namespace Modules\Inventory\Models\Product;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Scout\Searchable;
use Modules\Inventory\Filters\Product\ProductFilter;

class Product extends TenantBaseModel implements HasMedia
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
        'code',
        'Type_barcode',
        'name',
        'status',
        'cost',
        'price',
        'category_id',
        'brand_id',
        'unit_id',
        'unit_sale_id',
        'unit_purchase_id',
        'TaxNet',
        'tax_method',
        'note',
        'stock_alert',
        'is_variant',
        'is_active',
    ];

    protected $appends = [
        'image',
        'images',
    ];

    protected $casts = [
        'status'      => 'boolean',
        'is_variant'  => 'boolean',
        'is_active'   => 'boolean',
        'TaxNet'      => 'float',
        'cost'        => 'float',
        'price'       => 'float',
        'stock_alert' => 'float',
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

    public function scopeFilter($query, ProductFilter $filter)
    {
        return $filter->apply($query);
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->orderBy('products.id', 'DESC');
        });
    }

    public function ordering($ordering = [])
    {
        if (empty($ordering) || empty($ordering['order_by'])) {
            return $this->orderBy('created_at', 'desc');
        }
        $order_by   = $ordering["order_by"] ?? null;
        $order_type = (!empty($ordering["order_type"]) && in_array(strtolower($ordering["order_type"]), ["desc", "asc"])) ? $ordering["order_type"] : 'asc';
        if (in_array($order_by, ['name', 'code', 'price', 'cost'])) {
            return $this->orderBy($order_by, $order_type);
        }
        return $this->orderBy('created_at', 'desc');
    }

    public function getImageAttribute()
    {
        $file = $this->getMedia('product')->last();
        if ($file) {
            $file->id       = $this->getMedia('product')->last()->id;
            $file->url      = $file->getUrl();
            $file->localUrl = app('url')->asset('storage/' . $file->id . '/' . $file->file_name);
        }
        return $file;
    }

    public function getImagesAttribute()
    {
        $files = $this->getMedia('product_images');
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
        return $urls;
    }

    public function category()
    {
        return $this->belongsTo(\Modules\Inventory\Models\Category\Category::class, 'category_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(\Modules\Inventory\Models\Brand\Brand::class, 'brand_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\Inventory\Models\Unit\Unit::class, 'unit_id', 'id');
    }

    public function unitSale()
    {
        return $this->belongsTo(\Modules\Inventory\Models\Unit\Unit::class, 'unit_sale_id', 'id');
    }

    public function unitPurchase()
    {
        return $this->belongsTo(\Modules\Inventory\Models\Unit\Unit::class, 'unit_purchase_id', 'id');
    }

    public function variants()
    {
        return $this->hasMany(\Modules\Inventory\Models\ProductVariant\ProductVariant::class, 'product_id', 'id');
    }

    public function productWarehouses()
    {
        return $this->hasMany(\Modules\Inventory\Models\ProductWarehouse\ProductWarehouse::class, 'product_id', 'id');
    }
}
