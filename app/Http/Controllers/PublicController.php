<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Course;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;

use App\Models\Testimonial;

class PublicController extends Controller
{
    // 1. الصفحة الرئيسية الشاملة (11 قسماً معيارياً)
    public function home()
    {
        $info = config('asots');
        $packages = Package::where('is_active', true)->take(4)->get();
        $levels = Level::with('courses')->where('is_active', true)->orderBy('level_order')->get();
        $courses = Course::with('level')->where('is_active', true)->get();
        $teachers = $info['featured_teachers'];
        $testimonials = Testimonial::where('is_approved', true)->latest()->get();

        if ($testimonials->isEmpty() && !empty($info['testimonials'])) {
            $testimonials = collect($info['testimonials'])->map(function ($t) {
                return (object) [
                    'name' => $t['name'],
                    'role' => $t['role'],
                    'user_type' => 'student',
                    'avatar' => $t['avatar'],
                    'title' => null,
                    'content' => $t['text'],
                    'score' => $t['score'],
                    'rating' => 5,
                    'stars_string' => '⭐⭐⭐⭐⭐',
                    'user_type_label' => 'طالب / ولي أمر 🌟',
                    'badge_class' => 'bg-indigo-500/20 text-indigo-300 border-indigo-400/30',
                ];
            });
        }

        return view('public.home', compact('info', 'packages', 'courses', 'teachers', 'testimonials'));
    }

