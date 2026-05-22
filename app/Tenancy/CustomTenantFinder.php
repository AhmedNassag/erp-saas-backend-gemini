<?php

namespace App\Tenancy;

use Illuminate\Http\Request;
use Spatie\Multitenancy\TenantFinder\TenantFinder;
use Spatie\Multitenancy\Models\Tenant;

class CustomTenantFinder extends TenantFinder
{
    public function findForRequest(Request $request): ?Tenant
    {
        $host = $request->getHost(); // بيجيب الدومين الحالي (مثلا erp.test أو alpha.erp.test)

        // 💡 الحركة السحرية: لو واقف على الدومين الرئيسي لـ الـ SaaS، بنرجع null فورا بدون ما نضرب إيرور
        if ($host === 'erp.test' || $host === 'localhost' || $host === '127.0.0.1') {
            return null; 
        }

        // لو مش الدومين الرئيسي (يعني ساب دومين للعملاء)، بندور عليه بالـ domain مباشرة
        return Tenant::where('domain', $host)->first();
    }
}