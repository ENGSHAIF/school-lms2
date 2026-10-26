<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Level;
use App\Models\LevelResource;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\User;
use Illuminate\Support\Facades\File;

class CurriculumBankSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin?->id ?? 1;

        // التأكد من وجود مجلد رفع الموارد
        $uploadsDir = public_path('uploads/level_resources');
        if (!File::exists($uploadsDir)) {
            File::makeDirectory($uploadsDir, 0777, true, true);
        }

        // إنشاء ملفات تجريبية حقيقية صالحة للتحميل والاستماع
        $samplePdfContent = "%PDF-1.4\n1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n4 0 obj << /Length 72 >> stream\nBT\n/F1 20 Tf\n80 720 Td\n(ASOTS Academy - Level Coursebook & Curriculum) Tj\nET\nendstream endobj\n5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\nxref\n0 6\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000244 00000 n \n0000000367 00000 n \ntrailer << /Size 6 /Root 1 0 R >>\nstartxref\n440\n%%EOF\n";

        // ملف MP3 صامت صالح للمشغل الصوتي HTML5 (MPEG Layer 3 frame)
        $sampleMp3Bytes = hex2bin('fffb906400000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000');

        $level1BookFile = 'uploads/level_resources/level1_coursebook.pdf';
        $level1AudioFile = 'uploads/level_resources/level1_unit1_listening.mp3';
        $level1WorksheetFile = 'uploads/level_resources/level1_worksheet1.pdf';

        $level2BookFile = 'uploads/level_resources/level2_coursebook.pdf';
        $level2AudioFile = 'uploads/level_resources/level2_unit1_listening.mp3';
        $level2WorksheetFile = 'uploads/level_resources/level2_worksheet1.pdf';

        File::put(public_path($level1BookFile), $samplePdfContent);
        File::put(public_path($level1AudioFile), $sampleMp3Bytes);
        File::put(public_path($level1WorksheetFile), $samplePdfContent);

        File::put(public_path($level2BookFile), $samplePdfContent);
        File::put(public_path($level2AudioFile), $sampleMp3Bytes);
        File::put(public_path($level2WorksheetFile), $samplePdfContent);

        // جلب أو إنشاء المستوى الأول والمستوى الثاني
        $course = Course::firstOrCreate(
            ['name' => 'برنامج اللغة الإنجليزية العامة (General English)'],
            ['description' => 'البرنامج المعتمد لتأسيس ومهارات المحادثة الإنجليزية والتدرج في مستويات CEFR العالمية.', 'is_active' => true]
        );

        $level1 = Level::firstOrCreate(
            ['course_id' => $course->id, 'level_order' => 1],
            [
                'name' => 'المستوى الأول: التأسيسي (Level 1: Beginner A1)',
                'description' => 'منهج التأسيس الشامل لقواعد اللغة الأساسية، المحادثات اليومية الأولية، ومخارج الحروف.',
                'fee_amount' => 80.00,
                'currency' => 'USD',
                'is_active' => true,
            ]
        );

        $level2 = Level::firstOrCreate(
            ['course_id' => $course->id, 'level_order' => 2],
            [
                'name' => 'المستوى الثاني: المتوسط (Level 2: Elementary A2)',
                'description' => 'تطوير الطلاقة في المحادثة، التراكيب المركبة، والمواقف الاجتماعية والسياحية.',
                'fee_amount' => 100.00,
                'currency' => 'USD',
                'is_active' => true,
            ]
        );

        // إنشاء كويز تفاعلي للمستوى الأول
        $quizLevel1 = Exam::firstOrCreate(
            ['level_id' => $level1->id, 'type' => 'quiz'],
            [
                'title' => 'كويز الوحدة الأولى: القواعد والمحادثة (Quiz 1 - Fundamentals)',
                'description' => 'اختبار تقييم استيعاب الوحدة الأولى للمستوى الأول.',
                'duration_minutes' => 20,
                'total_points' => 20.00,
                'passing_score' => 12.00,
                'is_active' => true,
            ]
        );

        if ($quizLevel1->questions()->count() === 0) {
            ExamQuestion::create([
                'exam_id' => $quizLevel1->id,
                'type' => 'mcq',
                'question_text' => 'Choose the correct greeting for the morning:',
                'options' => ['Good morning', 'Good night', 'Goodbye', 'Good evening'],
                'correct_answer' => 'Good morning',
                'points' => 10.00,
                'order_index' => 1,
            ]);

            ExamQuestion::create([
                'exam_id' => $quizLevel1->id,
                'type' => 'mcq',
                'question_text' => 'What is the correct pronoun: "___ am a student"?',
                'options' => ['I', 'He', 'She', 'They'],
                'correct_answer' => 'I',
                'points' => 10.00,
                'order_index' => 2,
            ]);
        }

        // إنشاء كويز تفاعلي للمستوى الثاني
        $quizLevel2 = Exam::firstOrCreate(
            ['level_id' => $level2->id, 'type' => 'quiz'],
            [
                'title' => 'كويز تقييم منتصف المستوى الثاني (Level 2 Progress Quiz)',
                'description' => 'اختبار قصير لمراجعة المفردات والتراكيب المركبة.',
                'duration_minutes' => 25,
                'total_points' => 25.00,
                'passing_score' => 15.00,
                'is_active' => true,
            ]
        );

        if ($quizLevel2->questions()->count() === 0) {
            ExamQuestion::create([
                'exam_id' => $quizLevel2->id,
                'type' => 'mcq',
                'question_text' => 'Yesterday, I ________ to the supermarket to buy fruits.',
                'options' => ['went', 'go', 'going', 'gone'],
                'correct_answer' => 'went',
                'points' => 12.50,
                'order_index' => 1,
            ]);

            ExamQuestion::create([
                'exam_id' => $quizLevel2->id,
                'type' => 'mcq',
                'question_text' => 'Which sentence expresses future plan correctly?',
                'options' => ['I am going to travel next month', 'I went travel next month', 'I travel yesterday', 'I am go traveling'],
                'correct_answer' => 'I am going to travel next month',
                'points' => 12.50,
                'order_index' => 2,
            ]);
        }

        // =========================================================
        // موارد المستوى الأول (Level 1 Master Curriculum Bank)
        // =========================================================
        $level1Resources = [
            [
                'level_id' => $level1->id,
                'title' => 'كتاب الطالب المعتمد: المستوى الأول التأسيسي (Level 1 Student Coursebook)',
                'type' => 'book',
                'description' => 'الكتاب الرسمي لمنهج المستوى الأول يشمل 8 وحدات تدريبية وتطبيقات محادثة وقواعد.',
                'file_path' => $level1BookFile,
                'file_name' => 'Level1_Coursebook_Full.pdf',
                'file_size' => '14.2 MB',
                'external_url' => null,
                'duration_minutes' => null,
                'order_index' => 1,
                'created_by' => $adminId,
            ],
            [
                'level_id' => $level1->id,
                'title' => 'التسجيل الصوتي: محادثات واستماع الوحدة الأولى (Unit 1 Audio Tracks)',
                'type' => 'audio',
                'description' => 'تسجيلات صوتية بنطق أمريكي وبريطاني للتدريب على الاستماع والمحادثة اليومية.',
                'file_path' => $level1AudioFile,
                'file_name' => 'Unit1_Listening_Dialogue.mp3',
                'file_size' => '4.8 MB',
                'external_url' => null,
                'duration_minutes' => 15,
                'order_index' => 2,
                'created_by' => $adminId,
            ],
            [
                'level_id' => $level1->id,
                'title' => 'محاضرة فيديو تعليمية: شرح كتاب المستوى الأول - الدرس الأول (Video Lecture)',
                'type' => 'video',
                'description' => 'شرح مرئي مسجل يغطي الوحدة الأولى: قواعد تكوين الجمل، أدوات التعريف، والضمائر.',
                'file_path' => null,
                'file_name' => null,
                'file_size' => null,
                'external_url' => 'https://www.youtube.com/watch?v=juKd26QKNAw', // رابط فيديو تعليمي
                'duration_minutes' => 32,
                'order_index' => 3,
                'created_by' => $adminId,
            ],
            [
                'level_id' => $level1->id,
                'title' => 'ورقة نشاط وتطبيق: كتابة الجمل والمفردات اليومية (Daily Activity Worksheet 1)',
                'type' => 'worksheet',
                'description' => 'ورقة عمل منزلية يقوم الطالب بحلها وإرسالها للمعلم عبر بوابة المجموعة.',
                'file_path' => $level1WorksheetFile,
                'file_name' => 'Level1_Activity_Worksheet_01.pdf',
                'file_size' => '1.5 MB',
                'external_url' => null,
                'duration_minutes' => null,
                'order_index' => 4,
                'created_by' => $adminId,
            ],
            [
                'level_id' => $level1->id,
                'title' => 'كويز واختبار قصير: تقييم مهارات الوحدة الأولى (Quiz 1)',
                'type' => 'quiz',
                'description' => 'اختبار إلكتروني قصير فوري التصحيح لقياس فهم الطالب لمحتوى الوحدة.',
                'file_path' => null,
                'file_name' => null,
                'file_size' => null,
                'external_url' => null,
                'duration_minutes' => 20,
                'exam_id' => $quizLevel1->id,
                'order_index' => 5,
                'created_by' => $adminId,
            ],
        ];

        foreach ($level1Resources as $res) {
            LevelResource::firstOrCreate(
                ['level_id' => $res['level_id'], 'title' => $res['title']],
                $res
            );
        }

        // =========================================================
        // موارد المستوى الثاني (Level 2 Master Curriculum Bank)
        // =========================================================
        $level2Resources = [
            [
                'level_id' => $level2->id,
                'title' => 'كتاب الطالب للمستوى الثاني: المهارات المتوسطة (Level 2 Coursebook)',
                'type' => 'book',
                'description' => 'كتاب المنهج الدراسي للمستوى الثاني يشمل تراكيب القواعد الأزمنة، المقارنات، والوظائف.',
                'file_path' => $level2BookFile,
                'file_name' => 'Level2_Coursebook_Elementary.pdf',
                'file_size' => '18.6 MB',
                'external_url' => null,
                'duration_minutes' => null,
                'order_index' => 1,
                'created_by' => $adminId,
            ],
            [
                'level_id' => $level2->id,
                'title' => 'التسجيل الصوتي: حوارات السفر والمواقف الاجتماعية (Travel & Situations Audio)',
                'type' => 'audio',
                'description' => 'تسجيلات استماع تفاعلية لمواقف المطارات والفنادق وحوارات العمل.',
                'file_path' => $level2AudioFile,
                'file_name' => 'Level2_Travel_Listening.mp3',
                'file_size' => '6.2 MB',
                'external_url' => null,
                'duration_minutes' => 22,
                'order_index' => 2,
                'created_by' => $adminId,
            ],
            [
                'level_id' => $level2->id,
                'title' => 'محاضرة فيديو: شرح الأزمنة الماضية والمستقبلية (Video Lecture: Past & Future)',
                'type' => 'video',
                'description' => 'شرح تفاعلي للأزمنة واستخداماتها الواقعية في الحوارات والمقابلات.',
                'file_path' => null,
                'file_name' => null,
                'file_size' => null,
                'external_url' => 'https://www.youtube.com/watch?v=L9A8CE46u3E',
                'duration_minutes' => 40,
                'order_index' => 3,
                'created_by' => $adminId,
            ],
            [
                'level_id' => $level2->id,
                'title' => 'ورقة نشاط وتطبيق: كتابة فقرة تعبيرية ومحادثة ثنائية (Worksheet: Paragraph Writing)',
                'type' => 'worksheet',
                'description' => 'نشاط كتابي وتطبيقي للتدريب على بناء الفقرات وتوسيع الحصيلة اللغوية.',
                'file_path' => $level2WorksheetFile,
                'file_name' => 'Level2_Writing_Worksheet_01.pdf',
                'file_size' => '1.8 MB',
                'external_url' => null,
                'duration_minutes' => null,
                'order_index' => 4,
                'created_by' => $adminId,
            ],
            [
                'level_id' => $level2->id,
                'title' => 'كويز ومراجعة منتصف المستوى الثاني (Mid-Term Quiz)',
                'type' => 'quiz',
                'description' => 'كويز إلكتروني لتقييم تقدم الطالب في مهارات القراءة والقواعد.',
                'file_path' => null,
                'file_name' => null,
                'file_size' => null,
                'external_url' => null,
                'duration_minutes' => 25,
                'exam_id' => $quizLevel2->id,
                'order_index' => 5,
                'created_by' => $adminId,
            ],
        ];

        foreach ($level2Resources as $res) {
            LevelResource::firstOrCreate(
                ['level_id' => $res['level_id'], 'title' => $res['title']],
                $res
            );
        }
    }
}
