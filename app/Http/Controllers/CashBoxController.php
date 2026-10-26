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

        // جلب الحسابات المالية الخاصة بالصناديق والبنوك
        $query = FinancialAccount::where(function ($q) {
                $q->whereIn('analytical_type', ['cash_box', 'bank'])
                  ->orWhereIn('account_type', ['cash_box', 'bank']);
            })
            ->where(function ($q) {
                // استبعاد الحسابات التجميعية العامة للأصول
                $q->where('analytical_type', '!=', 'general')
                  ->orWhere('level', 5);
            })
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
            $t = $request->account_type;
            $query->where(function ($q) use ($t) {
                $q->where('account_type', $t)
                  ->orWhere('analytical_type', $t);
            });
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

        // الحسابات الرئيسية للصناديق المعرفة بالدليل المحاسبي بنوع تحليلي (صندوق)
        $parentBoxes = FinancialAccount::where('is_parent', true)
            ->where('analytical_type', 'cash_box')
            ->with(['children.custodian', 'custodian'])
            ->withCount('boxVouchers')
            ->orderBy('account_code')
            ->get();

        // الحسابات الرئيسية للبنوك المعرفة بالدليل المحاسبي بنوع تحليلي (بنك)
        $parentBanks = FinancialAccount::where('is_parent', true)
            ->where('analytical_type', 'bank')
            ->with(['children.custodian', 'custodian'])
            ->withCount('boxVouchers')
            ->orderBy('account_code')
            ->get();

        // تجميع الصناديق في هيكلية شجرية: الصناديق الرئيسية وما يتفرع منها من صناديق فرعية
        $mainBoxes = $parentBoxes;

        // الحسابات البنكية الفعلية (الفرعية أو المسجلة)
        $banks = $allAccounts->filter(function ($a) {
            return ($a->analytical_type === 'bank' || $a->account_type === 'bank') && !$a->is_parent;
        });
        if ($banks->isEmpty()) {
            $banks = $allAccounts->filter(function ($a) {
                return $a->analytical_type === 'bank' || $a->account_type === 'bank';
            });
        }

        // الصناديق الفرعية التي لم يتم ربطها بأحد الصناديق الرئيسية أعلاه كأبناء مباشرين
        $knownChildIds = $mainBoxes->flatMap->children->pluck('id');
        $orphanSubBoxes = $allAccounts->filter(function ($a) use ($knownChildIds) {
            return ($a->analytical_type === 'cash_box' || $a->account_type === 'cash_box')
                && !$a->is_parent
                && !$knownChildIds->contains($a->id);
        });

        // إحصاءات الأرصدة والمؤشرات
        $leafAccounts = $allAccounts->where('is_parent', false);
        if ($leafAccounts->isEmpty()) {
            $leafAccounts = $allAccounts;
        }

        $stats = [
            'total_boxes' => $leafAccounts->where('analytical_type', 'cash_box')->count() ?: $allAccounts->where('analytical_type', 'cash_box')->count(),
            'main_boxes_count' => $parentBoxes->count(),
            'sub_boxes_count' => $leafAccounts->where('analytical_type', 'cash_box')->count(),
            'pos_boxes_count' => $allAccounts->where('box_nature', 'pos')->count(),
            'banks_count' => $leafAccounts->where('analytical_type', 'bank')->count() ?: $parentBanks->count(),
            
            // إجمالي الأرصدة حسب العملات
            'balance_yer' => $leafAccounts->where('currency', 'YER')->sum('current_balance'),
            'balance_sar' => $leafAccounts->where('currency', 'SAR')->sum('current_balance'),
            'balance_usd' => $leafAccounts->where('currency', 'USD')->sum('current_balance'),
            
            // توزيع حسب طبيعة الحركة
            'receipt_boxes_count' => $allAccounts->whereIn('box_nature', ['receipt', 'pos', 'both'])->count(),
            'payment_boxes_count' => $allAccounts->whereIn('box_nature', ['payment', 'both'])->count(),
        ];

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
            'parentBanks',
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

        // تحديد الحساب الأب: إذا حدد المستخدم حساب رئيسي نستخدمه، وإلا نربطه بالحساب الرئيسي المناسب في الدليل المحاسبي
        $parentId = $request->parent_id;
        if (empty($parentId)) {
            if ($request->account_type === 'bank') {
                $parentId = FinancialAccount::where('account_code', '1112')->value('id')
                    ?? FinancialAccount::where('is_parent', true)->where('analytical_type', 'bank')->value('id');
            } else {
                $parentId = FinancialAccount::where('account_code', '1111')->value('id')
                    ?? FinancialAccount::where('is_parent', true)->where('analytical_type', 'cash_box')->value('id');
            }
        }

        // توليد رقم الحساب / الكود آلياً في حال تركه فارغاً وفق المعايير القياسية لدليل الحسابات
        $code = $request->account_code;
        if (empty($code)) {
            $code = $this->generateNextAccountCode($request->account_type, $parentId);
        }

        $openingBal = (float)($request->opening_balance ?? 0.00);

        $box = FinancialAccount::create([
            'parent_id'       => $parentId,
            'account_code'    => $code,
            'account_name'    => $request->account_name,
            'account_type'    => $request->account_type,
            'analytical_type' => $request->account_type,
            'account_category'=> 'asset',
            'nature'          => 'debit',
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
            'level'           => 5,
            'is_parent'       => false,
        ]);

        SystemAuditLog::record(
            'cash_box.create',
            "إنشاء حساب مالي جديد ({$box->account_name}) بكود ({$box->account_code}) بطبيعة ({$box->box_nature_label}) وتصنيف ({$box->box_category_label}) وتحليلي ({$box->analytical_type_label})",
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

        $parentId = $request->parent_id ?: $box->parent_id;

        $box->update([
            'parent_id'       => $parentId,
            'account_code'    => $request->account_code,
            'account_name'    => $request->account_name,
            'account_type'    => $request->account_type,
            'analytical_type' => $request->account_type,
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
                $candidate = $parent->account_code . str_pad($subCount, 2, '0', STR_PAD_LEFT);
                while (FinancialAccount::where('account_code', $candidate)->exists()) {
                    $subCount++;
                    $candidate = $parent->account_code . str_pad($subCount, 2, '0', STR_PAD_LEFT);
                }
                return $candidate;
            }
        }

        if ($type === 'bank') {
            $parentBank = FinancialAccount::where('account_code', '1112')->first();
            if ($parentBank) {
                return $this->generateNextAccountCode($type, $parentBank->id);
            }
            $count = FinancialAccount::where('account_type', 'bank')->count() + 1;
            return '1112' . str_pad($count, 2, '0', STR_PAD_LEFT);
        }

        $parentBox = FinancialAccount::where('account_code', '1111')->first();
        if ($parentBox) {
            return $this->generateNextAccountCode($type, $parentBox->id);
        }
        $count = FinancialAccount::where('account_type', 'cash_box')->count() + 1;
        return '1111' . str_pad($count, 2, '0', STR_PAD_LEFT);
    }
}
