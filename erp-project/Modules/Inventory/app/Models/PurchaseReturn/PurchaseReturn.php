<?php

namespace Modules\Inventory\Models\PurchaseReturn;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Filters\PurchaseReturn\PurchaseReturnFilter;
use Modules\Inventory\Models\PurchaseReturnDetail\PurchaseReturnDetail;
use Modules\Inventory\Models\PaymentPurchaseReturn\PaymentPurchaseReturn;
use Modules\Inventory\Models\Provider\Provider;
use Modules\Core\Models\Warehouse\Warehouse;

class PurchaseReturn extends TenantBaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'Ref',
        'date',
        'provider_id',
        'warehouse_id',
        'tax_rate',
        'TaxNet',
        'discount',
        'shipping',
        'GrandTotal',
        'paid_amount',
        'payment_status',
        'status',
        'notes',
        'items',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseReturn) {
            if (empty($purchaseReturn->Ref)) {
                $purchaseReturn->Ref = self::getNumberOrder();
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

    public function scopeFilter($query, PurchaseReturnFilter $filter)
    {
        return $filter->apply($query);
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->orderBy('purchase_returns.id', 'DESC');
        });
    }

    public function ordering($ordering = [])
    {
        if (empty($ordering) || empty($ordering['order_by'])) {
            return $this->orderBy('created_at', 'desc');
        }
        $order_by = $ordering["order_by"] ?? null;
        $order_type = (!empty($ordering["order_type"]) && in_array(strtolower($ordering["order_type"]), ["desc", "asc"])) ? $ordering["order_type"] : 'asc';
        if (in_array($order_by, ['Ref', 'date', 'GrandTotal', 'payment_status', 'status'])) {
            return $this->orderBy($order_by, $order_type);
        }
        return $this->orderBy('created_at', 'desc');
    }

    public static function getNumberOrder()
    {
        $last = static::withTrashed()->latest('id')->first();

        if ($last && !empty($last->Ref)) {
            $parts      = explode('_', $last->Ref);
            $lastNumber = isset($parts[1]) ? (int)$parts[1] : 0;
            $newNumber  = $lastNumber + 1;
            $code       = 'PR_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } else {
            $code = 'PR_0001';
        }

        while (self::where('Ref', $code)->exists()) {
            $lastNumber = (int)explode('_', $code)[1];
            $newNumber  = $lastNumber + 1;
            $code       = 'PR_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function purchaseReturnDetails()
    {
        return $this->hasMany(PurchaseReturnDetail::class, 'purchase_return_id', 'id');
    }

    public function paymentPurchaseReturns()
    {
        return $this->hasMany(PaymentPurchaseReturn::class, 'purchase_return_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\Modules\Core\Models\User\User::class, 'user_id', 'id');
    }
}
