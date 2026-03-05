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
        \Illuminate\Support\Facades\Schema::connection('biometrico')->create('equipos', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->string('ip');
            $table->timestamps();
        });

        // Seed initial data
        \Illuminate\Support\Facades\DB::connection('biometrico')->table('equipos')->insert([
            ['location' => 'Delegación Principal', 'ip' => '192.160.141.37', 'created_at' => now(), 'updated_at' => now()],
            ['location' => 'Almacén', 'ip' => '192.160.169.230', 'created_at' => now(), 'updated_at' => now()],
            ['location' => 'San Felipe', 'ip' => '192.165.240.253', 'created_at' => now(), 'updated_at' => now()],
            ['location' => 'Los Algodones', 'ip' => '192.165.232.253', 'created_at' => now(), 'updated_at' => now()],
            ['location' => 'Tecate', 'ip' => '192.165.171.253', 'created_at' => now(), 'updated_at' => now()],
            ['location' => 'EBDI 60', 'ip' => '192.161.192.253', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\Schema::connection('biometrico')->dropIfExists('equipos');
    }
};
