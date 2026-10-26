<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إضافة حقول التحكم بالبث الحي للمجموعات الدراسية
        Schema::table('study_groups', function (Blueprint $table) {
            $table->boolean('is_live')->default(false)->after('room_id');
            $table->string('live_status')->default('closed')->after('is_live'); // closed, live, pending_approval
            $table->foreignId('live_requested_by')->nullable()->after('live_status')->constrained('users')->nullOnDelete();
            $table->timestamp('live_requested_at')->nullable()->after('live_requested_by');
            $table->foreignId('live_approved_by')->nullable()->after('live_requested_at')->constrained('users')->nullOnDelete();
            $table->timestamp('live_started_at')->nullable()->after('live_approved_by');
        });

        // 2. إضافة حقول التحكم بالبث الحي للحجوزات الفردية إن لم تكن موجودة
        if (Schema::hasTable('bookings') && !Schema::hasColumn('bookings', 'is_live')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->boolean('is_live')->default(false)->after('room_id');
                $table->timestamp('live_started_at')->nullable()->after('is_live');
            });
        }
    }

    public function down(): void
    {
        Schema::table('study_groups', function (Blueprint $table) {
            $table->dropForeign(['live_requested_by']);
            $table->dropForeign(['live_approved_by']);
            $table->dropColumn([
                'is_live',
                'live_status',
                'live_requested_by',
                'live_requested_at',
                'live_approved_by',
                'live_started_at',
            ]);
        });

        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'is_live')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn(['is_live', 'live_started_at']);
            });
        }
    }
};
