<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'whatsapp_notifications_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('whatsapp_notifications_enabled')->default(true)->after('phone_whatsapp');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'whatsapp_notifications_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('whatsapp_notifications_enabled');
            });
        }
    }
};
