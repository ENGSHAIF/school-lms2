<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ExamAttempt;
use App\Models\FinancialAccount;
use App\Models\LessonReport;
use App\Models\Level;
use App\Models\StudentEnrollment;
use App\Models\StudentInstallment;
use App\Models\User;
use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    // لوحة تحكم المشرف الأكاديمي
    public function dashboard()
    {
        $supervisor = Auth::user();

        if (!$supervisor->isSupervisor() && !$supervisor->isAdmin()) {
            abort(403, 'غير مصرح لك بالدخول إلى لوحة المشرف الأكاديمي.');
        }

        // 1. الطلاب بانتظار مراجعة واعتماد المستوى بعد الاختبار
        $pendingApprovals = User::where('role', 'student')
            ->where('status', 'pending_approval')
            ->with([
                'studentProfile',
                'examAttempts' => fn($q) => $q->with(['suggestedLevel.course', 'answers'])->latest()->limit(1)
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

        if (!$supervisor->isSupervisor() && !$supervisor->isAdmin()) {
            abort(403, 'غير مصرح لك باعتماد المستويات.');
        }

        $request->validate([
            'level_id' => 'required|exists:levels,id',
        ]);

        $student = User::findOrFail($id);
        $level = Level::with('course')->findOrFail($request->level_id);

        // 1. قيد تسجيل الطالب في المستوى
        StudentEnrollment::create([
            'user_id' => $student->id,
            'level_id' => $level->id,
            'status' => 'active',
            'enrolled_at' => now(),
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

        // 4. تفعيل حساب الطالب رسمياً
        $student->update([
            'status' => 'active'
        ]);

        // 5. إشعار الواتساب الفوري
        WhatsAppLog::create([
            'user_id' => $student->id,
            'phone_number' => $student->phone_whatsapp ?? 'unknown',
            'message_type' => 'announcement',
            'message_body' => "مرحباً {$student->name}، تمت مراجعة اختبارك واعتماد مستواك الأكاديمي رسمياً من قبل الإشراف الأكاديمي: [{$level->name}]. يمكنك الآن حجز حصصك ومتابعة دروسك من بوابتك.",
            'status' => 'sent',
        ]);

        return back()->with('success', "تم اعتماد المستوى [{$level->name}] للطالب [{$student->name}] بنجاح وتفعيل حسابه الأكاديمي والمالي.");
    }
}
