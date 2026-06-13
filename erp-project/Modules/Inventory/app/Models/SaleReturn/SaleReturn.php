<?php

namespace Modules\Inventory\Models\SaleReturn;

use App\Models\TenantBaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Inventory\Filters\SaleReturn\SaleReturnFilter;
use Modules\Inventory\Models\SaleReturnDetail\SaleReturnDetail;
use Modules\Inventory\Models\PaymentSaleReturn\PaymentSaleReturn;
use Modules\Inventory\Models\Client\Client;
use Modules\Core\Models\Warehouse\Warehouse;

class SaleReturn extends TenantBaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'Ref',
        'date',
        'client_id',
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

        static::creating(function ($saleReturn) {
            if (empty($saleReturn->Ref)) {
                $saleReturn->Ref = self::getNumberOrder();
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

    public function scopeFilter($query, SaleReturnFilter $filter)
    {
        return $filter->apply($query);
    }

    public static function search($query = '', $callback = null)
    {
        return static::parentSearch($query, $callback)->query(function ($builder) use ($query) {
            $builder->orderBy('sale_returns.id', 'DESC');
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
            $code       = 'SR_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } else {
            $code = 'SR_0001';
        }

        while (self::where('Ref', $code)->exists()) {
            $lastNumber = (int)explode('_', $code)[1];
            $newNumber  = $lastNumber + 1;
            $code       = 'SR_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function saleReturnDetails()
    {
        return $this->hasMany(SaleReturnDetail::class, 'sale_return_id', 'id');
    }

    public function paymentSaleReturns()
    {
        return $this->hasMany(PaymentSaleReturn::class, 'sale_return_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\Modules\Core\Models\User\User::class, 'user_id', 'id');
    }
}
