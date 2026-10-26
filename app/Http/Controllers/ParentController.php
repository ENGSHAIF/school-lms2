<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ExamAttempt;
use App\Models\FinancialAccount;
use App\Models\GroupActivity;
use App\Models\GroupSession;
use App\Models\GroupStudentEvaluation;
use App\Models\LessonReport;
use App\Models\StudentEnrollment;
use App\Models\StudentInstallment;
use App\Models\StudentProfile;
use App\Models\StudentWallet;
use App\Models\StudyGroup;
use App\Models\TeacherSchedule;
use App\Models\User;
use App\Models\Voucher;
use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParentController extends Controller
{
    // لوحة تحكم ولي الأمر ومتابعة الأبناء
    public function dashboard(Request $request)
    {
        $parent = Auth::user();

        if (!$parent->isParent() && !$parent->isAdmin()) {
            abort(403, 'غير مصرح لك بالدخول إلى لوحة ولي الأمر.');
        }

        // فحص صلاحية دخول لوحة ولي الأمر
        if (!$parent->hasPermission('parent.dashboard')) {
            abort(403, 'غير مصرح لك بالوصول إلى لوحة ولي الأمر بحسب الصلاحيات الممنوحة لحسابك.');
        }

        // جلب جميع الأبناء المرتبطين بحساب ولي الأمر
        $childrenProfiles = StudentProfile::with([
            'user.financialAccount',
            'user.enrollments.level.course',
            'user.studyGroups.course',
            'user.studyGroups.level',
            'user.studyGroups.teachers',
            'user.studyGroups.supervisors',
        ])
        ->where('parent_id', $parent->id)
        ->get();

        // إذا كان مدير نظام ولم يكن لديه أبناء مسجلين، نحضر له أول طالب مسجل في النظام كمعاينة
        if ($childrenProfiles->isEmpty() && $parent->isAdmin()) {
            $demoStudent = User::where('role', 'student')->first();
            if ($demoStudent) {
                $fakeProfile = new StudentProfile([
                    'user_id' => $demoStudent->id,
                    'parent_id' => $parent->id,
                    'stage' => 'primary',
                    'grade_substage' => 'معاينة تجريبية للإدارة',
                ]);
                $fakeProfile->setRelation('user', $demoStudent->load([
                    'financialAccount',
                    'enrollments.level.course',
                    'studyGroups.course',
                    'studyGroups.level',
                    'studyGroups.teachers',
                    'studyGroups.supervisors',
                ]));
                $childrenProfiles = collect([$fakeProfile]);
            }
        }

        $activeChildId = $request->query('child_id') ?? $childrenProfiles->first()?->user_id;
        $activeChildProfile = $childrenProfiles->firstWhere('user_id', $activeChildId) ?? $childrenProfiles->first();
        $activeChild = $activeChildProfile?->user;

        // بيانات المحفظة وحصص الـ 1-on-1 التجريبية
        $wallet = $activeChild ? StudentWallet::firstOrCreate(
            ['user_id' => $activeChild->id],
            ['total_lessons' => 1, 'used_lessons' => 0, 'remaining_lessons' => 1]
        ) : null;

        $bookings = $activeChild ? Booking::with(['teacher', 'schedule', 'report'])
            ->where('student_id', $activeChild->id)
            ->latest()
            ->get() : collect();

        $reports = $activeChild ? LessonReport::with(['teacher', 'booking'])
            ->where('student_id', $activeChild->id)
            ->latest()
            ->get() : collect();

        // المجموعات الدراسية المسكن فيها الطالب
        $studyGroups = $activeChild ? $activeChild->studyGroups()->with(['course', 'level', 'teachers', 'supervisors'])->get() : collect();

        // سجل الحضور والتقييم اليومي ومنح النجوم
        $evaluations = $activeChild ? GroupStudentEvaluation::with(['session.teacher', 'group.course', 'group.level'])
            ->where('student_id', $activeChild->id)
            ->latest()
            ->get() : collect();

        $totalSessions = $evaluations->count();
        $presentCount = $evaluations->where('attendance_status', 'present')->count();
        $absentCount = $evaluations->where('attendance_status', 'absent')->count();
        $lateCount = $evaluations->where('attendance_status', 'late')->count();
        $excusedCount = $evaluations->where('attendance_status', 'excused')->count();
        $attendanceRate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : 100;
        $totalStars = (int)$evaluations->sum('stars_awarded') + (int)$reports->sum('stars_awarded');

        // التكليفات والواجبات المدرسية للشعب المسكن بها
        $groupActivities = $studyGroups->isNotEmpty() ? GroupActivity::whereIn('study_group_id', $studyGroups->pluck('id'))
            ->with(['group', 'publisher'])
            ->latest()
            ->take(20)
            ->get() : collect();

        // نتائج الاختبارات والكويزات الدورية
        $examAttempts = $activeChild ? ExamAttempt::with(['exam.course', 'exam.level'])
            ->where('user_id', $activeChild->id)
            ->latest()
            ->get() : collect();

        // الرسوم المالية وسندات القبض والأقساط
        $account = $activeChild ? $activeChild->financialAccount : null;
        $vouchers = $account ? Voucher::where('account_id', $account->id)->latest()->get() : collect();
        $installments = $activeChild ? StudentInstallment::with('level')->where('user_id', $activeChild->id)->orderBy('installment_number')->get() : collect();

        $totalTuition = $installments->sum('amount');
        $totalDiscounts = $installments->sum('discount_amount');
        $netTuition = max(0, $totalTuition - $totalDiscounts);
        $totalPaid = $vouchers->where('voucher_type', 'receipt')->sum('amount');
        $currentBalance = $account ? (float)$account->current_balance : ($netTuition - $totalPaid);

        // مواعيد المعلمين المتاحة للحجز السريع
        $availableSchedules = TeacherSchedule::with('teacher')
            ->where('is_booked', false)
            ->where('session_date', '>=', now()->toDateString())
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->take(8)
            ->get();

        return view('parent.dashboard', compact(
            'parent',
            'childrenProfiles',
            'activeChildProfile',
            'activeChild',
            'wallet',
            'bookings',
            'reports',
            'studyGroups',
            'evaluations',
            'totalSessions',
            'presentCount',
            'absentCount',
            'lateCount',
            'excusedCount',
            'attendanceRate',
            'totalStars',
            'groupActivities',
            'examAttempts',
            'account',
            'vouchers',
            'installments',
            'totalTuition',
            'totalDiscounts',
            'netTuition',
            'totalPaid',
            'currentBalance',
            'availableSchedules'
        ));
    }

    // إضافة طفل تجريبي جديد وربطه بحساب ولي الأمر
    public function storeChild(Request $request)
    {
        $parent = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stage' => 'required|string|in:kindergarten,primary,middle_school,high_school',
            'grade_substage' => 'nullable|string|max:100',
        ]);

        $latestId = User::max('id') ?? 0;
        $studentCode = 'STU-' . date('Y') . '-' . str_pad($latestId + 1, 4, '0', STR_PAD_LEFT);
        $email = 'child_' . ($latestId + 1) . '@school.com';

        $child = User::create([
            'name' => $validated['name'],
            'email' => $email,
            'phone_whatsapp' => $parent->phone_whatsapp,
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'pending_test',
            'student_code' => $studentCode,
        ]);

        StudentProfile::create([
            'user_id' => $child->id,
            'parent_id' => $parent->id,
            'stage' => $validated['stage'],
            'grade_substage' => $validated['grade_substage'] ?? 'مستوى تجريبي جديد',
            'learning_goals' => ['نطق الحروف والأصوات', 'المحادثة التفاعلية 1-on-1'],
            'preferred_time' => 'مسائي (5:00 م - 9:00 م)',
            'preferred_frequency' => 'حصتان إلى 3 حصص أسبوعياً',
            'parent_consent' => true,
            'is_trial_booked' => false,
        ]);

        StudentWallet::create([
            'user_id' => $child->id,
            'total_lessons' => 1,
            'used_lessons' => 0,
            'remaining_lessons' => 1,
        ]);

        FinancialAccount::create([
            'account_code' => '21' . str_pad($child->id, 4, '0', STR_PAD_LEFT),
            'account_name' => 'حساب الطالب: ' . $child->name,
            'account_type' => 'student',
            'user_id' => $child->id,
            'currency' => 'YER',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
        ]);

        return redirect()->route('parent.dashboard', ['child_id' => $child->id])
            ->with('success', "تم إضافة [{$child->name}] بنجاح، ومُنح حصة تجريبية مجانية 1-on-1!");
    }

    // حجز حصة نيابة عن الطفل
    public function bookForChild(Request $request, $childId)
    {
        $parent = Auth::user();
        $child = User::findOrFail($childId);

        // التحقق من أن الطفل مسجل تحت هذا الوالد
        $profile = StudentProfile::where('user_id', $child->id)->where('parent_id', $parent->id)->first();
        if (!$profile && !$parent->isAdmin()) {
            abort(403, 'غير مصرح لك بالحجز لهذا الطالب.');
        }

        $request->validate([
            'schedule_id' => 'required|exists:teacher_schedules,id',
        ]);

        $wallet = StudentWallet::where('user_id', $child->id)->first();
        if (!$wallet || $wallet->remaining_lessons < 1) {
            return back()->with('error', "عذراً! لا يوجد رصيد حصص كافٍ في محفظة [{$child->name}]. يرجى شحن باقة الحصص أولاً.");
        }

        $schedule = TeacherSchedule::findOrFail($request->schedule_id);
        if ($schedule->is_booked) {
            return back()->with('error', 'تم حجز هذه الخانة من قبل طالب آخر، يرجى اختيار موعد بديل.');
        }

        $roomId = 'airclass-' . Str::random(10);
        Booking::create([
            'student_id' => $child->id,
            'teacher_id' => $schedule->teacher_id,
            'schedule_id' => $schedule->id,
            'room_id' => $roomId,
            'status' => 'scheduled',
            'topic' => $request->input('topic', 'حصة تدريبية فردية 1-on-1'),
        ]);

        $schedule->update(['is_booked' => true]);
        $wallet->decrement('remaining_lessons', 1);
        $wallet->increment('used_lessons', 1);

        WhatsAppLog::create([
            'user_id' => $child->id,
            'phone_number' => $parent->phone_whatsapp ?? 'unknown',
            'message_type' => 'announcement',
            'message_body' => "ولي الأمر العزيز، تم تأكيد حجز حصة للطفل [{$child->name}] مع المعلم في تاريخ {$schedule->session_date->format('Y-m-d')} الساعة {$schedule->start_time}. رابط الدخول جاهز في لوحة تحكمك.",
            'status' => 'sent',
        ]);

        return back()->with('success', "تم تأكيد حجز الحصة بنجاح لـ [{$child->name}] وإشعار هاتفك عبر الواتساب.");
    }
}
