<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('companyName', 192);
            $table->string('companyPhone', 192);
            $table->string('companyAdress', 192);
            $table->string('developed_by', 192)->default('Ahmed Nassag');
            $table->string('footer', 192)->default('Ahmed Nassag - Ultimate Inventory With POS');
            $table->unsignedBigInteger('currency_id');
            $table->foreign('currency_id', 'settings_currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id', 'settings_client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id', 'settings_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
