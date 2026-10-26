<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppearanceController extends Controller
{
    // عرض صفحة إعدادات المظهر والـ 10 قوالب والخطوط
    public function show()
    {
        $themes = config('themes.profiles');
        $defaultTheme = config('themes.default');

        $activeTheme = session('theme');
        if (!$activeTheme && Auth::check()) {
            $activeTheme = Auth::user()->theme_preference;
        }
        $activeTheme = $activeTheme ?? $defaultTheme;
        $activeFont = session('font', 'Cairo');

        return view('settings.appearance', compact('themes', 'activeTheme', 'defaultTheme', 'activeFont'));
    }

    // حفظ وتطبيق القالب المختار
    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|string',
        ]);

        $themeId = $request->input('theme');
        $themes = config('themes.profiles');

        if (!array_key_exists($themeId, $themes)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'القالب غير صالح'], 400);
            }
            return back()->with('error', 'القالب المختار غير صالح');
        }

        session(['theme' => $themeId]);

        if (Auth::check()) {
            try {
                Auth::user()->update([
                    'theme_preference' => $themeId,
                ]);
            } catch (\Exception $e) {
                // تجاهل إذا لم يكن الحقل متاحاً
            }
        }

        $themeName = $themes[$themeId]['name_ar'] ?? $themeId;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'theme' => $themes[$themeId],
                'message' => "تم تفعيل قالب ({$themeName}) بنجاح!",
            ]);
        }

        return back()->with('success', "تم تفعيل وتطبيق قالب ({$themeName}) بنجاح! 🎨");
    }

    // حفظ وتطبيق نوع الخط العربي المختار
    public function updateFont(Request $request)
    {
        $validFonts = ['Cairo', 'Almarai', 'Tajawal', 'IBM Plex Sans Arabic'];
        $font = $request->input('font', 'Cairo');

        if (!in_array($font, $validFonts)) {
            $font = 'Cairo';
        }

        session(['font' => $font]);

        return back()->with('success', "تم تطبيق خط المنصة إلى ({$font}) بنجاح! ✍️");
    }

    // إعادة ضبط المظهر الافتراضي
    public function reset()
    {
        $defaultTheme = config('themes.default');
        session(['theme' => $defaultTheme]);
        session(['font' => 'Cairo']);

        if (Auth::check()) {
            try {
                Auth::user()->update([
                    'theme_preference' => $defaultTheme,
                ]);
            } catch (\Exception $e) {}
        }

        return back()->with('success', 'تمت استعادة المظهر والخط الافتراضي للمنصة بنجاح.');
    }

    // تحويل اللغة بين العربية (RTL) والإنجليزية (LTR)
    public function switchLanguage($locale)
    {
        if (in_array($locale, ['ar', 'en'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);

            if (Auth::check()) {
                Auth::user()->update(['locale' => $locale]);
            }
        }

        return back();
    }
}
