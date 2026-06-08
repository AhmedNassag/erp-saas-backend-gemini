<?php

namespace Modules\Core\Models\City;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Laravel\Scout\Searchable;
use App\Traits\ActivityLogTrait;
use Modules\Core\Filters\City\CityFilter;
use Modules\Core\Models\Area\Area;
use Modules\Core\Models\Country\Country;

class City extends TenantBaseModel implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Searchable/*, HasTranslations*/;
    use Searchable {
        Searchable::search as parentSearch;
    }

    //this function use to make validation before destroy the record to refuse deleting if it has a related data in other tables
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if (
                $model->areas()->count() > 0
            ) {
                throw new \Exception(__('Can Not Delete Because There Is Related Data'));
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'status',
        'country_id'
    ];



    public function scopeStatus($query)
    {
        $query->where('status', 1);
    }



    public function scopeFilter($query, CityFilter $filter)
    {
        return $filter->apply($query);
    }



    ////////////////////////////// start search with relations models //////////////////////////////
    public function toSearchableArray()
    {
        return [
            'name'         => $this->name,
            'country.name' => $this->country ? $this->country->name : null,
        ];
    }



    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->join('countries', 'cities.country_id', '=', 'countries.id')
                ->select(['countries.name', 'cities.*'])
                ->orderBy('cities.id', 'DESC');
        });
    }
    ////////////////////////////// end search with relations models //////////////////////////////


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
        if (in_array($order_by, ['country_id'])) {
            $model = str_replace("y_id", "", $order_by);
            $models = $order_by == "country_id" ? $model . 'ies' : $model . 's';
            return $this->select("cities.*")->join($models, $models . ".id", "=", "cities." . $order_by)->orderByRaw('JSON_EXTRACT(' . $models . '.name, "$.' . app()->getLocale() . '") ' . $order_type);
        }
        return $this->orderBy('created_at', 'desc');
    }



    //start relations
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function areas()
    {
        return $this->hasMany(Area::class, 'city_id', 'id');
    }
}
