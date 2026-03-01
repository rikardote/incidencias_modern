<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("capture_exceptions", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->dateTime("expires_at");
            $table->string("reason")->nullable();
            $table->unsignedBigInteger("qna_id")->nullable();
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users")->cascadeOnDelete();
            $table->foreign("qna_id")->references("id")->on("qnas")->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("capture_exceptions");
    }
};
