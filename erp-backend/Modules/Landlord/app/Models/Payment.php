<?php

namespace Modules\Landlord\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $connection = 'landlord';
    protected $fillable = [
        'tenant_id', 'package_id', 'amount_cents', 'currency',
        'paymob_order_id', 'paymob_transaction_id', 'paymob_payment_key',
        'status', 'paymob_response',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
