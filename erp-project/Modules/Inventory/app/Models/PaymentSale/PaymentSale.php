<?php

namespace Modules\Inventory\Models\PaymentSale;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Filters\PaymentSale\PaymentSaleFilter;
use Modules\Inventory\Models\Sale\Sale;

class PaymentSale extends TenantBaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'Ref',
        'date',
        'Reglement',
        'montant',
        'change',
        'notes',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->Ref)) {
                $payment->Ref = self::getNumberOrder();
            }
        });
    }

    public static function getNumberOrder()
    {
        $last = static::withTrashed()->latest('id')->first();

        if ($last && !empty($last->Ref)) {
            $parts      = explode('_', $last->Ref);
            $lastNumber = isset($parts[1]) ? (int)$parts[1] : 0;
            $newNumber  = $lastNumber + 1;
            $code       = 'PS_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } else {
            $code = 'PS_0001';
        }

        while (self::where('Ref', $code)->exists()) {
            $lastNumber = (int)explode('_', $code)[1];
            $newNumber  = $lastNumber + 1;
            $code       = 'PS_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function scopeFilter($query, PaymentSaleFilter $filter)
    {
        return $filter->apply($query);
    }

    public function ordering($ordering = [])
    {
        if (empty($ordering) || empty($ordering['order_by'])) {
            return $this->orderBy('created_at', 'desc');
        }
        $order_by = $ordering["order_by"] ?? null;
        $order_type = (!empty($ordering["order_type"]) && in_array(strtolower($ordering["order_type"]), ["desc", "asc"])) ? $ordering["order_type"] : 'asc';
        if (in_array($order_by, ['Ref', 'date', 'montant', 'Reglement'])) {
            return $this->orderBy($order_by, $order_type);
        }
        return $this->orderBy('created_at', 'desc');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\Modules\Core\Models\User\User::class, 'user_id', 'id');
    }
}
