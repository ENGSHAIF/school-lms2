<?php

use App\Models\Course;
use App\Models\Level;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إنشاء / تحديث المستوى والمسار الأكاديمي: دورات تكوينية في مجال اللغة العربية للناطقين بغيرها
        $level = Level::where('name_ar', 'LIKE', '%اللغة العربية%')
            ->orWhere('name_en', 'LIKE', '%Arabic%Non-Native%')
            ->first();

        $levelOrder = (Level::max('level_order') ?? 8) + 1;

        if ($level) {
            $level->update([
                'name_ar' => 'دورات تكوينية في مجال اللغة العربية للناطقين بغيرها',
                'name_en' => 'Arabic for Non-Native Speakers',
                'name' => 'دورات تكوينية في مجال اللغة العربية للناطقين بغيرها (Arabic for Non-Native Speakers)',
                'description' => 'مسار وبرامج تعليم وتأهيل لغوي مكثفة في مهارات وفنون اللغة العربية (المحادثة، القراءة، الكتابة، وفقه اللغة) للناطقين بغيرها، وفق أحدث المعايير الأكاديمية الدولية وإشراف لغوي متخصص.',
                'fee_amount' => $level->fee_amount > 0 ? $level->fee_amount : 60000.00,
                'currency' => $level->currency ?: 'YER',
                'is_active' => true,
            ]);
        } else {
            $level = Level::create([
                'name_ar' => 'دورات تكوينية في مجال اللغة العربية للناطقين بغيرها',
                'name_en' => 'Arabic for Non-Native Speakers',
                'name' => 'دورات تكوينية في مجال اللغة العربية للناطقين بغيرها (Arabic for Non-Native Speakers)',
                'description' => 'مسار وبرامج تعليم وتأهيل لغوي مكثفة في مهارات وفنون اللغة العربية (المحادثة، القراءة، الكتابة، وفقه اللغة) للناطقين بغيرها، وفق أحدث المعايير الأكاديمية الدولية وإشراف لغوي متخصص.',
                'level_order' => $levelOrder,
                'fee_amount' => 60000.00,
                'currency' => 'YER',
                'is_active' => true,
            ]);
        }

        // 2. ربط أو إنشاء الدورات التكوينية المتخصصة ضمن هذا المسار الأكاديمي
        $coursesData = [
            [
                'name_en' => 'Introductory Level: Arabic Phonetics & Alphabet',
                'name_ar' => 'المستوى التمهيدي: أصوات وحروف وتأسيس النطق',
                'hours' => 40,
                'fee' => 25000.00,
                'currency' => 'YER',
                'desc' => 'تأسيس شامل في مخارج الحروف العربية، الأصوات، القراءة الأولية، وبناء الكلمات.',
            ],
            [
                'name_en' => 'Conversational & Spoken Arabic',
                'name_ar' => 'المحادثة والتعبير والتواصل الشفوي',
                'hours' => 40,
                'fee' => 30000.00,
                'currency' => 'YER',
                'desc' => 'تنمية الطلاقة الشفوية والتواصل اليومي والمحادثة التفاعلية للمواقف الحياتية.',
            ],
            [
                'name_en' => 'Reading Comprehension & Text Analysis',
                'name_ar' => 'مهارات القراءة وفهم النصوص واستيعابها',
                'hours' => 40,
                'fee' => 30000.00,
                'currency' => 'YER',
                'desc' => 'قراءة نصوص متدرجة، فهم التراكيب اللغوية، وتنمية المعجم والمفردات العربية.',
            ],
            [
                'name_en' => 'Functional Arabic Writing & Grammar',
                'name_ar' => 'الكتابة والتعبير الإنشائي وقواعد النحو الوظيفي',
                'hours' => 40,
                'fee' => 30000.00,
                'currency' => 'YER',
                'desc' => 'قواعد النحو العربي التطبيقي الوظيفي، صياغة الجمل، والإنشاء التعبيري السليم.',
            ],
            [
                'name_en' => 'Arabic for Business & Diplomacy',
                'name_ar' => 'اللغة العربية للأغراض الدبلوماسية والتجارية',
                'hours' => 40,
                'fee' => 40000.00,
                'currency' => 'YER',
                'desc' => 'برنامج تخصصي متقدم للمراسلات الرسمية، المصطلحات الدبلوماسية، وبيئات الأعمال.',
            ],
        ];

        // نقل أي دورة سابقة خاصة بالعربية للناطقين بغيرها إلى هذا المستوى
        Course::where('name_en', 'LIKE', '%Arabic for Non-Native%')
            ->orWhere('name_ar', 'LIKE', '%اللغة العربية لغير الناطقين%')
            ->update(['level_id' => $level->id]);

        $order = (Course::where('level_id', $level->id)->max('course_order') ?? 0);

        foreach ($coursesData as $c) {
            $existing = Course::where('level_id', $level->id)
                ->where(function ($q) use ($c) {
                    $q->where('name_en', $c['name_en'])
                        ->orWhere('name_ar', $c['name_ar']);
                })->first();

            if (! $existing) {
                $order++;
                Course::create([
                    'level_id' => $level->id,
                    'name_en' => $c['name_en'],
                    'name_ar' => $c['name_ar'],
                    'name' => "{$c['name_ar']} ({$c['name_en']})",
                    'description' => $c['desc'],
                    'hours' => $c['hours'],
                    'fee_amount' => $c['fee'],
                    'currency' => $c['currency'],
                    'course_order' => $order,
                    'is_active' => true,
                ]);
            }
        }
    }

    public function down(): void
    {
        $level = Level::where('name_ar', 'دورات تكوينية في مجال اللغة العربية للناطقين بغيرها')->first();
        if ($level) {
            Course::where('level_id', $level->id)->delete();
            $level->delete();
        }
    }
};
