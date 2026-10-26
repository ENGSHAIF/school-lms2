<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseGradeScale;
use App\Models\Exam;
use App\Models\Level;
use App\Models\SiteSetting;
use App\Models\StudentCourseGrade;
use App\Models\StudyGroup;
use App\Models\SystemAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExamManagementController extends Controller
{
    /**
     * التحقق من صلاحية الوصول لمركز الاختبارات والشهائد
     */
    protected function authorizeAccess(string $permission = 'exams_management.view')
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'يرجى تسجيل الدخول أولاً.');
        }

        if ($user->isAdmin() || $user->isSupervisor() || $user->isTeacher()) {
            return $user;
        }

        if ($user->hasPermission($permission)) {
            return $user;
        }

        abort(403, 'غير مصرح لك بالوصول لإدارة الاختبارات والشهائد الأكاديمية.');
    }

    /**
     * المركز الرئيسي لإدارة الاختبارات وسلالم الدرجات ورصد النتائج وإصدار الشهائد
     */
    public function index(Request $request)
    {
        $user = $this->authorizeAccess('exams_management.view');
        $tab = $request->get('tab', 'exams'); // exams | scales | grading | certificates
        $courseId = $request->get('course_id');
        $groupId = $request->get('group_id');
        $levelId = $request->get('level_id');

        // جلب الدورات والمستويات المتاحة
        $courses = Course::with('levels')->where('is_active', true)->orderBy('course_order')->get();
        $levels = Level::where('is_active', true)->orderBy('level_order')->get();

        // تصفية المجموعات بحسب دور المعلم إذا لم يكن مديراً أو مشرفاً
        $groupsQuery = StudyGroup::with(['course', 'level', 'teacher']);
        if ($user->isTeacher() && !$user->isAdmin() && !$user->isSupervisor()) {
            $groupsQuery->where(function ($q) use ($user) {
                $q->where('teacher_id', $user->id)
                  ->orWhereHas('teachers', function ($tq) use ($user) {
                      $tq->where('users.id', $user->id);
                  });
            });
        }
        $groups = $groupsQuery->latest()->get();

        // 1. بيانات تبويب الاختبارات والكويزات
        $examsQuery = Exam::with(['course', 'level', 'studyGroup', 'creator'])->latest();
        if ($courseId) {
            $examsQuery->where('course_id', $courseId);
        }
        if ($groupId) {
            $examsQuery->where('study_group_id', $groupId);
        }
        if ($levelId) {
            $examsQuery->where('level_id', $levelId);
        }
        $exams = $examsQuery->paginate(15)->appends($request->query());

        // 2. بيانات تبويب سلالم الدرجات وتوزيع الأوزان
        $gradeScales = CourseGradeScale::with(['course', 'studyGroup', 'level'])->latest()->get();
        $selectedScale = null;
        if ($groupId) {
            $selectedScale = CourseGradeScale::getScaleForGroup($groupId, $courseId);
        } elseif ($courseId) {
            $selectedScale = CourseGradeScale::where('course_id', $courseId)->whereNull('study_group_id')->first()
                ?? CourseGradeScale::getScaleForGroup(null, $courseId);
        } else {
            $selectedScale = CourseGradeScale::getScaleForGroup(null, null);
        }

        // 3. بيانات تبويب رصد ومراجعة درجات الطلاب
        $selectedGroup = null;
        $groupStudents = collect();
        $groupScale = null;
        $gradingStats = [
            'total_students' => 0,
            'passed' => 0,
            'failed' => 0,
            'incomplete' => 0,
            'average_score' => 0,
            'completion_rate' => 0,
        ];

        if ($groupId) {
            $selectedGroup = StudyGroup::with(['course', 'level', 'teacher', 'students'])->find($groupId);
            if ($selectedGroup) {
                $groupScale = CourseGradeScale::getScaleForGroup($selectedGroup->id, $selectedGroup->course_id);
                
                // جلب سجلات الدرجات المسجلة مسبقاً
                $existingGrades = StudentCourseGrade::where('study_group_id', $selectedGroup->id)
                    ->get()
                    ->keyBy('student_id');

                $studentsList = $selectedGroup->students;
                $totalScoresSum = 0;
                $gradedCount = 0;

                foreach ($studentsList as $st) {
                    $grade = $existingGrades->get($st->id);
                    if (!$grade) {
                        $grade = new StudentCourseGrade([
                            'student_id' => $st->id,
                            'study_group_id' => $selectedGroup->id,
                            'course_id' => $selectedGroup->course_id,
                            'level_id' => $selectedGroup->level_id,
                            'attendance_score' => 0,
                            'participation_score' => 0,
                            'activities_score' => 0,
                            'midterm_score' => 0,
                            'practical_score' => 0,
                            'final_exam_score' => 0,
                            'total_score' => 0,
                            'percentage' => 0,
                            'grade_letter' => 'قيد التقييم',
                            'status' => 'incomplete',
                        ]);
                    }

                    if ($grade->total_score > 0 || $grade->status === 'passed') {
                        $gradedCount++;
                        $totalScoresSum += $grade->total_score;
                    }

                    if ($grade->status === 'passed') {
                        $gradingStats['passed']++;
                    } elseif ($grade->status === 'failed') {
                        $gradingStats['failed']++;
                    } else {
                        $gradingStats['incomplete']++;
                    }

                    $st->course_grade = $grade;
                    $groupStudents->push($st);
                }

                $gradingStats['total_students'] = $studentsList->count();
                $gradingStats['average_score'] = $gradedCount > 0 ? round($totalScoresSum / $gradedCount, 1) : 0;
                $gradingStats['completion_rate'] = $gradingStats['total_students'] > 0 
                    ? round(($gradedCount / $gradingStats['total_students']) * 100, 1) 
                    : 0;
            }
        }

        // 4. بيانات تبويب الشهائد الصادرة
        $certificatesQuery = Certificate::with(['student', 'course', 'studyGroup', 'level', 'issuer'])->latest();
        if ($groupId) {
            $certificatesQuery->where('study_group_id', $groupId);
        }
        if ($courseId) {
            $certificatesQuery->where('course_id', $courseId);
        }
        $certificates = $certificatesQuery->paginate(20)->appends($request->query());

        // احتساب الطلاب الجاهزين لإصدار الشهائد في المجموعة المحددة
        $readyForCertCount = 0;
        if ($selectedGroup) {
            $issuedStudentIds = Certificate::where('study_group_id', $selectedGroup->id)->pluck('student_id')->toArray();
            $readyForCertCount = StudentCourseGrade::where('study_group_id', $selectedGroup->id)
                ->where('status', 'passed')
                ->whereNotIn('student_id', $issuedStudentIds)
                ->count();
        }

        return view('admin.exams_management.index', compact(
            'tab',
            'courses',
            'levels',
            'groups',
            'courseId',
            'groupId',
            'levelId',
            'exams',
            'gradeScales',
            'selectedScale',
            'selectedGroup',
            'groupStudents',
            'groupScale',
            'gradingStats',
            'certificates',
            'readyForCertCount'
        ));
    }

    /**
     * إنشاء أو تخصيص اختبار/كويز جديد مع تحديد المستوى أو الدورة أو المجموعة أو الطلاب والوزن النسبي
     */
    public function storeExam(Request $request)
    {
        $this->authorizeAccess('exams_management.assign');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_type' => 'required|in:all,course,level,group,students',
            'course_id' => 'nullable|exists:courses,id',
            'level_id' => 'nullable|exists:levels,id',
            'study_group_id' => 'nullable|exists:study_groups,id',
            'assigned_student_ids' => 'nullable|array',
            'assigned_student_ids.*' => 'exists:users,id',
            'quiz_period' => 'required|string',
            'weight_category' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:5|max:240',
            'total_points' => 'required|numeric|min:1|max:100',
            'passing_score' => 'required|numeric|min:1|max:100',
            'description' => 'nullable|string',
        ]);

        // إذا تم اختيار مجموعة ولم تحدد الدورة أو المستوى، نجلبهم من المجموعة تلقائياً
        if (!empty($validated['study_group_id'])) {
            $grp = StudyGroup::find($validated['study_group_id']);
            if ($grp) {
                if (empty($validated['course_id'])) $validated['course_id'] = $grp->course_id;
                if (empty($validated['level_id'])) $validated['level_id'] = $grp->level_id;
            }
        }

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;
        $validated['type'] = 'quiz';

        $exam = Exam::create($validated);

        return redirect()->route('admin.exams_management.index', [
            'tab' => 'exams',
            'course_id' => $exam->course_id,
            'group_id' => $exam->study_group_id,
        ])->with('success', "تم إنشاء الاختبار «{$exam->title}» وإسناده بنجاح.");
    }

    /**
     * إسناد وتخصيص وتحديث إعدادات ودرجة اختبار قائم
     */
    public function assignExam(Request $request, $id)
    {
        $this->authorizeAccess('exams_management.assign');

        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'target_type' => 'required|in:all,course,level,group,students',
            'course_id' => 'nullable|exists:courses,id',
            'level_id' => 'nullable|exists:levels,id',
            'study_group_id' => 'nullable|exists:study_groups,id',
            'assigned_student_ids' => 'nullable|array',
            'quiz_period' => 'nullable|string',
            'weight_category' => 'nullable|string',
            'total_points' => 'required|numeric|min:1|max:100',
            'passing_score' => 'required|numeric|min:1|max:100',
        ]);

        if (!empty($validated['study_group_id'])) {
            $grp = StudyGroup::find($validated['study_group_id']);
            if ($grp) {
                if (empty($validated['course_id'])) $validated['course_id'] = $grp->course_id;
                if (empty($validated['level_id'])) $validated['level_id'] = $grp->level_id;
            }
        }

        $exam->update($validated);

        return redirect()->back()->with('success', "تم تحديث إسناد ودرجات الاختبار «{$exam->title}» بنجاح.");
    }

    /**
     * حفظ وتعديل سلم الدرجات وتوزيع الأوزان (نظري، عملي، أنشطة، مشاركة، نصفي، نهائي)
     */
    public function saveGradeScale(Request $request)
    {
        $this->authorizeAccess('exams_management.scales');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'study_group_id' => 'nullable|exists:study_groups,id',
            'level_id' => 'nullable|exists:levels,id',
            'attendance_max' => 'required|numeric|min:0|max:100',
            'participation_max' => 'required|numeric|min:0|max:100',
            'activities_max' => 'required|numeric|min:0|max:100',
            'midterm_max' => 'required|numeric|min:0|max:100',
            'practical_max' => 'required|numeric|min:0|max:100',
            'final_exam_max' => 'required|numeric|min:0|max:100',
            'passing_percentage' => 'required|numeric|min:40|max:90',
        ]);

        $totalMax = (float)$validated['attendance_max']
                  + (float)$validated['participation_max']
                  + (float)$validated['activities_max']
                  + (float)$validated['midterm_max']
                  + (float)$validated['practical_max']
                  + (float)$validated['final_exam_max'];

        $validated['total_max'] = $totalMax;
        $validated['updated_by'] = Auth::id();

        // البحث عن سلم قائم لنفس المجموعة أو الدورة أو إنشاء جديد
        $query = CourseGradeScale::query();
        if (!empty($validated['study_group_id'])) {
            $query->where('study_group_id', $validated['study_group_id']);
        } elseif (!empty($validated['course_id'])) {
            $query->where('course_id', $validated['course_id'])->whereNull('study_group_id');
        } else {
            $query->whereNull('course_id')->whereNull('study_group_id');
        }

        $scale = $query->first();

        if ($scale) {
            $scale->update($validated);
        } else {
            $validated['created_by'] = Auth::id();
            $scale = CourseGradeScale::create($validated);
        }

        return redirect()->route('admin.exams_management.index', [
            'tab' => 'scales',
            'course_id' => $scale->course_id,
            'group_id' => $scale->study_group_id,
        ])->with('success', "تم حفظ وتطبيق سلم الدرجات وتوزيع الأوزان بنجاح (المجموع الكلي: {$totalMax} درجة).");
    }

    /**
     * رصد وحفظ وتعديل درجات طلاب المجموعة دفعة واحدة مع إعادة الاحتساب التلقائي
     */
    public function saveGroupGrades(Request $request, $groupId)
    {
        $user = $this->authorizeAccess('exams_management.grading');

        $group = StudyGroup::with(['course', 'level'])->findOrFail($groupId);

        // التحقق من صلاحية المعلم الخاصة بمجموعته
        if ($user->isTeacher() && !$user->isAdmin() && !$user->isSupervisor()) {
            $isAssigned = $group->teacher_id == $user->id || $group->teachers()->where('users.id', $user->id)->exists();
            if (!$isAssigned) {
                abort(403, 'غير مصرح لك برصد درجات مجموعة لست معلماً لها.');
            }
        }

        $gradesInput = $request->input('grades', []);
        $scale = CourseGradeScale::getScaleForGroup($group->id, $group->course_id);
        $finalize = $request->boolean('finalize', false);

        DB::beginTransaction();
        try {
            foreach ($gradesInput as $studentId => $scores) {
                $grade = StudentCourseGrade::firstOrNew([
                    'student_id' => (int)$studentId,
                    'study_group_id' => $group->id,
                ]);

                $grade->course_id = $group->course_id;
                $grade->level_id = $group->level_id;
                $grade->attendance_score = (float)($scores['attendance'] ?? 0);
                $grade->participation_score = (float)($scores['participation'] ?? 0);
                $grade->activities_score = (float)($scores['activities'] ?? 0);
                $grade->midterm_score = (float)($scores['midterm'] ?? 0);
                $grade->practical_score = (float)($scores['practical'] ?? 0);
                $grade->final_exam_score = (float)($scores['final_exam'] ?? 0);
                $grade->teacher_notes = $scores['notes'] ?? null;
                $grade->reviewed_by = Auth::id();
                if ($finalize) {
                    $grade->is_finalized = true;
                }

                $grade->recalculate($scale);
                $grade->save();
            }
            DB::commit();

            SystemAuditLog::record(
                $finalize ? 'grade.finalize' : 'grade.update',
                $finalize 
                    ? "اعتماد وتثبيت درجات ونتائج المجموعة [{$group->name}] نهائياً لعدد (" . count($gradesInput) . ") طالب"
                    : "رصد وتعديل درجات الطلاب في المجموعة [{$group->name}] لعدد (" . count($gradesInput) . ") طالب",
                $group,
                [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'course' => $group->course?->name,
                    'students_count' => count($gradesInput),
                    'is_finalized' => $finalize,
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ الدرجات: ' . $e->getMessage());
        }

        $msg = $finalize 
            ? "تم رصد وتثبيت درجات المجموعة «{$group->name}» واعتماد النتائج النهائية بنجاح." 
            : "تم حفظ ورصد درجات المجموعة «{$group->name}» بنجاح.";

        return redirect()->route('admin.exams_management.index', [
            'tab' => 'grading',
            'group_id' => $group->id,
        ])->with('success', $msg);
    }

    /**
     * طباعة كشف درجات طلاب المجموعة (إجمالي أو تفصيلي)
     */
    public function printGradeSheet(Request $request, $groupId)
    {
        $this->authorizeAccess('exams_management.print_sheets');

        $group = StudyGroup::with(['course', 'level', 'teacher', 'students'])->findOrFail($groupId);
        $scale = CourseGradeScale::getScaleForGroup($group->id, $group->course_id);
        $type = $request->get('type', 'detailed'); // detailed | summary

        $grades = StudentCourseGrade::where('study_group_id', $group->id)
            ->with('student')
            ->get()
            ->keyBy('student_id');

        $studentsWithGrades = $group->students->map(function ($st) use ($grades, $group, $scale) {
            $g = $grades->get($st->id);
            if (!$g) {
                $g = new StudentCourseGrade([
                    'student_id' => $st->id,
                    'study_group_id' => $group->id,
                    'course_id' => $group->course_id,
                    'level_id' => $group->level_id,
                    'attendance_score' => 0,
                    'participation_score' => 0,
                    'activities_score' => 0,
                    'midterm_score' => 0,
                    'practical_score' => 0,
                    'final_exam_score' => 0,
                    'total_score' => 0,
                    'percentage' => 0,
                    'grade_letter' => 'قيد التقييم',
                    'status' => 'incomplete',
                ]);
            }
            $st->course_grade = $g;
            return $st;
        });

        // إحصائيات الكشف
        $stats = [
            'total' => $studentsWithGrades->count(),
            'passed' => $studentsWithGrades->where('course_grade.status', 'passed')->count(),
            'failed' => $studentsWithGrades->where('course_grade.status', 'failed')->count(),
            'incomplete' => $studentsWithGrades->where('course_grade.status', 'incomplete')->count(),
            'avg_score' => round($studentsWithGrades->avg('course_grade.total_score') ?? 0, 1),
            'avg_pct' => round($studentsWithGrades->avg('course_grade.percentage') ?? 0, 1),
        ];

        return view('admin.exams_management.grade_sheet_print', compact(
            'group',
            'scale',
            'studentsWithGrades',
            'stats',
            'type'
        ));
    }

    /**
     * إصدار شهائد التخرج لكافة الطلاب الناجحين دفعة واحدة
     */
    public function bulkGenerateCertificates(Request $request, $groupId)
    {
        $this->authorizeAccess('exams_management.certificates_issue');

        $group = StudyGroup::with(['course', 'level', 'teacher'])->findOrFail($groupId);

        // جلب سجلات الطلاب الناجحين
        $passedGrades = StudentCourseGrade::where('study_group_id', $group->id)
            ->where('status', 'passed')
            ->with('student')
            ->get();

        if ($passedGrades->isEmpty()) {
            return redirect()->back()->with('error', 'لا يوجد طلاب مؤهلين (بحالة ناجح) في هذه المجموعة حالياً. يرجى رصد الدرجات أولاً والتأكد من نجاحهم.');
        }

        $certType = $request->get('type', 'course'); // course | diploma | level
        $certTitle = $request->filled('title') 
            ? $request->title 
            : ($group->course?->displayName ?: $group->name);

        $hours = $group->course?->hours ?: ($request->filled('hours') ? (int)$request->hours : 40);
        $issueDate = $request->filled('issue_date') ? $request->issue_date : now()->toDateString();

        $generatedCount = 0;
        $existingCount = 0;

        DB::beginTransaction();
        try {
            foreach ($passedGrades as $grade) {
                $student = $grade->student;
                if (!$student) continue;

                $cert = Certificate::firstOrNew([
                    'student_id' => $student->id,
                    'study_group_id' => $group->id,
                ]);

                if ($cert->exists) {
                    $existingCount++;
                } else {
                    // توليد رقم شهادة معتمد فريد: ASOTS-2026-XXXX
                    $randomPart = strtoupper(Str::random(5));
                    $cert->certificate_number = 'ASOTS-' . date('Y') . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT) . '-' . $randomPart;
                    $cert->qr_code_hash = sha1($cert->certificate_number . '|' . $student->id . '|' . $group->id);
                    $generatedCount++;
                }

                $cert->course_id = $group->course_id;
                $cert->level_id = $group->level_id;
                $cert->type = $certType;
                $cert->title = $certTitle;
                $cert->student_name_ar = $student->name;
                $cert->student_name_en = $student->name_en ?: $student->name;
                $cert->course_name_ar = $group->course?->name_ar ?: ($group->course?->name ?: $group->name);
                $cert->course_name_en = $group->course?->name_en ?: 'English Language Course';
                $cert->level_name_en = $group->level?->name_en ?: ($group->level?->name ?: 'Standard Level');
                $cert->hours = $hours;
                $cert->total_score = $grade->total_score;
                $cert->percentage = $grade->percentage;
                $cert->grade_letter = $grade->grade_letter;
                $cert->issue_date = $issueDate;
                $cert->is_verified = true;
                $cert->issued_by = Auth::id();
                $cert->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إصدار الشهائد: ' . $e->getMessage());
        }

        $msg = "تم تصميم وإصدار {$generatedCount} شهادة أكاديمية جديدة بنجاح لكافة الطلاب الناجحين في المجموعة.";
        if ($existingCount > 0) {
            $msg .= " (تم تحديث بيانات {$existingCount} شهادة مصدرة مسبقاً).";
        }

        return redirect()->route('admin.exams_management.index', [
            'tab' => 'certificates',
            'group_id' => $group->id,
        ])->with('success', $msg);
    }

    /**
     * إصدار شهادة فردية لطالب محدد
     */
    public function generateSingleCertificate(Request $request)
    {
        $this->authorizeAccess('exams_management.certificates_issue');

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'study_group_id' => 'required|exists:study_groups,id',
            'type' => 'required|in:course,diploma,level',
            'title' => 'nullable|string|max:255',
            'hours' => 'nullable|integer|min:1',
            'issue_date' => 'nullable|date',
        ]);

        $group = StudyGroup::with(['course', 'level'])->findOrFail($validated['study_group_id']);
        $student = User::findOrFail($validated['student_id']);
        $grade = StudentCourseGrade::where('study_group_id', $group->id)
            ->where('student_id', $student->id)
            ->first();

        $cert = Certificate::firstOrNew([
            'student_id' => $student->id,
            'study_group_id' => $group->id,
        ]);

        if (!$cert->exists) {
            $randomPart = strtoupper(Str::random(5));
            $cert->certificate_number = 'ASOTS-' . date('Y') . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT) . '-' . $randomPart;
            $cert->qr_code_hash = sha1($cert->certificate_number . '|' . $student->id . '|' . $group->id);
        }

        $cert->course_id = $group->course_id;
        $cert->level_id = $group->level_id;
        $cert->type = $validated['type'];
        $cert->title = $validated['title'] ?: ($group->course?->displayName ?: $group->name);
        $cert->student_name_ar = $student->name;
        $cert->student_name_en = $student->name_en ?: $student->name;
        $cert->course_name_ar = $group->course?->name_ar ?: ($group->course?->name ?: $group->name);
        $cert->course_name_en = $group->course?->name_en ?: 'English Language Course';
        $cert->level_name_en = $group->level?->name_en ?: ($group->level?->name ?: 'Standard Level');
        $cert->hours = !empty($validated['hours']) ? (int)$validated['hours'] : ($group->course?->hours ?: 40);
        $cert->total_score = $grade ? $grade->total_score : 100.00;
        $cert->percentage = $grade ? $grade->percentage : 100.00;
        $cert->grade_letter = $grade ? $grade->grade_letter : 'ممتاز (Excellent)';
        $cert->issue_date = !empty($validated['issue_date']) ? $validated['issue_date'] : now()->toDateString();
        $cert->is_verified = true;
        $cert->issued_by = Auth::id();
        $cert->save();

        return redirect()->back()->with('success', "تم إصدار الشهادة الأكاديمية للطالب «{$student->name}» بنجاح.");
    }

    /**
     * طباعة شهادة طالب فردية بتصميم ملكي فاخر
     */
    public function printCertificate($id)
    {
        $this->authorizeAccess('exams_management.certificates_print');

        $certificate = Certificate::with(['student', 'course', 'studyGroup', 'level', 'issuer'])->findOrFail($id);
        $certificates = collect([$certificate]);
        $group = $certificate->studyGroup;

        return view('admin.exams_management.certificate_print', compact('certificates', 'group'));
    }

    /**
     * طباعة شهائد المجموعة كاملة دفعة واحدة بتصميم ملكي A4 Landscape
     */
    public function printBatchCertificates(Request $request, $groupId)
    {
        $this->authorizeAccess('exams_management.certificates_print');

        $group = StudyGroup::with(['course', 'level', 'teacher'])->findOrFail($groupId);
        $certificates = Certificate::where('study_group_id', $groupId)
            ->with(['student', 'course', 'studyGroup', 'level', 'issuer'])
            ->get();

        if ($certificates->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد شهائد مصدرة لهذه المجموعة بعد.');
        }

        return view('admin.exams_management.certificate_print', compact('certificates', 'group'));
    }

    /**
     * حذف شهادة مصدرة
     */
    public function destroyCertificate($id)
    {
        $this->authorizeAccess('exams_management.certificates_issue');

        $certificate = Certificate::with(['student', 'course'])->findOrFail($id);
        $num = $certificate->certificate_number;
        $studentName = $certificate->student?->name ?? 'غير محدد';
        $courseName = $certificate->course?->name ?? 'غير محدد';

        SystemAuditLog::record(
            'certificate.delete',
            "إلغاء وحذف الشهادة الصادرة رقم ({$num}) للطالب ({$studentName}) في دورة [{$courseName}]",
            null,
            [
                'old_values' => [
                    'رقم الشهادة' => $num,
                    'اسم الطالب' => $studentName,
                    'الدورة' => $courseName,
                    'التقدير' => $certificate->grade_letter ?? ($certificate->percentage . '%'),
                    'تاريخ الإصدار' => $certificate->issue_date,
                ],
                'new_values' => [
                    'الحالة' => 'محذوفة وملغاة نهائياً من السجلات 🗑️',
                ],
                'certificate_number' => $num,
            ]
        );

        $certificate->delete();

        return redirect()->back()->with('success', "تم حذف الشهادة ذات الرقم «{$num}» بنجاح.");
    }

    /**
     * صفحة التحقق العامة من صحة الشهادة برقم السيريال أو مسح QR
     */
    public function verifyCertificate($serial)
    {
        $certificate = Certificate::where('certificate_number', $serial)
            ->with(['student', 'course', 'studyGroup', 'level', 'issuer'])
            ->first();

        return view('public.verify_certificate', compact('certificate', 'serial'));
    }
}
