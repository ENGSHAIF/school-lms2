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
        // 1. ترقية جدول الحسابات بالحدود والمجموعات والتقارير (وفق معايير يمن سوفت)
        Schema::table('financial_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('financial_accounts', 'min_balance')) {
                $table->decimal('min_balance', 14, 2)->default(0.00)->after('current_balance'); // سقف أدنى لتنبيه السيولة
            }
            if (! Schema::hasColumn('financial_accounts', 'max_balance')) {
                $table->decimal('max_balance', 14, 2)->nullable()->after('min_balance'); // سقف أعلى لتنبيه الائتمان
            }
            if (! Schema::hasColumn('financial_accounts', 'report_type')) {
                $table->enum('report_type', ['balance_sheet', 'income_statement'])->default('balance_sheet')->after('nature'); // نوع التقرير السنوي
            }
        });

        // 2. الفترات المحاسبية (Accounting Periods)
        if (! Schema::hasTable('accounting_periods')) {
            Schema::create('accounting_periods', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('fiscal_year'); // 2026
                $table->unsignedTinyInteger('period_number'); // 1 إلى 12
                $table->string('name', 100); // يناير 2026
                $table->date('start_date');
                $table->date('end_date');
                $table->enum('status', ['open', 'suspended', 'closed'])->default('open'); // مفتوح / موقف / مغلق
                $table->timestamp('closed_at')->nullable();
                $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. مجموعات الحسابات ومجموعات مراكز التكلفة (Account Groups & Cost Center Groups)
        if (! Schema::hasTable('account_groups')) {
            Schema::create('account_groups', function (Blueprint $table) {
                $table->id();
                $table->string('code', 30)->unique();
                $table->string('name', 150);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cost_center_groups')) {
            Schema::create('cost_center_groups', function (Blueprint $table) {
                $table->id();
                $table->string('code', 30)->unique();
                $table->string('name', 150);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. الموازنة التقديرية للحسابات ومراكز التكلفة (Budgeting)
        if (! Schema::hasTable('account_budgets')) {
            Schema::create('account_budgets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('financial_accounts')->cascadeOnDelete();
                $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
                $table->unsignedSmallInteger('fiscal_year');
                $table->unsignedTinyInteger('month'); // 1 - 12
                $table->decimal('budget_amount', 14, 2)->default(0.00);
                $table->decimal('actual_amount', 14, 2)->default(0.00);
                $table->decimal('variance', 14, 2)->default(0.00);
                $table->string('currency', 10)->default('YER');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 5. محفظة الشيكات الآجلة وأوراق القبض والدفع (Cheques Portfolio)
        if (! Schema::hasTable('cheque_portfolios')) {
            Schema::create('cheque_portfolios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
                $table->foreignId('account_id')->constrained('financial_accounts')->cascadeOnDelete(); // حساب العميل / المستفيد
                $table->foreignId('bank_id')->nullable()->constrained('financial_accounts')->nullOnDelete(); // حساب البنك المسحوب عليه
                $table->string('cheque_no', 50);
                $table->string('bank_name', 150)->nullable();
                $table->enum('cheque_type', ['receivable', 'payable'])->default('receivable'); // ورقة قبض أم ورقة دفع
                $table->decimal('amount', 14, 2);
                $table->string('currency', 10)->default('YER');
                $table->date('due_date'); // تاريخ الاستحقاق
                $table->enum('status', ['under_collection', 'collected', 'returned', 'endorsed', 'extended'])->default('under_collection');
                $table->timestamp('collected_at')->nullable();
                $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('endorsed_to_account_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
                $table->date('extension_date')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // 6. حوافظ الصرف والقبض اليومية (Daily Portfolios)
        if (! Schema::hasTable('daily_portfolios')) {
            Schema::create('daily_portfolios', function (Blueprint $table) {
                $table->id();
                $table->string('portfolio_no', 50)->unique();
                $table->enum('portfolio_type', ['receipt', 'payment'])->default('payment'); // حافظة قبض أم حافظة صرف
                $table->date('portfolio_date');
                $table->foreignId('cash_box_id')->constrained('financial_accounts')->cascadeOnDelete(); // الصندوق المرتبط
                $table->string('currency', 10)->default('YER');
                $table->decimal('total_amount', 14, 2)->default(0.00);
                $table->enum('status', ['draft', 'approved', 'posted_to_voucher'])->default('draft');
                $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('daily_portfolio_items')) {
            Schema::create('daily_portfolio_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('portfolio_id')->constrained('daily_portfolios')->cascadeOnDelete();
                $table->foreignId('account_id')->constrained('financial_accounts')->cascadeOnDelete();
                $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
                $table->decimal('amount', 14, 2);
                $table->string('statement', 255);
                $table->string('payee_or_payer', 150)->nullable(); // المستلم أو الدافع
                $table->timestamps();
            });
        }

        // 7. الإشعارات المدينة والدائنة (Debit & Credit Notes)
        if (! Schema::hasTable('financial_notes')) {
            Schema::create('financial_notes', function (Blueprint $table) {
                $table->id();
                $table->string('note_no', 50)->unique();
                $table->enum('note_type', ['debit', 'credit'])->default('credit'); // إشعار مدين / إشعار دائن
                $table->date('note_date');
                $table->foreignId('account_id')->constrained('financial_accounts')->cascadeOnDelete(); // حساب العميل/المورد المتأثر
                $table->foreignId('secondary_account_id')->nullable()->constrained('financial_accounts')->nullOnDelete(); // حساب الإيراد أو المصروف المقابل
                $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
                $table->decimal('amount', 14, 2);
                $table->string('currency', 10)->default('YER');
                $table->decimal('exchange_rate', 10, 4)->default(1.0000);
                $table->text('statement');
                $table->enum('status', ['draft', 'posted'])->default('draft');
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // 8. جرد النقدية الفعلي بفئات العملة الورقية (Cash Inventory Count)
        if (! Schema::hasTable('cash_inventory_counts')) {
            Schema::create('cash_inventory_counts', function (Blueprint $table) {
                $table->id();
                $table->string('count_no', 50)->unique();
                $table->foreignId('cash_box_id')->constrained('financial_accounts')->cascadeOnDelete();
                $table->date('count_date');
                $table->string('currency', 10)->default('YER');
                $table->decimal('system_balance', 14, 2)->default(0.00); // الرصيد الدفتري
                $table->decimal('counted_balance', 14, 2)->default(0.00); // الرصيد الفعلي بعد الجرد
                $table->decimal('variance', 14, 2)->default(0.00); // الفارق (عجز أو فائض)
                $table->enum('status', ['balanced', 'surplus', 'deficit'])->default('balanced');
                $table->json('denominations')->nullable(); // تفصيل الفئات: [1000 => 50, 500 => 20...]
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // 9. ترقية جدول السندات لدعم الشيكات والتعليق والترحيل النهائي (Vouchers Advanced Features)
        Schema::table('vouchers', function (Blueprint $table) {
            if (! Schema::hasColumn('vouchers', 'cheque_no')) {
                $table->string('cheque_no', 50)->nullable()->after('payment_method');
            }
            if (! Schema::hasColumn('vouchers', 'cheque_bank_name')) {
                $table->string('cheque_bank_name', 150)->nullable()->after('cheque_no');
            }
            if (! Schema::hasColumn('vouchers', 'cheque_due_date')) {
                $table->date('cheque_due_date')->nullable()->after('cheque_bank_name');
            }
            if (! Schema::hasColumn('vouchers', 'is_posted')) {
                $table->boolean('is_posted')->default(true)->after('is_compound'); // مرحل نهائياً
            }
            if (! Schema::hasColumn('vouchers', 'is_suspended')) {
                $table->boolean('is_suspended')->default(false)->after('is_posted'); // تحت الانتظار / معلق
            }
            if (! Schema::hasColumn('vouchers', 'posted_at')) {
                $table->timestamp('posted_at')->nullable()->after('is_suspended');
            }
            if (! Schema::hasColumn('vouchers', 'posted_by')) {
                $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete()->after('posted_at');
            }
        });

        // 10. ترقية جدول قيود اليومية لدعم القيود المعلقة والدورية والعكسية (Journal Entries Advanced Features)
        Schema::table('journal_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('journal_entries', 'entry_type')) {
                $table->string('entry_type', 40)->default('general')->after('entry_no'); // عام / تسويات / عكسي / دوري / فروق عملة
            }
            if (! Schema::hasColumn('journal_entries', 'is_suspended')) {
                $table->boolean('is_suspended')->default(false)->after('is_posted'); // قيد تحت الانتظار معلق
            }
            if (! Schema::hasColumn('journal_entries', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('is_suspended'); // قيد دوري
            }
            if (! Schema::hasColumn('journal_entries', 'is_reversal')) {
                $table->boolean('is_reversal')->default(false)->after('is_recurring'); // قيد عكسي
            }
            if (! Schema::hasColumn('journal_entries', 'reversal_date')) {
                $table->date('reversal_date')->nullable()->after('is_reversal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping tables if rolled back
        Schema::dropIfExists('cash_inventory_counts');
        Schema::dropIfExists('financial_notes');
        Schema::dropIfExists('daily_portfolio_items');
        Schema::dropIfExists('daily_portfolios');
        Schema::dropIfExists('cheque_portfolios');
        Schema::dropIfExists('account_budgets');
        Schema::dropIfExists('cost_center_groups');
        Schema::dropIfExists('account_groups');
        Schema::dropIfExists('accounting_periods');
    }
};
