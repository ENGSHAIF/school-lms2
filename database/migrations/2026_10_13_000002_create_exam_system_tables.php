<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول الامتحانات (الاختبارات، تحديد المستوى، ونماذج التوفل)
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->string('title'); // عنوان الاختبار
            $table->text('description')->nullable();
            $table->enum('type', ['placement_test', 'quiz', 'midterm', 'final', 'toefl_mock'])->default('quiz');
            $table->integer('duration_minutes')->default(45); // مدة الاختبار بالدقائق
            $table->decimal('total_points', 8, 2)->default(100.00); // الدرجة الكلية
            $table->decimal('passing_score', 8, 2)->default(60.00); // درجة النجاح
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. بنك الأسئلة (يدعم أسئلة الاختيارات، الاستماع، والقراءة، والتحدث بالذكاء الاصطناعي)
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->enum('type', ['mcq', 'listening', 'reading', 'speaking_ai'])->default('mcq');
            $table->text('question_text'); // نص السؤال أو التعليمات
            $table->text('context_passage')->nullable(); // قطعة القراءة (إن وجدت)
            $table->string('audio_url')->nullable(); // ملف الصوت لأسئلة الاستماع
            $table->json('options')->nullable(); // خيارات الإجابة بصيغة JSON [A, B, C, D]
            $table->string('correct_answer')->nullable(); // الإجابة الصحيحة للاختيارات
            $table->text('expected_speech_text')->nullable(); // النص المتوقع نطقه للطالب لفحص الصوت
            $table->decimal('points', 6, 2)->default(1.00); // درجة السؤال
            $table->integer('order_index')->default(1);
            $table->timestamps();
        });

        // 3. جدول محاولات ونتائج الطلاب (Attempts)
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->decimal('total_score', 8, 2)->default(0.00);
            $table->decimal('speech_score', 8, 2)->nullable(); // درجة فحص الصوت والنطق
            $table->json('speech_feedback')->nullable(); // تقرير مخارج الحروف من بايثون
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'reviewed'])->default('in_progress');
            $table->foreignId('suggested_level_id')->nullable()->constrained('levels')->nullOnDelete(); // المستوى المقترح بعد تحديد المستوى
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        // 4. جدول إجابات الطالب التفصيلية
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('exam_questions')->onDelete('cascade');
            $table->text('student_answer')->nullable();
            $table->string('recorded_audio_path')->nullable(); // مسار التسجيل الصوتي للطالب
            $table->decimal('speech_accuracy', 5, 2)->nullable(); // نسبة دقة النطق %
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_awarded', 6, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
    }
};
