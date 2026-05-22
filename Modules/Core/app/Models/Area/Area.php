<?php

namespace Modules\Core\Models\Area;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Scout\Searchable;
use App\Traits\ActivityLogTrait;
use Modules\Core\Filters\Area\AreaFilter;
use Modules\Core\Models\Branch\Branch;
use Modules\Core\Models\City\City;

class Area extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Searchable;
    use Searchable {
        Searchable::search as parentSearch;
    }



    //this function use to make validation before destroy the record to refuse deleting if it has a related data in other tables
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            if (
                $model->branches()->count() > 0
            ) {
                throw new \Exception(__('Can Not Delete Beacause There Is A Related Data'));
            }
        });
    }

    
    
    protected $fillable = [
        'name',
        'status',
        'city_id'
    ];



    public function scopeStatus($query)
    {
        $query->where('status', 1);
    }



    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }



    public function scopeFilter($query, AreaFilter $filter)
    {
        return $filter->apply($query);
    }



    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->join('cities', 'areas.city_id', '=', 'cities.id')
                ->select(['cities.name', 'areas.*'])
                ->orderBy('areas.id', 'DESC');
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
            return $this->orderBy('name', $order_type);
        }
        if ($order_by == 'status') {
            return $this->orderBy($order_by, $order_type);
        }

        if (in_array($order_by, ['city_id'])) {
            $model = str_replace("y_id", "", $order_by);
            $models = $order_by == "city_id" ? $model . 'ies' : $model . 's';
            return $this->select("areas.*")->join($models, $models . ".id", "=", "areas." . $order_by)->orderBy($models . '.name', $order_type);
        }

        return $this->orderBy('created_at', 'desc');
    }


    //start relations
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}