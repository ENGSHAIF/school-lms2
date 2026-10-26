<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول سلّم وتوزيع درجات الدورة / المجموعة / الدبلوم (Course & Group Grade Scales)
        if (!Schema::hasTable('course_grade_scales')) {
            Schema::create('course_grade_scales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->nullable()->constrained('courses')->cascadeOnDelete();
                $table->foreignId('study_group_id')->nullable()->constrained('study_groups')->cascadeOnDelete();
                $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
                $table->string('name')->default('السلم القياسي للدرجات (100 درجة)');
                $table->decimal('attendance_max', 5, 2)->default(10.00); // حضور ومواظبة
                $table->decimal('participation_max', 5, 2)->default(10.00); // مشاركة وتفاعل
                $table->decimal('activities_max', 5, 2)->default(15.00); // واجبات وأنشطة
                $table->decimal('midterm_max', 5, 2)->default(20.00); // اختبار نصفي
                $table->decimal('practical_max', 5, 2)->default(15.00); // عملي وشفهي وتحدث
                $table->decimal('final_exam_max', 5, 2)->default(30.00); // اختبار نهائي
                $table->decimal('total_max', 5, 2)->default(100.00); // الإجمالي 100
                $table->decimal('passing_percentage', 5, 2)->default(60.00); // درجة النجاح 60%
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 2. جدول رصد وتعديل درجات الطلاب بالدورة والمجموعة (Student Course Grades)
        if (!Schema::hasTable('student_course_grades')) {
            Schema::create('student_course_grades', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('study_group_id')->constrained('study_groups')->cascadeOnDelete();
                $table->foreignId('course_id')->nullable()->constrained('courses')->cascadeOnDelete();
                $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
                $table->decimal('attendance_score', 5, 2)->default(0.00);
                $table->decimal('participation_score', 5, 2)->default(0.00);
                $table->decimal('activities_score', 5, 2)->default(0.00);
                $table->decimal('midterm_score', 5, 2)->default(0.00);
                $table->decimal('practical_score', 5, 2)->default(0.00);
                $table->decimal('final_exam_score', 5, 2)->default(0.00);
                $table->decimal('total_score', 5, 2)->default(0.00); // المجموع الكلي
                $table->decimal('percentage', 5, 2)->default(0.00); // النسبة المئوية
                $table->string('grade_letter', 20)->default('راسب'); // ممتاز، جيد جداً، جيد، مقبول، راسب
                $table->enum('status', ['in_progress', 'passed', 'failed', 'incomplete'])->default('in_progress');
                $table->text('teacher_notes')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_finalized')->default(false);
                $table->timestamps();

                $table->unique(['student_id', 'study_group_id'], 'student_group_grade_unique');
            });
        }

        // 3. جدول الشهادات المعتمدة الصادرة (Certificates System)
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->string('certificate_number')->unique(); // مثال: ASOTS-2026-0001
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('study_group_id')->nullable()->constrained('study_groups')->nullOnDelete();
                $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
                $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
                $table->enum('type', ['course', 'level', 'diploma'])->default('course');
                $table->string('title'); // مثال: شهادة إتمام دورة Team Together 1
                $table->string('student_name_ar');
                $table->string('student_name_en')->nullable();
                $table->string('course_name_ar')->nullable();
                $table->string('course_name_en')->nullable();
                $table->string('level_name_en')->nullable();
                $table->integer('hours')->default(40);
                $table->decimal('total_score', 5, 2)->default(0.00);
                $table->decimal('percentage', 5, 2)->default(0.00);
                $table->string('grade_letter', 20)->default('ممتاز');
                $table->date('issue_date');
                $table->string('qr_code_hash')->unique();
                $table->boolean('is_verified')->default(true);
                $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 4. تعزيز جدول الاختبارات exams لدعم الإسناد للمجموعات والطلاب والدورات
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'study_group_id')) {
                $table->foreignId('study_group_id')->nullable()->after('course_id')->constrained('study_groups')->nullOnDelete();
            }
            if (!Schema::hasColumn('exams', 'target_type')) {
                $table->string('target_type', 30)->default('general')->after('study_group_id'); // general, level, course, group, students
            }
            if (!Schema::hasColumn('exams', 'assigned_student_ids')) {
                $table->json('assigned_student_ids')->nullable()->after('target_type');
            }
            if (!Schema::hasColumn('exams', 'weight_category')) {
                $table->string('weight_category', 30)->default('quiz')->after('assigned_student_ids'); // quiz, midterm, final, practical, activity
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('exams', 'study_group_id')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropForeign(['study_group_id']);
                $table->dropColumn(['study_group_id', 'target_type', 'assigned_student_ids', 'weight_category']);
            });
        }
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('student_course_grades');
        Schema::dropIfExists('course_grade_scales');
    }
};
