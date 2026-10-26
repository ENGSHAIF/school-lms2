<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. باقات الحصص ونظام الرصيد (ASOTS Packages)
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الباقة (مثلاً: باقة المحادثة 24 حصة)
            $table->text('description')->nullable();
            $table->integer('lessons_count')->default(12); // عدد الحصص (جلسات 25 دقيقة)
            $table->decimal('price', 12, 2); // السعر
            $table->string('currency', 10)->default('YER');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. محفظة رصيد الحصص للطالب (Student Wallet)
        Schema::create('student_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('total_lessons')->default(0); // إجمالي الحصص المشتراة
            $table->integer('used_lessons')->default(0); // الحصص المستهلكة
            $table->integer('remaining_lessons')->default(0); // الحصص المتبقية
            $table->timestamps();
        });

        // 3. جدول خانات تفرغ المعلمين (Teacher Availability Slots - 25 mins)
        Schema::create('teacher_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->date('session_date'); // تاريخ اليوم
            $table->time('start_time'); // وقت البدء (مثلاً 17:00)
            $table->time('end_time'); // وقت الانتهاء (17:25)
            $table->boolean('is_booked')->default(false); // هل حُجزت من طالب؟
            $table->timestamps();
        });

        // 4. جدول حجوزات الحصص الحية (1-on-1 Bookings)
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained('teacher_schedules')->onDelete('cascade');
            $table->string('room_id')->unique(); // معرف غرفة الفصل الافتراضي (WebRTC)
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->string('topic')->default('محادثة حية وتطوير النطق والتوفل');
            $table->timestamps();
        });

        // 5. تقرير تقييم الحصة الفوري (Post-Class Evaluation Report)
        Schema::create('lesson_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('pronunciation_score')->default(5); // تقييم النطق من 5
            $table->integer('fluency_score')->default(5); // تقييم الطلاقة من 5
            $table->integer('grammar_score')->default(5); // تقييم القواعد من 5
            $table->integer('stars_awarded')->default(3); // عدد النجوم التشجيعية
            $table->text('teacher_comment'); // ملاحظات المعلم وتوجيهاته
            $table->text('vocabulary_learned')->nullable(); // الكلمات الجديدة التي تعلمها
            $table->text('homework')->nullable(); // الواجب الصوتي المنزلي
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_reports');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('teacher_schedules');
        Schema::dropIfExists('student_wallets');
        Schema::dropIfExists('packages');
    }
};
