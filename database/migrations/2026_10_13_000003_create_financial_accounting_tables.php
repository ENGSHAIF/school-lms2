<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. شجرة الحسابات المالية (حسابات الطلاب، المدرسين، الصندوق الرئيسي، البنك) - المعايير المحاسبية المعتمدة
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code')->unique(); // رقم الحساب المالي (مثل 1101 لصندوق النقد، 210001 لحساب طالب)
            $table->string('account_name'); // اسم الحساب
            $table->enum('account_type', ['student', 'teacher', 'cash_box', 'bank', 'revenue', 'expense']);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // ارتباط بالطالب أو المعلم
            $table->string('currency', 10)->default('YER'); // العملة الافتراضية (YER, SAR, USD)
            $table->decimal('opening_balance', 14, 2)->default(0.00); // الرصيد الافتتاحي
            $table->decimal('current_balance', 14, 2)->default(0.00); // الرصيد الحالي
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. جدول سندات القبض وسندات الصرف (Vouchers)
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->unique(); // رقم السند الآلي (REC-2026-0001 أو PAY-2026-0001)
            $table->string('manual_no')->nullable(); // الرقم الدفتري اليدوي (مطابق للأنظمة المحاسبية القياسية)
            $table->enum('voucher_type', ['receipt', 'payment']); // receipt = قبض, payment = صرف
            $table->foreignId('account_id')->constrained('financial_accounts'); // الحساب المالي للطرف الثاني (الطالب/المعلم)
            $table->foreignId('cash_or_bank_account_id')->constrained('financial_accounts'); // حساب الصندوق أو البنك المستلم/الصارف
            $table->decimal('amount', 14, 2); // المبلغ الأصلي
            $table->string('currency', 10)->default('YER'); // عملة السند الافتراضية
            $table->decimal('exchange_rate', 10, 4)->default(1.0000); // سعر الصرف
            $table->decimal('amount_local', 14, 2); // المبلغ بالعملة المحلية
            $table->string('amount_in_words')->nullable(); // تفقيط المبلغ كتابةً بالعربية
            $table->enum('payment_method', ['cash', 'bank_transfer', 'cheque', 'electronic_wallet'])->default('cash');
            $table->text('statement'); // البيان والسبب
            $table->string('pdf_path')->nullable(); // مسار ملف السند الرسمي PDF
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // المحاسب المدخل
            $table->timestamps();
        });

        // 3. جدول الأقساط الدراسية المجدولة للطلاب (Installments)
        Schema::create('student_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('level_id')->constrained('levels')->onDelete('cascade');
            $table->integer('installment_number')->default(1);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('YER');
            $table->date('due_date'); // تاريخ الاستحقاق
            $table->decimal('paid_amount', 12, 2)->default(0.00); // المدفوع حتى الآن
            $table->enum('status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid');
            $table->timestamps();
        });

        // 4. عقود واحتساب مستحقات المدرسين (Teacher Contracts)
        Schema::create('teacher_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->enum('calculation_type', ['percentage', 'per_hour', 'fixed_monthly']); // نسبة %، بالساعة، راتب ثابت
            $table->decimal('rate', 10, 2); // قيمة النسبة أو الأجر أو الراتب
            $table->string('currency', 10)->default('USD');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. سجل رسائل وإشعارات الواتساب وسندات القبض (WhatsApp Logs)
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('phone_number');
            $table->enum('message_type', ['receipt_voucher', 'exam_result', 'installment_reminder', 'announcement']);
            $table->text('message_body');
            $table->string('attachment_url')->nullable(); // رابط السند أو الشهادة PDF
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->text('api_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
        Schema::dropIfExists('teacher_contracts');
        Schema::dropIfExists('student_installments');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('financial_accounts');
    }
};
