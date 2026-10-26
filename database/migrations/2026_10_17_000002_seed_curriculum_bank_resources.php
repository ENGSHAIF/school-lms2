<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    public function up(): void
    {
        $adminId = DB::table('users')->where('role', 'admin')->value('id') ?? 1;

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

        @File::put(public_path($level1BookFile), $samplePdfContent);
        @File::put(public_path($level1AudioFile), $sampleMp3Bytes);
        @File::put(public_path($level1WorksheetFile), $samplePdfContent);

        @File::put(public_path($level2BookFile), $samplePdfContent);
        @File::put(public_path($level2AudioFile), $sampleMp3Bytes);
        @File::put(public_path($level2WorksheetFile), $samplePdfContent);

        // جلب أول دورة (برنامج اللغة الإنجليزية العامة)
        $courseId = DB::table('courses')->value('id');
        if (!$courseId) {
            $courseId = DB::table('courses')->insertGetId([
                'name' => 'برنامج اللغة الإنجليزية العامة (General English)',
                'description' => 'البرنامج المعتمد لتأسيس ومهارات المحادثة الإنجليزية والتدرج في مستويات CEFR العالمية.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // جلب المستوى الأول
        $level1 = DB::table('levels')->where('level_order', 1)->first();
        if (!$level1) {
            $level1Id = DB::table('levels')->insertGetId([
                'course_id' => $courseId,
                'name' => 'المستوى الأول: التأسيسي (Level 1: Beginner A1)',
                'description' => 'منهج التأسيس الشامل لقواعد اللغة الأساسية، المحادثات اليومية الأولية، ومخارج الحروف.',
                'fee_amount' => 80.00,
                'currency' => 'USD',
                'level_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $level1Id = $level1->id;
            DB::table('levels')->where('id', $level1Id)->update([
                'description' => 'منهج التأسيس الشامل لقواعد اللغة الأساسية، المحادثات اليومية الأولية، ومخارج الحروف.',
            ]);
        }

        // جلب المستوى الثاني
        $level2 = DB::table('levels')->where('level_order', 2)->first();
        if (!$level2) {
            $level2Id = DB::table('levels')->insertGetId([
                'course_id' => $courseId,
                'name' => 'المستوى الثاني: المتوسط (Level 2: Elementary A2)',
                'description' => 'تطوير الطلاقة في المحادثة، التراكيب المركبة، والمواقف الاجتماعية والسياحية.',
                'fee_amount' => 100.00,
                'currency' => 'USD',
                'level_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $level2Id = $level2->id;
            DB::table('levels')->where('id', $level2Id)->update([
                'description' => 'تطوير الطلاقة في المحادثة، التراكيب المركبة، والمواقف الاجتماعية والسياحية.',
            ]);
        }

        // إنشاء كويز للمستوى الأول
        $quizLevel1Id = DB::table('exams')->where('level_id', $level1Id)->where('type', 'quiz')->value('id');
        if (!$quizLevel1Id) {
            $quizLevel1Id = DB::table('exams')->insertGetId([
                'level_id' => $level1Id,
                'title' => 'كويز الوحدة الأولى: القواعد والمحادثة (Quiz 1 - Fundamentals)',
                'description' => 'اختبار تقييم استيعاب الوحدة الأولى للمستوى الأول.',
                'type' => 'quiz',
                'duration_minutes' => 20,
                'total_points' => 20.00,
                'passing_score' => 12.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('exam_questions')->insert([
                [
                    'exam_id' => $quizLevel1Id,
                    'type' => 'mcq',
                    'question_text' => 'Choose the correct greeting for the morning:',
                    'options' => json_encode(['Good morning', 'Good night', 'Goodbye', 'Good evening'], JSON_UNESCAPED_UNICODE),
                    'correct_answer' => 'Good morning',
                    'points' => 10.00,
                    'order_index' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'exam_id' => $quizLevel1Id,
                    'type' => 'mcq',
                    'question_text' => 'What is the correct pronoun: "___ am a student"?',
                    'options' => json_encode(['I', 'He', 'She', 'They'], JSON_UNESCAPED_UNICODE),
                    'correct_answer' => 'I',
                    'points' => 10.00,
                    'order_index' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // إنشاء كويز للمستوى الثاني
        $quizLevel2Id = DB::table('exams')->where('level_id', $level2Id)->where('type', 'quiz')->value('id');
        if (!$quizLevel2Id) {
            $quizLevel2Id = DB::table('exams')->insertGetId([
                'level_id' => $level2Id,
                'title' => 'كويز تقييم منتصف المستوى الثاني (Level 2 Progress Quiz)',
                'description' => 'اختبار قصير لمراجعة المفردات والتراكيب المركبة.',
                'type' => 'quiz',
                'duration_minutes' => 25,
                'total_points' => 25.00,
                'passing_score' => 15.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('exam_questions')->insert([
                [
                    'exam_id' => $quizLevel2Id,
                    'type' => 'mcq',
                    'question_text' => 'Yesterday, I ________ to the supermarket to buy fruits.',
                    'options' => json_encode(['went', 'go', 'going', 'gone'], JSON_UNESCAPED_UNICODE),
                    'correct_answer' => 'went',
                    'points' => 12.50,
                    'order_index' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'exam_id' => $quizLevel2Id,
                    'type' => 'mcq',
                    'question_text' => 'Which sentence expresses future plan correctly?',
                    'options' => json_encode(['I am going to travel next month', 'I went travel next month', 'I travel yesterday', 'I am go traveling'], JSON_UNESCAPED_UNICODE),
                    'correct_answer' => 'I am going to travel next month',
                    'points' => 12.50,
                    'order_index' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // إضافة موارد المستوى الأول إلى جدول level_resources
        $level1Items = [
            [
                'level_id' => $level1Id,
                'title' => 'كتاب الطالب المعتمد: المستوى الأول التأسيسي (Level 1 Student Coursebook)',
                'type' => 'book',
                'description' => 'الكتاب الرسمي لمنهج المستوى الأول يشمل 8 وحدات تدريبية وتطبيقات محادثة وقواعد.',
                'file_path' => $level1BookFile,
                'file_name' => 'Level1_Coursebook_Full.pdf',
                'file_size' => '14.2 MB',
                'external_url' => null,
                'duration_minutes' => null,
                'exam_id' => null,
                'order_index' => 1,
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_id' => $level1Id,
                'title' => 'التسجيل الصوتي: محادثات واستماع الوحدة الأولى (Unit 1 Audio Tracks)',
                'type' => 'audio',
                'description' => 'تسجيلات صوتية بنطق أمريكي وبريطاني للتدريب على الاستماع والمحادثة اليومية.',
                'file_path' => $level1AudioFile,
                'file_name' => 'Unit1_Listening_Dialogue.mp3',
                'file_size' => '4.8 MB',
                'external_url' => null,
                'duration_minutes' => 15,
                'exam_id' => null,
                'order_index' => 2,
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_id' => $level1Id,
                'title' => 'محاضرة فيديو تعليمية: شرح كتاب المستوى الأول - الدرس الأول (Video Lecture)',
                'type' => 'video',
                'description' => 'شرح مرئي مسجل يغطي الوحدة الأولى: قواعد تكوين الجمل، أدوات التعريف، والضمائر.',
                'file_path' => null,
                'file_name' => null,
                'file_size' => null,
                'external_url' => 'https://www.youtube.com/watch?v=juKd26QKNAw',
                'duration_minutes' => 32,
                'exam_id' => null,
                'order_index' => 3,
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_id' => $level1Id,
                'title' => 'ورقة نشاط وتطبيق: كتابة الجمل والمفردات اليومية (Daily Activity Worksheet 1)',
                'type' => 'worksheet',
                'description' => 'ورقة عمل منزلية يقوم الطالب بحلها وإرسالها للمعلم عبر بوابة المجموعة.',
                'file_path' => $level1WorksheetFile,
                'file_name' => 'Level1_Activity_Worksheet_01.pdf',
                'file_size' => '1.5 MB',
                'external_url' => null,
                'duration_minutes' => null,
                'exam_id' => null,
                'order_index' => 4,
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_id' => $level1Id,
                'title' => 'كويز واختبار قصير: تقييم مهارات الوحدة الأولى (Quiz 1)',
                'type' => 'quiz',
                'description' => 'اختبار إلكتروني قصير فوري التصحيح لقياس فهم الطالب لمحتوى الوحدة.',
                'file_path' => null,
                'file_name' => null,
                'file_size' => null,
                'external_url' => null,
                'duration_minutes' => 20,
                'exam_id' => $quizLevel1Id,
                'order_index' => 5,
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($level1Items as $item) {
            $exists = DB::table('level_resources')
                ->where('level_id', $item['level_id'])
                ->where('title', $item['title'])
                ->exists();
            if (!$exists) {
                DB::table('level_resources')->insert($item);
            }
        }

        // إضافة موارد المستوى الثاني
        $level2Items = [
            [
                'level_id' => $level2Id,
                'title' => 'كتاب الطالب للمستوى الثاني: المهارات المتوسطة (Level 2 Coursebook)',
                'type' => 'book',
                'description' => 'كتاب المنهج الدراسي للمستوى الثاني يشمل تراكيب القواعد الأزمنة، المقارنات، والوظائف.',
                'file_path' => $level2BookFile,
                'file_name' => 'Level2_Coursebook_Elementary.pdf',
                'file_size' => '18.6 MB',
                'external_url' => null,
                'duration_minutes' => null,
                'exam_id' => null,
                'order_index' => 1,
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_id' => $level2Id,
                'title' => 'التسجيل الصوتي: حوارات السفر والمواقف الاجتماعية (Travel & Situations Audio)',
                'type' => 'audio',
                'description' => 'تسجيلات استماع تفاعلية لمواقف المطارات والفنادق وحوارات العمل.',
                'file_path' => $level2AudioFile,
                'file_name' => 'Level2_Travel_Listening.mp3',
                'file_size' => '6.2 MB',
                'external_url' => null,
                'duration_minutes' => 22,
                'exam_id' => null,
                'order_index' => 2,
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_id' => $level2Id,
                'title' => 'محاضرة فيديو: شرح الأزمنة الماضية والمستقبلية (Video Lecture: Past & Future)',
                'type' => 'video',
                'description' => 'شرح تفاعلي للأزمنة واستخداماتها الواقعية في الحوارات والمقابلات.',
                'file_path' => null,
                'file_name' => null,
                'file_size' => null,
                'external_url' => 'https://www.youtube.com/watch?v=L9A8CE46u3E',
                'duration_minutes' => 40,
                'exam_id' => null,
                'order_index' => 3,
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_id' => $level2Id,
                'title' => 'ورقة نشاط وتطبيق: كتابة فقرة تعبيرية ومحادثة ثنائية (Worksheet: Paragraph Writing)',
                'type' => 'worksheet',
                'description' => 'نشاط كتابي وتطبيقي للتدريب على بناء الفقرات وتوسيع الحصيلة اللغوية.',
                'file_path' => $level2WorksheetFile,
                'file_name' => 'Level2_Writing_Worksheet_01.pdf',
                'file_size' => '1.8 MB',
                'external_url' => null,
                'duration_minutes' => null,
                'exam_id' => null,
                'order_index' => 4,
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'level_id' => $level2Id,
                'title' => 'كويز ومراجعة منتصف المستوى الثاني (Mid-Term Quiz)',
                'type' => 'quiz',
                'description' => 'كويز إلكتروني لتقييم تقدم الطالب في مهارات القراءة والقواعد.',
                'file_path' => null,
                'file_name' => null,
                'file_size' => null,
                'external_url' => null,
                'duration_minutes' => 25,
                'exam_id' => $quizLevel2Id,
                'order_index' => 5,
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($level2Items as $item) {
            $exists = DB::table('level_resources')
                ->where('level_id', $item['level_id'])
                ->where('title', $item['title'])
                ->exists();
            if (!$exists) {
                DB::table('level_resources')->insert($item);
            }
        }
    }

    public function down(): void
    {
        DB::table('level_resources')->truncate();
    }
};
