<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\StudentSurvey;
use App\Models\StudyGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentSurveySeeder extends Seeder
{
    public function run(): void
    {
        $teacher1 = User::where('role', 'teacher')->first();
        $teacher2 = User::where('role', 'teacher')->skip(1)->first() ?: $teacher1;

        $student1 = User::where('role', 'student')->first();
        $student2 = User::where('role', 'student')->skip(3)->first() ?: $student1;
        $student3 = User::where('role', 'student')->skip(4)->first() ?: $student1;

        $group = StudyGroup::first();
        $course = Course::first();

        // 1. تقييمات يومية سريعة
        StudentSurvey::create([
            'survey_type' => 'daily',
            'survey_date' => Carbon::today(),
            'teacher_id' => $teacher1?->id,
            'study_group_id' => $group?->id,
            'course_id' => $course?->id,
            'student_id' => $student1?->id,
            'student_name' => $student1?->name,
            'student_phone' => '+967771234567',
            'is_anonymous' => false,
            'teacher_clarity' => 5,
            'daily_benefited' => 'yes',
            'teacher_interaction' => 5,
            'daily_level_appropriate' => 'yes',
            'notes_suggestions' => 'شرح ممتاز جداً وممتع، وتفاعل الأستاذ رائع بالأسئلة.',
        ]);

        StudentSurvey::create([
            'survey_type' => 'daily',
            'survey_date' => Carbon::today(),
            'teacher_id' => $teacher2?->id,
            'study_group_id' => $group?->id,
            'course_id' => $course?->id,
            'student_id' => null,
            'student_name' => 'طالب من دفعة A',
            'student_phone' => '+967733445566',
            'is_anonymous' => true,
            'teacher_clarity' => 4,
            'daily_benefited' => 'yes',
            'teacher_interaction' => 4,
            'daily_level_appropriate' => 'yes',
            'notes_suggestions' => 'أتمنى إعطاء وقت أطول لممارسة المحادثة في نهاية اللقاء.',
        ]);

        StudentSurvey::create([
            'survey_type' => 'daily',
            'survey_date' => Carbon::yesterday(),
            'teacher_id' => $teacher1?->id,
            'study_group_id' => $group?->id,
            'course_id' => $course?->id,
            'student_id' => $student2?->id,
            'student_name' => $student2?->name,
            'is_anonymous' => false,
            'teacher_clarity' => 5,
            'daily_benefited' => 'yes',
            'teacher_interaction' => 4,
            'daily_level_appropriate' => 'yes',
            'notes_suggestions' => 'الحصة كانت مفيدة جداً خصوصاً حل التمارين التطبيقية.',
        ]);

        // 2. تقييمات أسبوعية شاملة
        StudentSurvey::create([
            'survey_type' => 'weekly',
            'survey_date' => Carbon::now()->subDays(2),
            'teacher_id' => $teacher1?->id,
            'study_group_id' => $group?->id,
            'course_id' => $course?->id,
            'student_id' => $student3?->id,
            'student_name' => $student3?->name,
            'is_anonymous' => false,
            'teacher_competence' => 5,
            'teacher_clarity' => 5,
            'teacher_interaction' => 5,
            'teacher_punctuality' => 5,
            'curriculum_suitability' => 4,
            'curriculum_alignment' => 5,
            'learning_challenges' => ['speaking', 'listening'],
            'admin_communication' => 5,
            'remote_learning_ease' => 5,
            'remote_learning_difficulties' => null,
            'notes_suggestions' => 'أسبوع رائع ومليء بالاستفادة، نرجو تكثيف فقرات الاستماع الصوتي.',
        ]);

        StudentSurvey::create([
            'survey_type' => 'weekly',
            'survey_date' => Carbon::now()->subDays(3),
            'teacher_id' => $teacher2?->id,
            'study_group_id' => $group?->id,
            'course_id' => $course?->id,
            'student_id' => null,
            'student_name' => 'طالب مجهول',
            'is_anonymous' => true,
            'teacher_competence' => 4,
            'teacher_clarity' => 4,
            'teacher_interaction' => 4,
            'teacher_punctuality' => 4,
            'curriculum_suitability' => 4,
            'curriculum_alignment' => 4,
            'learning_challenges' => ['grammar'],
            'admin_communication' => 4,
            'remote_learning_ease' => 4,
            'remote_learning_difficulties' => 'انقطع الاتصال في بداية حصة الثلاثاء لدقائق معدودة.',
            'notes_suggestions' => 'توضيح القواعد بأمثلة واقعية أكثر.',
        ]);

        // 3. تقييمات نهاية الدبلوم
        StudentSurvey::create([
            'survey_type' => 'diploma_final',
            'survey_date' => Carbon::now()->subDays(4),
            'teacher_id' => $teacher1?->id,
            'study_group_id' => $group?->id,
            'course_id' => $course?->id,
            'student_id' => $student1?->id,
            'student_name' => $student1?->name,
            'student_phone' => '+967771234567',
            'is_anonymous' => false,
            'teacher_competence' => 5,
            'teacher_clarity' => 5,
            'teacher_interaction' => 5,
            'teacher_punctuality' => 5,
            'curriculum_suitability' => 5,
            'curriculum_alignment' => 5,
            'learning_challenges' => ['speaking'],
            'admin_communication' => 5,
            'management_organization' => 5,
            'remote_learning_difficulties' => 'لا توجد أي صعوبات، النظام ممتاز جداً.',
            'best_feature' => 'المتابعة الحثيثة والحرص الشديد من إدارة المكتب والدكتور فخرالدين على كل طالب.',
            'recommend_institute' => 'yes',
            'recommend_reason' => 'لأن المنصة تجمع بين المنهج القوي والمعلمين المحترفين والمرونة التامة.',
            'notes_suggestions' => 'كل الشكر والتقدير لإدارة ASOTS وللدكتور فخرالدين الصوفي على هذه التجربة الثرية.',
        ]);
    }
}
