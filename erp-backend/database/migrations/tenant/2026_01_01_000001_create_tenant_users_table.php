<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الـ Migration.
     * لاحظ: مش بنحدد هنا اتصال محدد لأن الـ Listener هيشغله ديناميكياً
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الموظف/المستخدم في الشركة
            $table->string('email')->unique(); // إيميله للدخول
            $table->string('password'); // الباسورد
            $table->enum('role', ['admin', 'manager', 'employee'])->default('employee'); // دور الموظف
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * التراجع عن الـ Migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};