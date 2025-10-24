<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telegram_messages', function (Blueprint $table) {
            $table->json('ai_metadata')->nullable()->after('response');
            $table->json('intent_data')->nullable()->after('ai_metadata');
        });
    }

    public function down(): void
    {
        Schema::table('telegram_messages', function (Blueprint $table) {
            $table->dropColumn(['ai_metadata', 'intent_data']);
        });
    }
};

