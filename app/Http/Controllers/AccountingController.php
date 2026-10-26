<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\Voucher;
use App\Models\User;
use App\Models\Course;
use App\Models\Level;
use App\Models\StudyGroup;
use App\Models\EducationalService;
use App\Models\TeacherContract;
use App\Models\TeacherEntitlement;
use App\Models\StudentInstallment;
use App\Models\WhatsAppLog;
use App\Models\SystemAuditLog;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\CostCenter;
use App\Services\GeneralLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingController extends Controller
{
    public function dashboard()
    {
        $cashBox = FinancialAccount::where('account_type', 'cash_box')->first();
        $bankAccount = FinancialAccount::where('account_type', 'bank')->first();

        $totalReceipts = Voucher::where('voucher_type', 'receipt')->sum('amount');
        $totalPayments = Voucher::where('voucher_type', 'payment')->sum('amount');

        $vouchers = Voucher::with(['account.user', 'creator', 'cashOrBankAccount', 'items.account.user'])
            ->latest()
            ->take(15)
            ->get();

        $studentsAccounts = FinancialAccount::where('account_type', 'student')
            ->with('user')
            ->get();

        $teachers = User::whereIn('role', ['teacher', 'supervisor', 'admin', 'accountant', 'staff'])
            ->orderBy('name')
            ->get();

        foreach ($teachers as $t) {
            FinancialAccount::firstOrCreate(
                ['user_id' => $t->id],
                [
                    'account_code' => '22' . str_pad($t->id, 4, '0', STR_PAD_LEFT),
                    'account_name' => 'حساب الكادر: ' . $t->name,
                    'account_type' => 'teacher',
                    'currency' => 'YER',
                    'opening_balance' => 0.00,
                    'current_balance' => 0.00,
                ]
            );

            // ضمان وجود عقد مالي / نمط تعاقد للمستخدم
            if (!$t->teacherContract) {
                TeacherContract::create([
                    'teacher_id' => $t->id,
                    'contract_type' => $t->role === 'supervisor' ? 'fixed_salary' : 'per_course',
                    'calculation_type' => $t->role === 'supervisor' ? 'fixed_monthly' : 'per_hour',
                    'monthly_salary' => $t->role === 'supervisor' ? 80000.00 : 0.00,
                    'default_hourly_rate' => 2500.00,
                    'rate' => $t->role === 'supervisor' ? 80000.00 : 2500.00,
                    'currency' => 'YER',
                    'notes' => $t->role === 'supervisor' ? 'مشرف أكاديمي وإداري' : 'متعاقد بنظام تدريس الدورات والمجموعات',
                    'is_active' => true,
                ]);
            }
        }

        $teachersAccounts = FinancialAccount::where('account_type', 'teacher')
            ->with('user')
            ->get();

        // 6. قيود إثبات مستحقات المعلمين الحديثة
        $entitlements = TeacherEntitlement::with(['teacher', 'studyGroup.course', 'course', 'service', 'creator'])
            ->latest()
            ->take(20)
            ->get();

        $totalEntitlements = TeacherEntitlement::where('status', '!=', 'cancelled')->sum('amount');

        // الدورات والمجموعات والخدمات لاختيارها في نموذج إثبات المستحقات
        $allCourses = Course::with('level')->where('is_active', true)->orderBy('course_order')->get();
        $allStudyGroups = StudyGroup::with(['course.level', 'teachers', 'teacher'])->where('status', 'active')->latest()->get();
        $educationalServices = EducationalService::where('is_active', true)->orderBy('name')->get();

        $teachersWithContracts = User::whereIn('role', ['teacher', 'supervisor', 'admin', 'accountant', 'staff'])
            ->with(['teacherContract', 'financialAccount', 'teachingGroups.course'])
            ->orderBy('name')
            ->get();

        // الصناديق والخزائن المصرحة للقبض وللصرف وللشجرة
        $receiptBoxes = FinancialAccount::receiptAllowed()->orderBy('account_code')->get();
        $paymentBoxes = FinancialAccount::paymentAllowed()->orderBy('account_code')->get();
        $allCashBoxes = FinancialAccount::cashBoxesAndBanks()->with(['parent', 'children', 'custodian'])->orderBy('account_code')->get();

        return view('accounting.dashboard', compact(
            'cashBox',
            'bankAccount',
            'totalReceipts',
            'totalPayments',
            'vouchers',
            'studentsAccounts',
            'teachersAccounts',
            'entitlements',
            'totalEntitlements',
            'allCourses',
            'allStudyGroups',
            'educationalServices',
            'teachersWithContracts',
            'receiptBoxes',
            'paymentBoxes',
            'allCashBoxes'
        ));
    }

    public function showStatement($studentId)
    {
        $user = Auth::user();
        $isChildOfParent = ($user && $user->isParent() && $user->hasPermission('parent.finance_view') && \App\Models\StudentProfile::where('user_id', $studentId)->where('parent_id', $user->id)->exists());

        if (!$user->isAdmin() && !$user->hasPermission('print_statement') && !$user->hasPermission('finance.statements') && !$isChildOfParent) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية استعراض وطباعة كشف الحساب.');
        }

        $student = User::with('financialAccount')->findOrFail($studentId);
        $account = $student->financialAccount;

        // جلب حركات السندات الخاصة بالطالب
        $vouchers = Voucher::where('account_id', $account?->id)->orderBy('created_at')->get();
        $installments = StudentInstallment::with('level')->where('user_id', $student->id)->get();

        return view('accounting.statement', compact('student', 'account', 'vouchers', 'installments'));
    }

    public function storeReceipt(Request $request)
    {
        if (!Auth::user()->hasPermission('create_receipt_voucher')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إصدار سند قبض مالي.');
        }

        $isBatch = $request->filled('items') && is_array($request->items) && count($request->items) > 0;

        if ($isBatch) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency' => 'required|string',
                'payment_method' => 'required|string',
                'cash_or_bank_account_id' => 'nullable|exists:financial_accounts,id',
                'manual_no' => 'nullable|string',
                'statement' => 'required|string',
                'items' => 'required|array|min:1',
                'items.*.student_id' => 'nullable|exists:users,id',
                'items.*.amount' => 'nullable|numeric|min:0.01',
                'items.*.statement' => 'nullable|string',
            ]);

            $filteredItems = array_values(array_filter($request->items, function ($it) {
                return !empty($it['student_id']) && (float)($it['amount'] ?? 0) > 0;
            }));

            if (empty($filteredItems)) {
                return back()->with('error', 'يرجى إضافة سطر تفصيلي واحد على الأقل مع تحديد الطالب والمبلغ.')->withInput();
            }

            $detailSum = (float)collect($filteredItems)->sum('amount');
            $masterAmount = (float)$request->amount;

            // التحقق والتشييك الصارم من تطابق إجمالي الماستر مع مجموع الديتيل
            if (abs($masterAmount - $detailSum) > 0.01) {
                return back()->with('error', "خطأ في المطابقة: المبلغ الإجمالي في الماستر (" . number_format($masterAmount, 2) . ") لا يطابق مجموع أسطر الديتيل (" . number_format($detailSum, 2) . "). الفارق: (" . number_format(abs($masterAmount - $detailSum), 2) . "). يجب أن يتطابقا تماماً!")->withInput();
            }

            if ($request->filled('cash_or_bank_account_id')) {
                $cashBox = FinancialAccount::findOrFail($request->cash_or_bank_account_id);
            } else {
                $cashBox = FinancialAccount::receiptAllowed()->first() 
                    ?? FinancialAccount::where('account_type', 'cash_box')->first();
            }

            $count = Voucher::count() + 1;
            $voucherNo = 'REC-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            $amountInWords = \App\Helpers\ArabicCurrencyHelper::tafqeet($masterAmount, $request->currency);

            $firstStudent = User::with('financialAccount')->find($filteredItems[0]['student_id']);
            $firstAccount = $firstStudent?->financialAccount;

            $voucher = Voucher::create([
                'voucher_no' => $voucherNo,
                'manual_no' => $request->manual_no,
                'voucher_type' => 'receipt',
                'is_compound' => count($filteredItems) > 1,
                'account_id' => $firstAccount?->id ?? 1,
                'cash_or_bank_account_id' => $cashBox?->id ?? 1,
                'amount' => $masterAmount,
                'currency' => $request->currency,
                'exchange_rate' => 1.0000,
                'amount_local' => $masterAmount,
                'amount_in_words' => $amountInWords,
                'payment_method' => $request->payment_method,
                'statement' => $request->statement,
                'created_by' => Auth::id() ?? 1,
            ]);

            // ترحيل بنود الديتيل وتحديث حساب كل طالب على حدة
            foreach ($filteredItems as $it) {
                $sUser = User::with('financialAccount')->find($it['student_id']);
                $sAccount = $sUser?->financialAccount;
                if ($sAccount) {
                    $sAccount->decrement('current_balance', $it['amount']);
                }
                \App\Models\VoucherItem::create([
                    'voucher_id' => $voucher->id,
                    'account_id' => $sAccount?->id ?? $voucher->account_id,
                    'amount' => $it['amount'],
                    'statement' => !empty($it['statement']) ? $it['statement'] : $request->statement,
                ]);
            }

            // زيادة رصيد الصندوق بإجمالي الماستر
            if ($cashBox) {
                $cashBox->increment('current_balance', $masterAmount);
            }

            // توليد قيد اليومية العامة الآلي المزدوج
            \App\Services\GeneralLedgerService::recordVoucherEntry($voucher);

            SystemAuditLog::record(
                'voucher.receipt_batch_create',
                "إصدار سند قبض جماعي مركب رقم ({$voucherNo}) بمبلغ إجمالي {$masterAmount} {$request->currency} لعدد (" . count($filteredItems) . ") طلاب",
                $voucher,
                ['master_amount' => $masterAmount, 'currency' => $request->currency, 'items_count' => count($filteredItems)]
            );

            return back()->with('success', "تم إصدار سند القبض الجماعي رقم ({$voucherNo}) بمبلغ إجمالي (" . number_format($masterAmount, 2) . " {$request->currency}) وترحيله لحسابات الطلاب والصندوق بنجاح 🚀");
        }

        // 1.2 السند الفردي العادي (Single Mode)
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string',
            'payment_method' => 'required|string',
            'cash_or_bank_account_id' => 'nullable|exists:financial_accounts,id',
            'manual_no' => 'nullable|string',
            'statement' => 'required|string',
        ]);

        $student = User::with('financialAccount')->findOrFail($request->student_id);
        $studentAccount = $student->financialAccount;

        // اختيار الصندوق أو البنك المستلم
        if ($request->filled('cash_or_bank_account_id')) {
            $cashBox = FinancialAccount::findOrFail($request->cash_or_bank_account_id);
        } else {
            $cashBox = FinancialAccount::receiptAllowed()->first() 
                ?? FinancialAccount::where('account_type', 'cash_box')->first();
        }

        // توليد رقم سند آلي متسلسل
        $count = Voucher::count() + 1;
        $voucherNo = 'REC-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        $amountInWords = \App\Helpers\ArabicCurrencyHelper::tafqeet((float)$request->amount, $request->currency);

        $voucher = Voucher::create([
            'voucher_no' => $voucherNo,
            'manual_no' => $request->manual_no,
            'voucher_type' => 'receipt',
            'is_compound' => false,
            'account_id' => $studentAccount->id,
            'cash_or_bank_account_id' => $cashBox?->id ?? 1,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'exchange_rate' => 1.0000,
            'amount_local' => $request->amount,
            'amount_in_words' => $amountInWords,
            'payment_method' => $request->payment_method,
            'statement' => $request->statement,
            'created_by' => Auth::id() ?? 1,
        ]);

        \App\Models\VoucherItem::create([
            'voucher_id' => $voucher->id,
            'account_id' => $studentAccount->id,
            'amount' => $request->amount,
            'statement' => $request->statement,
        ]);

        // تحديث رصيد حساب الطالب المالي (تخفيض المديونية)
        $studentAccount->decrement('current_balance', $request->amount);

        // زيادة رصيد الصندوق
        if ($cashBox) {
            $cashBox->increment('current_balance', $request->amount);
        }

        // توليد قيد اليومية العامة الآلي المزدوج
        \App\Services\GeneralLedgerService::recordVoucherEntry($voucher);

        $currencyArabic = \App\Helpers\ArabicCurrencyHelper::currencyName($request->currency);
        $formattedAmount = number_format($request->amount, 2);

        // إرسال إشعار وسند القبض عبر الواتساب للطالب
        $voucherUrl = route('accounting.voucher.print', $voucher->id);
        $messageBody = \App\Services\WhatsAppService::renderTemplate(
            \App\Models\SiteSetting::get('wa_template_receipt_voucher_body', "عزيزي الطالب {name}، تم استلام دفعة مالية بقيمة {amount}. سند القبض رقم ({voucher_no}) معتمد ومرفق بحسابك في {school_name}.\n\n📄 رابط استعراض وطباعة سند القبض المالي:\n{link}"),
            [
                '{name}' => $student->name,
                '{amount}' => "{$formattedAmount} {$currencyArabic}",
                '{voucher_no}' => $voucherNo,
                '{link}' => $voucherUrl,
            ]
        );

        $sendResult = \App\Services\WhatsAppService::send(
            $student->phone_whatsapp ?: '',
            $messageBody,
            $voucherUrl,
            $student->id,
            'receipt_voucher'
        );

        if (!empty($sendResult['wa_url']) && ($sendResult['status'] ?? '') !== 'sent') {
            session()->flash('whatsapp_direct_url', $sendResult['wa_url']);
            session()->flash('whatsapp_recipient_name', $student->name);
            session()->flash('whatsapp_voucher_no', $voucherNo);
        }

        SystemAuditLog::record(
            'voucher.receipt_create',
            "إصدار سند قبض مالي رقم ({$voucherNo}) بمبلغ {$request->amount} {$request->currency} للطالب ({$student->name})",
            $voucher,
            ['amount' => $request->amount, 'currency' => $request->currency, 'student_id' => $student->id]
        );

        return back()->with('success', "تم إصدار سند القبض رقم ({$voucherNo}) بنجاح وإرسال الإشعار لواتساب الطالب.");
    }

    public function storePayment(Request $request)
    {
        if (!Auth::user()->hasPermission('create_payment_voucher')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إصدار سند صرف مالي.');
        }

        $isBatch = $request->filled('items') && is_array($request->items) && count($request->items) > 0;

        if ($isBatch) {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'currency' => 'required|string',
                'payment_method' => 'required|string',
                'cash_or_bank_account_id' => 'nullable|exists:financial_accounts,id',
                'manual_no' => 'nullable|string',
                'statement' => 'required|string',
                'items' => 'required|array|min:1',
                'items.*.teacher_id' => 'nullable|exists:users,id',
                'items.*.amount' => 'nullable|numeric|min:0.01',
                'items.*.statement' => 'nullable|string',
            ]);

            $filteredItems = array_values(array_filter($request->items, function ($it) {
                return !empty($it['teacher_id']) && (float)($it['amount'] ?? 0) > 0;
            }));

            if (empty($filteredItems)) {
                return back()->with('error', 'يرجى إضافة سطر تفصيلي واحد على الأقل مع تحديد المستفيد والمبلغ.')->withInput();
            }

            $detailSum = (float)collect($filteredItems)->sum('amount');
            $masterAmount = (float)$request->amount;

            // التحقق من تطابق إجمالي الماستر مع مجموع الديتيل
            if (abs($masterAmount - $detailSum) > 0.01) {
                return back()->with('error', "خطأ في المطابقة: المبلغ الإجمالي في الماستر (" . number_format($masterAmount, 2) . ") لا يطابق مجموع أسطر الديتيل (" . number_format($detailSum, 2) . "). الفارق: (" . number_format(abs($masterAmount - $detailSum), 2) . "). يجب أن يتطابقا تماماً!")->withInput();
            }

            if ($request->filled('cash_or_bank_account_id')) {
                $cashBox = FinancialAccount::findOrFail($request->cash_or_bank_account_id);
            } else {
                $cashBox = FinancialAccount::paymentAllowed()->first() 
                    ?? FinancialAccount::where('account_type', 'cash_box')->first();
            }

            $count = Voucher::count() + 1;
            $voucherNo = 'PAY-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            $amountInWords = \App\Helpers\ArabicCurrencyHelper::tafqeet($masterAmount, $request->currency);

            $firstTeacher = User::with('financialAccount')->find($filteredItems[0]['teacher_id']);
            $firstAccount = $firstTeacher?->financialAccount ?? FinancialAccount::create([
                'user_id' => $firstTeacher->id,
                'account_code' => '22' . str_pad($firstTeacher->id, 4, '0', STR_PAD_LEFT),
                'account_name' => 'حساب المستفيد: ' . $firstTeacher->name,
                'account_type' => 'teacher',
                'currency' => $request->currency,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
            ]);

            $voucher = Voucher::create([
                'voucher_no' => $voucherNo,
                'manual_no' => $request->manual_no,
                'voucher_type' => 'payment',
                'is_compound' => count($filteredItems) > 1,
                'account_id' => $firstAccount->id,
                'cash_or_bank_account_id' => $cashBox?->id ?? 1,
                'amount' => $masterAmount,
                'currency' => $request->currency,
                'exchange_rate' => 1.0000,
                'amount_local' => $masterAmount,
                'amount_in_words' => $amountInWords,
                'payment_method' => $request->payment_method,
                'statement' => $request->statement,
                'created_by' => Auth::id() ?? 1,
            ]);

            foreach ($filteredItems as $it) {
                $tUser = User::with('financialAccount')->find($it['teacher_id']);
                $tAccount = $tUser?->financialAccount ?? FinancialAccount::create([
                    'user_id' => $tUser->id,
                    'account_code' => '22' . str_pad($tUser->id, 4, '0', STR_PAD_LEFT),
                    'account_name' => 'حساب المستفيد: ' . $tUser->name,
                    'account_type' => 'teacher',
                    'currency' => $request->currency,
                    'opening_balance' => 0.00,
                    'current_balance' => 0.00,
                ]);

                $tAccount->decrement('current_balance', $it['amount']);

                \App\Models\VoucherItem::create([
                    'voucher_id' => $voucher->id,
                    'account_id' => $tAccount->id,
                    'amount' => $it['amount'],
                    'statement' => !empty($it['statement']) ? $it['statement'] : $request->statement,
                ]);
            }

            // تخفيض رصيد الصندوق بمبلغ الماستر الإجمالي
            if ($cashBox) {
                $cashBox->decrement('current_balance', $masterAmount);
            }

            // توليد قيد اليومية العامة الآلي المزدوج
            \App\Services\GeneralLedgerService::recordVoucherEntry($voucher);

            SystemAuditLog::record(
                'voucher.payment_batch_create',
                "إصدار سند صرف جماعي مركب رقم ({$voucherNo}) بمبلغ إجمالي {$masterAmount} {$request->currency} لعدد (" . count($filteredItems) . ") مستفيدين",
                $voucher,
                ['master_amount' => $masterAmount, 'currency' => $request->currency, 'items_count' => count($filteredItems)]
            );

            return back()->with('success', "تم إصدار سند الصرف الجماعي رقم ({$voucherNo}) بمبلغ إجمالي (" . number_format($masterAmount, 2) . " {$request->currency}) وخصمه من الصندوق بنجاح 💸");
        }

        // 2.2 السند الفردي العادي (Single Mode)
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string',
            'payment_method' => 'required|string',
            'cash_or_bank_account_id' => 'nullable|exists:financial_accounts,id',
            'manual_no' => 'nullable|string',
            'statement' => 'required|string',
        ]);

        $teacher = User::with('financialAccount')->findOrFail($request->teacher_id);
        $teacherAccount = $teacher->financialAccount ?? FinancialAccount::create([
            'user_id' => $teacher->id,
            'account_code' => '22' . str_pad($teacher->id, 4, '0', STR_PAD_LEFT),
            'account_name' => 'حساب المعلم: ' . $teacher->name,
            'account_type' => 'teacher',
            'currency' => $request->currency,
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
        ]);

        // اختيار الصندوق أو البنك الصارف
        if ($request->filled('cash_or_bank_account_id')) {
            $cashBox = FinancialAccount::findOrFail($request->cash_or_bank_account_id);
        } else {
            $cashBox = FinancialAccount::paymentAllowed()->first() 
                ?? FinancialAccount::where('account_type', 'cash_box')->first();
        }

        $count = Voucher::count() + 1;
        $voucherNo = 'PAY-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
        $amountInWords = \App\Helpers\ArabicCurrencyHelper::tafqeet((float)$request->amount, $request->currency);

        $voucher = Voucher::create([
            'voucher_no' => $voucherNo,
            'manual_no' => $request->manual_no,
            'voucher_type' => 'payment',
            'is_compound' => false,
            'account_id' => $teacherAccount->id,
            'cash_or_bank_account_id' => $cashBox?->id ?? 1,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'exchange_rate' => 1.0000,
            'amount_local' => $request->amount,
            'amount_in_words' => $amountInWords,
            'payment_method' => $request->payment_method,
            'statement' => $request->statement,
            'created_by' => Auth::id() ?? 1,
        ]);

        \App\Models\VoucherItem::create([
            'voucher_id' => $voucher->id,
            'account_id' => $teacherAccount->id,
            'amount' => $request->amount,
            'statement' => $request->statement,
        ]);

        // تخفيض رصيد الصندوق النقدي
        if ($cashBox) {
            $cashBox->decrement('current_balance', $request->amount);
        }

        // تحديث رصيد المعلم
        $teacherAccount->decrement('current_balance', $request->amount);

        // توليد قيد اليومية العامة الآلي المزدوج
        \App\Services\GeneralLedgerService::recordVoucherEntry($voucher);

        $currencyArabic = \App\Helpers\ArabicCurrencyHelper::currencyName($request->currency);
        $formattedAmount = number_format($request->amount, 2);

        $voucherUrl = route('accounting.voucher.print', $voucher->id);
        $messageBody = \App\Services\WhatsAppService::renderTemplate(
            \App\Models\SiteSetting::get('wa_template_payment_voucher_body', "أستاذنا الفاضل {name}، تم صرف مستحقاتكم المالية بمبلغ {amount} بموجب سند الصرف رقم ({voucher_no}) في {school_name}.\n\n📄 رابط استعراض وطباعة سند الصرف:\n{link}\n\nشكراً لجهودكم وعطائكم المستمر."),
            [
                '{name}' => $teacher->name,
                '{amount}' => "{$formattedAmount} {$currencyArabic}",
                '{voucher_no}' => $voucherNo,
                '{link}' => $voucherUrl,
            ]
        );

        $sendResult = \App\Services\WhatsAppService::send(
            $teacher->phone_whatsapp ?: '',
            $messageBody,
            $voucherUrl,
            $teacher->id,
            'payment_voucher'
        );

        if (!empty($sendResult['wa_url']) && ($sendResult['status'] ?? '') !== 'sent') {
            session()->flash('whatsapp_direct_url', $sendResult['wa_url']);
            session()->flash('whatsapp_recipient_name', $teacher->name);
            session()->flash('whatsapp_voucher_no', $voucherNo);
        }

        SystemAuditLog::record(
            'voucher.payment_create',
            "إصدار سند صرف مالي رقم ({$voucherNo}) بمبلغ {$request->amount} {$request->currency} للمعلم ({$teacher->name})",
            $voucher,
            ['amount' => $request->amount, 'currency' => $request->currency, 'teacher_id' => $teacher->id]
        );

        return back()->with('success', "تم إصدار سند الصرف رقم ({$voucherNo}) بنجاح وقيد المبلغ بحساب المعلم.");
    }

    public function updateVoucher(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('edit_voucher')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل السندات المالية.');
        }

        $voucher = Voucher::with('items')->findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'cash_or_bank_account_id' => 'required|exists:financial_accounts,id',
            'statement' => 'required|string|max:255',
            'manual_no' => 'nullable|string|max:100',
            'payment_method' => 'required|string',
        ]);

        $oldAmount = (float)$voucher->amount;
        $newAmount = (float)$request->amount;
        $diff = $newAmount - $oldAmount;

        $oldCashBoxId = (int)$voucher->cash_or_bank_account_id;
        $newCashBoxId = (int)$request->cash_or_bank_account_id;

        $oldCashBox = FinancialAccount::find($oldCashBoxId);
        $newCashBox = FinancialAccount::find($newCashBoxId);
        $partyAccount = FinancialAccount::find($voucher->account_id);

        if ($voucher->voucher_type === 'receipt') {
            // سند قبض:
            // تعديل الصندوق
            if ($oldCashBoxId === $newCashBoxId) {
                if ($oldCashBox && abs($diff) > 0.001) {
                    $oldCashBox->increment('current_balance', $diff);
                }
            } else {
                if ($oldCashBox) $oldCashBox->decrement('current_balance', $oldAmount);
                if ($newCashBox) $newCashBox->increment('current_balance', $newAmount);
            }

            // تعديل حساب الطرف الثاني (الطالب)
            if ($partyAccount && abs($diff) > 0.001) {
                $partyAccount->decrement('current_balance', $diff);
            }
        } elseif ($voucher->voucher_type === 'payment') {
            // سند صرف:
            // تعديل الصندوق
            if ($oldCashBoxId === $newCashBoxId) {
                if ($oldCashBox && abs($diff) > 0.001) {
                    $oldCashBox->decrement('current_balance', $diff);
                }
            } else {
                if ($oldCashBox) $oldCashBox->increment('current_balance', $oldAmount);
                if ($newCashBox) $newCashBox->decrement('current_balance', $newAmount);
            }

            // تعديل حساب الطرف الثاني (المعلم)
            if ($partyAccount && abs($diff) > 0.001) {
                $partyAccount->decrement('current_balance', $diff);
            }
        }

        // تحديث أسطر الديتيل إذا كان هناك سطر واحد مطابق
        if ($voucher->items->count() === 1) {
            $voucher->items->first()->update([
                'amount' => $newAmount,
                'statement' => $request->statement,
            ]);
        }

        $amountInWords = \App\Helpers\ArabicCurrencyHelper::tafqeet($newAmount, $voucher->currency);

        $voucher->update([
            'amount' => $newAmount,
            'amount_local' => $newAmount,
            'amount_in_words' => $amountInWords,
            'cash_or_bank_account_id' => $newCashBoxId,
            'statement' => $request->statement,
            'manual_no' => $request->manual_no,
            'payment_method' => $request->payment_method,
        ]);

        $oldBoxName = $oldCashBox ? $oldCashBox->account_name : "صندوق #{$oldCashBoxId}";
        $newBoxName = $newCashBox ? $newCashBox->account_name : "صندوق #{$newCashBoxId}";

        SystemAuditLog::record(
            'voucher.update',
            "تحديث وتعديل بيانات ومبلغ السند المالي رقم ({$voucher->voucher_no}) من ({$oldAmount}) إلى ({$newAmount}) {$voucher->currency}",
            $voucher,
            [
                'old_values' => [
                    'رقم السند' => $voucher->voucher_no,
                    'المبلغ قبل التعديل' => number_format($oldAmount, 2) . " {$voucher->currency}",
                    'الصندوق / الحساب' => $oldBoxName,
                    'البيان' => $oldStatement,
                    'طريقة السداد' => $voucher->payment_method ?? 'نقداً',
                ],
                'new_values' => [
                    'رقم السند' => $voucher->voucher_no,
                    'المبلغ بعد التعديل' => number_format($newAmount, 2) . " {$voucher->currency}",
                    'الصندوق / الحساب' => $newBoxName,
                    'البيان' => $request->statement,
                    'طريقة السداد' => $request->payment_method ?? 'نقداً',
                ],
                'diff' => [
                    'فارق المبلغ' => ($diff > 0 ? '+' : '') . number_format($diff, 2) . " {$voucher->currency}",
                ],
                'old_amount' => $oldAmount,
                'new_amount' => $newAmount,
                'old_box' => $oldCashBoxId,
                'new_box' => $newCashBoxId,
            ]
        );

        return back()->with('success', "تم تحديث بيانات ومبلغ السند ({$voucher->voucher_no}) وضبط الأثر المالي على الحسابات بنجاح 💾");
    }

    public function destroyVoucher($id)
    {
        if (!Auth::user()->hasPermission('delete_voucher')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية حذف أو إلغاء السندات المالية.');
        }

        $voucher = Voucher::with('items')->findOrFail($id);
        $cashBox = FinancialAccount::find($voucher->cash_or_bank_account_id);

        // عكس الأثر المالي للسند قبل الحذف
        if ($voucher->items && $voucher->items->count() > 0) {
            foreach ($voucher->items as $item) {
                $itemAcc = FinancialAccount::find($item->account_id);
                if ($itemAcc) {
                    if ($voucher->voucher_type === 'receipt') {
                        // إعادة المبلغ لذمة الطالب
                        $itemAcc->increment('current_balance', $item->amount);
                    } else {
                        // إعادة المبلغ لاستحقاق المعلم
                        $itemAcc->increment('current_balance', $item->amount);
                    }
                }
            }

            // عكس الأثر المالي من الصندوق بمبلغ الماستر
            if ($cashBox) {
                if ($voucher->voucher_type === 'receipt') {
                    $cashBox->decrement('current_balance', $voucher->amount);
                } else {
                    $cashBox->increment('current_balance', $voucher->amount);
                }
            }
        } else {
            $otherAccount = FinancialAccount::find($voucher->account_id);
            if ($voucher->voucher_type === 'receipt') {
                if ($otherAccount) {
                    $otherAccount->increment('current_balance', $voucher->amount);
                }
                if ($cashBox) {
                    $cashBox->decrement('current_balance', $voucher->amount);
                }
            } elseif ($voucher->voucher_type === 'payment') {
                if ($otherAccount) {
                    $otherAccount->increment('current_balance', $voucher->amount);
                }
                if ($cashBox) {
                    $cashBox->increment('current_balance', $voucher->amount);
                }
            }
        }

        $voucherNo = $voucher->voucher_no;
        $voucherAmount = (float)$voucher->amount;
        $voucherCurrency = $voucher->currency;
        $voucherTypeAr = match($voucher->voucher_type) {
            'receipt' => 'سند قبض مالي',
            'payment' => 'سند صرف مالي',
            default => 'سند قيد يومية',
        };
        $cashBoxName = $cashBox ? $cashBox->account_name : 'غير محدد';
        $beneficiary = $voucher->account?->user?->name ?? $voucher->statement ?? 'غير محدد';

        SystemAuditLog::record(
            'voucher.delete',
            "إلغاء وحذف {$voucherTypeAr} رقم ({$voucherNo}) بمبلغ " . number_format($voucherAmount, 2) . " {$voucherCurrency} وعكس أثره المالي على الحسابات.",
            null,
            [
                'old_values' => [
                    'رقم السند المحذوف' => $voucherNo,
                    'نوع السند' => $voucherTypeAr,
                    'المبلغ المحذوف' => number_format($voucherAmount, 2) . " {$voucherCurrency}",
                    'الحساب / المستفيد' => $beneficiary,
                    'الصندوق المتأثر' => $cashBoxName,
                    'تاريخ السند' => $voucher->voucher_date,
                    'البيان' => $voucher->statement,
                ],
                'new_values' => [
                    'الحالة' => 'ملغي ومحذوف نهائياً من السجلات 🗑️',
                    'الأثر المالي' => 'تم عكس المبلغ وإرجاع الرصيد للحسابات بنجاح',
                ],
                'voucher_no' => $voucherNo,
                'amount' => $voucherAmount,
                'currency' => $voucherCurrency,
            ]
        );

        $voucher->delete();

        return back()->with('success', "تم إلغاء وحذف السند رقم ({$voucherNo}) وإعادة ضبط أرصدة الحسابات المالية بنجاح.");
    }

    public function printVoucher($id)
    {
        $voucher = Voucher::with(['account.user', 'creator', 'items.account.user', 'cashOrBankAccount'])->findOrFail($id);
        return view('accounting.print_voucher', compact('voucher'));
    }

    /**
     * شاشة إدارة الأسعار المالية للدورات والخدمات والتعاقدات
     */
    public function pricingAndServices()
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasPermission('curriculum.pricing') && !$user->hasPermission('finance.view')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية استعراض شاشة التسعير المالي.');
        }

        $courses = Course::with('level')->orderBy('course_order')->get();
        $levels = Level::with('courses')->orderBy('level_order')->get();
        $services = EducationalService::latest()->get();
        $teachers = User::whereIn('role', ['teacher', 'supervisor', 'admin', 'accountant', 'staff'])
            ->with(['teacherContract', 'financialAccount', 'teachingGroups.course'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        // ضمان وجود عقد مالي لكل معلم
        foreach ($teachers as $t) {
            if (!$t->teacherContract) {
                TeacherContract::create([
                    'teacher_id' => $t->id,
                    'contract_type' => 'per_course',
                    'calculation_type' => 'per_hour',
                    'monthly_salary' => 0.00,
                    'default_hourly_rate' => 2500.00,
                    'rate' => 2500.00,
                    'currency' => 'YER',
                    'notes' => 'متعاقد بنظام تدريس الدورات والمجموعات بالسعر المعتمد',
                    'is_active' => true,
                ]);
            }
        }
        $teachers->load('teacherContract');

        return view('accounting.pricing_and_services', compact('courses', 'levels', 'services', 'teachers'));
    }

    /**
     * تحديث محتوى الدورة الكامل (الاسم، المستوى، الساعات، التسعيرة، أجر المعلم)
     */
    public function updateCoursePricing(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasPermission('curriculum.pricing') && !$user->hasPermission('manage_courses') && !$user->hasPermission('finance.voucher_edit')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل بيانات وتسعيرة الدورات.');
        }

        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'level_id' => 'nullable|exists:levels,id',
            'fee_amount' => 'required|numeric|min:0',
            'teacher_fee' => 'required|numeric|min:0',
            'teacher_hourly_rate' => 'nullable|numeric|min:0',
            'hours' => 'required|integer|min:1',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'nullable',
        ]);

        $nameAr = $request->filled('name_ar') ? trim($request->name_ar) : $course->name_ar;
        $nameEn = $request->filled('name_en') ? trim($request->name_en) : $course->name_en;
        $fullName = $request->filled('name')
            ? trim($request->name)
            : (($nameAr && $nameEn) ? "{$nameEn} ({$nameAr})" : ($nameAr ?: ($nameEn ?: $course->name)));

        $hours = (int)$validated['hours'];
        $teacherFee = (float)$validated['teacher_fee'];
        
        $teacherHourlyRate = $request->filled('teacher_hourly_rate') && (float)$request->teacher_hourly_rate > 0
            ? (float)$request->teacher_hourly_rate
            : ($hours > 0 ? round($teacherFee / $hours, 2) : 0.00);

        $oldFee = (float)$course->fee_amount;
        $oldTeacherFee = (float)$course->teacher_fee;
        $oldHours = (int)$course->hours;
        $oldCurrency = $course->currency;
        $oldCourseName = $course->name;

        $course->update([
            'name' => $fullName,
            'name_ar' => $nameAr,
            'name_en' => $nameEn,
            'level_id' => $validated['level_id'] ?? $course->level_id,
            'hours' => $hours,
            'fee_amount' => $validated['fee_amount'],
            'teacher_fee' => $teacherFee,
            'teacher_hourly_rate' => $teacherHourlyRate,
            'currency' => $validated['currency'],
            'description' => $validated['description'] ?? $course->description,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
            'updated_by' => Auth::id(),
        ]);

        $feeDiff = (float)$validated['fee_amount'] - $oldFee;
        $teacherFeeDiff = $teacherFee - $oldTeacherFee;

        SystemAuditLog::record(
            'course.pricing_update',
            "تحديث محتوى وتسعيرة الدورة [{$course->name}]: رسوم الطالب (" . number_format($oldFee, 2) . " -> " . number_format((float)$validated['fee_amount'], 2) . " {$validated['currency']})، أجر المعلم (" . number_format($oldTeacherFee, 2) . " -> " . number_format($teacherFee, 2) . " {$validated['currency']})",
            $course,
            [
                'old_values' => [
                    'اسم الدورة' => $oldCourseName,
                    'رسوم الطالب السابقة' => number_format($oldFee, 2) . " {$oldCurrency}",
                    'أجر المعلم السابق' => number_format($oldTeacherFee, 2) . " {$oldCurrency}",
                    'عدد الساعات السابق' => "{$oldHours} ساعة",
                ],
                'new_values' => [
                    'اسم الدورة' => $fullName,
                    'رسوم الطالب الجديدة' => number_format((float)$validated['fee_amount'], 2) . " {$validated['currency']}",
                    'أجر المعلم الجديد' => number_format($teacherFee, 2) . " {$validated['currency']}",
                    'عدد الساعات الجديد' => "{$hours} ساعة",
                ],
                'diff' => [
                    'تغير رسم الطالب' => ($feeDiff > 0 ? '+' : '') . number_format($feeDiff, 2) . " {$validated['currency']}",
                    'تغير أجر المعلم' => ($teacherFeeDiff > 0 ? '+' : '') . number_format($teacherFeeDiff, 2) . " {$validated['currency']}",
                ],
                'old_fee' => $oldFee,
                'new_fee' => (float)$validated['fee_amount'],
            ]
        );

        return back()->with('success', "تم تحديث محتوى وتسعيرة دورة [{$course->name}] بنجاح.");
    }

    /**
     * إضافة دورة تعليمية جديدة وتسعيرها مباشرة من الشاشة المالية
     */
    public function storeCourseFromPricing(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->hasPermission('curriculum.pricing') && !$user->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إضافة الدورات.');
        }

        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'hours' => 'required|integer|min:1',
            'fee_amount' => 'required|numeric|min:0',
            'teacher_fee' => 'required|numeric|min:0',
            'teacher_hourly_rate' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
        ]);

        $nameAr = trim($validated['name_ar']);
        $nameEn = $request->filled('name_en') ? trim($validated['name_en']) : null;
        $fullName = ($nameAr && $nameEn) ? "{$nameEn} ({$nameAr})" : ($nameAr ?: $nameEn);

        $nextOrder = (Course::where('level_id', $validated['level_id'])->max('course_order') ?? 0) + 1;

        $hours = (int)$validated['hours'];
        $teacherFee = (float)$validated['teacher_fee'];
        $hourly = $request->filled('teacher_hourly_rate') && (float)$validated['teacher_hourly_rate'] > 0
            ? (float)$validated['teacher_hourly_rate']
            : ($hours > 0 ? round($teacherFee / $hours, 2) : 0.00);

        $course = Course::create([
            'level_id' => $validated['level_id'],
            'name' => $fullName,
            'name_ar' => $nameAr,
            'name_en' => $nameEn,
            'hours' => $hours,
            'fee_amount' => $validated['fee_amount'],
            'teacher_fee' => $teacherFee,
            'teacher_hourly_rate' => $hourly,
            'currency' => $validated['currency'],
            'course_order' => $nextOrder,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        SystemAuditLog::record(
            'course.create_from_pricing',
            "إضافة دورة جديدة وتسعيرها: [{$fullName}] بسعر طالب ({$course->fee_amount} {$course->currency}) وأجر معلم ({$course->teacher_fee} {$course->currency})",
            $course,
            $validated
        );

        return back()->with('success', "تمت إضافة الدورة التعليمية [{$fullName}] وتسعيرها بنجاح.");
    }

    /**
     * إضافة خدمة تعليمية جديدة للدليل
     */
    public function storeEducationalService(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('finance.voucher_edit')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إضافة خدمات تعليمية.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'service_type' => 'required|string',
            'default_fee' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'unit_type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $count = EducationalService::count() + 1;
        $code = 'SRV-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $service = EducationalService::create([
            'service_code' => $code,
            'name' => $validated['name'],
            'service_type' => $validated['service_type'],
            'default_fee' => $validated['default_fee'],
            'currency' => $validated['currency'],
            'unit_type' => $validated['unit_type'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        SystemAuditLog::record(
            'service.create',
            "إضافة خدمة تعليمية جديدة [{$service->name}] برمز ({$code}) وبسعر افتراضي ({$service->default_fee} {$service->currency})",
            $service,
            $validated
        );

        return back()->with('success', "تمت إضافة الخدمة التعليمية [{$service->name}] بنجاح.");
    }

    /**
     * تعديل بيانات وسعر خدمة تعليمية
     */
    public function updateEducationalService(Request $request, $id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('finance.voucher_edit')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل الخدمات.');
        }

        $service = EducationalService::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'service_type' => 'required|string',
            'default_fee' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'unit_type' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $service->update([
            'name' => $validated['name'],
            'service_type' => $validated['service_type'],
            'default_fee' => $validated['default_fee'],
            'currency' => $validated['currency'],
            'unit_type' => $validated['unit_type'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', "تم تحديث الخدمة التعليمية [{$service->name}] بنجاح.");
    }

    /**
     * حذف أو تعطيل خدمة تعليمية
     */
    public function destroyEducationalService($id)
    {
        if (!Auth::user()->isAdmin()) {
            return back()->with('error', 'عذراً، حذف الخدمات مخصص للإدارة العليا فقط.');
        }

        $service = EducationalService::withCount('entitlements')->findOrFail($id);

        if ($service->entitlements_count > 0) {
            $service->update(['is_active' => false]);
            return back()->with('warning', "تم تعطيل الخدمة [{$service->name}] بدلاً من حذفها لوجود مستحقات مرتبطة بها.");
        }

        $serviceName = $service->name;
        $service->delete();

        return back()->with('success', "تم حذف الخدمة التعليمية [{$serviceName}] بنجاح.");
    }

    /**
     * تحديث عقد المعلم ونمط التوظيف (موظف براتب ثابت أو متعاقد بالدورات)
     */
    public function updateTeacherContract(Request $request, $teacherId)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('finance.voucher_edit')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل عقود المعلمين.');
        }

        $teacher = User::whereIn('role', ['teacher', 'supervisor', 'admin', 'accountant', 'staff'])->findOrFail($teacherId);

        $validated = $request->validate([
            'contract_type' => 'required|in:fixed_salary,per_course,hourly,percentage,services_only',
            'monthly_salary' => 'nullable|numeric|min:0',
            'default_hourly_rate' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $contract = TeacherContract::updateOrCreate(
            ['teacher_id' => $teacher->id],
            [
                'contract_type' => $validated['contract_type'],
                'calculation_type' => match ($validated['contract_type']) {
                    'fixed_salary' => 'fixed_monthly',
                    'hourly' => 'per_hour',
                    'percentage' => 'percentage',
                    default => 'per_hour',
                },
                'monthly_salary' => $validated['monthly_salary'] ?? 0.00,
                'default_hourly_rate' => $validated['default_hourly_rate'] ?? 0.00,
                'rate' => $validated['contract_type'] === 'fixed_salary'
                    ? ($validated['monthly_salary'] ?? 0.00)
                    : ($validated['default_hourly_rate'] ?? 0.00),
                'currency' => $validated['currency'],
                'notes' => $validated['notes'] ?? null,
                'is_active' => true,
            ]
        );

        SystemAuditLog::record(
            'teacher_contract.update',
            "تحديث العقد المالي والوظيفي للمعلم [{$teacher->name}]: نمط التعاقد ({$contract->contract_type_label})",
            $contract,
            $validated
        );

        return back()->with('success', "تم تحديث العقد المالي للمعلم [{$teacher->name}] بنجاح.");
    }

    /**
     * إثبات مستحق مالي لمعلم (قيد استحقاق مالي دائن)
     */
    public function storeTeacherEntitlement(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('finance.voucher_edit') && !Auth::user()->hasPermission('create_payment_voucher')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إثبات مستحقات المعلمين.');
        }

        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'entitlement_type' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|max:10',
            'due_date' => 'required|date',
            'period' => 'nullable|string|max:100',
            'study_group_id' => 'nullable|exists:study_groups,id',
            'course_id' => 'nullable|exists:courses,id',
            'educational_service_id' => 'nullable|exists:educational_services,id',
            'manual_no' => 'nullable|string|max:100',
            'statement' => 'required|string|max:500',
        ]);

        $teacher = User::with('financialAccount')->findOrFail($validated['teacher_id']);

        // التأكد من وجود حساب مالي للمعلم
        $teacherAccount = $teacher->financialAccount ?? FinancialAccount::create([
            'user_id' => $teacher->id,
            'account_code' => '22' . str_pad($teacher->id, 4, '0', STR_PAD_LEFT),
            'account_name' => 'حساب المعلم: ' . $teacher->name,
            'account_type' => 'teacher',
            'currency' => $validated['currency'],
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
        ]);

        // توليد رقم قيد متسلسل آلي
        $count = TeacherEntitlement::count() + 1;
        $entitlementNo = 'ENT-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        // صياغة العنوان التلقائي الذكي
        $title = match ($validated['entitlement_type']) {
            'course_teaching' => 'أجر تدريس مجموعة / دورة',
            'fixed_salary' => 'راتب شهري ثابت (' . ($validated['period'] ?: date('m/Y')) . ')',
            'exam_prep' => 'أتعاب إعداد اختبار وبنك أسئلة',
            'grading' => 'أتعاب تصحيح ومراجعة كشوفات درجات',
            'supervision' => 'أتعاب إشراف أكاديمي',
            'activity' => 'أتعاب ورشة عمل / نشاط تدريبي',
            'custom_service' => 'مستحقات خدمة تعليمية معتمدة',
            'bonus' => 'مكافأة تميز وبدل إنجاز',
            default => 'إثبات مستحقات مالية معتمدة',
        };

        if (!empty($validated['study_group_id'])) {
            $group = StudyGroup::find($validated['study_group_id']);
            if ($group) {
                $title .= ' - مجموعة: ' . $group->name;
                if (empty($validated['course_id'])) {
                    $validated['course_id'] = $group->course_id;
                }
            }
        } elseif (!empty($validated['course_id'])) {
            $course = Course::find($validated['course_id']);
            if ($course) {
                $title .= ' - دورة: ' . $course->name;
            }
        }

        $entitlement = TeacherEntitlement::create([
            'entitlement_no' => $entitlementNo,
            'manual_no' => $validated['manual_no'] ?? null,
            'teacher_id' => $teacher->id,
            'study_group_id' => $validated['study_group_id'] ?? null,
            'course_id' => $validated['course_id'] ?? null,
            'educational_service_id' => $validated['educational_service_id'] ?? null,
            'entitlement_type' => $validated['entitlement_type'],
            'title' => $title,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'due_date' => $validated['due_date'],
            'period' => $validated['period'] ?? null,
            'statement' => $validated['statement'],
            'status' => 'approved',
            'paid_amount' => 0.00,
            'created_by' => Auth::id(),
        ]);

        // زيادة رصيد المعلم المستحق (دائن له)
        $teacherAccount->increment('current_balance', $validated['amount']);

        // توثيق العملية في سجل التدقيق المالي
        SystemAuditLog::record(
            'teacher_entitlement.create',
            "إثبات قيد مستحقات رقم ({$entitlementNo}) بمبلغ {$validated['amount']} {$validated['currency']} للمعلم ({$teacher->name}) عن [{$title}]",
            $entitlement,
            ['amount' => $validated['amount'], 'currency' => $validated['currency'], 'teacher_id' => $teacher->id]
        );

        $currencyArabic = \App\Helpers\ArabicCurrencyHelper::currencyName($validated['currency']);
        $formattedAmount = number_format($validated['amount'], 2);

        $entitlementUrl = route('accounting.teacher_entitlement.print', $entitlement->id);
        $messageBody = \App\Services\WhatsAppService::renderTemplate(
            \App\Models\SiteSetting::get('wa_template_entitlement_body', "أستاذنا {name}، تم قيد مستحقات مالية بحسابكم برقم ({voucher_no}) بمبلغ {amount} عن: {statement} في {school_name}.\n\n📄 رابط كشف الحساب والمستند:\n{link}"),
            [
                '{name}' => $teacher->name,
                '{amount}' => "{$formattedAmount} {$currencyArabic}",
                '{voucher_no}' => $entitlementNo,
                '{statement}' => $title,
                '{link}' => $entitlementUrl,
            ]
        );

        $sendResult = \App\Services\WhatsAppService::send(
            $teacher->phone_whatsapp ?: '',
            $messageBody,
            $entitlementUrl,
            $teacher->id,
            'entitlement'
        );

        if (!empty($sendResult['wa_url']) && ($sendResult['status'] ?? '') !== 'sent') {
            session()->flash('whatsapp_direct_url', $sendResult['wa_url']);
            session()->flash('whatsapp_recipient_name', $teacher->name);
            session()->flash('whatsapp_voucher_no', $entitlementNo);
        }

        return back()->with('success', "تم إثبات وقيد المستحق المالي رقم ({$entitlementNo}) بمبلغ {$formattedAmount} {$currencyArabic} في حساب المعلم بنجاح.");
    }

    /**
     * تعديل بيانات ومبلغ قيد استحقاق المعلم
     */
    public function updateTeacherEntitlement(Request $request, $id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('finance.voucher_edit')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل مستحقات المعلمين.');
        }

        $entitlement = TeacherEntitlement::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'period' => 'nullable|string|max:100',
            'manual_no' => 'nullable|string|max:100',
            'statement' => 'required|string|max:500',
        ]);

        $teacher = User::with('financialAccount')->findOrFail($entitlement->teacher_id);
        $oldAmount = (float)$entitlement->amount;
        $newAmount = (float)$request->amount;
        $diff = $newAmount - $oldAmount;

        // تعديل رصيد استحقاق المعلم بالفرق
        if ($teacher->financialAccount && abs($diff) > 0.001) {
            $teacher->financialAccount->increment('current_balance', $diff);
        }

        $entitlement->update([
            'amount' => $newAmount,
            'due_date' => $request->due_date,
            'period' => $request->period,
            'manual_no' => $request->manual_no,
            'statement' => $request->statement,
        ]);

        SystemAuditLog::record(
            'teacher_entitlement.update',
            "تعديل قيد المستحق رقم ({$entitlement->entitlement_no}) للمعلم ({$teacher->name}) من مبلغ ({$oldAmount}) إلى ({$newAmount})",
            $entitlement,
            ['old_amount' => $oldAmount, 'new_amount' => $newAmount, 'diff' => $diff]
        );

        return back()->with('success', "تم تعديل قيد المستحق ({$entitlement->entitlement_no}) وتحديث رصيد المعلم بنجاح 💾");
    }

    /**
     * إلغاء وحذف قيد إثبات المستحق وعكس أثره المالي
     */
    public function destroyTeacherEntitlement($id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('finance.voucher_edit')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إلغاء مستحقات المعلمين.');
        }

        $entitlement = TeacherEntitlement::findOrFail($id);
        $teacher = User::with('financialAccount')->find($entitlement->teacher_id);

        // عكس الأثر المالي بإنقاص رصيد المعلم
        if ($teacher && $teacher->financialAccount) {
            $teacher->financialAccount->decrement('current_balance', $entitlement->amount);
        }

        $entNo = $entitlement->entitlement_no;
        $entAmount = $entitlement->amount;
        $entCurrency = $entitlement->currency;

        SystemAuditLog::record(
            'teacher_entitlement.delete',
            "إلغاء قيد مستحق رقم ({$entNo}) بمبلغ {$entAmount} {$entCurrency} للمعلم ({$teacher?->name}) وعكس أثره المالي.",
            null,
            ['entitlement_no' => $entNo, 'amount' => $entAmount, 'currency' => $entCurrency]
        );

        $entitlement->delete();

        return back()->with('success', "تم إلغاء قيد المستحق رقم ({$entNo}) وضبط رصيد حساب المعلم بنجاح.");
    }

    /**
     * استعراض القيد المحاسبي والأثر المالي للسند مع فحص التوازن المعياري (مدين ودائن)
     */
    public function getVoucherJournalEntry($id)
    {
        $voucher = Voucher::with([
            'account.user',
            'cashOrBankAccount',
            'creator',
            'items.account.user'
        ])->findOrFail($id);

        // جلب قيد اليومية المرتبط أو إنشاؤه فورياً في حال كان السند قديماً قبل تفعيل القيود الآلية
        $entry = JournalEntry::where('reference_type', 'voucher')
            ->where('reference_id', $voucher->id)
            ->with(['lines.account', 'lines.costCenter', 'creator', 'poster'])
            ->first();

        if (!$entry) {
            $entry = GeneralLedgerService::recordVoucherEntry($voucher);
            $entry->load(['lines.account', 'lines.costCenter', 'creator', 'poster']);
        }

        $totalDebit = (float) $entry->lines->sum('debit');
        $totalCredit = (float) $entry->lines->sum('credit');
        $diff = abs($totalDebit - $totalCredit);
        $isBalanced = $diff < 0.001;

        $lines = $entry->lines->map(function ($line) {
            $acc = $line->account;
            $debit = (float) $line->debit;
            $credit = (float) $line->credit;
            $cat = $acc?->account_category ?? 'asset';

            $isDebit = $debit > 0;
            $sideLabel = $isDebit ? 'طرف مدين (منه)' : 'طرف دائن (له)';
            $sideBadge = $isDebit ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-purple-50 text-purple-700 border-purple-200';

            // تحديد طبيعة الأثر المالي الدقيق وفق المعايير المحاسبية
            $impact = '';
            if ($isDebit) {
                switch ($cat) {
                    case 'asset':
                        $impact = 'زيادة في الأصول (+) تدفق نقدي وارد للخزينة';
                        break;
                    case 'liability':
                        $impact = 'سداد التزام / تخفيض مستحقات والتزامات دائنة (-)';
                        break;
                    case 'expense':
                        $impact = 'إثبات مصروف تشغيلي / تكاليف ومصروفات دورات (+)';
                        break;
                    case 'equity':
                        $impact = 'تخفيض في حقوق الملكية / مسحوبات (-)';
                        break;
                    default:
                        $impact = 'حركة قيد مدينة في الحساب';
                }
            } else {
                switch ($cat) {
                    case 'asset':
                        $impact = 'نقص في الأصول (-) سداد مديونية عميل / صرف نقدي';
                        break;
                    case 'liability':
                        $impact = 'إثبات التزام دائن / استحقاق مورد أو كادر (+)';
                        break;
                    case 'revenue':
                        $impact = 'إثبات إيراد محقق / إيرادات رسوم وخدمات (+)';
                        break;
                    case 'equity':
                        $impact = 'زيادة في حقوق الملكية / أرباح (+)';
                        break;
                    default:
                        $impact = 'حركة قيد دائنة في الحساب';
                }
            }

            return [
                'id' => $line->id,
                'account_id' => $line->account_id,
                'account_code' => $acc?->account_code ?? '—',
                'account_name' => $acc?->account_name ?? '—',
                'category' => $acc?->category_label ?? 'أصول',
                'category_code' => $cat,
                'cost_center_code' => $line->costCenter?->code,
                'cost_center_name' => $line->costCenter?->name ?? '—',
                'debit' => $debit,
                'credit' => $credit,
                'formatted_debit' => $debit > 0 ? number_format($debit, 2) : '—',
                'formatted_credit' => $credit > 0 ? number_format($credit, 2) : '—',
                'line_description' => $line->line_description,
                'side_label' => $sideLabel,
                'side_badge' => $sideBadge,
                'impact' => $impact,
            ];
        });

        // الأثر المالي الإجمالي الملخص
        $boxAccount = $voucher->cashOrBankAccount;
        $partyAccount = $voucher->account;
        $partyName = $voucher->is_compound
            ? 'سند مركب (' . $voucher->items->count() . ' بنود)'
            : ($partyAccount?->user?->name ?? $partyAccount?->account_name ?? 'حساب غير محدد');

        return response()->json([
            'success' => true,
            'voucher' => [
                'id' => $voucher->id,
                'voucher_no' => $voucher->voucher_no,
                'voucher_type' => $voucher->voucher_type,
                'voucher_type_label' => $voucher->voucher_type === 'receipt' ? 'سند قبض مالي' : 'سند صرف مالي',
                'is_compound' => (bool) $voucher->is_compound,
                'amount' => (float) $voucher->amount,
                'formatted_amount' => number_format((float) $voucher->amount, 2),
                'currency' => $voucher->currency,
                'currency_symbol' => $voucher->currency_symbol,
                'statement' => $voucher->statement,
                'created_at' => $voucher->created_at?->format('Y-m-d H:i') ?? '—',
                'creator_name' => $voucher->creator?->name ?? 'مدير النظام',
                'box_name' => $boxAccount?->account_name ?? 'الخزينة الرئيسية',
                'party_name' => $partyName,
            ],
            'journal_entry' => [
                'id' => $entry->id,
                'entry_no' => $entry->entry_no,
                'entry_date' => $entry->entry_date?->format('Y-m-d') ?? now()->toDateString(),
                'description' => $entry->description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'formatted_total_debit' => number_format($totalDebit, 2),
                'formatted_total_credit' => number_format($totalCredit, 2),
                'difference' => $diff,
                'formatted_difference' => number_format($diff, 2),
                'is_balanced' => $isBalanced,
                'is_posted' => (bool) $entry->is_posted,
                'posted_at' => $entry->posted_at?->format('Y-m-d H:i') ?? '—',
                'posted_by' => $entry->poster?->name ?? $entry->creator?->name ?? 'النظام الآلي',
                'view_url' => route('accounting.journal_entries.show', $entry->id),
                'print_url' => route('accounting.journal_entries.print', $entry->id),
                'lines' => $lines,
            ],
            'impact_summary' => [
                'type' => $voucher->voucher_type,
                'box_impact' => $voucher->voucher_type === 'receipt'
                    ? "زيادة سيولة الصندوق بمبلغ + " . number_format($voucher->amount, 2) . " " . $voucher->currency_symbol
                    : "صرف ونقص سيولة الصندوق بمبلغ - " . number_format($voucher->amount, 2) . " " . $voucher->currency_symbol,
                'party_impact' => $voucher->voucher_type === 'receipt'
                    ? "تخفيض مديونية العميل / الطالب بمبلغ " . number_format($voucher->amount, 2) . " " . $voucher->currency_symbol
                    : "سداد وتسوية مستحقات بمبلغ " . number_format($voucher->amount, 2) . " " . $voucher->currency_symbol,
                'accounting_rule' => $voucher->voucher_type === 'receipt'
                    ? "الطرف المدين (الصندوق المستلم) = الطرف الدائن (الحساب المقابل/العميل)"
                    : "الطرف المدين (المستفيد/المصروف) = الطرف الدائن (الصندوق الصارف)",
            ],
        ]);
    }
}

