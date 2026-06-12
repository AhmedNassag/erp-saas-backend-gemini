<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adjustment_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('type', 192);
            $table->unsignedBigInteger('adjustment_id');
            $table->foreign('adjustment_id')->references('id')->on('adjustments')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjustment_details');
    }
};
