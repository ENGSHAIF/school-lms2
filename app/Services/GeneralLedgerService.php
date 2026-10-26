<?php

namespace App\Services;

use App\Models\FinancialAccount;
use App\Models\CostCenter;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Voucher;
use App\Models\SystemAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GeneralLedgerService
{
    /**
     * توليد رقم متسلسل آلي لقيد اليومية العامة
     */
    public static function generateEntryNo(): string
    {
        $year = date('Y');
        $count = JournalEntry::whereYear('created_at', $year)->count() + 1;
        return 'JV-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * توليد وتثبيت قيد اليومية الآلي عند إصدار سند قبض أو صرف
     */
    public static function recordVoucherEntry(Voucher $voucher): JournalEntry
    {
        // إذا كان القيد مسجلاً مسبقاً لهذا السند، يتم إرجاعه مباشرة
        $existing = JournalEntry::where('reference_type', 'voucher')->where('reference_id', $voucher->id)->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($voucher) {
            $entryNo = self::generateEntryNo();
            $boxAccount = FinancialAccount::findOrFail($voucher->cash_or_bank_account_id);
            $amount = (float) $voucher->amount;

            $entry = JournalEntry::create([
                'entry_no'       => $entryNo,
                'entry_date'     => $voucher->created_at?->toDateString() ?? now()->toDateString(),
                'reference_type' => 'voucher',
                'reference_id'   => $voucher->id,
                'description'    => "قيد آلي ناتج عن سند " . ($voucher->voucher_type === 'receipt' ? 'قبض' : 'صرف') . " رقم ({$voucher->voucher_no}): {$voucher->statement}",
                'total_debit'    => $amount,
                'total_credit'   => $amount,
                'is_posted'      => true,
                'posted_at'      => now(),
                'posted_by'      => Auth::id() ?? 1,
                'created_by'     => Auth::id() ?? 1,
            ]);

            if ($voucher->voucher_type === 'receipt') {
                // سند قبض: الخزينة/البنك مدين (+) والعميل/الطالب دائن (-)
                // 1. الطرف المدين: الصندوق أو البنك المستلم
                JournalEntryLine::create([
                    'entry_id'         => $entry->id,
                    'account_id'       => $boxAccount->id,
                    'cost_center_id'   => null,
                    'debit'            => $amount,
                    'credit'           => 0.00,
                    'line_description' => "استلام نقدي في: {$boxAccount->account_name}",
                ]);

                // 2. الطرف الدائن: حساب الطالب أو بنود السند المركب (Master-Detail)
                if ($voucher->is_compound && $voucher->items()->exists()) {
                    foreach ($voucher->items as $item) {
                        JournalEntryLine::create([
                            'entry_id'         => $entry->id,
                            'account_id'       => $item->account_id,
                            'cost_center_id'   => null,
                            'debit'            => 0.00,
                            'credit'           => (float)$item->amount,
                            'line_description' => $item->statement ?: "تحصيل دفعة/قسط من: {$item->account?->account_name}",
                        ]);
                    }
                } else {
                    $secondAccount = FinancialAccount::findOrFail($voucher->account_id);
                    JournalEntryLine::create([
                        'entry_id'         => $entry->id,
                        'account_id'       => $secondAccount->id,
                        'cost_center_id'   => null,
                        'debit'            => 0.00,
                        'credit'           => $amount,
                        'line_description' => "سداد لحساب: {$secondAccount->account_name}",
                    ]);
                }
            } else {
                // سند صرف: المستفيد/المصروف مدين (+) والخزينة/البنك دائن (-)
                $secondAccount = FinancialAccount::findOrFail($voucher->account_id);

                // 1. الطرف المدين: المعلم أو بند المصروف
                // افتراضياً نربطه بمركز التكلفة العام إن وجد
                $defaultCC = CostCenter::where('code', '101')->first() ?? CostCenter::first();

                JournalEntryLine::create([
                    'entry_id'         => $entry->id,
                    'account_id'       => $secondAccount->id,
                    'cost_center_id'   => $defaultCC?->id,
                    'debit'            => $amount,
                    'credit'           => 0.00,
                    'line_description' => "صرف لحساب: {$secondAccount->account_name}",
                ]);

                // 2. الطرف الدائن: الصندوق أو البنك الصارف
                JournalEntryLine::create([
                    'entry_id'         => $entry->id,
                    'account_id'       => $boxAccount->id,
                    'cost_center_id'   => null,
                    'debit'            => 0.00,
                    'credit'           => $amount,
                    'line_description' => "سداد منصرف من: {$boxAccount->account_name}",
                ]);
            }

            return $entry;
        });
    }

    /**
     * إنشاء قيد يومية يدوي متزن مع فحص التوازن الصارم
     */
    public static function createManualEntry(array $headerData, array $linesData): JournalEntry
    {
        return DB::transaction(function () use ($headerData, $linesData) {
            $totalDebit = 0.00;
            $totalCredit = 0.00;

            foreach ($linesData as $line) {
                $totalDebit += (float) ($line['debit'] ?? 0);
                $totalCredit += (float) ($line['credit'] ?? 0);
            }

            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception("القيد المحاسبي غير متزن! إجمالي المدين ({$totalDebit}) لا يساوي إجمالي الدائن ({$totalCredit}).");
            }

            if ($totalDebit <= 0) {
                throw new \Exception("لا يمكن إنشاء قيد بمبلغ صفر أو مبالغ سالبة.");
            }

            $entryNo = self::generateEntryNo();
            $isPosted = !empty($headerData['is_posted']);

            $entry = JournalEntry::create([
                'entry_no'       => $entryNo,
                'entry_date'     => $headerData['entry_date'] ?? now()->toDateString(),
                'reference_type' => 'manual',
                'reference_id'   => null,
                'description'    => $headerData['description'],
                'total_debit'    => $totalDebit,
                'total_credit'   => $totalCredit,
                'is_posted'      => $isPosted,
                'posted_at'      => $isPosted ? now() : null,
                'posted_by'      => $isPosted ? (Auth::id() ?? 1) : null,
                'created_by'     => Auth::id() ?? 1,
            ]);

            foreach ($linesData as $line) {
                if (empty($line['account_id'])) continue;
                $debit = (float)($line['debit'] ?? 0);
                $credit = (float)($line['credit'] ?? 0);
                if ($debit == 0 && $credit == 0) continue;

                $account = FinancialAccount::findOrFail($line['account_id']);
                if ($account->is_parent || $account->level < 5) {
                    throw new \Exception("لا يمكن إدخال قيد على حساب رئيسي تجميعي ({$account->account_name} - رتبة {$account->level}). وفق معايير النظام المحاسبي، تقبل القيود فقط على حسابات الرتبة الخامسة الفرعية الحصرية.");
                }

                JournalEntryLine::create([
                    'entry_id'         => $entry->id,
                    'account_id'       => $account->id,
                    'cost_center_id'   => !empty($line['cost_center_id']) ? $line['cost_center_id'] : null,
                    'debit'            => $debit,
                    'credit'           => $credit,
                    'line_description' => $line['line_description'] ?? $headerData['description'],
                ]);

                // إذا كان مرحلاً، نحدث رصيد الحساب المالي
                if ($isPosted) {
                    if ($account->nature === 'debit') {
                        $account->increment('current_balance', $debit - $credit);
                    } else {
                        $account->increment('current_balance', $credit - $debit);
                    }
                }
            }

            SystemAuditLog::record(
                'journal_entry.create',
                "إدخال قيد يومية محاسبي رقم ({$entryNo}) بإجمالي {$totalDebit} ر.ي",
                $entry,
                ['total' => $totalDebit, 'lines_count' => count($linesData), 'is_posted' => $isPosted]
            );

            return $entry;
        });
    }

    /**
     * إلغاء قيد مرحل عبر قيد عكسي مطابق (Reversal Entry)
     */
    public static function reverseEntry(JournalEntry $originalEntry, string $reason): JournalEntry
    {
        return DB::transaction(function () use ($originalEntry, $reason) {
            if ($originalEntry->reversed_by_entry_id) {
                throw new \Exception("هذا القيد تم إلغاؤه مسبقاً بقيد عكسي.");
            }

            $reverseNo = self::generateEntryNo();
            $reversal = JournalEntry::create([
                'entry_no'       => $reverseNo,
                'entry_date'     => now()->toDateString(),
                'reference_type' => 'reversal',
                'reference_id'   => $originalEntry->id,
                'description'    => "قيد عكسي لإلغاء القيد رقم ({$originalEntry->entry_no}): {$reason}",
                'total_debit'    => $originalEntry->total_credit,
                'total_credit'   => $originalEntry->total_debit,
                'is_posted'      => true,
                'posted_at'      => now(),
                'posted_by'      => Auth::id() ?? 1,
                'created_by'     => Auth::id() ?? 1,
            ]);

            // عكس الأطراف: المدين يصبح دائناً، والدائن يصبح مديناً
            foreach ($originalEntry->lines as $line) {
                JournalEntryLine::create([
                    'entry_id'         => $reversal->id,
                    'account_id'       => $line->account_id,
                    'cost_center_id'   => $line->cost_center_id,
                    'debit'            => $line->credit,
                    'credit'           => $line->debit,
                    'line_description' => "عكس: " . ($line->line_description ?: $originalEntry->description),
                ]);

                // تسوية الرصيد
                $account = $line->account;
                if ($account) {
                    if ($account->nature === 'debit') {
                        $account->increment('current_balance', $line->credit - $line->debit);
                    } else {
                        $account->increment('current_balance', $line->debit - $line->credit);
                    }
                }
            }

            $originalEntry->reversed_by_entry_id = $reversal->id;
            $originalEntry->save();

            SystemAuditLog::record(
                'journal_entry.reversed',
                "إلغاء القيد ({$originalEntry->entry_no}) بالقيد العكسي ({$reverseNo}) - السبب: {$reason}",
                $reversal,
                ['original_id' => $originalEntry->id, 'reason' => $reason]
            );

            return $reversal;
        });
    }

    /**
     * استخراج ميزان المراجعة بالمجاميع والأرصدة (Trial Balance)
     */
    public static function getTrialBalance($fromDate = null, $toDate = null): array
    {
        $accounts = FinancialAccount::where('is_active', true)
            ->where('level', 5)
            ->where('is_parent', false)
            ->orderBy('account_code')
            ->get();

        $rows = [];
        $totalDebitSum = 0;
        $totalCreditSum = 0;
        $totalDebitBalance = 0;
        $totalCreditBalance = 0;

        foreach ($accounts as $acc) {
            $linesQuery = JournalEntryLine::where('account_id', $acc->id)
                ->whereHas('entry', function ($q) use ($fromDate, $toDate) {
                    $q->where('is_posted', true);
                    if ($fromDate) $q->whereDate('entry_date', '>=', $fromDate);
                    if ($toDate) $q->whereDate('entry_date', '<=', $toDate);
                });

            $totalDebit = (float) $linesQuery->sum('debit');
            $totalCredit = (float) $linesQuery->sum('credit');

            // احتساب الرصيد الصافي المباشر للقيد المزدوج
            $net = $totalDebit - $totalCredit;
            $balanceDebit = $net > 0 ? $net : 0;
            $balanceCredit = $net < 0 ? abs($net) : 0;

            // تضمين الحساب إذا كان عليه حركات خلال الفترة
            if ($totalDebit > 0 || $totalCredit > 0) {
                $rows[] = [
                    'account_code'    => $acc->account_code,
                    'account_name'    => $acc->account_name,
                    'account_category'=> $acc->account_category_label,
                    'nature'          => $acc->nature,
                    'total_debit'     => $totalDebit,
                    'total_credit'    => $totalCredit,
                    'balance_debit'   => $balanceDebit,
                    'balance_credit'  => $balanceCredit,
                ];

                $totalDebitSum += $totalDebit;
                $totalCreditSum += $totalCredit;
                $totalDebitBalance += $balanceDebit;
                $totalCreditBalance += $balanceCredit;
            }
        }

        return [
            'rows'                 => $rows,
            'total_debit_sum'      => $totalDebitSum,
            'total_credit_sum'     => $totalCreditSum,
            'total_debit_balance'  => $totalDebitBalance,
            'total_credit_balance' => $totalCreditBalance,
            'is_balanced'          => abs($totalDebitBalance - $totalCreditBalance) < 1.0,
        ];
    }

    /**
     * استخراج قائمة الدخل والأرباح والخسائر (Income Statement / P&L)
     */
    public static function getIncomeStatement($fromDate = null, $toDate = null): array
    {
        // 1. الإيرادات
        $revenues = FinancialAccount::where('account_category', 'revenue')
            ->where('is_parent', false)
            ->get()
            ->map(function ($acc) use ($fromDate, $toDate) {
                $sum = (float) JournalEntryLine::where('account_id', $acc->id)
                    ->whereHas('entry', fn($q) => $q->where('is_posted', true))
                    ->sum('credit');
                return [
                    'code'   => $acc->account_code,
                    'name'   => $acc->account_name,
                    'amount' => $sum > 0 ? $sum : (float)$acc->current_balance,
                ];
            });

        // 2. المصروفات
        $expenses = FinancialAccount::where('account_category', 'expense')
            ->where('is_parent', false)
            ->get()
            ->map(function ($acc) use ($fromDate, $toDate) {
                $sum = (float) JournalEntryLine::where('account_id', $acc->id)
                    ->whereHas('entry', fn($q) => $q->where('is_posted', true))
                    ->sum('debit');
                return [
                    'code'   => $acc->account_code,
                    'name'   => $acc->account_name,
                    'amount' => $sum > 0 ? $sum : (float)$acc->current_balance,
                ];
            });

        $totalRevenues = $revenues->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalRevenues - $totalExpenses;

        return [
            'revenues'       => $revenues,
            'expenses'       => $expenses,
            'total_revenues' => $totalRevenues,
            'total_expenses' => $totalExpenses,
            'net_profit'     => $netProfit,
            'is_profit'      => $netProfit >= 0,
        ];
    }

    /**
     * تقرير ربحية مراكز التكلفة (Cost Centers P&L Report)
     */
    public static function getCostCentersReport(): array
    {
        $centers = CostCenter::with('children')->whereNull('parent_id')->get();
        $report = [];

        foreach ($centers as $mainCenter) {
            $subReport = [];
            $mainRev = 0;
            $mainExp = 0;

            foreach ($mainCenter->children as $sub) {
                $rev = (float) JournalEntryLine::where('cost_center_id', $sub->id)
                    ->whereHas('account', fn($q) => $q->where('account_category', 'revenue'))
                    ->sum('credit');

                $exp = (float) JournalEntryLine::where('cost_center_id', $sub->id)
                    ->whereHas('account', fn($q) => $q->where('account_category', 'expense'))
                    ->sum('debit');

                $profit = $rev - $exp;
                $subReport[] = [
                    'id'       => $sub->id,
                    'code'     => $sub->code,
                    'name'     => $sub->name,
                    'revenues' => $rev,
                    'expenses' => $exp,
                    'profit'   => $profit,
                ];

                $mainRev += $rev;
                $mainExp += $exp;
            }

            $report[] = [
                'main_center' => $mainCenter,
                'children'    => $subReport,
                'total_rev'   => $mainRev,
                'total_exp'   => $mainExp,
                'net_profit'  => $mainRev - $mainExp,
            ];
        }

        return $report;
    }
}
