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
        // 1. ترقية جدول الحسابات المالية ليصبح دليلاً محاسبياً شجرياً متكاملاً (Enterprise Chart of Accounts)
        Schema::table('financial_accounts', function (Blueprint $table) {
            $table->unsignedTinyInteger('level')->default(1)->after('parent_id'); // مستوى الحساب في الشجرة (1 إلى 5)
            $table->boolean('is_parent')->default(false)->after('level'); // هل الحساب تجميعي رئيسي (لا يقبل قيود مباشرة)
            $table->enum('nature', ['debit', 'credit'])->default('debit')->after('account_type'); // طبيعة الحساب (مدين أو دائن)
            $table->enum('account_category', ['asset', 'liability', 'equity', 'revenue', 'expense'])->default('asset')->after('nature'); // التصنيف الرئيسي
            $table->boolean('allow_cost_center')->default(false)->after('is_active'); // هل يسمح أو يلزم مركز تكلفة
        });

        // 2. إنشاء جدول شجرة مراكز التكلفة (Cost Centers)
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->string('code', 30)->unique(); // كود مركز التكلفة (مثال: 101, 102)
            $table->string('name', 255); // اسم مركز التكلفة (قسم اللغات، دبلوم الإنجليزية...)
            $table->unsignedTinyInteger('level')->default(1);
            $table->boolean('is_parent')->default(false);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. إنشاء جدول دورة طلبات الصرف والقبض والاعتمادات (Payment & Receipt Requests Workflow)
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no', 30)->unique(); // PR-2026-0001 أو RR-2026-0001
            $table->enum('request_type', ['payment', 'receipt'])->default('payment'); // طلب صرف أم طلب توريد/قبض
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete(); // الموظف أو المعلم مقدم الطلب
            $table->string('beneficiary_name', 255); // اسم المستفيد / الجهه
            $table->foreignId('account_id')->nullable()->constrained('financial_accounts')->nullOnDelete(); // الحساب المالي
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete(); // مركز التكلفة
            $table->decimal('amount', 14, 2); // المبلغ المطلوب
            $table->string('currency', 10)->default('YER');
            $table->text('purpose'); // بيان ومبررات الطلب
            $table->string('attachment_path')->nullable(); // ملف فاتورة أو إيصال مرفق
            $table->enum('status', [
                'draft',              // مسودة
                'pending_financial',  // بانتظار المراجعة المالية
                'pending_manager',    // بانتظار اعتماد المشرف / المدير العام
                'approved',           // معتمد وجاهز للتنفيذ والصرف
                'executed',           // تم الصرف وإنشاء السند الفعلي
                'rejected'            // مرفوض
            ])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete(); // ربط بسند الصرف أو القبض المنفذ
            $table->timestamps();
        });

        // 4. إنشاء جدول رأس قيود اليومية العامة (General Journal Entries Header)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_no', 30)->unique(); // JV-2026-00001
            $table->date('entry_date'); // تاريخ القيد
            $table->string('reference_type', 50)->nullable(); // 'voucher', 'billing', 'manual', 'payroll', 'reversal'
            $table->unsignedBigInteger('reference_id')->nullable(); // معرّف المصدر (مثل رقم السند)
            $table->text('description'); // شرح وبيان القيد المحاسبي العام
            $table->decimal('total_debit', 14, 2)->default(0.00); // إجمالي الطرف المدين
            $table->decimal('total_credit', 14, 2)->default(0.00); // إجمالي الطرف الدائن
            $table->boolean('is_posted')->default(false); // هل القيد مرحل نهائياً
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reversed_by_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete(); // إذا تم إلغاؤه بقيد عكسي
            $table->timestamps();
        });

        // 5. إنشاء جدول تفاصيل وأسطر قيود اليومية (Journal Entry Lines)
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('financial_accounts')->cascadeOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->decimal('debit', 14, 2)->default(0.00); // المدين
            $table->decimal('credit', 14, 2)->default(0.00); // الدائن
            $table->string('line_description', 255)->nullable(); // بيان تفصيلي للسطر
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('payment_requests');
        Schema::dropIfExists('cost_centers');

        Schema::table('financial_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'level',
                'is_parent',
                'nature',
                'account_category',
                'allow_cost_center',
            ]);
        });
    }
};
