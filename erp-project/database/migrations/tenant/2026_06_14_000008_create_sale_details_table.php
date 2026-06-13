<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedBigInteger('sale_unit_id')->nullable();
            $table->foreign('sale_unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->decimal('TaxNet', 10, 2)->default(0);
            $table->string('tax_method', 192)->default('1');
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('discount_method', 192)->default('1');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
