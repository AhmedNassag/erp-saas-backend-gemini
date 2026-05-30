<?php

namespace Modules\Landlord\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $connection = 'landlord';
    protected $fillable = ['tenant_id', 'package_id', 'status', 'trial_ends_at', 'starts_at', 'ends_at', 'cancelled_at'];
    protected $casts = ['trial_ends_at' => 'datetime', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'cancelled_at' => 'datetime'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeActive($q)
    {
        return $q->whereIn('status', ['trialing', 'active']);
    }
}
