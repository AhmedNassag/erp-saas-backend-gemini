<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'landlord';

    public function up(): void
    {
        Schema::connection('landlord')->create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('key');
            $table->string('language_code', 10);
            $table->text('value');
            $table->timestamps();

            $table->unique(['group', 'key', 'language_code']);
            $table->foreign('language_code')->references('code')->on('languages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('translations');
    }
};
