<?php

namespace App\Tenancy;

use Illuminate\Http\Request;
use Spatie\Multitenancy\TenantFinder\TenantFinder;
use Modules\Landlord\Models\Tenant;

class CustomTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        $host = $request->getHost();

        if ($host === 'erp.test' || $host === 'localhost' || $host === '127.0.0.1') {
            return null;
        }

        return Tenant::where('domain', $host)->first();
    }
}
