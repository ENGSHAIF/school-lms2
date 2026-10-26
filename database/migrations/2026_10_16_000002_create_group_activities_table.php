<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول أنشطة وموارد وصوتيات المجموعة (Group Activities, Worksheets & Audio Notes)
        Schema::create('group_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_group_id')->constrained('study_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ناشر النشاط (أستاذ أو مدير)
            $table->string('title'); // عنوان النشاط (مثال: ورقة نشاط درس الأزمنة / تدريب استماع صوتي)
            $table->enum('type', ['worksheet', 'audio', 'video', 'announcement'])->default('worksheet');
            $table->text('instructions')->nullable(); // تعليمات وتوجيهات النشاط
            $table->string('file_path')->nullable(); // مسار الملف المرفق (PDF, MP3, Doc)
            $table->string('file_name')->nullable();
            $table->string('file_size')->nullable();
            $table->date('due_date')->nullable(); // موعد التسليم اختياري
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_activities');
    }
};
