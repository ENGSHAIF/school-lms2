<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\CostCenter;
use App\Models\JournalEntryLine;
use App\Services\GeneralLedgerService;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    /**
     * لوحة تقارير وقوائم النظام المالي
     */
    public function index()
    {
        return view('accounting.reports.index');
    }

    /**
     * تقرير ميزان المراجعة بالمجاميع والأرصدة (Trial Balance)
     */
    public function trialBalance(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date', now()->toDateString());

        $data = GeneralLedgerService::getTrialBalance($fromDate, $toDate);

        if ($request->boolean('print')) {
            return view('accounting.reports.print_trial_balance', compact('data', 'fromDate', 'toDate'));
        }

        return view('accounting.reports.trial_balance', compact('data', 'fromDate', 'toDate'));
    }

    /**
     * قائمة الدخل والأرباح والخسائر (Income Statement / P&L)
     */
    public function incomeStatement(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date', now()->toDateString());

        $data = GeneralLedgerService::getIncomeStatement($fromDate, $toDate);

        if ($request->boolean('print')) {
            return view('accounting.reports.print_income_statement', compact('data', 'fromDate', 'toDate'));
        }

        return view('accounting.reports.income_statement', compact('data', 'fromDate', 'toDate'));
    }

    /**
     * كشف حساب دفتر الأستاذ العام (General Ledger Statement)
     */
    public function generalLedger(Request $request)
    {
        $accountId = $request->input('account_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date', now()->toDateString());

        $accounts = FinancialAccount::where('is_parent', false)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $selectedAccount = $accountId ? FinancialAccount::find($accountId) : ($accounts->first() ?? null);

        $lines = collect([]);
        $runningBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;

        if ($selectedAccount) {
            $query = JournalEntryLine::with(['entry', 'costCenter'])
                ->where('account_id', $selectedAccount->id)
                ->whereHas('entry', function ($q) use ($fromDate, $toDate) {
                    $q->where('is_posted', true);
                    if ($fromDate) $q->whereDate('entry_date', '>=', $fromDate);
                    if ($toDate) $q->whereDate('entry_date', '<=', $toDate);
                })
                ->join('journal_entries', 'journal_entry_lines.entry_id', '=', 'journal_entries.id')
                ->orderBy('journal_entries.entry_date')
                ->orderBy('journal_entries.id')
                ->select('journal_entry_lines.*');

            $lines = $query->get();

            $totalDebit = $lines->sum('debit');
            $totalCredit = $lines->sum('credit');
            $runningBalance = $selectedAccount->nature === 'debit' 
                ? ($totalDebit - $totalCredit) 
                : ($totalCredit - $totalDebit);
        }

        if ($request->boolean('print') && $selectedAccount) {
            return view('accounting.reports.print_general_ledger', compact(
                'selectedAccount',
                'lines',
                'totalDebit',
                'totalCredit',
                'runningBalance',
                'fromDate',
                'toDate'
            ));
        }

        return view('accounting.reports.general_ledger', compact(
            'accounts',
            'selectedAccount',
            'lines',
            'totalDebit',
            'totalCredit',
            'runningBalance',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * تقرير ربحية مراكز التكلفة (Cost Centers P&L Report)
     */
    public function costCentersReport(Request $request)
    {
        $report = GeneralLedgerService::getCostCentersReport();

        if ($request->boolean('print')) {
            return view('accounting.reports.print_cost_centers', compact('report'));
        }

        return view('accounting.reports.cost_centers', compact('report'));
    }
}
