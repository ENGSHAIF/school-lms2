<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FinancialAccount;
use App\Models\Level;
use App\Models\Package;
use App\Models\StudentInstallment;
use App\Models\StudyGroup;
use App\Models\StudyGroupStudent;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * العرض الرئيسي لمركز التقارير والإحصائيات الشاملة
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('view_reports'))) {
            return redirect()->route('home')->with('error', 'عذراً، ليس لديك صلاحية الوصول لمركز التقارير.');
        }

        $tab = $request->get('tab', 'overview');

        // معالجة الاختصارات السريعة للإدارة (Presets)
        if ($request->filled('preset')) {
            switch ($request->preset) {
                case 'active_groups':
                    $tab = 'groups';
                    $request->merge(['group_status' => 'active']);
                    break;
                case 'courses_pricing':
                    $tab = 'courses';
                    break;
                case 'vouchers_cash':
                    $tab = 'vouchers';
                    break;
                case 'students_placement':
                    $tab = 'students';
                    break;
            }
        }

        // 1. المؤشرات الإجمالية العليا (Global KPIs)
        $kpis = $this->calculateGlobalKpis();

        // 2. جلب البيانات بحسب التبويب المختار
        $groupsData = null;
        $coursesData = null;
        $vouchersData = null;
        $studentsData = null;
        $staffData = null;
        $statementsData = null;

        // القوائم المساعدة للفلاتر
        $allCourses = Course::orderBy('name')->get();
        $allTeachers = User::where('role', 'teacher')->orderBy('name')->get();
        $allSupervisors = User::where('role', 'supervisor')->orderBy('name')->get();
        $allGroups = StudyGroup::orderBy('id')->get();
        $allStudents = User::where('role', 'student')->orderBy('name')->get();

        // خيارات الأترام والدفعات المتاحة للفلترة
        $allTerms = StudyGroup::whereNotNull('term')->where('term', '!=', '')->distinct()->pluck('term')->all();
        $allBatches = StudyGroup::whereNotNull('batch')->where('batch', '!=', '')->distinct()->pluck('batch')->all();

        switch ($tab) {
            case 'groups':
                $groupsData = $this->queryGroupsReport($request);
                break;
            case 'courses':
                $coursesData = $this->queryCoursesReport($request);
                break;
            case 'vouchers':
                $vouchersData = $this->queryVouchersReport($request);
                break;
            case 'students':
                $studentsData = $this->queryStudentsReport($request);
                break;
            case 'staff':
                $staffData = $this->queryStaffReport($request);
                break;
            case 'statements':
                $statementsData = $this->queryStatementsReport($request);
                break;
            case 'overview':
            default:
                // جلب مقتطفات من كل تقرير للوحة العامة
                $groupsData = $this->queryGroupsReport(new Request(['group_status' => 'active', 'limit' => 10]));
                $coursesData = $this->queryCoursesReport(new Request(['limit' => 10]));
                $vouchersData = $this->queryVouchersReport(new Request(['limit' => 10]));
                break;
        }

        return view('admin.reports.index', compact(
            'tab',
            'kpis',
            'allCourses',
            'allTeachers',
            'allSupervisors',
            'allGroups',
            'allStudents',
            'allTerms',
            'allBatches',
            'groupsData',
            'coursesData',
            'vouchersData',
            'studentsData',
            'staffData',
            'statementsData'
        ));
    }

    /**
     * صفحة الطباعة الرسمية المعتمدة للتقارير
     */
    public function printReport(Request $request)
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('view_reports'))) {
            return redirect()->route('home')->with('error', 'عذراً، ليس لديك صلاحية طباعة التقارير.');
        }

        $type = $request->get('type', 'groups');
        $title = 'تقرير رسمي معتمد';
        $subtitle = '';
        $data = null;
        $summary = [];

        switch ($type) {
            case 'groups':
                $title = 'تقرير المجموعات الدراسية والدفعات';
                $statusLabel = match ($request->group_status) {
                    'active' => 'المجموعات النشطة فقط',
                    'closed' => 'المجموعات المغلقة والمكتملة',
                    default => 'كافة المجموعات (النشطة والمغلقة)'
                };
                $subtitle = "حالة المجموعات: {$statusLabel}";
                if ($request->filled('term') && $request->term !== 'all') {
                    $subtitle .= " | الترم الدراسي: {$request->term}";
                }
                if ($request->filled('batch') && $request->batch !== 'all') {
                    $subtitle .= " | الدفعة: {$request->batch}";
                }
                if ($request->filled('from_group') && $request->filled('to_group')) {
                    $subtitle .= " | نطاق المجموعات من رقم ({$request->from_group}) إلى ({$request->to_group})";
                }
                $data = $this->queryGroupsReport($request);
                $summary = [
                    'إجمالي المجموعات المعروضة' => $data['groups']->count(),
                    'إجمالي الطلاب المسجلين' => $data['total_enrolled'],
                    'إجمالي المقاعد الشاغرة' => $data['total_available_seats'],
                ];
                break;

            case 'courses':
                $title = 'تقرير تسعيرة الدورات والمستويات والمناهج الدراسية';
                $subtitle = 'المراجعة الإدارية الشاملة لتسعيرة المقررات وباقات الحصص التعليمية';
                $data = $this->queryCoursesReport($request);
                $summary = [
                    'إجمالي الدورات' => $data['courses']->count(),
                    'إجمالي المستويات التعليمية' => $data['total_levels'],
                    'إجمالي باقات الحصص' => $data['packages']->count(),
                ];
                break;

            case 'vouchers':
                $title = 'كشف السندات المالية وحركة الصناديق';
                $subtitle = 'سجل سندات القبض والصرف الرسمية مع بيان النقدية';
                if ($request->filled('voucher_from') || $request->filled('voucher_to')) {
                    $subtitle .= " | من رقم سند: " . ($request->voucher_from ?? 'البداية') . " إلى: " . ($request->voucher_to ?? 'النهاية');
                }
                if ($request->filled('date_from') || $request->filled('date_to')) {
                    $subtitle .= " | الفترة من: " . ($request->date_from ?? 'البداية') . " إلى: " . ($request->date_to ?? 'اليوم');
                }
                $data = $this->queryVouchersReport($request);
                $summary = [
                    'عدد السندات المعروضة' => $data['vouchers']->count(),
                    'إجمالي سندات القبض' => number_format($data['total_receipts_amount'], 2) . ' YER',
                    'إجمالي سندات الصرف' => number_format($data['total_payments_amount'], 2) . ' YER',
                    'صافي حركة الفترة' => number_format($data['net_amount'], 2) . ' YER',
                ];
                break;

            case 'students':
                $title = 'كشف الطلاب والملتحقين بالمجموعات';
                $subtitle = 'حالة القبول، التسكين في المجموعات الدراسية، والأرصدة';
                if ($request->filled('term') && $request->term !== 'all') {
                    $subtitle .= " | الترم الدراسي: {$request->term}";
                }
                if ($request->filled('batch') && $request->batch !== 'all') {
                    $subtitle .= " | الدفعة: {$request->batch}";
                }
                $data = $this->queryStudentsReport($request);
                $summary = [
                    'عدد الطلاب بالتقرير' => $data['students']->count(),
                    'المسكنين في مجموعات' => $data['in_groups_count'],
                    'بانتظار التسكين' => $data['pending_placement_count'],
                ];
                break;
        }

        $kpis = $this->calculateGlobalKpis();

        return view('admin.reports.print', compact('type', 'title', 'subtitle', 'data', 'summary', 'kpis'));
    }

    /**
     * احتساب المؤشرات الإجمالية
     */
    private function calculateGlobalKpis(): array
    {
        $totalStudents = User::where('role', 'student')->count();
        $activeStudents = User::where('role', 'student')->where('status', 'active')->count();

        // طلاب في مجموعات نشطة
        $studentsInGroups = StudyGroupStudent::where('status', 'active')->distinct('student_id')->count('student_id');
        $pendingPlacement = max(0, $totalStudents - $studentsInGroups);

        $totalCourses = Course::count();
        $activeCourses = Course::where('is_active', true)->count();
        $totalLevels = Level::count();

        $totalGroups = StudyGroup::count();
        $activeGroups = StudyGroup::where('status', 'active')->count();
        $closedGroups = StudyGroup::where('status', '!=', 'active')->count();

        $totalTeachers = User::where('role', 'teacher')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();
        $totalUsers = User::count();

        // الصناديق المالية
        $cashBox = FinancialAccount::where('account_type', 'cash_box')->first();
        $cashBoxBalance = $cashBox ? (float)$cashBox->current_balance : 0.0;

        $bankAccount = FinancialAccount::where('account_type', 'bank')->first();
        $bankBalance = $bankAccount ? (float)$bankAccount->current_balance : 0.0;

        // السندات المالية
        $receiptsSum = Voucher::where('voucher_type', 'receipt')->sum('amount');
        $receiptsCount = Voucher::where('voucher_type', 'receipt')->count();
        $paymentsSum = Voucher::where('voucher_type', 'payment')->sum('amount');
        $paymentsCount = Voucher::where('voucher_type', 'payment')->count();

        // باقات الحصص
        $packagesCount = Package::count();

        // إحصائيات الأترام الدراسية
        $totalTerms = StudyGroup::whereNotNull('term')->where('term', '!=', '')->distinct('term')->count('term');
        $activeTerm = StudyGroup::whereNotNull('term')->latest('id')->value('term') ?? 'الترم الأول 2026';

        return [
            'total_students' => $totalStudents,
            'active_students' => $activeStudents,
            'students_in_groups' => $studentsInGroups,
            'pending_placement' => $pendingPlacement,
            'total_courses' => $totalCourses,
            'active_courses' => $activeCourses,
            'total_levels' => $totalLevels,
            'total_groups' => $totalGroups,
            'active_groups' => $activeGroups,
            'closed_groups' => $closedGroups,
            'total_teachers' => $totalTeachers,
            'total_supervisors' => $totalSupervisors,
            'total_users' => $totalUsers,
            'total_terms' => $totalTerms,
            'active_term' => $activeTerm,
            'cash_box_balance' => $cashBoxBalance,
            'bank_balance' => $bankBalance,
            'total_cash_funds' => $cashBoxBalance + $bankBalance,
            'receipts_sum' => $receiptsSum,
            'receipts_count' => $receiptsCount,
            'payments_sum' => $paymentsSum,
            'payments_count' => $paymentsCount,
            'net_vouchers_cash' => $receiptsSum - $paymentsSum,
            'packages_count' => $packagesCount,
        ];
    }

    /**
     * تقرير المجموعات الدراسية مع فلاتر الحالة ومن مجموعة إلى مجموعة
     */
    private function queryGroupsReport(Request $request): array
    {
        $query = StudyGroup::with(['course', 'level', 'teacher', 'supervisor', 'teachers', 'supervisors', 'students']);

        // فلترة الحالة (نشطة / مغلقة ومكتملة / الكل)
        $status = $request->get('group_status', 'all');
        if ($status === 'active') {
            $query->where('status', 'active');
        } elseif ($status === 'closed') {
            $query->whereIn('status', ['closed', 'completed', 'archived']);
        }

        // فلترة بالترم الدراسي
        if ($request->filled('term') && $request->term !== 'all') {
            $query->where('term', $request->term);
        }

        // فلترة بالدفعة
        if ($request->filled('batch') && $request->batch !== 'all') {
            $query->where('batch', $request->batch);
        }

        // فلترة بالدورة
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // فلترة بالمستوى
        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        // فلترة بالمعلم (رئيسي أو مساعد)
        if ($request->filled('teacher_id')) {
            $tId = $request->teacher_id;
            $query->where(function ($q) use ($tId) {
                $q->where('teacher_id', $tId)
                  ->orWhereHas('teachers', function ($t) use ($tId) {
                      $t->where('users.id', $tId);
                  });
            });
        }

        // فلترة بالمشرف (رئيسي أو مساعد)
        if ($request->filled('supervisor_id')) {
            $sId = $request->supervisor_id;
            $query->where(function ($q) use ($sId) {
                $q->where('supervisor_id', $sId)
                  ->orWhereHas('supervisors', function ($s) use ($sId) {
                      $s->where('users.id', $sId);
                  });
            });
        }

        // فلترة "من مجموعة إلى مجموعة" (بواسطة المعرف الرقمي أو الترتيبي)
        if ($request->filled('from_group')) {
            $query->where('id', '>=', (int)$request->from_group);
        }
        if ($request->filled('to_group')) {
            $query->where('id', '<=', (int)$request->to_group);
        }

        // بحث بالاسم أو الترم أو المعلم
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('term', 'like', "%{$s}%")
                  ->orWhere('batch', 'like', "%{$s}%")
                  ->orWhere('program_type', 'like', "%{$s}%");
            });
        }

        if ($request->filled('limit')) {
            $query->take((int)$request->limit);
        }

        $groups = $query->orderBy('id', 'asc')->get();

        $totalCapacity = $groups->sum('max_students');
        $totalEnrolled = 0;
        $totalAvailableSeats = 0;

        foreach ($groups as $g) {
            $enrolled = $g->students->count();
            $totalEnrolled += $enrolled;
            $totalAvailableSeats += max(0, $g->max_students - $enrolled);
        }

        $occupancyRate = $totalCapacity > 0 ? round(($totalEnrolled / $totalCapacity) * 100, 1) : 0;

        return [
            'groups' => $groups,
            'total_groups' => $groups->count(),
            'total_capacity' => $totalCapacity,
            'total_enrolled' => $totalEnrolled,
            'total_available_seats' => $totalAvailableSeats,
            'occupancy_rate' => $occupancyRate,
            'active_count' => $groups->where('status', 'active')->count(),
            'closed_count' => $groups->where('status', '!=', 'active')->count(),
            'live_count' => $groups->where('is_live', true)->count(),
        ];
    }

    /**
     * تقرير الدورات والتسعيرة وباقات الحصص للمراجعة الإدارية
     */
    private function queryCoursesReport(Request $request): array
    {
        $query = Course::with(['levels.lessons', 'levels.books', 'studyGroups.students']);

        if ($request->filled('course_status') && $request->course_status !== 'all') {
            $isActive = $request->course_status === 'active';
            $query->where('is_active', $isActive);
        }

        if ($request->filled('course_id')) {
            $query->where('id', $request->course_id);
        }

        if ($request->filled('limit')) {
            $query->take((int)$request->limit);
        }

        $courses = $query->orderBy('name')->get();

        // إحصائيات المستويات والتسعيرات
        $totalLevels = 0;
        $totalLessons = 0;
        $totalCourseStudents = 0;
        $levelsPricing = [];

        foreach ($courses as $c) {
            $totalLevels += $c->levels->count();
            foreach ($c->levels as $lvl) {
                $totalLessons += $lvl->lessons->count();
                $levelsPricing[] = [
                    'course_name' => $c->name,
                    'level_name' => $lvl->name,
                    'fee' => $lvl->fee_amount,
                    'currency' => $lvl->currency,
                    'is_active' => $lvl->is_active,
                ];
            }
            foreach ($c->studyGroups as $grp) {
                $totalCourseStudents += $grp->students->count();
            }
        }

        // باقات الحصص الإضافية
        $packages = Package::orderBy('lessons_count')->get();

        return [
            'courses' => $courses,
            'packages' => $packages,
            'total_courses' => $courses->count(),
            'total_levels' => $totalLevels,
            'total_lessons' => $totalLessons,
            'total_course_students' => $totalCourseStudents,
            'levels_pricing' => $levelsPricing,
        ];
    }

    /**
     * تقرير السندات المالية والصناديق مع فلترة من رقم إلى رقم ومن تاريخ إلى تاريخ
     */
    private function queryVouchersReport(Request $request): array
    {
        $query = Voucher::with(['account.user', 'cashOrBankAccount', 'creator']);

        // فلترة نوع السند (قبض / صرف / الكل)
        if ($request->filled('voucher_type') && $request->voucher_type !== 'all') {
            $query->where('voucher_type', $request->voucher_type);
        }

        // فلترة "من رقم سند إلى رقم سند" (من رقم إلى رقم)
        // يدعم البحث بالرقم التسلسلي ID أو لاحقة رقم السند أو الرقم الدفتري اليدوي
        if ($request->filled('voucher_from')) {
            $from = trim($request->voucher_from);
            if (is_numeric($from)) {
                $query->where(function ($q) use ($from) {
                    $q->where('id', '>=', (int)$from)
                      ->orWhere('voucher_no', 'like', "%{$from}%")
                      ->orWhere('manual_no', '>=', $from);
                });
            } else {
                $query->where('voucher_no', '>=', $from);
            }
        }

        if ($request->filled('voucher_to')) {
            $to = trim($request->voucher_to);
            if (is_numeric($to)) {
                $query->where(function ($q) use ($to) {
                    $q->where('id', '<=', (int)$to)
                      ->orWhere('voucher_no', 'like', "%{$to}%")
                      ->orWhere('manual_no', '<=', $to);
                });
            } else {
                $query->where('voucher_no', '<=', $to);
            }
        }

        // فلترة "من تاريخ إلى تاريخ"
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // طريقة الدفع
        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // بحث نصي
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('voucher_no', 'like', "%{$s}%")
                  ->orWhere('manual_no', 'like', "%{$s}%")
                  ->orWhere('statement', 'like', "%{$s}%");
            });
        }

        if ($request->filled('limit')) {
            $query->take((int)$request->limit);
        }

        $vouchers = $query->latest('id')->get();

        $receipts = $vouchers->where('voucher_type', 'receipt');
        $payments = $vouchers->where('voucher_type', 'payment');

        $totalReceiptsAmount = $receipts->sum('amount');
        $totalPaymentsAmount = $payments->sum('amount');
        $netAmount = $totalReceiptsAmount - $totalPaymentsAmount;

        // الصناديق المالية
        $cashAccounts = FinancialAccount::whereIn('account_type', ['cash_box', 'bank'])->get();

        return [
            'vouchers' => $vouchers,
            'cash_accounts' => $cashAccounts,
            'total_receipts_count' => $receipts->count(),
            'total_receipts_amount' => $totalReceiptsAmount,
            'total_payments_count' => $payments->count(),
            'total_payments_amount' => $payments->count() ? $payments->sum('amount') : 0,
            'net_amount' => $netAmount,
        ];
    }

    /**
     * تقرير الطلاب والملتحقين
     */
    private function queryStudentsReport(Request $request): array
    {
        $query = User::where('role', 'student')->with([
            'studentProfile',
            'financialAccount',
            'studyGroups.course',
            'studyGroups.level',
            'studyGroups.teachers',
            'studyGroups.supervisors',
            'enrollments.level'
        ]);

        if ($request->filled('student_status') && $request->student_status !== 'all') {
            switch ($request->student_status) {
                case 'active':
                    $query->where('status', 'active');
                    break;
                case 'in_group':
                    $query->whereHas('studyGroups', function ($q) {
                        $q->where('study_group_students.status', 'active');
                    });
                    break;
                case 'not_in_group':
                    $query->whereDoesntHave('studyGroups', function ($q) {
                        $q->where('study_group_students.status', 'active');
                    });
                    break;
            }
        }

        // فلترة بالترم الدراسي
        if ($request->filled('term') && $request->term !== 'all') {
            $termVal = $request->term;
            $query->whereHas('studyGroups', function ($q) use ($termVal) {
                $q->where('study_groups.term', $termVal);
            });
        }

        // فلترة بالدفعة
        if ($request->filled('batch') && $request->batch !== 'all') {
            $batchVal = $request->batch;
            $query->whereHas('studyGroups', function ($q) use ($batchVal) {
                $q->where('study_groups.batch', $batchVal);
            });
        }

        // فلترة بالدورة
        if ($request->filled('course_id')) {
            $cId = $request->course_id;
            $query->whereHas('studyGroups', function ($q) use ($cId) {
                $q->where('study_groups.course_id', $cId);
            });
        }

        // فلترة بالمستوى
        if ($request->filled('level_id')) {
            $lId = $request->level_id;
            $query->whereHas('studyGroups', function ($q) use ($lId) {
                $q->where('study_groups.level_id', $lId);
            });
        }

        // فلترة بالمعلم (رئيسي أو مساعد)
        if ($request->filled('teacher_id')) {
            $tId = $request->teacher_id;
            $query->whereHas('studyGroups', function ($q) use ($tId) {
                $q->where('study_groups.teacher_id', $tId)
                  ->orWhereHas('teachers', function ($t) use ($tId) {
                      $t->where('users.id', $tId);
                  });
            });
        }

        // فلترة بالمشرف (رئيسي أو مساعد)
        if ($request->filled('supervisor_id')) {
            $sId = $request->supervisor_id;
            $query->whereHas('studyGroups', function ($q) use ($sId) {
                $q->where('study_groups.supervisor_id', $sId)
                  ->orWhereHas('supervisors', function ($s) use ($sId) {
                      $s->where('users.id', $sId);
                  });
            });
        }

        if ($request->filled('from_id')) {
            $query->where('id', '>=', (int)$request->from_id);
        }
        if ($request->filled('to_id')) {
            $query->where('id', '<=', (int)$request->to_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('student_code', 'like', "%{$s}%")
                  ->orWhere('phone_whatsapp', 'like', "%{$s}%");
            });
        }

        if ($request->filled('limit')) {
            $query->take((int)$request->limit);
        }

        $students = $query->orderBy('id', 'asc')->get();

        $inGroupsCount = 0;
        $pendingPlacementCount = 0;

        foreach ($students as $stu) {
            if ($stu->studyGroups->where('pivot.status', 'active')->count() > 0) {
                $inGroupsCount++;
            } else {
                $pendingPlacementCount++;
            }
        }

        return [
            'students' => $students,
            'total_students' => $students->count(),
            'in_groups_count' => $inGroupsCount,
            'pending_placement_count' => $pendingPlacementCount,
        ];
    }

    /**
     * تقرير الكادر التعليمي والمستخدمين بحسب الأدوار
     */
    private function queryStaffReport(Request $request): array
    {
        $teachers = User::where('role', 'teacher')
            ->with(['financialAccount'])
            ->get();

        // حساب عدد المجموعات والطلاب لكل معلم
        $teachersData = [];
        foreach ($teachers as $t) {
            $groups = StudyGroup::where('teacher_id', $t->id)->with('students')->get();
            $studentsCount = 0;
            foreach ($groups as $g) {
                $studentsCount += $g->students->count();
            }

            $teachersData[] = [
                'teacher' => $t,
                'groups_count' => $groups->count(),
                'active_groups_count' => $groups->where('status', 'active')->count(),
                'students_count' => $studentsCount,
                'balance' => $t->financialAccount?->current_balance ?? 0.0,
            ];
        }

        // توزيع المستخدمين بحسب الأدوار
        $rolesDistribution = [
            'admin' => ['label' => 'مدير نظام', 'count' => User::where('role', 'admin')->count(), 'color' => 'indigo'],
            'supervisor' => ['label' => 'مشرف أكاديمي', 'count' => User::where('role', 'supervisor')->count(), 'color' => 'purple'],
            'teacher' => ['label' => 'معلم لغات', 'count' => User::where('role', 'teacher')->count(), 'color' => 'blue'],
            'student' => ['label' => 'طالب مسجل', 'count' => User::where('role', 'student')->count(), 'color' => 'emerald'],
            'parent' => ['label' => 'ولي أمر', 'count' => User::where('role', 'parent')->count(), 'color' => 'amber'],
            'accountant' => ['label' => 'محاسب مالي', 'count' => User::where('role', 'accountant')->count(), 'color' => 'teal'],
        ];

        return [
            'teachers_data' => $teachersData,
            'roles_distribution' => $rolesDistribution,
            'total_users' => User::count(),
        ];
    }

    /**
     * تقرير كشوفات الحساب والأرصدة المدينة والدائنة
     */
    private function queryStatementsReport(Request $request): array
    {
        // حسابات الطلاب مع رصيد مدين
        $studentAccounts = FinancialAccount::where('account_type', 'student')
            ->with('user')
            ->orderBy('current_balance', 'desc')
            ->get();

        // حسابات المعلمين
        $teacherAccounts = FinancialAccount::where('account_type', 'teacher')
            ->with('user')
            ->get();

        $totalReceivables = $studentAccounts->where('current_balance', '>', 0)->sum('current_balance');
        $totalPayables = $teacherAccounts->where('current_balance', '<', 0)->sum('current_balance');

        return [
            'student_accounts' => $studentAccounts,
            'teacher_accounts' => $teacherAccounts,
            'total_receivables' => $totalReceivables,
            'total_payables' => abs($totalPayables),
        ];
    }

    /**
     * تقرير السجل الأكاديمي والمالي الشامل للطالب (360 Student Transcript & Movement)
     * يعرض مسار الطالب منذ دخوله المنصة وحتى التخرج والحركة المالية كاملة
     */
    public function studentTranscript($studentId)
    {
        $user = Auth::user();
        $isChildOfParent = ($user && $user->isParent() && $user->hasPermission('parent.transcript_view') && \App\Models\StudentProfile::where('user_id', $studentId)->where('parent_id', $user->id)->exists());

        if (!$user || (!$user->isAdmin() && !$user->hasPermission('view_reports') && !$user->hasPermission('print_statement') && !$user->hasPermission('reports.academic') && !$isChildOfParent)) {
            return redirect()->route('home')->with('error', 'عذراً، ليس لديك صلاحية استعراض السجل الشامل للطلاب.');
        }

        $student = User::with([
            'studentProfile',
            'financialAccount',
            'studyGroups.course',
            'studyGroups.level',
            'studyGroups.teachers',
            'studyGroups.supervisors',
            'enrollments.level.course',
            'examAttempts.exam',
            'installments.level',
        ])->findOrFail($studentId);

        $account = $student->financialAccount;

        // الحركات المالية والسندات والأقساط
        $vouchers = Voucher::where('account_id', $account?->id)->orderBy('created_at')->get();
        $installments = StudentInstallment::with('level')->where('user_id', $student->id)->orderBy('created_at')->get();

        $totalTuition = $installments->sum('amount');
        $totalDiscounts = $installments->sum('discount_amount');
        $netTuition = max(0, $totalTuition - $totalDiscounts);
        $totalPaid = $vouchers->where('voucher_type', 'receipt')->sum('amount');
        $currentBalance = $account ? (float)$account->current_balance : ($netTuition - $totalPaid);

        // الحركات المحاسبية المدمجة (تحليلي)
        $movements = [];

        foreach ($installments as $inst) {
            $movements[] = [
                'date' => $inst->created_at,
                'doc_no' => 'INV-' . str_pad($inst->id, 5, '0', STR_PAD_LEFT),
                'type' => 'debit',
                'title' => 'رسوم دراسية: ' . ($inst->level?->name ?? 'مستوى دراسي') . ' (القسط ' . $inst->installment_number . ')',
                'debit' => (float)$inst->amount,
                'credit' => 0.0,
                'discount' => (float)$inst->discount_amount,
                'discount_reason' => $inst->discount_reason,
            ];
        }

        foreach ($vouchers as $v) {
            if ($v->voucher_type === 'receipt') {
                $movements[] = [
                    'date' => $v->created_at,
                    'doc_no' => $v->voucher_no . ($v->manual_no ? ' (دفتري: ' . $v->manual_no . ')' : ''),
                    'type' => 'credit',
                    'title' => 'سند قبض مالي: ' . $v->statement . ' [' . ($v->payment_method_label ?? $v->payment_method) . ']',
                    'debit' => 0.0,
                    'credit' => (float)$v->amount,
                    'discount' => 0.0,
                    'discount_reason' => null,
                ];
            } else {
                $movements[] = [
                    'date' => $v->created_at,
                    'doc_no' => $v->voucher_no,
                    'type' => 'debit',
                    'title' => 'سند صرف مالي: ' . $v->statement,
                    'debit' => (float)$v->amount,
                    'credit' => 0.0,
                    'discount' => 0.0,
                    'discount_reason' => null,
                ];
            }
        }

        usort($movements, fn($a, $b) => $a['date'] <=> $b['date']);

        // حساب الرصيد التراكمي
        $runningBalance = 0;
        foreach ($movements as &$m) {
            $runningBalance += ($m['debit'] - $m['discount'] - $m['credit']);
            $m['running_balance'] = $runningBalance;
        }

        // الأنشطة والكويزات
        $examAttempts = $student->examAttempts()->with('exam')->latest()->get();

        return view('admin.reports.student_360', compact(
            'student',
            'account',
            'vouchers',
            'installments',
            'totalTuition',
            'totalDiscounts',
            'netTuition',
            'totalPaid',
            'currentBalance',
            'movements',
            'examAttempts'
        ));
    }

    /**
     * كشف حساب المعلم للحركة المالية والأكاديمية (تحليلي وإجمالي)
     */
    public function teacherStatement($teacherId)
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('view_reports') && !$user->hasPermission('print_statement'))) {
            return redirect()->route('home')->with('error', 'عذراً، ليس لديك صلاحية استعراض كشف حساب المعلم.');
        }

        $teacher = User::with([
            'financialAccount',
            'teacherContract',
            'teachingGroups.course',
            'teachingGroups.level',
            'teachingGroups.students',
            'assignedTeachingGroups.course',
            'assignedTeachingGroups.level',
            'assignedTeachingGroups.students',
            'teacherEntitlements.studyGroup.course',
            'teacherEntitlements.course',
            'teacherEntitlements.service',
        ])->findOrFail($teacherId);

        $account = $teacher->financialAccount;
        $contract = $teacher->teacherContract;

        // 1. سندات الصرف والتحويلات المسلمة للمعلم (حركات مدينة - عليه)
        $vouchers = Voucher::where('account_id', $account?->id)->orderBy('created_at')->get();
        $totalPaidVouchers = (float)$vouchers->where('voucher_type', 'payment')->sum('amount');

        // 2. قيود إثبات المستحقات المعتمدة للمعلم (حركات دائنة - له)
        $entitlements = \App\Models\TeacherEntitlement::where('teacher_id', $teacher->id)
            ->where('status', '!=', 'cancelled')
            ->with(['studyGroup.course', 'course', 'service'])
            ->orderBy('due_date')
            ->orderBy('created_at')
            ->get();

        $totalEntitlements = (float)$entitlements->sum('amount');

        // في حال عدم وجود قيود استحقاق حديثة مسجلة بعد، نقوم باحتساب المستحقات من الرصيد الدفتري الحالي لضمان التوافق مع البيانات القديمة
        if ($entitlements->isEmpty()) {
            $currentBalance = $account ? (float)$account->current_balance : 0.0;
            $totalEntitlements = max($totalPaidVouchers + ($currentBalance > 0 ? $currentBalance : 0), 0);
        }

        $netBalance = $totalEntitlements - $totalPaidVouchers;

        // المجموعات التي يقوم بتدريسها
        $teachingGroups = $teacher->assignedTeachingGroups->isNotEmpty()
            ? $teacher->assignedTeachingGroups
            : $teacher->teachingGroups;

        $primaryGroupsCount = $teachingGroups->filter(function($g) use ($teacher) {
            return ($g->pivot && $g->pivot->is_primary) || ($g->teacher_id == $teacher->id);
        })->count();
        $asstGroupsCount = max(0, $teachingGroups->count() - $primaryGroupsCount);

        $totalStudentsTaught = $teachingGroups->sum(fn($g) => $g->students->count());
        $currency = $account?->currency_symbol ?: ($account?->currency ?? 'YER');

        // 3. بناء كشف الحساب التحليلي الزمني (Double-entry Chronological Ledger)
        $ledger = [];

        // إضافة قيود إثبات المستحقات
        foreach ($entitlements as $ent) {
            $dateStr = $ent->due_date ? \Carbon\Carbon::parse($ent->due_date)->format('Y-m-d') : ($ent->created_at ? $ent->created_at->format('Y-m-d') : date('Y-m-d'));
            $desc = $ent->title;
            if ($ent->statement) {
                $desc .= ' - ' . $ent->statement;
            }
            if ($ent->period) {
                $desc .= ' [فترة: ' . $ent->period . ']';
            }

            $ref = $ent->entitlement_no ?: ('قيد #' . $ent->id);
            if ($ent->manual_no) {
                $ref .= ' (دفتر: ' . $ent->manual_no . ')';
            }

            $ledger[] = [
                'date' => $dateStr,
                'type' => 'entitlement',
                'description' => $desc,
                'reference' => $ref,
                'credit' => (float)$ent->amount,
                'debit' => 0.0,
                'timestamp' => $ent->due_date ? \Carbon\Carbon::parse($ent->due_date)->timestamp : ($ent->created_at ? $ent->created_at->timestamp : time()),
            ];
        }

        // إضافة سندات الصرف والقبض
        foreach ($vouchers as $v) {
            $ledger[] = [
                'date' => $v->created_at ? $v->created_at->format('Y-m-d') : date('Y-m-d'),
                'type' => $v->voucher_type === 'payment' ? 'voucher' : 'entitlement',
                'description' => $v->statement ?: ($v->voucher_type === 'payment' ? 'سند صرف نقدي للمعلم' : 'قبض مالي'),
                'reference' => 'سند #' . ($v->voucher_no ?: $v->id) . ($v->manual_no ? ' (دفتر: ' . $v->manual_no . ')' : ''),
                'credit' => $v->voucher_type === 'receipt' ? (float)$v->amount : 0.0,
                'debit' => $v->voucher_type === 'payment' ? (float)$v->amount : 0.0,
                'timestamp' => $v->created_at ? $v->created_at->timestamp : time(),
            ];
        }

        // في حال عدم وجود أي حركات، نعرض المجموعات المسندة كاستحقاقات استرشادية
        if (empty($ledger)) {
            foreach ($teachingGroups as $g) {
                $ledger[] = [
                    'date' => $g->created_at ? $g->created_at->format('Y-m-d') : date('Y-m-d'),
                    'type' => 'entitlement',
                    'description' => 'استحقاق تدريس ' . ($g->pivot?->is_primary ? 'رئيسي' : 'مساعد') . ' لمجموعة: ' . $g->name,
                    'reference' => 'مجموعة #' . $g->id,
                    'credit' => (float)($g->level?->course?->teacher_fee ?? 0),
                    'debit' => 0.0,
                    'timestamp' => $g->created_at ? $g->created_at->timestamp : time(),
                ];
            }
        } else {
            // ترتيب الحركات زمنياً حسب تاريخ الإنشاء / الاستحقاق
            usort($ledger, function($a, $b) {
                return $a['timestamp'] <=> $b['timestamp'];
            });
        }

        // تفقيط صافي الرصيد باللغة العربية
        $netBalanceWords = \App\Helpers\ArabicCurrencyHelper::tafqeet(abs($netBalance), $account?->currency ?? 'YER');
        $generatedAt = now();

        return view('admin.reports.teacher_statement', compact(
            'teacher',
            'contract',
            'account',
            'vouchers',
            'entitlements',
            'totalPaidVouchers',
            'teachingGroups',
            'primaryGroupsCount',
            'asstGroupsCount',
            'totalStudentsTaught',
            'currency',
            'totalEntitlements',
            'netBalance',
            'netBalanceWords',
            'ledger',
            'generatedAt'
        ));
    }

    /**
     * تقرير كشف طلاب ومجموعات الترم للطباعة والمقارنة الإحصائية
     */
    public function termRosterPrint(Request $request)
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->hasPermission('view_reports'))) {
            return redirect()->route('home')->with('error', 'عذراً، ليس لديك صلاحية طباعة تقارير الأترام.');
        }

        $term = $request->get('term');
        if (!$term) {
            $term = StudyGroup::whereNotNull('term')->latest('id')->value('term') ?? 'الترم الأول 2026';
        }

        $groups = StudyGroup::where('term', $term)
            ->with(['course', 'level', 'teacher', 'supervisor', 'teachers', 'supervisors', 'students.financialAccount'])
            ->get();

        $students = User::whereHas('studyGroups', function ($q) use ($term) {
            $q->where('study_groups.term', $term);
        })->with([
            'studyGroups' => function ($q) use ($term) {
                $q->where('study_groups.term', $term);
            },
            'financialAccount',
            'studentProfile',
        ])->orderBy('name')->get();

        $totalCapacity = $groups->sum('max_students');
        $totalEnrolled = $students->count();
        $occupancyRate = $totalCapacity > 0 ? round(($totalEnrolled / $totalCapacity) * 100, 1) : 0;

        // إحصائيات المقارنة مع الأترام الأخرى
        $allTerms = StudyGroup::whereNotNull('term')->where('term', '!=', '')->distinct()->pluck('term')->all();

        return view('admin.reports.print_term', compact(
            'term',
            'groups',
            'students',
            'totalCapacity',
            'totalEnrolled',
            'occupancyRate',
            'allTerms'
        ));
    }
}
