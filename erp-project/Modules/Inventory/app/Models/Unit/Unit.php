<?php

namespace Modules\Inventory\Models\Unit;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Modules\Inventory\Filters\Unit\UnitFilter;

class Unit extends TenantBaseModel
{
    use HasFactory, SoftDeletes, Searchable;
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
        'shortName',
        'base_unit',
        'operator',
        'operator_value',
    ];

    public function scopeStatus($query)
    {
        $query->where('status', 1);
    }

    public function toSearchableArray(): array
    {
        return [
            'name'      => $this->name,
            'code'      => $this->code,
            'shortName' => $this->shortName,
        ];
    }

    public function scopeFilter($query, UnitFilter $filter)
    {
        return $filter->apply($query);
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->orderBy('units.id', 'DESC');
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

    public function parent()
    {
        return $this->belongsTo(Unit::class, 'base_unit', 'id');
    }

    public function children()
    {
        return $this->hasMany(Unit::class, 'base_unit', 'id');
    }
}
