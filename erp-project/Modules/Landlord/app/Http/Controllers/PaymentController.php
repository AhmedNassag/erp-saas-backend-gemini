<?php

namespace Modules\Landlord\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Landlord\Models\Package;
use Modules\Landlord\Models\Payment;
use Modules\Landlord\Services\Payments\PayMobService;
use Modules\Landlord\Listeners\CreateTenantDatabaseListener;

class PaymentController extends Controller
{
    public function __construct(
        protected PayMobService $paymob
    ) {}

    public function success(Request $request)
    {
        $data = $request->all();

        Log::info('PayMob callback received', $data);

        if (!$this->paymob->verifyHmac($data)) {
            Log::error('PayMob HMAC verification failed', $data);
            return view('landlord::portfolio.failed', ['message' => 'Invalid payment signature.']);
        }

        $payment = $this->paymob->processCallback($data);
        if (!$payment) {
            return view('landlord::portfolio.failed', ['message' => 'Payment record not found.']);
        }

        if ($payment->status !== 'paid') {
            return view('landlord::portfolio.failed', ['message' => 'Payment was not successful.']);
        }

        $tenant = $payment->tenant;
        if (!$tenant) {
            return view('landlord::portfolio.failed', ['message' => 'Tenant not found.']);
        }

        if ($tenant->status === 'active') {
            return redirect()->route('landlord.payment.success-display')->with([
                'login_url' => "http://{$tenant->domain}:8000",
                'email'     => $payment->paymob_response ? json_decode($payment->paymob_response, true)['email'] ?? 'admin@company.com' : 'admin@company.com',
            ]);
        }

        try {
            $pending = session('pending_subscription', []);
            $dbName = $tenant->database;

            $listener = new CreateTenantDatabaseListener();
            $listener->handle($tenant);

            config(['database.connections.tenant.database' => $dbName]);
            DB::purge('tenant');
            DB::reconnect('tenant');

            if (DB::connection('tenant')->getSchemaBuilder()->hasTable('users')) {
                $userData = [
                    'name'       => $pending['admin_name'] ?? 'Admin',
                    'email'      => $pending['admin_email'] ?? 'admin@company.com',
                    'password'   => Hash::make($pending['admin_password'] ?? 'password'),
                    'role'       => 'admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::connection('tenant')->table('users')->insert($userData);

                $userId = DB::connection('tenant')->table('users')->where('email', $userData['email'])->value('id');
                $roleId = DB::connection('tenant')->table('roles')->where('name', 'Admin')->where('guard_name', 'tenant')->value('id');
                if ($userId && $roleId) {
                    DB::connection('tenant')->table('model_has_roles')->insert([
                        'role_id'    => $roleId,
                        'model_type' => 'App\Models\User',
                        'model_id'   => $userId,
                    ]);
                }
            }

            $tenant->update(['status' => 'active']);
            session()->forget('pending_subscription');

            return redirect()->route('landlord.payment.success-display')->with([
                'login_url' => "http://{$tenant->domain}:8000",
                'email'     => $userData['email'],
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant provisioning failed after payment', [
                'tenant_id' => $tenant->id,
                'error'     => $e->getMessage(),
            ]);
            return view('landlord::portfolio.failed', [
                'message' => 'Payment succeeded but tenant provisioning failed. Please contact support.',
            ]);
        }
    }

    public function showSuccess()
    {
        return view('landlord::portfolio.success');
    }

    public function failed(Request $request)
    {
        return view('landlord::portfolio.failed', [
            'message' => $request->query('message', 'An error occurred during payment.'),
        ]);
    }
}
