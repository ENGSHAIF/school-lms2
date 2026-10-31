<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Exam;
use App\Models\GroupActivity;
use App\Models\Level;
use App\Models\LevelResource;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LevelResourceController extends Controller
{
    // عرض واستعراض بنك المناهج والموارد التعليمية للدورة المحددة (Course Curriculum Bank)
    public function courseResources($courseId)
    {
        $course = Course::with([
            'level',
            'sections' => function ($q) {
                $q->orderBy('order_index');
            },
            'sections.resources' => function ($q) {
                $q->orderBy('order_index')->latest();
            },
            'sections.resources.creator',
            'sections.resources.exam',
            'sections.resources.groupActivities',
            'resources' => function ($q) {
                $q->orderBy('order_index')->latest();
            },
            'resources.creator',
            'resources.exam',
            'resources.groupActivities',
        ])->findOrFail($courseId);

        $stats = [
            'total' => $course->resources->count(),
            'books' => $course->resources->where('type', 'book')->count(),
            'audios' => $course->resources->where('type', 'audio')->count(),
            'videos' => $course->resources->where('type', 'video')->count(),
            'images' => $course->resources->where('type', 'image')->count(),
            'worksheets' => $course->resources->where('type', 'worksheet')->count(),
            'quizzes' => $course->resources->where('type', 'quiz')->count(),
            'sections' => $course->sections->count(),
        ];

        // المجموعات الدراسية المسكنة في هذه الدورة والمجموعات النشطة لمشاركة الموارد بسهولة
        $studyGroups = StudyGroup::where('status', 'active')
            ->with(['teacher', 'students', 'course', 'level'])
            ->orderByRaw('CASE WHEN course_id = ? THEN 0 WHEN level_id = ? THEN 1 ELSE 2 END', [$course->id, $course->level_id ?? 0])
            ->orderBy('name')
            ->get();

        // الكويزات والاختبارات المتاحة للربط
        $availableExams = Exam::where('is_active', true)
            ->where(function ($q) use ($course) {
                $q->where('course_id', $course->id)
                    ->orWhere('level_id', $course->level_id)
                    ->orWhereNull('level_id');
            })
            ->get();

        $sections = $course->sections;
        $unassignedResources = $course->resources->whereNull('course_section_id');

        return view('admin.courses.resources', compact('course', 'stats', 'studyGroups', 'availableExams', 'sections', 'unassignedResources'));
    }

    // رفع وإضافة مورد تعليمي جديد لبنك الدورة
    public function storeCourseResource(Request $request, $courseId)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->hasPermission('upload_level_resources') && ! $user->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية رفع موارد وإدارة بنك المناهج.');
        }

        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:book,audio,video,worksheet,quiz,image',
            'course_section_id' => 'nullable|exists:course_sections,id',
            'description' => 'nullable|string',
            'external_url' => 'nullable|url|max:500',
            'duration_hours' => 'nullable|numeric|min:0.01|max:1000',
            'duration_minutes' => 'nullable|integer|min:1|max:60000',
            'exam_id' => 'nullable|exists:exams,id',
            'order_index' => 'nullable|integer|min:1',
            'attachment' => 'nullable|file|max:524288', // حد أقصى 512 ميجابايت للمحاضرات والفيديوهات
        ]);

        $durationHours = null;
        $durationMinutes = null;
        if ($request->filled('duration_hours')) {
            $durationHours = round((float) $request->input('duration_hours'), 2);
            $durationMinutes = (int) round($durationHours * 60);
        } elseif ($request->filled('duration_minutes')) {
            $durationMinutes = (int) $request->input('duration_minutes');
            $durationHours = round($durationMinutes / 60.0, 2);
        }

        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $sizeBytes = $file->getSize();

            if ($sizeBytes >= 1048576) {
                $fileSize = round($sizeBytes / 1048576, 1).' MB';
            } else {
                $fileSize = round($sizeBytes / 1024, 0).' KB';
            }

            $uniqueName = 'crs_'.$course->id.'_'.time().'_'.Str::slug(pathinfo($originalName, PATHINFO_FILENAME)).'.'.$extension;
            $destinationPath = public_path('uploads/level_resources');

            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $uniqueName);
            $filePath = 'uploads/level_resources/'.$uniqueName;
            $fileName = $originalName;
        }

        $resource = LevelResource::create([
            'course_id' => $course->id,
            'level_id' => $course->level_id,
            'course_section_id' => $validated['course_section_id'] ?? null,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'external_url' => $validated['external_url'] ?? null,
            'duration_hours' => $durationHours,
            'duration_minutes' => $durationMinutes,
            'exam_id' => $validated['exam_id'] ?? null,
            'order_index' => $validated['order_index'] ?? ($course->resources()->max('order_index') + 1),
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        return back()->with('success', "تمت إضافة المورد الأكاديمي [{$resource->title}] إلى بنك دورة [{$course->name_en}] بنجاح! 🚀");
    }

    // عرض واستعراض بنك المناهج والموارد التعليمية للمستوى الأكاديمي (توافق إضافي)
    public function index($levelId)
    {
        $level = Level::with(['course', 'courses', 'resources' => function ($q) {
            $q->orderBy('order_index')->latest();
        }, 'resources.creator', 'resources.exam', 'resources.groupActivities'])->findOrFail($levelId);

        // إحصائيات الموارد المصنفة
        $stats = [
            'total' => $level->resources->count(),
            'books' => $level->resources->where('type', 'book')->count(),
            'audios' => $level->resources->where('type', 'audio')->count(),
            'videos' => $level->resources->where('type', 'video')->count(),
            'images' => $level->resources->where('type', 'image')->count(),
            'worksheets' => $level->resources->where('type', 'worksheet')->count(),
            'quizzes' => $level->resources->where('type', 'quiz')->count(),
        ];

        // المجموعات الدراسية المسكنة في هذا المستوى للمشاركة المباشرة
        $studyGroups = StudyGroup::where('level_id', $level->id)
            ->where('status', 'active')
            ->with(['teacher', 'students'])
            ->get();

        // الكويزات والاختبارات المتاحة للربط
        $availableExams = Exam::where('is_active', true)
            ->where(function ($q) use ($levelId) {
                $q->where('level_id', $levelId)->orWhereNull('level_id');
            })
            ->get();

        return view('admin.levels.resources', compact('level', 'stats', 'studyGroups', 'availableExams'));
    }

    // رفع وإضافة مورد تعليمي جديد لبنك المستوى
    public function store(Request $request, $levelId)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->hasPermission('upload_level_resources') && ! $user->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية رفع موارد وإدارة بنك المناهج.');
        }

        $level = Level::findOrFail($levelId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:book,audio,video,worksheet,quiz,image',
            'description' => 'nullable|string',
            'external_url' => 'nullable|url|max:500',
            'duration_hours' => 'nullable|numeric|min:0.01|max:1000',
            'duration_minutes' => 'nullable|integer|min:1|max:60000',
            'exam_id' => 'nullable|exists:exams,id',
            'order_index' => 'nullable|integer|min:1',
            'attachment' => 'nullable|file|max:524288', // حد أقصى 512 ميجابايت للمحاضرات والفيديوهات
        ]);

        $durationHours = null;
        $durationMinutes = null;
        if ($request->filled('duration_hours')) {
            $durationHours = round((float) $request->input('duration_hours'), 2);
            $durationMinutes = (int) round($durationHours * 60);
        } elseif ($request->filled('duration_minutes')) {
            $durationMinutes = (int) $request->input('duration_minutes');
            $durationHours = round($durationMinutes / 60.0, 2);
        }

        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $sizeBytes = $file->getSize();

            if ($sizeBytes >= 1048576) {
                $fileSize = round($sizeBytes / 1048576, 1).' MB';
            } else {
                $fileSize = round($sizeBytes / 1024, 0).' KB';
            }

            $uniqueName = 'lvl_'.$level->id.'_'.time().'_'.Str::slug(pathinfo($originalName, PATHINFO_FILENAME)).'.'.$extension;
            $destinationPath = public_path('uploads/level_resources');

            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $uniqueName);
            $filePath = 'uploads/level_resources/'.$uniqueName;
            $fileName = $originalName;
        }

        $resource = LevelResource::create([
            'level_id' => $level->id,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'external_url' => $validated['external_url'] ?? null,
            'duration_hours' => $durationHours,
            'duration_minutes' => $durationMinutes,
            'exam_id' => $validated['exam_id'] ?? null,
            'order_index' => $validated['order_index'] ?? ($level->resources()->max('order_index') + 1),
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        // توافق تلقائي: إذا كان كتاباً يضاف أيضاً إلى جدول books
        if ($resource->type === 'book' && $filePath) {
            Book::firstOrCreate(
                ['level_id' => $level->id, 'title' => $resource->title],
                [
                    'description' => $resource->description,
                    'file_path' => $filePath,
                    'file_size' => $fileSize,
                ]
            );
        }

        return back()->with('success', "تمت إضافة المورد الأكاديمي [{$resource->title}] إلى بنك المستوى بنجاح! 🚀");
    }

    // تعديل بيانات المورد
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->hasPermission('upload_level_resources') && ! $user->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل موارد المناهج.');
        }

        $resource = LevelResource::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:book,audio,video,worksheet,quiz,image',
            'course_section_id' => 'nullable|exists:course_sections,id',
            'description' => 'nullable|string',
            'external_url' => 'nullable|url|max:500',
            'duration_hours' => 'nullable|numeric|min:0.01|max:1000',
            'duration_minutes' => 'nullable|integer|min:1|max:60000',
            'exam_id' => 'nullable|exists:exams,id',
            'order_index' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'attachment' => 'nullable|file|max:524288', // حد أقصى 512 ميجابايت
        ]);

        $durationHours = $resource->duration_hours;
        $durationMinutes = $resource->duration_minutes;
        if ($request->filled('duration_hours')) {
            $durationHours = round((float) $request->input('duration_hours'), 2);
            $durationMinutes = (int) round($durationHours * 60);
        } elseif ($request->filled('duration_minutes')) {
            $durationMinutes = (int) $request->input('duration_minutes');
            $durationHours = round($durationMinutes / 60.0, 2);
        } elseif ($request->has('duration_hours') || $request->has('duration_minutes')) {
            $durationHours = null;
            $durationMinutes = null;
        }

        $data = [
            'title' => $validated['title'],
            'type' => $validated['type'],
            'course_section_id' => $request->has('course_section_id') ? $request->input('course_section_id') : $resource->course_section_id,
            'description' => $validated['description'] ?? null,
            'external_url' => $validated['external_url'] ?? null,
            'duration_hours' => $durationHours,
            'duration_minutes' => $durationMinutes,
            'exam_id' => $validated['exam_id'] ?? null,
            'order_index' => $validated['order_index'] ?? $resource->order_index,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $resource->is_active,
        ];

        if ($request->hasFile('attachment')) {
            // حذف الملف القديم إن وجد
            if ($resource->file_path && file_exists(public_path($resource->file_path))) {
                @unlink(public_path($resource->file_path));
            }

            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $sizeBytes = $file->getSize();

            if ($sizeBytes >= 1048576) {
                $fileSize = round($sizeBytes / 1048576, 1).' MB';
            } else {
                $fileSize = round($sizeBytes / 1024, 0).' KB';
            }

            $uniqueName = 'lvl_'.$resource->level_id.'_'.time().'_'.Str::slug(pathinfo($originalName, PATHINFO_FILENAME)).'.'.$extension;
            $destinationPath = public_path('uploads/level_resources');
            $file->move($destinationPath, $uniqueName);

            $data['file_path'] = 'uploads/level_resources/'.$uniqueName;
            $data['file_name'] = $originalName;
            $data['file_size'] = $fileSize;
        }

        $resource->update($data);

        return back()->with('success', "تم تحديث بيانات المورد الأكاديمي [{$resource->title}] بنجاح.");
    }

    // حذف مورد من بنك المستوى
    public function destroy($id)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->hasPermission('upload_level_resources') && ! $user->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية حذف موارد المناهج.');
        }

        $resource = LevelResource::findOrFail($id);
        $title = $resource->title;

        if ($resource->file_path && file_exists(public_path($resource->file_path))) {
            @unlink(public_path($resource->file_path));
        }

        $resource->delete();

        return back()->with('success', "تم حذف المورد [{$title}] من بنك المستوى بنجاح.");
    }

    // مشاركة واستدعاء المورد لداخل المجموعة الدراسية يومياً (Day-by-Day Drip)
    public function shareToGroup(Request $request, $groupId = null)
    {
        $groupId = $groupId ?: $request->input('study_group_id');
        $resourceId = $request->input('resource_id') ?: $request->input('level_resource_id') ?: $request->route('resourceId');

        $request->validate([
            'study_group_id' => 'nullable|exists:study_groups,id',
            'resource_id' => 'nullable|exists:level_resources,id',
            'level_resource_id' => 'nullable|exists:level_resources,id',
            'custom_instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $group = StudyGroup::with(['teachers', 'supervisors'])->findOrFail($groupId);
        $resource = LevelResource::findOrFail($resourceId);
        $user = auth()->user();

        // التحقق من الصلاحيات: مدير أو مشرف أو أي معلم للمجموعة أو من يمتلك صلاحية share_group_resources
        $canShare = $user->isAdmin()
            || $user->id === $group->teacher_id
            || $user->id === $group->supervisor_id
            || $group->teachers->contains('id', $user->id)
            || $group->supervisors->contains('id', $user->id)
            || $user->hasPermission('share_group_resources');

        if (! $canShare) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية مشاركة الموارد في هذه المجموعة الدراسية.');
        }

        // فحص هل تمت مشاركته مسبقاً في هذه المجموعة لتجنب التكرار غير المقصود
        $existing = GroupActivity::where('study_group_id', $group->id)
            ->where('level_resource_id', $resource->id)
            ->first();

        $instructions = $request->custom_instructions ?: $resource->description;

        if ($existing) {
            // تحديث التعليمات أو تاريخ التسليم
            $existing->update([
                'user_id' => $user->id,
                'instructions' => $instructions,
                'external_url' => $resource->external_url,
                'file_path' => $resource->file_path ?: $existing->file_path,
                'file_name' => $resource->file_name ?: $existing->file_name,
                'file_size' => $resource->file_size ?: $existing->file_size,
                'due_date' => $request->due_date ?: $existing->due_date,
            ]);

            return back()->with('success', "تم تحديث مشاركة المورد [{$resource->title}] في مجموعة [{$group->name}] بنجاح! 🚀");
        }

        // إنشاء النشاط في المجموعة استناداً إلى المورد المركزي
        GroupActivity::create([
            'study_group_id' => $group->id,
            'level_resource_id' => $resource->id,
            'user_id' => $user->id,
            'title' => $resource->title,
            'type' => $resource->type,
            'instructions' => $instructions,
            'file_path' => $resource->file_path,
            'file_name' => $resource->file_name,
            'file_size' => $resource->file_size,
            'external_url' => $resource->external_url,
            'due_date' => $request->due_date ?? null,
        ]);

        return back()->with('success', "تمت مشاركة [{$resource->title}] بنجاح مع طلاب مجموعة [{$group->name}]! ستظهر فوراً في جدول أنشطتهم وبوابة الطالب.");
    }

    // إلغاء مشاركة مورد من مجموعة
    public function unshareFromGroup($resourceId, $groupId)
    {
        $user = auth()->user();
        $group = StudyGroup::findOrFail($groupId);

        $canUnshare = $user->isAdmin()
            || $user->id === $group->teacher_id
            || $user->id === $group->supervisor_id
            || $user->hasPermission('share_group_resources');

        if (! $canUnshare) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إلغاء مشاركة هذا المورد.');
        }

        $activity = GroupActivity::where('study_group_id', $groupId)
            ->where('level_resource_id', $resourceId)
            ->first();

        if ($activity) {
            $activity->delete();

            return back()->with('success', 'تمت إزالة المورد من هذه المجموعة بنجاح.');
        }

        return back()->with('error', 'هذا المورد غير منشور في هذه المجموعة.');
    }

    // إضافة عنوان / يوم دراسي / وحدة جديدة للدورة (Course Section / Topic / Day)
    public function storeSection(Request $request, $courseId)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية إضافة أقسام وعناوين للدورة.');
        }

        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_index' => 'nullable|integer|min:1',
        ]);

        $nextOrder = (CourseSection::where('course_id', $course->id)->max('order_index') ?? 0) + 1;

        CourseSection::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order_index' => $validated['order_index'] ?? $nextOrder,
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        return back()->with('success', "تمت إضافة العنوان / اليوم الدراسي [{$validated['title']}] للدورة بنجاح! 📌");
    }

    // تعديل عنوان / يوم دراسي
    public function updateSection(Request $request, $id)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية تعديل أقسام الدورة.');
        }

        $section = CourseSection::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_index' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $section->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order_index' => $validated['order_index'] ?? $section->order_index,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $section->is_active,
        ]);

        return back()->with('success', "تم تحديث بيانات العنوان [{$section->title}] بنجاح.");
    }

    // حذف عنوان / يوم دراسي (إذا كان فارغاً)
    public function destroySection($id)
    {
        $user = auth()->user();
        if (! $user->isAdmin() && ! $user->hasPermission('manage_courses')) {
            return back()->with('error', 'عذراً، ليس لديك صلاحية حذف أقسام الدورة.');
        }

        $section = CourseSection::withCount('resources')->findOrFail($id);

        if ($section->resources_count > 0) {
            return back()->with('error', "لا يمكن حذف العنوان [{$section->title}] لأنه يحتوي على ({$section->resources_count}) من الموارد التعليمية. يرجى نقل الموارد أو حذفها أولاً.");
        }

        $title = $section->title;
        $section->delete();

        return back()->with('success', "تم حذف العنوان [{$title}] بنجاح.");
    }

    // بث وتشغيل ملف المورد (فيديو، صوت، مستند، صورة) مع دعم Range Requests لتشغيل وسلاسة الفيديو
    public function streamResource($id)
    {
        $resource = LevelResource::findOrFail($id);

        if ($resource->external_url && ! $resource->file_path) {
            return redirect()->away($resource->external_url);
        }

        $cleanPath = ltrim(str_replace('\\', '/', $resource->file_path ?? ''), '/');

        $candidatePaths = [
            public_path($cleanPath),
            base_path('public/'.$cleanPath),
            base_path($cleanPath),
            public_path('uploads/level_resources/'.basename($cleanPath)),
            storage_path('app/public/'.$cleanPath),
        ];

        $foundPath = null;
        foreach ($candidatePaths as $p) {
            if (! empty($p) && file_exists($p) && ! is_dir($p)) {
                $foundPath = $p;
                break;
            }
        }

        if (! $foundPath) {
            if ($resource->external_url) {
                return redirect()->away($resource->external_url);
            }

            return response()->view('errors.custom_file_not_found', [
                'title' => 'ملف المورد التعليمي غير متوفر حالياً',
                'resourceTitle' => $resource->title,
                'filePath' => $resource->file_path,
                'message' => 'عذراً، ملف هذا المورد لم يتم العثور عليه على الخادم، قد يكون لم يكتمل رفعه بعد أو تم حذفه.',
                'backUrl' => url()->previous(),
            ], 404);
        }

        $mimeType = mime_content_type($foundPath) ?: 'application/octet-stream';
        $lowerPath = strtolower($foundPath);
        if (str_ends_with($lowerPath, '.mp4')) {
            $mimeType = 'video/mp4';
        } elseif (str_ends_with($lowerPath, '.webm')) {
            $mimeType = 'video/webm';
        } elseif (str_ends_with($lowerPath, '.mp3')) {
            $mimeType = 'audio/mpeg';
        } elseif (str_ends_with($lowerPath, '.pdf')) {
            $mimeType = 'application/pdf';
        }

        return response()->file($foundPath, [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => 'inline; filename="'.($resource->file_name ?: basename($foundPath)).'"',
        ]);
    }

    // تحميل ملف المورد مباشرة إلى الهاتف أو الكمبيوتر
    public function downloadResource($id)
    {
        $resource = LevelResource::findOrFail($id);

        if ($resource->external_url && ! $resource->file_path) {
            return redirect()->away($resource->external_url);
        }

        $cleanPath = ltrim(str_replace('\\', '/', $resource->file_path ?? ''), '/');

        $candidatePaths = [
            public_path($cleanPath),
            base_path('public/'.$cleanPath),
            base_path($cleanPath),
            public_path('uploads/level_resources/'.basename($cleanPath)),
            storage_path('app/public/'.$cleanPath),
        ];

        $foundPath = null;
        foreach ($candidatePaths as $p) {
            if (! empty($p) && file_exists($p) && ! is_dir($p)) {
                $foundPath = $p;
                break;
            }
        }

        if (! $foundPath) {
            if ($resource->external_url) {
                return redirect()->away($resource->external_url);
            }

            return back()->with('error', "عذراً، الملف المطلوب غير متوفر على الخادم (المسار: {$resource->file_path}).");
        }

        $downloadName = $resource->file_name ?: ($resource->title.'.'.pathinfo($foundPath, PATHINFO_EXTENSION));

        return response()->download($foundPath, $downloadName);
    }
}
