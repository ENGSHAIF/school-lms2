<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Support\Str;

class PermissionGroupController extends Controller
{
    /**
     * التحقق من صلاحية الوصول لإدارة مجموعات الصلاحيات
     */
    protected function authorizeAccess()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }
        if (!$user->isAdmin() && !$user->hasPermission('system.groups_manage')) {
            abort(403, 'عذراً، ليس لديك صلاحية إدارة مجموعات الصلاحيات والأدوار.');
        }
    }

    /**
     * عرض جميع مجموعات الصلاحيات والأدوار في النظام
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $selectedGroupId = $request->query('group_id');
        $search = $request->query('search');

        $groups = PermissionGroup::withCount('users')->latest('is_system')->oldest('id')->get();

        $selectedGroup = null;
        $groupUsers = collect();

        if ($selectedGroupId) {
            $selectedGroup = PermissionGroup::withCount('users')->find($selectedGroupId);
        }

        // إذا لم يُحدد مجموعة، نحدد أول مجموعة تلقائياً
        if (!$selectedGroup && $groups->isNotEmpty()) {
            $selectedGroup = $groups->first();
        }

        if ($selectedGroup) {
            $usersQuery = User::where('permission_group_id', $selectedGroup->id)->latest();
            if ($search) {
                $usersQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone_whatsapp', 'like', "%{$search}%")
                      ->orWhere('user_code', 'like', "%{$search}%");
                });
            }
            $groupUsers = $usersQuery->paginate(15)->withQueryString();
        }

        $allSystemPermissions = PermissionGroup::allSystemPermissions();
        $systemCategories = PermissionGroup::systemCategories();

        // قائمة المستخدمين غير المنتمين لهذه المجموعة لإمكانية إضافتهم
        $availableUsers = User::where(function ($q) use ($selectedGroup) {
            if ($selectedGroup) {
                $q->whereNull('permission_group_id')
                  ->orWhere('permission_group_id', '!=', $selectedGroup->id);
            }
        })->whereIn('role', ['admin', 'supervisor', 'teacher', 'accountant'])->limit(100)->get();

        return view('admin.permission_groups.index', compact(
            'groups',
            'selectedGroup',
            'groupUsers',
            'allSystemPermissions',
            'systemCategories',
            'availableUsers',
            'search'
        ));
    }

    /**
     * إنشاء مجموعة صلاحيات جديدة مخصصة
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|in:indigo,purple,blue,emerald,amber,rose,teal,slate',
            'icon' => 'nullable|string|max:10',
            'permissions' => 'nullable|array',
        ]);

        $slugBase = Str::slug($validated['name_en'] ?? $validated['name']);
        if (empty($slugBase)) {
            $slugBase = 'group';
        }
        $slug = $slugBase;
        $counter = 1;
        while (PermissionGroup::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter++;
        }

        $group = PermissionGroup::create([
            'name' => $validated['name'],
            'name_en' => $validated['name_en'] ?? null,
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? 'indigo',
            'icon' => $validated['icon'] ?? '🛡️',
            'is_system' => false,
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return redirect()->route('admin.permission_groups.index', ['group_id' => $group->id])
            ->with('success', "تم إنشاء مجموعة الصلاحيات [{$group->name}] بنجاح وتحديد الصلاحيات المخصصة لها! 🛡️");
    }

    /**
     * تحديث بيانات وصلاحيات المجموعة
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAccess();

        $group = PermissionGroup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|in:indigo,purple,blue,emerald,amber,rose,teal,slate',
            'icon' => 'nullable|string|max:10',
            'permissions' => 'nullable|array',
        ]);

        $data = [
            'name' => $validated['name'],
            'name_en' => $validated['name_en'] ?? null,
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? $group->color,
            'icon' => $validated['icon'] ?? $group->icon,
        ];

        // حماية مجموعة مدير النظام الأساسية: صلاحياتها غير قابلة للتعديل أو التقليص
        if ($group->isAdminGroup()) {
            $data['permissions'] = ['*'];
        } else {
            $data['permissions'] = $validated['permissions'] ?? [];
        }

        $group->update($data);

        return redirect()->route('admin.permission_groups.index', ['group_id' => $group->id])
            ->with('success', "تم تحديث بيانات وصلاحيات مجموعة [{$group->name}] بنجاح! ✅");
    }

    /**
     * حذف مجموعة صلاحيات مخصصة
     */
    public function destroy($id)
    {
        $this->authorizeAccess();

        $group = PermissionGroup::findOrFail($id);

        // منع حذف مجموعة مدير النظام الأساسية أو أي مجموعة نظام محمية
        if ($group->is_system || $group->isAdminGroup()) {
            return back()->with('error', 'عذراً، لا يمكن حذف مجموعة النظام الأساسية المحمية [مديرو النظام].');
        }

        // إلغاء ارتباط المستخدمين بالمجموعة المحذوفة
        User::where('permission_group_id', $group->id)->update(['permission_group_id' => null]);

        $name = $group->name;
        $group->delete();

        return redirect()->route('admin.permission_groups.index')
            ->with('success', "تم حذف مجموعة الصلاحيات [{$name}] بنجاح، وتم فك ارتباط المستخدمين التابعين لها.");
    }

    /**
     * إضافة / نقل مستخدم أو عدة مستخدمين إلى المجموعة
     */
    public function assignUsers(Request $request, $id)
    {
        $this->authorizeAccess();

        $group = PermissionGroup::findOrFail($id);

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ], [
            'user_ids.required' => 'يرجى اختيار مستخدم واحد على الأقل لإضافته إلى المجموعة.',
        ]);

        User::whereIn('id', $validated['user_ids'])->update([
            'permission_group_id' => $group->id,
        ]);

        $count = count($validated['user_ids']);

        return redirect()->route('admin.permission_groups.index', ['group_id' => $group->id])
            ->with('success', "تمت إضافة ({$count}) مستخدم بنجاح إلى مجموعة [{$group->name}] وتفعيل صلاحياتهم فوراً! 🎉");
    }

    /**
     * إزالة مستخدم من مجموعة الصلاحيات
     */
    public function removeUser(Request $request, $id, $userId)
    {
        $this->authorizeAccess();

        $group = PermissionGroup::findOrFail($id);
        $user = User::where('id', $userId)->where('permission_group_id', $group->id)->firstOrFail();

        // التحقق: إذا كان هو المدير الوحيد في مجموعة الأدمن، نمنع إزالته لحماية لوحة التحكم
        if ($group->isAdminGroup()) {
            $adminCount = User::where('permission_group_id', $group->id)->count();
            if ($adminCount <= 1 && $user->role === 'admin') {
                return back()->with('error', 'لا يمكن إزالة الحساب الإداري الوحيد من مجموعة مدير النظام.');
            }
        }

        $user->update(['permission_group_id' => null]);

        return redirect()->route('admin.permission_groups.index', ['group_id' => $group->id])
            ->with('success', "تمت إزالة المستخدم ({$user->name}) من مجموعة [{$group->name}].");
    }
}
