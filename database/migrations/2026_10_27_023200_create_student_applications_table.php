<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); // الاسم الكامل بالعربية
            $table->string('name_en')->nullable(); // الاسم الكامل بالإنجليزية (مطابق للجواز)
            $table->string('birth_place')->nullable(); // مكان الميلاد
            $table->date('birth_date')->nullable(); // تاريخ الميلاد
            $table->string('educational_level')->nullable(); // المستوى التعليمي للطالب
            $table->string('study_phone_whatsapp'); // رقم الهاتف المخصص للدراسة (واتساب)
            $table->string('parent_phone')->nullable(); // رقم ولي الأمر
            $table->boolean('needs_placement_test')->default(true); // هل تحتاج لاختبار تحديد مستوى؟ (نعم / لا)
            $table->string('course_interest')->nullable(); // البرنامج أو الدورة المرغوبة
            $table->text('notes')->nullable(); // ملاحظات المتقدم

            // بيانات خاصة بالإدارة (يُعبئها القسم المختص)
            $table->string('term')->default('Term 40'); // الترم الدراسي
            $table->string('academic_number')->nullable(); // الرقم الأكاديمي
            $table->string('course_assigned')->nullable(); // الدورة المقررة للطالب
            $table->string('program_duration')->default('شهر واحد — 5 أيام أسبوعياً (السبت إلى الأربعاء)'); // مدة البرنامج

            // حالة الاستمارة والمتابعة
            $table->enum('status', ['pending', 'contacted', 'approved', 'rejected'])->default('pending');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_applications');
    }
};
