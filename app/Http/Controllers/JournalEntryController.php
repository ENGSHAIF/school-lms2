<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\FinancialAccount;
use App\Models\CostCenter;
use App\Models\SystemAuditLog;
use App\Services\GeneralLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JournalEntryController extends Controller
{
    /**
     * عرض دفتر قيود اليومية العامة (General Journal)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $type = $request->input('type');

        $query = JournalEntry::with(['lines.account', 'lines.costCenter', 'creator'])
            ->latest('entry_date')
            ->latest('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('entry_no', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($fromDate) {
            $query->whereDate('entry_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('entry_date', '<=', $toDate);
        }
        if ($type) {
            $query->where('reference_type', $type);
        }

        $entries = $query->paginate(15);

        // إحصائيات
        $totalEntries = JournalEntry::count();
        $totalDebits = JournalEntry::where('is_posted', true)->sum('total_debit');
        $totalCredits = JournalEntry::where('is_posted', true)->sum('total_credit');
        $manualEntriesCount = JournalEntry::where('reference_type', 'manual')->count();

        // الحسابات ومراكز التكلفة المتاحة للقيود اليدوية (الرتبة الخامسة الفرعية حصرية)
        $subAccounts = FinancialAccount::where('level', 5)
            ->where('is_parent', false)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $costCenters = CostCenter::where('is_parent', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('accounting.journal_entries.index', compact(
            'entries',
            'totalEntries',
            'totalDebits',
            'totalCredits',
            'manualEntriesCount',
            'subAccounts',
            'costCenters',
            'search',
            'fromDate',
            'toDate',
            'type'
        ));
    }

    /**
     * شاشة إضافة قيد يومية يدوي جديد
     */
    public function create()
    {
        $subAccounts = FinancialAccount::where('level', 5)
            ->where('is_parent', false)
            ->where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $costCenters = CostCenter::where('is_parent', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $nextEntryNo = GeneralLedgerService::generateEntryNo();

        return view('accounting.journal_entries.create', compact(
            'subAccounts',
            'costCenters',
            'nextEntryNo'
        ));
    }

    /**
     * حفظ قيد اليومية اليدوي في قاعدة البيانات
     */
    public function store(Request $request)
    {
        $request->validate([
            'entry_date'  => 'required|date',
            'description' => 'required|string|max:1000',
            'lines'       => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:financial_accounts,id',
            'lines.*.debit'      => 'nullable|numeric|min:0',
            'lines.*.credit'     => 'nullable|numeric|min:0',
        ]);

        try {
            $headerData = [
                'entry_date'  => $request->entry_date,
                'description' => $request->description,
                'is_posted'   => $request->boolean('is_posted', true),
            ];

            $entry = GeneralLedgerService::createManualEntry($headerData, $request->lines);

            return redirect()->route('accounting.journal_entries.show', $entry->id)
                ->with('success', "تم تسجيل قيد اليومية رقم ({$entry->entry_no}) بنجاح بمبلغ إجمالي (" . number_format($entry->total_debit, 2) . " ر.ي).");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * استعراض تفاصيل قيد يومية محدد
     */
    public function show($id)
    {
        $entry = JournalEntry::with(['lines.account', 'lines.costCenter', 'creator', 'poster', 'reversedBy'])
            ->findOrFail($id);

        return view('accounting.journal_entries.show', compact('entry'));
    }

    /**
     * طباعة مستند قيد اليومية A4
     */
    public function print($id)
    {
        $entry = JournalEntry::with(['lines.account', 'lines.costCenter', 'creator', 'poster'])
            ->findOrFail($id);

        return view('accounting.journal_entries.print', compact('entry'));
    }

    /**
     * إلغاء قيد مرحل بقيد عكسي
     */
    public function reverse(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $entry = JournalEntry::findOrFail($id);

        try {
            $reversal = GeneralLedgerService::reverseEntry($entry, $request->reason);

            return redirect()->route('accounting.journal_entries.show', $reversal->id)
                ->with('success', "تم إنشاء القيد العكسي رقم ({$reversal->entry_no}) وإلغاء أثر القيد الأصلي رقم ({$entry->entry_no}) بنجاح.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
