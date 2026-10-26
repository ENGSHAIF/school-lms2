<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. جدول الكورسات والمسارات (مثل: دورات اللغة الإنجليزية العامة، دورات التوفل)
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الكورس
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. جدول المستويات والمراحل (مثل: Level 1, Level 2, TOEFL Preparation)
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('name'); // اسم المستوى
            $table->integer('level_order')->default(1); // ترتيب المستوى
            $table->decimal('fee_amount', 12, 2)->default(0.00); // رسوم المستوى
            $table->string('currency', 10)->default('YER'); // العملة الافتراضية (YER, SAR, USD)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. جدول تسجيل وتسكين الطلاب في المستويات (Enrollments)
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->enum('status', ['active', 'completed', 'suspended'])->default('active');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 4. جدول المحاضرات والدروس المسجلة (Lessons)
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable(); // رابط الفيديو (محمي أو من Bunny/YouTube)
            $table->integer('duration_minutes')->default(0);
            $table->integer('order_index')->default(1);
            $table->timestamps();
        });

        // 5. جدول المكتبة الرقمية والكتب والمذكرات (Books & Materials)
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path'); // مسار ملف PDF
            $table->string('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('student_enrollments');
        Schema::dropIfExists('levels');
        Schema::dropIfExists('courses');
    }
};
