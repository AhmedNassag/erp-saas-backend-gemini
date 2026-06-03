<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');

        if (!Schema::hasColumn($tableNames['roles'], 'deleted_at')) {
            Schema::table($tableNames['roles'], function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (Schema::hasColumn($tableNames['roles'], 'deleted_at')) {
            Schema::table($tableNames['roles'], function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
