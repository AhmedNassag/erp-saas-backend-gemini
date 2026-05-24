<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // تحديد الاتصال المركزي
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name'); // اسم شركة العميل (مثلاً: شركة النور للتجارة)
            $blueprint->string('domain')->unique(); // الدومين أو الساب دومين (مثلاً: alnoor.saas.com)
            $blueprint->string('database')->unique(); // اسم قاعدة البيانات الخاصة بيه هو بس (مثلاً: erp_tenant_alnoor)
            // ربط العميل بالباقة بتاعته
            $blueprint->foreignId('package_id')->constrained('packages')->onDelete('restrict');
            // تفاصيل الاشتراك
            $blueprint->timestamp('subscription_ends_at')->nullable(); // تاريخ انتهاء الاشتراك
            $blueprint->enum('status', ['active', 'suspended', 'expired'])->default('active'); // حالة الحساب
            
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};