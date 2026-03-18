<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('system_notifications')) {
            Schema::create('system_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('sender_id');
                $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();
                
                // null = para todos; si tiene valor, es solo para ese usuario
                $table->unsignedInteger('target_user_id')->nullable();
                $table->foreign('target_user_id')->references('id')->on('users')->cascadeOnDelete();
                
                $table->string('title');
                $table->text('body')->nullable();
                // 'info' | 'warning' | 'success' | 'danger'
                $table->string('type', 20)->default('info');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('system_notification_reads')) {
            Schema::create('system_notification_reads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('notification_id')->constrained('system_notifications')->cascadeOnDelete();
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->timestamp('read_at');
                $table->unique(['notification_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notification_reads');
        Schema::dropIfExists('system_notifications');
    }
};
