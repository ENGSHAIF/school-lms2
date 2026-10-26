<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    // عرض قائمة البانرات ولوحة التحكم
    public function index()
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'غير مصرح لك بإدارة المحتوى البصري والإعلانات.');
        }

        $banners = Banner::orderBy('sort_order')->latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    // إضافة بانر أو إعلان جديد
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'غير مصرح لك بإضافة إعلانات.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'link_url' => 'required|string|max:255',
            'button_text' => 'required|string|max:100',
            'placement' => 'required|string|in:top_announcement,hero_slider,popup_modal,sidebar',
            'theme_style' => 'required|string|in:amber,indigo,emerald,rose',
            'device_target' => 'required|string|in:all,desktop,mobile',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'nullable|integer',
        ]);

        Banner::create([
            'title' => $validated['title'],
            'title_en' => $validated['title_en'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'icon' => $validated['icon'] ?? '⭐',
            'link_url' => $validated['link_url'],
            'button_text' => $validated['button_text'],
            'placement' => $validated['placement'],
            'theme_style' => $validated['theme_style'],
            'device_target' => $validated['device_target'],
            'starts_at' => $validated['starts_at'] ?? now(),
            'ends_at' => $validated['ends_at'] ?? now()->addDays(30),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء الإعلان والبانر الترويجي بنجاح ونشره في الموقع.');
    }

    // تفعيل أو تعطيل البانر بنقرة واحدة
    public function toggle($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'غير مصرح لك بتعديل حالة الإعلانات.');
        }

        $banner = Banner::findOrFail($id);
        $banner->update([
            'is_active' => !$banner->is_active,
        ]);

        $statusText = $banner->is_active ? 'تفعيل' : 'إيقاف';
        return back()->with('success', "تم {$statusText} البانر [{$banner->title}] بنجاح.");
    }

    // حذف البانر
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isSupervisor()) {
            abort(403, 'غير مصرح لك بحذف الإعلانات.');
        }

        $banner = Banner::findOrFail($id);
        $banner->delete();

        return back()->with('success', 'تم حذف البانر بنجاح.');
    }
}
