<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * جلب البيانات الأساسية للوحة تحكم العميل الحالي (SaaS ERP Dashboard)
     */
    public function index(Request $request): JsonResponse
    {
        // 1. جلب بيانات المستخدم الحالي المعزول أوتوماتيكياً بواسطة الـ Token
        $user = $request->user();

        // 2. فحص اسم الداتا بيز الحالية اللي العميل متصل بيها حالياً كنوع من التأكيد (مثال للمطور)
        $currentDatabase = DB::connection('tenant')->getDatabaseName();

        /*
         * 💡 هنا مستقبلاً هتبدأ تعمل Queries سريعة لعرض إحصائيات لوحة التحكم مثل:
         * $totalProducts = DB::connection('tenant')->table('products')->count();
         * $recentInvoices = DB::connection('tenant')->table('invoices')->latest()->take(5)->get();
         */

        // 3. الرد بالـ JSON النظيف للـ Vue 3 لبناء الـ Widgets
        return response()->json([
            'status'  => 'success',
            'message' => 'Welcome to your ERP Dashboard',
            'data'    => [
                'current_connection' => 'tenant',
                'connected_database' => $currentDatabase, // هتقرأ erp_tenant_subdomain
                'company'            => \Spatie\Multitenancy\Models\Tenant::current()->name, // اسم الشركة من الـ Landlord
                'user'               => [
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
                'stats'              => [
                    'active_users_count' => 1,
                    'package_status'     => 'active'
                ]
            ]
        ], 200);
    }
}