<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Models\FinancialAccount;
use App\Models\CostCenter;
use App\Models\Voucher;
use App\Models\VoucherItem;
use App\Models\SystemAuditLog;
use App\Services\GeneralLedgerService;
use App\Helpers\ArabicCurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentRequestController extends Controller
{
    /**
     * عرض قائمة طلبات الصرف والقبض والاعتمادات
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $type = $request->input('type');

        $query = PaymentRequest::with(['requester', 'approver', 'account', 'costCenter', 'voucher'])
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }
        if ($type) {
            $query->where('request_type', $type);
        }

        $requests = $query->paginate(15);

        // إحصائيات سريعة
        $pendingFinancialCount = PaymentRequest::where('status', 'pending_financial')->count();
        $pendingManagerCount = PaymentRequest::where('status', 'pending_manager')->count();
        $approvedCount = PaymentRequest::where('status', 'approved')->count();
        $executedCount = PaymentRequest::where('status', 'executed')->count();

        // حسابات الصناديق والبنوك المتاحة للتنفيذ
        $cashBoxes = FinancialAccount::whereIn('account_type', ['cash_box', 'bank'])
            ->where('is_active', true)
            ->get();

        // الحسابات ومراكز التكلفة المتاحة لطلب الصرف
        $accounts = FinancialAccount::where('is_parent', false)->where('is_active', true)->get();
        $costCenters = CostCenter::where('is_parent', false)->where('is_active', true)->get();

        return view('accounting.payment_requests.index', compact(
            'requests',
            'pendingFinancialCount',
            'pendingManagerCount',
            'approvedCount',
            'executedCount',
            'cashBoxes',
            'accounts',
            'costCenters',
            'status',
            'type'
        ));
    }

    /**
     * حفظ طلب صرف أو توريد جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'request_type'     => 'required|in:payment,receipt',
            'beneficiary_name' => 'required|string|max:255',
            'amount'           => 'required|numeric|min:1',
            'currency'         => 'required|string',
            'purpose'          => 'required|string',
            'account_id'       => 'nullable|exists:financial_accounts,id',
            'cost_center_id'   => 'nullable|exists:cost_centers,id',
            'attachment'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $year = date('Y');
        $prefix = $request->request_type === 'payment' ? 'PR' : 'RR';
        $count = PaymentRequest::whereYear('created_at', $year)->count() + 1;
        $requestNo = "{$prefix}-{$year}-" . str_pad($count, 5, '0', STR_PAD_LEFT);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('payment_requests', 'public');
        }

        $paymentRequest = PaymentRequest::create([
            'request_no'       => $requestNo,
            'request_type'     => $request->request_type,
            'requested_by'     => Auth::id() ?? 1,
            'beneficiary_name' => $request->beneficiary_name,
            'account_id'       => $request->account_id,
            'cost_center_id'   => $request->cost_center_id,
            'amount'           => $request->amount,
            'currency'         => $request->currency,
            'purpose'          => $request->purpose,
            'attachment_path'  => $attachmentPath,
            'status'           => 'pending_manager', // يدخل مسار الاعتماد الإداري مباشرة
        ]);

        SystemAuditLog::record(
            'payment_request.create',
            "إنشاء طلب " . ($request->request_type === 'payment' ? 'صرف' : 'توريد') . " رقم ({$requestNo}) بمبلغ {$request->amount} {$request->currency} للمستفيد: {$request->beneficiary_name}",
            $paymentRequest,
            ['amount' => $request->amount, 'currency' => $request->currency]
        );

        return back()->with('success', "تم إنشاء الطلب المالي رقم ({$requestNo}) بنجاح وهو الآن قيد الاعتماد الإداري.");
    }

    /**
     * اعتماد وموافقة على الطلب المالي
     */
    public function approve(Request $request, $id)
    {
        $item = PaymentRequest::findOrFail($id);

        if (!in_array($item->status, ['draft', 'pending_financial', 'pending_manager'])) {
            return back()->with('error', 'لا يمكن اعتماد هذا الطلب في حالته الحالية.');
        }

        $item->update([
            'status'      => 'approved',
            'approved_by' => Auth::id() ?? 1,
            'approved_at' => now(),
        ]);

        SystemAuditLog::record(
            'payment_request.approve',
            "اعتماد الطلب المالي رقم ({$item->request_no}) بمبلغ {$item->amount} {$item->currency}",
            $item
        );

        return back()->with('success', "تم اعتماد الطلب رقم ({$item->request_no}) وأصبح جاهزاً للتنفيذ والصرف لدى الصندوق.");
    }

    /**
     * رفض الطلب المالي مع بيان السبب
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $item = PaymentRequest::findOrFail($id);
        $item->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        SystemAuditLog::record(
            'payment_request.reject',
            "رفض الطلب المالي رقم ({$item->request_no}) - السبب: {$request->rejection_reason}",
            $item,
            ['reason' => $request->rejection_reason]
        );

        return back()->with('success', "تم رفض الطلب المالي رقم ({$item->request_no}).");
    }

    /**
     * تنفيذ الطلب المعتمد وإصدار سند الصرف أو القبض الفعلي وتحديث الصندوق والقيود
     */
    public function execute(Request $request, $id)
    {
        $item = PaymentRequest::findOrFail($id);

        if ($item->status !== 'approved') {
            return back()->with('error', 'لا يمكن تنفيذ هذا الطلب إلا بعد اعتماده رسمياً.');
        }

        $request->validate([
            'cash_or_bank_account_id' => 'required|exists:financial_accounts,id',
            'payment_method'          => 'required|string',
            'manual_no'               => 'nullable|string',
        ]);

        return DB::transaction(function () use ($item, $request) {
            $cashBox = FinancialAccount::findOrFail($request->cash_or_bank_account_id);
            $amount = (float) $item->amount;
            $voucherType = $item->request_type === 'payment' ? 'payment' : 'receipt';

            $prefix = $voucherType === 'payment' ? 'PAY' : 'REC';
            $count = Voucher::count() + 1;
            $voucherNo = "{$prefix}-" . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            $amountInWords = ArabicCurrencyHelper::tafqeet($amount, $item->currency);

            // تحديد حساب الطرف الآخر
            $targetAccountId = $item->account_id;
            if (!$targetAccountId) {
                // إذا لم يُحدد نربطه بحساب المصروفات العامة أو الإيرادات
                $fallback = $voucherType === 'payment' 
                    ? FinancialAccount::where('account_code', '5204')->first() 
                    : FinancialAccount::where('account_code', '4101')->first();
                $targetAccountId = $fallback?->id ?? $cashBox->id;
            }

            // 1. إنشاء السند المالي
            $voucher = Voucher::create([
                'voucher_no'              => $voucherNo,
                'manual_no'               => $request->manual_no,
                'voucher_type'            => $voucherType,
                'is_compound'             => false,
                'account_id'              => $targetAccountId,
                'cash_or_bank_account_id' => $cashBox->id,
                'amount'                  => $amount,
                'currency'                => $item->currency,
                'exchange_rate'           => 1.0000,
                'amount_local'            => $amount,
                'amount_in_words'         => $amountInWords,
                'payment_method'          => $request->payment_method,
                'statement'               => "تنفيذ طلب {$item->request_no}: {$item->purpose} (المستفيد: {$item->beneficiary_name})",
                'created_by'              => Auth::id() ?? 1,
            ]);

            VoucherItem::create([
                'voucher_id' => $voucher->id,
                'account_id' => $targetAccountId,
                'amount'     => $amount,
                'statement'  => $voucher->statement,
            ]);

            // 2. تحديث رصيد الصندوق
            if ($voucherType === 'payment') {
                $cashBox->decrement('current_balance', $amount);
            } else {
                $cashBox->increment('current_balance', $amount);
            }

            // 3. توليد قيد اليومية العامة الآلي المزدوج
            GeneralLedgerService::recordVoucherEntry($voucher);

            // 4. تحديث حالة الطلب إلى منفذ
            $item->update([
                'status'     => 'executed',
                'voucher_id' => $voucher->id,
            ]);

            SystemAuditLog::record(
                'payment_request.executed',
                "تنفيذ وصرف الطلب المالي ({$item->request_no}) عبر السند رقم ({$voucherNo}) من حساب ({$cashBox->account_name})",
                $voucher,
                ['voucher_id' => $voucher->id, 'box_id' => $cashBox->id]
            );

            return back()->with('success', "تم تنفيذ الطلب بنجاح وإصدار السند المالي رقم ({$voucherNo}) وقيده بالدفاتر اليومية والصندوق 🚀");
        });
    }
}
