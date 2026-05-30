<?php

namespace Modules\Landlord\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Modules\Landlord\Models\CmsSetting;
use Modules\Landlord\Models\Package;
use Modules\Landlord\Models\Tenant;
use Modules\Landlord\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Modules\Landlord\Listeners\CreateTenantDatabaseListener;
use Illuminate\Support\Facades\Hash;

class PortfolioApiController extends Controller
{
    public function packages(): JsonResponse
    {
        $packages = Package::where('is_active', true)->orderBy('price')->get();

        return response()->json(['status' => 'success', 'data' => $packages]);
    }

    public function settings(): JsonResponse
    {
        $hero = CmsSetting::get('hero', []);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'company_name'      => CmsSetting::get('company_name', 'NexaERP'),
                'tagline_en'        => CmsSetting::get('tagline_en', 'The Future of Business Management'),
                'tagline_ar'        => CmsSetting::get('tagline_ar', 'مستقبل إدارة الأعمال'),
                'email'             => CmsSetting::get('email', 'hello@nexaerp.com'),
                'phone'             => CmsSetting::get('phone', '+1 (234) 567-890'),
                'badge_en'          => $hero['badge_en'] ?? 'Trusted by 500+ companies worldwide',
                'badge_ar'          => $hero['badge_ar'] ?? 'موثوق من قبل أكثر من 500 شركة',
                'title_en'          => $hero['title_en'] ?? 'Scale Your Business with NexaERP',
                'title_ar'          => $hero['title_ar'] ?? 'طوّر أعمالك مع NexaERP',
                'title_highlight_en' => $hero['title_highlight_en'] ?? 'Without Limits',
                'title_highlight_ar' => $hero['title_highlight_ar'] ?? 'بلا حدود',
                'subtitle_en'       => $hero['subtitle_en'] ?? 'Enterprise-grade multi-tenant ERP with isolated databases, modular architecture, and global scalability.',
                'subtitle_ar'       => $hero['subtitle_ar'] ?? 'نظام ERP متعدد المستأجرين من الفئة المؤسسية مع قواعد بيانات معزولة.',
                'cta_primary_en'    => $hero['cta_primary_en'] ?? 'View Pricing',
                'cta_primary_ar'    => $hero['cta_primary_ar'] ?? 'عرض الأسعار',
                'cta_secondary_en'  => $hero['cta_secondary_en'] ?? 'Contact Sales',
                'cta_secondary_ar'  => $hero['cta_secondary_ar'] ?? 'اتصل بالمبيعات',
            ],
        ]);
    }

    public function modules(): JsonResponse
    {
        $modulesPath = base_path('Modules');
        $result = [];

        if (File::isDirectory($modulesPath)) {
            $directories = File::directories($modulesPath);
            foreach ($directories as $dir) {
                $jsonPath = $dir . '/module.json';
                if (File::exists($jsonPath)) {
                    $config = json_decode(File::get($jsonPath), true);
                    $name = $config['name'] ?? basename($dir);
                    $alias = $config['alias'] ?? strtolower(basename($dir));

                    $result[] = [
                        'key'   => $alias,
                        'name'  => $name,
                        'label' => [
                            'en' => $name,
                            'ar' => $name,
                            'fr' => $name,
                        ],
                    ];
                }
            }
        }

        return response()->json(['status' => 'success', 'data' => $result]);
    }

    public function features(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => CmsSetting::get('features', []),
        ]);
    }

    public function testimonials(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => CmsSetting::get('testimonials', []),
        ]);
    }

    public function subscribe(Request $request, int $packageId): JsonResponse
    {
        $data = $request->validate([
            'company_name'   => 'required|string|max:255',
            'subdomain'      => 'required|alpha|min:3|max:20',
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|email',
            'admin_password' => 'required|min:6',
        ]);

        $domainName = $data['subdomain'] . '.erp.test';

        if (Tenant::where('domain', $domainName)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'This subdomain is already taken.'], 422);
        }

        $package = Package::findOrFail($packageId);
        $dbName  = 'erp_tenant_' . $data['subdomain'];

        $tenant = Tenant::create([
            'name'     => $data['company_name'],
            'domain'   => $domainName,
            'database' => $dbName,
            'package_id' => $packageId,
            'status'   => 'active',
        ]);

        try {
            $listener = new CreateTenantDatabaseListener();
            $listener->handle($tenant);
        } catch (\Exception $e) {
            Log::error('Tenant creation failed: ' . $e->getMessage());
            $tenant->delete();
            return response()->json(['status' => 'error', 'message' => 'Failed to provision tenant database.'], 500);
        }

        // Create subscription record
        Subscription::create([
            'tenant_id'  => $tenant->id,
            'package_id' => $packageId,
            'status'     => 'active',
            'starts_at'  => now(),
            'ends_at'    => now()->addYear(),
        ]);

        config(['database.connections.tenant.database' => $dbName]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        DB::connection('tenant')->table('users')->insert([
            'name'       => $data['admin_name'],
            'email'      => $data['admin_email'],
            'password'   => Hash::make($data['admin_password']),
            'role'       => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId = DB::connection('tenant')->table('users')->where('email', $data['admin_email'])->value('id');
        $roleId = DB::connection('tenant')->table('roles')->where('name', 'Admin')->where('guard_name', 'tenant')->value('id');
        if ($userId && $roleId) {
            DB::connection('tenant')->table('model_has_roles')->insert([
                'role_id'    => $roleId,
                'model_type' => 'App\Models\User',
                'model_id'   => $userId,
            ]);
        }

        $loginUrl = 'http://' . $domainName . ':8000';

        return response()->json([
            'status'    => 'success',
            'message'   => 'Your ERP has been provisioned successfully!',
            'login_url' => $loginUrl,
            'email'     => $data['admin_email'],
        ], 201);
    }

    public function contact(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string|min:10',
        ]);

        Log::info('Contact form submission', $data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Your message has been received. We will get back to you within 24 hours.',
        ]);
    }
}
