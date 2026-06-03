<?php

namespace Modules\Landlord\Models;

use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $fillable = ['name', 'domain', 'database', 'package_id', 'subscription_ends_at', 'status'];
    protected $casts = ['subscription_ends_at' => 'datetime'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
