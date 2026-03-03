<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = app()->environment('testing') ? config('database.default') : 'mysql_chats';
        Schema::connection($connection)->create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id')->index(); // No FK because we are on a different schema
            $table->unsignedBigInteger('sender_id')->index();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = app()->environment('testing') ? config('database.default') : 'mysql_chats';
        Schema::connection($connection)->dropIfExists('messages');
    }
};