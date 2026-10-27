<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamQuestion;
use App\Models\GroupActivity;
use App\Models\Level;
use App\Models\LevelResource;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * عرض قائمة الاختبارات والكويزات التابعة للمستوى أو الدورة
     */
    public function index(Request $request, $levelId = null)
    {
        $level = null;
        if ($levelId) {
            $level = Level::with('course')->findOrFail($levelId);
            $query = Exam::where('level_id', $levelId);
        } else {
            $query = Exam::query();
            if ($request->filled('level_id')) {
                $query->where('level_id', $request->level_id);
            }
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('quiz_period') && $request->quiz_period !== 'all') {
            $query->where('quiz_period', $request->quiz_period);
        }

        if ($request->filled('term') && $request->term !== 'all') {
            $query->where('term', $request->term);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $exams = $query->with(['course', 'level.course', 'creator', 'questions', 'attempts'])
            ->latest()
            ->paginate(15);

        $courses = Course::with('levels')->where('is_active', true)->orderBy('course_order')->get();
        $levels = Level::with('course')->where('is_active', true)->orderBy('level_order')->get();
        $allTerms = Exam::whereNotNull('term')->distinct()->pluck('term')->all();

        return view('admin.exams.index', compact('exams', 'level', 'courses', 'levels', 'allTerms'));
    }

    /**
     * صفحة إنشاء كويز أو اختبار جديد (سواء لمستوى أو لدورة تعليمية مباشرة)
     */
    public function create(Request $request, $levelId = null)
    {
        $level = $levelId ? Level::with('course')->find($levelId) : null;
        $courseId = $request->get('course_id', $level?->course_id);
        $selectedCourse = $courseId ? Course::find($courseId) : null;

        $courses = Course::with('levels')->where('is_active', true)->orderBy('course_order')->get();
        $levels = Level::where('is_active', true)->orderBy('level_order')->get();
        $allTerms = StudyGroup::whereNotNull('term')->distinct()->pluck('term')->all();

        return view('admin.exams.create', compact('level', 'courses', 'levels', 'selectedCourse', 'allTerms'));
    }

    /**
     * حفظ إعدادات الاختبار أو الكويز الجديد وربطه بالدورة أو المستوى
     */
    public function store(Request $request, $levelId = null)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('manage_courses') && ! $user->hasPermission('upload_level_resources') && $user->role !== 'teacher') {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إنشاء كويزات واختبارات.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string',
            'course_id' => 'nullable|exists:courses,id',
            'level_id' => 'nullable|exists:levels,id',
            'description' => 'nullable|string',
            'quiz_period' => 'required|string',
            'term' => 'nullable|string|max:100',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'passing_score' => 'required|numeric|min:1|max:100',
            'random_questions_count' => 'nullable|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_options' => 'nullable|boolean',
            'show_result_immediately' => 'nullable|boolean',
        ]);

        $targetLevelId = $levelId ?: ($validated['level_id'] ?? null);
        $targetCourseId = $validated['course_id'] ?? null;

        if ($targetLevelId && ! $targetCourseId) {
            $lvl = Level::find($targetLevelId);
            $targetCourseId = $lvl?->course_id;
        }

        $exam = Exam::create([
            'level_id' => $targetLevelId,
            'course_id' => $targetCourseId,
            'created_by' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'] ?? ($validated['quiz_period'] === 'placement' ? 'placement_test' : 'quiz'),
            'quiz_period' => $validated['quiz_period'],
            'term' => $validated['term'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'total_points' => 0.00,
            'passing_score' => $validated['passing_score'],
            'random_questions_count' => $validated['random_questions_count'] ?? null,
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_options' => $request->has('shuffle_options'),
            'show_result_immediately' => $request->has('show_result_immediately'),
            'is_active' => true,
        ]);

        // ربطه تلقائياً كمورد كويز في بنك موارد المستوى أو الدورة
        LevelResource::create([
            'course_id' => $targetCourseId,
            'level_id' => $targetLevelId,
            'title' => $exam->title,
            'type' => 'quiz',
            'description' => $exam->description ?: ('كويز وتقييم: '.$exam->quiz_period_label),
            'duration_minutes' => $exam->duration_minutes,
            'exam_id' => $exam->id,
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        return redirect()->route('admin.exams.edit', $exam->id)
            ->with('success', "تم إنشاء «{$exam->title}» بنجاح! يمكنك الآن إضافة الأسئلة يدوياً أو استيرادها دفعة واحدة.");
    }

    /**
     * صفحة مصمم الأسئلة وبنك أسئلة الاختبار
     */
    public function edit($examId)
    {
        $exam = Exam::with(['course', 'level.course', 'creator', 'questions', 'attempts.user'])->findOrFail($examId);
        $courses = Course::with('levels')->where('is_active', true)->orderBy('course_order')->get();
        $levels = Level::where('is_active', true)->orderBy('level_order')->get();

        $studyGroups = StudyGroup::where(function ($q) use ($exam) {
            if ($exam->level_id) {
                $q->where('level_id', $exam->level_id);
            }
            if ($exam->course_id) {
                $q->orWhere('course_id', $exam->course_id);
            }
        })->where('status', 'active')->get();

        if ($studyGroups->isEmpty()) {
            $studyGroups = StudyGroup::where('status', 'active')->latest()->get();
        }

        $allTerms = StudyGroup::whereNotNull('term')->distinct()->pluck('term')->all();

        return view('admin.exams.edit', compact('exam', 'courses', 'levels', 'studyGroups', 'allTerms'));
    }

    /**
     * تحديث بيانات وإعدادات واسم وتصنيف الاختبار والدورة والمستوى
     */
    public function update(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string',
            'course_id' => 'nullable|exists:courses,id',
            'level_id' => 'nullable|exists:levels,id',
            'description' => 'nullable|string',
            'quiz_period' => 'required|string',
            'term' => 'nullable|string|max:100',
            'duration_minutes' => 'required|integer|min:1|max:300',
            'passing_score' => 'required|numeric|min:1|max:100',
            'random_questions_count' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $courseId = $validated['course_id'] ?? null;
        $levelId = $validated['level_id'] ?? null;
        if ($levelId && ! $courseId) {
            $lvl = Level::find($levelId);
            $courseId = $lvl?->course_id;
        }

        $exam->update([
            'title' => $validated['title'],
            'type' => $validated['type'] ?? $exam->type,
            'course_id' => $courseId,
            'level_id' => $levelId,
            'description' => $validated['description'] ?? null,
            'quiz_period' => $validated['quiz_period'],
            'term' => $validated['term'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'passing_score' => $validated['passing_score'],
            'random_questions_count' => $validated['random_questions_count'] ?: null,
            'shuffle_questions' => $request->has('shuffle_questions'),
            'shuffle_options' => $request->has('shuffle_options'),
            'show_result_immediately' => $request->has('show_result_immediately'),
            'is_active' => $request->has('is_active'),
        ]);

        // تحديث عنوان وبيانات المورد في بنك المناهج والموارد
        LevelResource::where('exam_id', $exam->id)->update([
            'title' => $exam->title,
            'course_id' => $courseId,
            'level_id' => $levelId,
            'duration_minutes' => $exam->duration_minutes,
            'description' => $exam->description,
        ]);

        return back()->with('success', "تم تحديث اسم وإعدادات الدورة والمستوى للكويز/الاختبار «{$exam->title}» بنجاح.");
    }

    /**
     * حذف اختبار من النظام
     */
    public function destroy($examId)
    {
        $exam = Exam::findOrFail($examId);
        LevelResource::where('exam_id', $examId)->delete();
        $exam->delete();

        return redirect()->route('admin.levels.resources', $exam->level_id)
            ->with('success', 'تم حذف الاختبار من بنك المستوى بنجاح.');
    }

    /**
     * إضافة سؤال يدوي فردي باختيارات Radio Points
     */
    public function addQuestion(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'context_passage' => 'nullable|string',
            'type' => 'required|in:mcq,listening,reading,speaking_ai',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'points' => 'required|numeric|min:0.5|max:100',
            'expected_speech_text' => 'nullable|string',
        ]);

        $orderIndex = ($exam->questions()->max('order_index') ?? 0) + 1;

        ExamQuestion::create([
            'exam_id' => $exam->id,
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
            'context_passage' => $validated['context_passage'] ?? null,
            'options' => array_values(array_filter($validated['options'])),
            'correct_answer' => $validated['correct_answer'],
            'expected_speech_text' => $validated['expected_speech_text'] ?? null,
            'points' => $validated['points'],
            'order_index' => $orderIndex,
        ]);

        // تحديث إجمالي درجات الاختبار
        $this->syncExamTotalPoints($exam);

        return back()->with('success', 'تمت إضافة السؤال بنجاح إلى بنك أسئلة الاختبار.');
    }

    /**
     * استيراد أسئلة دفعة واحدة من ملف Word/Excel أو نص منسوخ
     */
    public function importQuestions(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        $rawText = $request->input('raw_text');

        // إذا تم رفع ملف (Word / Text / CSV)
        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');
            $ext = strtolower($file->getClientOriginalExtension());

            if (in_array($ext, ['txt', 'csv'])) {
                $rawText = file_get_contents($file->getRealPath());
            } elseif ($ext === 'docx') {
                // استخراج النصوص من ملف Word docx
                $rawText = $this->extractTextFromDocx($file->getRealPath());
            }
        }

        if (empty(trim($rawText))) {
            return back()->with('error', 'يرجى لصق نص الأسئلة أو اختيار ملف يحتوي على الأسئلة.');
        }

        $importedCount = $this->parseAndSaveQuestions($rawText, $exam);

        if ($importedCount > 0) {
            $this->syncExamTotalPoints($exam);

            return back()->with('success', "تم استيراد وإضافة ({$importedCount}) سؤال بنجاح إلى بنك الاختبار!");
        }

        return back()->with('error', 'لم يتم التعرف على أي أسئلة. يرجى التأكد من التنسيق (السؤال متبوعاً بالخيارات A, B, C, D ثم Answer: B).');
    }

    /**
     * تحديث سؤال محدد وتعديل نصه وخياراته والإجابة الصحيحة ودرجته
     */
    public function updateQuestion(Request $request, $questionId)
    {
        $question = ExamQuestion::findOrFail($questionId);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'nullable|in:mcq,listening,reading,speaking_ai',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string',
            'points' => 'required|numeric|min:0.5|max:100',
            'context_passage' => 'nullable|string',
        ]);

        $cleanedOptions = array_values(array_filter(array_map('trim', $validated['options'])));

        $question->update([
            'question_text' => $validated['question_text'],
            'type' => $validated['type'] ?? $question->type,
            'options' => $cleanedOptions,
            'correct_answer' => trim($validated['correct_answer']),
            'points' => (float) $validated['points'],
            'context_passage' => $validated['context_passage'] ?? null,
        ]);

        $this->syncExamTotalPoints($question->exam);

        return back()->with('success', 'تم تعديل السؤال وتحديث الخيارات والإجابة الصحيحة والدرجة بنجاح.');
    }

    /**
     * حذف سؤال من الاختبار
     */
    public function deleteQuestion($questionId)
    {
        $question = ExamQuestion::findOrFail($questionId);
        $exam = $question->exam;
        $question->delete();

        $this->syncExamTotalPoints($exam);

        return back()->with('success', 'تم حذف السؤال من بنك الاختبار.');
    }

    /**
     * مشاركة الاختبار وإرساله لمجموعة دراسية محددة كنشاط
     */
    public function shareToGroup(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        $group = StudyGroup::findOrFail($request->study_group_id);

        if ($request->has('show_result_immediately')) {
            $exam->update(['show_result_immediately' => $request->boolean('show_result_immediately')]);
        }

        $dueDate = $request->due_date ?: null;
        $notes = $request->custom_instructions ?: "اختبار مقرر: {$exam->title} ({$exam->quiz_period_label}) - المدة {$exam->duration_minutes} دقيقة.";

        // التأكد من وجود مورد في بنك المستوى مربوط بهذا الاختبار
        $resource = LevelResource::where('exam_id', $exam->id)->first();
        if (! $resource) {
            $resource = LevelResource::create([
                'level_id' => $exam->level_id,
                'created_by' => Auth::id(),
                'type' => 'quiz',
                'title' => $exam->title,
                'description' => $exam->description,
                'exam_id' => $exam->id,
                'is_active' => true,
            ]);
        }

        // حفظ كنشاط تفاعلي للمجموعة
        $activity = GroupActivity::create([
            'study_group_id' => $group->id,
            'level_resource_id' => $resource->id,
            'user_id' => Auth::id(),
            'type' => 'quiz',
            'title' => "🎯 {$exam->title} ({$exam->quiz_period_label})",
            'instructions' => $notes,
            'due_date' => $dueDate,
        ]);

        return back()->with('success', "تمت مشاركة الاختبار وإرساله بنجاح إلى طلاب مجموعة: [{$group->name}]!");
    }

    /**
     * استعراض محاولات ونتائج الطلاب في اختبار محدد للإدارة والمعلم
     */
    public function attempts(Request $request, $examId)
    {
        $exam = Exam::with(['level.course', 'questions'])->findOrFail($examId);

        $query = ExamAttempt::where('exam_id', $examId)
            ->with(['user.financialAccount', 'studyGroup', 'reviewer', 'answers']);

        if ($request->filled('study_group_id')) {
            $query->where('study_group_id', $request->study_group_id);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'pending_publish') {
                $query->where('is_published', false);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }

        $attempts = $query->latest('submitted_at')->paginate(20);

        // إحصائيات المحاولات
        $allAttempts = ExamAttempt::where('exam_id', $examId)->get();
        $totalAttempts = $allAttempts->count();
        $publishedCount = $allAttempts->where('is_published', true)->count();
        $pendingPublishCount = $totalAttempts - $publishedCount;
        $avgScore = $totalAttempts > 0 ? round($allAttempts->avg('total_score'), 1) : 0;
        $passCount = $allAttempts->where('total_score', '>=', $exam->passing_score)->count();
        $passRate = $totalAttempts > 0 ? round(($passCount / $totalAttempts) * 100, 1) : 0;

        $groups = StudyGroup::where('level_id', $exam->level_id)->get();

        return view('admin.exams.attempts', compact(
            'exam',
            'attempts',
            'totalAttempts',
            'publishedCount',
            'pendingPublishCount',
            'avgScore',
            'passRate',
            'groups'
        ));
    }

    /**
     * مراجعة تفصيلية لمحاولة طالب محدد (عرض الأسئلة والإجابة التي اختارها والدرجة)
     */
    public function reviewAttempt($attemptId)
    {
        $attempt = ExamAttempt::with(['exam.questions', 'exam.course', 'exam.level.course', 'user.studentProfile', 'studyGroup', 'reviewer', 'suggestedLevel.course', 'answers.question'])
            ->findOrFail($attemptId);

        $exam = $attempt->exam;

        return view('admin.exams.review_attempt', compact('attempt', 'exam'));
    }

    /**
     * اعتماد وإصدار النتيجة للطالب (Release / Publish Result)
     */
    public function publishResult(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::findOrFail($attemptId);

        $totalScore = $request->has('adjusted_score') ? (float) $request->adjusted_score : $attempt->total_score;
        $teacherNotes = $request->input('teacher_notes', $attempt->teacher_notes);

        $attempt->update([
            'total_score' => $totalScore,
            'teacher_notes' => $teacherNotes,
            'is_published' => true,
            'status' => 'reviewed',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "تم اعتماد وإصدار النتيجة للطالب [{$attempt->user->name}] بنجاح! يمكن للطالب الآن رؤية النتيجة.");
    }

    /**
     * اعتماد وإصدار كافة نتائج الاختبار لجميع الطلاب دفعة واحدة
     */
    public function publishAllResults(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        $updated = ExamAttempt::where('exam_id', $examId)
            ->where('is_published', false)
            ->update([
                'is_published' => true,
                'status' => 'reviewed',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

        return back()->with('success', "تم اعتماد وإصدار كافة النتائج لـ ({$updated}) طالب بنجاح!");
    }

    /**
     * استعراض كافة محاولات ونتائج الاختبارات والكويزات وتحديد المستوى لجميع الطلاب
     * متاح لمدير النظام والمشرف الأكاديمي ولأصحاب الصلاحيات للمراجعة والشفافية
     */
    public function allAttempts(Request $request)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor() && ! $user->hasPermission('exams.view') && ! $user->hasPermission('exams.grade')) {
            abort(403, 'غير مصرح لك باستعراض سجل نتائج الاختبارات.');
        }

        $query = ExamAttempt::with(['exam.course', 'exam.level.course', 'user.studentProfile', 'studyGroup', 'reviewer', 'answers']);

        // فلترة بنوع الاختبار / الفترة (تحديد مستوى، كويز، نصفي، نهائي)
        if ($request->filled('quiz_period') && $request->quiz_period !== 'all') {
            $period = $request->quiz_period;
            $query->whereHas('exam', function ($q) use ($period) {
                if ($period === 'placement') {
                    $q->where('type', 'placement_test')->orWhere('quiz_period', 'placement');
                } else {
                    $q->where('quiz_period', $period);
                }
            });
        }

        // فلترة بحالة النتيجة (معلنة / بانتظار الاعتماد)
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'pending_publish') {
                $query->where('is_published', false);
            } else {
                $query->where('status', $request->status);
            }
        }

        // فلترة بالدورة
        if ($request->filled('course_id')) {
            $query->whereHas('exam', fn ($q) => $q->where('course_id', $request->course_id));
        }

        // فلترة بالمستوى
        if ($request->filled('level_id')) {
            $query->whereHas('exam', fn ($q) => $q->where('level_id', $request->level_id));
        }

        // فلترة بالمجموعة الدراسية
        if ($request->filled('study_group_id')) {
            $query->where('study_group_id', $request->study_group_id);
        }

        // البحث باسم الطالب أو الكود أو عنوان الاختبار
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', function ($uq) use ($s) {
                    $uq->where('name', 'like', "%{$s}%")
                        ->orWhere('student_code', 'like', "%{$s}%")
                        ->orWhere('user_code', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%");
                })->orWhereHas('exam', function ($eq) use ($s) {
                    $eq->where('title', 'like', "%{$s}%");
                });
            });
        }

        $attempts = $query->latest('submitted_at')->paginate(20)->appends($request->query());

        // إحصائيات عامة
        $totalAttempts = ExamAttempt::count();
        $publishedCount = ExamAttempt::where('is_published', true)->count();
        $pendingPublishCount = ExamAttempt::where('is_published', false)->count();
        $placementCount = ExamAttempt::whereHas('exam', fn ($q) => $q->where('type', 'placement_test')->orWhere('quiz_period', 'placement'))->count();
        $avgScore = $totalAttempts > 0 ? round(ExamAttempt::avg('total_score'), 1) : 0;

        $courses = Course::where('is_active', true)->orderBy('course_order')->get();
        $levels = Level::with('course')->where('is_active', true)->orderBy('level_order')->get();
        $groups = StudyGroup::orderBy('name')->get();

        return view('admin.exams.all_attempts', compact(
            'attempts',
            'totalAttempts',
            'publishedCount',
            'pendingPublishCount',
            'placementCount',
            'avgScore',
            'courses',
            'levels',
            'groups'
        ));
    }

    /**
     * سجل شامل لكافة اختبارات وكويزات طالب محدد (نتائج ومحاولات)
     */
    public function studentAttempts(Request $request, $studentId)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor() && ! $user->hasPermission('exams.view') && ! $user->hasPermission('students.view')) {
            abort(403, 'غير مصرح لك باستعراض سجل اختبارات الطالب.');
        }

        $student = User::with(['studentProfile', 'financialAccount'])->findOrFail($studentId);

        $query = ExamAttempt::where('user_id', $studentId)
            ->with(['exam.course', 'exam.level.course', 'studyGroup', 'reviewer', 'answers']);

        if ($request->filled('quiz_period') && $request->quiz_period !== 'all') {
            $period = $request->quiz_period;
            $query->whereHas('exam', function ($q) use ($period) {
                if ($period === 'placement') {
                    $q->where('type', 'placement_test')->orWhere('quiz_period', 'placement');
                } else {
                    $q->where('quiz_period', $period);
                }
            });
        }

        $attempts = $query->latest('submitted_at')->get();

        $totalAttempts = $attempts->count();
        $passedCount = $attempts->filter(function ($a) {
            $passScore = $a->exam?->passing_score ?? 50;

            return $a->total_score >= $passScore;
        })->count();
        $passRate = $totalAttempts > 0 ? round(($passedCount / $totalAttempts) * 100, 1) : 0;
        $avgScore = $totalAttempts > 0 ? round($attempts->avg('total_score'), 1) : 0;

        return view('admin.students.exam_attempts', compact(
            'student',
            'attempts',
            'totalAttempts',
            'passedCount',
            'passRate',
            'avgScore'
        ));
    }

    /**
     * تقرير كشف أخطاء ومراجعة نتائج الطالب في الاختبار (مخصص للطباعة والشفافية الأكاديمية)
     */
    public function errorReport($attemptId)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor() && ! $user->hasPermission('exams.view') && ! $user->hasPermission('exams.grade') && $user->role !== 'teacher') {
            abort(403, 'غير مصرح لك باستعراض تقرير أخطاء الاختبار.');
        }

        $attempt = ExamAttempt::with([
            'exam.questions',
            'exam.course',
            'exam.level.course',
            'user.studentProfile',
            'studyGroup',
            'reviewer',
            'suggestedLevel.course',
            'answers.question',
        ])->findOrFail($attemptId);

        $exam = $attempt->exam;

        // استخراج الأسئلة بالترتيب الذي ظهر للطالب
        $questionIds = $attempt->questions_order ?: $exam->questions->pluck('id')->all();
        $questions = ExamQuestion::whereIn('id', $questionIds)->get()->sortBy(function ($q) use ($questionIds) {
            return array_search($q->id, $questionIds);
        });

        $answersMap = $attempt->answers->keyBy('question_id');

        // إحصائيات تفصيلية للأخطاء والصواب
        $totalQuestions = $questions->count();
        $correctCount = 0;
        $incorrectCount = 0;
        $unansweredCount = 0;
        $pointsEarned = 0;
        $totalPoints = $exam->total_points > 0 ? $exam->total_points : $questions->sum('points');

        $questionsAnalysis = [];

        foreach ($questions as $q) {
            $ans = $answersMap->get($q->id);
            $studentAnswer = $ans?->student_answer;
            $isCorrect = (bool) ($ans?->is_correct);
            $points = $ans?->points_awarded ?? 0;
            $isUnanswered = empty($studentAnswer) && empty($ans?->recorded_audio_path);

            if ($q->type === 'speaking_ai') {
                $hasVoice = ! empty($ans?->recorded_audio_path);
                $accuracy = $ans?->speech_accuracy ?? 0;
                $isCorrect = $accuracy >= 70;
            }

            if ($isCorrect) {
                $correctCount++;
            } elseif ($isUnanswered) {
                $unansweredCount++;
                $incorrectCount++;
            } else {
                $incorrectCount++;
            }

            $pointsEarned += $points;

            $questionsAnalysis[] = [
                'question' => $q,
                'answer' => $ans,
                'student_answer' => $studentAnswer,
                'is_correct' => $isCorrect,
                'is_unanswered' => $isUnanswered,
                'points_awarded' => $points,
            ];
        }

        $scorePercentage = $totalPoints > 0 ? round(($attempt->total_score / $totalPoints) * 100, 1) : 0;
        $isPassed = $attempt->total_score >= ($exam->passing_score ?? 50);

        return view('admin.exams.error_report', compact(
            'attempt',
            'exam',
            'questionsAnalysis',
            'totalQuestions',
            'correctCount',
            'incorrectCount',
            'unansweredCount',
            'pointsEarned',
            'totalPoints',
            'scorePercentage',
            'isPassed'
        ));
    }

    /**
     * واجهة الطالب / ولي الأمر لاستعراض وطباعة تقرير تدقيق أخطاء الاختبار
     */
    public function studentErrorReport($attemptId)
    {
        $user = Auth::user();
        $attempt = ExamAttempt::with([
            'exam.questions',
            'exam.course',
            'exam.level.course',
            'user.studentProfile',
            'studyGroup',
            'reviewer',
            'suggestedLevel.course',
            'answers.question',
        ])->findOrFail($attemptId);

        // التحقق من هوية الطالب أو ولي أمره أو كادر الإشراف
        $isOwnerStudent = ($attempt->user_id === $user->id);
        $isParentOfStudent = ($user->isParent() && $user->children()->where('users.id', $attempt->user_id)->exists());
        $isStaff = ($user->isAdmin() || $user->isSupervisor() || $user->role === 'teacher' || $user->hasPermission('exams.view'));

        if (! $isOwnerStudent && ! $isParentOfStudent && ! $isStaff) {
            abort(403, 'غير مصرح لك باستعراض تقرير هذا الاختبار.');
        }

        if (! $isStaff && ! $attempt->isResultVisibleToStudent()) {
            return redirect()->route('student.exam.result', $attempt->id)->with('warning', 'النتيجة وتقرير الأخطاء محجوبان حتى اعتماد النتيجة رسمياً من الإدارة.');
        }

        return $this->errorReport($attemptId);
    }

    // =========================================================================
    // واجهة الطالب: أداء الاختبار في نفس الوقت مع التوقيت والخيارات العشوائية
    // =========================================================================

    /**
     * شاشة أداء الاختبار للطالب (Real-Time Test Interface)
     */
    public function take(Request $request, $examId, $groupId = null)
    {
        $user = Auth::user();
        $exam = Exam::with(['questions', 'level.course'])->findOrFail($examId);

        // التحقق إن كانت هناك محاولة سابقة مكتملة للطالب
        $existingAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->whereIn('status', ['submitted', 'graded', 'reviewed'])
            ->latest()
            ->first();

        if ($existingAttempt) {
            return redirect()->route('student.exam.result', $existingAttempt->id)
                ->with('info', 'لقد قمت بتسليم إجابات هذا الاختبار مسبقاً.');
        }

        // جلب أسئلة الاختبار
        $allQuestions = $exam->questions;

        if ($allQuestions->isEmpty()) {
            return back()->with('error', 'هذا الاختبار لا يحتوي على أي أسئلة مضافة حتى الآن.');
        }

        // ترتيب الأسئلة واختيار العشوائي منها
        $questionsToPresent = $allQuestions;

        // التحقق من وجود محاولة قيد التنفيذ لاسترجاع نفس الأسئلة للطالب
        $inProgressAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->first();

        if ($inProgressAttempt && ! empty($inProgressAttempt->questions_order)) {
            $orderedIds = $inProgressAttempt->questions_order;
            $questionsToPresent = $allQuestions->whereIn('id', $orderedIds)->sortBy(function ($q) use ($orderedIds) {
                return array_search($q->id, $orderedIds);
            })->values();
        } else {
            // توليد ترتيب عشوائي واختيار العدد المحدد
            if ($exam->shuffle_questions) {
                $questionsToPresent = $allQuestions->shuffle();
            }

            if ($exam->random_questions_count && $exam->random_questions_count > 0) {
                $questionsToPresent = $questionsToPresent->take($exam->random_questions_count);
            }

            // إنشاء المحاولة
            $inProgressAttempt = ExamAttempt::create([
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'study_group_id' => $groupId ?: $user->studyGroups->first()?->id,
                'total_score' => 0.00,
                'status' => 'in_progress',
                'started_at' => now(),
                'questions_order' => $questionsToPresent->pluck('id')->all(),
            ]);
        }

        // خلط الخيارات لكل سؤال إن كان الخيار مفعل
        if ($exam->shuffle_options) {
            foreach ($questionsToPresent as $q) {
                if (is_array($q->options)) {
                    $opts = $q->options;
                    shuffle($opts);
                    $q->shuffled_options = $opts;
                }
            }
        }

        return view('student.exams.take', [
            'exam' => $exam,
            'questions' => $questionsToPresent,
            'attempt' => $inProgressAttempt,
            'groupId' => $groupId,
        ]);
    }

    /**
     * تسليم الطالب للاختبار والتصحيح التلقائي
     */
    public function submit(Request $request, $examId)
    {
        $user = Auth::user();
        $exam = Exam::with('questions')->findOrFail($examId);

        $attempt = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        if (! $attempt) {
            $attempt = ExamAttempt::create([
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'study_group_id' => $request->study_group_id ?: $user->studyGroups->first()?->id,
                'total_score' => 0,
                'status' => 'submitted',
                'started_at' => now(),
                'submitted_at' => now(),
            ]);
        }

        // الأسئلة المحددة لهذه المحاولة
        $questionIds = $attempt->questions_order ?: $exam->questions->pluck('id')->all();
        $questions = ExamQuestion::whereIn('id', $questionIds)->get();

        $totalScore = 0;
        $maxPossibleScore = $questions->sum('points') ?: 100;

        foreach ($questions as $question) {
            $ansKey = 'q_'.$question->id;
            $studentAnswer = $request->input($ansKey);

            $isCorrect = false;
            $pointsAwarded = 0;

            if ($studentAnswer !== null) {
                // مقارنة الإجابة غير حساسة للمسافات
                $isCorrect = (trim(mb_strtolower($studentAnswer)) === trim(mb_strtolower($question->correct_answer)));
                if ($isCorrect) {
                    $pointsAwarded = (float) $question->points;
                    $totalScore += $pointsAwarded;
                }
            }

            ExamAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'student_answer' => $studentAnswer,
                    'is_correct' => $isCorrect,
                    'points_awarded' => $pointsAwarded,
                ]
            );
        }

        // حساب نسبة النتيجة المئوية إن كان المجموع مختلف عن 100
        $finalScore = $totalScore;

        $shouldPublishNow = (bool) $exam->show_result_immediately;

        $attempt->update([
            'total_score' => $finalScore,
            'status' => $shouldPublishNow ? 'graded' : 'submitted',
            'is_published' => $shouldPublishNow,
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.exam.result', $attempt->id)
            ->with('success', 'تم تسليم إجاباتك بنجاح!');
    }

    /**
     * عرض نتيجة المحاولة للطالب
     */
    public function studentResult($attemptId)
    {
        $user = Auth::user();
        $attempt = ExamAttempt::with(['exam.questions', 'answers.question', 'reviewer'])
            ->findOrFail($attemptId);

        // التأكد من أن الطالب صاحب المحاولة أو ولي أمره أو مشرف/أدمن
        $isOwnerStudent = ($attempt->user_id === $user->id);
        $isParentOfStudent = ($user->isParent() && $user->children()->where('users.id', $attempt->user_id)->exists());
        $isStaff = ($user->isAdmin() || $user->isSupervisor() || $user->role === 'teacher' || $user->hasPermission('exams.view'));

        if (! $isOwnerStudent && ! $isParentOfStudent && ! $isStaff) {
            abort(403, 'غير مصرح باستعراض نتيجة هذا الاختبار.');
        }

        $exam = $attempt->exam;
        $isResultVisible = $attempt->isResultVisibleToStudent() || $isStaff;

        return view('student.exams.result', compact('attempt', 'exam', 'isResultVisible'));
    }

    // =========================================================================
    // دوال مساعدة خاصة (Helpers & Word/Excel Parsers)
    // =========================================================================

    /**
     * مزامنة إجمالي درجات الاختبار من مجموع درجات أسئلته
     */
    private function syncExamTotalPoints(Exam $exam): void
    {
        $total = $exam->questions()->sum('points');
        $exam->update(['total_points' => $total]);
    }

    /**
     * محلل نصوص الأسئلة (Word, Notepad, Text Parser)
     * يقرأ الأسئلة والخيارات والإجابة الصحيحة بذكاء
     */
    private function parseAndSaveQuestions(string $rawText, Exam $exam): int
    {
        $lines = preg_split('/\r\n|\r|\n/', $rawText);
        $imported = 0;

        $currentQuestion = null;
        $currentPassage = null;
        $currentOptions = [];
        $currentAnswer = null;
        $currentPoints = 1.0;

        $orderIndex = ($exam->questions()->max('order_index') ?? 0) + 1;

        $saveCurrent = function () use (&$currentQuestion, &$currentPassage, &$currentOptions, &$currentAnswer, &$currentPoints, &$orderIndex, &$imported, $exam) {
            if ($currentQuestion && ! empty($currentOptions)) {
                // استخراج الإجابة الصحيحة
                $cleanAnswer = $currentAnswer ?: $currentOptions[0];
                // إذا كانت الإجابة المحددة هي حرف الخيار (مثلاً: A أو B أو أ)
                if (strlen(trim($cleanAnswer)) === 1) {
                    $letter = strtoupper(trim($cleanAnswer));
                    $idxMap = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'أ' => 0, 'ب' => 1, 'ج' => 2, 'د' => 3];
                    if (isset($idxMap[$letter]) && isset($currentOptions[$idxMap[$letter]])) {
                        $cleanAnswer = $currentOptions[$idxMap[$letter]];
                    }
                }

                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'type' => 'mcq',
                    'question_text' => $currentQuestion,
                    'context_passage' => $currentPassage,
                    'options' => array_values($currentOptions),
                    'correct_answer' => $cleanAnswer,
                    'points' => $currentPoints,
                    'order_index' => $orderIndex++,
                ]);

                $imported++;
            }

            // تصفير للمؤشر القادم
            $currentQuestion = null;
            $currentPassage = null;
            $currentOptions = [];
            $currentAnswer = null;
            $currentPoints = 1.0;
        };

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }

            // 1. فحص إجابة السؤال (Answer / Correct / الإجابة / الحل)
            if (preg_match('/^(?:Answer|Ans|Correct|الإجابة|الحل)[\s\:\-]+(.+)$/ui', $trimmed, $m)) {
                $currentAnswer = trim($m[1]);

                continue;
            }

            // 2. فحص درجة السؤال (Points / Mark / الدرجة)
            if (preg_match('/^(?:Points|Point|Marks|Degree|الدرجة)[\s\:\-]+([0-9\.]+)$/ui', $trimmed, $m)) {
                $currentPoints = (float) $m[1];

                continue;
            }

            // 3. فحص بداية سؤال جديد: يبدأ برقم متبوع برمز (1. أو 1- أو 1:) أو كلمة سؤال / Question / Q1
            if (preg_match('/^(?:(?:سؤال|question|q)\s*\d*|\d+)\s*[\.\:\-\)]\s*(.+)$/ui', $trimmed, $m)) {
                $saveCurrent();
                $currentQuestion = trim($m[1]);

                continue;
            }

            // 4. فحص خيارات الإجابة: A) أو A. أو أ) أو أ- أو (A) أو (أ) أو 1)
            if (preg_match('/^(?:\(?([A-Ea-eأ-د])\)?|[1-5]\))\s*[\.\)\-\:\s]\s*(.+)$/u', $trimmed, $m)) {
                $currentOptions[] = trim($m[2]);

                continue;
            }

            // إذا لم يكن خياراً ولا إجابة وكان هناك سؤال نشط، قد يكون تكملة لنص السؤال
            if ($currentQuestion && empty($currentOptions)) {
                $currentQuestion .= ' '.$trimmed;
            } elseif (! $currentQuestion) {
                // سطر جديد نعتبره سؤالاً
                $saveCurrent();
                $currentQuestion = $trimmed;
            }
        }

        $saveCurrent();

        return $imported;
    }

    /**
     * استخراج النص النقي من ملف Word (.docx)
     */
    private function extractTextFromDocx(string $filePath): string
    {
        $text = '';
        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive;
            if ($zip->open($filePath) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();
                if ($xml) {
                    $text = strip_tags(str_replace('</w:p>', "\n", $xml));
                }
            }
        }

        return $text;
    }
}
