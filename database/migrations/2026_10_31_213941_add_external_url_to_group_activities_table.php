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
        Schema::table('group_activities', function (Blueprint $table) {
            if (! Schema::hasColumn('group_activities', 'external_url')) {
                $table->string('external_url', 500)->nullable()->after('file_size');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_activities', function (Blueprint $table) {
            if (Schema::hasColumn('group_activities', 'external_url')) {
                $table->dropColumn('external_url');
            }
        });
    }
};
