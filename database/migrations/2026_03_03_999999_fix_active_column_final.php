<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('employees', 'active')) {
            Schema::table('employees', function (Blueprint $table) {
                // MySQL 8+ handles tinyint(1) naturally, but to avoid warnings 
                // about display width we can use boolean() which is alias.
                // However, raw SQL was already run, let's make it standard.
                $table->boolean('active')->default(1)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('employees', 'active')) {
            Schema::table('employees', function (Blueprint $table) {
                // If needed to go back to enum (though not recommended)
                // $table->enum('active', ['0', '1'])->default('1')->change();
            });
        }
    }
};
