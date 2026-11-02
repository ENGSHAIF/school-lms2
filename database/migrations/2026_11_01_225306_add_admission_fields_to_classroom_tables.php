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
        if (Schema::hasTable('classroom_participants')) {
            Schema::table('classroom_participants', function (Blueprint $table) {
                if (! Schema::hasColumn('classroom_participants', 'admission_status')) {
                    $table->string('admission_status', 20)->default('admitted')->after('is_muted_by_host');
                }
            });
        }

        if (Schema::hasTable('classroom_room_controls')) {
            Schema::table('classroom_room_controls', function (Blueprint $table) {
                if (! Schema::hasColumn('classroom_room_controls', 'requires_admission')) {
                    $table->boolean('requires_admission')->default(true)->after('is_live');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('classroom_participants')) {
            Schema::table('classroom_participants', function (Blueprint $table) {
                if (Schema::hasColumn('classroom_participants', 'admission_status')) {
                    $table->dropColumn('admission_status');
                }
            });
        }

        if (Schema::hasTable('classroom_room_controls')) {
            Schema::table('classroom_room_controls', function (Blueprint $table) {
                if (Schema::hasColumn('classroom_room_controls', 'requires_admission')) {
                    $table->dropColumn('requires_admission');
                }
            });
        }
    }
};
