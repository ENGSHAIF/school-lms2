<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('role')->default('طالب في المنصة');
            $table->string('user_type')->nullable(); // student, parent, teacher, guest
            $table->string('avatar')->default('🎓');
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('score')->nullable(); // e.g. "حقق 98 في التوفل" or "تقييم 5 نجوم"
            $table->unsignedTinyInteger('rating')->default(5); // 1 - 5
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_featured')->default(true);
            $table->timestamps();
        });

        // بذر التجارب والمقالات التأسيسية المتنوعة للطلاب وأولياء الأمور والمعلمين
        $seedData = [
            [
                'name' => 'طالب تجريبي (مسار التوفل الدولي)',
                'role' => 'طالب دورة التوفل والتحضير للماجستير',
                'user_type' => 'student',
                'avatar' => '🎓',
                'title' => 'اجتياز التوفل الدولي بدرجة مشرفة',
                'score' => 'حقق 98 في TOEFL iBT 🏆',
                'rating' => 5,
                'content' => 'كنت أعاني من رهبة قسم المحادثة Speaking، لكن ميزة حصص الـ 25 دقيقة الفردية مع نظام فحص النطق بالذكاء الاصطناعي مكنتني من تصحيح أخطائي يومياً حتى حصلت على الدرجة المطلوبة في وقت قياسي.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'name' => 'ولي الأمر التجريبي (متابعة طفل)',
                'role' => 'ولي أمر لطفلين مسجلين بالمنصة',
                'user_type' => 'parent',
                'avatar' => '👨‍👧‍👦',
                'title' => 'تقارير الواتساب ومتابعة الأبناء دقيقة جداً',
                'score' => 'تقدم ملحوظ خلال 3 أسابيع 🌟',
                'rating' => 5,
                'content' => 'أكثر ما أعجبني هو تقرير الواتساب الفوري الذي يصلني بعد كل حصة بدقيقة واحدة! أعرف تماماً الكلمات التي نطقها طفلي وعدد النجوم التي جمعها، والمعلم متعاون ومشجع جداً وبوابة ولي الأمر تمكني من الاطلاع على كل شيء.',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'name' => 'طالب تجريبي (مسار إنجليزية الأعمال)',
                'role' => 'متدرب في برنامج المقابلات والوظائف',
                'user_type' => 'student',
                'avatar' => '💼',
                'title' => 'مرونة الحجز وسندات القبض المعتمدة',
                'score' => 'اجتياز مقابلة عمل دولية بنجاح 💼',
                'rating' => 5,
                'content' => 'الحصص الفردية 1-on-1 وفرت علي ساعات من التنقل المرهق. جدولتها في أوقاتي المناسبة وسندات القبض المعتمدة والأنظمة المحاسبية عززت من انضباط التدريب واحترافيته ومصداقية المدرسة.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'name' => 'أ. أحمد المعلم (مدرب لغات معتمد)',
                'role' => 'معلم لغة إنجليزية وأخصائي TEFL',
                'user_type' => 'teacher',
                'avatar' => '👨‍🏫',
                'title' => 'بيئة تعليمية ممتعة وسهولة في رصد التقييمات',
                'score' => 'تدريس أكثر من 400 حصة تفاعلية ⭐',
                'rating' => 5,
                'content' => 'المنصة وفّرت لنا كمعلمين كل الأدوات التفاعلية: السبورة الذكية، مشاركة كتب المستوى، وتوزيع النجوم للطلاب المحفزة. أرى حماس الطلاب وتطور طلاقتهم يوماً بعد يوم بشكل ملموس.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'name' => 'أم سارة (ولية أمر طالبة بالابتدائي)',
                'role' => 'ولية أمر طالبة في برنامج الصغار',
                'user_type' => 'parent',
                'avatar' => '👩‍👧',
                'title' => 'ابنتي أصبحت تتحدث الإنجليزية بكل ثقة',
                'score' => 'حصلت على 50 نجمة مكافأة 🌟',
                'rating' => 5,
                'content' => 'ابنتي كانت خجولة جداً وتتردد في التحدث بالإنجليزية أمام زملائها. بعد شهر من الدروس التفاعلية ورصيد النجوم المكافأة أصبحت تنتظر موعد الحصة بفارغ الصبر وتنطق الحروف بمخارج سليمة تماماً.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'name' => 'م. فهد الزهراني (طالب دبلوم الترجمة)',
                'role' => 'طالب بالدبلوم المتقدم للترجمة واللغات',
                'user_type' => 'student',
                'avatar' => '📜',
                'title' => 'السجل الأكاديمي الشامل 360 يثبت احترافية المنظومة',
                'score' => 'معدل امتياز 96% 🎯',
                'rating' => 5,
                'content' => 'ميزة السجل الأكاديمي الشامل 360 والكويزات الدورية مع التصحيح الفوري تعطي الطالب شعوراً بالأمان والوضوح في مساره الدراسي من أول يوم وحتى نيل شهادة التخرج.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('testimonials')->insert($seedData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
