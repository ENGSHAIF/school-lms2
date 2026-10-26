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
        // 1. إضافة حقل سند مركب/ماستر في جدول السندات
        Schema::table('vouchers', function (Blueprint $table) {
            $table->boolean('is_compound')->default(false)->after('voucher_type'); // سند مركب ماستر وديتيل
        });

        // 2. جدول أسطر الديتيل للسندات المركبة (Master-Detail Voucher Items)
        Schema::create('voucher_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('financial_accounts')->onDelete('cascade'); // حساب الطالب أو المعلم في هذا السطر
            $table->decimal('amount', 14, 2); // المبلغ الخاص بهذا السطر
            $table->string('statement')->nullable(); // البيان والشرح الخاص بهذا السطر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_items');
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('is_compound');
        });
    }
};
