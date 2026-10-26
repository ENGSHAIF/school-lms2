<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // العنوان الرئيسي للبانر
            $table->string('title_en')->nullable(); // العنوان بالإنجليزية
            $table->text('subtitle')->nullable(); // النص الفرعي أو الوصف
            $table->string('icon')->default('⭐'); // الأيقونة التعبيرية
            $table->string('link_url')->default('/onboarding'); // الرابط المستهدف
            $table->string('button_text')->default('احجز حصتك الآن 🚀'); // نص الزر
            $table->enum('placement', [
                'top_announcement', // شريط الإعلانات العلوي
                'hero_slider',      // سلايدر الصفحة الرئيسية
                'popup_modal',      // نافذة منبثقة ترويجية
                'sidebar'           // شريط جانبي للبوابات
            ])->default('top_announcement');
            $table->string('theme_style')->default('amber'); // التدرج اللوني (amber, indigo, emerald, rose)
            $table->enum('device_target', ['all', 'desktop', 'mobile'])->default('all');
            $table->dateTime('starts_at')->nullable(); // تاريخ بدء العرض
            $table->dateTime('ends_at')->nullable();   // تاريخ انتهاء العرض
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true); // مفعل / معطل
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
