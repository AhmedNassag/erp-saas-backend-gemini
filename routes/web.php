<?php

use Illuminate\Support\Facades\Route;
use App\Models\User; // استدعاء الموديل اللي لسه مأمنينه

Route::middleware(['tenant'])->group(function () {
    
    Route::get('/', function () {
        
        // 1. هنعمل موظف تجريبي لشركة ألفا لو الجدول فاضي
        if (User::count() === 0) {
            User::create([
                'name' => 'أحمد المدير رائد الأعمال',
                'email' => 'ahmed@alpha.com',
                'password' => bcrypt('123456'),
                'role' => 'admin'
            ]);
        }

        // 2. هنجيب كل المستخدمين من قاعدة بيانات العميل الحالية
        $users = User::all();
        
        return response()->json([
            'message' => 'Welcome to your SaaS ERP!',
            'tenant_name' => \Spatie\Multitenancy\Models\Tenant::current()->name,
            'current_connected_database' => DB::connection()->getDatabaseName(),
            'users_inside_this_tenant' => $users
        ]);
    });

});