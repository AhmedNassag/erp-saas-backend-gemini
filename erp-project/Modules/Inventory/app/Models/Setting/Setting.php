<?php

namespace Modules\Inventory\Models\Setting;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Scout\Searchable;
use Modules\Inventory\Filters\Setting\SettingFilter;

class Setting extends TenantBaseModel implements HasMedia
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
        'companyName',
        'companyPhone',
        'companyAdress',
        'developed_by',
        'footer',
        'currency_id',
        'client_id',
        'warehouse_id',
    ];

    protected $appends = [
        'image',
        'images',
    ];

    public function scopeFilter($query, SettingFilter $filter)
    {
        return $filter->apply($query);
    }

    public function toSearchableArray(): array
    {
        return [
            'companyName' => $this->companyName,
        ];
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->orderBy('settings.id', 'DESC');
        });
    }

    public function ordering($ordering = [])
    {
        return $this->orderBy('created_at', 'desc');
    }

    public function getImageAttribute()
    {
        $file = $this->getMedia('setting')->last();
        if ($file) {
            $file->id       = $this->getMedia('setting')->last()->id;
            $file->url      = $file->getUrl();
            $file->localUrl = app('url')->asset('storage/' . $file->id . '/' . $file->file_name);
        }
        return $file;
    }

    public function getImagesAttribute()
    {
        $files = $this->getMedia('setting_images');
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

    public function currency()
    {
        return $this->belongsTo(\Modules\Inventory\Models\Currency\Currency::class, 'currency_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(\Modules\Inventory\Models\Client\Client::class, 'client_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(\Modules\Core\Models\Warehouse\Warehouse::class, 'warehouse_id', 'id');
    }
}
