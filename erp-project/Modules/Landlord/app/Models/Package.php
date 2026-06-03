<?php

namespace Modules\Landlord\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $connection = 'landlord';
    protected $fillable = ['name', 'slug', 'price', 'limit_users', 'limit_tenants', 'features', 'is_active'];
    protected $casts = ['features' => 'array', 'is_active' => 'boolean', 'price' => 'float'];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
