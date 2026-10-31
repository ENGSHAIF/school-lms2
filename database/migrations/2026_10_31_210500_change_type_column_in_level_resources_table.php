<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('level_resources')) {
            // تحويل عمود type إلى VARCHAR(50) بدلاً من ENUM لقبول أي نوع من الموارد (مثل: image, video, audio, book, worksheet, quiz, doc)
            DB::statement("ALTER TABLE level_resources MODIFY type VARCHAR(50) NOT NULL DEFAULT 'book'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('level_resources')) {
            DB::statement("ALTER TABLE level_resources MODIFY type ENUM('book', 'audio', 'video', 'worksheet', 'quiz') NOT NULL DEFAULT 'book'");
        }
    }
};
