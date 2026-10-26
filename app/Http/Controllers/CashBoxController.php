<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialAccount;
use App\Models\Voucher;
use App\Models\User;
use App\Models\SystemAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CashBoxController extends Controller
{
    /**
     * التحقق من صلاحية الوصول لمركز إدارة الصناديق والبنوك
     */
    protected function checkAccess()
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('manage_cash_boxes') && !$user->hasPermission('manage_accounting') && $user->role !== 'accountant')) {
            abort(403, 'عذراً، ليس لديك صلاحية الوصول إلى شاشة إدارة الصناديق والبنوك.');
        }
    }

    /**
     * عرض دليل وشجرة الصناديق والخزائن المالية والحسابات البنكية
     */
    public function index(Request $request)
    {
        $this->checkAccess();

        $query = FinancialAccount::cashBoxesAndBanks()
            ->with(['parent', 'children.custodian', 'custodian'])
            ->withCount('boxVouchers');

        // البحث بالاسم أو الكود
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('account_name', 'like', "%{$s}%")
                  ->orWhere('account_code', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%")
                  ->orWhere('account_number', 'like', "%{$s}%");
            });
        }

        // فلترة بنوع الحساب (صندوق / بنك)
        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        // فلترة بطبيعة الحركة (قبض / صرف / كاشير / كلاهما)
        if ($request->filled('box_nature')) {
            $query->where('box_nature', $request->box_nature);
        }

        // فلترة بالتصنيف
        if ($request->filled('box_category')) {
            $query->where('box_category', $request->box_category);
        }

        // فلترة بالعملة
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        $allAccounts = $query->orderBy('account_code')->get();

        // تجميع الصناديق في هيكلية شجرية (رئيسية وفرعية)
        $mainBoxes = $allAccounts->whereNull('parent_id')->where('account_type', 'cash_box');
        $banks = $allAccounts->where('account_type', 'bank');
        $orphanSubBoxes = $allAccounts->whereNotNull('parent_id')->where('account_type', 'cash_box');

        // إحصاءات الأرصدة والمؤشرات
        $stats = [
            'total_boxes' => $allAccounts->where('account_type', 'cash_box')->count(),
            'main_boxes_count' => $allAccounts->where('account_type', 'cash_box')->where('box_category', 'main_box')->count(),
            'sub_boxes_count' => $allAccounts->where('box_category', 'sub_box')->count(),
            'pos_boxes_count' => $allAccounts->where('box_nature', 'pos')->count(),
            'banks_count' => $allAccounts->where('account_type', 'bank')->count(),
            
            // إجمالي الأرصدة حسب العملات
            'balance_yer' => $allAccounts->where('currency', 'YER')->sum('current_balance'),
            'balance_sar' => $allAccounts->where('currency', 'SAR')->sum('current_balance'),
            'balance_usd' => $allAccounts->where('currency', 'USD')->sum('current_balance'),
            
            // توزيع حسب طبيعة الحركة
            'receipt_boxes_count' => $allAccounts->whereIn('box_nature', ['receipt', 'pos', 'both'])->count(),
            'payment_boxes_count' => $allAccounts->whereIn('box_nature', ['payment', 'both'])->count(),
        ];

        // قائمة الصناديق الرئيسية لاختيار الصندوق الأب عند إنشاء صندوق فرعي
        $parentBoxes = FinancialAccount::where('account_type', 'cash_box')
            ->whereNull('parent_id')
            ->orderBy('account_name')
            ->get();

        // قائمة كادر المنصة لاختيار أمين / مسؤول الصندوق
        $custodians = User::whereIn('role', ['admin', 'accountant', 'staff', 'supervisor', 'teacher'])
            ->orderBy('name')
            ->get();

        return view('accounting.cash_boxes.index', compact(
            'allAccounts',
            'mainBoxes',
            'banks',
            'orphanSubBoxes',
            'stats',
            'parentBoxes',
            'custodians'
        ));
    }

    /**
     * إضافة صندوق نقدي أو حساب بنكي جديد
     */
    public function store(Request $request)
    {
        $this->checkAccess();

        $request->validate([
            'account_name' => 'required|string|max:150',
            'account_code' => 'nullable|string|max:50|unique:financial_accounts,account_code',
            'account_type' => 'required|in:cash_box,bank',
            'box_nature'   => 'required|in:both,receipt,payment,pos',
            'box_category' => 'required|in:main_box,sub_box,general_revenue,pos,bank,electronic_wallet',
            'parent_id'    => 'nullable|exists:financial_accounts,id',
            'currency'     => 'required|string|max:10',
            'opening_balance' => 'nullable|numeric|min:0',
            'custodian_id' => 'nullable|exists:users,id',
            'bank_name'    => 'nullable|string|max:120',
            'account_number' => 'nullable|string|max:80',
            'description'  => 'nullable|string|max:500',
        ]);

        // توليد رقم الحساب / الكود آلياً في حال تركه فارغاً وفق المعايير القياسية لدليل الحسابات
        $code = $request->account_code;
        if (empty($code)) {
            $code = $this->generateNextAccountCode($request->account_type, $request->parent_id);
        }

        $openingBal = (float)($request->opening_balance ?? 0.00);

        $box = FinancialAccount::create([
            'parent_id'       => $request->box_category === 'sub_box' ? $request->parent_id : null,
            'account_code'    => $code,
            'account_name'    => $request->account_name,
            'account_type'    => $request->account_type,
            'box_nature'      => $request->box_nature,
            'box_category'    => $request->box_category,
            'custodian_id'    => $request->custodian_id,
            'currency'        => $request->currency,
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'opening_balance' => $openingBal,
            'current_balance' => $openingBal,
            'description'     => $request->description,
            'is_active'       => true,
        ]);

        SystemAuditLog::record(
            'cash_box.create',
            "إنشاء حساب مالي جديد ({$box->account_name}) بكود ({$box->account_code}) بطبيعة ({$box->box_nature_label}) وتصنيف ({$box->box_category_label})",
            $box,
            $box->toArray()
        );

        return back()->with('success', "تم إضافة {$box->box_category_label} ({$box->account_name}) بنجاح برقم حساب ({$box->account_code}).");
    }

    /**
     * تحديث بيانات الصندوق أو الحساب البنكي
     */
    public function update(Request $request, $id)
    {
        $this->checkAccess();

        $box = FinancialAccount::findOrFail($id);

        $request->validate([
            'account_name' => 'required|string|max:150',
            'account_code' => ['required', 'string', 'max:50', Rule::unique('financial_accounts', 'account_code')->ignore($box->id)],
            'account_type' => 'required|in:cash_box,bank',
            'box_nature'   => 'required|in:both,receipt,payment,pos',
            'box_category' => 'required|in:main_box,sub_box,general_revenue,pos,bank,electronic_wallet',
            'parent_id'    => 'nullable|exists:financial_accounts,id',
            'currency'     => 'required|string|max:10',
            'custodian_id' => 'nullable|exists:users,id',
            'bank_name'    => 'nullable|string|max:120',
            'account_number' => 'nullable|string|max:80',
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'required|boolean',
        ]);

        // منع ربط الصندوق بنفسه كأب
        if ($request->parent_id && (int)$request->parent_id === (int)$box->id) {
            return back()->with('error', 'لا يمكن تعيين الصندوق كأب لنفسه.');
        }

        $box->update([
            'parent_id'       => $request->box_category === 'sub_box' ? $request->parent_id : null,
            'account_code'    => $request->account_code,
            'account_name'    => $request->account_name,
            'account_type'    => $request->account_type,
            'box_nature'      => $request->box_nature,
            'box_category'    => $request->box_category,
            'custodian_id'    => $request->custodian_id,
            'currency'        => $request->currency,
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'description'     => $request->description,
            'is_active'       => (bool)$request->is_active,
        ]);

        SystemAuditLog::record(
            'cash_box.update',
            "تحديث بيانات الحساب المالي ({$box->account_name}) رقم ({$box->account_code})",
            $box,
            $box->toArray()
        );

        return back()->with('success', "تم تحديث بيانات ({$box->account_name}) بنجاح.");
    }

    /**
     * حذف أو تعطيل الصندوق
     */
    public function destroy($id)
    {
        $this->checkAccess();

        $box = FinancialAccount::withCount(['children', 'boxVouchers'])->findOrFail($id);

        if ($box->children_count > 0) {
            return back()->with('error', '⛔ لا يمكن حذف هذا الصندوق لوجود صناديق فرعية تابعة له. قم بنقل أو حذف الصناديق الفرعية أولاً.');
        }

        if ($box->box_vouchers_count > 0 || abs((float)$box->current_balance) > 0.001) {
            return back()->with('error', "⛔ لا يمكن حذف هذا الصندوق لوجود {$box->box_vouchers_count} حركة مالية/سندات مسجلة عليه ورصيد غير صفري ({$box->formatted_balance}). يمكنك إيقاف تفعيله بدلاً من حذفه لضمان سلامة القيود.");
        }

        $boxName = $box->account_name;
        $box->delete();

        SystemAuditLog::record(
            'cash_box.delete',
            "حذف الصندوق المالي ({$boxName})",
            null,
            ['id' => $id, 'name' => $boxName]
        );

        return back()->with('success', "تم حذف الصندوق ({$boxName}) بنجاح.");
    }

    /**
     * كشف حساب تفصيلي لحركة الصندوق / البنك
     */
    public function statement(Request $request, $id)
    {
        $this->checkAccess();

        $box = FinancialAccount::with(['parent', 'custodian'])->findOrFail($id);

        $vouchersQuery = Voucher::where('cash_or_bank_account_id', $box->id)
            ->with(['account.user', 'creator'])
            ->orderBy('created_at');

        if ($request->filled('from_date')) {
            $vouchersQuery->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $vouchersQuery->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('voucher_type')) {
            $vouchersQuery->where('voucher_type', $request->voucher_type);
        }

        $vouchers = $vouchersQuery->get();

        // حساب إجمالي القبض والصرف
        $totalReceipts = (float)$vouchers->where('voucher_type', 'receipt')->sum('amount');
        $totalPayments = (float)$vouchers->where('voucher_type', 'payment')->sum('amount');
        $netMovement = $totalReceipts - $totalPayments;
        $calculatedBalance = (float)$box->opening_balance + $netMovement;
        $discrepancy = (float)$box->current_balance - $calculatedBalance;

        // جلب سجلات التدقيق المتعلقة بالسندات المحذوفة أو التسويات لتوضيح أي فارق معلق
        $auditTrail = SystemAuditLog::where('action', 'like', '%voucher%')
            ->orWhere('action', 'like', '%cash_box%')
            ->latest()
            ->take(10)
            ->get();

        return view('accounting.cash_boxes.statement', compact(
            'box',
            'vouchers',
            'totalReceipts',
            'totalPayments',
            'netMovement',
            'calculatedBalance',
            'discrepancy',
            'auditTrail'
        ));
    }

    /**
     * تقرير كشف حساب تفصيلي رسمي جاهز للطباعة وتصدير PDF (A4)
     */
    public function printStatement(Request $request, $id)
    {
        $this->checkAccess();

        $box = FinancialAccount::with(['parent', 'custodian'])->findOrFail($id);

        $vouchersQuery = Voucher::where('cash_or_bank_account_id', $box->id)
            ->with(['account.user', 'creator'])
            ->orderBy('created_at');

        if ($request->filled('from_date')) {
            $vouchersQuery->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $vouchersQuery->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('voucher_type')) {
            $vouchersQuery->where('voucher_type', $request->voucher_type);
        }

        $vouchers = $vouchersQuery->get();

        $totalReceipts = (float)$vouchers->where('voucher_type', 'receipt')->sum('amount');
        $totalPayments = (float)$vouchers->where('voucher_type', 'payment')->sum('amount');
        $netMovement = $totalReceipts - $totalPayments;
        $calculatedBalance = (float)$box->opening_balance + $netMovement;

        return view('accounting.cash_boxes.print_statement', compact(
            'box',
            'vouchers',
            'totalReceipts',
            'totalPayments',
            'netMovement',
            'calculatedBalance'
        ));
    }

    /**
     * تسوية جردية ومطابقة رصيد الصندوق أو الحساب البنكي
     */
    public function reconcile(Request $request, $id)
    {
        $this->checkAccess();

        $box = FinancialAccount::findOrFail($id);

        $request->validate([
            'reconcile_type' => 'required|in:set_opening,match_vouchers,custom_balance',
            'custom_amount'  => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string|max:255',
        ]);

        $vouchers = Voucher::where('cash_or_bank_account_id', $box->id)->get();
        $totalReceipts = (float)$vouchers->where('voucher_type', 'receipt')->sum('amount');
        $totalPayments = (float)$vouchers->where('voucher_type', 'payment')->sum('amount');
        $netVouchers = $totalReceipts - $totalPayments;

        $oldOpening = $box->opening_balance;
        $oldCurrent = $box->current_balance;

        if ($request->reconcile_type === 'set_opening') {
            // جعل الفارق كرصيد افتتاحي معتمد
            $diff = (float)$box->current_balance - $netVouchers;
            $box->update([
                'opening_balance' => max(0, $diff),
            ]);
            $msg = "تم اعتماد مبلغ (" . number_format($diff, 2) . " " . $box->currency . ") كرصيد افتتاحي معتمد للصندوق ليتطابق الرصيد الدفتري والكشف بنسبة 100%.";
        } elseif ($request->reconcile_type === 'match_vouchers') {
            // مطابقة رصيد الصندوق مع مجموع السندات الفعلية
            $newBalance = (float)$box->opening_balance + $netVouchers;
            $box->update([
                'current_balance' => $newBalance,
            ]);
            $msg = "تمت التسوية الجردية وضبط رصيد الصندوق الفعلي إلى (" . number_format($newBalance, 2) . " " . $box->currency . ") مطابقاً لحركة السندات المسجلة تماماً.";
        } elseif ($request->reconcile_type === 'custom_balance') {
            $customAmount = (float)($request->custom_amount ?? 0);
            $box->update([
                'current_balance' => $customAmount,
            ]);
            $msg = "تم تعديل الرصيد الفعلي للصندوق بناءً على الجرد النقدي إلى (" . number_format($customAmount, 2) . " " . $box->currency . ").";
        }

        SystemAuditLog::record(
            'cash_box.reconcile',
            "تسوية جردية ومطابقة رصيد ({$box->account_name}): من ({$oldCurrent}) إلى ({$box->current_balance}) - الرصيد الافتتاحي: ({$box->opening_balance})",
            $box,
            [
                'old_current' => $oldCurrent,
                'new_current' => $box->current_balance,
                'old_opening' => $oldOpening,
                'new_opening' => $box->opening_balance,
                'notes'       => $request->notes,
                'type'        => $request->reconcile_type,
            ]
        );

        return back()->with('success', $msg ?? 'تمت التسوية بنجاح.');
    }

    /**
     * توليد الكود التلقائي وفق تسلسل دليل الحسابات
     */
    protected function generateNextAccountCode(string $type, ?int $parentId = null): string
    {
        if ($parentId) {
            $parent = FinancialAccount::find($parentId);
            if ($parent) {
                $subCount = FinancialAccount::where('parent_id', $parentId)->count() + 1;
                return $parent->account_code . '-' . str_pad($subCount, 2, '0', STR_PAD_LEFT);
            }
        }

        if ($type === 'bank') {
            $count = FinancialAccount::where('account_type', 'bank')->count() + 1;
            return '12' . str_pad($count, 2, '0', STR_PAD_LEFT);
        }

        // صناديق رئيسية
        $count = FinancialAccount::where('account_type', 'cash_box')->whereNull('parent_id')->count() + 1;
        return '11' . str_pad($count, 2, '0', STR_PAD_LEFT);
    }
}
