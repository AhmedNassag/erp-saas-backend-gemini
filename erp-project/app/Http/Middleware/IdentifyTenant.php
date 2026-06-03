<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Tenancy\CustomTenantFinder;
use Spatie\Multitenancy\Exceptions\NoCurrentTenant;

class IdentifyTenant
{
    public function handle($request, Closure $next)
    {
        $finder = app(CustomTenantFinder::class);
        $tenant = $finder->findForRequest($request);

        if (!$tenant) {
            return $next($request);
        }

        $tenant->makeCurrent();

        $dbName = config('database.connections.tenant.database');
        if ($dbName && Schema::hasTable('languages')) {
            try {
                $langs = DB::connection('tenant')->table('languages')
                    ->where('status', 1)
                    ->orderBy('is_default', 'desc')
                    ->pluck('code')
                    ->toArray();

                if (!empty($langs)) {
                    config(['myConfig.langs' => $langs]);
                }
            } catch (\Exception $e) {
                // fallback to default
            }
        }

        return $next($request);
    }
}
