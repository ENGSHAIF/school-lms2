<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء المستخدمين الأساسيين (مدير، محاسب، معلم، طالب تجريبي)
        $adminId = DB::table('users')->insertGetId([
            'name' => 'مدير النظام (Super Admin)',
            'email' => 'admin@school.com',
            'phone_whatsapp' => '+967770000001',
            'role' => 'admin',
            'status' => 'active',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $accountantId = DB::table('users')->insertGetId([
            'name' => 'المحاسب المالي',
            'email' => 'accountant@school.com',
            'phone_whatsapp' => '+967770000002',
            'role' => 'accountant',
            'status' => 'active',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $teacherId = DB::table('users')->insertGetId([
            'name' => 'أ. أحمد مدرس التوفل',
            'email' => 'teacher@school.com',
            'phone_whatsapp' => '+967770000003',
            'role' => 'teacher',
            'status' => 'active',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $studentId = DB::table('users')->insertGetId([
            'name' => 'الطالب التجريبي',
            'email' => 'student@school.com',
            'phone_whatsapp' => '+967770000004',
            'role' => 'student',
            'status' => 'pending_test',
            'student_code' => 'STU-2026-001',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. الحسابات المالية الافتراضية (الصندوق الرئيسي، البنك، إيرادات الرسوم)
        $cashBoxId = DB::table('financial_accounts')->insertGetId([
            'account_code' => '1101',
            'account_name' => 'الصندوق الرئيسي - النقدية',
            'account_type' => 'cash_box',
            'currency' => 'USD',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $bankAccountId = DB::table('financial_accounts')->insertGetId([
            'account_code' => '1102',
            'account_name' => 'الحساب البنكي الرئيسي',
            'account_type' => 'bank',
            'currency' => 'USD',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // حساب الطالب المالي
        DB::table('financial_accounts')->insert([
            'account_code' => '210001',
            'account_name' => 'حساب الطالب: الطالب التجريبي',
            'account_type' => 'student',
            'user_id' => $studentId,
            'currency' => 'USD',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. الدورات والمستويات الأكاديمية
        $courseGeneralId = DB::table('courses')->insertGetId([
            'name' => 'برنامج اللغة الإنجليزية العامة (General English)',
            'description' => 'برنامج تدريبي شامل للمهارات الأربع (استماع، تحدث، قراءة، وكتابة).',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $courseToeflId = DB::table('courses')->insertGetId([
            'name' => 'برنامج الإعداد لاختبار التوفل الدولي (TOEFL iBT Preparation)',
            'description' => 'تدريب مكثف على مهارات اختبار التوفل الرسمي وحل النماذج الشاملة.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $level1Id = DB::table('levels')->insertGetId([
            'course_id' => $courseGeneralId,
            'name' => 'المستوى الأول: مبتدئ (Level 1: Beginner)',
            'level_order' => 1,
            'fee_amount' => 80.00,
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $level2Id = DB::table('levels')->insertGetId([
            'course_id' => $courseGeneralId,
            'name' => 'المستوى الثاني: متوسط (Level 2: Intermediate)',
            'level_order' => 2,
            'fee_amount' => 100.00,
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $levelToeflId = DB::table('levels')->insertGetId([
            'course_id' => $courseToeflId,
            'name' => 'دورة التوفل الشاملة مع المحاكاة (TOEFL Comprehensive)',
            'level_order' => 1,
            'fee_amount' => 150.00,
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. اختبار تحديد المستوى الإلزامي (Placement Test)
        $examId = DB::table('exams')->insertGetId([
            'title' => 'اختبار تحديد المستوى والقبول العام (English Placement Test)',
            'description' => 'اختبار قياس الكفاءة يشمل القواعد والمفردات واختبار التحدث الصوتي بالذكاء الاصطناعي.',
            'type' => 'placement_test',
            'duration_minutes' => 30,
            'total_points' => 100.00,
            'passing_score' => 50.00,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // أسئلة الاختبار التجريبي (قواعد + نطق صوتي)
        DB::table('exam_questions')->insert([
            [
                'exam_id' => $examId,
                'type' => 'mcq',
                'question_text' => 'She __________ to school by bus every day.',
                'context_passage' => null,
                'audio_url' => null,
                'options' => json_encode(['go', 'goes', 'going', 'went']),
                'correct_answer' => 'goes',
                'expected_speech_text' => null,
                'points' => 25.00,
                'order_index' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'exam_id' => $examId,
                'type' => 'mcq',
                'question_text' => 'If it rains tomorrow, we __________ the picnic.',
                'context_passage' => null,
                'audio_url' => null,
                'options' => json_encode(['cancel', 'will cancel', 'cancelled', 'have cancelled']),
                'correct_answer' => 'will cancel',
                'expected_speech_text' => null,
                'points' => 25.00,
                'order_index' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'exam_id' => $examId,
                'type' => 'speaking_ai',
                'question_text' => 'اضغط على زر التسجيل واقرأ الجملة التالية بوضوح لفحص مخارج الحروف وطلاقة النطق:',
                'context_passage' => null,
                'audio_url' => null,
                'options' => null,
                'correct_answer' => null,
                'expected_speech_text' => 'Learning English opens great opportunities for personal and professional growth around the world.',
                'points' => 50.00,
                'order_index' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 5. باقات الحصص وفق نموذج ASOTS (12, 24, 48 حصة)
        $pkg1Id = DB::table('packages')->insertGetId([
            'title' => 'باقة التأسيس والمحادثة الحية (12 حصة)',
            'description' => '12 جلسة تدريبية مكثفة 1-on-1 مدة كل جلسة 25 دقيقة مع تصحيح النطق المباشر.',
            'lessons_count' => 12,
            'price' => 120.00,
            'currency' => 'USD',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pkg2Id = DB::table('packages')->insertGetId([
            'title' => 'باقة التوفل والطلاقة المتقدمة (24 حصة)',
            'description' => '24 جلسة مخصصة لاجتياز اختبار التحدث في TOEFL iBT والمحادثات المتقدمة.',
            'lessons_count' => 24,
            'price' => 220.00,
            'currency' => 'USD',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // محفظة الطالب التجريبي (إيداع رصيد 12 حصة لتجربة الحجز فوراً)
        DB::table('student_wallets')->insert([
            'user_id' => $studentId,
            'total_lessons' => 12,
            'used_lessons' => 0,
            'remaining_lessons' => 12,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. جدول خانات تفرغ المعلم (خانات 25 دقيقة جاهزة للحجز)
        $today = date('Y-m-d');
        DB::table('teacher_schedules')->insert([
            [
                'teacher_id' => $teacherId,
                'session_date' => $today,
                'start_time' => '17:00:00',
                'end_time' => '17:25:00',
                'is_booked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => $teacherId,
                'session_date' => $today,
                'start_time' => '17:30:00',
                'end_time' => '17:55:00',
                'is_booked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => $teacherId,
                'session_date' => $today,
                'start_time' => '18:00:00',
                'end_time' => '18:25:00',
                'is_booked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => $teacherId,
                'session_date' => $today,
                'start_time' => '19:00:00',
                'end_time' => '19:25:00',
                'is_booked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
