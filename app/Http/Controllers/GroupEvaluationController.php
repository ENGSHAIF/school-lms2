<?php

namespace App\Http\Controllers;

use App\Models\GroupSession;
use App\Models\GroupStudentEvaluation;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupEvaluationController extends Controller
{
    // لوحة تحكم تقييمات وحضور المجموعات الدراسية (يومي، أسبوعي، شهري، ونهائي)
    public function index(Request $request)
    {
        $user = auth()->user();
        $period = $request->input('period', 'daily'); // daily, weekly, monthly, final
        $selectedGroupId = $request->input('group_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to', now()->toDateString());
        $search = $request->input('search');

        // استعلام المجموعات المتاحة للمستخدم حسب صلاحيته (المعلم يرى مجموعاته، والمدير/المشرف يرى الكل)
        $groupsQuery = StudyGroup::with(['course', 'level', 'teacher', 'activeStudents']);
        if ($user->isTeacher() && !$user->isAdmin() && !$user->isSupervisor()) {
            $groupsQuery->where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id)
                  ->orWhereHas('teachers', function ($t) use ($user) {
                      $t->where('user_id', $user->id);
                  });
            });
        }
        $availableGroups = $groupsQuery->orderBy('name')->get();

        // استعلام الجلسات والتقييمات
        $sessionsQuery = GroupSession::with(['group.course', 'group.level', 'teacher', 'evaluations.student'])
            ->when($selectedGroupId, fn($q) => $q->where('study_group_id', $selectedGroupId))
            ->when($dateFrom, fn($q) => $q->where('session_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->where('session_date', '<=', $dateTo));

        if ($user->isTeacher() && !$user->isAdmin() && !$user->isSupervisor()) {
            $sessionsQuery->where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id)
                  ->orWhereHas('group.teachers', function ($t) use ($user) {
                      $t->where('user_id', $user->id);
                  });
            });
        }

        // إحصائيات عامة
        $totalSessionsCount = (clone $sessionsQuery)->count();
        $avgAttendanceRate = (clone $sessionsQuery)->avg('attendance_rate') ?? 100;
        
        $totalStarsAwarded = GroupStudentEvaluation::when($selectedGroupId, fn($q) => $q->where('study_group_id', $selectedGroupId))
            ->sum('stars_awarded');

        // الطلاب الأكثر تفاعلاً ونشاطاً
        $topActiveStudents = GroupStudentEvaluation::with('student', 'group')
            ->when($selectedGroupId, fn($q) => $q->where('study_group_id', $selectedGroupId))
            ->select(
                'student_id',
                'study_group_id',
                DB::raw('COUNT(id) as total_sessions_attended'),
                DB::raw('SUM(stars_awarded) as total_stars'),
                DB::raw('SUM(spoke_voice) as total_voice_participations'),
                DB::raw('SUM(wrote_chat) as total_chat_participations'),
                DB::raw('SUM(submitted_task) as total_homework_submissions'),
                DB::raw('AVG(participation_score) as avg_participation')
            )
            ->groupBy('student_id', 'study_group_id')
            ->orderByDesc('total_stars')
            ->orderByDesc('total_voice_participations')
            ->take(10)
            ->get();

        // ملخص كل مجموعة (Group Performance Summaries)
        $groupSummaries = [];
        foreach ($availableGroups as $grp) {
            $grpSessions = GroupSession::where('study_group_id', $grp->id)->get();
            $groupSummaries[] = (object) [
                'group' => $grp,
                'sessions_count' => $grpSessions->count(),
                'avg_attendance' => round($grpSessions->avg('attendance_rate') ?? 100, 1),
                'total_stars' => GroupStudentEvaluation::where('study_group_id', $grp->id)->sum('stars_awarded'),
                'last_session' => $grpSessions->sortByDesc('session_date')->first(),
                'active_students_count' => $grp->active_count,
            ];
        }

        // جلب البيانات حسب الفترة المختارة
        $sessions = (clone $sessionsQuery)->latest('session_date')->paginate(15);

        // إذا كان المطلوب تقييم نهاية الدورة والترم (Final Course & Diploma Evaluations)
        $finalDiplomaEvaluations = collect();
        if ($period === 'final' && $selectedGroupId) {
            $selectedGroup = StudyGroup::with(['students', 'course', 'level'])->find($selectedGroupId);
            if ($selectedGroup) {
                $finalDiplomaEvaluations = $this->calculateFinalDiplomaEvaluations($selectedGroup);
            }
        }

        return view('admin.groups.evaluations.index', compact(
            'availableGroups',
            'period',
            'selectedGroupId',
            'dateFrom',
            'dateTo',
            'search',
            'sessions',
            'totalSessionsCount',
            'avgAttendanceRate',
            'totalStarsAwarded',
            'topActiveStudents',
            'groupSummaries',
            'finalDiplomaEvaluations'
        ));
    }

    // نموذج رصد الحضور والتقييم اليومي لمجموعة محددة
    public function create($groupId)
    {
        $group = StudyGroup::with(['course', 'level', 'teacher', 'activeStudents'])->findOrFail($groupId);
        $user = auth()->user();

        // فحص الصلاحية
        $isTeacherOrStaff = ($user->isAdmin() || $user->isSupervisor() || $user->id === $group->teacher_id || $group->teachers->contains('id', $user->id));
        if (!$isTeacherOrStaff) {
            abort(403, 'غير مصرح لك برصد تقييم هذه المجموعة.');
        }

        // اقتراح رقم الجلسة القادمة
        $lastSession = GroupSession::where('study_group_id', $group->id)->latest('session_number')->first();
        $nextSessionNumber = $lastSession ? ($lastSession->session_number + 1) : 1;

        return view('admin.groups.evaluations.create', compact('group', 'nextSessionNumber'));
    }

    // حفظ الجلسة والتقييم اليومي للطلاب
    public function store(Request $request, $groupId)
    {
        $group = StudyGroup::with('activeStudents')->findOrFail($groupId);
        $user = auth()->user();

        $validated = $request->validate([
            'session_date' => 'required|date',
            'session_number' => 'required|integer|min:1',
            'lesson_topic' => 'required|string|max:250',
            'lesson_summary' => 'nullable|string',
            'homework_assigned' => 'nullable|string',
            'general_notes' => 'nullable|string',
            'evaluations' => 'required|array',
            'evaluations.*.attendance_status' => 'required|in:present,absent,late,excused',
            'evaluations.*.participation_score' => 'nullable|integer|min:1|max:5',
            'evaluations.*.understanding_score' => 'nullable|integer|min:1|max:5',
            'evaluations.*.homework_score' => 'nullable|integer|min:1|max:5',
            'evaluations.*.stars_awarded' => 'nullable|integer|min:0|max:50',
            'evaluations.*.teacher_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. حساب أعداد الحضور والغياب
            $totalStudents = count($validated['evaluations']);
            $presentCount = 0;
            $absentCount = 0;
            $lateCount = 0;
            $excusedCount = 0;

            foreach ($validated['evaluations'] as $evalData) {
                match ($evalData['attendance_status']) {
                    'present' => $presentCount++,
                    'absent' => $absentCount++,
                    'late' => $lateCount++,
                    'excused' => $excusedCount++,
                    default => $presentCount++,
                };
            }

            $effectivePresent = $presentCount + ($lateCount * 0.8) + ($excusedCount * 0.5);
            $attendanceRate = $totalStudents > 0 ? round(($effectivePresent / $totalStudents) * 100, 1) : 100.00;

            // 2. إنشاء الجلسة اليومية
            $session = GroupSession::create([
                'study_group_id' => $group->id,
                'teacher_id' => $user->isTeacher() ? $user->id : ($group->teacher_id ?: $user->id),
                'session_date' => $validated['session_date'],
                'session_number' => $validated['session_number'],
                'lesson_topic' => $validated['lesson_topic'],
                'lesson_summary' => $validated['lesson_summary'] ?? null,
                'homework_assigned' => $validated['homework_assigned'] ?? null,
                'total_students' => $totalStudents,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'late_count' => $lateCount,
                'excused_count' => $excusedCount,
                'attendance_rate' => $attendanceRate,
                'general_notes' => $validated['general_notes'] ?? null,
            ]);

            // 3. تخزين تقييم كل طالب وتحديث رصيد النجوم
            foreach ($validated['evaluations'] as $studentId => $eval) {
                $status = $eval['attendance_status'];
                $partScore = $status === 'absent' ? 0 : ($eval['participation_score'] ?? 5);
                $undScore = $status === 'absent' ? 0 : ($eval['understanding_score'] ?? 5);
                $hwScore = $status === 'absent' ? 0 : ($eval['homework_score'] ?? 5);
                $stars = $status === 'absent' ? 0 : intval($eval['stars_awarded'] ?? 0);

                // حساب الدرجة اليومية المحسوبة من 100
                $dailyScore = 0;
                if ($status === 'present') {
                    $dailyScore = (($partScore + $undScore + $hwScore) / 15) * 100;
                } elseif ($status === 'late') {
                    $dailyScore = ((($partScore + $undScore + $hwScore) / 15) * 100) * 0.85;
                } elseif ($status === 'excused') {
                    $dailyScore = 60.00;
                } else {
                    $dailyScore = 0.00;
                }

                $spokeVoice = $request->boolean("evaluations.{$studentId}.spoke_voice");
                $wroteChat = $request->boolean("evaluations.{$studentId}.wrote_chat");
                $submittedTask = $request->boolean("evaluations.{$studentId}.submitted_task");

                GroupStudentEvaluation::create([
                    'group_session_id' => $session->id,
                    'study_group_id' => $group->id,
                    'student_id' => $studentId,
                    'attendance_status' => $status,
                    'participation_score' => $partScore,
                    'understanding_score' => $undScore,
                    'homework_score' => $hwScore,
                    'spoke_voice' => $spokeVoice,
                    'wrote_chat' => $wroteChat,
                    'submitted_task' => $submittedTask,
                    'stars_awarded' => $stars,
                    'daily_total_score' => round($dailyScore, 1),
                    'teacher_notes' => $eval['teacher_notes'] ?? null,
                ]);

                // تحديث رصيد نجوم الطالب المكتسبة في حسابه الشخصي
                if ($stars > 0) {
                    $student = User::find($studentId);
                    if ($student) {
                        $student->increment('stars_balance', $stars);
                    }
                }
            }

            DB::commit();

            // إرسال إشعارات التقييم والحضور عبر الواتساب للطلاب
            if (\App\Models\SiteSetting::get('whatsapp_notify_evaluation', true)) {
                foreach ($validated['evaluations'] as $studentId => $eval) {
                    $student = User::find($studentId);
                    if ($student && !empty($student->phone_whatsapp)) {
                        $statusName = match ($eval['attendance_status'] ?? 'present') {
                            'present' => 'حاضر ✅',
                            'absent' => 'غائب ❌',
                            'late' => 'متأخر ⏰',
                            'excused' => 'معذور 📝',
                            default => 'حاضر ✅',
                        };

                        $evalMsg = \App\Services\WhatsAppService::renderTemplate(
                            \App\Models\SiteSetting::get('wa_template_evaluation_body', "عزيزنا الطالب {name} 📋\n\nتم رصد تقرير الحصة الدراسية رقم ({session_no}) لمجموعة ({group_name}):\n- حالة الحضور: {status}\n- موضوع الدرس: {topic}\n- النجوم المكتسبة: {stars} ⭐\n\nنتمنى لك دوام التقدم والاجتهاد في {school_name}!"),
                            [
                                '{name}' => $student->name,
                                '{session_no}' => $session->session_number,
                                '{group_name}' => $group->name,
                                '{status}' => $statusName,
                                '{topic}' => $session->lesson_topic,
                                '{stars}' => intval($eval['stars_awarded'] ?? 0),
                            ]
                        );

                        \App\Services\WhatsAppService::send(
                            $student->phone_whatsapp,
                            $evalMsg,
                            null,
                            $student->id,
                            'evaluation'
                        );
                    }
                }
            }

            return redirect()->route('admin.groups.evaluations.index', ['group_id' => $group->id])
                ->with('success', "تم رصد تقرير وحضور الحصة رقم [{$session->session_number}] لمجموعة [{$group->name}] بنجاح، وترحيل النجوم إلى أرصدة الطلاب!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ التقييم: ' . $e->getMessage());
        }
    }

    // عرض تفاصيل جلسة محددة
    public function showSession($sessionId)
    {
        $session = GroupSession::with(['group.course', 'group.level', 'teacher', 'evaluations.student'])->findOrFail($sessionId);
        return view('admin.groups.evaluations.show_session', compact('session'));
    }

    // حذف جلسة وتقييمها في حال الخطأ
    public function destroySession($sessionId)
    {
        $session = GroupSession::with('evaluations')->findOrFail($sessionId);
        $groupId = $session->study_group_id;

        // خصم النجوم التي تم منحها في هذه الجلسة من رصيد الطلاب قبل الحذف
        foreach ($session->evaluations as $eval) {
            if ($eval->stars_awarded > 0) {
                $student = User::find($eval->student_id);
                if ($student && $student->stars_balance >= $eval->stars_awarded) {
                    $student->decrement('stars_balance', $eval->stars_awarded);
                }
            }
        }

        $session->delete();

        return redirect()->route('admin.groups.evaluations.index', ['group_id' => $groupId])
            ->with('success', 'تم حذف سجل الحصة وتقييماتها بنجاح وإعادة ضبط أرصدة النجوم.');
    }

    // طباعة التقارير الرسمية (يومي، أسبوعي، شهري، ونهائي للدبلوم)
    public function printReport(Request $request)
    {
        $period = $request->input('period', 'daily');
        $groupId = $request->input('group_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to', now()->toDateString());
        $sessionId = $request->input('session_id');

        $group = $groupId ? StudyGroup::with(['course', 'level', 'teacher', 'students'])->find($groupId) : null;
        
        if ($sessionId) {
            $session = GroupSession::with(['group.course', 'group.level', 'teacher', 'evaluations.student'])->findOrFail($sessionId);
            return view('admin.groups.evaluations.print_daily', compact('session'));
        }

        if ($period === 'final' && $group) {
            $evaluations = $this->calculateFinalDiplomaEvaluations($group);
            return view('admin.groups.evaluations.print_final', compact('group', 'evaluations'));
        }

        $sessions = GroupSession::with(['group.course', 'group.level', 'teacher', 'evaluations.student'])
            ->when($groupId, fn($q) => $q->where('study_group_id', $groupId))
            ->when($dateFrom, fn($q) => $q->where('session_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->where('session_date', '<=', $dateTo))
            ->latest('session_date')
            ->get();

        return view('admin.groups.evaluations.print_periodic', compact('sessions', 'group', 'period', 'dateFrom', 'dateTo'));
    }

    // حساب التقييم النهائي التراكمي لطلاب المجموعة لشهادة الدبلوم (Grading Engine for Diploma)
    private function calculateFinalDiplomaEvaluations(StudyGroup $group)
    {
        $students = $group->students;
        $sessions = GroupSession::where('study_group_id', $group->id)->get();
        $totalSessionsCount = $sessions->count();

        $results = collect();

        foreach ($students as $student) {
            $evals = GroupStudentEvaluation::where('study_group_id', $group->id)
                ->where('student_id', $student->id)
                ->get();

            $attendedCount = $evals->whereIn('attendance_status', ['present', 'late'])->count();
            $attendancePercentage = $totalSessionsCount > 0 ? round(($attendedCount / $totalSessionsCount) * 100, 1) : 100;
            
            // 1. درجة المواظبة والحضور (من 20 درجة)
            $attendanceScore = round(($attendancePercentage / 100) * 20, 1);

            // 2. درجة المشاركة والتفاعل الصوتي والكتابي (من 20 درجة)
            $avgParticipation = $evals->count() > 0 ? $evals->avg('participation_score') : 5;
            $participationScore = round(($avgParticipation / 5) * 20, 1);

            // 3. درجة الواجبات وأوراق الإجابات (من 20 درجة)
            $avgHomework = $evals->count() > 0 ? $evals->avg('homework_score') : 5;
            $homeworkScore = round(($avgHomework / 5) * 20, 1);

            // 4. درجات الكويزات والاختبارات الدورية للمجموعة (من 20 درجة)
            $examAttempts = \App\Models\ExamAttempt::where('user_id', $student->id)
                ->whereIn('status', ['submitted', 'graded', 'reviewed'])
                ->get();
            $quizAvg = $examAttempts->count() > 0 ? $examAttempts->avg('percentage') : 90;
            $quizScore = round(($quizAvg / 100) * 20, 1);

            // 5. تقييم المعلم ومكافآت النجوم والسلوك (من 20 درجة)
            $totalStars = $evals->sum('stars_awarded');
            $starsBonus = min(20, round(15 + ($totalStars > 10 ? 5 : ($totalStars * 0.5)), 1));

            // المجموع النهائي الكلي من 100%
            $finalTotalGrade = round($attendanceScore + $participationScore + $homeworkScore + $quizScore + $starsBonus, 1);
            $finalTotalGrade = min(100, $finalTotalGrade);

            // التقدير اللفظي
            $gradeLetter = match (true) {
                $finalTotalGrade >= 90 => 'ممتاز مرتفع (A+)',
                $finalTotalGrade >= 85 => 'ممتاز (A)',
                $finalTotalGrade >= 80 => 'جيد جداً مرتفع (B+)',
                $finalTotalGrade >= 75 => 'جيد جداً (B)',
                $finalTotalGrade >= 70 => 'جيد (C)',
                $finalTotalGrade >= 60 => 'مقبول (D)',
                default => 'يحتاج إعادة وتقوية (F)',
            };

            $results->push((object) [
                'student' => $student,
                'total_sessions' => $totalSessionsCount,
                'attended_sessions' => $attendedCount,
                'attendance_percentage' => $attendancePercentage,
                'attendance_score' => $attendanceScore,
                'participation_score' => $participationScore,
                'homework_score' => $homeworkScore,
                'quiz_score' => $quizScore,
                'stars_bonus' => $starsBonus,
                'total_stars' => $totalStars,
                'final_grade' => $finalTotalGrade,
                'grade_letter' => $gradeLetter,
            ]);
        }

        return $results->sortByDesc('final_grade');
    }
}
