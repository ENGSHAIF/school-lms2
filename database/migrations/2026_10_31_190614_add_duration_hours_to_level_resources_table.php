<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('level_resources', function (Blueprint $table) {
            if (! Schema::hasColumn('level_resources', 'duration_hours')) {
                $table->decimal('duration_hours', 6, 2)->nullable()->after('duration_minutes');
            }
        });

        // مزامنة البيانات القديمة إن وجدت
        DB::table('level_resources')
            ->whereNotNull('duration_minutes')
            ->whereNull('duration_hours')
            ->update([
                'duration_hours' => DB::raw('ROUND(duration_minutes / 60.0, 2)'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('level_resources', function (Blueprint $table) {
            if (Schema::hasColumn('level_resources', 'duration_hours')) {
                $table->dropColumn('duration_hours');
            }
        });
    }
};
