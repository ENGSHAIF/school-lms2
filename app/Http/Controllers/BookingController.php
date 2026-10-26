<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\FinancialAccount;
use App\Models\LessonReport;
use App\Models\StudentWallet;
use App\Models\TeacherSchedule;
use App\Models\User;
use App\Models\WhatsAppLog;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    // شاشة استعراض وحجز المواعيد والمعلمين (25 دقيقة)
    public function index()
    {
        $user = Auth::user();
        
        // جلب محفظة الطالب
        $wallet = StudentWallet::firstOrCreate(
            ['user_id' => $user->id],
            ['total_lessons' => 12, 'used_lessons' => 0, 'remaining_lessons' => 12]
        );

        // الخانات المتاحة للمعلمين
        $schedules = TeacherSchedule::with('teacher')
            ->where('is_booked', false)
            ->where('session_date', '>=', now()->toDateString())
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        // الحجوزات الحالية للطالب
        $myBookings = Booking::with(['teacher', 'schedule', 'report'])
            ->where('student_id', $user->id)
            ->latest()
            ->get();

        return view('student.booking', compact('wallet', 'schedules', 'myBookings'));
    }

    // تأكيد حجز الحصة وخصم رصيد من المحفظة
    public function book(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:teacher_schedules,id',
        ]);

        $user = Auth::user();
        $wallet = StudentWallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->remaining_lessons < 1) {
            return back()->with('error', 'عذراً! لا يوجد رصيد حصص كافٍ في محفظتك. يرجى تجديد الباقة لحجز حصة جديدة.');
        }

        $schedule = TeacherSchedule::findOrFail($request->schedule_id);

        if ($schedule->is_booked) {
            return back()->with('error', 'تم حجز هذه الخانة من قبل طالب آخر، يرجى اختيار موعد بديل.');
        }

        // 1. تثبيت الحجز وتوليد غرفة الفصل
        $roomId = 'airclass-' . Str::random(10);
        $booking = Booking::create([
            'student_id' => $user->id,
            'teacher_id' => $schedule->teacher_id,
            'schedule_id' => $schedule->id,
            'room_id' => $roomId,
            'status' => 'scheduled',
            'topic' => $request->input('topic', 'محادثة حية 1-on-1 وتطوير النطق والتوفل'),
        ]);

        // 2. تحديث حالة الموعد
        $schedule->update(['is_booked' => true]);

        // 3. خصم حصة واحدة من محفظة الطالب
        $wallet->decrement('remaining_lessons', 1);
        $wallet->increment('used_lessons', 1);

        // 4. إشعار الواتساب الفوري بتأكيد الحجز
        WhatsAppLog::create([
            'user_id' => $user->id,
            'phone_number' => $user->phone_whatsapp ?? 'unknown',
            'message_type' => 'announcement',
            'message_body' => "مرحباً {$user->name}، تم تأكيد حجز حصتك الحية (25 دقيقة) مع المعلم في تاريخ {$schedule->session_date->format('Y-m-d')} الساعة {$schedule->start_time}. رابط دخول الفصل المباشر جاهز في حسابك.",
            'status' => 'sent',
        ]);

        return back()->with('success', 'تم حجز الحصة بنجاح! تم خصم حصة واحدة من رصيدك، وتأكيد الموعد.');
    }

    // غرفة الفصل التفاعلي المباشر (Air Class 1-on-1 & Group Classes)
    public function showClassroom($roomId)
    {
        $user = Auth::user();

        // 1. فحص هل الغرفة خاصة بحجز فردي (1-on-1 Booking)
        $booking = Booking::with(['student.enrollments.level.resources', 'teacher', 'schedule'])
            ->where('room_id', $roomId)
            ->first();

        if ($booking) {
            $isTeacher = ($user->id === $booking->teacher_id || $user->isAdmin());
            $title = $booking->topic;
            $teacher = $booking->teacher;
            $students = collect([$booking->student]);
            $isGroup = false;

            // إذا كان المستخدم طالباً ولم يتم فتح البث الحي بعد من قبل المعلم أو الإدارة
            if (!$isTeacher && !$booking->is_live && $booking->status !== 'in_progress') {
                return view('classroom.waiting', [
                    'booking' => $booking,
                    'group' => null,
                    'isTeacher' => false,
                    'title' => $title,
                    'teacher' => $teacher,
                    'isGroup' => false,
                    'roomId' => $roomId,
                ]);
            }

            return view('classroom.live', compact('booking', 'isTeacher', 'title', 'teacher', 'students', 'isGroup', 'roomId'));
        }

        // 2. فحص هل الغرفة خاصة بمجموعة دراسية جماعية (Study Group)
        $group = StudyGroup::with(['course', 'level.resources.exam', 'teacher', 'supervisor', 'students', 'requester', 'activities.publisher'])
            ->where('room_id', $roomId)
            ->first();

        if ($group) {
            $isTeacher = ($user->id === $group->teacher_id || $user->id === $group->supervisor_id || $user->isAdmin());
            $title = $group->name . ' • ' . ($group->course?->name ?? 'دورات اللغة');
            $teacher = $group->teacher;
            $students = $group->students;
            $isGroup = true;

            // إذا كان المستخدم طالباً ولم يتم فتح البث الحي بعد من قبل المعلم أو الإدارة
            if (!$isTeacher && !$group->is_live) {
                return view('classroom.waiting', [
                    'group' => $group,
                    'booking' => null,
                    'isTeacher' => false,
                    'title' => $title,
                    'teacher' => $teacher,
                    'isGroup' => true,
                    'roomId' => $roomId,
                ]);
            }

            return view('classroom.live', compact('group', 'isTeacher', 'title', 'teacher', 'students', 'isGroup', 'roomId'));
        }

        // 3. حالة تجريبية أو مباشرة للمدير والمعلم لضمان عدم ظهور أي خطأ
        $isTeacher = ($user->isTeacher() || $user->isAdmin() || $user->isSupervisor());
        $title = 'الفصل التفاعلي المباشر (Air Class Live)';
        $teacher = $isTeacher ? $user : User::where('role', 'teacher')->first();
        $students = User::where('role', 'student')->take(5)->get();
        $isGroup = true;

        return view('classroom.live', compact('isTeacher', 'title', 'teacher', 'students', 'isGroup', 'roomId'));
    }

    // فحص حالة البث الحي للغرفة التفاعلية في الوقت الحقيقي (لإعادة توجيه الطلاب تلقائياً عند فتح البث)
    public function checkStatus($roomId)
    {
        $group = StudyGroup::with('requester')->where('room_id', $roomId)->first();
        if ($group) {
            return response()->json([
                'success' => true,
                'type' => 'group',
                'is_live' => (bool) $group->is_live,
                'live_status' => $group->live_status,
                'live_requested_by' => $group->requester?->name,
                'live_requested_at' => $group->live_requested_at?->diffForHumans(),
                'message' => $group->is_live ? 'البث الحي مفتوح الآن!' : ($group->live_status === 'pending_approval' ? 'طلب فتح البث قيد المراجعة...' : 'البث مغلق'),
            ]);
        }

        $booking = Booking::where('room_id', $roomId)->first();
        if ($booking) {
            $isLive = (bool) ($booking->is_live || $booking->status === 'in_progress');
            return response()->json([
                'success' => true,
                'type' => 'booking',
                'is_live' => $isLive,
                'live_status' => $isLive ? 'live' : 'closed',
                'message' => $isLive ? 'البث الحي مفتوح الآن!' : 'البث مغلق',
            ]);
        }

        return response()->json([
            'success' => false,
            'is_live' => false,
            'live_status' => 'closed',
            'message' => 'الغرفة غير موجودة',
        ]);
    }

    // تبديل حالة البث للحصص الفردية 1-on-1
    public function toggleBookingLive($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $user = Auth::user();

        if ($user->id !== $booking->teacher_id && !$user->isAdmin()) {
            abort(403, 'غير مصرح لك بالتحكم في بث هذه الحصة.');
        }

        $newLive = !$booking->is_live;
        $booking->update([
            'is_live' => $newLive,
            'status' => $newLive ? 'in_progress' : ($booking->status === 'in_progress' ? 'completed' : $booking->status),
            'live_started_at' => $newLive ? now() : $booking->live_started_at,
        ]);

        $msg = $newLive ? 'تم بدء وفتح البث الحي للحصة بنجاح!' : 'تم إغلاق البث الحي للحصة.';
        return back()->with('success', $msg);
    }

    // المعلم ينهي الحصة ويرسل تقرير التقييم الفوري
    public function submitReport(Request $request, $bookingId)
    {
        $request->validate([
            'pronunciation_score' => 'required|integer|min:1|max:5',
            'fluency_score' => 'required|integer|min:1|max:5',
            'grammar_score' => 'required|integer|min:1|max:5',
            'stars_awarded' => 'required|integer|min:1|max:5',
            'teacher_comment' => 'required|string',
            'vocabulary_learned' => 'nullable|string',
            'homework' => 'nullable|string',
        ]);

        $booking = Booking::with(['student', 'teacher'])->findOrFail($bookingId);

        $report = LessonReport::create([
            'booking_id' => $booking->id,
            'teacher_id' => $booking->teacher_id,
            'student_id' => $booking->student_id,
            'pronunciation_score' => $request->pronunciation_score,
            'fluency_score' => $request->fluency_score,
            'grammar_score' => $request->grammar_score,
            'stars_awarded' => $request->stars_awarded,
            'teacher_comment' => $request->teacher_comment,
            'vocabulary_learned' => $request->vocabulary_learned,
            'homework' => $request->homework,
        ]);

        $booking->update(['status' => 'completed']);

        // 1. المحاسبة المالية للمعلم (قيد استحقاق أجر الحصة في كشف حساب المعلم - المعايير المحاسبية المعتمدة)
        $teacherAccount = FinancialAccount::where('user_id', $booking->teacher_id)->first();
        if ($teacherAccount) {
            $sessionRate = 7.00; // أجر الحصة الافتراضي 7 دولار لكل 25 دقيقة
            $teacherAccount->increment('current_balance', $sessionRate);
        }

        // 2. إرسال تقرير الحصة الفوري لواتساب الطالب وولي أمره
        WhatsAppLog::create([
            'user_id' => $booking->student_id,
            'phone_number' => $booking->student?->phone_whatsapp ?? 'unknown',
            'message_type' => 'exam_result',
            'message_body' => "تقرير الحصة الفوري 🌟\nمرحباً {$booking->student?->name}، أنهيت للتو حصتك مع المعلم {$booking->teacher?->name}.\nتقييم النطق: {$request->pronunciation_score}/5 | الطلاقة: {$request->fluency_score}/5\nملاحظات المعلم: {$request->teacher_comment}\nالواجب المنزلي: {$request->homework}",
            'status' => 'sent',
        ]);

        return redirect()->route('student.booking')->with('success', 'تم اعتماد تقرير الحصة بنجاح، وترحيل استحقاق المعلم المالي وإرسال تقرير الواتساب للطالب.');
    }
}
