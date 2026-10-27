<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('platform_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان الترخيص / الوثيقة
            $table->string('license_number')->nullable(); // رقم الترخيص أو السجل
            $table->string('issuing_authority')->nullable(); // الجهة المصدرة للترخيص
            $table->string('image_path'); // مسار الصورة المحمية
            $table->date('issue_date')->nullable(); // تاريخ الإصدار
            $table->date('expiry_date')->nullable(); // تاريخ الانتهاء
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->string('status')->default('active'); // active, hidden
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_licenses');
    }
};
