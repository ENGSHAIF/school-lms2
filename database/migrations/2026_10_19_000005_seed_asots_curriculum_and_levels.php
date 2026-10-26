<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Level;
use App\Models\Course;

return new class extends Migration
{
    public function up(): void
    {
        // 1. تعريف المستويات المعتمدة في ASOTS وفق البروشور الرسمي
        $levelsData = [
            1 => [
                'name_en' => 'BEGINNERS 1',
                'name_ar' => 'المستوى المبتدئ الأول',
                'name' => 'المستوى المبتدئ الأول (BEGINNERS 1)',
                'level_order' => 1,
                'fee_amount' => 50000.00,
                'currency' => 'YER',
                'description' => 'المستوى التأسيسي للأطفال والناشئين والراغبين في تعلم أساسيات اللغة الإنجليزية وبناء المفردات الأولية.',
            ],
            2 => [
                'name_en' => 'BEGINNERS 2',
                'name_ar' => 'المستوى المبتدئ الثاني',
                'name' => 'المستوى المبتدئ الثاني (BEGINNERS 2)',
                'level_order' => 2,
                'fee_amount' => 50000.00,
                'currency' => 'YER',
                'description' => 'تطوير مهارات القراءة والنطق وتراكيب الجمل البسيطة والتفاعل اليومي باللغة الإنجليزية.',
            ],
            3 => [
                'name_en' => 'INTERMEDIATE 1',
                'name_ar' => 'المستوى المتوسط الأول',
                'name' => 'المستوى المتوسط الأول (INTERMEDIATE 1)',
                'level_order' => 3,
                'fee_amount' => 60000.00,
                'currency' => 'YER',
                'description' => 'الارتقاء بالقدرات اللغوية والتعبير الشفهي وقواعد المحادثة والاستماع المتقدم.',
            ],
            4 => [
                'name_en' => 'INTERMEDIATE 2',
                'name_ar' => 'المستوى المتوسط الثاني',
                'name' => 'المستوى المتوسط الثاني (INTERMEDIATE 2)',
                'level_order' => 4,
                'fee_amount' => 60000.00,
                'currency' => 'YER',
                'description' => 'توسيع المهارات الحوارية والكتابة الأكاديمية والنقاشات التفاعلية المباشرة.',
            ],
            5 => [
                'name_en' => 'ADVANCED 1',
                'name_ar' => 'المستوى المتقدم الأول',
                'name' => 'المستوى المتقدم الأول (ADVANCED 1)',
                'level_order' => 5,
                'fee_amount' => 70000.00,
                'currency' => 'YER',
                'description' => 'إتقان الطلاقة اللغوية ومناقشة الموضوعات المعقدة والتحليل النقدي باللغة الإنجليزية.',
            ],
            6 => [
                'name_en' => 'ADVANCED 2',
                'name_ar' => 'المستوى المتقدم الثاني',
                'name' => 'المستوى المتقدم الثاني (ADVANCED 2)',
                'level_order' => 6,
                'fee_amount' => 70000.00,
                'currency' => 'YER',
                'description' => 'الوصول إلى الاحترافية والطلاقة التامة بمستوى المتحدث الأصلي للغة الإنجليزية.',
            ],
            7 => [
                'name_en' => 'TOEFL / IELTS',
                'name_ar' => 'برنامج اختبارات التوفل والآيلتس الدولية',
                'name' => 'برنامج التوفل والآيلتس (TOEFL / IELTS)',
                'level_order' => 7,
                'fee_amount' => 100000.00,
                'currency' => 'YER',
                'description' => 'إعداد مكثف لاجتياز اختبارات TOEFL iBT و IELTS الدولية بأعلى الدرجات والسكور المطلوب للجامعات العالمية.',
            ],
            8 => [
                'name_en' => 'SPECIFIC PROGRAMS',
                'name_ar' => 'البرامج والمسارات المهنية التخصصية',
                'name' => 'البرامج التخصصية (SPECIFIC PROGRAMS)',
                'level_order' => 8,
                'fee_amount' => 80000.00,
                'currency' => 'YER',
                'description' => 'برامج تطبيقية متخصصة في الإنجليزية للأعمال، المراسلات، الترجمة، المصطلحات الطبية، مهارات الاتصال، وتدريب المعلمين.',
            ],
        ];

        // حفظ / تحديث المستويات
        $createdLevels = [];
        foreach ($levelsData as $order => $data) {
            $existing = Level::where('level_order', $order)
                ->orWhere('name_en', $data['name_en'])
                ->first();

            if ($existing) {
                $existing->update([
                    'name_en' => $data['name_en'],
                    'name_ar' => $data['name_ar'],
                    'name' => $data['name'],
                    'level_order' => $data['level_order'],
                    'fee_amount' => $data['fee_amount'],
                    'currency' => $data['currency'],
                    'description' => $data['description'],
                    'is_active' => true,
                ]);
                $createdLevels[$data['name_en']] = $existing;
            } else {
                $createdLevels[$data['name_en']] = Level::create(array_merge($data, ['is_active' => true]));
            }
        }

        // 2. تعريف الدورات لكل مستوى وفق الصورة المرفقة بالضبط مع الساعات والتسعيرات
        $coursesHierarchy = [
            'BEGINNERS 1' => [
                ['name_en' => 'Team Together 1', 'name_ar' => 'فريق معاً 1 (Team Together 1)', 'hours' => 40, 'fee' => 20000],
                ['name_en' => 'Team Together 2', 'name_ar' => 'فريق معاً 2 (Team Together 2)', 'hours' => 40, 'fee' => 20000],
                ['name_en' => 'Team Together 3', 'name_ar' => 'فريق معاً 3 (Team Together 3)', 'hours' => 40, 'fee' => 20000],
            ],
            'BEGINNERS 2' => [
                ['name_en' => 'Access A', 'name_ar' => 'دورة الوصول A (Access A)', 'hours' => 40, 'fee' => 20000],
                ['name_en' => 'Access B', 'name_ar' => 'دورة الوصول B (Access B)', 'hours' => 40, 'fee' => 20000],
                ['name_en' => 'Intro A', 'name_ar' => 'المقدمة A (Intro A)', 'hours' => 40, 'fee' => 20000],
            ],
            'INTERMEDIATE 1' => [
                ['name_en' => 'Intro B', 'name_ar' => 'المقدمة B (Intro B)', 'hours' => 40, 'fee' => 22000],
                ['name_en' => 'Basic C', 'name_ar' => 'الأساسيات C (Basic C)', 'hours' => 40, 'fee' => 22000],
                ['name_en' => 'Level 1A', 'name_ar' => 'المرحلة 1A (Level 1A)', 'hours' => 40, 'fee' => 22000],
                ['name_en' => 'Level 1B', 'name_ar' => 'المرحلة 1B (Level 1B)', 'hours' => 40, 'fee' => 22000],
            ],
            'INTERMEDIATE 2' => [
                ['name_en' => 'Level 1C', 'name_ar' => 'المرحلة 1C (Level 1C)', 'hours' => 40, 'fee' => 22000],
                ['name_en' => 'Level 2A', 'name_ar' => 'المرحلة 2A (Level 2A)', 'hours' => 40, 'fee' => 22000],
                ['name_en' => 'Level 2B', 'name_ar' => 'المرحلة 2B (Level 2B)', 'hours' => 40, 'fee' => 22000],
                ['name_en' => 'Level 2C', 'name_ar' => 'المرحلة 2C (Level 2C)', 'hours' => 40, 'fee' => 22000],
            ],
            'ADVANCED 1' => [
                ['name_en' => 'Level 3A', 'name_ar' => 'المرحلة 3A (Level 3A)', 'hours' => 40, 'fee' => 25000],
                ['name_en' => 'Level 3B', 'name_ar' => 'المرحلة 3B (Level 3B)', 'hours' => 40, 'fee' => 25000],
                ['name_en' => 'Level 3C', 'name_ar' => 'المرحلة 3C (Level 3C)', 'hours' => 40, 'fee' => 25000],
                ['name_en' => 'Level 4A', 'name_ar' => 'المرحلة 4A (Level 4A)', 'hours' => 40, 'fee' => 25000],
            ],
            'ADVANCED 2' => [
                ['name_en' => 'Level 4B', 'name_ar' => 'المرحلة 4B (Level 4B)', 'hours' => 40, 'fee' => 25000],
                ['name_en' => 'Level 5A', 'name_ar' => 'المرحلة 5A (Level 5A)', 'hours' => 40, 'fee' => 25000],
                ['name_en' => 'Level 5B', 'name_ar' => 'المرحلة 5B (Level 5B)', 'hours' => 40, 'fee' => 25000],
                ['name_en' => 'Level 6A', 'name_ar' => 'المرحلة 6A (Level 6A)', 'hours' => 40, 'fee' => 25000],
                ['name_en' => 'Level 6B', 'name_ar' => 'المرحلة 6B (Level 6B)', 'hours' => 40, 'fee' => 25000],
            ],
            'TOEFL / IELTS' => [
                ['name_en' => 'TOEFL / IELTS Preparation', 'name_ar' => 'التحضير لاختبارات التوفل والآيلتس الشاملة', 'hours' => 80, 'fee' => 100000],
            ],
            'SPECIFIC PROGRAMS' => [
                ['name_en' => 'Business English', 'name_ar' => 'اللغة الإنجليزية للأعمال (Business English)', 'hours' => 40, 'fee' => 40000],
                ['name_en' => 'Commercial Correspondence', 'name_ar' => 'المراسلات التجارية الاحترافية (Commercial Correspondence)', 'hours' => 40, 'fee' => 40000],
                ['name_en' => 'Arabic - English Translation Courses', 'name_ar' => 'دورات الترجمة التحريرية والفورية (عربي - إنجليزي)', 'hours' => 40, 'fee' => 40000],
                ['name_en' => 'Arabic for Non-Native Speakers', 'name_ar' => 'اللغة العربية لغير الناطقين بها', 'hours' => 40, 'fee' => 40000],
                ['name_en' => 'Artificial Intelligence for All', 'name_ar' => 'الذكاء الاصطناعي للجميع (AI for All)', 'hours' => 40, 'fee' => 40000],
                ['name_en' => 'Medical Terminology Courses', 'name_ar' => 'المصطلحات الطبية باللغة الإنجليزية (Medical Terminology)', 'hours' => 40, 'fee' => 40000],
                ['name_en' => 'Communication Skills', 'name_ar' => 'مهارات الاتصال والتواصل الفعال (Communication Skills)', 'hours' => 40, 'fee' => 40000],
                ['name_en' => 'TESOL Program', 'name_ar' => 'برنامج تدريب واعتماد معلمي اللغة الإنجليزية (TESOL)', 'hours' => 40, 'fee' => 50000],
            ],
        ];

        // معالجة الدورة الأولى رقم 1 الموجودة في النظام لترتبط بالـ BEGINNERS 1 كـ Team Together 1
        $firstCourse = Course::find(1);
        $orderCounter = 1;

        foreach ($coursesHierarchy as $lvlKey => $courses) {
            $level = $createdLevels[$lvlKey] ?? Level::where('name_en', $lvlKey)->first();
            if (!$level) continue;

            $courseOrder = 1;
            foreach ($courses as $c) {
                // هل توجد دورة مسجلة بهذا الاسم الإنجليزي؟
                $existingCourse = Course::where('name_en', $c['name_en'])
                    ->orWhere('name', $c['name_en'])
                    ->first();

                if (!$existingCourse && $firstCourse && $firstCourse->id === 1 && $orderCounter === 1) {
                    $existingCourse = $firstCourse;
                }

                $courseData = [
                    'level_id' => $level->id,
                    'name_en' => $c['name_en'],
                    'name_ar' => $c['name_ar'],
                    'name' => "{$c['name_en']} ({$c['name_ar']})",
                    'hours' => $c['hours'],
                    'fee_amount' => $c['fee'],
                    'currency' => 'YER',
                    'course_order' => $courseOrder++,
                    'is_active' => true,
                    'description' => "دورة معتمدة بواقع {$c['hours']} ساعة تدريبية تفاعلية مباشرة مع نخبة من المدربين المعتمدين.",
                ];

                if ($existingCourse) {
                    $existingCourse->update($courseData);
                } else {
                    Course::create($courseData);
                }

                $orderCounter++;
            }
        }

        // تحديث مجموعة الدراسة الأولى لتتبع المستوى والدورة الأولى
        DB::table('study_groups')->where('id', 1)->update([
            'course_id' => 1,
            'level_id' => $createdLevels['BEGINNERS 1']->id ?? 1,
        ]);
    }

    public function down(): void
    {
        // لا نحذف البيانات تلقائياً في التراجع
    }
};
