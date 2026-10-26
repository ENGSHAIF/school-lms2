<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BrandingController extends Controller
{
    // عرض لوحة إدارة الهوية البصرية وخلفيات وشعارات ونصوص الموقع
    public function index()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'غير مصرح لك بإدارة الهوية البصرية وخلفية الموقع.');
        }

        $settings = SiteSetting::allCached();

        // مكتبة خلفيات تعليمية جاهزة للاختيار الفوري
        $presets = [
            [
                'id' => 'preset_campus',
                'name' => 'حرم جامعي دولي متطور',
                'url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1920&auto=format&fit=crop',
                'thumb' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=400&auto=format&fit=crop',
            ],
            [
                'id' => 'preset_library',
                'name' => 'مكتبة أكاديمية فاخرة',
                'url' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=1920&auto=format&fit=crop',
                'thumb' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=400&auto=format&fit=crop',
            ],
            [
                'id' => 'preset_classroom',
                'name' => 'قاعة دراسية تفاعلية حية',
                'url' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=1920&auto=format&fit=crop',
                'thumb' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=400&auto=format&fit=crop',
            ],
            [
                'id' => 'preset_digital',
                'name' => 'تقنية وتعلّم رقمي وذكاء اصطناعي',
                'url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1920&auto=format&fit=crop',
                'thumb' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=400&auto=format&fit=crop',
            ],
            [
                'id' => 'preset_architecture',
                'name' => 'تصميم هندسي حديث هادئ',
                'url' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=1920&auto=format&fit=crop',
                'thumb' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=400&auto=format&fit=crop',
            ],
            [
                'id' => 'preset_future',
                'name' => 'تخرج وتمكين مستقبلي واعد',
                'url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1920&auto=format&fit=crop',
                'thumb' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=400&auto=format&fit=crop',
            ],
        ];

        return view('admin.branding.index', compact('settings', 'presets'));
    }

    // 1. تحديث أو رفع خلفية الموقع
    public function updateBackground(Request $request)
    {
        $request->validate([
            'background_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:10240',
            'preset_url' => 'nullable|string',
            'background_mode' => 'required|in:hero,body,all_pages',
            'overlay_opacity' => 'required|numeric|min:0.1|max:0.95',
            'background_blur' => 'required|in:none,sm,md',
        ]);

        $bgUrl = null;

        // في حال رفع ملف جديد
        if ($request->hasFile('background_file')) {
            $file = $request->file('background_file');
            $fileName = 'bg_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/branding');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            // حذف الملف القديم إن وجد
            $oldBg = SiteSetting::get('site_background');
            if ($oldBg && str_starts_with($oldBg, 'uploads/branding/') && file_exists(public_path($oldBg))) {
                @unlink(public_path($oldBg));
            }

            $file->move($destination, $fileName);
            $bgUrl = 'uploads/branding/' . $fileName;
        } elseif (!empty($request->preset_url)) {
            $bgUrl = $request->preset_url;
        }

        if ($bgUrl) {
            SiteSetting::set('site_background', $bgUrl, 'background');
            SiteSetting::set('site_background_enabled', '1', 'background');
        }

        SiteSetting::set('background_mode', $request->background_mode, 'background');
        SiteSetting::set('overlay_opacity', (string) $request->overlay_opacity, 'background');
        SiteSetting::set('background_blur', $request->background_blur, 'background');

        return back()->with('success', 'تم تطبيق خلفية الموقع وإعدادات التعتيم بنجاح! 🖼️');
    }

    // 2. إزالة خلفية الموقع والعودة للشكل الطبيعي الافتراضي
    public function removeBackground()
    {
        $oldBg = SiteSetting::get('site_background');
        if ($oldBg && str_starts_with($oldBg, 'uploads/branding/') && file_exists(public_path($oldBg))) {
            @unlink(public_path($oldBg));
        }

        SiteSetting::forget('site_background');
        SiteSetting::set('site_background_enabled', '0', 'background');

        return back()->with('success', 'تمت إزالة خلفية الموقع واستعادة الشكل الطبيعي الافتراضي للمنصة بنجاح! 🌿');
    }

    // 3. تحديث أو رفع شعار المنصة
    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo_file' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'logo_text' => 'nullable|string|max:100',
        ]);

        $file = $request->file('logo_file');
        $fileName = 'logo_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $destination = public_path('uploads/branding');

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $oldLogo = SiteSetting::get('site_logo');
        if ($oldLogo && str_starts_with($oldLogo, 'uploads/branding/') && file_exists(public_path($oldLogo))) {
            @unlink(public_path($oldLogo));
        }

        $file->move($destination, $fileName);
        $logoPath = 'uploads/branding/' . $fileName;

        SiteSetting::set('site_logo', $logoPath, 'branding');
        if ($request->has('logo_text')) {
            SiteSetting::set('logo_text', $request->logo_text, 'branding');
        }

        return back()->with('success', 'تم رفع وتحديث شعار المنصة بنجاح! ⭐');
    }

    // 4. إزالة الشعار المخصص والعودة للشعار الافتراضي
    public function removeLogo()
    {
        $oldLogo = SiteSetting::get('site_logo');
        if ($oldLogo && str_starts_with($oldLogo, 'uploads/branding/') && file_exists(public_path($oldLogo))) {
            @unlink(public_path($oldLogo));
        }

        SiteSetting::forget('site_logo');
        SiteSetting::forget('logo_text');

        return back()->with('success', 'تمت إزالة الشعار واستعادة الشعار المؤسسي الافتراضي للمنصة بنجاح.');
    }

    // 5. تحديث نصوص وشعارات المنصة الترويجية
    public function updateTexts(Request $request)
    {
        $validated = $request->validate([
            'site_name_ar' => 'required|string|max:190',
            'site_name_en' => 'nullable|string|max:190',
            'site_slogan_ar' => 'nullable|string|max:255',
            'hero_badge_text' => 'nullable|string|max:190',
            'hero_title_ar' => 'required|string|max:255',
            'hero_subtitle_ar' => 'nullable|string',
            'hero_cta_primary' => 'nullable|string|max:100',
            'hero_cta_secondary' => 'nullable|string|max:100',
            'whatsapp_number' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:100',
        ]);

        foreach ($validated as $k => $val) {
            SiteSetting::set($k, (string) $val, 'texts');
        }

        if (!empty($validated['whatsapp_number'])) {
            SiteSetting::set('whatsapp_platform_number', (string) $validated['whatsapp_number'], 'whatsapp');
        }

        return back()->with('success', 'تم حفظ وتحديث نصوص وشعارات المنصة بنجاح! 📝');
    }

    // 6. استعادة كافة النصوص والشعارات الافتراضية
    public function resetTexts()
    {
        $keys = [
            'site_name_ar',
            'site_name_en',
            'site_slogan_ar',
            'hero_badge_text',
            'hero_title_ar',
            'hero_subtitle_ar',
            'hero_cta_primary',
            'hero_cta_secondary',
            'whatsapp_number',
            'contact_email',
        ];

        foreach ($keys as $k) {
            SiteSetting::forget($k);
        }

        return back()->with('success', 'تمت استعادة كافة النصوص والشعارات الافتراضية الأصلية للمنصة.');
    }
}
