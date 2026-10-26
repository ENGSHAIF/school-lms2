<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete(); // ربط الطالب بولي الأمر
            $table->enum('stage', [
                'kindergarten',   // طفل روضة
                'primary',        // ابتدائي
                'middle_school',  // متوسط / أساسي
                'high_school',    // ثانوي
                'university',     // جامعي
                'adult',          // بالغ / موظف
                'parent',         // ولي أمر يسجل لطفل
                'other'           // غير ذلك
            ])->default('adult');
            $table->string('grade_substage')->nullable(); // الصف الدراسي أو المرحلة الفرعية
            $table->json('learning_goals')->nullable(); // الأهداف المحددة (توفل، محادثة، أعمال...)
            $table->string('prior_english_exposure')->nullable(); // المعرفة السابقة باللغة
            $table->string('preferred_time')->nullable(); // الأوقات المفضلة (صباح، مساء، نهاية الأسبوع)
            $table->string('preferred_frequency')->nullable(); // تكرار الحصص (مرتان، 3 مرات، يومياً)
            $table->boolean('parent_consent')->default(false); // موافقة ولي الأمر للأطفال
            $table->boolean('is_trial_booked')->default(false); // هل حجز حصة تجريبية؟
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
