<?php

namespace Modules\Landlord\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Modules\Landlord\Listeners\CreateTenantDatabaseListener;

class PortfolioController extends Controller
{
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
        // جلب الباقات المتاحة من داتا بيز اللاندلورد
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
        Log::info('Package ID: ' . $package_id);
        Log::info('Request Data: ', $request->all());

        $request->validate([
            'company_name' => 'required|string|max:255',
            'subdomain' => 'required|alpha|min:3|max:20',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:6',
        ]);

        // التأكد إن الساب دومين مش محجوز
        $domainName = $request->subdomain . '.erp.test';
        Log::info('التحقق من النطاق: ' . $domainName);
        
        $exists = DB::connection('landlord')->table('tenants')->where('domain', $domainName)->exists();
        if ($exists) {
            Log::warning('النطاق محجوز بالفعل: ' . $domainName);
            return back()->withErrors(['subdomain' => 'هذا النطاق محجوز بالفعل!']);
        }
        Log::info('النطاق متاح ✅');

        // 1. تسجيل كارت الـ Tenant في داتا بيز الـ Landlord
        $dbName = 'erp_tenant_' . $request->subdomain;
        Log::info('إنشاء Tenant جديد:', [
            'company_name' => $request->company_name,
            'domain' => $domainName,
            'database' => $dbName,
            'package_id' => $package_id
        ]);

        $tenantId = DB::connection('landlord')->table('tenants')->insertGetId([
            'name' => $request->company_name,
            'domain' => $domainName,
            'database' => $dbName,
            'package_id' => $package_id,
            'subscription_ends_at' => now()->addYear(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Tenant تم إنشاؤه بنجاح - ID: ' . $tenantId);

        // جلب الـ Tenant كـ Object
        $tenant = DB::connection('landlord')->table('tenants')->where('id', $tenantId)->first();
        Log::info('بيانات Tenant:', (array)$tenant);

        // 2. تشغيل الـ Automation بالكامل عبر الـ Listener
        Log::info('بدء تشغيل CreateTenantDatabaseListener...');
        try {
            $listener = new CreateTenantDatabaseListener();
            $listener->handle($tenant);
            Log::info('CreateTenantDatabaseListener تم التنفيذ بنجاح ✅');
        } catch (\Exception $e) {
            Log::error('خطأ في CreateTenantDatabaseListener: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }

        // 3. تأمين الاتصال على داتا بيز العميل الجديد
        Log::info('تأمين الاتصال بقاعدة بيانات العميل: ' . $dbName);
        config(['database.connections.tenant.database' => $dbName]);
        
        try {
            DB::connection('tenant')->reconnect();
            Log::info('تم إعادة الاتصال بقاعدة البيانات بنجاح ✅');
            
            // التحقق من وجود الاتصال
            $connectionName = DB::connection('tenant')->getDatabaseName();
            Log::info('متصل بقاعدة البيانات: ' . $connectionName);
            
        } catch (\Exception $e) {
            Log::error('فشل الاتصال بقاعدة البيانات: ' . $e->getMessage());
            throw $e;
        }

        // 4. إنشاء حساب الـ Admin
        Log::info('بدء إنشاء حساب Admin:', [
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'role' => 'admin'
        ]);

        try {
            // التحقق من وجود جدول users
            if (!DB::connection('tenant')->getSchemaBuilder()->hasTable('users')) {
                Log::error('جدول users غير موجود في قاعدة بيانات العميل!');
                throw new \Exception('جدول users غير موجود');
            }
            Log::info('جدول users موجود ✅');

            // التحقق من وجود حقل role في الجدول
            $columns = DB::connection('tenant')->getSchemaBuilder()->getColumnListing('users');
            Log::info('الأعمدة الموجودة في جدول users:', $columns);
            
            if (!in_array('role', $columns)) {
                Log::warning('⚠️ حقل role غير موجود في جدول users! سيتم إدخال بدون role');
            }

            $userId = DB::connection('tenant')->table('users')->insertGetId([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'admin', // لو الحقل موجود
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Admin تم إنشاؤه بنجاح - User ID: ' . $userId);
            
            // التحقق من أن المستخدم تم حفظه فعلاً
            $savedUser = DB::connection('tenant')->table('users')->find($userId);
            Log::info('بيانات المستخدم المحفوظة:', (array)$savedUser);
            
        } catch (\Exception $e) {
            Log::error('❌ فشل إنشاء حساب Admin: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }

        // 5. تخزين اللينك في السيشن
        $loginUrl = "http://" . $request->subdomain . ".erp.test:8000";
        Log::info('تم الانتهاء بنجاح! رابط تسجيل الدخول: ' . $loginUrl);
        Log::info('======= نهاية عملية Checkout بنجاح =======');

        return redirect()->route('landlord.payment.success')->with([
            'login_url' => $loginUrl,
            'email' => $request->admin_email
        ]);
    }



    public function paymentSuccess()
    {
        return view('landlord::portfolio.success');
    }
}