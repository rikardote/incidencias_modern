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
        \DB::table('codigos_de_incidencias')->updateOrInsert(
            ['code' => '93'],
            [
                'description' => 'TOLERANCIA DE ESTANCIA',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    public function down(): void
    {
        //
    }
};
