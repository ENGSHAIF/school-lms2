<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\FinancialAccount;
use App\Models\LessonReport;
use App\Models\TeacherSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    // لوحة تحكم المعلم (Teacher Dashboard)
    public function dashboard()
    {
        $teacher = Auth::user();

        if (!$teacher->isTeacher() && !$teacher->isAdmin()) {
            abort(403, 'غير مصرح لك بالدخول إلى لوحة المعلم.');
        }

        // الحصص القادمة المحجوزة مع هذا المعلم
        $upcomingBookings = Booking::with(['student.studentProfile', 'schedule'])
            ->where('teacher_id', $teacher->id)
            ->where('status', 'scheduled')
            ->latest()
            ->get();

        // الحصص المنفذة والتقارير المرفوعة
        $completedBookings = Booking::with(['student', 'report'])
            ->where('teacher_id', $teacher->id)
            ->where('status', 'completed')
            ->latest()
            ->take(10)
            ->get();

        // جدول مواعيد المعلم المتاحة
        $schedules = TeacherSchedule::where('teacher_id', $teacher->id)
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        // الحساب المالي والأرباح المستحقة
        $account = FinancialAccount::firstOrCreate(
            ['user_id' => $teacher->id],
            [
                'account_code' => '22' . str_pad($teacher->id, 4, '0', STR_PAD_LEFT),
                'account_name' => 'حساب المعلم: ' . $teacher->name,
                'account_type' => 'teacher',
                'currency' => 'YER',
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
            ]
        );

        // المجموعات والدفعات الدراسية المسندة لهذا المعلم
        $myGroups = \App\Models\StudyGroup::with(['course', 'level.resources.exam', 'activities.publisher', 'students' => function ($q) {
            $q->wherePivot('status', 'active');
        }])
            ->where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('teacher.dashboard', compact(
            'teacher',
            'upcomingBookings',
            'completedBookings',
            'schedules',
            'account',
            'myGroups'
        ));
    }
}
