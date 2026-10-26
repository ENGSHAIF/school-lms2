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
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('level_id')->constrained('courses')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->after('course_id')->constrained('users')->nullOnDelete();
            $table->string('quiz_period', 50)->default('weekly')->after('type'); // weekly, midterm, final, pop_quiz, placement
            $table->string('term', 100)->nullable()->after('quiz_period');
            $table->integer('random_questions_count')->nullable()->after('passing_score'); // عدد الأسئلة العشوائية المعروضة للطالب من البنك
            $table->boolean('shuffle_questions')->default(true)->after('random_questions_count'); // خلط ترتيب الأسئلة
            $table->boolean('shuffle_options')->default(true)->after('shuffle_questions'); // خلط ترتيب خيارات الإجابة
            $table->boolean('show_result_immediately')->default(false)->after('shuffle_options'); // إظهار النتيجة فوراً أم حجبها للمراجعة
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->foreignId('study_group_id')->nullable()->after('exam_id')->constrained('study_groups')->nullOnDelete();
            $table->boolean('is_published')->default(false)->after('status'); // اعتماد وإعلان النتيجة للطالب
            $table->foreignId('reviewed_by')->nullable()->after('is_published')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('teacher_notes')->nullable()->after('reviewed_at');
            $table->json('questions_order')->nullable()->after('teacher_notes'); // ترتيب الأسئلة العشوائية المسندة للمحاولة
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropForeign(['study_group_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['study_group_id', 'is_published', 'reviewed_by', 'reviewed_at', 'teacher_notes', 'questions_order']);
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'course_id',
                'created_by',
                'quiz_period',
                'term',
                'random_questions_count',
                'shuffle_questions',
                'shuffle_options',
                'show_result_immediately'
            ]);
        });
    }
};
