<?php

namespace App\Http\Controllers;

use App\Helpers\ArabicCurrencyHelper;
use App\Models\AccountBudget;
use App\Models\AccountingPeriod;
use App\Models\CashInventoryCount;
use App\Models\ChequePortfolio;
use App\Models\CostCenter;
use App\Models\DailyPortfolio;
use App\Models\DailyPortfolioItem;
use App\Models\FinancialAccount;
use App\Models\FinancialNote;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Voucher;
use App\Models\VoucherItem;
use App\Services\GeneralLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class YemenSoftAccountingController extends Controller
{
    // =========================================================================
    // 1. الفترات المحاسبية وإقفال الشهور (Accounting Periods)
    // =========================================================================
    public function periods(Request $request)
    {
        $year = (int) $request->input('year', 2026);
        $periods = AccountingPeriod::where('fiscal_year', $year)->orderBy('period_number')->get();

        return view('accounting.yemensoft.periods', compact('periods', 'year'));
    }

    public function togglePeriodStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,suspended,closed',
            'notes' => 'nullable|string|max:500',
        ]);

        $period = AccountingPeriod::findOrFail($id);
        $period->status = $request->status;
        $period->notes = $request->notes;

        if ($request->status === 'closed') {
            $period->closed_at = now();
            $period->closed_by = Auth::id() ?? 1;
        } else {
            $period->closed_at = null;
            $period->closed_by = null;
        }

        $period->save();

        return back()->with('success', "تم تحديث حالة فترة ({$period->name}) بنجاح إلى: ".$period->status_label);
    }

    // =========================================================================
    // 2. خيارات وتهيئة الحسابات الوسيطة (System Options & Clearing Accounts)
    // =========================================================================
    public function options()
    {
        $settingsRaw = DB::table('site_settings')->where('group', 'accounting')->pluck('value', 'key');
        $accounts = FinancialAccount::where('is_parent', false)->where('is_active', true)->orderBy('account_code')->get();

        return view('accounting.yemensoft.options', compact('settingsRaw', 'accounts'));
    }

    public function updateOptions(Request $request)
    {
        $keys = [
            'accounting.prevent_overdraft',
            'accounting.allow_suspended_entries',
            'accounting.strict_period_lock',
            'accounting.allow_daily_portfolios',
            'accounting.f9_statement_history',
            'accounting.clearing_cheque_receivable_account_id',
            'accounting.clearing_cheque_payable_account_id',
            'accounting.currency_diff_gain_account_id',
            'accounting.currency_diff_loss_account_id',
        ];

        foreach ($keys as $k) {
            $val = $request->input($k, '0');
            DB::table('site_settings')->updateOrInsert(
                ['key' => $k],
                [
                    'value' => (string) $val,
                    'group' => 'accounting',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return back()->with('success', 'تم حفظ وتفعيل خيارات نظام يمن سوفت المحاسبي بنجاح!');
    }

    // =========================================================================
    // 3. مدخلات الأرصدة الافتتاحية والميزان الافتتاحي (Opening Balances)
    // =========================================================================
    public function openingBalances(Request $request)
    {
        $accounts = FinancialAccount::where('is_parent', false)->where('is_active', true)->orderBy('account_code')->get();

        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($accounts as $acc) {
            if ($acc->nature === 'debit') {
                $totalDebit += (float) $acc->opening_balance;
            } else {
                $totalCredit += (float) $acc->opening_balance;
            }
        }

        $difference = abs($totalDebit - $totalCredit);
        $isBalanced = $difference < 0.01;

        return view('accounting.yemensoft.opening_balances', compact('accounts', 'totalDebit', 'totalCredit', 'difference', 'isBalanced'));
    }

    public function saveOpeningBalances(Request $request)
    {
        $balances = $request->input('balances', []);

        DB::transaction(function () use ($balances) {
            foreach ($balances as $accId => $data) {
                $account = FinancialAccount::find($accId);
                if ($account) {
                    $amount = (float) ($data['amount'] ?? 0);
                    $account->opening_balance = $amount;
                    // تحديث الرصيد الحالي إذا لم تكن هناك حركات مسجلة
                    $account->current_balance = $amount;
                    if (! empty($data['min_balance'])) {
                        $account->min_balance = (float) $data['min_balance'];
                    }
                    if (! empty($data['max_balance'])) {
                        $account->max_balance = (float) $data['max_balance'];
                    }
                    $account->save();
                }
            }
        });

        return back()->with('success', 'تم حفظ الأرصدة الافتتاحية وحدود الحسابات بنجاح!');
    }

    // =========================================================================
    // 4. الموازنة التقديرية للحسابات ومراكز التكلفة (Budgets)
    // =========================================================================
    public function budgets(Request $request)
    {
        $year = (int) $request->input('year', 2026);
        $accounts = FinancialAccount::where('is_parent', false)->where('is_active', true)->orderBy('account_code')->get();
        $costCenters = CostCenter::where('is_active', true)->get();

        $selectedAccountId = $request->input('account_id', $accounts->first()?->id);
        $selectedAccount = FinancialAccount::find($selectedAccountId);

        $monthlyBudgets = [];
        if ($selectedAccountId) {
            $budgets = AccountBudget::where('fiscal_year', $year)
                ->where('account_id', $selectedAccountId)
                ->get()
                ->keyBy('month');

            for ($m = 1; $m <= 12; $m++) {
                $monthlyBudgets[$m] = $budgets->get($m);
            }
        }

        return view('accounting.yemensoft.budgets', compact('year', 'accounts', 'costCenters', 'selectedAccount', 'monthlyBudgets'));
    }

    public function saveBudget(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:financial_accounts,id',
            'fiscal_year' => 'required|integer',
            'months' => 'required|array',
        ]);

        $accountId = $request->account_id;
        $year = $request->fiscal_year;
        $costCenterId = $request->cost_center_id;

        DB::transaction(function () use ($request, $accountId, $year, $costCenterId) {
            foreach ($request->months as $m => $amount) {
                $budgetAmount = (float) $amount;
                // حساب الفعلي المسجل في الشهر الحالي
                $actual = DB::table('journal_entry_lines')
                    ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.entry_id')
                    ->where('journal_entry_lines.account_id', $accountId)
                    ->whereYear('journal_entries.entry_date', $year)
                    ->whereMonth('journal_entries.entry_date', $m)
                    ->sum(DB::raw('journal_entry_lines.debit + journal_entry_lines.credit'));

                $variance = $budgetAmount - $actual;

                AccountBudget::updateOrCreate(
                    [
                        'account_id' => $accountId,
                        'fiscal_year' => $year,
                        'month' => (int) $m,
                    ],
                    [
                        'cost_center_id' => $costCenterId,
                        'budget_amount' => $budgetAmount,
                        'actual_amount' => $actual,
                        'variance' => $variance,
                        'currency' => $request->currency ?? 'YER',
                    ]
                );
            }
        });

        return back()->with('success', 'تم حفظ الموازنة التقديرية السنوية والشهرية بنجاح!');
    }

    // =========================================================================
    // 5. محفظة الشيكات وأوراق القبض والدفع (Cheques Portfolio)
    // =========================================================================
    public function cheques(Request $request)
    {
        $query = ChequePortfolio::with(['account', 'bankAccount', 'voucher', 'creator', 'collector'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('cheque_type', $request->type);
        }

        $cheques = $query->paginate(20);
        $banks = FinancialAccount::whereIn('account_type', ['bank', 'cash_box'])->where('is_parent', false)->get();
        $accounts = FinancialAccount::where('is_parent', false)->get();

        return view('accounting.yemensoft.cheques', compact('cheques', 'banks', 'accounts'));
    }

    public function storeCheque(Request $request)
    {
        $request->validate([
            'cheque_no' => 'required|string|max:50',
            'cheque_type' => 'required|in:receivable,payable',
            'account_id' => 'required|exists:financial_accounts,id',
            'amount' => 'required|numeric|min:1',
            'due_date' => 'required|date',
        ]);

        ChequePortfolio::create([
            'cheque_no' => $request->cheque_no,
            'cheque_type' => $request->cheque_type,
            'account_id' => $request->account_id,
            'bank_id' => $request->bank_id,
            'bank_name' => $request->bank_name,
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'YER',
            'due_date' => $request->due_date,
            'status' => 'under_collection',
            'notes' => $request->notes,
            'created_by' => Auth::id() ?? 1,
        ]);

        return back()->with('success', 'تم تسجيل الشيك في محفظة الأوراق المالية بنجاح!');
    }

    public function updateChequeStatus(Request $request, $id)
    {
        $cheque = ChequePortfolio::findOrFail($id);
        $action = $request->input('action');

        if ($action === 'collect') {
            $cheque->status = 'collected';
            $cheque->collected_at = now();
            $cheque->collected_by = Auth::id() ?? 1;
            $cheque->save();

            // تحديث رصيد البنك ورصيد العميل عبر قيد محاسبي
            return back()->with('success', "تم تحصيل الشيك رقم ({$cheque->cheque_no}) وإيداعه بنجاح!");
        } elseif ($action === 'return') {
            $cheque->status = 'returned';
            $cheque->notes = ($cheque->notes ? $cheque->notes.' | ' : '').'مرتجع: '.$request->input('reason', 'عدم كفاية الرصيد');
            $cheque->save();

            return back()->with('warning', "تم تسجيل الشيك رقم ({$cheque->cheque_no}) كشيك مرتجع.");
        } elseif ($action === 'extend') {
            $request->validate(['extension_date' => 'required|date']);
            $cheque->status = 'extended';
            $cheque->extension_date = $request->extension_date;
            $cheque->save();

            return back()->with('success', "تم تمديد أجل الشيك رقم ({$cheque->cheque_no}) إلى تاريخ: {$request->extension_date}");
        }

        return back();
    }

    // =========================================================================
    // 6. حوافظ الصرف والقبض اليومية (Daily Portfolios)
    // =========================================================================
    public function dailyPortfolios(Request $request)
    {
        $portfolios = DailyPortfolio::with(['cashBox', 'creator', 'approver', 'voucher'])
            ->withCount('items')
            ->latest()
            ->paginate(15);

        $cashBoxes = FinancialAccount::where('account_type', 'cash_box')->where('is_parent', false)->get();
        $accounts = FinancialAccount::where('is_parent', false)->where('is_active', true)->get();
        $costCenters = CostCenter::where('is_active', true)->get();

        return view('accounting.yemensoft.daily_portfolios', compact('portfolios', 'cashBoxes', 'accounts', 'costCenters'));
    }

    public function storeDailyPortfolio(Request $request)
    {
        $request->validate([
            'portfolio_type' => 'required|in:receipt,payment',
            'portfolio_date' => 'required|date',
            'cash_box_id' => 'required|exists:financial_accounts,id',
            'items' => 'required|array|min:1',
        ]);

        $no = 'DP-'.date('Ymd').'-'.str_pad(DailyPortfolio::count() + 1, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $no) {
            $total = 0.0;
            foreach ($request->items as $it) {
                if (! empty($it['amount']) && (float) $it['amount'] > 0) {
                    $total += (float) $it['amount'];
                }
            }

            $portfolio = DailyPortfolio::create([
                'portfolio_no' => $no,
                'portfolio_type' => $request->portfolio_type,
                'portfolio_date' => $request->portfolio_date,
                'cash_box_id' => $request->cash_box_id,
                'currency' => $request->currency ?? 'YER',
                'total_amount' => $total,
                'status' => 'draft',
                'created_by' => Auth::id() ?? 1,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $it) {
                if (! empty($it['amount']) && (float) $it['amount'] > 0) {
                    DailyPortfolioItem::create([
                        'portfolio_id' => $portfolio->id,
                        'account_id' => $it['account_id'],
                        'cost_center_id' => $it['cost_center_id'] ?? null,
                        'amount' => (float) $it['amount'],
                        'statement' => $it['statement'] ?? 'حركة حافظة يومية',
                        'payee_or_payer' => $it['payee_or_payer'] ?? null,
                    ]);
                }
            }
        });

        return back()->with('success', "تم فتح وحفظ الحافظة اليومية رقم ({$no}) بنجاح!");
    }

    public function approveDailyPortfolio($id)
    {
        $p = DailyPortfolio::findOrFail($id);
        $p->status = 'approved';
        $p->approved_by = Auth::id() ?? 1;
        $p->save();

        return back()->with('success', "تم اعتماد الحافظة اليومية رقم ({$p->portfolio_no}) بنجاح، وهي الآن جاهزة للترحيل إلى سند رسمي!");
    }

    public function postDailyPortfolioToVoucher($id)
    {
        $p = DailyPortfolio::with(['items', 'cashBox'])->findOrFail($id);

        if ($p->status === 'posted_to_voucher') {
            return back()->with('warning', 'هذه الحافظة مرحلة مسبقاً!');
        }

        DB::transaction(function () use ($p) {
            $voucherCount = Voucher::count() + 1;
            $prefix = $p->portfolio_type === 'payment' ? 'PAY-' : 'REC-';
            $voucherNo = $prefix.date('Y').'-'.str_pad($voucherCount, 5, '0', STR_PAD_LEFT);
            $amountInWords = ArabicCurrencyHelper::tafqeet($p->total_amount, $p->currency);

            $voucher = Voucher::create([
                'voucher_no' => $voucherNo,
                'voucher_type' => $p->portfolio_type,
                'is_compound' => $p->items->count() > 1,
                'account_id' => $p->items->first()?->account_id ?? $p->cash_box_id,
                'cash_or_bank_account_id' => $p->cash_box_id,
                'amount' => $p->total_amount,
                'currency' => $p->currency,
                'exchange_rate' => 1.0000,
                'amount_local' => $p->total_amount,
                'amount_in_words' => $amountInWords,
                'payment_method' => 'cash',
                'statement' => "مرحل آلياً من الحافظة اليومية ({$p->portfolio_no}): ".($p->notes ?? ''),
                'created_by' => Auth::id() ?? 1,
            ]);

            foreach ($p->items as $it) {
                VoucherItem::create([
                    'voucher_id' => $voucher->id,
                    'account_id' => $it->account_id,
                    'amount' => $it->amount,
                    'statement' => $it->statement,
                ]);

                // تحديث رصيد الحساب الفرعي
                $subAccount = FinancialAccount::find($it->account_id);
                if ($subAccount) {
                    if ($p->portfolio_type === 'payment') {
                        $subAccount->decrement('current_balance', $it->amount);
                    } else {
                        $subAccount->increment('current_balance', $it->amount);
                    }
                }
            }

            // تحديث رصيد الصندوق
            if ($p->portfolio_type === 'payment') {
                $p->cashBox->decrement('current_balance', $p->total_amount);
            } else {
                $p->cashBox->increment('current_balance', $p->total_amount);
            }

            // قيد اليومية
            GeneralLedgerService::recordVoucherEntry($voucher);

            $p->status = 'posted_to_voucher';
            $p->voucher_id = $voucher->id;
            $p->save();
        });

        return back()->with('success', 'تم ترحيل الحافظة اليومية بنجاح إلى سند رسمي مرقم ومقيد في دفتر الأستاذ العام!');
    }

    // =========================================================================
    // 7. الإشعارات المدينة والدائنة (Debit & Credit Notes)
    // =========================================================================
    public function financialNotes(Request $request)
    {
        $notes = FinancialNote::with(['account', 'secondaryAccount', 'costCenter', 'creator'])
            ->latest()
            ->paginate(20);

        $accounts = FinancialAccount::where('is_parent', false)->where('is_active', true)->get();
        $costCenters = CostCenter::where('is_active', true)->get();

        return view('accounting.yemensoft.notes', compact('notes', 'accounts', 'costCenters'));
    }

    public function storeFinancialNote(Request $request)
    {
        $request->validate([
            'note_type' => 'required|in:debit,credit',
            'note_date' => 'required|date',
            'account_id' => 'required|exists:financial_accounts,id',
            'secondary_account_id' => 'required|exists:financial_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'statement' => 'required|string|max:500',
        ]);

        $prefix = $request->note_type === 'debit' ? 'DN-' : 'CN-';
        $noteNo = $prefix.date('Y').'-'.str_pad(FinancialNote::count() + 1, 5, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $noteNo) {
            $note = FinancialNote::create([
                'note_no' => $noteNo,
                'note_type' => $request->note_type,
                'note_date' => $request->note_date,
                'account_id' => $request->account_id,
                'secondary_account_id' => $request->secondary_account_id,
                'cost_center_id' => $request->cost_center_id,
                'amount' => $request->amount,
                'currency' => $request->currency ?? 'YER',
                'exchange_rate' => 1.0000,
                'statement' => $request->statement,
                'status' => 'posted',
                'created_by' => Auth::id() ?? 1,
            ]);

            // إنشاء قيد يومية آلي للإشعار المدين أو الدائن
            $entryNo = 'JV-'.date('Y').'-'.str_pad(JournalEntry::count() + 1, 5, '0', STR_PAD_LEFT);
            $entry = JournalEntry::create([
                'entry_no' => $entryNo,
                'entry_type' => $request->note_type === 'debit' ? 'debit_note' : 'credit_note',
                'entry_date' => $request->note_date,
                'reference_type' => 'manual',
                'reference_id' => $note->id,
                'description' => "قيد إشعار {$note->note_type_label}: ".$request->statement,
                'total_debit' => $request->amount,
                'total_credit' => $request->amount,
                'is_posted' => true,
                'posted_at' => now(),
                'posted_by' => Auth::id() ?? 1,
                'created_by' => Auth::id() ?? 1,
            ]);

            if ($request->note_type === 'debit') {
                // إشعار مدين: الحساب الأول مدين (قيد عليه)، والحساب المقابل دائن
                JournalEntryLine::create([
                    'entry_id' => $entry->id,
                    'account_id' => $request->account_id,
                    'debit' => $request->amount,
                    'credit' => 0.00,
                    'description' => $request->statement,
                    'cost_center_id' => $request->cost_center_id,
                ]);
                JournalEntryLine::create([
                    'entry_id' => $entry->id,
                    'account_id' => $request->secondary_account_id,
                    'debit' => 0.00,
                    'credit' => $request->amount,
                    'description' => $request->statement,
                    'cost_center_id' => $request->cost_center_id,
                ]);
            } else {
                // إشعار دائن: الحساب الأول دائن (إضافة له)، والحساب المقابل مدين
                JournalEntryLine::create([
                    'entry_id' => $entry->id,
                    'account_id' => $request->secondary_account_id,
                    'debit' => $request->amount,
                    'credit' => 0.00,
                    'description' => $request->statement,
                    'cost_center_id' => $request->cost_center_id,
                ]);
                JournalEntryLine::create([
                    'entry_id' => $entry->id,
                    'account_id' => $request->account_id,
                    'debit' => 0.00,
                    'credit' => $request->amount,
                    'description' => $request->statement,
                    'cost_center_id' => $request->cost_center_id,
                ]);
            }

            $note->journal_entry_id = $entry->id;
            $note->save();
        });

        return back()->with('success', "تم تسجيل وترحيل الإشعار رقم ({$noteNo}) وتوليد قيده المحاسبي بنجاح!");
    }

    // =========================================================================
    // 8. جرد النقدية الفعلي ومطابقة الفئات (Cash Inventory Count)
    // =========================================================================
    public function cashCounts(Request $request)
    {
        $counts = CashInventoryCount::with(['cashBox', 'creator'])->latest()->paginate(15);
        $cashBoxes = FinancialAccount::where('account_type', 'cash_box')->where('is_parent', false)->get();

        return view('accounting.yemensoft.cash_counts', compact('counts', 'cashBoxes'));
    }

    public function storeCashCount(Request $request)
    {
        $request->validate([
            'cash_box_id' => 'required|exists:financial_accounts,id',
            'count_date' => 'required|date',
            'denominations' => 'required|array',
        ]);

        $cashBox = FinancialAccount::findOrFail($request->cash_box_id);
        $systemBalance = (float) $cashBox->current_balance;

        // حساب المبلغ الفعلي من مجموع ضرب الفئات
        $countedTotal = 0.0;
        foreach ($request->denominations as $denom => $qty) {
            $count = (int) $qty;
            if ($count > 0) {
                $countedTotal += ((float) $denom * $count);
            }
        }

        $variance = $countedTotal - $systemBalance;
        $status = 'balanced';
        if ($variance > 0.01) {
            $status = 'surplus'; // فائض
        } elseif ($variance < -0.01) {
            $status = 'deficit'; // عجز
        }

        $countNo = 'CNT-'.date('Ymd').'-'.str_pad(CashInventoryCount::count() + 1, 3, '0', STR_PAD_LEFT);

        CashInventoryCount::create([
            'count_no' => $countNo,
            'cash_box_id' => $cashBox->id,
            'count_date' => $request->count_date,
            'currency' => $cashBox->currency ?? 'YER',
            'system_balance' => $systemBalance,
            'counted_balance' => $countedTotal,
            'variance' => $variance,
            'status' => $status,
            'denominations' => $request->denominations,
            'notes' => $request->notes,
            'created_by' => Auth::id() ?? 1,
        ]);

        return back()->with('success', "تم إثبات محضر جرد الخزينة رقم ({$countNo}) بنجاح. النتيجة: ".($status === 'balanced' ? 'مطابق تماماً' : ($status === 'surplus' ? "فائض بمقدار (+{$variance})" : "عجز بمقدار ({$variance})")));
    }

    // =========================================================================
    // 9. الترحيل النهائي وتجميد العمليات (Batch Posting Engine)
    // =========================================================================
    public function posting(Request $request)
    {
        $unpostedVouchers = Voucher::where('is_posted', false)->with(['account', 'cashOrBankAccount'])->latest()->get();
        $unpostedEntries = JournalEntry::where('is_posted', false)->latest()->get();

        $periods = AccountingPeriod::open()->get();

        return view('accounting.yemensoft.posting', compact('unpostedVouchers', 'unpostedEntries', 'periods'));
    }

    public function postBatch(Request $request)
    {
        $voucherIds = $request->input('voucher_ids', []);
        $entryIds = $request->input('entry_ids', []);

        DB::transaction(function () use ($voucherIds, $entryIds) {
            if (! empty($voucherIds)) {
                Voucher::whereIn('id', $voucherIds)->update([
                    'is_posted' => true,
                    'is_suspended' => false,
                    'posted_at' => now(),
                    'posted_by' => Auth::id() ?? 1,
                ]);
            }

            if (! empty($entryIds)) {
                JournalEntry::whereIn('id', $entryIds)->update([
                    'is_posted' => true,
                    'is_suspended' => false,
                    'posted_at' => now(),
                    'posted_by' => Auth::id() ?? 1,
                ]);
            }
        });

        $count = count($voucherIds) + count($entryIds);

        return back()->with('success', "تم إجراء الترحيل النهائي وتجميد عدد ({$count}) عملية وسند وقيد في الدفاتر الرسمية بنجاح!");
    }
}
