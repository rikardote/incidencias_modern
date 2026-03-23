<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sistemas.employees', function (Blueprint $table) {
            $table->string('password')->nullable()->after('rfc');
            $table->rememberToken()->after('password');
            $table->string('fcm_token')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sistemas.employees', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token', 'fcm_token']);
        });
    }
};
