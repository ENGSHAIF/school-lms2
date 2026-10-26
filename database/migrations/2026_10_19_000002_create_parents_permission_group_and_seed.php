<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        $defaultParentPermissions = [
            'parent.dashboard',
            'parent.profile_view',
            'parent.attendance_view',
            'parent.evaluations_view',
            'parent.activities_view',
            'parent.exams_view',
            'parent.finance_view',
            'parent.transcript_view',
            'parent.live_schedule',
        ];

        // 1. إنشاء أو تحديث مجموعة الصلاحيات القياسية لأولياء الأمور
        $existingGroup = DB::table('permission_groups')->where('slug', 'parents')->first();

        if (!$existingGroup) {
            $parentGroupId = DB::table('permission_groups')->insertGetId([
                'name' => 'أولياء الأمور ومتابعة الطلاب',
                'name_en' => 'Parents Portal & Follow-up',
                'slug' => 'parents',
                'description' => 'مجموعة صلاحيات أولياء الأمور: تتيح لولي الأمر متابعة الأبناء في كافة المستويات (الرسوم، الحضور، التقييمات والنجوم، الواجبات، نتائج الاختبارات، والسجل الشامل 360).',
                'color' => 'amber',
                'icon' => '👨‍👧‍👦',
                'is_system' => false,
                'permissions' => json_encode($defaultParentPermissions),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $parentGroupId = $existingGroup->id;
            DB::table('permission_groups')->where('id', $parentGroupId)->update([
                'permissions' => json_encode($defaultParentPermissions),
                'updated_at' => $now,
            ]);
        }

        // 2. ربط جميع أولياء الأمور الحاليين بمجموعة صلاحيات أولياء الأمور تلقائياً
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'permission_group_id')) {
            DB::table('users')
                ->where('role', 'parent')
                ->whereNull('permission_group_id')
                ->update(['permission_group_id' => $parentGroupId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permission_groups')->where('slug', 'parents')->delete();
    }
};
