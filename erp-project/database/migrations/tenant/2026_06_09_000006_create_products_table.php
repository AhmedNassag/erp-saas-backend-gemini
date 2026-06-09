<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 192);
            $table->string('Type_barcode', 192);
            $table->string('name', 192);
            $table->boolean('status')->default(1);
            $table->float('cost', 10, 0);
            $table->float('price', 10, 0);
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'products_category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id', 'products_brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->foreign('unit_id', 'products_unit_id')->references('id')->on('units')->onDelete('set null');
            $table->unsignedBigInteger('unit_sale_id')->nullable();
            $table->foreign('unit_sale_id', 'products_unit_sale_id')->references('id')->on('units')->onDelete('set null');
            $table->unsignedBigInteger('unit_purchase_id')->nullable();
            $table->foreign('unit_purchase_id', 'products_unit_purchase_id')->references('id')->on('units')->onDelete('set null');
            $table->float('TaxNet', 10, 0)->nullable()->default(0);
            $table->string('tax_method', 192)->nullable()->default('1');
            $table->text('note')->nullable();
            $table->float('stock_alert', 10, 0)->nullable()->default(0);
            $table->boolean('is_variant')->default(0);
            $table->boolean('is_active')->nullable()->default(1);
            $table->text('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
