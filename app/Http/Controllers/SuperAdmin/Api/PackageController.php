<?php

namespace App\Http\Controllers\SuperAdmin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index(): JsonResponse
    {
        $packages = DB::connection("landlord")->table("packages")
            ->orderByDesc("created_at")->get();

        return response()->json(["status" => "success", "data" => $packages]);
    }

    public function show(int $id): JsonResponse
    {
        $package = DB::connection("landlord")->table("packages")->where("id", $id)->first();
        if (!$package) {
            return response()->json(["status" => "error", "message" => "Package not found."], 404);
        }
        return response()->json(["status" => "success", "data" => $package]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            "name"        => "required|string|max:100",
            "slug"        => "required|string|unique:landlord.packages,slug",
            "price"       => "required|numeric|min:0",
            "limit_users" => "required|integer",
            "features"    => "nullable|array",
            "is_active"   => "boolean",
        ]);

        $data["features"]   = json_encode($data["features"] ?? []);
        $data["created_at"] = now();
        $data["updated_at"] = now();

        $id = DB::connection("landlord")->table("packages")->insertGetId($data);
        $package = DB::connection("landlord")->table("packages")->find($id);

        return response()->json(["status" => "success", "data" => $package], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            "name"        => "sometimes|string|max:100",
            "slug"        => "sometimes|string|unique:landlord.packages,slug," . $id,
            "price"       => "sometimes|numeric|min:0",
            "limit_users" => "sometimes|integer",
            "features"    => "nullable|array",
            "is_active"   => "boolean",
        ]);

        if (isset($data["features"])) {
            $data["features"] = json_encode($data["features"]);
        }

        $data["updated_at"] = now();
        DB::connection("landlord")->table("packages")->where("id", $id)->update($data);

        return response()->json(["status" => "success", "message" => "Package updated."]);
    }

    public function destroy(int $id): JsonResponse
    {
        $inUse = DB::connection("landlord")->table("tenants")->where("package_id", $id)->count();
        if ($inUse > 0) {
            return response()->json([
                "status"  => "error",
                "message" => "Cannot delete package — {$inUse} tenant(s) are using it.",
            ], 422);
        }

        DB::connection("landlord")->table("packages")->where("id", $id)->delete();
        return response()->json(["status" => "success", "message" => "Package deleted."]);
    }
}