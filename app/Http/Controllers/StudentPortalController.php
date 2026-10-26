<?php

namespace App\Http\Controllers;

use App\Models\StudentEnrollment;
use App\Models\Voucher;
use App\Models\StudentInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPortalController extends Controller
{
    public function portal()
    {
        $user = Auth::user();

        // المستوى الدراسي النشط
        $enrollment = StudentEnrollment::with(['level.lessons', 'level.books', 'level.course', 'level.resources.exam'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        $account = $user->financialAccount;
        $vouchers = Voucher::where('account_id', $account?->id)->latest()->get();
        $installments = StudentInstallment::where('user_id', $user->id)->get();

        // المجموعة الدراسية المسكن فيها الطالب حالياً
        $studyGroup = $user->studyGroups()
            ->with(['course', 'level.resources.exam', 'teacher', 'supervisor', 'activities.publisher', 'activities.levelResource'])
            ->wherePivot('status', 'active')
            ->latest('study_group_students.created_at')
            ->first();

        // المستوى الفعلي المعتمد للطالب (سواء من التسجيل المباشر أو من مجموعته الدراسية)
        $activeLevel = $enrollment?->level ?: $studyGroup?->level;

        return view('student.portal', compact('user', 'enrollment', 'activeLevel', 'account', 'vouchers', 'installments', 'studyGroup'));
    }
}
