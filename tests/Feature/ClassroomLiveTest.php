<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\GroupActivity;
use App\Models\Level;
use App\Models\StudyGroup;
use App\Models\StudyGroupStudent;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClassroomLiveTest extends TestCase
{
    public function test_all_roles_can_access_group_classroom_live_without_500_error(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $supervisor = User::where('role', 'supervisor')->first() ?? User::factory()->create(['role' => 'supervisor', 'is_active' => true]);
        $teacher = User::where('role', 'teacher')->first() ?? User::factory()->create(['role' => 'teacher', 'is_active' => true]);
        $student = User::where('role', 'student')->first() ?? User::factory()->create(['role' => 'student', 'is_active' => true]);
        $level = Level::first();
        $course = Course::first();

        $group = StudyGroup::create([
            'name' => 'مجموعة اختبارية للبث الحي المتكامل',
            'name_ar' => 'مجموعة اختبارية للبث الحي المتكامل',
            'course_id' => $course->id,
            'level_id' => $level->id,
            'room_id' => 'asots-grp-'.strtolower(Str::random(8)),
            'teacher_id' => $teacher->id,
            'supervisor_id' => $supervisor->id,
            'is_live' => true,
            'live_status' => 'live',
            'max_students' => 15,
            'status' => 'active',
        ]);

        // 1. فحص دخول الطالب
        $studentResponse = $this->actingAs($student)->get(route('classroom.show', $group->room_id));
        $this->assertEquals(200, $studentResponse->status());

        // 2. فحص دخول المدير (كان يسبب 500 سابقاً بسبب نقص متغير groupId)
        $adminResponse = $this->actingAs($admin)->get(route('classroom.show', $group->room_id));
        $this->assertEquals(200, $adminResponse->status());

        // 3. فحص دخول المعلم
        $teacherResponse = $this->actingAs($teacher)->get(route('classroom.show', $group->room_id));
        $this->assertEquals(200, $teacherResponse->status());

        // 4. فحص دخول المشرف
        $supervisorResponse = $this->actingAs($supervisor)->get(route('classroom.show', $group->room_id));
        $this->assertEquals(200, $supervisorResponse->status());

        // تنظيف
        $group->delete();
    }

    public function test_group_cannot_be_deleted_if_has_students_or_activities(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $student = User::where('role', 'student')->first() ?? User::factory()->create(['role' => 'student', 'is_active' => true]);
        $level = Level::first();
        $course = Course::first();

        $group = StudyGroup::create([
            'name' => 'مجموعة محمية من الحذف',
            'name_ar' => 'مجموعة محمية من الحذف',
            'course_id' => $course->id,
            'level_id' => $level->id,
            'room_id' => 'asots-grp-'.strtolower(Str::random(8)),
            'max_students' => 10,
            'status' => 'active',
        ]);

        // المجموعة فارغة في البداية -> canBeDeleted = true
        $this->assertTrue($group->canBeDeleted());

        // إضافة طالب للمجموعة
        StudyGroupStudent::create([
            'study_group_id' => $group->id,
            'student_id' => $student->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        // بعد إضافة الطالب -> canBeDeleted = false
        $this->assertFalse($group->fresh()->canBeDeleted());

        // محاولة الحذف من الإدارة -> تفشل مع رسالة خطأ
        $response = $this->actingAs($admin)->delete(route('admin.groups.destroy', $group->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('study_groups', ['id' => $group->id]);

        // إزالة الطالب وإضافة نشاط/محادثة
        StudyGroupStudent::where('study_group_id', $group->id)->delete();
        GroupActivity::create([
            'study_group_id' => $group->id,
            'user_id' => $admin->id,
            'title' => 'محادثة ونشاط اختباري بالمجموعة',
            'type' => 'announcement',
        ]);

        // بعد إضافة النشاط/المحادثة -> canBeDeleted = false
        $this->assertFalse($group->fresh()->canBeDeleted());

        // محاولة الحذف تفشل
        $response2 = $this->actingAs($admin)->delete(route('admin.groups.destroy', $group->id));
        $response2->assertRedirect();
        $response2->assertSessionHas('error');
        $this->assertDatabaseHas('study_groups', ['id' => $group->id]);

        // تنظيف
        GroupActivity::where('study_group_id', $group->id)->delete();
        $group->delete();
    }
}
