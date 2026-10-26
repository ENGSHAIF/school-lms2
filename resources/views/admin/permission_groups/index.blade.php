@extends('layouts.app', ['title' => 'إدارة مجموعات الصلاحيات والشاشات - ASOTS'])

@section('content')
<div class="space-y-6">

    <!-- =============================================================== -->
    <!-- 1. الماستر العلوي (Master Header Card) -->
    <!-- =============================================================== -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 sm:p-7 relative overflow-hidden">
        <div class="absolute -top-12 -left-12 w-48 h-48 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5 relative z-10">
            <!-- بيانات المجموعة الرئيسية (Master Info) -->
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shadow-sm border {{ $selectedGroup->isAdminGroup() ? 'bg-rose-50 border-rose-200 text-rose-600' : 'bg-indigo-50 border-indigo-200 text-indigo-600' }}">
                    {{ $selectedGroup->icon ?? '🛡️' }}
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="font-mono text-xs font-black px-2.5 py-0.5 rounded-lg bg-slate-900 text-amber-400 border border-slate-800 shadow-xs">
                            #GRP-{{ str_pad($selectedGroup->id, 3, '0', STR_PAD_LEFT) }}
                        </span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
                            {{ $selectedGroup->name }}
                        </h1>
                        @if($selectedGroup->name_en)
                            <span class="text-xs text-slate-400 font-mono" dir="ltr">({{ $selectedGroup->name_en }})</span>
                        @endif

                        @if($selectedGroup->isAdminGroup())
                            <span class="text-xs font-black bg-rose-600 text-white px-3 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                <span>👑</span>
                                <span>مدير النظام (كامل الصلاحيات 100% تلقائياً)</span>
                            </span>
                        @elseif($selectedGroup->is_system)
                            <span class="text-xs font-bold bg-slate-200 text-slate-700 px-2.5 py-0.5 rounded-full">
                                🔒 مجموعة قياسية
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                        {{ $selectedGroup->description ?? 'التحكم الدقيق بكافة أزرار وشاشات وعمليات وتقارير النظام الخاصة بهذه المجموعة.' }}
                    </p>

                    <!-- إحصائيات سريعة للماستر -->
                    <div class="flex items-center gap-4 mt-3 text-xs flex-wrap">
                        <span class="flex items-center gap-1.5 font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-xl">
                            <span>👥</span>
                            <span>المستخدمون المسكنون:</span>
                            <span class="font-mono font-black text-indigo-700">{{ $selectedGroup->users_count }} موظف</span>
                        </span>

                        @php
                            $totalOperations = 0;
                            foreach ($allSystemPermissions as $scr) {
                                $totalOperations += count($scr['permissions']);
                            }
                            $activeCount = $selectedGroup->isAdminGroup() ? $totalOperations : (is_array($selectedGroup->permissions) ? count($selectedGroup->permissions) : 0);
                            $activePercent = $totalOperations > 0 ? round(($activeCount / $totalOperations) * 100) : 0;
                        @endphp

                        <span class="flex items-center gap-1.5 font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-xl">
                            <span>⚡</span>
                            <span>العمليات الممنوحة:</span>
                            <span class="font-mono font-black text-emerald-600">{{ $activeCount }} من {{ $totalOperations }}</span>
                            <span class="text-[10px] text-slate-400">({{ $activePercent }}%)</span>
                        </span>

                        <span class="font-mono text-[11px] bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded-lg">
                            رمز المجموعة: {{ $selectedGroup->slug }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- محول المجموعات السريع + أزرار الإجراءات -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full lg:w-auto shrink-0">
                <!-- اختيار مجموعة أخرى للتنقل السريع -->
                <div class="relative">
                    <select onchange="window.location.href = '{{ route('admin.permission_groups.index') }}?group_id=' + this.value" 
                        class="w-full sm:w-56 text-xs font-bold rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-3 focus:ring-2 focus:ring-indigo-500 cursor-pointer shadow-xs">
                        @foreach($groups as $grp)
                            <option value="{{ $grp->id }}" {{ $selectedGroup->id === $grp->id ? 'selected' : '' }}>
                                {{ $grp->icon ?? '🛡️' }} {{ $grp->name }} ({{ $grp->users_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="button" onclick="openCreateGroupModal()" class="px-3.5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                    <span>➕</span>
                    <span>مجموعة جديدة</span>
                </button>

                @if(!$selectedGroup->isAdminGroup())
                    <button type="button" onclick="document.getElementById('permissionsMasterForm').submit()" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                        <span>💾</span>
                        <span>حفظ التعديلات</span>
                    </button>
                @endif
            </div>
        </div>

        @if($selectedGroup->isAdminGroup())
            <div class="mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 flex items-center gap-3 text-xs text-rose-900">
                <span class="text-xl">👑</span>
                <span class="font-bold">
                    <strong>تنبيه الإدارة:</strong> مجموعة مدير النظام تمتلك كافة الصلاحيات على جميع الشاشات والأزرار والتقارير تلقائياً وبشكل دائم. أي شاشة أو عملية جديدة تُضاف إلى النظام تكون متاحة لهذه المجموعة فوراً بدون أي تعديل.
                </span>
            </div>
        @endif
    </div>

    <!-- =============================================================== -->
    <!-- 2. التبويبات العامة الرئيسية (Master Categories Tabs) -->
    <!-- =============================================================== -->
    <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-none py-1">
            @foreach($systemCategories as $catKey => $cat)
                <button type="button" 
                    onclick="switchMainCategory('{{ $catKey }}')" 
                    id="catBtn_{{ $catKey }}" 
                    class="main-cat-btn px-4 py-2.5 rounded-xl text-xs font-black transition flex items-center gap-2 whitespace-nowrap {{ $loop->first ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span class="text-base">{{ $cat['icon'] }}</span>
                    <span>{{ $cat['title'] }}</span>
                </button>
            @endforeach

            <!-- تبويب المستخدمين المسكنين في المجموعة -->
            <button type="button" 
                onclick="switchMainCategory('assigned_users')" 
                id="catBtn_assigned_users" 
                class="main-cat-btn px-4 py-2.5 rounded-xl text-xs font-black transition flex items-center gap-2 whitespace-nowrap bg-slate-100 text-slate-600 hover:bg-slate-200">
                <span>👥</span>
                <span>المستخدمون المسكنون بالمجموعة</span>
                <span class="font-mono text-[10px] px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">{{ $selectedGroup->users_count }}</span>
            </button>
        </div>

        <!-- أزرار سريعة للتحكم الشامل -->
        @if(!$selectedGroup->isAdminGroup())
            <div class="flex items-center gap-2 px-2">
                <button type="button" onclick="toggleAllSystemPermissions(true)" class="text-[11px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-3 py-1.5 rounded-xl transition">
                    ✓ تحديد الكل (كامل النظام)
                </button>
                <button type="button" onclick="toggleAllSystemPermissions(false)" class="text-[11px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 px-3 py-1.5 rounded-xl transition">
                    ✕ إلغاء تحديد الكل
                </button>
            </div>
        @endif
    </div>

    <!-- =============================================================== -->
    <!-- 3. النموذج المركزي لحفظ كافة الصلاحيات (Form Container) -->
    <!-- =============================================================== -->
    <form id="permissionsMasterForm" method="POST" action="{{ route('admin.permission_groups.update', $selectedGroup->id) }}">
        @csrf
        <input type="hidden" name="name" value="{{ $selectedGroup->name }}">
        <input type="hidden" name="name_en" value="{{ $selectedGroup->name_en }}">
        <input type="hidden" name="description" value="{{ $selectedGroup->description }}">

        @php
            $groupPerms = $selectedGroup->permissions ?? [];
            $isAdmin = $selectedGroup->isAdminGroup();
        @endphp

        <!-- =========================================================== -->
        <!-- قسم صلاحيات الشاشات (Master-Detail: القائمة باليمين + المحتوى بالوسط) -->
        <!-- =========================================================== -->
        <div id="categoryContainer_screens" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- العمود الأيمن: قائمة الشاشات (Sidebar of Screens - 4 أعمدة) -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden sticky top-20">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <span class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span>📱</span>
                        <span>قائمة الشاشات والوحدات</span>
                    </span>
                    <span id="screensCountBadge" class="text-[11px] font-mono text-slate-500 font-bold"></span>
                </div>

                <div class="p-2 space-y-1 max-h-[620px] overflow-y-auto" id="screensListSidebar">
                    @foreach($allSystemPermissions as $screenKey => $screen)
                        @php
                            $scrPerms = $screen['permissions'];
                            $scrTotal = count($scrPerms);
                            $scrActive = 0;
                            foreach ($scrPerms as $pk => $pv) {
                                if ($isAdmin || in_array('*', $groupPerms, true) || in_array($pk, $groupPerms, true)) {
                                    $scrActive++;
                                }
                            }
                            $isAllChecked = ($scrActive === $scrTotal);
                        @endphp
                        <button type="button" 
                            onclick="selectScreen('{{ $screenKey }}')" 
                            id="sidebarScreenBtn_{{ $screenKey }}" 
                            data-category="{{ $screen['category'] ?? 'academic' }}"
                            class="screen-sidebar-item w-full p-3 rounded-2xl text-right transition-all flex items-center justify-between border {{ $loop->first ? 'bg-indigo-50/80 border-indigo-200 text-indigo-950 shadow-xs' : 'bg-white border-transparent hover:bg-slate-50 text-slate-700' }}">
                            
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="text-lg shrink-0">{{ $screen['icon'] ?? '📁' }}</span>
                                <div class="truncate text-right">
                                    <span class="font-black text-xs block leading-tight truncate">{{ $screen['title'] }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono block mt-0.5 truncate">{{ $screenKey }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($isAllChecked)
                                    <span class="w-2 h-2 rounded-full bg-emerald-500" title="جميع الأزرار مفعلة"></span>
                                @endif
                                <span id="screenCountPill_{{ $screenKey }}" class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full {{ $isAllChecked ? 'bg-emerald-100 text-emerald-800' : ($scrActive > 0 ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-500') }}">
                                    {{ $scrActive }}/{{ $scrTotal }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- الوسط: تفاصيل الشاشة المختارة وأزرارها وعملياتها (8 أعمدة) -->
            <div class="lg:col-span-8 space-y-4">
                @foreach($allSystemPermissions as $screenKey => $screen)
                    @php
                        $scrPerms = $screen['permissions'];
                        $scrTotal = count($scrPerms);
                        $scrActive = 0;
                        foreach ($scrPerms as $pk => $pv) {
                            if ($isAdmin || in_array('*', $groupPerms, true) || in_array($pk, $groupPerms, true)) {
                                $scrActive++;
                            }
                        }
                    @endphp

                    <div id="screenDetailPanel_{{ $screenKey }}" 
                         data-category="{{ $screen['category'] ?? 'academic' }}"
                         class="screen-detail-panel bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden {{ $loop->first ? '' : 'hidden' }}">
                        
                        <!-- رأس الشاشة المختارة -->
                        <div class="p-6 bg-slate-50/70 border-b border-slate-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-2xl flex items-center justify-center shadow-xs">
                                        {{ $screen['icon'] ?? '📁' }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-base font-black text-slate-900">{{ $screen['title'] }}</h3>
                                            <span class="text-[10px] font-mono text-slate-400 bg-slate-200 px-2 py-0.5 rounded-md">{{ $screenKey }}</span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $screen['desc'] ?? 'حدد الصلاحيات والأزرار المسموح بها لهذه المجموعة في هذه الشاشة.' }}</p>
                                    </div>
                                </div>

                                <!-- أزرار التحكم السريعة للشاشة الحالية -->
                                @if(!$isAdmin)
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" onclick="toggleScreenCheckboxes('{{ $screenKey }}', true)" class="text-xs font-bold text-indigo-700 bg-white hover:bg-indigo-50 border border-slate-300 px-3 py-1.5 rounded-xl transition shadow-xs flex items-center gap-1">
                                            <span>✓</span>
                                            <span>تحديد كل أزرار الشاشة</span>
                                        </button>
                                        <button type="button" onclick="toggleScreenCheckboxes('{{ $screenKey }}', false)" class="text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 border border-slate-300 px-3 py-1.5 rounded-xl transition shadow-xs flex items-center gap-1">
                                            <span>✕</span>
                                            <span>إلغاء التحديد</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- شبكة الأزرار والعمليات الخاصة بهذه الشاشة -->
                        <div class="p-6">
                            <div class="mb-3 flex justify-between items-center text-xs text-slate-500 font-bold">
                                <span>الأزرار والعمليات المتاحة في هذه الشاشة ({{ $scrTotal }} زر وعملية):</span>
                                <span class="font-mono text-indigo-600">انقر على أي مربع لتفعيل الصلاحية فوراً</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                @foreach($screen['permissions'] as $opKey => $op)
                                    @php
                                        $isOpChecked = $isAdmin || in_array('*', $groupPerms, true) || in_array($opKey, $groupPerms, true);
                                        
                                        // ألوان وتنسيقات البادجات حسب نوع الزر
                                        $badgeBg = match($op['type'] ?? 'view') {
                                            'create' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'edit' => 'bg-amber-100 text-amber-800 border-amber-300',
                                            'delete' => 'bg-rose-100 text-rose-800 border-rose-300',
                                            'live' => 'bg-purple-100 text-purple-800 border-purple-300',
                                            'print' => 'bg-cyan-100 text-cyan-800 border-cyan-300',
                                            'pricing' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                            'security' => 'bg-pink-100 text-pink-800 border-pink-300',
                                            'journal', 'funds' => 'bg-teal-100 text-teal-800 border-teal-300',
                                            default => 'bg-slate-100 text-slate-800 border-slate-300',
                                        };
                                    @endphp

                                    <label class="op-card flex items-start gap-3.5 p-4 rounded-2xl border transition-all cursor-pointer select-none {{ $isOpChecked ? 'bg-indigo-50/50 border-indigo-300 shadow-2xs ring-1 ring-indigo-200' : 'bg-slate-50/40 border-slate-200 hover:bg-slate-50 hover:border-slate-300' }}">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $opKey }}" 
                                               data-screen="{{ $screenKey }}"
                                               class="op-checkbox mt-1 w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                               {{ $isOpChecked ? 'checked' : '' }}
                                               {{ $isAdmin ? 'disabled' : '' }}
                                               onchange="updateScreenCounter('{{ $screenKey }}')">

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1 mb-1">
                                                <span class="text-xs font-black text-slate-900 leading-snug">{{ $op['label'] }}</span>
                                                <span class="text-[9px] font-black px-2 py-0.5 rounded-md border shrink-0 {{ $badgeBg }}">
                                                    {{ $op['badge'] ?? 'عملية' }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ $op['desc'] }}</p>
                                            <span class="text-[9px] font-mono text-slate-400 mt-1.5 block tracking-tight" dir="ltr">{{ $opKey }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                    </div>
                @endforeach

                <!-- زر الحفظ الدائم أسفل الشاشات -->
                @if(!$isAdmin)
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-3">
                        <span class="text-xs text-slate-500 font-bold">
                            تذكر حفظ الصلاحيات لتطبيقها فوراً على كافة الموظفين التابعين لهذه المجموعة.
                        </span>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                            <span>💾</span>
                            <span>حفظ وتطبيق صلاحيات الشاشات الآن</span>
                        </button>
                    </div>
                @endif
            </div>

        </div>
    </form>

    <!-- =============================================================== -->
    <!-- 4. تبويب المستخدمين المسكنين بالمجموعة (Assigned Users) -->
    <!-- =============================================================== -->
    <div id="categoryContainer_assigned_users" class="hidden bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-base font-black text-slate-900">المستخدمون المسكنون في [{{ $selectedGroup->name }}]</h3>
                <p class="text-xs text-slate-500">يحصل هؤلاء المستخدمون على صلاحيات هذه المجموعة فوراً وتلقائياً.</p>
            </div>

            <button type="button" onclick="openAssignUsersModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5">
                <span>➕</span>
                <span>إضافة / نقل مستخدم لهذه المجموعة</span>
            </button>
        </div>

        <!-- جدول المستخدمين -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3"># الكود</th>
                        <th class="p-3">المستخدم والبريد</th>
                        <th class="p-3">الدور الأساسي</th>
                        <th class="p-3">رقم الهاتف</th>
                        <th class="p-3">الحالة</th>
                        <th class="p-3 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($groupUsers as $u)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-mono font-bold text-indigo-700 text-[11px]">
                                {{ $u->user_code ?? $u->student_code ?? ('USR-' . $u->id) }}
                            </td>
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-slate-800 text-amber-400 font-bold flex items-center justify-center text-xs">
                                        {{ mb_substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $u->name }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono block" dir="ltr">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $u->role === 'admin' ? 'مدير نظام' : ($u->role === 'supervisor' ? 'مشرف أكاديمي' : ($u->role === 'teacher' ? 'معلم' : ($u->role === 'accountant' ? 'محاسب' : $u->role))) }}
                                </span>
                            </td>
                            <td class="p-3 font-mono text-[11px]" dir="ltr">{{ $u->phone_whatsapp }}</td>
                            <td class="p-3">
                                @if($u->status === 'active')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">نشط 🟢</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ $u->status }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <form method="POST" action="{{ route('admin.permission_groups.remove_user', [$selectedGroup->id, $u->id]) }}" onsubmit="return confirm('هل أنت متأكد من فك ارتباط المستخدم من هذه المجموعة؟')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-bold rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 transition" title="إزالة من المجموعة">
                                        فك الارتباط ✕
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <span>لا يوجد مستخدمون مسكنون في هذه المجموعة حالياً. انقر على "إضافة / نقل مستخدم لهذه المجموعة" لإضافة كوادر وموظفين.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $groupUsers->links() }}
        </div>
    </div>

</div>

<!-- مودال إنشاء مجموعة صلاحيات جديدة -->
<div id="createGroupModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden animate-pop">
        <div class="p-5 bg-gradient-to-r from-indigo-900 to-purple-900 text-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xl">🛡️</span>
                <h3 class="text-sm font-black">إنشاء مجموعة صلاحيات وأدوار جديدة</h3>
            </div>
            <button type="button" onclick="closeCreateGroupModal()" class="text-white/80 hover:text-white text-lg">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.permission_groups.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">اسم المجموعة <span class="text-rose-500">*</span></label>
                <input type="text" name="name" placeholder="مثال: مدراء الفروع، خدمة العملاء..." required class="w-full text-xs rounded-xl border-slate-200 p-2.5 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">الاسم بالإنجليزي (اختياري)</label>
                <input type="text" name="name_en" placeholder="e.g. Branch Managers" class="w-full text-xs rounded-xl border-slate-200 p-2.5 focus:ring-2 focus:ring-indigo-500" dir="ltr">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">وصف واختصاص المجموعة</label>
                <textarea name="description" rows="2" placeholder="وصف موجز للمهام الموكلة لأعضاء هذه المجموعة..." class="w-full text-xs rounded-xl border-slate-200 p-2.5 focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">الرمز التعبيري (الأيقونة)</label>
                    <input type="text" name="icon" value="🛡️" class="w-full text-xs rounded-xl border-slate-200 p-2.5 text-center text-lg">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">لون البادج المميز</label>
                    <select name="color" class="w-full text-xs rounded-xl border-slate-200 p-2.5">
                        <option value="indigo">أرجواني (Indigo)</option>
                        <option value="purple">بنفسجي (Purple)</option>
                        <option value="blue">أزرق (Blue)</option>
                        <option value="emerald">أخضر (Emerald)</option>
                        <option value="amber">كهرماني (Amber)</option>
                        <option value="teal">فيروزي (Teal)</option>
                        <option value="rose">وردي (Rose)</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeCreateGroupModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition">
                    إنشاء المجموعة والبدء بتحديد صلاحياتها ➕
                </button>
            </div>
        </form>
    </div>
</div>

<!-- مودال إضافة وتسكين مستخدمين في المجموعة -->
@if($selectedGroup)
<div id="assignUsersModal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden animate-pop">
        <div class="p-5 bg-gradient-to-r from-slate-900 to-indigo-900 text-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xl">👥</span>
                <h3 class="text-sm font-black">إضافة مستخدمين إلى [{{ $selectedGroup->name }}]</h3>
            </div>
            <button type="button" onclick="closeAssignUsersModal()" class="text-white/80 hover:text-white text-lg">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.permission_groups.assign_users', $selectedGroup->id) }}" class="p-6 space-y-4">
            @csrf

            <p class="text-xs text-slate-500">
                اختر مستخدماً أو عدة مستخدمين من القائمة لنقلهم إلى هذه المجموعة وتفعيل صلاحياتها عليهم فوراً:
            </p>

            <div class="max-h-60 overflow-y-auto space-y-2 border border-slate-200 p-3 rounded-2xl">
                @forelse($availableUsers as $u)
                    <label class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 cursor-pointer border border-transparent hover:border-slate-200 transition">
                        <div class="flex items-center gap-2.5">
                            <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block leading-tight">{{ $u->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono" dir="ltr">{{ $u->email }}</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                            {{ $u->role }}
                        </span>
                    </label>
                @empty
                    <div class="p-4 text-center text-xs text-slate-400">
                        جميع الكوادر مسكنون بالفعل في هذه المجموعة.
                    </div>
                @endforelse
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeAssignUsersModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md transition">
                    إضافة المحددين للمجموعة ✅
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
let currentActiveCategory = 'academic';

// 1. التبديل بين التبويبات العامة (أكاديمي، مالي، تقارير، نظام، مستخدمون)
function switchMainCategory(catKey) {
    currentActiveCategory = catKey;

    // تحديث أزرار التبويبات
    document.querySelectorAll('.main-cat-btn').forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
        btn.classList.add('bg-slate-100', 'text-slate-600');
    });
    const activeBtn = document.getElementById('catBtn_' + catKey);
    if (activeBtn) {
        activeBtn.classList.remove('bg-slate-100', 'text-slate-600');
        activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
    }

    if (catKey === 'assigned_users') {
        document.getElementById('categoryContainer_screens').classList.add('hidden');
        document.getElementById('categoryContainer_assigned_users').classList.remove('hidden');
    } else {
        document.getElementById('categoryContainer_screens').classList.remove('hidden');
        document.getElementById('categoryContainer_assigned_users').classList.add('hidden');

        // فلترة عناصر الشريط الجانبي للشاشات حسب التبويب المختار
        let firstVisibleScreen = null;
        let countVisible = 0;

        document.querySelectorAll('.screen-sidebar-item').forEach(item => {
            const itemCat = item.getAttribute('data-category');
            if (itemCat === catKey) {
                item.classList.remove('hidden');
                countVisible++;
                if (!firstVisibleScreen) {
                    firstVisibleScreen = item.id.replace('sidebarScreenBtn_', '');
                }
            } else {
                item.classList.add('hidden');
            }
        });

        const badge = document.getElementById('screensCountBadge');
        if (badge) {
            badge.innerText = countVisible + ' شاشة';
        }

        if (firstVisibleScreen) {
            selectScreen(firstVisibleScreen);
        }
    }
}

// 2. تحديد الشاشة من القائمة باليمين وإظهار محتواها بالوسط
function selectScreen(screenKey) {
    // تحديث أزرار القائمة الجانبية
    document.querySelectorAll('.screen-sidebar-item').forEach(btn => {
        btn.classList.remove('bg-indigo-50/80', 'border-indigo-200', 'text-indigo-950', 'shadow-xs');
        btn.classList.add('bg-white', 'border-transparent', 'text-slate-700');
    });

    const activeSidebarBtn = document.getElementById('sidebarScreenBtn_' + screenKey);
    if (activeSidebarBtn) {
        activeSidebarBtn.classList.remove('bg-white', 'border-transparent', 'text-slate-700');
        activeSidebarBtn.classList.add('bg-indigo-50/80', 'border-indigo-200', 'text-indigo-950', 'shadow-xs');
    }

    // إخفاء كافة لوحات التفاصيل وإظهار لوحة الشاشة المحددة
    document.querySelectorAll('.screen-detail-panel').forEach(panel => {
        panel.classList.add('hidden');
    });

    const targetPanel = document.getElementById('screenDetailPanel_' + screenKey);
    if (targetPanel) {
        targetPanel.classList.remove('hidden');
    }
}

// 3. تحديد أو إلغاء تحديد كل أزرار الشاشة الحالية
function toggleScreenCheckboxes(screenKey, checked) {
    document.querySelectorAll(`.op-checkbox[data-screen="${screenKey}"]:not(:disabled)`).forEach(cb => {
        cb.checked = checked;
    });
    updateScreenCounter(screenKey);
}

// 4. تحديد أو إلغاء تحديد كل أزرار النظام
function toggleAllSystemPermissions(checked) {
    document.querySelectorAll('.op-checkbox:not(:disabled)').forEach(cb => {
        cb.checked = checked;
    });
    // تحديث كافة العدادات
    document.querySelectorAll('.screen-sidebar-item').forEach(item => {
        const key = item.id.replace('sidebarScreenBtn_', '');
        updateScreenCounter(key);
    });
}

// 5. تحديث عداد الصلاحيات للشاشة
function updateScreenCounter(screenKey) {
    const checkboxes = document.querySelectorAll(`.op-checkbox[data-screen="${screenKey}"]`);
    const total = checkboxes.length;
    let checkedCount = 0;
    checkboxes.forEach(cb => {
        if (cb.checked) checkedCount++;
        // تحديث تنسيق البطاقة
        const card = cb.closest('.op-card');
        if (card) {
            if (cb.checked) {
                card.classList.add('bg-indigo-50/50', 'border-indigo-300', 'shadow-2xs', 'ring-1', 'ring-indigo-200');
                card.classList.remove('bg-slate-50/40', 'border-slate-200');
            } else {
                card.classList.remove('bg-indigo-50/50', 'border-indigo-300', 'shadow-2xs', 'ring-1', 'ring-indigo-200');
                card.classList.add('bg-slate-50/40', 'border-slate-200');
            }
        }
    });

    const pill = document.getElementById('screenCountPill_' + screenKey);
    if (pill) {
        pill.innerText = `${checkedCount}/${total}`;
        if (checkedCount === total && total > 0) {
            pill.className = 'text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
        } else if (checkedCount > 0) {
            pill.className = 'text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800';
        } else {
            pill.className = 'text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500';
        }
    }
}

function openCreateGroupModal() {
    document.getElementById('createGroupModal').classList.remove('hidden');
}

function closeCreateGroupModal() {
    document.getElementById('createGroupModal').classList.add('hidden');
}

function openAssignUsersModal() {
    const el = document.getElementById('assignUsersModal');
    if (el) el.classList.remove('hidden');
}

function closeAssignUsersModal() {
    const el = document.getElementById('assignUsersModal');
    if (el) el.classList.add('hidden');
}

// تشغيل الفلترة الأولية عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
    switchMainCategory('academic');
});
</script>
@endsection
