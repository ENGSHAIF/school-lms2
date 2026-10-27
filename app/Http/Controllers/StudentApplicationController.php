<?php

namespace App\Http\Controllers;

use App\Models\StudentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentApplicationController extends Controller
{
    // عرض صفحة استمارة التسجيل الرسمية للطلاب
    public function create()
    {
        $info = config('asots');

        return view('public.student_registration', compact('info'));
    }

    // استلام وحفظ استمارة التسجيل من المتقدم
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'educational_level' => 'nullable|string|max:255',
            'study_phone_whatsapp' => 'required|string|max:50',
            'parent_phone' => 'nullable|string|max:50',
            'needs_placement_test' => 'required|in:1,0,yes,no',
            'course_interest' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $needsPlacement = in_array($validated['needs_placement_test'], [1, '1', 'yes', 'true'], true);

        $application = StudentApplication::create([
            'name_ar' => trim($validated['name_ar']),
            'name_en' => ! empty($validated['name_en']) ? trim($validated['name_en']) : null,
            'birth_place' => ! empty($validated['birth_place']) ? trim($validated['birth_place']) : null,
            'birth_date' => $validated['birth_date'] ?? null,
            'educational_level' => ! empty($validated['educational_level']) ? trim($validated['educational_level']) : null,
            'study_phone_whatsapp' => trim($validated['study_phone_whatsapp']),
            'parent_phone' => ! empty($validated['parent_phone']) ? trim($validated['parent_phone']) : null,
            'needs_placement_test' => $needsPlacement,
            'course_interest' => $validated['course_interest'] ?? 'برنامج دبلوم اللغة الإنجليزية من الصفر حتى الاحتراف',
            'notes' => $validated['notes'] ?? null,
            'term' => 'Term 40',
            'program_duration' => 'شهر واحد — خمسة أيام أسبوعياً (من السبت إلى الأربعاء)',
            'status' => 'pending',
            'user_id' => Auth::id(),
        ]);

        // بناء نص الرسالة المنسق للواتساب
        $whatsappMessage = "مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات – ASOTS\n".
            "📋 إستمارة التسجيل (طلب رقم #{$application->id})\n\n".
            "* الاسم الكامل بالعربية: {$application->name_ar}\n".
            '* الاسم بالإنجليزية (مطابق للجواز): '.($application->name_en ?: 'غير محدد')."\n".
            '* مكان وتاريخ الميلاد: '.($application->birth_place ?: '—').' | '.($application->birth_date ? $application->birth_date->format('Y-m-d') : '—')."\n".
            '* المستوى التعليمي للطالب: '.($application->educational_level ?: '—')."\n".
            "* رقم الهاتف المخصص للدراسة (واتساب): {$application->study_phone_whatsapp}\n".
            '* رقم ولي الأمر: '.($application->parent_phone ?: '—')."\n".
            '* هل تحتاج لاختبار تحديد مستوى؟ '.($application->needs_placement_test ? 'نعم (✅)' : 'لا (❌)')."\n\n".
            "📑 بيانات الإدارة المقترحة:\n".
            "- Term: 40\n".
            "- مدة البرنامج: شهر واحد (خمسة أيام أسبوعياً من السبت إلى الأربعاء)\n\n".
            'شكراً لثقتكم بنا .. فأنتم بأعيننا 🤍';

        $cleanTargetPhone = '967735999101';
        $whatsappUrl = 'https://wa.me/'.$cleanTargetPhone.'?text='.urlencode($whatsappMessage);

        return redirect()->back()
            ->with('success', 'تم استلام استمارة تسجيلك بنجاح وسيقوم فريق القبول والتسجيل بمراجعتها فوراً! 🎉')
            ->with('application_id', $application->id)
            ->with('whatsapp_url', $whatsappUrl)
            ->with('application', $application);
    }

    // لوحة تحكم الإدارة: استعراض كافة استمارات الطلاب
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor()) {
            abort(403, 'غير مصرح لك باستعراض استمارات التسجيل.');
        }

        $query = StudentApplication::latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'LIKE', "%{$search}%")
                    ->orWhere('name_en', 'LIKE', "%{$search}%")
                    ->orWhere('study_phone_whatsapp', 'LIKE', "%{$search}%")
                    ->orWhere('academic_number', 'LIKE', "%{$search}%");
            });
        }

        $applications = $query->paginate(20)->withQueryString();
        $counts = [
            'all' => StudentApplication::count(),
            'pending' => StudentApplication::where('status', 'pending')->count(),
            'contacted' => StudentApplication::where('status', 'contacted')->count(),
            'approved' => StudentApplication::where('status', 'approved')->count(),
            'rejected' => StudentApplication::where('status', 'rejected')->count(),
        ];

        return view('admin.student_applications.index', compact('applications', 'counts'));
    }

    // تحديث بيانات الإدارة للاستمارة (الترم، الرقم الأكاديمي، الدورة، الحالة)
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor()) {
            abort(403, 'غير مصرح لك بتعديل بيانات الاستمارة.');
        }

        $application = StudentApplication::findOrFail($id);

        $validated = $request->validate([
            'term' => 'nullable|string|max:100',
            'academic_number' => 'nullable|string|max:100',
            'course_assigned' => 'nullable|string|max:255',
            'program_duration' => 'nullable|string|max:255',
            'status' => 'required|in:pending,contacted,approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $application->update([
            'term' => $validated['term'] ?? $application->term,
            'academic_number' => $validated['academic_number'] ?? $application->academic_number,
            'course_assigned' => $validated['course_assigned'] ?? $application->course_assigned,
            'program_duration' => $validated['program_duration'] ?? $application->program_duration,
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "تم تحديث بيانات الاستمارة للطالب [{$application->name_ar}] بنجاح.");
    }

    // حذف استمارة
    public function destroy($id)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor()) {
            abort(403, 'غير مصرح لك بحذف الاستمارات.');
        }

        $application = StudentApplication::findOrFail($id);
        $application->delete();

        return back()->with('success', 'تم حذف استمارة التسجيل بنجاح.');
    }

    // طباعة الاستمارة الرسمية
    public function print($id)
    {
        $application = StudentApplication::findOrFail($id);
        $info = config('asots');

        return view('admin.student_applications.print', compact('application', 'info'));
    }
}
