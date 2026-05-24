<?php

namespace App\Http\Controllers\SuperAdmin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DB::connection("landlord")->table("tenants")
            ->join("packages", "tenants.package_id", "=", "packages.id")
            ->select("tenants.*", "packages.name as package_name", "packages.price as package_price");

        if ($request->filled("search")) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where("tenants.name", "like", "%{$search}%")
                  ->orWhere("tenants.domain", "like", "%{$search}%");
            });
        }

        if ($request->filled("status")) {
            $query->where("tenants.status", $request->status);
        }

        $perPage = $request->input("per_page", 15);
        $page    = $request->input("page", 1);
        $total   = $query->count();
        $items   = $query->orderByDesc("tenants.created_at")
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            "status" => "success",
            "data"   => $items,
            "meta"   => [
                "total"        => $total,
                "current_page" => (int) $page,
                "per_page"     => (int) $perPage,
                "last_page"    => (int) ceil($total / $perPage),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $tenant = DB::connection("landlord")->table("tenants")
            ->join("packages", "tenants.package_id", "=", "packages.id")
            ->select("tenants.*", "packages.name as package_name", "packages.price as package_price")
            ->where("tenants.id", $id)
            ->first();

        if (!$tenant) {
            return response()->json(["status" => "error", "message" => "Tenant not found."], 404);
        }

        return response()->json(["status" => "success", "data" => $tenant]);
    }

    public function suspend(int $id): JsonResponse
    {
        DB::connection("landlord")->table("tenants")->where("id", $id)->update([
            "status"     => "suspended",
            "updated_at" => now(),
        ]);
        return response()->json(["status" => "success", "message" => "Tenant suspended."]);
    }

    public function activate(int $id): JsonResponse
    {
        DB::connection("landlord")->table("tenants")->where("id", $id)->update([
            "status"     => "active",
            "updated_at" => now(),
        ]);
        return response()->json(["status" => "success", "message" => "Tenant activated."]);
    }

    public function destroy(int $id): JsonResponse
    {
        DB::connection("landlord")->table("tenants")->where("id", $id)->delete();
        return response()->json(["status" => "success", "message" => "Tenant deleted."]);
    }
}