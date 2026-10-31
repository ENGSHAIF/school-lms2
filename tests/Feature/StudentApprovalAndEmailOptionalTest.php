<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Level;
use App\Models\StudentEnrollment;
use App\Models\StudentInstallment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentApprovalAndEmailOptionalTest extends TestCase
{
    protected function getAdminUser(): User
    {
        return User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    protected function getSupervisorUser(): User
    {
        return User::where('role', 'supervisor')->first() ?? User::factory()->create([
            'role' => 'supervisor',
            'is_active' => true,
        ]);
    }

    public function test_user_can_register_without_email(): void
    {
        $uniquePhone = '96777'.rand(1000000, 9999999);

        $response = $this->post(route('register.post'), [
            'name' => 'طالب تجريبي بدون بريد',
            'email' => '',
            'gender' => 'male',
            'phone_whatsapp' => $uniquePhone,
            'password' => 'Secret123456!',
            'password_confirmation' => 'Secret123456!',
            'role' => 'student',
        ]);

        $response->assertRedirect();

        $user = User::where('phone_whatsapp', $uniquePhone)->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email);

        // تنظيف
        $user->delete();
    }

    public function test_admin_can_create_teacher_without_email(): void
    {
        $admin = $this->getAdminUser();
        $uniquePhone = '96771'.rand(1000000, 9999999);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'معلم جديد اختباري',
            'email' => '',
            'gender' => 'male',
            'phone_whatsapp' => $uniquePhone,
            'role' => 'teacher',
            'password' => 'Secret123456!',
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $teacher = User::where('phone_whatsapp', $uniquePhone)->first();
        $this->assertNotNull($teacher);
        $this->assertNull($teacher->email);
        $this->assertEquals('teacher', $teacher->role);

        // تنظيف
        $teacher->delete();
    }

    public function test_user_without_email_can_login_with_phone_number(): void
    {
        $uniquePhone = '96773'.rand(1000000, 9999999);
        $user = User::create([
            'name' => 'مستخدم مسجل بالهاتف فقط',
            'email' => null,
            'gender' => 'male',
            'phone_whatsapp' => $uniquePhone,
            'password' => Hash::make('Secret123456!'),
            'role' => 'student',
            'is_active' => true,
        ]);

        $response = $this->post(route('login.post'), [
            'login' => $uniquePhone,
            'password' => 'Secret123456!',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);

        // تنظيف
        $user->delete();
    }

    public function test_admin_can_approve_student_with_both_level_and_course(): void
    {
        $admin = $this->getAdminUser();

        $level = Level::first();
        $this->assertNotNull($level, 'يجب وجود مستوى على الأقل');

        $course = Course::where('level_id', $level->id)->first() ?? Course::create([
            'level_id' => $level->id,
            'name_ar' => 'دورة اختبارية للاعتماد',
            'name_en' => 'Approval Test Course',
            'fee_amount' => 150.00,
        ]);

        $student = User::create([
            'name' => 'طالب قيد الاعتماد للإدارة',
            'email' => null,
            'gender' => 'male',
            'phone_whatsapp' => '96770'.rand(1000000, 9999999),
            'password' => Hash::make('Secret123456!'),
            'role' => 'student',
            'status' => 'pending_approval',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.assign_level', $student->id), [
            'level_id' => $level->id,
            'course_id' => $course->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $enrollment = StudentEnrollment::where('user_id', $student->id)
            ->where('status', 'active')
            ->first();

        $this->assertNotNull($enrollment);
        $this->assertEquals($level->id, $enrollment->level_id);
        $this->assertEquals($course->id, $enrollment->course_id);

        $installment = StudentInstallment::where('user_id', $student->id)->first();
        $this->assertNotNull($installment);
        $this->assertEquals($course->id, $installment->course_id);

        // تنظيف
        StudentInstallment::where('user_id', $student->id)->delete();
        StudentEnrollment::where('user_id', $student->id)->delete();
        $student->delete();
    }

    public function test_supervisor_can_approve_student_with_both_level_and_course(): void
    {
        $supervisor = $this->getSupervisorUser();

        $level = Level::first();
        $course = Course::where('level_id', $level->id)->first() ?? Course::first();

        $student = User::create([
            'name' => 'طالب قيد الاعتماد للمشرف',
            'email' => null,
            'gender' => 'male',
            'phone_whatsapp' => '96778'.rand(1000000, 9999999),
            'password' => Hash::make('Secret123456!'),
            'role' => 'student',
            'status' => 'pending_approval',
            'is_active' => false,
        ]);

        $response = $this->actingAs($supervisor)->post(route('supervisor.assign_level', $student->id), [
            'level_id' => $level->id,
            'course_id' => $course?->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $enrollment = StudentEnrollment::where('user_id', $student->id)
            ->where('status', 'active')
            ->first();

        $this->assertNotNull($enrollment);
        $this->assertEquals($level->id, $enrollment->level_id);
        if ($course) {
            $this->assertEquals($course->id, $enrollment->course_id);
        }

        // تنظيف
        StudentInstallment::where('user_id', $student->id)->delete();
        StudentEnrollment::where('user_id', $student->id)->delete();
        $student->delete();
    }
}
