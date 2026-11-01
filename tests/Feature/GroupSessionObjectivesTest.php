<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\GroupSession;
use App\Models\Level;
use App\Models\StudyGroup;
use App\Models\User;
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
}
