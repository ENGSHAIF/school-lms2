<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Level;
use App\Models\StudentEnrollment;
use App\Models\StudyGroup;
use App\Models\StudyGroupStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudyGroupController extends Controller
{
    // عرض قائمة المجموعات وإدارتها
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $courseId = $request->query('course_id');
        $levelId = $request->query('level_id');
        $term = $request->query('term');
        $batch = $request->query('batch');
        $teacherId = $request->query('teacher_id');
        $supervisorId = $request->query('supervisor_id');
        $search = $request->query('search');

        $query = StudyGroup::with([
            'course',
            'level.resources.exam',
            'teacher',
            'supervisor',
            'teachers',
            'supervisors',
            'requester',
            'activities.publisher',
            'students' => function ($q) {
                $q->wherePivot('status', 'active');
            }
        ]);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($levelId) {
            $query->where('level_id', $levelId);
        }

        if ($term && $term !== 'all') {
            $query->where('term', $term);
        }

        if ($batch && $batch !== 'all') {
            $query->where('batch', $batch);
        }

        if ($teacherId) {
            $query->where(function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId)
                  ->orWhereHas('teachers', function ($t) use ($teacherId) {
                      $t->where('users.id', $teacherId);
                  });
            });
        }

        if ($supervisorId) {
            $query->where(function ($q) use ($supervisorId) {
                $q->where('supervisor_id', $supervisorId)
                  ->orWhereHas('supervisors', function ($s) use ($supervisorId) {
                      $s->where('users.id', $supervisorId);
                  });
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('term', 'like', "%{$search}%")
                  ->orWhere('batch', 'like', "%{$search}%")
                  ->orWhere('program_type', 'like', "%{$search}%")
                  ->orWhere('schedule_days', 'like', "%{$search}%")
                  ->orWhereHas('teacher', function ($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $groups = $query->latest()->get();

        // إحصائيات سريعة
        $totalGroups = StudyGroup::count();
        $activeGroups = StudyGroup::where('status', 'active')->count();
        $totalEnrolledStudents = StudyGroupStudent::where('status', 'active')->distinct('student_id')->count('student_id');
        $totalTeachersAssigned = StudyGroup::whereNotNull('teacher_id')->distinct('teacher_id')->count('teacher_id');
        
        // طلبات فتح البث الحي المعلقة بانتظار اعتماد الإدارة
        $pendingLiveRequests = StudyGroup::with(['requester', 'course', 'teacher'])
            ->where('live_status', 'pending_approval')
            ->get();

        // البيانات المساعدة للنوافذ المنبثقة
        $courses = Course::with('level')->where('is_active', true)->get();
        $levels = Level::with('courses')->where('is_active', true)->get();
        $teachers = User::where('role', 'teacher')->where('status', 'active')->orderBy('name')->get();
        $supervisors = User::where('role', 'supervisor')->where('status', 'active')->orderBy('name')->get();

        // خيارات الأترام والدفعات المتاحة للفلترة
        $allTerms = StudyGroup::whereNotNull('term')->where('term', '!=', '')->distinct()->pluck('term')->all();
        $allBatches = StudyGroup::whereNotNull('batch')->where('batch', '!=', '')->distinct()->pluck('batch')->all();
        $allPrograms = StudyGroup::whereNotNull('program_type')->where('program_type', '!=', '')->distinct()->pluck('program_type')->all();

        // قائمة الطلاب المؤهلين للتسكين (الطلاب المسجلين أو الذين أتموا الاختبار)
        $students = User::where('role', 'student')
            ->with(['studyGroups', 'enrollments.level', 'financialAccount'])
            ->orderBy('name')
            ->get();

        return view('admin.groups.index', compact(
            'groups',
            'totalGroups',
            'activeGroups',
            'totalEnrolledStudents',
            'totalTeachersAssigned',
            'pendingLiveRequests',
            'courses',
            'levels',
            'teachers',
            'supervisors',
            'students',
            'allTerms',
            'allBatches',
            'allPrograms'
        ));
    }

    // إنشاء مجموعة دراسية جديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:190',
            'name_ar' => 'nullable|string|max:190',
            'name_en' => 'nullable|string|max:190',
            'course_id' => 'required|exists:courses,id',
            'level_id' => 'nullable|exists:levels,id',
            'term' => 'nullable|string|max:190',
            'batch' => 'nullable|string|max:190',
            'program_type' => 'nullable|string|max:190',
            'duration_months' => 'nullable|integer|min:1|max:36',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'teacher_id' => 'nullable|exists:users,id',
            'primary_teacher_id' => 'nullable|exists:users,id',
            'assistant_teacher_ids' => 'nullable|array',
            'assistant_teacher_ids.*' => 'exists:users,id',
            'teacher_roles' => 'nullable|array',
            'supervisor_id' => 'nullable|exists:users,id',
            'primary_supervisor_id' => 'nullable|exists:users,id',
            'assistant_supervisor_ids' => 'nullable|array',
            'assistant_supervisor_ids.*' => 'exists:users,id',
            'max_students' => 'required|integer|min:1|max:100',
            'schedule_days' => 'nullable|string|max:190',
            'schedule_time' => 'nullable|string|max:190',
            'description' => 'nullable|string',
        ]);

        $nameAr = $request->filled('name_ar') ? trim($request->name_ar) : trim($request->name);
        $nameEn = $request->filled('name_en') ? trim($request->name_en) : null;
        if (empty($nameAr) && !empty($nameEn)) {
            $nameAr = $nameEn;
        }
        $fullName = $nameEn && $nameEn !== $nameAr ? "{$nameAr} ({$nameEn})" : $nameAr;

        $validated['name_ar'] = $nameAr;
        $validated['name_en'] = $nameEn;
        $validated['name'] = $fullName;
        $validated['room_id'] = 'asots-grp-' . strtolower(Str::random(8));
        $validated['status'] = 'active';
        $validated['teacher_id'] = $request->primary_teacher_id ?: ($request->teacher_id ?: null);
        $validated['supervisor_id'] = $request->primary_supervisor_id ?: ($request->supervisor_id ?: null);

        $group = StudyGroup::create($validated);

        // مزامنة المعلمين والمشرفين
        $this->syncGroupTeachersAndSupervisors($group, $request);

        return redirect()->route('admin.groups.index')
            ->with('success', "تم إنشاء المجموعة الدراسية [{$group->name}] وتعيين الكادر التدريسي والأكاديمي بنجاح!");
    }

    // تعديل بيانات المجموعة وتعيين الكادر التعليمي
    public function update(Request $request, $id)
    {
        $group = StudyGroup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|max:190',
            'name_ar' => 'nullable|string|max:190',
            'name_en' => 'nullable|string|max:190',
            'course_id' => 'required|exists:courses,id',
            'level_id' => 'nullable|exists:levels,id',
            'term' => 'nullable|string|max:190',
            'batch' => 'nullable|string|max:190',
            'program_type' => 'nullable|string|max:190',
            'duration_months' => 'nullable|integer|min:1|max:36',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'teacher_id' => 'nullable|exists:users,id',
            'primary_teacher_id' => 'nullable|exists:users,id',
            'assistant_teacher_ids' => 'nullable|array',
            'assistant_teacher_ids.*' => 'exists:users,id',
            'teacher_roles' => 'nullable|array',
            'supervisor_id' => 'nullable|exists:users,id',
            'primary_supervisor_id' => 'nullable|exists:users,id',
            'assistant_supervisor_ids' => 'nullable|array',
            'assistant_supervisor_ids.*' => 'exists:users,id',
            'max_students' => 'required|integer|min:1|max:100',
            'schedule_days' => 'nullable|string|max:190',
            'schedule_time' => 'nullable|string|max:190',
            'status' => 'required|in:active,completed,archived',
            'description' => 'nullable|string',
        ]);

        $nameAr = $request->filled('name_ar') ? trim($request->name_ar) : ($request->filled('name') ? trim($request->name) : ($group->name_ar ?: $group->name));
        $nameEn = $request->filled('name_en') ? trim($request->name_en) : null;
        if (empty($nameAr) && !empty($nameEn)) {
            $nameAr = $nameEn;
        }
        $fullName = $nameEn && $nameEn !== $nameAr ? "{$nameAr} ({$nameEn})" : $nameAr;

        $validated['name_ar'] = $nameAr;
        $validated['name_en'] = $nameEn;
        $validated['name'] = $fullName;
        $validated['teacher_id'] = $request->primary_teacher_id ?: ($request->teacher_id ?: $group->teacher_id);
        $validated['supervisor_id'] = $request->primary_supervisor_id ?: ($request->supervisor_id ?: $group->supervisor_id);

        $group->update($validated);

        // مزامنة المعلمين والمشرفين
        $this->syncGroupTeachersAndSupervisors($group, $request);

        return redirect()->route('admin.groups.index')
            ->with('success', "تم تحديث بيانات المجموعة [{$group->name}] وتعيين الكادر التدريسي بنجاح.");
    }

    // دالة مساعدة لمزامنة المعلمين والمشرفين المتعددين
    private function syncGroupTeachersAndSupervisors(StudyGroup $group, Request $request)
    {
        // 1. مزامنة المعلمين (المعلم الرئيسي + المعلمون المساعدون)
        $primaryTeacherId = $request->primary_teacher_id ?: $request->teacher_id;
        $assistantTeacherIds = (array) ($request->assistant_teacher_ids ?: []);
        $teacherRoles = (array) ($request->teacher_roles ?: []);

        $teachersPivotData = [];

        if ($primaryTeacherId) {
            $teachersPivotData[$primaryTeacherId] = [
                'is_primary' => true,
                'role_title' => $teacherRoles[$primaryTeacherId] ?? 'معلم رئيسي',
            ];
        }

        foreach ($assistantTeacherIds as $asstId) {
            if ($asstId && $asstId != $primaryTeacherId) {
                $teachersPivotData[$asstId] = [
                    'is_primary' => false,
                    'role_title' => $teacherRoles[$asstId] ?? 'معلم مساعد',
                ];
            }
        }

        $group->teachers()->sync($teachersPivotData);

        // 2. مزامنة المشرفين
        $primarySupervisorId = $request->primary_supervisor_id ?: $request->supervisor_id;
        $assistantSupervisorIds = (array) ($request->assistant_supervisor_ids ?: []);

        $supervisorsPivotData = [];

        if ($primarySupervisorId) {
            $supervisorsPivotData[$primarySupervisorId] = [
                'is_primary' => true,
                'notes' => 'المشرف الأكاديمي الرئيسي للمجموعة',
            ];
        }

        foreach ($assistantSupervisorIds as $supId) {
            if ($supId && $supId != $primarySupervisorId) {
                $supervisorsPivotData[$supId] = [
                    'is_primary' => false,
                    'notes' => 'مشرف مساعد ومتابع للأنشطة',
                ];
            }
        }

        $group->supervisors()->sync($supervisorsPivotData);
    }

    // تسكين وقيد الطلاب دفعة واحدة داخل المجموعة (مثل الـ 5 طلاب)
    public function enrollStudents(Request $request, $id)
    {
        $group = StudyGroup::findOrFail($id);

        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
        ]);

        $addedCount = 0;
        $alreadyInCount = 0;

        foreach ($request->student_ids as $studentId) {
            // التحقق من عدم امتلاء المجموعة
            if ($group->isFull()) {
                break;
            }

            // فحص هل الطالب مسكن مسبقاً في هذه المجموعة
            $exists = StudyGroupStudent::where('study_group_id', $group->id)
                ->where('student_id', $studentId)
                ->first();

            if ($exists) {
                if ($exists->status !== 'active') {
                    $exists->update(['status' => 'active', 'enrolled_at' => now()]);
                    $addedCount++;
                } else {
                    $alreadyInCount++;
                }
            } else {
                StudyGroupStudent::create([
                    'study_group_id' => $group->id,
                    'student_id' => $studentId,
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);
                $addedCount++;
            }

            // إذا كانت المجموعة مرتبطة بمستوى أكاديمي، نضمن تفعيل المنهج للطالب
            if ($group->level_id) {
                StudentEnrollment::firstOrCreate(
                    [
                        'user_id' => $studentId,
                        'level_id' => $group->level_id,
                    ],
                    [
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]
                );

                // تفعيل حساب الطالب للدراسة
                User::where('id', $studentId)->update(['status' => 'active']);
            }
        }

        $msg = "تم تسكين ({$addedCount}) طالب في مجموعة [{$group->name}] بنجاح!";
        if ($alreadyInCount > 0) {
            $msg .= " (يوجد {$alreadyInCount} طلاب مسكنين مسبقاً).";
        }

        return redirect()->route('admin.groups.index')->with('success', $msg);
    }

    // حذف أو إلغاء تسكين طالب من المجموعة
    public function removeStudent(Request $request, $groupId, $studentId)
    {
        $group = StudyGroup::findOrFail($groupId);

        $enrollment = StudyGroupStudent::where('study_group_id', $groupId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        // الاحتفاظ بالسجل التاريخي كما طلبت الوثيقة
        $enrollment->update(['status' => 'cancelled']);

        return redirect()->route('admin.groups.index')
            ->with('success', "تم إلغاء تسكين الطالب من مجموعة [{$group->name}] مع الاحتفاظ بسجله التاريخي.");
    }

    // نقل طالب من مجموعة إلى مجموعة أخرى
    public function transferStudent(Request $request, $groupId, $studentId)
    {
        $request->validate([
            'target_group_id' => 'required|exists:study_groups,id|different:groupId',
        ]);

        $fromGroup = StudyGroup::findOrFail($groupId);
        $toGroup = StudyGroup::findOrFail($request->target_group_id);

        if ($toGroup->isFull()) {
            return back()->with('error', "عذراً، المجموعة المستهدفة [{$toGroup->name}] مكتملة العدد ولا تتسع لطلاب جدد.");
        }

        // تحديث القديم
        StudyGroupStudent::where('study_group_id', $groupId)
            ->where('student_id', $studentId)
            ->update([
                'status' => 'transferred',
                'notes' => 'تم النقل إلى مجموعة: ' . $toGroup->name . ' في ' . now()->format('Y-m-d H:i'),
            ]);

        // إضافة في الجديد
        StudyGroupStudent::updateOrCreate(
            ['study_group_id' => $toGroup->id, 'student_id' => $studentId],
            ['status' => 'active', 'enrolled_at' => now(), 'notes' => 'منقول من: ' . $fromGroup->name]
        );

        return redirect()->route('admin.groups.index')
            ->with('success', "تم نقل الطالب بنجاح من [{$fromGroup->name}] إلى [{$toGroup->name}].");
    }

    // حذف أو أرشفة المجموعة
    public function destroy($id)
    {
        $group = StudyGroup::findOrFail($id);

        // التحقق من الصلاحية
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('groups.delete')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية حذف المجموعات الدراسية.');
        }

        // منع الحذف إذا كانت المجموعة تضم طلاباً أو عُقدت فيها جلسات دراسية (كما طلبت الإدارة)
        $studentsCount = $group->students()->where('study_group_students.status', '!=', 'transferred')->count();
        $sessionsCount = $group->sessions()->count();

        if ($studentsCount > 0 || $sessionsCount > 0) {
            return back()->with('error', "لا يمكن حذف المجموعة [{$group->name}] لوجود ({$studentsCount}) طالب مسجلين أو ({$sessionsCount}) جلسة دراسية منقعدة فيها. يمكنك إيقاف المجموعة أو نقل الطلاب بدلاً من الحذف للحفاظ على السجلات الأكاديمية والتقييمات.");
        }

        $name = $group->name;
        $group->delete();

        return redirect()->route('admin.groups.index')
            ->with('success', "تم حذف المجموعة الدراسية [{$name}] بنجاح.");
    }

    // إضافة نشاط أو ورقة عمل أو تسجيل صوتي للمجموعة (مثل الواتساب)
    public function storeActivity(Request $request, $groupId)
    {
        $group = StudyGroup::with('students', 'teachers')->findOrFail($groupId);
        $user = auth()->user();

        // فحص الصلاحية: هل المستخدم معلم المجموعة أو مشرفها أو مدير أو طالب مسجل في هذه المجموعة
        $isStaff = ($user->isAdmin() || $user->isSupervisor() || $user->id === $group->teacher_id || $user->id === $group->supervisor_id || $group->teachers->contains('id', $user->id));
        $isGroupStudent = $group->students->contains('id', $user->id);

        if (!$isStaff && !$isGroupStudent) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'غير مصرح لك بالنشر في هذه المجموعة.'], 403);
            }
            return back()->with('error', 'غير مصرح لك بالنشر في هذه المجموعة.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:190',
            'type' => 'required|in:worksheet,audio,video,announcement,image,student_submission',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'attachment' => 'nullable|file|max:51200', // حد أقصى 50MB
        ]);

        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName() ?: ('upload_' . time() . '.' . ($file->guessExtension() ?: 'bin'));
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
            
            // تصحيح امتداد التسجيلات الصوتية المسجلة من المتصفح عبر WebM
            if ($validated['type'] === 'audio' && ($extension === 'bin' || empty($extension))) {
                $extension = 'webm';
            }

            $sizeBytes = $file->getSize();
            
            // حساب الحجم بطريقة سهلة القراءة
            if ($sizeBytes >= 1048576) {
                $fileSize = round($sizeBytes / 1048576, 1) . ' MB';
            } else {
                $fileSize = round($sizeBytes / 1024, 0) . ' KB';
            }

            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $uniqueName = time() . '_' . Str::slug($baseName ?: 'file') . '.' . $extension;
            $destinationPath = public_path('uploads/group_activities');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $uniqueName);
            $filePath = 'uploads/group_activities/' . $uniqueName;
            $fileName = $originalName;
        }

        $activity = \App\Models\GroupActivity::create([
            'study_group_id' => $group->id,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'instructions' => $validated['instructions'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        $typeArabic = match ($validated['type']) {
            'audio' => 'التسجيل الصوتي',
            'image' => 'صورة الإجابة',
            'video' => 'مقطع الفيديو',
            'announcement' => 'التنبيه والملاحظة',
            'student_submission' => 'ورقة إجابة الطالب',
            default => 'ورقة النشاط / الواجب',
        };

        $successMsg = $user->isStudent() 
            ? "تم إرسال {$typeArabic} بنجاح إلى حائط المجموعة والمعلم!"
            : "تم نشر {$typeArabic} بنجاح لجميع طلاب مجموعة [{$group->name}]!";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'activity' => [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'type' => $activity->type,
                    'type_icon' => $activity->type_icon,
                    'type_label' => $activity->type_label,
                    'instructions' => $activity->instructions,
                    'file_path' => $activity->file_path ? asset($activity->file_path) : null,
                    'file_name' => $activity->file_name,
                    'file_size' => $activity->file_size,
                    'publisher_name' => $user->name,
                    'publisher_role' => $user->role,
                    'created_at' => $activity->created_at->diffForHumans(),
                ],
            ]);
        }

        return back()->with('success', $successMsg);
    }

    // حذف نشاط أو تسجيل صوتي من المجموعة
    public function destroyActivity($activityId)
    {
        $activity = \App\Models\GroupActivity::findOrFail($activityId);
        
        // التحقق من الصلاحية (الناشر أو المدير)
        if (auth()->id() !== $activity->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'غير مصرح لك بحذف هذا النشاط.');
        }

        if ($activity->file_path && file_exists(public_path($activity->file_path))) {
            @unlink(public_path($activity->file_path));
        }

        $activity->delete();

        return back()->with('success', 'تم حذف النشاط بنجاح.');
    }

    // بدء وفتح البث الحي للمجموعة (المعلم أو الإدارة)
    public function startLive(Request $request, $groupId)
    {
        $group = StudyGroup::with('students')->findOrFail($groupId);
        $user = auth()->user();

        // التحقق من الصلاحيات (المدير، أستاذ المجموعة، مشرف المجموعة)
        if (!$user->isAdmin() && $user->id !== $group->teacher_id && $user->id !== $group->supervisor_id) {
            abort(403, 'غير مصرح لك بفتح البث الحي لهذه المجموعة.');
        }

        $group->update([
            'is_live' => true,
            'live_status' => 'live',
            'live_started_at' => now(),
            'live_approved_by' => $user->id,
        ]);

        // إرسال إشعار في سجل الواتساب لطلاب المجموعة
        \App\Models\WhatsAppLog::create([
            'user_id' => $user->id,
            'phone_number' => 'group_' . $group->id,
            'message_type' => 'announcement',
            'message_body' => "بدأ البث المباشر لمجموعة [{$group->name}] الآن عبر المنصة! الأستاذ متواجد داخل الفصل التفاعلي الحي، يمكنكم الدخول الآن.",
            'status' => 'sent',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "تم فتح البث الحي لمجموعة [{$group->name}] بنجاح، وفتح الدخول لجميع الطلاب في غرفة الانتظار.",
                'room_url' => route('classroom.show', $group->room_id),
            ]);
        }

        return back()->with('success', "تم فتح البث الحي لمجموعة [{$group->name}] بنجاح! يمكن للطلاب في غرفة الانتظار الدخول الآن.");
    }

    // إغلاق وإنهاء البث الحي للمجموعة
    public function stopLive(Request $request, $groupId)
    {
        $group = StudyGroup::findOrFail($groupId);
        $user = auth()->user();

        if (!$user->isAdmin() && $user->id !== $group->teacher_id && $user->id !== $group->supervisor_id) {
            abort(403, 'غير مصرح لك بإغلاق البث الحي لهذه المجموعة.');
        }

        $group->update([
            'is_live' => false,
            'live_status' => 'closed',
            'live_requested_by' => null,
            'live_requested_at' => null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "تم إغلاق البث الحي لمجموعة [{$group->name}].",
            ]);
        }

        return back()->with('success', "تم إغلاق البث الحي لمجموعة [{$group->name}]. تحول الفصل لغرفة انتظار للطلاب.");
    }

    // إرسال طلب من الطالب أو المعلم لفتح البث الحي واعتماده من الإدارة
    public function requestLive(Request $request, $groupId)
    {
        $group = StudyGroup::findOrFail($groupId);
        $user = auth()->user();

        // تحديث حالة المجموعة إلى بانتظار الاعتماد
        $group->update([
            'live_status' => 'pending_approval',
            'live_requested_by' => $user->id,
            'live_requested_at' => now(),
        ]);

        // تسجيل إشعار إداري في سجل الإشعارات والواتساب
        \App\Models\WhatsAppLog::create([
            'user_id' => $user->id,
            'phone_number' => $user->phone_whatsapp ?: '00967735999101',
            'message_type' => 'announcement',
            'message_body' => "طلب فتح بث حي جديد: أرسل ({$user->name}) طلباً لفتح البث الحي لمجموعة [{$group->name}]. يرجى الدخول للوحة التحكم للاعتماد أو الرفض.",
            'status' => 'queued',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إرسال طلب فتح البث الحي إلى الإدارة بنجاح! سيتم إشعارك فور الاعتماد.',
                'live_status' => 'pending_approval',
            ]);
        }

        return back()->with('success', 'تم إرسال طلب فتح البث الحي إلى الإدارة بنجاح! سيتم فتح الفصل فور اعتماد الطلب.');
    }

    // رفض طلب فتح البث الحي للمجموعة
    public function rejectLive(Request $request, $groupId)
    {
        $group = StudyGroup::findOrFail($groupId);
        $user = auth()->user();

        if (!$user->isAdmin() && $user->id !== $group->teacher_id && $user->id !== $group->supervisor_id) {
            abort(403, 'غير مصرح لك برفض الطلب.');
        }

        $group->update([
            'live_status' => 'closed',
            'is_live' => false,
            'live_requested_by' => null,
            'live_requested_at' => null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم رفض طلب فتح البث الحي.',
            ]);
        }

        return back()->with('info', "تم رفض طلب فتح البث الحي لمجموعة [{$group->name}].");
    }
}
