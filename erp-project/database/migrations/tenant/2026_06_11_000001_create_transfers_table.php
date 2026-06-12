<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('Ref', 192);
            $table->unsignedBigInteger('from_warehouse_id');
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->unsignedBigInteger('to_warehouse_id');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->integer('items')->nullable()->default(0);
            $table->decimal('tax_rate', 10, 2)->nullable()->default(0);
            $table->decimal('TaxNet', 10, 2)->nullable()->default(0);
            $table->decimal('discount', 10, 2)->nullable()->default(0);
            $table->decimal('shipping', 10, 2)->nullable()->default(0);
            $table->decimal('GrandTotal', 10, 2)->nullable()->default(0);
            $table->string('status', 192)->nullable()->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
