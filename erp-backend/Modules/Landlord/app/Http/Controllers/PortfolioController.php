<?php

namespace Modules\Landlord\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Landlord\Models\Tenant;
use Modules\Landlord\Services\Payments\PayMobService;

class PortfolioController extends Controller
{
    public function __construct(
        protected PayMobService $paymob
    ) {}

    public function home()
    {
        return view('landlord::portfolio.home');
    }

    public function about()
    {
        return view('landlord::portfolio.about');
    }

    public function contact()
    {
        return view('landlord::portfolio.contact');
    }

    public function pricing() 
    {
        $packages = DB::connection('landlord')->table('packages')->where('is_active', true)->get();
        return view('landlord::portfolio.pricing', compact('packages'));
    }

    public function subscribeForm($package_id)
    {
        $package = DB::connection('landlord')->table('packages')->where('id', $package_id)->first();
        return view('landlord::portfolio.subscribe', compact('package'));
    }

    public function processCheckout(Request $request, $package_id)
    {
        Log::info('======= بداية عملية Checkout =======');

        $request->validate([
            'company_name' => 'required|string|max:255',
            'subdomain'    => 'required|alpha|min:3|max:20',
            'admin_name'   => 'required|string|max:255',
            'admin_email'  => 'required|email',
            'admin_password' => 'required|min:6',
        ]);

        $domainName = $request->subdomain . '.erp.test';

        if (DB::connection('landlord')->table('tenants')->where('domain', $domainName)->exists()) {
            return back()->withErrors(['subdomain' => 'هذا النطاق محجوز بالفعل!']);
        }

        $package = DB::connection('landlord')->table('packages')->where('id', $package_id)->first();
        if (!$package) {
            return back()->withErrors(['package' => 'Package not found.']);
        }

        $tenant = Tenant::create([
            'name'     => $request->company_name,
            'domain'   => $domainName,
            'database' => 'erp_tenant_' . $request->subdomain,
            'package_id' => $package_id,
            'status'   => 'pending',
        ]);

        // Store admin info temporarily in session for later use
        session([
            'pending_subscription' => [
                'tenant_id'       => $tenant->id,
                'admin_name'      => $request->admin_name,
                'admin_email'     => $request->admin_email,
                'admin_password'  => $request->admin_password,
                'subdomain'       => $request->subdomain,
            ],
        ]);

        // If PayMob is configured, redirect to payment
        // Otherwise provision immediately (dev mode)
        if ($this->paymob->isConfigured()) {
            try {
                $amount = (float) ($package->price ?? 0);
                $payment = $this->paymob->initPayment($tenant, $package_id, $amount);
                $iframeUrl = $this->paymob->getIframeUrl($payment->paymob_payment_key);

                Log::info('توجيه للدفع عبر PayMob', ['url' => $iframeUrl]);

                return redirect()->away($iframeUrl);
            } catch (\Exception $e) {
                Log::error('PayMob checkout failed', ['error' => $e->getMessage()]);
                $tenant->delete();
                session()->forget('pending_subscription');
                return back()->withErrors(['payment' => 'Payment gateway error. Please try again.']);
            }
        }

        // Fallback: direct provisioning without payment (dev mode)
        Log::info('PayMob غير مُهيأ، تشغيل الوضع المباشر (بدون دفع)');
        return $this->provisionTenant($tenant);
    }

    private function provisionTenant(Tenant $tenant)
    {
        $pending = session('pending_subscription');
        if (!$pending) {
            return redirect()->route('landlord.home');
        }

        try {
            $listener = new \Modules\Landlord\Listeners\CreateTenantDatabaseListener();
            $listener->handle($tenant);

            $dbName = $tenant->database;
            config(['database.connections.tenant.database' => $dbName]);
            \Illuminate\Support\Facades\DB::purge('tenant');
            \Illuminate\Support\Facades\DB::reconnect('tenant');

            if (\Illuminate\Support\Facades\DB::connection('tenant')->getSchemaBuilder()->hasTable('users')) {
                \Illuminate\Support\Facades\DB::connection('tenant')->table('users')->insert([
                    'name'       => $pending['admin_name'],
                    'email'      => $pending['admin_email'],
                    'password'   => \Illuminate\Support\Facades\Hash::make($pending['admin_password']),
                    'role'       => 'admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $userId = \Illuminate\Support\Facades\DB::connection('tenant')->table('users')->where('email', $pending['admin_email'])->value('id');
                $roleId = \Illuminate\Support\Facades\DB::connection('tenant')->table('roles')->where('name', 'Admin')->where('guard_name', 'tenant')->value('id');
                if ($userId && $roleId) {
                    \Illuminate\Support\Facades\DB::connection('tenant')->table('model_has_roles')->insert([
                        'role_id'    => $roleId,
                        'model_type' => 'App\Models\User',
                        'model_id'   => $userId,
                    ]);
                }
            }

            $tenant->update(['status' => 'active']);

            session()->forget('pending_subscription');

            return redirect()->route('landlord.payment.success-display')->with([
                'login_url' => "http://" . $pending['subdomain'] . ".erp.test:8000",
                'email'     => $pending['admin_email'],
            ]);
        } catch (\Exception $e) {
            Log::error('❌ فشل التجهيز:', ['error' => $e->getMessage()]);
            $tenant->delete();
            session()->forget('pending_subscription');
            return back()->withErrors(['provisioning' => 'Failed to create tenant.']);
        }
    }

    public function paymentSuccess()
    {
        return view('landlord::portfolio.success');
    }
}