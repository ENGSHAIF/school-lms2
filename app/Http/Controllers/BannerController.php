<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    // عرض قائمة البانرات ولوحة التحكم
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor()) {
            abort(403, 'غير مصرح لك بإدارة المحتوى البصري والإعلانات.');
        }

        $currentCategory = $request->query('category', 'all');

        $query = Banner::orderBy('sort_order')->latest();

        if ($currentCategory === 'ad') {
            $query->ads();
        } elseif ($currentCategory === 'event_gallery') {
            $query->events();
        }

        $banners = $query->get();

        $counts = [
            'total' => Banner::count(),
            'active' => Banner::where('is_active', true)->count(),
            'ads' => Banner::ads()->count(),
            'events' => Banner::events()->count(),
        ];

        return view('admin.banners.index', compact('banners', 'counts', 'currentCategory'));
    }

    // إضافة بانر أو إعلان جديد
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor()) {
            abort(403, 'غير مصرح لك بإضافة إعلانات.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'category' => 'required|string|in:ad,event_gallery',
            'event_date' => 'nullable|date',
            'event_location' => 'nullable|string|max:255',
            'batch_name' => 'nullable|string|max:100',
            'subtitle' => 'nullable|string',
            'content' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'link_url' => 'nullable|string|max:1000',
            'button_text' => 'nullable|string|max:100',
            'placement' => 'required|string|in:top_announcement,hero_slider,popup_modal,sidebar',
            'theme_style' => 'required|string|in:amber,indigo,emerald,rose',
            'device_target' => 'nullable|string|in:all,desktop,mobile',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $prefix = ($validated['category'] === 'event_gallery') ? 'event_' : 'ad_';
            $fileName = $prefix.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/banners');
            if (! file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $file->move($destination, $fileName);
            $imagePath = 'uploads/banners/'.$fileName;
        }

        $isEvent = ($validated['category'] === 'event_gallery');

        Banner::create([
            'title' => $validated['title'],
            'title_en' => $validated['title_en'] ?? null,
            'category' => $validated['category'] ?? 'ad',
            'event_date' => $validated['event_date'] ?? null,
            'event_location' => $validated['event_location'] ?? null,
            'batch_name' => $validated['batch_name'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'content' => $validated['content'] ?? null,
            'icon' => $validated['icon'] ?? ($isEvent ? '🎓' : '⭐'),
            'image_path' => $imagePath,
            'link_url' => ! empty($validated['link_url']) ? $validated['link_url'] : ($isEvent ? '#gallery' : '/onboarding'),
            'button_text' => ! empty($validated['button_text']) ? $validated['button_text'] : ($isEvent ? 'مشاهدة الألبوم 📸' : 'احجز حصتك الآن 🚀'),
            'placement' => $validated['placement'],
            'theme_style' => $validated['theme_style'],
            'device_target' => $validated['device_target'] ?? 'all',
            'starts_at' => $validated['starts_at'] ?? now(),
            'ends_at' => $validated['ends_at'] ?? now()->addDays(365),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        $successMsg = $isEvent
            ? 'تمت إضافة فعالية/حفلة التخرج بنجاح ونشر صورها في معرض الفعاليات والحفلات! 🎓'
            : 'تم إنشاء الإعلان والبانر الترويجي بنجاح ونشره في المنصة! 📢';

        return back()->with('success', $successMsg);
    }

    // تحديث بيانات إعلان وبانر موجود
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor()) {
            abort(403, 'غير مصرح لك بتعديل الإعلانات.');
        }

        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'category' => 'nullable|string|in:ad,event_gallery',
            'event_date' => 'nullable|date',
            'event_location' => 'nullable|string|max:255',
            'batch_name' => 'nullable|string|max:100',
            'subtitle' => 'nullable|string',
            'content' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'link_url' => 'nullable|string|max:1000',
            'button_text' => 'nullable|string|max:100',
            'placement' => 'required|string|in:top_announcement,hero_slider,popup_modal,sidebar',
            'theme_style' => 'required|string|in:amber,indigo,emerald,rose',
            'device_target' => 'nullable|string|in:all,desktop,mobile',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->boolean('remove_image')) {
            if ($banner->image_path && file_exists(public_path($banner->image_path))) {
                @unlink(public_path($banner->image_path));
            }
            $banner->image_path = null;
        } elseif ($request->hasFile('image')) {
            $file = $request->file('image');
            $prefix = (($validated['category'] ?? $banner->category) === 'event_gallery') ? 'event_' : 'ad_';
            $fileName = $prefix.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/banners');
            if (! file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            if ($banner->image_path && file_exists(public_path($banner->image_path))) {
                @unlink(public_path($banner->image_path));
            }
            $file->move($destination, $fileName);
            $banner->image_path = 'uploads/banners/'.$fileName;
        }

        $banner->title = $validated['title'];
        $banner->title_en = $validated['title_en'] ?? null;
        if (! empty($validated['category'])) {
            $banner->category = $validated['category'];
        }
        $banner->event_date = $validated['event_date'] ?? null;
        $banner->event_location = $validated['event_location'] ?? null;
        $banner->batch_name = $validated['batch_name'] ?? null;

        $banner->subtitle = $validated['subtitle'] ?? null;
        $banner->content = $validated['content'] ?? null;
        $banner->icon = $validated['icon'] ?? $banner->icon;
        if (! empty($validated['link_url'])) {
            $banner->link_url = $validated['link_url'];
        }
        if (! empty($validated['button_text'])) {
            $banner->button_text = $validated['button_text'];
        }
        $banner->placement = $validated['placement'];
        $banner->theme_style = $validated['theme_style'];
        $banner->device_target = $validated['device_target'] ?? $banner->device_target ?? 'all';
        $banner->starts_at = $validated['starts_at'] ?? $banner->starts_at;
        $banner->ends_at = $validated['ends_at'] ?? $banner->ends_at;
        if (isset($validated['sort_order'])) {
            $banner->sort_order = $validated['sort_order'];
        }
        if ($request->has('is_active')) {
            $banner->is_active = $request->boolean('is_active');
        }

        $banner->save();

        $msg = $banner->isEvent()
            ? 'تم تحديث بيانات فعالية/حفلة التخرج بنجاح! 🎓'
            : 'تم تحديث بيانات الإعلان بنجاح! ⭐';

        return back()->with('success', $msg);
    }

    // تفعيل أو تعطيل البانر بنقرة واحدة
    public function toggle($id)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor()) {
            abort(403, 'غير مصرح لك بتعديل حالة الإعلانات.');
        }

        $banner = Banner::findOrFail($id);
        $banner->update([
            'is_active' => ! $banner->is_active,
        ]);

        $statusText = $banner->is_active ? 'تفعيل' : 'إيقاف';

        return back()->with('success', "تم {$statusText} البانر [{$banner->title}] بنجاح.");
    }

    // حذف البانر
    public function destroy($id)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->isSupervisor()) {
            abort(403, 'غير مصرح لك بحذف الإعلانات.');
        }

        $banner = Banner::findOrFail($id);
        $banner->delete();

        return back()->with('success', 'تم حذف البانر بنجاح.');
    }
}
