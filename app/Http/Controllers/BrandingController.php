<?php

namespace App\Http\Controllers;

use App\Models\PlatformLicense;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BrandingController extends Controller
{
    // عرض لوحة إدارة الهوية البصرية وخلفيات وشعارات ونصوص الموقع والتراخيص الرسمية
    public function index()
    {
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403, 'غير مصرح لك بإدارة الهوية البصرية وخلفية الموقع.');
        }

        $settings = SiteSetting::allCached();
        $licenses = PlatformLicense::orderBy('sort_order')->latest()->get();

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

        return view('admin.branding.index', compact('settings', 'presets', 'licenses'));
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
            $fileName = 'bg_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/branding');

            if (! file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            // حذف الملف القديم إن وجد
            $oldBg = SiteSetting::get('site_background');
            if ($oldBg && str_starts_with($oldBg, 'uploads/branding/') && file_exists(public_path($oldBg))) {
                @unlink(public_path($oldBg));
            }

            $file->move($destination, $fileName);
            $bgUrl = 'uploads/branding/'.$fileName;
        } elseif (! empty($request->preset_url)) {
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
        $fileName = 'logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
        $destination = public_path('uploads/branding');

        if (! file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $oldLogo = SiteSetting::get('site_logo');
        if ($oldLogo && str_starts_with($oldLogo, 'uploads/branding/') && file_exists(public_path($oldLogo))) {
            @unlink(public_path($oldLogo));
        }

        $file->move($destination, $fileName);
        $logoPath = 'uploads/branding/'.$fileName;

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
            'whatsapp_number_2' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:100',
            'session_duration_value' => 'nullable|string|max:50',
            'session_duration_label' => 'nullable|string|max:100',
            'stats_sessions_value' => 'nullable|string|max:50',
            'stats_satisfaction_value' => 'nullable|string|max:50',
            'stats_ai_value' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $k => $val) {
            SiteSetting::set($k, (string) $val, 'texts');
        }

        if (! empty($validated['whatsapp_number'])) {
            SiteSetting::set('whatsapp_platform_number', (string) $validated['whatsapp_number'], 'whatsapp');
        }

        if (isset($validated['whatsapp_number_2'])) {
            SiteSetting::set('whatsapp_number_2', (string) $validated['whatsapp_number_2'], 'texts');
        }

        return back()->with('success', 'تم حفظ وتحديث نصوص وشعارات المنصة بنجاح! 📝');
    }

    // 6. تحديث محتويات وسيرة وبيانات القيادة الأكاديمية
    public function updateLeadership(Request $request)
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'عذراً، تعديل بيانات وسيرة القيادة الأكاديمية مخصص لمدير النظام فقط.');
        }

        $validated = $request->validate([
            'leadership_name_ar' => 'required|string|max:190',
            'leadership_name_en' => 'nullable|string|max:190',
            'leadership_title_ar' => 'required|string|max:190',
            'leadership_title_en' => 'nullable|string|max:190',
            'leadership_role_ar' => 'required|string|max:255',
            'leadership_role_en' => 'nullable|string|max:255',
            'leadership_years_badge' => 'nullable|string|max:100',
            'leadership_direct_phone_1' => 'nullable|string|max:50',
            'leadership_direct_phone_2' => 'nullable|string|max:50',
            'leadership_bio_summary_ar' => 'required|string',
            'leadership_vision_statement_ar' => 'nullable|string',
            'leadership_specialties_text' => 'nullable|string',
        ]);

        foreach ($validated as $k => $val) {
            SiteSetting::set($k, (string) $val, 'leadership');
        }

        return back()->with('success', 'تم حفظ وتحديث بيانات وسيرة القيادة الأكاديمية بنجاح! 🎓');
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

    // 4. رفع وإرفاق وثيقة ترخيص رسمية جديدة (خاص بمدير النظام)
    public function storeLicense(Request $request)
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'عذراً، رفع وإدارة التراخيص مخصصة لمدير النظام فقط.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'issuing_authority' => 'nullable|string|max:255',
            'license_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:12288',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('license_image');
        $fileName = 'license_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
        $destination = public_path('uploads/licenses');

        if (! file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $file->move($destination, $fileName);
        $imagePath = 'uploads/licenses/'.$fileName;

        PlatformLicense::create([
            'title' => $request->title,
            'license_number' => $request->license_number,
            'issuing_authority' => $request->issuing_authority,
            'image_path' => $imagePath,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'notes' => $request->notes,
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        return back()->with('success', 'تم رفع وإرفاق وثيقة الترخيص بنجاح، وهي الآن جاهزة في العارض الآمن المحمي.');
    }

    // 5. تبديل حالة ظهور الترخيص (نشط / مخفي)
    public function toggleLicenseStatus($id)
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'عذراً، هذه الصلاحية لمدير النظام فقط.');
        }

        $license = PlatformLicense::findOrFail($id);
        $license->status = ($license->status === 'active') ? 'hidden' : 'active';
        $license->save();

        $statusMsg = ($license->status === 'active') ? 'تم تنشيط وإظهار الترخيص' : 'تم إخفاء الترخيص مؤقتاً';

        return back()->with('success', $statusMsg);
    }

    // 6. حذف وثيقة ترخيص رسمية
    public function destroyLicense($id)
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'عذراً، حذف التراخيص مقتصر على مدير النظام.');
        }

        $license = PlatformLicense::findOrFail($id);
        if ($license->image_path && file_exists(public_path($license->image_path))) {
            @unlink(public_path($license->image_path));
        }
        $license->delete();

        return back()->with('success', 'تم حذف وثيقة الترخيص بنجاح.');
    }

    // 7. استعراض التراخيص بالعارض الرقمي الآمن فائق الحماية (صلاحية مشروطة مع حماية مانعة للتحميل والنسخ والتصوير)
    public function viewLicenses(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول بحساب معتمد للاطلاع على وثائق التراخيص والاعتمادات الرسمية.');
        }

        if (! $user->isAdmin() && ! $user->hasPermission('system.licenses_view') && ! $user->hasPermission('view_licenses')) {
            return redirect()->route('home')->with('error', 'عذراً، ليس لديك الصلاحية المعتمدة لاستعراض وثائق وتراخيص المنصة.');
        }

        $licenses = PlatformLicense::active()->orderBy('sort_order')->latest()->get();

        return view('public.licenses_viewer', compact('licenses'));
    }
}
