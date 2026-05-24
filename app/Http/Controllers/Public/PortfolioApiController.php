<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Landlord\Listeners\CreateTenantDatabaseListener;
use Illuminate\Support\Facades\Hash;

class PortfolioApiController extends Controller
{
    public function packages(): JsonResponse
    {
        $packages = DB::connection("landlord")->table("packages")
            ->where("is_active", true)
            ->orderBy("price")
            ->get();

        return response()->json(["status" => "success", "data" => $packages]);
    }

    public function settings(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data"   => [
                "company_name" => CmsSetting::get("company_name", "NexaERP"),
                "tagline_en"   => CmsSetting::get("tagline_en", "The Future of Business Management"),
                "tagline_ar"   => CmsSetting::get("tagline_ar", "مستقبل إدارة الأعمال"),
                "email"        => CmsSetting::get("email", "hello@nexaerp.com"),
                "phone"        => CmsSetting::get("phone", "+1 (234) 567-890"),
            ],
        ]);
    }

    public function features(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data"   => CmsSetting::get("features", []),
        ]);
    }

    public function testimonials(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data"   => CmsSetting::get("testimonials", []),
        ]);
    }

    public function subscribe(Request $request, int $packageId): JsonResponse
    {
        $request->validate([
            "company_name"    => "required|string|max:255",
            "subdomain"       => "required|alpha|min:3|max:20",
            "admin_name"      => "required|string|max:255",
            "admin_email"     => "required|email",
            "admin_password"  => "required|min:6",
        ]);

        $domainName = $request->subdomain . ".erp.test";
        $exists = DB::connection("landlord")->table("tenants")->where("domain", $domainName)->exists();
        if ($exists) {
            return response()->json(["status" => "error", "message" => "This subdomain is already taken."], 422);
        }

        $dbName   = "erp_tenant_" . $request->subdomain;
        $tenantId = DB::connection("landlord")->table("tenants")->insertGetId([
            "name"                 => $request->company_name,
            "domain"               => $domainName,
            "database"             => $dbName,
            "package_id"           => $packageId,
            "subscription_ends_at" => now()->addYear(),
            "status"               => "active",
            "created_at"           => now(),
            "updated_at"           => now(),
        ]);

        $tenant = DB::connection("landlord")->table("tenants")->where("id", $tenantId)->first();

        try {
            $listener = new CreateTenantDatabaseListener();
            $listener->handle($tenant);
        } catch (\Exception $e) {
            Log::error("Tenant creation failed: " . $e->getMessage());
            DB::connection("landlord")->table("tenants")->where("id", $tenantId)->delete();
            return response()->json(["status" => "error", "message" => "Failed to provision tenant database."], 500);
        }

        config(["database.connections.tenant.database" => $dbName]);
        DB::purge("tenant");
        DB::reconnect("tenant");

        DB::connection("tenant")->table("users")->insert([
            "name"       => $request->admin_name,
            "email"      => $request->admin_email,
            "password"   => Hash::make($request->admin_password),
            "role"       => "admin",
            "created_at" => now(),
            "updated_at" => now(),
        ]);

        $loginUrl = "http://" . $domainName . ":8000";

        return response()->json([
            "status"    => "success",
            "message"   => "Your ERP has been provisioned successfully!",
            "login_url" => $loginUrl,
            "email"     => $request->admin_email,
        ], 201);
    }

    public function contact(Request $request): JsonResponse
    {
        $request->validate([
            "name"    => "required|string|max:100",
            "email"   => "required|email",
            "subject" => "required|string",
            "message" => "required|string|min:10",
        ]);

        Log::info("Contact form submission", $request->only("name", "email", "subject"));

        // TODO: Send email notification
        // Mail::to("hello@nexaerp.com")->send(new ContactMail($request->all()));

        return response()->json([
            "status"  => "success",
            "message" => "Your message has been received. We will get back to you within 24 hours.",
        ]);
    }
}