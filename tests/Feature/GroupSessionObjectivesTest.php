<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\GroupSession;
use App\Models\Level;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GroupSessionObjectivesTest extends TestCase
{
    protected function getAdmin(): User
    {
        return User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_group_session_create_page_shows_objectives_fields(): void
    {
        $admin = $this->getAdmin();

        $group = StudyGroup::first() ?? StudyGroup::create([
            'name' => 'مجموعة تجريبية',
            'course_id' => Course::first()?->id,
            'level_id' => Level::first()?->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.groups.evaluations.create', $group->id));

        $response->assertOk();
        $response->assertSee('أهداف الحصة');
        $response->assertSee('أهداف المنهج');
        $response->assertSee('lesson_objectives');
        $response->assertSee('curriculum_objectives');
    }

    public function test_group_session_saves_lesson_and_curriculum_objectives(): void
    {
        $admin = $this->getAdmin();

        $student = User::create([
            'name' => 'طالب تقييم حصة',
            'email' => null,
            'gender' => 'male',
            'phone_whatsapp' => '96773'.rand(1000000, 9999999),
            'password' => Hash::make('Secret123!'),
            'role' => 'student',
            'status' => 'active',
            'is_active' => true,
        ]);

        $group = StudyGroup::create([
            'name' => 'مجموعة تقييم أهداف '.rand(100, 999),
            'course_id' => Course::first()?->id,
            'level_id' => Level::first()?->id,
            'status' => 'active',
        ]);

        $group->students()->attach($student->id, ['status' => 'active']);

        $lessonObj = 'إتقان زمن الماضي البسيط والمفردات المتعلقة بالرحلات';
        $curriculumObj = 'تحقيق الكفاءة في التعبير عن التجارب السابقة وفق الإطار الأوروبي A2';

        $response = $this->actingAs($admin)->post(route('admin.groups.evaluations.store', $group->id), [
            'session_date' => now()->toDateString(),
            'session_number' => 1,
            'lesson_topic' => 'Past Simple & Travel Experiences',
            'lesson_objectives' => $lessonObj,
            'curriculum_objectives' => $curriculumObj,
            'lesson_summary' => 'تم شرح القواعد وتطبيق محادثات ثنائية',
            'homework_assigned' => 'حل تمارين صفحة 20',
            'evaluations' => [
                $student->id => [
                    'attendance_status' => 'present',
                    'participation_score' => 5,
                    'understanding_score' => 5,
                    'homework_score' => 5,
                    'stars_awarded' => 3,
                ],
            ],
        ]);

        $response->assertRedirect();

        $session = GroupSession::where('study_group_id', $group->id)->first();
        $this->assertNotNull($session);
        $this->assertEquals($lessonObj, $session->lesson_objectives);
        $this->assertEquals($curriculumObj, $session->curriculum_objectives);

        // فحص العرض والطباعة
        $showResponse = $this->actingAs($admin)->get(route('admin.groups.evaluations.show', $session->id));
        $showResponse->assertOk();
        $showResponse->assertSee($lessonObj);
        $showResponse->assertSee($curriculumObj);

        $printResponse = $this->actingAs($admin)->get(route('admin.groups.evaluations.print', ['session_id' => $session->id]));
        $printResponse->assertOk();
        $printResponse->assertSee($lessonObj);
        $printResponse->assertSee($curriculumObj);

        // تنظيف
        $session->evaluations()->delete();
        $session->delete();
        $group->students()->detach();
        $group->delete();
        $student->delete();
    }

    public function test_group_session_uploads_and_downloads_summary_and_homework_files(): void
    {
        $admin = $this->getAdmin();

        $student = User::create([
            'name' => 'طالب ملفات حصة',
            'email' => null,
            'gender' => 'male',
            'phone_whatsapp' => '96773'.rand(1000000, 9999999),
            'password' => Hash::make('Secret123!'),
            'role' => 'student',
            'status' => 'active',
            'is_active' => true,
        ]);

        $group = StudyGroup::create([
            'name' => 'مجموعة ملفات '.rand(100, 999),
            'course_id' => Course::first()?->id,
            'level_id' => Level::first()?->id,
            'status' => 'active',
        ]);

        $group->students()->attach($student->id, ['status' => 'active']);

        $summaryFile = UploadedFile::fake()->create('lesson_summary_guide.pdf', 150, 'application/pdf');
        $homeworkFile = UploadedFile::fake()->create('homework_sheet.docx', 120, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($admin)->post(route('admin.groups.evaluations.store', $group->id), [
            'session_date' => now()->toDateString(),
            'session_number' => 2,
            'lesson_topic' => 'Grammar Unit 4 with Worksheet',
            'lesson_summary' => 'ملخص نصي مرفق مع ملف PDF',
            'lesson_summary_file' => $summaryFile,
            'homework_assigned' => 'حل الورقة المرفقة',
            'homework_file' => $homeworkFile,
            'evaluations' => [
                $student->id => [
                    'attendance_status' => 'present',
                    'participation_score' => 5,
                    'understanding_score' => 5,
                    'homework_score' => 5,
                    'stars_awarded' => 2,
                ],
            ],
        ]);

        $response->assertRedirect();

        $session = GroupSession::where('study_group_id', $group->id)->where('session_number', 2)->first();
        $this->assertNotNull($session);
        $this->assertTrue($session->hasSummaryFile());
        $this->assertTrue($session->hasHomeworkFile());
        $this->assertEquals('lesson_summary_guide.pdf', $session->lesson_summary_file_name);
        $this->assertEquals('homework_sheet.docx', $session->homework_file_name);

        // فحص تحميل الملف من قبل الإداري
        $downloadSummary = $this->actingAs($admin)->get(route('groups.sessions.download', ['sessionId' => $session->id, 'type' => 'summary']));
        $downloadSummary->assertOk();

        $downloadHw = $this->actingAs($admin)->get(route('groups.sessions.download', ['sessionId' => $session->id, 'type' => 'homework']));
        $downloadHw->assertOk();

        // فحص تحميل الملف من قبل الطالب المسكن في المجموعة
        $studentDownload = $this->actingAs($student)->get(route('groups.sessions.download', ['sessionId' => $session->id, 'type' => 'summary']));
        $studentDownload->assertOk();

        // فحص رفض التحميل لمستخدم غريب ليس في المجموعة
        $stranger = User::create([
            'name' => 'طالب غريب',
            'email' => null,
            'phone_whatsapp' => '96771'.rand(1000000, 9999999),
            'password' => Hash::make('Secret123!'),
            'role' => 'student',
            'status' => 'active',
            'is_active' => true,
        ]);
        $strangerDownload = $this->actingAs($stranger)->get(route('groups.sessions.download', ['sessionId' => $session->id, 'type' => 'summary']));
        $strangerDownload->assertStatus(403);

        // فحص حذف الجلسة وتنظيف الملفات
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.groups.evaluations.destroy', $session->id));
        $deleteResponse->assertRedirect();

        $this->assertNull(GroupSession::find($session->id));

        // تنظيف
        $group->students()->detach();
        $group->delete();
        $student->delete();
        $stranger->delete();
    }
}
