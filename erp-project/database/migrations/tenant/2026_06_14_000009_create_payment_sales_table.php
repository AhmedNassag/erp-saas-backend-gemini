<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Ref', 192);
            $table->date('date');
            $table->string('Reglement', 192);
            $table->decimal('montant', 10, 2)->default(0);
            $table->decimal('change', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_sales');
    }
};
