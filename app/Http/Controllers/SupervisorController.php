<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\LessonReport;
use App\Models\Level;
use App\Models\StudentEnrollment;
use App\Models\StudentInstallment;
use App\Models\SystemAuditLog;
use App\Models\User;
use App\Models\Voucher;
use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupervisorController extends Controller
{
    // لوحة تحكم المشرف الأكاديمي
    public function dashboard()
    {
        $supervisor = Auth::user();

        if (! $supervisor->isSupervisor() && ! $supervisor->isAdmin()) {
            abort(403, 'غير مصرح لك بالدخول إلى لوحة المشرف الأكاديمي.');
        }

        // 1. الطلاب بانتظار مراجعة واعتماد المستوى بعد الاختبار
        $pendingApprovals = User::where('role', 'student')
            ->where('status', 'pending_approval')
            ->with([
                'studentProfile',
                'approver',
                'revoker',
                'examAttempts' => fn ($q) => $q->with(['suggestedLevel.course', 'answers'])->latest()->limit(1),
            ])
            ->latest()
            ->get();

        // 2. إحصائيات عامة
        $totalStudents = User::where('role', 'student')->count();
        $activeStudents = User::where('role', 'student')->where('status', 'active')->count();
        $pendingCount = $pendingApprovals->count();
        $completedLessonsCount = LessonReport::count();

        // 3. المستويات الأكاديمية المتاحة
        $levels = Level::with('course')->get();

        // 4. تقارير الحصص الأخيرة لمراقبة الجودة الأكاديمية
        $recentReports = LessonReport::with(['student', 'teacher', 'booking'])
            ->latest()
            ->take(10)
            ->get();

        return view('supervisor.dashboard', compact(
            'supervisor',
            'pendingApprovals',
            'totalStudents',
            'activeStudents',
            'pendingCount',
            'completedLessonsCount',
            'levels',
            'recentReports'
        ));
    }

    // اعتماد المستوى الأكاديمي وتفعيل حساب الطالب رسمياً
    public function assignLevel(Request $request, $id)
    {
        $supervisor = Auth::user();

        if (! $supervisor->isSupervisor() && ! $supervisor->isAdmin() && ! $supervisor->hasPermission('students.approve')) {
            abort(403, 'غير مصرح لك باعتماد المستويات.');
        }

        $request->validate([
            'level_id' => 'required|exists:levels,id',
        ]);

        $student = User::findOrFail($id);
        $level = Level::with('course')->findOrFail($request->level_id);

        // 1. قيد تسجيل الطالب في المستوى مع توثيق من اعتمد الطالب وتاريخ الاعتماد
        StudentEnrollment::create([
            'user_id' => $student->id,
            'level_id' => $level->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'approved_by' => $supervisor->id,
            'approved_at' => now(),
        ]);

        // 2. جدولة القسط المالي للمستوى
        StudentInstallment::create([
            'user_id' => $student->id,
            'level_id' => $level->id,
            'installment_number' => 1,
            'amount' => $level->fee_amount,
            'currency' => $level->currency,
            'due_date' => now()->addDays(7),
            'status' => 'unpaid',
        ]);

        // 3. تقييد الرسوم في حساب الطالب المالي (كشف حساب قياسي)
        $studentAccount = FinancialAccount::where('user_id', $student->id)->first();
        if ($studentAccount) {
            $studentAccount->increment('current_balance', $level->fee_amount);
        }

        // 4. تفعيل حساب الطالب رسمياً وتوثيق من اعتمد وتاريخ الاعتماد وتصفير أي إلغاء سابق
        $student->update([
            'status' => 'active',
            'approved_by' => $supervisor->id,
            'approved_at' => now(),
            'revoked_by' => null,
            'revoked_at' => null,
            'revoke_reason' => null,
        ]);

        // 5. إشعار الواتساب الفوري
        WhatsAppLog::create([
            'user_id' => $student->id,
            'phone_number' => $student->phone_whatsapp ?? 'unknown',
            'message_type' => 'announcement',
            'message_body' => "مرحباً {$student->name}، تمت مراجعة اختبارك واعتماد مستواك الأكاديمي رسمياً من قبل الإشراف الأكاديمي: [{$level->name}]. يمكنك الآن حجز حصصك ومتابعة دروسك من بوابتك.",
            'status' => 'sent',
        ]);

        // 6. توثيق في سجل تدقيق النظام
        SystemAuditLog::record(
            'student.level_assigned',
            "تم اعتماد المستوى الأكاديمي [{$level->name}] للطالب [{$student->name}] ({$student->student_code}) ورسم دراسي {$level->fee_amount} {$level->currency} بواسطة المشرف الأكاديمي ({$supervisor->name})",
            $student,
            [
                'level_id' => $level->id,
                'level_name' => $level->name,
                'fee_amount' => $level->fee_amount,
                'approved_by' => $supervisor->id,
                'approved_by_name' => $supervisor->name,
                'approved_at' => now()->toDateTimeString(),
            ]
        );

        return back()->with('success', "تم اعتماد المستوى [{$level->name}] للطالب [{$student->name}] بنجاح وتفعيل حسابه الأكاديمي والمالي.");
    }

    /**
     * إلغاء اعتماد وتسكين المستوى للطالب الجديد (ما دام لم يدرس أي دورات أو حصص)
     */
    public function revokeLevelApproval(Request $request, $id)
    {
        $supervisor = Auth::user();

        if (! $supervisor->isAdmin() && ! $supervisor->hasPermission('students.revoke_approval')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إلغاء اعتماد وتسكين مستويات الطلاب.');
        }

        $student = User::with(['enrollments.level', 'financialAccount'])->findOrFail($id);

        if ($student->role !== 'student') {
            return back()->with('error', 'هذه العملية مخصصة لحسابات الطلاب فقط.');
        }

        if ($student->status !== 'active' && ! $student->enrollments()->where('status', 'active')->exists()) {
            return back()->with('error', 'هذا الطالب ليس معتمداً حالياً أو ليس لديه تسكين نشط ليتم إلغاؤه.');
        }

        // 1. فحص النزاهة الأكاديمية: التحقق الصارم هل الطالب جديد ولم يدرس أي دورات أو حصص؟
        if ($student->hasStudiedAnyCourses()) {
            return back()->with('error', "لا يمكن إلغاء اعتماد الطالب ({$student->name}) لأنه بدأ الدراسة بالفعل ولديه سجلات أكاديمية (حصص، تقييمات يومية، أو درجات) مسجلة في النظام.");
        }

        // 2. فحص النزاهة المالية: هل لديه سندات قبض مالية مقبوضة ومسجلة باسمه؟
        $hasPaidReceipt = false;
        if ($student->financialAccount) {
            $hasPaidReceipt = Voucher::where('account_id', $student->financialAccount->id)
                ->where('voucher_type', 'receipt')
                ->exists();
        }
        if ($hasPaidReceipt) {
            return back()->with('error', "لا يمكن إلغاء الاعتماد لوجود سند قبض مالي مسجل باسم الطالب ({$student->name})، يرجى مراجعة الإدارة المالية أو إلغاء السند أولاً.");
        }

        $reason = trim($request->input('reason', 'إلغاء الاعتماد من الإشراف الأكاديمي لإعادة المراجعة والتسكين'));

        // 3. إلغاء القيود النشطة في student_enrollments مع توثيق من ألغى وتاريخ الإلغاء
        $activeEnrollments = StudentEnrollment::where('user_id', $student->id)->where('status', 'active')->get();
        $totalRevokedFees = 0;
        $revokedLevelsNames = [];

        foreach ($activeEnrollments as $enr) {
            $enr->update([
                'status' => 'revoked',
                'revoked_by' => $supervisor->id,
                'revoked_at' => now(),
                'revoke_reason' => $reason,
            ]);
            if ($enr->level) {
                $totalRevokedFees += (float) $enr->level->fee_amount;
                $revokedLevelsNames[] = $enr->level->name;
            }
        }

        // 4. حذف الأقساط غير المسددة المجدولة للمستوى الملغي
        StudentInstallment::where('user_id', $student->id)->where('status', 'unpaid')->delete();

        // 5. تصحيح الحساب المالي للطالب (عكس الرسوم التي قُيّدت عند الاعتماد لأن الطالب لم يدرس)
        if ($student->financialAccount && $totalRevokedFees > 0) {
            $currentBal = (float) $student->financialAccount->current_balance;
            $newBal = max(0, $currentBal - $totalRevokedFees);
            $student->financialAccount->update(['current_balance' => $newBal]);
        }

        // 6. حذف الطالب من أي قوائم مجموعات تم ربطه بها مسبقاً قبل بدء الدراسة
        DB::table('study_group_students')->where('student_id', $student->id)->delete();

        // 7. إعادة حالة الطالب إلى "بانتظار الاعتماد" وتوثيق من ألغى الاعتماد والوقت والسبب
        $student->update([
            'status' => 'pending_approval',
            'revoked_by' => $supervisor->id,
            'revoked_at' => now(),
            'revoke_reason' => $reason,
        ]);

        // 8. توثيق في سجل تدقيق النظام الشامل
        $levelsStr = ! empty($revokedLevelsNames) ? implode('، ', $revokedLevelsNames) : 'المستوى الحالي';
        SystemAuditLog::record(
            'student.approval_revoked',
            "تم إلغاء اعتماد وتسكين الطالب [{$student->name}] ({$student->student_code}) للمستوى [{$levelsStr}] وإعادته لحالة بانتظار الاعتماد بواسطة المشرف ({$supervisor->name}). السبب: {$reason}",
            $student,
            [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'revoked_by' => $supervisor->id,
                'revoked_by_name' => $supervisor->name,
                'revoked_at' => now()->toDateTimeString(),
                'reason' => $reason,
                'revoked_levels' => $revokedLevelsNames,
                'reversed_amount' => $totalRevokedFees,
            ]
        );

        return back()->with('success', "تم إلغاء اعتماد الطالب ({$student->name}) بنجاح وإعادته لقائمة مراجعة وتخصيص المستويات بانتظار الاعتماد.");
    }
}
