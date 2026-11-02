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
        if (Schema::hasTable('classroom_room_controls')) {
            Schema::table('classroom_room_controls', function (Blueprint $table) {
                if (! Schema::hasColumn('classroom_room_controls', 'whiteboard_state')) {
                    $table->longText('whiteboard_state')->nullable()->after('kicked_user_ids');
                }
                if (! Schema::hasColumn('classroom_room_controls', 'whiteboard_version')) {
                    $table->unsignedBigInteger('whiteboard_version')->default(0)->after('whiteboard_state');
                }
                if (! Schema::hasColumn('classroom_room_controls', 'laser_state')) {
                    $table->json('laser_state')->nullable()->after('whiteboard_version');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('classroom_room_controls')) {
            Schema::table('classroom_room_controls', function (Blueprint $table) {
                if (Schema::hasColumn('classroom_room_controls', 'laser_state')) {
                    $table->dropColumn('laser_state');
                }
                if (Schema::hasColumn('classroom_room_controls', 'whiteboard_version')) {
                    $table->dropColumn('whiteboard_version');
                }
                if (Schema::hasColumn('classroom_room_controls', 'whiteboard_state')) {
                    $table->dropColumn('whiteboard_state');
                }
            });
        }
    }
};
