<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\SystemAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChartOfAccountsController extends Controller
{
    /**
     * عرض الشجرة المحاسبية العامة (Chart of Accounts Tree View)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        // جلب الحسابات الجذرية (المستوى 1) مع كافة أبنائها شجرياً
        $query = FinancialAccount::with(['children.children.children.children'])
            ->whereNull('parent_id')
            ->orderBy('account_code');

        if ($category) {
            $query->where('account_category', $category);
        }

        $rootAccounts = $query->get();

        // إحصائيات سريعة
        $totalAccountsCount = FinancialAccount::count();
        $leafAccountsCount = FinancialAccount::where('is_parent', false)->count();
        $totalAssets = FinancialAccount::where('account_category', 'asset')->where('is_parent', false)->sum('current_balance');
        $totalLiabilities = FinancialAccount::where('account_category', 'liability')->where('is_parent', false)->sum('current_balance');

        // قائمة الحسابات الرئيسية الصالحة لتكون أباً عند الإضافة (الرتب 1 إلى 4 فقط؛ لأن الرتبة 5 فرعية حصرية)
        $parentCandidates = FinancialAccount::where('level', '<', 5)
            ->where('is_parent', true)
            ->orderBy('account_code')
            ->get();

        return view('accounting.chart_of_accounts.index', compact(
            'rootAccounts',
            'totalAccountsCount',
            'leafAccountsCount',
            'totalAssets',
            'totalLiabilities',
            'parentCandidates',
            'search',
            'category'
        ));
    }

    /**
     * حفظ حساب جديد في الدليل المحاسبي وفق قاعدة الرتبة الخامسة الصارمة والنوع التحليلي
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_name'      => 'required|string|max:255',
            'parent_id'         => 'required|exists:financial_accounts,id',
            'account_code'      => 'nullable|string|unique:financial_accounts,account_code',
            'nature'            => 'required|in:debit,credit',
            'analytical_type'   => 'nullable|string|in:general,student,teacher,employee,parent,cash_box,bank,customer,supplier,revenue,expense',
            'allow_cost_center' => 'boolean',
            'description'       => 'nullable|string',
        ]);

        $parent = FinancialAccount::findOrFail($request->parent_id);

        // الرتبة الخامسة فرعية حصرية ولا تقبل التفريع
        if ($parent->level >= 5) {
            return back()->with('error', 'عذراً، وفق معايير النظام المحاسبي: الرتبة الخامسة هي الرتبة الفرعية الأخيرة في الدليل المحاسبي ولا تقبل تفريع حسابات تحتها.');
        }

        $newLevel = $parent->level + 1;
        // قاعدة الرتبة: الرتب 1، 2، 3، 4 رئيسية إجبارياً، والرتبة 5 فرعية إجبارياً
        $isParent = ($newLevel < 5);

        // تحديد النوع التحليلي ونوع الحساب التوافقي
        $analyticalType = $request->input('analytical_type') ?: ($parent->analytical_type ?: 'general');
        $accountType = match($analyticalType) {
            'cash_box' => 'cash_box',
            'bank' => 'bank',
            'student', 'parent', 'customer' => 'student',
            'teacher', 'employee', 'supplier' => 'teacher',
            'revenue' => 'revenue',
            'expense' => 'expense',
            default => $parent->account_type,
        };

        // توليد كود آلي قياسي إن لم يدخله المستخدم
        $accountCode = $request->account_code;
        if (empty($accountCode)) {
            $childrenCount = $parent->children()->count() + 1;
            $padLen = ($newLevel == 5) ? 4 : 2;
            $accountCode = $parent->account_code . str_pad($childrenCount, $padLen, '0', STR_PAD_LEFT);
            // التأكد من عدم التكرار
            while (FinancialAccount::where('account_code', $accountCode)->exists()) {
                $childrenCount++;
                $accountCode = $parent->account_code . str_pad($childrenCount, $padLen, '0', STR_PAD_LEFT);
            }
        }

        $account = FinancialAccount::create([
            'parent_id'         => $parent->id,
            'level'             => $newLevel,
            'is_parent'         => $isParent,
            'account_code'      => $accountCode,
            'account_name'      => $request->account_name,
            'account_type'      => $accountType,
            'analytical_type'   => $analyticalType,
            'account_category'  => $parent->account_category,
            'nature'            => $request->nature,
            'allow_cost_center' => $request->boolean('allow_cost_center', false),
            'currency'          => 'YER',
            'opening_balance'   => $request->input('opening_balance', 0.00),
            'current_balance'   => $request->input('opening_balance', 0.00),
            'description'       => $request->description,
            'is_active'         => true,
        ]);

        $typeLabel = $isParent ? "رئيسي (تجميعي - رتبة {$newLevel})" : "فرعي (حركي - رتبة 5)";

        SystemAuditLog::record(
            'chart_of_accounts.create',
            "إضافة حساب جديد بالدليل المحاسبي: [{$accountCode}] {$account->account_name} ({$typeLabel} - تحليلي: {$account->analytical_type_label}) تحت الحساب: [{$parent->account_code}] {$parent->account_name}",
            $account,
            ['code' => $accountCode, 'parent_id' => $parent->id, 'level' => $newLevel, 'is_parent' => $isParent, 'analytical_type' => $analyticalType]
        );

        return back()->with('success', "تم إضافة الحساب المحاسبي [{$accountCode}] {$account->account_name} ({$typeLabel} - {$account->analytical_type_label}) بنجاح.");
    }

    /**
     * تحديث بيانات حساب في الدليل المحاسبي
     */
    public function update(Request $request, $id)
    {
        $account = FinancialAccount::findOrFail($id);

        $request->validate([
            'account_name'      => 'required|string|max:255',
            'nature'            => 'required|in:debit,credit',
            'analytical_type'   => 'nullable|string|in:general,student,teacher,employee,parent,cash_box,bank,customer,supplier,revenue,expense',
            'allow_cost_center' => 'boolean',
            'is_active'         => 'boolean',
            'description'       => 'nullable|string',
        ]);

        $updateData = [
            'account_name'      => $request->account_name,
            'nature'            => $request->nature,
            'allow_cost_center' => $request->boolean('allow_cost_center'),
            'is_active'         => $request->boolean('is_active'),
            'description'       => $request->description,
        ];

        if ($request->filled('analytical_type')) {
            $updateData['analytical_type'] = $request->analytical_type;
            $updateData['account_type'] = match($request->analytical_type) {
                'cash_box' => 'cash_box',
                'bank' => 'bank',
                'student', 'parent', 'customer' => 'student',
                'teacher', 'employee', 'supplier' => 'teacher',
                'revenue' => 'revenue',
                'expense' => 'expense',
                default => $account->account_type,
            };
        }

        $account->update($updateData);

        SystemAuditLog::record(
            'chart_of_accounts.update',
            "تعديل بيانات الحساب المحاسبي: [{$account->account_code}] {$account->account_name}",
            $account,
            ['name' => $request->account_name, 'is_active' => $request->boolean('is_active')]
        );

        return back()->with('success', "تم تحديث بيانات الحساب [{$account->account_code}] بنجاح.");
    }

    /**
     * حذف أو تعطيل حساب محاسبي
     */
    public function destroy($id)
    {
        $account = FinancialAccount::findOrFail($id);

        // منع حذف الحساب إذا كان لديه أبناء أو عليه حركات سابقة
        if ($account->children()->exists()) {
            return back()->with('error', "لا يمكن حذف هذا الحساب لأنه يحتوي على حسابات فرعية تحته. يرجى حذف أو نقل الحسابات الفرعية أولاً.");
        }

        if ($account->vouchers()->exists() || $account->boxVouchers()->exists() || $account->journalLines()->exists()) {
            // تعطيل بدلاً من الحذف
            $account->update(['is_active' => false]);
            return back()->with('success', "تم تعطيل الحساب [{$account->account_code}] بنجاح نظراً لوجود قيود وحركات مالية مسجلة عليه.");
        }

        $account->delete();
        return back()->with('success', "تم حذف الحساب بنجاح.");
    }
}
