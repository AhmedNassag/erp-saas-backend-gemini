<?php

namespace Modules\Inventory\Models\Adjustment;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Filters\Adjustment\AdjustmentFilter;
use Modules\Inventory\Models\AdjustmentDetail\AdjustmentDetail;
use Modules\Core\Models\Warehouse\Warehouse;

class Adjustment extends TenantBaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'Ref',
        'date',
        'items',
        'notes',
        'warehouse_id',
        'user_id',
    ];

    // Boot method to auto-generate Ref
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($adjustment) {
            if (empty($adjustment->Ref)) {
                $adjustment->Ref = self::getNumberOrder();
            }
        });
    }

    public function toSearchableArray(): array
    {
        return [
            'Ref'  => $this->Ref,
            'date' => $this->date,
        ];
    }

    public function scopeFilter($query, AdjustmentFilter $filter)
    {
        return $filter->apply($query);
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->orderBy('adjustments.id', 'DESC');
        });
    }

    public function ordering($ordering = [])
    {
        if (empty($ordering) || empty($ordering['order_by'])) {
            return $this->orderBy('created_at', 'desc');
        }
        $order_by = $ordering["order_by"] ?? null;
        $order_type = (!empty($ordering["order_type"]) && in_array(strtolower($ordering["order_type"]), ["desc", "asc"])) ? $ordering["order_type"] : 'asc';
        if ($order_by == 'Ref') {
            return $this->orderBy($order_by, $order_type);
        }
        if ($order_by == 'date') {
            return $this->orderBy($order_by, $order_type);
        }

        return $this->orderBy('created_at', 'desc');
    }

    /**
     * Generate unique reference number for adjustment
     * 
     * @return string
     */
    public static function getNumberOrder()
    {
        // Get the last adjustment record using Eloquent (respects tenant connection)
        $last = static::withTrashed()->latest('id')->first();

        if ($last && !empty($last->Ref)) {
            // Extract the number from Ref (format: AD_XXXX)
            $parts      = explode('_', $last->Ref);
            $lastNumber = isset($parts[1]) ? (int)$parts[1] : 0;
            $newNumber  = $lastNumber + 1;
            $code       = 'AD_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } else {
            // First record, start with AD_0001
            $code = 'AD_0001';
        }

        // Ensure the code is unique (in case of race condition)
        while (self::where('Ref', $code)->exists()) {
            $lastNumber = (int)explode('_', $code)[1];
            $newNumber  = $lastNumber + 1;
            $code       = 'AD_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }



    //start relations
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function adjustmentDetails()
    {
        return $this->hasMany(AdjustmentDetail::class, 'adjustment_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\Modules\Core\Models\User\User::class, 'user_id', 'id');
    }
}
