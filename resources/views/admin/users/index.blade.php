@extends('layouts.app', ['title' => 'إدارة المستخدمين والصلاحيات - ASOTS'])

@section('content')
<div class="space-y-6">

    <!-- رأس الصفحة وأزرار التحكم العامة -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                    لوحة الإدارة والتحكم • User & Role Management
                </span>
                <span class="text-xs text-slate-400 font-mono">إجمالي المسجلين بالنظام: {{ $roleCounts['all'] }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900">إدارة المستخدمين وجداول الصلاحيات</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                جداول منفصلة ومخصصة لمستخدمي النظام، المعلمين، الطلاب، وأولياء الأمور لتصفح سريع وسلس مع إدارة دقيقة للصلاحيات.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('help.index') }}" title="دليل وشرح إدارة المستخدمين والصلاحيات" class="px-3.5 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-black rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                <span>📖</span>
                <span>دليل الشاشة</span>
            </a>
            <a href="{{ route('admin.courses.index') }}" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5">
                <span>📚</span>
                <span>إدارة الدورات والأسعار والباقات</span>
            </a>
            <a href="{{ route('accounting.dashboard') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5">
                <span>🧾</span>
                <span>السندات والمالية</span>
            </a>
            <button type="button" onclick="openAddModal()" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                <span>➕</span>
                <span>إضافة مستخدم جديد وتحديد صلاحياته</span>
            </button>
        </div>
    </div>

    <!-- التبويبات الأربعة المنفصلة (Staff / Teachers / Students / Parents) -->
    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        
        <!-- أزرار التبويبات -->
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto scrollbar-none py-1">
            <a href="{{ route('admin.users.index', ['tab' => 'staff']) }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $tab === 'staff' ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>👑</span>
                <span>مستخدمو النظام والإدارة</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'staff' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 font-mono' }}">{{ $roleCounts['staff'] }}</span>
            </a>

            <a href="{{ route('admin.users.index', ['tab' => 'teachers']) }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $tab === 'teachers' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>👨‍🏫</span>
                <span>الكادر التعليمي والمعلمون</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'teachers' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 font-mono' }}">{{ $roleCounts['teachers'] }}</span>
            </a>

            <a href="{{ route('admin.users.index', ['tab' => 'students']) }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $tab === 'students' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>🎓</span>
                <span>الطلاب المسجلون</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'students' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 font-mono' }}">{{ $roleCounts['students'] }}</span>
            </a>

            <a href="{{ route('admin.users.index', ['tab' => 'parents']) }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $tab === 'parents' ? 'bg-amber-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>👨‍👧‍👦</span>
                <span>أولياء الأمور</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'parents' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700 font-mono' }}">{{ $roleCounts['parents'] }}</span>
            </a>

            <a href="{{ route('admin.users.index', ['tab' => 'requests']) }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-2 {{ $tab === 'requests' ? 'bg-rose-600 text-white shadow-md' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' }}">
                <span>🔑</span>
                <span>طلبات استعادة كلمة المرور</span>
                @if(($pendingResetCount ?? 0) > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-600 text-white font-black animate-pulse shadow-sm">{{ $pendingResetCount }} جديد</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'requests' ? 'bg-white/20 text-white' : 'bg-rose-200 text-rose-800 font-mono' }}">{{ $roleCounts['requests'] ?? 0 }}</span>
                @endif
            </a>

            <a href="{{ route('admin.permission_groups.index') }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-black whitespace-nowrap transition flex items-center gap-2 bg-gradient-to-r from-purple-50 to-indigo-50 text-indigo-800 hover:from-purple-100 hover:to-indigo-100 border border-indigo-200 shadow-2xs">
                <span>🛡️</span>
                <span>مجموعات الصلاحيات والأدوار</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-600 text-white font-mono">{{ $permissionGroups->count() }}</span>
            </a>
        </div>


        <!-- حقل البحث السريع داخل التبويب الحالي -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 w-full md:w-auto">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="relative w-full md:w-72">
                <input type="text" name="search" value="{{ $search }}" placeholder="بحث بالاسم أو الهاتف أو الإيميل..." 
                    class="w-full pl-8 pr-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <span class="absolute left-2.5 top-2.5 text-slate-400 text-xs">🔍</span>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition whitespace-nowrap">
                بحث
            </button>
            @if($search)
                <a href="{{ route('admin.users.index', ['tab' => $tab]) }}" class="text-xs text-red-500 hover:underline whitespace-nowrap">إلغاء</a>
            @endif
        </form>
    </div>

    <!-- =============================================================== -->
    <!-- 1. جدول مستخدمي النظام والإدارة (Staff: Admin, Supervisor, Accountant) -->
    <!-- =============================================================== -->
    @if($tab === 'staff')
        @if(isset($reviewerUser) && $reviewerUser)
            <!-- بطاقة إدارة حساب الاستعراض والمراجعة المؤقت (Reviewer Mode) -->
            <div class="bg-gradient-to-br from-amber-50 via-white to-orange-50 border-2 {{ $reviewerUser->status === 'active' ? 'border-amber-300' : 'border-slate-300' }} rounded-3xl p-6 shadow-sm space-y-4">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl {{ $reviewerUser->status === 'active' ? 'bg-amber-500 text-slate-950' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-2xl font-black shadow-sm flex-shrink-0">
                            👁️
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-base font-black text-slate-900">حساب الاستعراض والدورة المستندية (Read-Only Reviewer)</h3>
                                @if($reviewerUser->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-black text-emerald-700 bg-emerald-100 px-3 py-0.5 rounded-full border border-emerald-300">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                        مفعّل وجاهز للاستعراض
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-black text-red-700 bg-red-100 px-3 py-0.5 rounded-full border border-red-300">
                                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                        موقوف حالياً (مغلق)
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 mt-0.5">
                                حساب مخصص للضيوف والمراجعين للاطلاع على كافة شاشات المنصة والدورة المستندية والتقارير المالية دون إمكانية تعديل أو حذف أي بيان.
                            </p>
                        </div>
                    </div>

                    <!-- زر الإيقاف / التفعيل المباشر -->
                    <form action="{{ route('admin.users.toggle_status', $reviewerUser->id) }}" method="POST">
                        @csrf
                        @if($reviewerUser->status === 'active')
                            <button type="submit" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black rounded-xl shadow transition flex items-center gap-2">
                                <span>⏸️</span>
                                <span>إيقاف الحساب الآن (تعطيل الدخول)</span>
                            </button>
                        @else
                            <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl shadow transition flex items-center gap-2">
                                <span>▶️</span>
                                <span>تفعيل الحساب الآن (تمكين الدخول)</span>
                            </button>
                        @endif
                    </form>
                </div>

                <!-- بيانات تسجيل الدخول السريعة لنسخها وإرسالها للمراجع -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-amber-200/60 text-xs">
                    <div class="bg-white/80 p-3 rounded-xl border border-amber-200 flex flex-col justify-between">
                        <span class="text-slate-500 text-[11px] font-bold">البريد الإلكتروني للدخول:</span>
                        <span class="font-mono font-black text-slate-800 text-xs mt-1" dir="ltr">{{ $reviewerUser->email }}</span>
                    </div>
                    <div class="bg-white/80 p-3 rounded-xl border border-amber-200 flex flex-col justify-between">
                        <span class="text-slate-500 text-[11px] font-bold">رمز المستخدم (كود الدخول):</span>
                        <span class="font-mono font-black text-slate-800 text-xs mt-1" dir="ltr">{{ $reviewerUser->user_code }}</span>
                    </div>
                    <div class="bg-white/80 p-3 rounded-xl border border-amber-200 flex flex-col justify-between">
                        <span class="text-slate-500 text-[11px] font-bold">كلمة المرور الافتراضية:</span>
                        <span class="font-mono font-black text-indigo-700 text-xs mt-1" dir="ltr">Viewer@2026</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-base font-black text-slate-900">سجل مستخدمي النظام والإدارة والصلاحيات</h3>
                    <p class="text-xs text-slate-500">حسابات المديرين، المشرفين الأكاديميين، والمحاسبين الماليين مع بيان صلاحياتهم النشطة.</p>
                </div>
                <span class="text-xs font-bold font-mono text-slate-500">{{ $users->total() }} موظف ومستخدم نظام</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-4"># المعرف</th>
                            <th class="p-4">الموظف والبريد الإلكتروني</th>
                            <th class="p-4">رقم الواتساب</th>
                            <th class="p-4">الدور الوظيفي</th>
                            <th class="p-4">الصلاحيات الممنوحة</th>
                            <th class="p-4">حالة الحساب</th>
                            <th class="p-4 text-center">التحكم والإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4">
                                    <div class="flex flex-col gap-1">
                                        <button type="button" onclick="copyUserCode('{{ $user->user_code }}')" 
                                            class="font-mono text-[11px] font-black text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded-lg w-max flex items-center gap-1 transition" title="انقر لنسخ كود الدخول">
                                            <span>🆔</span>
                                            <span>{{ $user->user_code ?? ('USR-' . $user->id) }}</span>
                                            <span class="text-[9px] text-indigo-400">📋</span>
                                        </button>
                                        <span class="text-[10px] text-slate-400 font-mono">#{{ $user->id }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-800 text-amber-400 font-black flex items-center justify-center shrink-0 text-sm shadow-sm">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-black text-slate-900 block leading-tight">{{ $user->name }}</span>
                                                <span class="text-xs" title="{{ $user->gender_label }}">{{ $user->gender_icon }}</span>
                                            </div>
                                            @if($user->name_en)
                                                <span class="text-[11px] text-slate-500 font-medium block" dir="ltr">{{ $user->name_en }}</span>
                                            @endif
                                            <span class="text-[11px] text-slate-400 font-mono block" dir="ltr">{{ $user->email }}</span>
                                            @if($user->birth_date)
                                                <span class="text-[10px] text-slate-400 block">🎂 {{ $user->birth_date->format('Y-m-d') }} ({{ $user->age }} سنة)</span>
                                            @endif
                                            @if($user->must_change_password)
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-amber-800 bg-amber-100 border border-amber-300 px-1.5 py-0.5 rounded mt-0.5">
                                                    ⚠️ ملزم بتغيير كلمة المرور
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-mono text-slate-700" dir="ltr">
                                    {{ $user->phone_whatsapp ?? '-' }}
                                </td>
                                <td class="p-4">
                                    @if($user->isReviewer())
                                        <span class="px-3 py-1 rounded-full text-[11px] font-black bg-amber-500 text-slate-950 border border-amber-600 flex items-center gap-1 w-max">
                                            <span>👁️</span> حساب استعراض ومراجعة
                                        </span>
                                    @elseif($user->role === 'admin')
                                        <span class="px-3 py-1 rounded-full text-[11px] font-black bg-slate-900 text-amber-400 border border-slate-800 flex items-center gap-1 w-max">
                                            <span>👑</span> مدير نظام (Full Admin)
                                        </span>
                                    @elseif($user->role === 'supervisor')
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200 flex items-center gap-1 w-max">
                                            <span>🛡️</span> مشرف أكاديمي
                                        </span>
                                    @elseif($user->role === 'accountant')
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1 w-max">
                                            <span>💼</span> محاسب مالي
                                        </span>
                                    @endif
                                    <span class="text-[10px] text-slate-400 block mt-1 font-bold">{{ $user->gender_label }}</span>
                                </td>
                                <td class="p-4">
                                    @if($user->isReviewer())
                                        <span class="text-[11px] font-black text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-300 flex items-center gap-1 w-max">
                                            <span>👁️</span>
                                            <span>استعراض شامل مقيد (Read-Only)</span>
                                        </span>
                                    @elseif($user->role === 'admin' || optional($user->permissionGroup)->isAdminGroup())
                                        <span class="text-[11px] font-black text-rose-800 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200 flex items-center gap-1 w-max">
                                            <span>👑</span>
                                            <span>كامل صلاحيات النظام تلقائياً 100%</span>
                                        </span>
                                    @elseif($user->permissionGroup)
                                        <a href="{{ route('admin.permission_groups.index', ['group_id' => $user->permission_group_id]) }}" 
                                           class="px-2.5 py-1 rounded-lg text-[11px] font-black border flex items-center gap-1.5 w-max transition shadow-xs {{ $user->permissionGroup->getBadgeClass() }}" title="عرض وتعديل صلاحيات المجموعة">
                                            <span>{{ $user->permissionGroup->icon ?? '🛡️' }}</span>
                                            <span>{{ $user->permissionGroup->name }}</span>
                                            <span class="opacity-75 font-mono text-[10px]">({{ is_array($user->permissionGroup->permissions) ? count($user->permissionGroup->permissions) : 0 }} صلاحية)</span>
                                        </a>
                                    @else
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @php
                                                $userPerms = is_array($user->permissions) ? array_keys(array_filter($user->permissions)) : [];
                                            @endphp
                                            @forelse(array_slice($userPerms, 0, 3) as $permKey)
                                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[10px] font-bold border border-slate-200">
                                                    {{ $permKey }}
                                                </span>
                                            @empty
                                                <span class="text-[11px] text-slate-400">غير مسكن في مجموعة</span>
                                            @endforelse
                                            @if(count($userPerms) > 3)
                                                <span class="text-[10px] text-indigo-600 font-bold">+{{ count($userPerms) - 3 }} أخرى</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                            <span>نشط ومفعل</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded-full border border-red-200">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                            <span>موقوف ومعطل</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <!-- زر تغيير كلمة المرور المباشر -->
                                        <button type="button" 
                                            onclick='openAdminChangePasswordModal(@json($user))'
                                            class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold rounded-lg transition flex items-center gap-1 shadow-2xs"
                                            title="تغيير كلمة المرور وإجبار التغيير عند الدخول">
                                            <span>🔑</span>
                                            <span>كلمة المرور</span>
                                        </button>

                                        <!-- زر تعديل الصلاحيات والمستخدم -->
                                        <button type="button" 
                                            onclick='openEditModal(@json($user))'
                                            class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition flex items-center gap-1">
                                            <span>⚙️</span>
                                            <span>تعديل</span>
                                        </button>

                                        @if(auth()->id() !== $user->id)
                                            <!-- زر إيقاف / تنشيط -->
                                            <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1.5 rounded-lg font-bold transition {{ $user->status === 'active' ? 'bg-slate-100 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                    {{ $user->status === 'active' ? '⏸️' : '▶️' }}
                                                </button>
                                            </form>

                                            <!-- زر حذف -->
                                            @if(!empty($user->integrity_reasons))
                                                <button type="button" 
                                                    onclick='showBlockedDeleteModal(@json($user))'
                                                    class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition" 
                                                    title="الحذف محظور ومقيد لوجود عمليات وسجلات (انقر لمعرفة القيود)">
                                                    🔒
                                                </button>
                                            @else
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد تماماً من حذف هذا المستخدم نهائياً؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition" title="حذف المستخدم">
                                                        🗑️
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-slate-400 text-[10px] px-2 py-1 bg-slate-100 rounded">حسابك الحالي</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">لا يوجد موظفون مسجلون بهذه المعايير.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- =============================================================== -->
    <!-- 2. جدول الكادر التعليمي والمعلمون (Teachers) -->
    <!-- =============================================================== -->
    @elseif($tab === 'teachers')
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-blue-50/30">
                <div>
                    <h3 class="text-base font-black text-slate-900">سجل الكادر التعليمي وهيئة التدريس</h3>
                    <p class="text-xs text-slate-500">إدارة المعلمين ومتابعة الجلسات الحية 1-on-1 وأرصدة المستحقات المالية.</p>
                </div>
                <span class="text-xs font-bold font-mono text-slate-500">{{ $users->total() }} معلم ومعلمة</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-4"># المعرف</th>
                            <th class="p-4">اسم المعلم والبريد</th>
                            <th class="p-4">رقم الواتساب</th>
                            <th class="p-4">رقم الحساب المالي</th>
                            <th class="p-4">رصيد المستحقات</th>
                            <th class="p-4">حالة الحساب</th>
                            <th class="p-4 text-center">التحكم والعمليات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4">
                                    <div class="flex flex-col gap-1">
                                        <button type="button" onclick="copyUserCode('{{ $user->user_code }}')" 
                                            class="font-mono text-[11px] font-black text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2 py-0.5 rounded-lg w-max flex items-center gap-1 transition" title="انقر لنسخ كود الدخول">
                                            <span>🆔</span>
                                            <span>{{ $user->user_code ?? ('TEA-' . $user->id) }}</span>
                                            <span class="text-[9px] text-blue-400">📋</span>
                                        </button>
                                        <span class="text-[10px] text-slate-400 font-mono">#{{ $user->id }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 font-black flex items-center justify-center shrink-0 text-sm">
                                            👨‍🏫
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-black text-slate-900 block leading-tight">{{ $user->name }}</span>
                                                <span class="text-xs" title="{{ $user->gender_label }}">{{ $user->gender_icon }}</span>
                                            </div>
                                            @if($user->name_en)
                                                <span class="text-[11px] text-slate-500 font-medium block" dir="ltr">{{ $user->name_en }}</span>
                                            @endif
                                            <span class="text-[11px] text-slate-400 font-mono block" dir="ltr">{{ $user->email }}</span>
                                            @if($user->birth_date)
                                                <span class="text-[10px] text-slate-400 block">🎂 {{ $user->birth_date->format('Y-m-d') }} ({{ $user->age }} سنة)</span>
                                            @endif
                                            @if($user->must_change_password)
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-amber-800 bg-amber-100 border border-amber-300 px-1.5 py-0.5 rounded mt-0.5">
                                                    ⚠️ ملزم بتغيير كلمة المرور
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-mono text-slate-700" dir="ltr">
                                    {{ $user->phone_whatsapp ?? '-' }}
                                    <span class="text-[10px] text-slate-400 block font-bold">{{ $user->gender_label }}</span>
                                </td>
                                <td class="p-4 font-mono font-bold text-indigo-700">
                                    {{ $user->financialAccount?->account_code ?? '22' . str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="p-4 font-mono font-bold text-emerald-600">
                                    {{ $user->financialAccount?->formatted_balance ?? '0.00 ر.ي' }}
                                </td>
                                <td class="p-4">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                            <span>نشط ومفعل</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded-full border border-red-200">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                            <span>موقوف</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <!-- زر تغيير كلمة المرور المباشر -->
                                        <button type="button" 
                                            onclick='openAdminChangePasswordModal(@json($user))'
                                            class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold rounded-lg transition flex items-center gap-1 shadow-2xs"
                                            title="تغيير كلمة المرور وإجبار التغيير عند الدخول">
                                            <span>🔑</span>
                                            <span>كلمة المرور</span>
                                        </button>

                                        <!-- زر كشف حساب وحركة المعلم -->
                                        <a href="{{ route('admin.reports.teacher.statement', $user->id) }}" target="_blank" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition flex items-center gap-1 shadow-2xs" title="كشف حساب المعلم وحركة التدريس">
                                            <span>💳</span>
                                            <span>كشف الحساب والحركة</span>
                                        </a>

                                        <!-- زر إصدار سند صرف لمستحقات المعلم -->
                                        <a href="{{ route('accounting.dashboard') }}#paymentTab" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-lg transition flex items-center gap-1">
                                            <span>💸</span>
                                            <span>سند صرف</span>
                                        </a>

                                        <!-- زر تعديل البيانات -->
                                        <button type="button" onclick='openEditModal(@json($user))' class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition">
                                            ✏️ تعديل
                                        </button>

                                        <!-- زر إيقاف / تنشيط -->
                                        <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg font-bold transition {{ $user->status === 'active' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                {{ $user->status === 'active' ? 'إيقاف ⏸️' : 'تنشيط ▶️' }}
                                            </button>
                                        </form>

                                        <!-- زر حذف للمعلم -->
                                        @if(!empty($user->integrity_reasons))
                                            <button type="button" 
                                                onclick='showBlockedDeleteModal(@json($user))'
                                                class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition" 
                                                title="الحذف محظور ومقيد لوجود عمليات وسجلات (انقر لمعرفة القيود)">
                                                🔒
                                            </button>
                                        @else
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد تماماً من حذف هذا المعلم نهائياً؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition" title="حذف المعلم">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">لا يوجد معلمون مسجلون بهذه المعايير.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- =============================================================== -->
    <!-- 3. جدول الطلاب المسجلين (Students) -->
    <!-- =============================================================== -->
    @elseif($tab === 'students')
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-indigo-50/30">
                <div>
                    <h3 class="text-base font-black text-slate-900">سجل الطلاب والدارسين</h3>
                    <p class="text-xs text-slate-500">متابعة المستويات الأكاديمية المقيد بها الطلاب، وحالة القبول والذمة المالية.</p>
                </div>
                <span class="text-xs font-bold font-mono text-slate-500">{{ $users->total() }} طالب مسجل</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-4">الرقم الأكاديمي</th>
                            <th class="p-4">اسم الطالب والبريد</th>
                            <th class="p-4">رقم الواتساب</th>
                            <th class="p-4">المستوى الأكاديمي</th>
                            <th class="p-4">الذمة المالية (المتبقي)</th>
                            <th class="p-4">الحالة الأكاديمية</th>
                            <th class="p-4 text-center">العمليات السريعة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            @php
                                $enrollment = $user->enrollments?->first();
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4">
                                    <div class="flex flex-col gap-1">
                                        <button type="button" onclick="copyUserCode('{{ $user->user_code ?? $user->student_code }}')" 
                                            class="font-mono text-[11px] font-black text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded-lg w-max flex items-center gap-1 transition" title="انقر لنسخ كود الطالب / المستخدم">
                                            <span>🆔</span>
                                            <span>{{ $user->user_code ?? $user->student_code ?? ('STU-' . $user->id) }}</span>
                                            <span class="text-[9px] text-indigo-400">📋</span>
                                        </button>
                                        <span class="text-[10px] text-slate-400 font-mono">#{{ $user->id }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 font-black flex items-center justify-center shrink-0 text-sm">
                                            🎓
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-black text-slate-900 block leading-tight">{{ $user->name }}</span>
                                                <span class="text-xs" title="{{ $user->gender_label }}">{{ $user->gender_icon }}</span>
                                            </div>
                                            @if($user->name_en)
                                                <span class="text-[11px] text-slate-500 font-medium block" dir="ltr">{{ $user->name_en }}</span>
                                            @endif
                                            <span class="text-[11px] text-slate-400 font-mono block" dir="ltr">{{ $user->email }}</span>
                                            @if($user->birth_date)
                                                <span class="text-[10px] text-slate-400 block">🎂 {{ $user->birth_date->format('Y-m-d') }} ({{ $user->age }} سنة)</span>
                                            @endif
                                            @if($user->must_change_password)
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-amber-800 bg-amber-100 border border-amber-300 px-1.5 py-0.5 rounded mt-0.5">
                                                    ⚠️ ملزم بتغيير كلمة المرور
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-mono text-slate-700" dir="ltr">
                                    {{ $user->phone_whatsapp ?? '-' }}
                                    <span class="text-[10px] text-slate-400 block font-bold">{{ $user->gender_label }}</span>
                                </td>
                                <td class="p-4">
                                    @if($enrollment?->level)
                                        <span class="font-bold text-slate-900 block">{{ $enrollment->level->name }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $enrollment->level->course?->name }}</span>
                                    @else
                                        <span class="text-amber-600 bg-amber-50 px-2 py-0.5 rounded font-bold">بانتظار الاعتماد</span>
                                    @endif
                                </td>
                                <td class="p-4 font-mono font-bold {{ ($user->financialAccount?->current_balance ?? 0) > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ $user->financialAccount?->formatted_balance ?? '0.00 ر.ي' }}
                                </td>
                                <td class="p-4">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                            <span>طالب نشط</span>
                                        </span>
                                    @elseif($user->status === 'pending_test')
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                                            <span>بانتظار الاختبار</span>
                                        </span>
                                    @elseif($user->status === 'pending_approval')
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-full border border-purple-200">
                                            <span>بانتظار اعتماد المشرف</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded-full border border-red-200">
                                            <span>موقوف</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <!-- زر تغيير كلمة المرور المباشر -->
                                        <button type="button" 
                                            onclick='openAdminChangePasswordModal(@json($user))'
                                            class="px-2 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold rounded-lg transition flex items-center gap-1 shadow-2xs text-[11px]"
                                            title="تغيير كلمة المرور وإجبار التغيير عند الدخول">
                                            <span>🔑</span>
                                            <span>كلمة المرور</span>
                                        </button>

                                        <!-- السجل الأكاديمي والمالي الشامل 360 -->
                                        <a href="{{ route('admin.reports.student.transcript', $user->id) }}" target="_blank" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition flex items-center gap-1 border border-indigo-200 shadow-2xs" title="السجل الأكاديمي والمالي الشامل 360 من أول ما دخل للتخرج">
                                            <span>📜</span>
                                            <span>السجل الشامل 360</span>
                                        </a>

                                        <!-- كشف حساب الطالب -->
                                        <a href="{{ route('accounting.statement', $user->id) }}" target="_blank" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition flex items-center gap-1" title="كشف حساب تفصيلي">
                                            <span>📄</span>
                                            <span>كشف الحساب</span>
                                        </a>

                                        <!-- سند قبض -->
                                        <a href="{{ route('accounting.dashboard') }}" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-lg transition flex items-center gap-1" title="إصدار سند قبض">
                                            <span>🧾</span>
                                            <span>سند قبض</span>
                                        </a>

                                        <!-- زر تعديل البيانات -->
                                        <button type="button" onclick='openEditModal(@json($user))' class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition" title="تعديل">
                                            ✏️
                                        </button>

                                        <!-- زر إيقاف / تنشيط -->
                                        <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1.5 rounded-lg font-bold transition {{ $user->status === 'active' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}" title="{{ $user->status === 'active' ? 'إيقاف' : 'تنشيط' }}">
                                                {{ $user->status === 'active' ? '⏸️' : '▶️' }}
                                            </button>
                                        </form>

                                        <!-- زر حذف للطالب -->
                                        @if(!empty($user->integrity_reasons))
                                            <button type="button" 
                                                onclick='showBlockedDeleteModal(@json($user))'
                                                class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition" 
                                                title="الحذف محظور ومقيد لوجود عمليات وسجلات (انقر لمعرفة القيود)">
                                                🔒
                                            </button>
                                        @else
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد تماماً من حذف هذا الطالب نهائياً؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition" title="حذف الطالب">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">لا يوجد طلاب مسجلون بهذه المعايير.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- =============================================================== -->
    <!-- 4. جدول أولياء الأمور (Parents) -->
    <!-- =============================================================== -->
    @elseif($tab === 'parents')
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-amber-50/30">
                <div>
                    <h3 class="text-base font-black text-slate-900">سجل أولياء الأمور</h3>
                    <p class="text-xs text-slate-500">حسابات أولياء الأمور المرتبطة بالأبناء لمتابعة التقارير وسداد الاشتراكات.</p>
                </div>
                <span class="text-xs font-bold font-mono text-slate-500">{{ $users->total() }} ولي أمر</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-4">كود ولي الأمر</th>
                            <th class="p-4">اسم ولي الأمر والبريد</th>
                            <th class="p-4">رقم الواتساب</th>
                            <th class="p-4">الأبناء التابعون</th>
                            <th class="p-4">حالة الحساب</th>
                            <th class="p-4">تاريخ التسجيل</th>
                            <th class="p-4 text-center">التحكم والإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4">
                                    <div class="flex flex-col gap-1">
                                        <button type="button" onclick="copyUserCode('{{ $user->user_code ?? $user->student_code }}')" 
                                            class="font-mono text-[11px] font-black text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-2 py-0.5 rounded-lg w-max flex items-center gap-1 transition" title="انقر لنسخ كود ولي الأمر">
                                            <span>🆔</span>
                                            <span>{{ $user->user_code ?? $user->student_code ?? ('PAR-' . $user->id) }}</span>
                                            <span class="text-[9px] text-amber-500">📋</span>
                                        </button>
                                        <span class="text-[10px] text-slate-400 font-mono">#{{ $user->id }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 font-black flex items-center justify-center shrink-0 text-sm">
                                            👨‍👧‍👦
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-black text-slate-900 block leading-tight">{{ $user->name }}</span>
                                                <span class="text-xs" title="{{ $user->gender_label }}">{{ $user->gender_icon }}</span>
                                            </div>
                                            @if($user->name_en)
                                                <span class="text-[11px] text-slate-500 font-medium block" dir="ltr">{{ $user->name_en }}</span>
                                            @endif
                                            <span class="text-[11px] text-slate-400 font-mono block" dir="ltr">{{ $user->email }}</span>
                                            @if($user->birth_date)
                                                <span class="text-[10px] text-slate-400 block">🎂 {{ $user->birth_date->format('Y-m-d') }} ({{ $user->age }} سنة)</span>
                                            @endif
                                            @if($user->must_change_password)
                                                <span class="inline-flex items-center gap-1 text-[9px] font-black text-amber-800 bg-amber-100 border border-amber-300 px-1.5 py-0.5 rounded mt-0.5">
                                                    ⚠️ ملزم بتغيير كلمة المرور
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-mono text-slate-700" dir="ltr">
                                    {{ $user->phone_whatsapp ?? '-' }}
                                    <span class="text-[10px] text-slate-400 block font-bold">{{ $user->gender_label }}</span>
                                </td>
                                <td class="p-4">
                                    @php
                                        $parentPayload = [
                                            'id' => $user->id,
                                            'name' => $user->name,
                                            'user_code' => $user->user_code ?? $user->student_code ?? ('PAR-' . $user->id),
                                            'email' => $user->email ?? '',
                                            'phone_whatsapp' => $user->phone_whatsapp ?? '',
                                            'children' => $user->children->map(function($c) {
                                                $levelName = $c->enrollments->first()?->level?->name ?? $c->studyGroups->first()?->name ?? 'طالب مسجل';
                                                return [
                                                    'id' => $c->id,
                                                    'name' => $c->name,
                                                    'name_en' => $c->name_en ?? '',
                                                    'user_code' => $c->user_code ?? $c->student_code ?? ('STU-' . $c->id),
                                                    'level_name' => $levelName,
                                                ];
                                            })->values(),
                                        ];
                                    @endphp
                                    <div class="space-y-1.5">
                                        <button type="button" 
                                            onclick='openManageChildrenModal(@json($parentPayload))'
                                            data-parent-data='@json($parentPayload)'
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-purple-50 to-indigo-50 hover:from-purple-100 hover:to-indigo-100 text-purple-900 font-black rounded-xl border border-purple-200 shadow-2xs transition text-xs"
                                            title="إدارة وربط الأبناء بهذا الحساب">
                                            <span>👨‍👧‍👦</span>
                                            <span>الأبناء ({{ $user->children->count() }})</span>
                                            <span class="text-[10px] text-purple-600 font-bold">ربط / فك ⚙️</span>
                                        </button>
                                        @if($user->children->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 max-w-xs">
                                                @foreach($user->children as $child)
                                                    <span class="inline-flex items-center gap-1 text-[10px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200 font-medium">
                                                        <span>🎓</span>
                                                        <span>{{ $child->name }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                            <span>نشط</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-red-700 bg-red-50 px-2.5 py-1 rounded-full border border-red-200">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                            <span>موقوف</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-500">
                                    {{ $user->created_at ? $user->created_at->format('Y/m/d') : '-' }}
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <!-- زر إدارة وربط الأبناء -->
                                        <button type="button" 
                                            onclick='openManageChildrenModal(@json($parentPayload))'
                                            data-parent-data='@json($parentPayload)'
                                            class="px-2.5 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-800 font-bold rounded-lg transition flex items-center gap-1 shadow-2xs text-[11px]"
                                            title="إدارة وربط الأبناء بحساب ولي الأمر">
                                            <span>👨‍👧‍👦</span>
                                            <span>الأبناء ({{ $user->children->count() }})</span>
                                        </button>

                                        <!-- زر تغيير كلمة المرور المباشر -->
                                        <button type="button" 
                                            onclick='openAdminChangePasswordModal(@json($user))'
                                            class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold rounded-lg transition flex items-center gap-1 shadow-2xs text-[11px]"
                                            title="تغيير كلمة المرور وإجبار التغيير عند الدخول">
                                            <span>🔑</span>
                                            <span>كلمة المرور</span>
                                        </button>

                                        <!-- زر تعديل البيانات -->
                                        <button type="button" onclick='openEditModal(@json($user))' class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition">
                                            ✏️ تعديل
                                        </button>

                                        <!-- زر إيقاف / تنشيط -->
                                        <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg font-bold transition {{ $user->status === 'active' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                {{ $user->status === 'active' ? 'إيقاف ⏸️' : 'تنشيط ▶️' }}
                                            </button>
                                        </form>

                                        <!-- زر حذف لولي الأمر -->
                                        @if(!empty($user->integrity_reasons))
                                            <button type="button" 
                                                onclick='showBlockedDeleteModal(@json($user))'
                                                class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 transition" 
                                                title="الحذف محظور ومقيد لوجود عمليات وسجلات (انقر لمعرفة القيود)">
                                                🔒
                                            </button>
                                        @else
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد تماماً من حذف ولي الأمر نهائياً؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition" title="حذف ولي الأمر">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">لا يوجد أولياء أمور مسجلون بهذه المعايير.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- =============================================================== -->
    <!-- 5. جدول طلبات استعادة وتغيير كلمات المرور (Password Reset Requests) -->
    <!-- =============================================================== -->
    @elseif($tab === 'requests')
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-rose-50/40">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-sm shadow-2xs">🔑</span>
                        <h3 class="text-base font-black text-slate-900">سجل طلبات استرجاع وتغيير كلمات المرور</h3>
                        @if(($pendingResetCount ?? 0) > 0)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-rose-600 text-white animate-pulse shadow-sm">
                                {{ $pendingResetCount }} طلب معلق بحاجة لإجراء
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        طلبات مقدمة من الطلاب والمعلمين والمستخدمين لمراجعتها وتعيين كلمة مرور مؤقتة وتفعيل إجبار التغيير عند أول تسجيل دخول.
                    </p>
                </div>
                @if(isset($passwordRequests))
                    <span class="text-xs font-bold font-mono text-slate-500">{{ $passwordRequests->total() }} طلب مسجل</span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-4"># الطلب</th>
                            <th class="p-4">مقدم الطلب والمعرّف المدخل</th>
                            <th class="p-4">حساب المستخدم المرتبط</th>
                            <th class="p-4">رقم الواتساب / الهاتف</th>
                            <th class="p-4">ملاحظات وسبب الطلب</th>
                            <th class="p-4">حالة الطلب</th>
                            <th class="p-4">تاريخ الطلب</th>
                            <th class="p-4 text-center">التحكم والمعالجة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @if(isset($passwordRequests))
                            @forelse($passwordRequests as $req)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-mono font-bold text-slate-500">
                                        #{{ $req->id }}
                                    </td>
                                    <td class="p-4">
                                        <div>
                                            <span class="font-black text-slate-900 block leading-tight">{{ $req->name ?? 'غير محدد' }}</span>
                                            <span class="text-[11px] font-mono text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 inline-block mt-0.5" dir="ltr">
                                                {{ $req->identifier }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        @if($req->user)
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs">{{ $req->user->gender_icon }}</span>
                                                <div>
                                                    <span class="font-bold text-slate-900 block leading-tight">{{ $req->user->name }}</span>
                                                    <div class="flex items-center gap-1.5 mt-0.5">
                                                        <span class="text-[10px] font-mono text-slate-500">{{ $req->user->user_code }}</span>
                                                        <span class="text-[10px] text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded font-bold">
                                                            {{ $req->user->role }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-[11px] text-slate-400">لم يتم ربطه بحساب تلقائياً</span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono text-slate-700">
                                        @if($req->phone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $req->phone) }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-800 font-bold bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200 transition" dir="ltr" title="مراسلة فورية عبر واتساب">
                                                <span>💬</span>
                                                <span>{{ $req->phone }}</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-4 max-w-xs text-slate-600">
                                        {{ $req->user_notes ?? 'نسيان كلمة المرور' }}
                                        @if($req->admin_notes)
                                            <div class="text-[10px] text-indigo-700 bg-indigo-50 p-1.5 rounded-md mt-1 border border-indigo-100 leading-tight">
                                                <strong>ملاحظة الإدارة:</strong> {{ $req->admin_notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        @if($req->status === 'pending')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200 animate-pulse">
                                                <span>⏳</span>
                                                <span>معلق بانتظار الإدارة</span>
                                            </span>
                                        @elseif($req->status === 'completed')
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                                <span>✅</span>
                                                <span>تم تعيين كلمة المرور</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200">
                                                <span>❌</span>
                                                <span>مرفوض</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-500 font-mono text-[11px]">
                                        {{ $req->created_at ? $req->created_at->diffForHumans() : '-' }}
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                            @if($req->status === 'pending')
                                                <!-- زر الموافقة وإعادة تعيين كلمة المرور -->
                                                <button type="button" 
                                                    onclick='openHandleResetModal(@json($req))'
                                                    class="px-3 py-1.5 bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black rounded-xl shadow-xs transition flex items-center gap-1">
                                                    <span>🔑</span>
                                                    <span>تعيين كلمة مرور مؤقتة</span>
                                                </button>

                                                <!-- زر الرفض -->
                                                <form action="{{ route('admin.password_requests.handle', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد رفض طلب استرجاع كلمة المرور هذا؟');">
                                                    @csrf
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl transition">
                                                        رفض ✕
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" 
                                                    onclick='openHandleResetModal(@json($req))'
                                                    class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition text-[11px]">
                                                    إعادة التعيين مجدداً 🔄
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-slate-400">لا توجد طلبات استرجاع كلمات مرور مسجلة حالياً.</td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- روابط الصفحات Pagination -->
    @if($tab === 'requests' && isset($passwordRequests) && $passwordRequests->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
            {{ $passwordRequests->links() }}
        </div>
    @elseif($tab !== 'requests' && isset($users) && $users->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
            {{ $users->links() }}
        </div>
    @endif


</div>

<!-- =============================================================== -->
<!-- مودال إضافة مستخدم جديد وتحديد الصلاحيات (Add User Modal) -->
<!-- =============================================================== -->
<div id="addUserModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 md:p-6 overflow-hidden" style="overscroll-behavior: contain;">
    <!-- خلفية النقر للإغلاق -->
    <div class="fixed inset-0 cursor-pointer" onclick="closeAddModal()"></div>

    <!-- بطاقة النافذة المنبثقة بحجم متناسق ومحدد الارتفاع -->
    <div class="relative bg-white w-full max-w-4xl rounded-3xl shadow-2xl border border-slate-200 flex flex-col max-h-[92vh] sm:max-h-[90vh] overflow-hidden z-10">
        
        <!-- الترويسة المثبتة بالأعلى (Fixed Header) -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl shadow-xs">
                    👤
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">إضافة مستخدم جديد للنظام وتحديد صلاحياته</h2>
                    <p class="text-xs text-slate-500 mt-0.5">اختر نوع الحساب وحدد الصلاحيات الدقيقة الممنوحة له.</p>
                </div>
            </div>
            <button type="button" onclick="closeAddModal()" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center text-sm font-bold transition shadow-xs" title="إغلاق النافذة (Esc)">✕</button>
        </div>

        <!-- نموذج الإضافة مع جسم قابل للتمرير بسلاسة -->
        <form action="{{ route('admin.users.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
            @csrf

            <!-- جسم النموذج القابل للتمرير بشريط تمرير واضح وأنيق -->
            <div class="modal-scrollable-body overflow-y-auto flex-1 p-5 sm:p-6 space-y-6 overscroll-contain">
                <!-- البيانات الأساسية -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الاسم الكامل بالعربية <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="مثال: أستاذ/ أحمد محمد" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الاسم بالإنجليزية (Full English Name) <span class="text-slate-400 font-normal">(اختياري)</span></label>
                        <input type="text" name="name_en" placeholder="e.g. Ahmed Mohammed" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none" dir="ltr">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الجنس (Gender) <span class="text-red-500">*</span></label>
                        <select name="gender" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                            <option value="male" selected>ذكر 👨 (Male)</option>
                            <option value="female">أنثى 👩 (Female)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تاريخ الميلاد <span class="text-slate-400 font-normal">(اختياري)</span></label>
                        <input type="date" name="birth_date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">البريد الإلكتروني <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required placeholder="email@asots.org" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono" dir="ltr">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">رقم الهاتف والواتساب <span class="text-red-500">*</span></label>
                        <input type="text" name="phone_whatsapp" required placeholder="+967735999101" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono" dir="ltr">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الدور في المنصة (Role) <span class="text-red-500">*</span></label>
                        <select name="role" id="addRoleSelect" onchange="onRoleChange(this.value, 'add')" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                            <option value="supervisor" selected>🛡️ مشرف أكاديمي (Supervisor)</option>
                            <option value="accountant">💼 محاسب مالي (Accountant)</option>
                            <option value="teacher">👨‍🏫 معلم (Teacher)</option>
                            <option value="student">🎓 طالب (Student)</option>
                            <option value="parent">👨‍👧‍👦 ولي أمر (Parent)</option>
                            <option value="admin">👑 مدير نظام (Administrator)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">مجموعة الصلاحيات والأدوار (Permission Group) 🛡️</label>
                        <select name="permission_group_id" id="addPermissionGroupSelect" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-xs">
                            <option value="">-- تلقائي حسب الدور أو بدون مجموعة --</option>
                            @foreach($permissionGroups as $pg)
                                <option value="{{ $pg->id }}" {{ $pg->slug === 'supervisors' ? 'selected' : '' }}>
                                    {{ $pg->icon ?? '🛡️' }} {{ $pg->name }} {{ $pg->isAdminGroup() ? '👑 (كل الصلاحيات تلقائياً)' : '(' . (is_array($pg->permissions) ? count($pg->permissions) : 0) . ' صلاحية)' }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-slate-400 block mt-1">يرث المستخدم كامل صلاحيات المجموعة فوراً</span>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-bold text-slate-700">كلمة المرور المؤقتة <span class="text-red-500">*</span></label>
                            <button type="button" onclick="generateRandomPass('addPassInput')" class="text-[10px] text-indigo-600 font-bold hover:underline">توليد عشوائي 🎲</button>
                        </div>
                        <input type="text" name="password" id="addPassInput" required value="password123" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono" dir="ltr">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">حالة الحساب</label>
                        <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                            <option value="active" selected>نشط ومفعل فوراً (Active)</option>
                            <option value="suspended">موقوف ومعطل (Suspended)</option>
                        </select>
                    </div>
                </div>

                <!-- إجبار تغيير كلمة المرور عند أول دخول -->
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3">
                    <input type="checkbox" name="must_change_password" id="addMustChangePass" value="1" checked class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
                    <label for="addMustChangePass" class="text-xs font-bold text-amber-900 cursor-pointer">
                        إجبار المستخدم على تغيير كلمة المرور فور أول تسجيل دخول لحسابه (موصى به للأمان) 🔐
                    </label>
                </div>


                <!-- مصفوفة الصلاحيات المخصصة (Permissions Matrix) -->
                <div id="addPermissionsSection" class="border border-slate-200 rounded-2xl p-5 bg-slate-50/70 space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-200 pb-3">
                        <div>
                            <span class="text-sm font-black text-slate-900 flex items-center gap-1.5">
                                <span>🔐</span>
                                <span>جدول الصلاحيات المخصصة للمستخدم</span>
                            </span>
                            <p class="text-[11px] text-slate-500">حدد الصلاحيات التي يستطيع هذا المستخدم تنفيذها في النظام.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="toggleAllCheckboxes('add', true)" class="px-2.5 py-1 bg-white border border-slate-300 hover:bg-slate-100 rounded-lg text-[10px] font-bold text-indigo-700">تحديد الكل</button>
                            <button type="button" onclick="toggleAllCheckboxes('add', false)" class="px-2.5 py-1 bg-white border border-slate-300 hover:bg-slate-100 rounded-lg text-[10px] font-bold text-slate-600">إلغاء التحديد</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($availablePermissions as $catKey => $catData)
                            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs space-y-2.5">
                                <span class="text-xs font-black text-slate-900 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                                    <span>{{ $catData['icon'] }}</span>
                                    <span>{{ $catData['title'] }}</span>
                                </span>

                                <div class="space-y-2">
                                    @foreach($catData['permissions'] as $permKey => $perm)
                                        <label class="flex items-start gap-2.5 cursor-pointer hover:bg-slate-50 p-1.5 rounded-lg transition">
                                            <input type="checkbox" name="permissions[{{ $permKey }}]" value="1" class="add-perm-cb mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                            <div>
                                                <span class="text-xs font-bold text-slate-800 block">{{ $perm['label'] }}</span>
                                                <span class="text-[10px] text-slate-400 block leading-tight">{{ $perm['desc'] }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- أسفل النافذة المثبت (Fixed Footer) - تظهر الأزرار دوماً دون أن تختفي أبداً -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 shrink-0">
                <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-200 bg-white border border-slate-300 transition shadow-2xs">
                    تراجع / إلغاء ✕
                </button>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>🚀</span>
                    <span>حفظ وإضافة المستخدم وتفعيل صلاحياته</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- مودال تعديل المستخدم والصلاحيات (Edit User & Permissions Modal) -->
<!-- =============================================================== -->
<div id="editUserModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 md:p-6 overflow-hidden" style="overscroll-behavior: contain;">
    <!-- خلفية النقر للإغلاق -->
    <div class="fixed inset-0 cursor-pointer" onclick="closeEditModal()"></div>

    <!-- بطاقة النافذة المنبثقة بحجم متناسق ومحدد الارتفاع -->
    <div class="relative bg-white w-full max-w-4xl rounded-3xl shadow-2xl border border-slate-200 flex flex-col max-h-[92vh] sm:max-h-[90vh] overflow-hidden z-10">
        
        <!-- الترويسة المثبتة بالأعلى (Fixed Header) -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl shadow-xs">
                    ⚙️
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">تعديل بيانات المستخدم وجدول الصلاحيات</h2>
                    <p class="text-xs text-slate-500 mt-0.5" id="editModalSubtitle">تحديث صلاحيات الحساب وتعيين حالته في النظام.</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center text-sm font-bold transition shadow-xs" title="إغلاق النافذة (Esc)">✕</button>
        </div>

        <!-- نموذج التعديل مع جسم قابل للتمرير بسلاسة -->
        <form id="editUserForm" method="POST" action="" class="flex flex-col flex-1 min-h-0 overflow-hidden">
            @csrf

            <!-- جسم النموذج القابل للتمرير بشريط تمرير واضح وأنيق -->
            <div class="modal-scrollable-body overflow-y-auto flex-1 p-5 sm:p-6 space-y-6 overscroll-contain">
                <!-- البيانات الأساسية -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الاسم الكامل بالعربية <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="editName" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الاسم بالإنجليزية (Full English Name) <span class="text-slate-400 font-normal">(اختياري)</span></label>
                        <input type="text" name="name_en" id="editNameEn" placeholder="e.g. John Doe" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none" dir="ltr">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الجنس (Gender) <span class="text-red-500">*</span></label>
                        <select name="gender" id="editGender" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                            <option value="male">ذكر 👨 (Male)</option>
                            <option value="female">أنثى 👩 (Female)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تاريخ الميلاد <span class="text-slate-400 font-normal">(اختياري)</span></label>
                        <input type="date" name="birth_date" id="editBirthDate" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">البريد الإلكتروني <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="editEmail" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono" dir="ltr">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">رقم الهاتف والواتساب <span class="text-red-500">*</span></label>
                        <input type="text" name="phone_whatsapp" id="editPhone" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono" dir="ltr">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الدور في المنصة (Role) <span class="text-red-500">*</span></label>
                        <select name="role" id="editRoleSelect" onchange="onRoleChange(this.value, 'edit')" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                            <option value="supervisor">🛡️ مشرف أكاديمي (Supervisor)</option>
                            <option value="accountant">💼 محاسب مالي (Accountant)</option>
                            <option value="teacher">👨‍🏫 معلم (Teacher)</option>
                            <option value="student">🎓 طالب (Student)</option>
                            <option value="parent">👨‍👧‍👦 ولي أمر (Parent)</option>
                            <option value="admin">👑 مدير نظام (Administrator)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">مجموعة الصلاحيات والأدوار (Permission Group) 🛡️</label>
                        <select name="permission_group_id" id="editPermissionGroupSelect" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-xs">
                            <option value="">-- بدون مجموعة (تخصيص يدوي مباشر) --</option>
                            @foreach($permissionGroups as $pg)
                                <option value="{{ $pg->id }}">
                                    {{ $pg->icon ?? '🛡️' }} {{ $pg->name }} {{ $pg->isAdminGroup() ? '👑 (كل الصلاحيات تلقائياً)' : '(' . (is_array($pg->permissions) ? count($pg->permissions) : 0) . ' صلاحية)' }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-slate-400 block mt-1">يرث المستخدم صلاحيات المجموعة المختارة مباشرة</span>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تغيير كلمة المرور (اختياري)</label>
                        <input type="text" name="password" placeholder="اتركه فارغاً دون تغيير" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono" dir="ltr">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">حالة الحساب</label>
                        <select name="status" id="editStatusSelect" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                            <option value="active">نشط ومفعل (Active)</option>
                            <option value="suspended">موقوف ومعطل (Suspended)</option>
                        </select>
                    </div>
                </div>

                <!-- خيار إجبار تغيير كلمة المرور عند أول دخول قادم -->
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3">
                    <input type="checkbox" name="must_change_password" id="editMustChangePass" value="1" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
                    <label for="editMustChangePass" class="text-xs font-bold text-amber-900 cursor-pointer">
                        إجبار المستخدم على تغيير كلمة المرور عند أول تسجيل دخول قادم 🔐
                    </label>
                </div>


                <!-- مصفوفة الصلاحيات المخصصة (Permissions Matrix) -->
                <div id="editPermissionsSection" class="border border-slate-200 rounded-2xl p-5 bg-slate-50/70 space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-200 pb-3">
                        <div>
                            <span class="text-sm font-black text-slate-900 flex items-center gap-1.5">
                                <span>🔐</span>
                                <span>تعديل الصلاحيات الممنوحة للمستخدم</span>
                            </span>
                            <p class="text-[11px] text-slate-500">حدد بالضبط العمليات المصرح للمستخدم إجراؤها.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="toggleAllCheckboxes('edit', true)" class="px-2.5 py-1 bg-white border border-slate-300 hover:bg-slate-100 rounded-lg text-[10px] font-bold text-indigo-700">تحديد الكل</button>
                            <button type="button" onclick="toggleAllCheckboxes('edit', false)" class="px-2.5 py-1 bg-white border border-slate-300 hover:bg-slate-100 rounded-lg text-[10px] font-bold text-slate-600">إلغاء التحديد</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($availablePermissions as $catKey => $catData)
                            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs space-y-2.5">
                                <span class="text-xs font-black text-slate-900 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                                    <span>{{ $catData['icon'] }}</span>
                                    <span>{{ $catData['title'] }}</span>
                                </span>

                                <div class="space-y-2">
                                    @foreach($catData['permissions'] as $permKey => $perm)
                                        <label class="flex items-start gap-2.5 cursor-pointer hover:bg-slate-50 p-1.5 rounded-lg transition">
                                            <input type="checkbox" name="permissions[{{ $permKey }}]" id="edit_perm_{{ $permKey }}" value="1" class="edit-perm-cb mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                            <div>
                                                <span class="text-xs font-bold text-slate-800 block">{{ $perm['label'] }}</span>
                                                <span class="text-[10px] text-slate-400 block leading-tight">{{ $perm['desc'] }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- أسفل النافذة المثبت (Fixed Footer) - يضم زر الحذف وأزرار التراجع والحفظ ثابتة دوماً -->
            <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 shrink-0">
                <!-- زر الحذف المباشر للمستخدم -->
                <div>
                    <button type="button" id="editDeleteBtn" onclick="confirmDeleteUserFromModal()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-bold text-red-600 hover:text-red-700 hover:bg-red-50 border border-red-200 transition flex items-center justify-center gap-1.5 shadow-2xs">
                        <span>🗑️</span>
                        <span>حذف المستخدم نهائياً</span>
                    </button>
                </div>

                <!-- أزرار التراجع والحفظ -->
                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <button type="button" onclick="closeEditModal()" class="flex-1 sm:flex-initial px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-200 bg-white border border-slate-300 transition shadow-2xs">
                        تراجع / إلغاء ✕
                    </button>
                    <button type="submit" class="flex-1 sm:flex-initial px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                        <span>💾</span>
                        <span>حفظ التعديلات وتحديث الصلاحيات</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- نموذج الحذف الخفي من داخل المودال -->
        <form id="deleteUserFromModalForm" method="POST" action="" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- مودال منع الحذف وتوضيح قيود وسجلات العمليات بالحساب (Blocked Deletion Modal) -->
<!-- =============================================================== -->
<div id="blockedDeleteModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="fixed inset-0 cursor-pointer" onclick="closeBlockedDeleteModal()"></div>
    <div class="relative bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 overflow-hidden z-10 animate-in fade-in zoom-in-95 duration-150">
        <!-- الرأس -->
        <div class="p-6 bg-gradient-to-r from-amber-50 to-rose-50 border-b border-amber-200/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-amber-500/20 text-amber-900 flex items-center justify-center text-2xl font-bold shadow-2xs">
                    🛡️
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-950">قيد أمان: الحذف محظور ومقيد</h3>
                    <p class="text-xs text-slate-500">حماية القيود المحاسبية وسجل العمليات الأكاديمية</p>
                </div>
            </div>
            <button type="button" onclick="closeBlockedDeleteModal()" class="w-8 h-8 rounded-full bg-white/80 hover:bg-white text-slate-400 hover:text-slate-600 flex items-center justify-center transition text-sm">
                ✕
            </button>
        </div>

        <!-- المحتوى والتفاصيل -->
        <div class="p-6 space-y-4">
            <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-bold text-slate-500">اسم المستخدم:</span>
                    <span id="blockedUserName" class="text-xs font-black text-slate-900"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">الرقم الفريد التسلسلي:</span>
                    <span id="blockedUserCode" class="text-xs font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-200"></span>
                </div>
            </div>

            <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl">
                <div class="flex items-center gap-2 mb-1.5 text-rose-900 font-bold text-xs">
                    <span>⚠️</span>
                    <span>لماذا يمنع النظام حذف هذا الحساب نهائياً؟</span>
                </div>
                <p class="text-xs text-rose-800 leading-relaxed">
                    لا يمكن حذف هذا المستخدم نهائياً نظراً لأنه أجرى عمليات بالنظام أو ترتبط به قيود دفاتر مالية وأكاديمية (مثل سندات، مستويات، تسعيرة، حصص أو تقييمات). حذفه سيتسبب في خلل بالتقارير المالية أو ضياع تسلسل السندات.
                </p>
            </div>

            <div>
                <span class="text-xs font-black text-slate-800 block mb-2">📋 العمليات والقيود المكتشفة على الحساب:</span>
                <div id="blockedReasonsList" class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                    <!-- يتم تعبئتها بالجافاسكريبت -->
                </div>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 p-3.5 rounded-2xl text-xs text-emerald-900 flex items-start gap-2">
                <span class="text-base shrink-0">💡</span>
                <div>
                    <span class="font-bold block mb-0.5">الحل النظامي المعتمد:</span>
                    <span>يمكنك استخدام خيار <strong>(إيقاف وتعطيل الحساب)</strong> مما يمنع المستخدم من الدخول نهائياً مع بقاء كافة سنداته وأرصدته وسجلاته سليمة وموثقة.</span>
                </div>
            </div>
        </div>

        <!-- أزرار الإجراءات في أسفل النافذة -->
        <div class="p-5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
            <button type="button" onclick="closeBlockedDeleteModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 transition shadow-2xs">
                إغلاق النافذة ✕
            </button>
            <form id="blockedToggleForm" method="POST" action="" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-black bg-slate-900 hover:bg-slate-800 text-amber-300 shadow-md transition flex items-center justify-center gap-1.5">
                    <span>⏸️</span>
                    <span>إيقاف وتعطيل الحساب الآن</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- =============================================================== -->
<!-- مودال تغيير كلمة المرور للمستخدم مباشرة (Direct Admin Password Reset) -->
<!-- =============================================================== -->
<div id="adminChangePasswordModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="fixed inset-0 cursor-pointer" onclick="closeAdminChangePasswordModal()"></div>
    <div class="relative bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 p-6 z-10">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shadow-xs">
                    🔑
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900" id="changePassUserName">تغيير كلمة المرور</h3>
                    <p class="text-[11px] text-slate-500" id="changePassUserSub">تعيين كلمة مرور جديدة للمستخدم مع إمكانية إجبار التغيير.</p>
                </div>
            </div>
            <button type="button" onclick="closeAdminChangePasswordModal()" class="text-slate-400 hover:text-slate-700 text-base font-bold">✕</button>
        </div>

        <form id="adminChangePasswordForm" method="POST" action="" class="space-y-4 text-xs">
            @csrf
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-bold text-slate-700">كلمة المرور الجديدة <span class="text-red-500">*</span></label>
                    <button type="button" onclick="generateRandomPass('adminNewPassInput')" class="text-[10px] text-indigo-600 font-bold hover:underline">توليد كلمة مرور عشوائية 🎲</button>
                </div>
                <input type="text" name="password" id="adminNewPassInput" required minlength="6"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-sm"
                    placeholder="أدخل 6 خانات على الأقل...">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                <input type="text" name="password_confirmation" id="adminNewPassConf" required minlength="6"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-sm"
                    placeholder="أعد إدخال نفس كلمة المرور...">
            </div>

            <!-- إجبار المستخدم على التغيير عند أول دخول -->
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-2.5">
                <input type="checkbox" name="force_change" id="adminForceChangeCb" value="1" checked class="mt-0.5 w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
                <label for="adminForceChangeCb" class="text-xs font-bold text-amber-900 cursor-pointer leading-relaxed">
                    إجبار المستخدم على تغيير كلمة المرور فور أول تسجيل دخول لحسابه (موصى به للأمان والخصوصية) 🔐
                </label>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeAdminChangePasswordModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">
                    إلغاء ✕
                </button>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>💾</span>
                    <span>حفظ وتعيين كلمة المرور</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- مودال معالجة طلب استرجاع كلمة المرور (Handle Password Reset Request Modal) -->
<!-- =============================================================== -->
<div id="handleResetRequestModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="fixed inset-0 cursor-pointer" onclick="closeHandleResetModal()"></div>
    <div class="relative bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 p-6 z-10">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl shadow-xs">
                    🔑
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900" id="handleModalTitle">معالجة طلب استرجاع الحساب</h3>
                    <p class="text-[11px] text-slate-500">إصدار كلمة مرور مؤقتة وتفعيل إجبار التغيير عند تسجيل الدخول.</p>
                </div>
            </div>
            <button type="button" onclick="closeHandleResetModal()" class="text-slate-400 hover:text-slate-700 text-base font-bold">✕</button>
        </div>

        <form id="handleResetRequestForm" method="POST" action="" class="space-y-4 text-xs">
            @csrf
            <!-- بطاقة تفاصيل الطلب -->
            <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200 space-y-1.5">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500">مقدم الطلب:</span>
                    <span class="font-bold text-slate-900" id="handleApplicantName">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500">المعرف المدخل:</span>
                    <span class="font-mono font-bold text-indigo-700" id="handleApplicantId" dir="ltr">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500">رقم الهاتف / واتساب:</span>
                    <span class="font-mono font-bold text-slate-800" id="handleApplicantPhone" dir="ltr">-</span>
                </div>
                <div class="pt-1 border-t border-slate-200 text-slate-600">
                    <span class="text-slate-400 block text-[10px]">ملاحظات المستخدم:</span>
                    <span id="handleApplicantNotes" class="font-medium">-</span>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-bold text-slate-700">كلمة المرور المؤقتة التي سيتم تعيينها <span class="text-red-500">*</span></label>
                    <button type="button" onclick="generateRandomPass('handleNewPassInput')" class="text-[10px] text-indigo-600 font-bold hover:underline">توليد عشوائي 🎲</button>
                </div>
                <input type="text" name="new_password" id="handleNewPassInput" required value="Pass@2026" minlength="6"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-sm"
                    placeholder="كلمة المرور المؤقتة...">
                <span class="text-[10px] text-slate-400 block mt-1">سيلزم النظام هذا المستخدم بتغييرها فور تسجيل الدخول الأول لحسابه.</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات الإدارة للطلب (اختياري)</label>
                <input type="text" name="admin_notes" id="handleAdminNotes" value="تم تعيين كلمة مرور مؤقتة وتفعيل إجبار التغيير عند الدخول."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <!-- زر تجهيز رسالة الواتساب للمستخدم -->
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xl">💬</span>
                    <div>
                        <span class="text-xs font-bold text-emerald-900 block">رسالة الواتساب للعميل</span>
                        <span class="text-[10px] text-emerald-700">نسخ رسالة جاهزة تتضمن كود الدخول وكلمة المرور المؤقتة</span>
                    </div>
                </div>
                <button type="button" onclick="copyWhatsAppMessage()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-[11px] shadow-sm transition whitespace-nowrap">
                    نسخ الرسالة 📋
                </button>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeHandleResetModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">
                    إلغاء ✕
                </button>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>✅</span>
                    <span>اعتماد وتعيين كلمة المرور</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- مودال إدارة وربط أبناء ولي الأمر (Manage Parent's Children Modal) -->
<!-- =============================================================== -->
<div id="manageChildrenModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <!-- خلفية النقر للإغلاق -->
    <div class="fixed inset-0" onclick="closeManageChildrenModal()"></div>

    <div class="relative bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 max-w-2xl w-full shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto modal-scrollable-body z-10">
        <!-- رأس المودال -->
        <div class="flex justify-between items-start border-b border-slate-100 pb-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-black border border-purple-200 mb-1">
                    <span>👨‍👧‍👦</span>
                    <span>إدارة وربط الأبناء بحساب ولي الأمر</span>
                </div>
                <h3 class="font-black text-lg text-slate-900" id="parentModalName">ولي الأمر: ---</h3>
                <p class="text-xs text-slate-500" id="parentModalInfo">كود ولي الأمر: ---</p>
            </div>
            <button type="button" onclick="closeManageChildrenModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 font-bold flex items-center justify-center transition">✕</button>
        </div>

        <!-- 1. نموذج ربط طالب جديد بولي الأمر -->
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 p-5 rounded-2xl space-y-3 shadow-2xs">
            <div class="flex items-center justify-between">
                <h4 class="font-black text-xs text-purple-900 flex items-center gap-2">
                    <span>➕</span>
                    <span>ربط طالب جديد من النظام بولي الأمر هذا</span>
                </h4>
                <span class="text-[10px] text-purple-600 font-medium">سيمكنه متابعة الحضور والتقييمات والسندات</span>
            </div>

            <!-- حقل بحث وتصفية فورية لاختيار الطالب بسهولة -->
            <div class="relative">
                <input type="text" id="studentFilterInput" onkeyup="filterStudentOptions()" placeholder="🔍 ابحث بالاسم أو كود الطالب للوصول السريع..."
                    class="w-full border border-purple-200 rounded-xl px-3 py-1.5 text-xs bg-white/90 focus:bg-white outline-none focus:ring-2 focus:ring-purple-400 placeholder:text-slate-400">
            </div>

            <form id="linkStudentForm" method="POST" action="" class="flex flex-col sm:flex-row gap-2.5">
                @csrf
                <div class="flex-1">
                    <select name="student_id" id="linkStudentSelect" required class="w-full border border-purple-300 rounded-xl p-2.5 text-xs bg-white outline-none focus:ring-2 focus:ring-purple-500 shadow-2xs font-medium">
                        <option value="">-- اختر الطالب من قائمة طلاب المدرسة ({{ $allStudents->count() }} طالب) --</option>
                        @foreach($allStudents as $st)
                            <option value="{{ $st->id }}" data-search="{{ mb_strtolower($st->name . ' ' . ($st->name_en ?? '') . ' ' . ($st->user_code ?? '') . ' ' . ($st->student_code ?? '')) }}">
                                🎓 {{ $st->name }} @if($st->name_en)({{ $st->name_en }})@endif • [كود: {{ $st->user_code ?? $st->student_code }}] {{ $st->phone_whatsapp ? '• ' . $st->phone_whatsapp : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                    <span>➕</span>
                    <span>ربط الطالب بولي الأمر</span>
                </button>
            </form>
        </div>

        <!-- 2. قائمة الأبناء المرتبطين حالياً -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <h4 class="font-black text-xs text-slate-900 flex items-center gap-2">
                    <span>📋</span>
                    <span>الأبناء المرتبطون حالياً (<span id="childrenCountText">0</span>)</span>
                </h4>
                <span class="text-[11px] text-slate-400">السجل الأكاديمي والمالي الشامل 360 وإلغاء الربط</span>
            </div>

            <div id="linkedChildrenContainer" class="space-y-2.5">
                <!-- يُملأ بالجافاسكريبت -->
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-slate-100">
            <button type="button" onclick="closeManageChildrenModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                إغلاق النافذة
            </button>
        </div>
    </div>
</div>

<style>
    /* أشرطة تمرير أنيقة وواضحة تمنع الخروج عن الإطار وتعمل على كافة المتصفحات */
    .modal-scrollable-body {
        scrollbar-width: thin;
        scrollbar-color: #94a3b8 #f1f5f9;
    }
    .modal-scrollable-body::-webkit-scrollbar {
        width: 8px;
    }
    .modal-scrollable-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 9999px;
    }
    .modal-scrollable-body::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 9999px;
    }
    .modal-scrollable-body::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>

<script>
    let currentEditingUserId = null;
    let currentEditingUserName = '';
    const currentLoggedInUserId = {{ auth()->id() ?? 0 }};

    function copyUserCode(code) {
        if (!code) return;
        navigator.clipboard.writeText(code).then(() => {
            alert('تم نسخ الكود الفريد للدخول بنجاح: ' + code);
        }).catch(() => {
            prompt('انسخ الكود الفريد للدخول:', code);
        });
    }

    function generateRandomPass(inputId) {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
        let pass = '';
        for (let i = 0; i < 9; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const input = document.getElementById(inputId);
        if (input) {
            input.value = pass;
            if (inputId === 'adminNewPassInput') {
                document.getElementById('adminNewPassConf').value = pass;
            }
        }
    }

    function openAdminChangePasswordModal(user) {
        document.body.style.overflow = 'hidden';
        document.getElementById('adminChangePasswordForm').action = '/admin/users/' + user.id + '/change-password';
        document.getElementById('changePassUserName').innerText = 'تغيير كلمة المرور: ' + user.name;
        document.getElementById('changePassUserSub').innerText = 'الرقم الفريد: ' + (user.user_code || user.student_code || ('USR-' + user.id)) + ' | البريد: ' + user.email;
        generateRandomPass('adminNewPassInput');
        const isRev = (user.email === 'demo.reviewer@asots.com' || user.is_reviewer || user.role === 'reviewer');
        document.getElementById('adminForceChangeCb').checked = isRev ? false : (user.must_change_password == 1 || user.must_change_password === true);
        document.getElementById('adminChangePasswordModal').classList.remove('hidden');
    }

    function closeAdminChangePasswordModal() {
        document.getElementById('adminChangePasswordModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    let activeResetReqData = null;

    function openHandleResetModal(req) {
        activeResetReqData = req;
        document.body.style.overflow = 'hidden';
        document.getElementById('handleResetRequestForm').action = '/admin/password-requests/' + req.id + '/handle';
        document.getElementById('handleModalTitle').innerText = 'معالجة طلب استرجاع الحساب #' + req.id;
        document.getElementById('handleApplicantName').innerText = req.name || (req.user ? req.user.name : 'غير محدد');
        document.getElementById('handleApplicantId').innerText = req.identifier;
        document.getElementById('handleApplicantPhone').innerText = req.phone || (req.user ? req.user.phone_whatsapp : 'غير مسجل');
        document.getElementById('handleApplicantNotes').innerText = req.user_notes || 'نسيان كلمة المرور';
        generateRandomPass('handleNewPassInput');
        document.getElementById('handleResetRequestModal').classList.remove('hidden');
    }

    function closeHandleResetModal() {
        document.getElementById('handleResetRequestModal').classList.add('hidden');
        document.body.style.overflow = '';
        activeResetReqData = null;
    }

    function copyWhatsAppMessage() {
        if (!activeResetReqData) return;
        const name = activeResetReqData.name || (activeResetReqData.user ? activeResetReqData.user.name : 'عزيزي المشترك');
        const pass = document.getElementById('handleNewPassInput').value;
        const userCode = (activeResetReqData.user ? (activeResetReqData.user.user_code || activeResetReqData.user.student_code) : activeResetReqData.identifier);
        
        const msg = `مرحباً ${name}،\nتمت معالجة طلبك وإعادة تعيين كلمة المرور لحسابك في منصة ASOTS بنجاح ✅\n\n📌 بيانات الدخول الخاصة بك:\n- الرقم الفريد / البريد: ${userCode}\n- كلمة المرور المؤقتة: ${pass}\n\n⚠️ ملاحظة: سيلزمك النظام بتعيين كلمة مرور جديدة خاصة بك فور أول تسجيل دخول للمحافظة على أمان حسابك.\nرابط الدخول: ${window.location.origin}/login`;

        navigator.clipboard.writeText(msg).then(() => {
            alert('تم نسخ نص رسالة الواتساب الجاهزة بنجاح! يمكنك الآن لصقها وإرسالها للمستخدم مباشرة عبر الواتساب.');
        }).catch(() => {
            prompt('انسخ نص الرسالة:', msg);
        });
    }

    function openAddModal() {
        document.body.style.overflow = 'hidden';
        document.getElementById('addUserModal').classList.remove('hidden');
        onRoleChange(document.getElementById('addRoleSelect').value, 'add');
    }

    function closeAddModal() {
        document.getElementById('addUserModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    let currentEditingUserObject = null;

    function openEditModal(user) {
        currentEditingUserObject = user;
        currentEditingUserId = user.id;
        currentEditingUserName = user.name || '';

        // منع تمرير الصفحة الرئيسية في الخلفية
        document.body.style.overflow = 'hidden';

        document.getElementById('editUserForm').action = '/admin/users/' + user.id + '/update';
        document.getElementById('editModalSubtitle').innerText = 'المستخدم: ' + user.name + ' (' + user.email + ')';
        document.getElementById('editName').value = user.name || '';
        document.getElementById('editNameEn').value = user.name_en || '';
        document.getElementById('editGender').value = user.gender || 'male';
        document.getElementById('editBirthDate').value = user.birth_date ? user.birth_date.substring(0, 10) : '';
        document.getElementById('editEmail').value = user.email || '';
        document.getElementById('editPhone').value = user.phone_whatsapp || '';
        document.getElementById('editRoleSelect').value = user.role || 'supervisor';
        const pgSelect = document.getElementById('editPermissionGroupSelect');
        if (pgSelect) {
            pgSelect.value = user.permission_group_id || '';
        }
        document.getElementById('editStatusSelect').value = (user.status === 'suspended' || user.status === 'inactive') ? 'suspended' : 'active';
        const isRevEdit = (user.email === 'demo.reviewer@asots.com' || user.is_reviewer || user.role === 'reviewer');
        document.getElementById('editMustChangePass').checked = isRevEdit ? false : (user.must_change_password == 1 || user.must_change_password === true);

        // إخفاء أو ضبط زر الحذف وفق حالة الحساب وقيود العمليات
        const delBtn = document.getElementById('editDeleteBtn');
        if (delBtn) {
            if (user.id === currentLoggedInUserId) {
                delBtn.classList.add('hidden');
            } else {
                delBtn.classList.remove('hidden');
                const hasOperations = (user.can_be_deleted === false) || (user.integrity_reasons && Object.keys(user.integrity_reasons).length > 0);
                if (hasOperations) {
                    delBtn.className = "w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-300 transition flex items-center justify-center gap-1.5 shadow-2xs";
                    delBtn.innerHTML = '<span>🔒</span><span>الحذف مقيد (توجد عمليات بالنظام)</span>';
                    delBtn.onclick = function() { showBlockedDeleteModal(currentEditingUserObject); };
                } else {
                    delBtn.className = "w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-bold text-red-600 hover:text-red-700 hover:bg-red-50 border border-red-200 transition flex items-center justify-center gap-1.5 shadow-2xs";
                    delBtn.innerHTML = '<span>🗑️</span><span>حذف المستخدم نهائياً</span>';
                    delBtn.onclick = confirmDeleteUserFromModal;
                }
            }
        }

        // ضبط مربعات اختيار الصلاحيات
        const permCheckboxes = document.querySelectorAll('.edit-perm-cb');
        permCheckboxes.forEach(cb => cb.checked = false);

        if (user.role === 'admin') {
            permCheckboxes.forEach(cb => cb.checked = true);
        } else if (user.permissions) {
            if (Array.isArray(user.permissions)) {
                user.permissions.forEach(p => {
                    const el = document.getElementById('edit_perm_' + p);
                    if (el) el.checked = true;
                });
            } else if (typeof user.permissions === 'object') {
                for (const [key, val] of Object.entries(user.permissions)) {
                    const el = document.getElementById('edit_perm_' + key);
                    if (el && (val == 1 || val === true || val === '1')) {
                        el.checked = true;
                    }
                }
            }
        }

        onRoleChange(user.role, 'edit');
        document.getElementById('editUserModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editUserModal').classList.add('hidden');
        document.body.style.overflow = '';
        currentEditingUserId = null;
        currentEditingUserName = '';
        currentEditingUserObject = null;
    }

    function confirmDeleteUserFromModal() {
        if (!currentEditingUserId) return;
        if (confirm('هل أنت متأكد تماماً من حذف المستخدم (' + currentEditingUserName + ') نهائياً من النظام؟ هذا الحساب لا يملك أي عمليات سابقة وسيتم حذفه.')) {
            const form = document.getElementById('deleteUserFromModalForm');
            form.action = '/admin/users/' + currentEditingUserId;
            form.submit();
        }
    }

    // عرض نافذة تفاصيل منع الحذف لوجود عمليات سابقة
    function showBlockedDeleteModal(user) {
        if (!user) return;
        const name = user.name || user.name_ar || 'المستخدم';
        const code = user.user_code || user.student_code || '---';
        const id = user.id;
        const reasonsData = user.integrity_reasons || {};

        document.getElementById('blockedUserName').textContent = name;
        document.getElementById('blockedUserCode').textContent = code;

        const listContainer = document.getElementById('blockedReasonsList');
        listContainer.innerHTML = '';

        const reasonsArr = (typeof reasonsData === 'object') ? Object.values(reasonsData) : [];

        if (reasonsArr.length > 0) {
            reasonsArr.forEach(reason => {
                const item = document.createElement('div');
                item.className = 'flex items-center gap-2 p-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 shadow-2xs';
                item.innerHTML = '<span class="text-amber-500 font-bold shrink-0">📌</span><span>' + reason + '</span>';
                listContainer.appendChild(item);
            });
        } else {
            const item = document.createElement('div');
            item.className = 'p-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800';
            item.textContent = 'مرتبط بقيود وعمليات (سندات، دورات، حصص أو تقييمات) بالنظام.';
            listContainer.appendChild(item);
        }

        const form = document.getElementById('blockedToggleForm');
        form.action = '/admin/users/' + id + '/toggle-status';

        document.getElementById('blockedDeleteModal').classList.remove('hidden');
    }

    function closeBlockedDeleteModal() {
        document.getElementById('blockedDeleteModal').classList.add('hidden');
    }

    function onRoleChange(role, prefix) {
        const sec = document.getElementById(prefix + 'PermissionsSection');
        if (!sec) return;

        // للأدوار غير الإدارية، يمكن إخفاء الصلاحيات لتوفير المساحة
        if (role === 'student' || role === 'parent') {
            sec.classList.add('opacity-40', 'pointer-events-none');
        } else {
            sec.classList.remove('opacity-40', 'pointer-events-none');
        }
    }

    function toggleAllCheckboxes(prefix, check) {
        const cbs = document.querySelectorAll('.' + prefix + '-perm-cb');
        cbs.forEach(cb => cb.checked = check);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // تصفية فورية لقائمة الطلاب في نافذة ربط الأبناء
    function filterStudentOptions() {
        const q = (document.getElementById('studentFilterInput')?.value || '').trim().toLowerCase();
        const select = document.getElementById('linkStudentSelect');
        if (!select) return;
        const options = select.querySelectorAll('option');
        options.forEach((opt, idx) => {
            if (idx === 0) return; // الاحتفاظ بخيار التحديد الأولي
            const search = (opt.getAttribute('data-search') || opt.textContent || '').toLowerCase();
            if (!q || search.includes(q)) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    // فتح نافذة إدارة وربط أبناء ولي الأمر
    function openManageChildrenModal(user) {
        if (!user) return;
        if (typeof user === 'string') {
            try { user = JSON.parse(user); } catch (e) { console.error('Failed to parse user payload', e); }
        }
        const name = user.name || 'ولي الأمر';
        const code = user.user_code || user.student_code || ('PAR-' + user.id);
        const email = user.email || '';
        const phone = user.phone_whatsapp || '';

        document.getElementById('parentModalName').textContent = 'ولي الأمر: ' + name;
        document.getElementById('parentModalInfo').textContent = 'كود: ' + code + ' | البريد: ' + email + ' | الواتساب: ' + phone;

        const linkForm = document.getElementById('linkStudentForm');
        linkForm.action = '/admin/parents/' + user.id + '/link-student';

        // تصفير حقل البحث واختيار الطالب
        const filterInput = document.getElementById('studentFilterInput');
        if (filterInput) filterInput.value = '';
        const select = document.getElementById('linkStudentSelect');
        if (select) {
            select.value = '';
            select.querySelectorAll('option').forEach(opt => opt.style.display = '');
        }

        // جمع الأبناء المرتبطين
        let children = [];
        if (user.children && Array.isArray(user.children) && user.children.length > 0) {
            children = user.children;
        } else if (user.children_profiles && Array.isArray(user.children_profiles)) {
            children = user.children_profiles.map(cp => cp.user).filter(Boolean);
        }

        const linkedStudentIds = new Set(children.map(c => Number(c.id)));

        // تمييز الطلاب المرتبطين مسبقاً في القائمة المنسدلة
        if (select) {
            select.querySelectorAll('option').forEach((opt, idx) => {
                if (idx === 0) return;
                const optVal = Number(opt.value);
                if (linkedStudentIds.has(optVal)) {
                    opt.disabled = true;
                    if (!opt.textContent.includes('(مرتبط حالياً')) {
                        opt.textContent = opt.textContent + ' (مرتبط حالياً بهذا الحساب ✅)';
                    }
                } else {
                    opt.disabled = false;
                    opt.textContent = opt.textContent.replace(' (مرتبط حالياً بهذا الحساب ✅)', '');
                }
            });
        }

        document.getElementById('childrenCountText').textContent = children.length;
        const container = document.getElementById('linkedChildrenContainer');
        container.innerHTML = '';

        if (children.length === 0) {
            container.innerHTML = `
                <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs space-y-1">
                    <span class="text-3xl block">👶</span>
                    <p class="font-bold text-slate-700">لا يوجد أبناء مرتبطون بهذا الحساب حتى الآن.</p>
                    <p class="text-[11px] text-slate-400">اختر طالباً من القائمة بالأعلى وانقر على "ربط الطالب بولي الأمر" للبدء.</p>
                </div>
            `;
        } else {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            children.forEach(child => {
                const childCard = document.createElement('div');
                childCard.className = 'flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl hover:border-purple-300 transition shadow-2xs';
                
                const childId = child.id;
                const childCode = child.user_code || child.student_code || ('STU-' + child.id);
                const childName = child.name || 'الطالب';
                const childNameEn = child.name_en ? `<span class="text-[11px] text-slate-400 font-mono" dir="ltr">(${escapeHtml(child.name_en)})</span>` : '';
                
                let levelInfo = child.level_name || 'طالب مسجل';
                if (!child.level_name) {
                    if (child.enrollments && child.enrollments.length > 0 && child.enrollments[0]?.level?.name) {
                        levelInfo = child.enrollments[0].level.name;
                    } else if (child.study_groups && child.study_groups.length > 0 && child.study_groups[0]?.name) {
                        levelInfo = child.study_groups[0].name;
                    } else if (child.studyGroups && child.studyGroups.length > 0 && child.studyGroups[0]?.name) {
                        levelInfo = child.studyGroups[0].name;
                    }
                }

                childCard.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 font-black flex items-center justify-center shrink-0 text-sm shadow-2xs">
                            🎓
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <strong class="text-xs text-slate-900">${escapeHtml(childName)}</strong>
                                ${childNameEn}
                                <span class="font-mono text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded">${escapeHtml(childCode)}</span>
                            </div>
                            <span class="text-[11px] text-slate-500 block">المستوى / الشعبة: <strong>${escapeHtml(levelInfo)}</strong></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center">
                        <a href="/admin/reports/student/${childId}/transcript" target="_blank" 
                            class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl border border-indigo-200 text-[11px] flex items-center gap-1 transition shadow-2xs"
                            title="استعراض السجل الأكاديمي والمالي الشامل 360">
                            <span>📜</span>
                            <span>السجل 360</span>
                        </a>

                        <form method="POST" action="/admin/parents/${user.id}/unlink-student/${childId}" class="inline unlink-form">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" 
                                class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl border border-rose-200 text-[11px] flex items-center gap-1 transition"
                                title="إلغاء ربط الطالب بهذا الحساب">
                                <span>❌</span>
                                <span>إلغاء الربط</span>
                            </button>
                        </form>
                    </div>
                `;

                const form = childCard.querySelector('.unlink-form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        if (!confirm(`هل أنت متأكد من إلغاء ربط الطالب (${childName}) من حساب ولي الأمر (${name})؟`)) {
                            e.preventDefault();
                        }
                    });
                }

                container.appendChild(childCard);
            });
        }

        const modal = document.getElementById('manageChildrenModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
        document.body.style.overflow = 'hidden';
    }

    function closeManageChildrenModal() {
        const modal = document.getElementById('manageChildrenModal');
        if (modal) {
            modal.classList.add('hidden');
        }
        document.body.style.overflow = '';
    }

    // إغلاق النوافذ المنبثقة عبر زر Escape في لوحة المفاتيح
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
            closeAddModal();
            closeAdminChangePasswordModal();
            closeHandleResetModal();
            closeBlockedDeleteModal();
            closeManageChildrenModal();
        }
    });
</script>
@endsection

