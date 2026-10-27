<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_surveys', function (Blueprint $table) {
            $table->id();
            $table->enum('survey_type', ['daily', 'weekly', 'diploma_final'])->default('daily');
            $table->date('survey_date');

            // بيانات الطالب والمشارك
            $table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('student_name')->nullable();
            $table->string('student_phone')->nullable();
            $table->boolean('is_anonymous')->default(false);

            // الارتباط الأكاديمي (المعلم، المجموعة، الدبلوم/الكورس)
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('study_group_id')->nullable()->constrained('study_groups')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();

            // محور المدرّس (1 إلى 5 نجوم)
            $table->decimal('teacher_rating', 3, 2)->nullable(); // متوسط تقييم المعلم
            $table->unsignedTinyInteger('teacher_clarity')->nullable(); // وضوح الشرح (1-5)
            $table->unsignedTinyInteger('teacher_competence')->nullable(); // الكفاءة العلمية (1-5)
            $table->unsignedTinyInteger('teacher_interaction')->nullable(); // أسلوب التدريس والتفاعل والتحفيز (1-5)
            $table->unsignedTinyInteger('teacher_punctuality')->nullable(); // الالتزام بالوقت / التعامل (1-5)

            // محور المنهج (1 إلى 5)
            $table->decimal('curriculum_rating', 3, 2)->nullable();
            $table->unsignedTinyInteger('curriculum_suitability')->nullable(); // ملاءمة ومناسبة المنهج لمستوى الطالب (1-5)
            $table->unsignedTinyInteger('curriculum_alignment')->nullable(); // توافق المحتوى مع أهداف الدورة (1-5)

            // محور الإدارة والدعم ونظام التعلم عن بعد (1 إلى 5)
            $table->decimal('support_rating', 3, 2)->nullable();
            $table->unsignedTinyInteger('admin_communication')->nullable(); // جودة تواصل الإدارة والدعم (1-5)
            $table->unsignedTinyInteger('management_organization')->nullable(); // التنظيم والمتابعة الأكاديمية (1-5)
            $table->unsignedTinyInteger('remote_learning_ease')->nullable(); // سهولة نظام التعلم عن بعد (1-5)
            $table->text('remote_learning_difficulties')->nullable(); // صعوبات في نظام التعلم عن بعد إن وجدت

            // أسئلة خاصة بالتقييم اليومي السريع
            $table->string('daily_benefited')->nullable(); // هل استفدت من الحصة؟ (yes, partial, no)
            $table->string('daily_level_appropriate')->nullable(); // هل كانت المادة مناسبة لمستواك؟ (yes, no)

            // محور التحديات (تحدث، استماع، قراءة، كتابة، قواعد)
            $table->json('learning_challenges')->nullable();

            // الانطباع العام والتوصية والملاحظات
            $table->decimal('overall_score', 3, 2)->nullable(); // النتيجة الإجمالية الموزونة للاستبيان (1-5)
            $table->string('recommend_institute')->nullable(); // هل توصي بـ ASOTS للآخرين؟ (yes, maybe, no)
            $table->text('recommend_reason')->nullable(); // ولماذا؟
            $table->text('best_feature')->nullable(); // أبرز ميزة لاحظتها في ASOTS
            $table->text('notes_suggestions')->nullable(); // ملاحظات ومقترحات لتحسين الحصة/البرنامج
            $table->json('raw_answers')->nullable(); // تخزين الإجابات الخام بصيغة مرنة

            $table->enum('status', ['submitted', 'reviewed'])->default('submitted');
            $table->timestamps();

            // الفهارس لتسريع استعلامات التقارير والفلاتر
            $table->index(['survey_type', 'survey_date']);
            $table->index(['teacher_id', 'survey_date']);
            $table->index(['study_group_id', 'survey_date']);
            $table->index(['course_id', 'survey_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_surveys');
    }
};