    // نشر تجربة أو مقال جديد من المستخدم (طالب، ولي أمر، أو معلم)
    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:10|max:2000',
            'title' => 'nullable|string|max:190',
            'rating' => 'required|integer|min:1|max:5',
            'score' => 'nullable|string|max:100',
        ], [
            'content.required' => 'يرجى كتابة تفاصيل مقالك ورأيك وتجربتك في المنصة.',
            'content.min' => 'يرجى كتابة مقال واضح لا يقل عن 10 أحرف.',
            'rating.required' => 'يرجى تحديد تقييمك بالنجوم (1 إلى 5).',
        ]);

        $user = auth()->user();

        $name = $user ? $user->name : ($request->input('name') ?: 'زائر ومستفيد من المنصة');
        $userType = $user ? $user->role : 'student';

        $role = match ($userType) {
            'parent' => 'ولي أمر في المنصة',
            'student' => 'طالب في المنصة',
            'teacher' => 'معلم لغات في المنصة',
            'supervisor' => 'مشرف أكاديمي',
            default => 'مشترك في المنصة',
        };

        if ($user && $user->isParent() && $user->children->isNotEmpty()) {
            $role = 'ولي أمر (' . $user->children->first()->name . ')';
        }

        $avatar = match ($userType) {
            'parent' => ($user && $user->gender === 'female') ? '👩‍👧‍👦' : '👨‍👧‍👦',
            'teacher' => ($user && $user->gender === 'female') ? '👩‍🏫' : '👨‍🏫',
            default => ($user && $user->gender === 'female') ? '👧' : '🎓',
        };

        Testimonial::create([
            'user_id' => $user?->id,
            'name' => $name,
            'role' => $role,
            'user_type' => $userType,
            'avatar' => $avatar,
            'title' => $validated['title'] ?? 'تجربتي ومقالي عن المنصة',
            'content' => $validated['content'],
            'score' => $validated['score'] ?: ($validated['rating'] . ' نجوم ⭐'),
            'rating' => (int)$validated['rating'],
            'is_approved' => true,
            'is_featured' => true,
        ]);

        return back()->with('success', 'شكراً لمشاركتك! تم نشر مقالك وتجربتك بنجاح، وستتحرك الآن دورياً في شريط التجارب الملهمة بالصفحة الرئيسية! 🎉');
    }

    // 2. القيادة الأكاديمية (Academic Leadership) - أ.د. فخر الدين الصوفي
    public function leadership()
    {
        $info = config('asots');
        return view('public.leadership', compact('info'));
    }

    // 3. الجودة والتميز وشؤون الطلاب (AQED)
    public function quality()
    {
        $info = config('asots');
        return view('public.quality', compact('info'));
    }

    // 4. خدمات الترجمة والترجمة الشفوية (Translation Services)
    public function translationServices()
    {
        $info = config('asots');
        return view('public.translation_services', compact('info'));
    }

    // 5. الاستشارات الأكاديمية والتربوية والمهنية (Consultancy Services)
    public function consultancyServices()
    {
        $info = config('asots');
        return view('public.consultancy_services', compact('info'));
    }

    // 6. صفحة المناهج الدراسية وخريطة المستويات CEFR
    public function curriculum()
    {
        $info = config('asots');
        $levels = Level::with(['courses' => fn($q) => $q->where('is_active', true)->orderBy('course_order')])
            ->where('is_active', true)
            ->orderBy('level_order')
            ->get();
        $courses = Course::with('level')->where('is_active', true)->get();
        return view('public.curriculum', compact('info', 'levels', 'courses'));
    }

    // 7. صفحة إنجليزية الكبار والتوفل ورجال الأعمال
    public function adultEnglish()
    {
        $info = config('asots');
        $packages = Package::where('is_active', true)->get();
        return view('public.adult_english', compact('info', 'packages'));
    }

    // 8. صفحة الأسعار والباقات وسندات القبض المعتمدة
    public function pricing()
    {
        $info = config('asots');
        $packages = Package::where('is_active', true)->get();
        return view('public.pricing', compact('info', 'packages'));
    }

    // 9. عن ASOTS ورؤيته وفلسفته
    public function about()
    {
        $info = config('asots');
        return view('public.about', compact('info'));
    }

    // 10. مجالات العمل والخدمات
    public function services()
    {
        $info = config('asots');
        return view('public.services', compact('info'));
    }

    // 11. التواصل والاستفسار
    public function contact()
    {
        $info = config('asots');
        return view('public.contact', compact('info'));
    }

    // 12. برنامج الإطلاق الأول: اللغة الإنجليزية العامة (Section 10 Blueprint)
    public function generalEnglish()
    {
        $info = config('asots');
        $packages = Package::where('is_active', true)->get();
        return view('public.general_english', compact('info', 'packages'));
    }

    // 13. السياسات والشروط والنزاهة المؤسسية (Section 14 Blueprint)
    public function policies()
    {
        $info = config('asots');
        return view('public.policies', compact('info'));
    }

    // 14. نظام التحقق الرقمي من صحة الشهادات (Digital Certificate Verification)
    public function verifyCertificate(Request $request)
    {
        $info = config('asots');
        $code = trim($request->input('code', ''));
        $certData = null;

        if ($code) {
            // تحقق تجريبي معتمد برقم الشهادة
            if (strtoupper($code) === 'ASOTS-2026-DEMO' || strtoupper($code) === 'ASOTS-ENG-101') {
                $certData = [
                    'valid' => true,
                    'code' => strtoupper($code),
                    'student_name' => 'طالب تجريبي (مسار الإنجليزية العامة)',
                    'course_name' => 'General English Programme (Interchange Level 2)',
                    'grade' => '94% (ممتاز مرتفع - A+)',
                    'issue_date' => '2026-10-01',
                    'issuer' => 'قسم الجودة والتميز AQED — إشراف أ.د. فخر الدين الصوفي',
                    'status' => 'معتمدة وموثقة رسمياً في سجلات المنصة',
                ];
            } else {
                $certData = [
                    'valid' => false,
                    'code' => $code,
                    'message' => 'الرقم المرجعي المدخل غير مسجل في قاعدة بيانات شهادات ASOTS أو انتهت صلاحيته.',
                ];
            }
        }

        return view('public.verify_certificate', compact('info', 'code', 'certData'));
    }
}
