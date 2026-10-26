<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('group_activities')) {
            Schema::table('group_activities', function (Blueprint $table) {
                $table->string('type', 50)->default('worksheet')->change();
            });
        }
    }

    public function down(): void
    {
        // No need to revert to strict enum to prevent data loss
    }
};
