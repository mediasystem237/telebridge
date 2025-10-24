<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')->constrained('telegram_bots')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->string('type')->default('text'); // e.g., text, photo, audio
            $table->text('content');
            $table->text('response')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('telegram_id')->on('telegram_users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_messages');
    }
};
