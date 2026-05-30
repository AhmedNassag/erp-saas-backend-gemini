<?php

namespace Modules\Landlord\Http\Controllers\SuperAdmin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function overview(): JsonResponse
    {
        $totalTenants        = DB::connection("landlord")->table("tenants")->count();
        $activeTenants       = DB::connection("landlord")->table("tenants")->where("status", "active")->count();
        $totalPackages       = DB::connection("landlord")->table("packages")->count();
        $expiringSoon        = DB::connection("landlord")->table("tenants")
            ->where("status", "active")
            ->where("subscription_ends_at", "<=", now()->addDays(30))
            ->count();

        return response()->json([
            "status" => "success",
            "data"   => [
                "total_tenants"        => $totalTenants,
                "active_subscriptions" => $activeTenants,
                "monthly_revenue"      => $this->calculateMonthlyRevenue(),
                "total_users"          => $totalTenants * 5, // estimate
                "expiring_soon"        => $expiringSoon,
                "total_packages"       => $totalPackages,
            ],
        ]);
    }

    public function revenue(): JsonResponse
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $end   = (clone $date)->endOfMonth();

            $revenue = DB::connection("landlord")
                ->table("tenants")
                ->join("packages", "tenants.package_id", "=", "packages.id")
                ->where("tenants.status", "active")
                ->where("tenants.created_at", "<=", $end)
                ->sum("packages.price");

            $count = DB::connection("landlord")->table("tenants")
                ->where("status", "active")
                ->where("created_at", "<=", $end)
                ->count();

            $months[] = [
                "month"   => $date->format("M Y"),
                "revenue" => round((float) $revenue, 2),
                "tenants" => $count,
            ];
        }

        return response()->json(["status" => "success", "data" => $months]);
    }

    private function calculateMonthlyRevenue(): float
    {
        return DB::connection("landlord")
            ->table("tenants")
            ->join("packages", "tenants.package_id", "=", "packages.id")
            ->where("tenants.status", "active")
            ->sum("packages.price");
    }
}
