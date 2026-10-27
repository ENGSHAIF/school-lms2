<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\StudentSurvey;
use App\Models\StudyGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentSurveyController extends Controller
{
    /**
     * لوحة تحكم إدارة الاستبيانات والتقييمات للطلاب (Admin Surveys Hub)
     */
    public function index(Request $request): View
    {
        $query = StudentSurvey::with(['student', 'teacher', 'studyGroup', 'course'])->latest('survey_date');

        // 1. فلتر نوع الاستبيان
        $surveyType = $request->input('survey_type', 'all');
        if ($surveyType !== 'all' && in_array($surveyType, ['daily', 'weekly', 'diploma_final'])) {
            $query->where('survey_type', $surveyType);
        }

        // 2. فلتر الأستاذ
        $teacherId = $request->input('teacher_id');
        if (! empty($teacherId)) {
            $query->where('teacher_id', $teacherId);
        }

        // 3. فلتر المجموعة الدراسية
        $studyGroupId = $request->input('study_group_id');
        if (! empty($studyGroupId)) {
            $query->where('study_group_id', $studyGroupId);
        }

        // 4. فلتر الفترة الزمنية (اليوم، الأسبوع، الشهر، أو مخصص)
        $period = $request->input('period', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($period === 'today') {
            $query->whereDate('survey_date', Carbon::today());
        } elseif ($period === 'week') {
            $query->whereBetween('survey_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $query->whereBetween('survey_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } elseif ($period === 'custom' || (! empty($dateFrom) && ! empty($dateTo))) {
            if (! empty($dateFrom)) {
                $query->whereDate('survey_date', '>=', $dateFrom);
            }
            if (! empty($dateTo)) {
                $query->whereDate('survey_date', '<=', $dateTo);
            }
        }

        // 5. فلتر تقييم النجوم (الحد الأدنى والأعلى)
        if ($request->filled('min_score')) {
            $query->where('overall_score', '>=', (float) $request->input('min_score'));
        }

        // 6. بحث بالاسم أو الهاتف
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_phone', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // نأخذ نسخة من الاستعلام لحساب الإحصائيات قبل التصفح (Pagination)
        $statsQuery = clone $query;
        $totalSurveys = (clone $statsQuery)->count();
        $avgOverall = round((float) ((clone $statsQuery)->avg('overall_score') ?? 0), 2);
        $avgTeacher = round((float) ((clone $statsQuery)->whereNotNull('teacher_rating')->avg('teacher_rating') ?? 0), 2);
        $avgCurriculum = round((float) ((clone $statsQuery)->whereNotNull('curriculum_rating')->avg('curriculum_rating') ?? 0), 2);
        $avgSupport = round((float) ((clone $statsQuery)->whereNotNull('support_rating')->avg('support_rating') ?? 0), 2);

        // إحصائيات التحديات الأكثر شيوعاً
        $allSurveysForStats = (clone $statsQuery)->select('learning_challenges', 'daily_benefited', 'recommend_institute', 'teacher_id')->get();

        $challengesCount = [
            'speaking' => 0,
            'listening' => 0,
            'reading' => 0,
            'writing' => 0,
            'grammar' => 0,
        ];

        $recommendationCount = [
            'yes' => 0,
            'maybe' => 0,
            'no' => 0,
        ];

        foreach ($allSurveysForStats as $surveyItem) {
            if (is_array($surveyItem->learning_challenges)) {
                foreach ($surveyItem->learning_challenges as $ch) {
                    if (isset($challengesCount[$ch])) {
                        $challengesCount[$ch]++;
                    }
                }
            }
            if (! empty($surveyItem->recommend_institute) && isset($recommendationCount[$surveyItem->recommend_institute])) {
                $recommendationCount[$surveyItem->recommend_institute]++;
            }
        }

        // توزيع التقييمات حسب المعلمين (Teacher Leaderboard)
        $teacherStats = [];
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        foreach ($teachers as $teacher) {
            $tSurveys = $allSurveysForStats->where('teacher_id', $teacher->id);
            if ($tSurveys->count() > 0) {
                $tAvg = (clone $statsQuery)->where('teacher_id', $teacher->id)->avg('teacher_rating');
                $teacherStats[] = [
                    'teacher' => $teacher,
                    'count' => $tSurveys->count(),
                    'avg' => round((float) ($tAvg ?? 0), 2),
                ];
            }
        }
        usort($teacherStats, fn ($a, $b) => $b['avg'] <=> $a['avg']);

        $surveys = $query->paginate(20)->withQueryString();
        $studyGroups = StudyGroup::with('teacher', 'course')->orderBy('name_ar')->get();
        $courses = Course::orderBy('name_ar')->get();

        $selectedTeacher = $teacherId ? User::find($teacherId) : null;
        $selectedGroup = $studyGroupId ? StudyGroup::find($studyGroupId) : null;

        return view('admin.surveys.index', compact(
            'surveys',
            'teachers',
            'studyGroups',
            'courses',
            'totalSurveys',
            'avgOverall',
            'avgTeacher',
            'avgCurriculum',
            'avgSupport',
            'challengesCount',
            'recommendationCount',
            'teacherStats',
            'surveyType',
            'teacherId',
            'studyGroupId',
            'period',
            'dateFrom',
            'dateTo',
            'selectedTeacher',
            'selectedGroup'
        ));
    }

    /**
     * طباعة تقرير تحليلي للاستبيانات (Print A4 / PDF Report)
     */
    public function printReport(Request $request): View
    {
        $query = StudentSurvey::with(['student', 'teacher', 'studyGroup', 'course'])->latest('survey_date');

        $surveyType = $request->input('survey_type', 'all');
        if ($surveyType !== 'all' && in_array($surveyType, ['daily', 'weekly', 'diploma_final'])) {
            $query->where('survey_type', $surveyType);
        }

        $teacherId = $request->input('teacher_id');
        if (! empty($teacherId)) {
            $query->where('teacher_id', $teacherId);
        }

        $studyGroupId = $request->input('study_group_id');
        if (! empty($studyGroupId)) {
            $query->where('study_group_id', $studyGroupId);
        }

        $period = $request->input('period', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($period === 'today') {
            $query->whereDate('survey_date', Carbon::today());
        } elseif ($period === 'week') {
            $query->whereBetween('survey_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $query->whereBetween('survey_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } elseif ($period === 'custom' || (! empty($dateFrom) && ! empty($dateTo))) {
            if (! empty($dateFrom)) {
                $query->whereDate('survey_date', '>=', $dateFrom);
            }
            if (! empty($dateTo)) {
                $query->whereDate('survey_date', '<=', $dateTo);
            }
        }

        $surveys = $query->get();

        $totalSurveys = $surveys->count();
        $avgOverall = round((float) ($surveys->avg('overall_score') ?? 0), 2);
        $avgTeacher = round((float) ($surveys->whereNotNull('teacher_rating')->avg('teacher_rating') ?? 0), 2);
        $avgCurriculum = round((float) ($surveys->whereNotNull('curriculum_rating')->avg('curriculum_rating') ?? 0), 2);
        $avgSupport = round((float) ($surveys->whereNotNull('support_rating')->avg('support_rating') ?? 0), 2);

        // مقاييس تفصيلية للمدرس
        $avgClarity = round((float) ($surveys->whereNotNull('teacher_clarity')->avg('teacher_clarity') ?? 0), 2);
        $avgCompetence = round((float) ($surveys->whereNotNull('teacher_competence')->avg('teacher_competence') ?? 0), 2);
        $avgInteraction = round((float) ($surveys->whereNotNull('teacher_interaction')->avg('teacher_interaction') ?? 0), 2);
        $avgPunctuality = round((float) ($surveys->whereNotNull('teacher_punctuality')->avg('teacher_punctuality') ?? 0), 2);

        // التحديات
        $challengesCount = [
            'speaking' => 0,
            'listening' => 0,
            'reading' => 0,
            'writing' => 0,
            'grammar' => 0,
        ];
        foreach ($surveys as $s) {
            if (is_array($s->learning_challenges)) {
                foreach ($s->learning_challenges as $ch) {
                    if (isset($challengesCount[$ch])) {
                        $challengesCount[$ch]++;
                    }
                }
            }
        }

        $selectedTeacher = $teacherId ? User::find($teacherId) : null;
        $selectedGroup = $studyGroupId ? StudyGroup::find($studyGroupId) : null;

        return view('admin.surveys.print', compact(
            'surveys',
            'totalSurveys',
            'avgOverall',
            'avgTeacher',
            'avgCurriculum',
            'avgSupport',
            'avgClarity',
            'avgCompetence',
            'avgInteraction',
            'avgPunctuality',
            'challengesCount',
            'surveyType',
            'selectedTeacher',
            'selectedGroup',
            'period',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * حذف استبيان
     */
    public function destroy(int $id): RedirectResponse
    {
        $survey = StudentSurvey::findOrFail($id);
        $survey->delete();

        return redirect()->back()->with('success', 'تم حذف التقييم بنجاح.');
    }

    /**
     * صفحة استبيانات الطالب في بوابة الطالب (Student Portal View)
     */
    public function studentIndex(): View
    {
        $user = Auth::user();

        // جلب مجموعة الطالب الحالية ومدرّسه
        $studyGroup = $user->studyGroups()
            ->with(['course', 'teacher', 'teachers'])
            ->wherePivot('status', 'active')
            ->latest('study_group_students.created_at')
            ->first();

        // التقييمات السابقة التي قدمها هذا الطالب
        $previousSurveys = StudentSurvey::where('student_id', $user->id)
            ->with(['teacher', 'studyGroup'])
            ->latest('survey_date')
            ->take(15)
            ->get();

        return view('student.surveys.index', compact('user', 'studyGroup', 'previousSurveys'));
    }

    /**
     * نموذج تعبئة الاستبيان (للطالب المسجل أو عبر رابط واتساب المباشر)
     */
    public function create(Request $request): View
    {
        $type = $request->input('type', 'daily');
        if (! in_array($type, ['daily', 'weekly', 'diploma_final'])) {
            $type = 'daily';
        }

        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $studyGroups = StudyGroup::with('teacher', 'course')->where('status', '!=', 'cancelled')->orderBy('name_ar')->get();
        $courses = Course::orderBy('name_ar')->get();

        $defaultTeacherId = $request->input('teacher_id');
        $defaultGroupId = $request->input('group_id');
        $defaultCourseId = $request->input('course_id');

        $currentUser = Auth::user();

        // إذا كان المستخدم طالب مسجل دخوله نحدد مجموعته ومدرسه تلقائياً
        if ($currentUser && empty($defaultGroupId)) {
            $currentGroup = $currentUser->studyGroups()
                ->with(['teacher'])
                ->wherePivot('status', 'active')
                ->latest('study_group_students.created_at')
                ->first();

            if ($currentGroup) {
                $defaultGroupId = $currentGroup->id;
                $defaultTeacherId = $defaultTeacherId ?: $currentGroup->teacher_id;
                $defaultCourseId = $defaultCourseId ?: $currentGroup->course_id;
            }
        }

        return view('public.surveys.form', compact(
            'type',
            'teachers',
            'studyGroups',
            'courses',
            'defaultTeacherId',
            'defaultGroupId',
            'defaultCourseId',
            'currentUser'
        ));
    }

    /**
     * حفظ وتخزين الاستبيان
     */
    public function store(Request $request): View|RedirectResponse
    {
        $type = $request->input('survey_type', 'daily');
        if (! in_array($type, ['daily', 'weekly', 'diploma_final'])) {
            $type = 'daily';
        }

        $request->validate([
            'survey_type' => 'required|in:daily,weekly,diploma_final',
            'teacher_id' => 'required|exists:users,id',
            'study_group_id' => 'nullable|exists:study_groups,id',
            'course_id' => 'nullable|exists:courses,id',
            'student_name' => 'nullable|string|max:190',
            'student_phone' => 'nullable|string|max:50',
            'is_anonymous' => 'nullable|boolean',
        ]);

        $currentUser = Auth::user();
        $isAnonymous = $request->boolean('is_anonymous');

        $survey = new StudentSurvey;
        $survey->survey_type = $type;
        $survey->survey_date = Carbon::today();
        $survey->teacher_id = (int) $request->input('teacher_id');
        $survey->study_group_id = $request->filled('study_group_id') ? (int) $request->input('study_group_id') : null;
        $survey->course_id = $request->filled('course_id') ? (int) $request->input('course_id') : null;

        // إذا لم يتم تحديد الكورس صراحة، نحاول استنتاجه من المجموعة
        if (! $survey->course_id && $survey->study_group_id) {
            $grp = StudyGroup::find($survey->study_group_id);
            $survey->course_id = $grp?->course_id;
        }

        if ($currentUser) {
            $survey->student_id = $currentUser->id;
            $survey->student_name = $request->filled('student_name') ? $request->input('student_name') : $currentUser->name;
            $survey->student_phone = $request->filled('student_phone') ? $request->input('student_phone') : $currentUser->phone_whatsapp;
        } else {
            $survey->student_name = $request->input('student_name', 'طالب زائر');
            $survey->student_phone = $request->input('student_phone');
        }

        $survey->is_anonymous = $isAnonymous;

        // 1. التقييم اليومي السريع
        if ($type === 'daily') {
            $survey->teacher_clarity = $request->filled('teacher_clarity') ? (int) $request->input('teacher_clarity') : null;
            $survey->daily_benefited = $request->input('daily_benefited'); // yes, partial, no
            $survey->teacher_interaction = $request->filled('teacher_interaction') ? (int) $request->input('teacher_interaction') : null;
            $survey->daily_level_appropriate = $request->input('daily_level_appropriate'); // yes, no
            $survey->notes_suggestions = $request->input('notes_suggestions');
        }

        // 2. التقييم الأسبوعي الشامل
        if ($type === 'weekly') {
            $survey->teacher_competence = $request->filled('teacher_competence') ? (int) $request->input('teacher_competence') : null;
            $survey->teacher_clarity = $request->filled('teacher_clarity') ? (int) $request->input('teacher_clarity') : null;
            $survey->teacher_interaction = $request->filled('teacher_interaction') ? (int) $request->input('teacher_interaction') : null;
            $survey->teacher_punctuality = $request->filled('teacher_punctuality') ? (int) $request->input('teacher_punctuality') : null;

            $survey->curriculum_suitability = $request->filled('curriculum_suitability') ? (int) $request->input('curriculum_suitability') : null;
            $survey->curriculum_alignment = $request->filled('curriculum_alignment') ? (int) $request->input('curriculum_alignment') : null;

            $survey->learning_challenges = (array) $request->input('learning_challenges', []);

            $survey->admin_communication = $request->filled('admin_communication') ? (int) $request->input('admin_communication') : null;
            $survey->remote_learning_ease = $request->filled('remote_learning_ease') ? (int) $request->input('remote_learning_ease') : null;
            $survey->remote_learning_difficulties = $request->input('remote_learning_difficulties');

            $survey->notes_suggestions = $request->input('notes_suggestions');
        }

        // 3. التقييم النهائي للدبلوم
        if ($type === 'diploma_final') {
            $survey->teacher_competence = $request->filled('teacher_competence') ? (int) $request->input('teacher_competence') : null;
            $survey->teacher_clarity = $request->filled('teacher_clarity') ? (int) $request->input('teacher_clarity') : null;
            $survey->teacher_interaction = $request->filled('teacher_interaction') ? (int) $request->input('teacher_interaction') : null;
            $survey->teacher_punctuality = $request->filled('teacher_punctuality') ? (int) $request->input('teacher_punctuality') : null;

            $survey->curriculum_suitability = $request->filled('curriculum_suitability') ? (int) $request->input('curriculum_suitability') : null;
            $survey->curriculum_alignment = $request->filled('curriculum_alignment') ? (int) $request->input('curriculum_alignment') : null;

            $survey->learning_challenges = (array) $request->input('learning_challenges', []);

            $survey->admin_communication = $request->filled('admin_communication') ? (int) $request->input('admin_communication') : null;
            $survey->management_organization = $request->filled('management_organization') ? (int) $request->input('management_organization') : null;
            $survey->remote_learning_difficulties = $request->input('remote_learning_difficulties');

            $survey->best_feature = $request->input('best_feature');
            $survey->recommend_institute = $request->input('recommend_institute');
            $survey->recommend_reason = $request->input('recommend_reason');
            $survey->notes_suggestions = $request->input('notes_suggestions');
        }

        $survey->raw_answers = $request->except(['_token']);
        $survey->save();

        return view('public.surveys.success', compact('survey'));
    }
}
