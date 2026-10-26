<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول المجموعات والدفعات الدراسية (Study Groups & Batches)
        Schema::create('study_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المجموعة (مثال: مجموعة المبتدئين A - مسائي)
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete(); // الأستاذ الرئيسي
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete(); // المشرف الأكاديمي
            $table->integer('max_students')->default(15); // الحد الأقصى للطلاب
            $table->string('schedule_days')->nullable(); // أيام الحصص (مثال: السبت، الإثنين، الأربعاء)
            $table->string('schedule_time')->nullable(); // توقيت الحصص (مثال: 05:00 م - 06:00 م)
            $table->string('room_id')->unique(); // رمز الفصل التفاعلي المباشر
            $table->enum('status', ['active', 'completed', 'archived'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. جدول تسكين وقيد الطلاب داخل المجموعات (Group Students)
        Schema::create('study_group_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_group_id')->constrained('study_groups')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'transferred', 'cancelled'])->default('active');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['study_group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_group_students');
        Schema::dropIfExists('study_groups');
    }
};
