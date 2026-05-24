<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // بنحدد هنا إن الجدول ده يترمي على قاعدة الـ landlord
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::create('packages', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name'); // اسم الباقة (مثلاً: برونزية، فضية، ذهبية)
            $blueprint->string('slug')->unique(); // اسم فريد للروابط والـ API
            $blueprint->decimal('price', 10, 2); // سعر الباقة
            $blueprint->integer('limit_users')->default(-1); // حد الموظفين (-1 تعني غير محدود)
            $blueprint->integer('limit_tenants')->default(1); // حد الفروع/الشركات المتاحة للمشترك
            $blueprint->json('features')->nullable(); // ميزات إضافية ملمومة كـ JSON
            $blueprint->boolean('is_active')->default(true); // حالة الباقة
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};