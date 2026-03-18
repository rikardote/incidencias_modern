<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            // null = para todos; si tiene valor, es solo para ese usuario
            $table->foreignId('target_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            // 'info' | 'warning' | 'success' | 'danger'
            $table->string('type', 20)->default('info');
            $table->timestamps();
        });

        Schema::create('system_notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('system_notifications')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->unique(['notification_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notification_reads');
        Schema::dropIfExists('system_notifications');
    }
};
