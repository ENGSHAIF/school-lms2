<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إضافة رصيد النجوم التراكمي للمستخدمين (إن لم يكن موجوداً)
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'stars_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('stars_balance')->default(0)->after('status');
            });
        }

        // 2. جدول الجلسات والحصص اليومية للمجموعات (Group Sessions & Daily Class Logs)
        Schema::create('group_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_group_id')->constrained('study_groups')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('session_date');
            $table->unsignedInteger('session_number')->default(1); // رقم الحصة / اللقاء
            $table->string('lesson_topic')->nullable(); // موضوع الحصة أو عنوان الدرس
            $table->text('lesson_summary')->nullable(); // ملخص محتوى الدرس والشرح
            $table->text('homework_assigned')->nullable(); // الواجب أو التكليف المطلوب
            $table->unsignedInteger('total_students')->default(0);
            $table->unsignedInteger('present_count')->default(0);
            $table->unsignedInteger('absent_count')->default(0);
            $table->unsignedInteger('late_count')->default(0);
            $table->unsignedInteger('excused_count')->default(0);
            $table->decimal('attendance_rate', 5, 2)->default(100.00); // نسبة الحضور المئوية
            $table->text('general_notes')->nullable();
            $table->timestamps();

            $table->index(['study_group_id', 'session_date']);
        });

        // 3. جدول تقييم وحضور كل طالب في الجلسة اليومية (Student Daily Evaluations & Scores)
        Schema::create('group_student_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_session_id')->constrained('group_sessions')->onDelete('cascade');
            $table->foreignId('study_group_id')->constrained('study_groups')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->enum('attendance_status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->unsignedTinyInteger('participation_score')->default(5); // تقييم المشاركة (من 5)
            $table->unsignedTinyInteger('understanding_score')->default(5); // الاستيعاب والفهم (من 5)
            $table->unsignedTinyInteger('homework_score')->default(5); // الواجب المنزلي (من 5)
            $table->boolean('spoke_voice')->default(false); // شارك صوتياً بالمايكروفون
            $table->boolean('wrote_chat')->default(false); // شارك كتابياً بالشات
            $table->boolean('submitted_task')->default(false); // سلم ورقة عمل أو نشاط
            $table->unsignedInteger('stars_awarded')->default(0); // عدد النجوم والمكافآت الممنوحة اليوم
            $table->decimal('daily_total_score', 5, 2)->default(100.00); // الدرجة اليومية المحسوبة
            $table->text('teacher_notes')->nullable(); // ملاحظات المعلم الفردية للطالب
            $table->timestamps();

            $table->unique(['group_session_id', 'student_id'], 'unique_session_student_eval');
            $table->index(['study_group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_student_evaluations');
        Schema::dropIfExists('group_sessions');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'stars_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('stars_balance');
            });
        }
    }
};
