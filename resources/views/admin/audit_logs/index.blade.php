@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/60 py-8 px-4 sm:px-6 lg:px-8" dir="rtl">
    <div class="max-w-7xl mx-auto space-y-6">

        <!-- ========================================================================= -->
        <!-- رأس الصفحة والإجراءات السريعة -->
        <!-- ========================================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-3xl shadow-lg shadow-indigo-600/20">
                    🛡️
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            غرفة الرقابة وتدقيق العمليات
                        </h1>
                        @if($surveillance['master_enabled'])
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                                الرقابة الحية نشطة
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-300">
                                <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                الرقابة متوقفة مؤقتاً
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        توثيق زمني فوري وتدقيق شامل لكافة العمليات المالية، تغيير التسعيرات، حذف المستندات، واسم الجهاز والـ IP.
                    </p>
                </div>
            </div>

            <!-- أزرار التصدير والطباعة والأرشفة -->
            <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                <button type="button" onclick="toggleSettingsPanel()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold border border-slate-300 transition">
                    <span>⚙️</span>
                    <span>إعدادات ومفاتيح الرقابة</span>
                </button>

                <a href="{{ route('admin.audit_logs.export_csv', request()->query()) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs sm:text-sm font-bold border border-emerald-200 transition">
                    <span>📊</span>
                    <span>تصدير Excel / CSV</span>
                </a>

                <a href="{{ route('admin.audit_logs.print', request()->query()) }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs sm:text-sm font-bold border border-indigo-200 transition">
                    <span>🖨️</span>
                    <span>طباعة تقرير رسمي</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <button type="button" onclick="openCleanArchiveModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs sm:text-sm font-bold border border-rose-200 transition">
                        <span>🧹</span>
                        <span>تنظيف الأرشيف</span>
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-black">×</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 font-bold text-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-black">×</button>
            </div>
        @endif

        <!-- ========================================================================= -->
        <!-- بطاقات المؤشرات والإحصائيات الرقابية السريعة -->
        <!-- ========================================================================= -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5 sm:gap-4">
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">إجمالي العمليات الموثقة</span>
                    <span class="text-xl">📜</span>
                </div>
                <div class="mt-3 text-xl sm:text-2xl font-black text-slate-900">
                    {{ number_format($stats['total']) }}
                </div>
                <p class="text-[11px] text-slate-400 mt-1">سجل تراكمي للأمان</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">حركات اليوم الحالية</span>
                    <span class="text-xl">⚡</span>
                </div>
                <div class="mt-3 text-xl sm:text-2xl font-black text-indigo-600">
                    {{ number_format($stats['today']) }}
                </div>
                <p class="text-[11px] text-slate-400 mt-1">منذ منتصف الليل</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-rose-200 bg-rose-50/20 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-700">عمليات الحذف والإلغاء ⚠️</span>
                    <span class="text-xl">🗑️</span>
                </div>
                <div class="mt-3 text-xl sm:text-2xl font-black text-rose-700">
                    {{ number_format($stats['deletions']) }}
                </div>
                <p class="text-[11px] text-rose-500 mt-1">مستندات وحسابات محذوفة</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-emerald-200 bg-emerald-50/20 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-700">الحركات المالية والسندات</span>
                    <span class="text-xl">💳</span>
                </div>
                <div class="mt-3 text-xl sm:text-2xl font-black text-emerald-700">
                    {{ number_format($stats['finance']) }}
                </div>
                <p class="text-[11px] text-emerald-600 mt-1">سندات وصناديق ومستحقات</p>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-amber-200 bg-amber-50/20 shadow-xs col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-700">تعديلات الأسعار والمناهج</span>
                    <span class="text-xl">🏷️</span>
                </div>
                <div class="mt-3 text-xl sm:text-2xl font-black text-amber-700">
                    {{ number_format($stats['pricing']) }}
                </div>
                <p class="text-[11px] text-amber-600 mt-1">رسوم وأجور باقات ودورات</p>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- بانل مفاتيح وإعدادات الرقابة والتحكم الشامل (Collapsible Control Room) -->
        <!-- ========================================================================= -->
        <div id="settingsPanel" class="bg-white rounded-3xl border border-indigo-200 shadow-md p-6 sm:p-8 space-y-6 transition-all {{ request()->has('settings_open') ? '' : 'hidden' }}">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl">
                        ⚙️
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">
                            غرفة التحكم الإداري: تشغيل وإيقاف الرقابة على وحدات النظام
                        </h2>
                        <p class="text-xs text-slate-500">
                            يمكنك من هنا تفعيل أو إلغاء الرقابة كلياً أو التحكم بمراقبة أقسام معينة (كالمالية أو الأسعار أو المستخدمين) في أي وقت.
                        </p>
                    </div>
                </div>
                <button type="button" onclick="toggleSettingsPanel()" class="text-slate-400 hover:text-slate-700 text-lg font-black">
                    ✕
                </button>
            </div>

            <form action="{{ route('admin.audit_logs.settings.update') }}" method="POST" class="space-y-6">
                @csrf

                <!-- المفتاح الرئيسي العام (Master Switch) -->
                <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3.5">
                        <span class="text-3xl">🎛️</span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-black text-white">المفتاح الرئيسي للرقابة والتوثيق (Master Switch)</h3>
                                @if($surveillance['master_enabled'])
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">مفعل</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/20 text-rose-300 border border-rose-500/30">معطل كلياً</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-300 mt-0.5">
                                عند إيقاف هذا المفتاح، يتوقف النظام بالكامل عن تسجيل أي حركات جديدة في سجل الرقابة مهما كانت.
                            </p>
                        </div>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="audit_master_enabled" value="1" class="sr-only peer" {{ $surveillance['master_enabled'] ? 'checked' : '' }}>
                        <div class="w-14 h-7 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <!-- مفاتيح الوحدات الفردية (Modular Switches) -->
                <div>
                    <h4 class="text-xs font-black text-slate-500 uppercase tracking-wider mb-3">
                        مفاتيح مراقبة الأقسام والوحدات الفردية:
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($modules as $key => $mod)
                            @php
                                $isModActive = !empty($surveillance['modules'][$key]);
                            @endphp
                            <div class="p-4 rounded-2xl border transition-all {{ $isModActive ? 'bg-white border-slate-200 hover:border-indigo-300' : 'bg-slate-50 border-slate-200/70 opacity-70' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-2.5">
                                        <span class="text-2xl">{{ $mod['icon'] }}</span>
                                        <div>
                                            <h5 class="text-sm font-black text-slate-900">{{ $mod['name'] }}</h5>
                                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                                {{ $mod['description'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                                        <input type="checkbox" name="audit_module_{{ $key }}" value="1" class="sr-only peer" {{ $isModActive ? 'checked' : '' }}>
                                        <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- إعدادات فترة الاحتفاظ والتنظيف -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">⏳</span>
                        <div>
                            <span class="text-xs font-bold text-slate-900">مدة الاحتفاظ بالسجلات تلقائياً:</span>
                            <p class="text-[11px] text-slate-500">عدد الأيام قبل اقتراح أرشفة وتنظيف السجلات القديمة (العمليات المالية محمية دائماً)</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="audit_retention_days" value="{{ $surveillance['retention_days'] }}" min="7" max="730" class="w-24 px-3 py-1.5 rounded-xl border border-slate-200 text-sm font-bold text-center focus:ring-2 focus:ring-indigo-500 outline-none">
                        <span class="text-xs font-bold text-slate-600">يوماً</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="toggleSettingsPanel()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        إلغاء
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition flex items-center gap-2">
                        <span>💾</span>
                        <span>حفظ إعدادات الرقابة</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================================================= -->
        <!-- شريط الفلاتر والبحث المتقدم -->
        <!-- ========================================================================= -->
        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🔍</span>
                    <h3 class="text-sm font-black text-slate-900">البحث المتقدم وتصفية السجلات</h3>
                </div>
                @if(request()->hasAny(['search', 'user_id', 'module', 'action_type', 'date_from', 'date_to']))
                    <a href="{{ route('admin.audit_logs.index') }}" class="text-xs font-bold text-rose-600 hover:underline flex items-center gap-1">
                        <span>✕</span>
                        <span>تفريغ الفلاتر</span>
                    </a>
                @endif
            </div>

            <form action="{{ route('admin.audit_logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <!-- 1. الكلمة المفتاحية -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">بحث نصي (البيان، كود، IP):</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم السند، اسم المستخدم، IP..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <!-- 2. المستخدم المنفذ للعملية -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">المستخدم المنفذ:</label>
                    <select name="user_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">كافة المستخدمين</option>
                        @foreach($usersList as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->user_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. القسم / الوحدة -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">القسم / الوحدة:</label>
                    <select name="module" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="all">كافة الأقسام</option>
                        @foreach($modules as $key => $mod)
                            <option value="{{ $key }}" {{ request('module') == $key ? 'selected' : '' }}>
                                {{ $mod['icon'] }} {{ $mod['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. نوع الإجراء -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">نوع الإجراء:</label>
                    <select name="action_type" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="all">كافة الإجراءات</option>
                        <option value="create" {{ request('action_type') == 'create' ? 'selected' : '' }}>✨ إنشاء وإصدار جديد</option>
                        <option value="update" {{ request('action_type') == 'update' ? 'selected' : '' }}>✏️ تعديل بيانات</option>
                        <option value="delete" {{ request('action_type') == 'delete' ? 'selected' : '' }}>🗑️ حذف وإلغاء مستند</option>
                        <option value="pricing" {{ request('action_type') == 'pricing' ? 'selected' : '' }}>🏷️ تغيير تسعيرة</option>
                        <option value="auth" {{ request('action_type') == 'auth' ? 'selected' : '' }}>🔐 أمان ودخول</option>
                    </select>
                </div>

                <!-- 5. التاريخ من -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">من تاريخ:</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <!-- 6. التاريخ إلى -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">إلى تاريخ:</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <!-- زر الإرسال -->
                <div class="sm:col-span-2 lg:col-span-6 flex justify-end gap-2 pt-2">
                    <button type="submit" class="px-6 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                        <span>🔍</span>
                        <span>تطبيق الفلترة والبحث</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================================================= -->
        <!-- جدول سجلات الرقابة والتدقيق (Surveillance Audit Logs Table) -->
        <!-- ========================================================================= -->
        <div class="bg-white rounded-3xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">📜</span>
                    <h3 class="text-base font-black text-slate-900">سجل العمليات الرقابي</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-200 text-slate-700">
                        {{ $logs->total() }} حركة مسجلة
                    </span>
                </div>
                <div class="text-xs text-slate-500">
                    عرض {{ $logs->firstItem() ?? 0 }} إلى {{ $logs->lastItem() ?? 0 }} من أصل {{ $logs->total() }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-100/75 border-b border-slate-200 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <th class="py-3.5 px-4">التاريخ والوقت</th>
                            <th class="py-3.5 px-4">المستخدم المنفذ</th>
                            <th class="py-3.5 px-4">الجهاز والـ IP</th>
                            <th class="py-3.5 px-4">القسم ونوع الإجراء</th>
                            <th class="py-3.5 px-4 min-w-[280px]">بيان وملخص العملية</th>
                            <th class="py-3.5 px-4 text-center">القيم (قبل / بعد)</th>
                            <th class="py-3.5 px-4 text-center">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($logs as $log)
                            @php
                                $badge = $log->action_badge;
                                $modInfo = $log->module_info;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition group">
                                <!-- 1. التاريخ والوقت بدقة -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">
                                        {{ $log->formatted_date }}
                                    </div>
                                    <div class="text-[11px] font-mono text-slate-500 dir-ltr text-right">
                                        {{ $log->formatted_time }}
                                    </div>
                                </td>

                                <!-- 2. المستخدم المنفذ -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-black text-xs shrink-0">
                                                {{ mb_substr($log->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900">{{ $log->user->name }}</div>
                                                <div class="text-[10px] text-slate-500 font-mono">
                                                    [{{ $log->user->user_code }}] · {{ $log->user->role }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 text-slate-500">
                                            <span class="text-base">🤖</span>
                                            <span class="font-bold">نظام آلي / زائر</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- 3. اسم الجهاز والمتصفح والـ IP -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 text-slate-800 font-bold" title="{{ $log->device_display }}">
                                        <span>{{ $log->device_icon }}</span>
                                        <span class="text-[11px] truncate max-w-[170px]">{{ $log->device_display }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                        IP: <span class="text-slate-700 font-bold">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                                    </div>
                                </td>

                                <!-- 4. القسم ونوع الإجراء -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-black border {{ $modInfo['badge_bg'] }}">
                                            <span>{{ $modInfo['icon'] }}</span>
                                            <span>{{ $modInfo['name'] }}</span>
                                        </span>
                                        <div>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $badge['bg'] }}">
                                                <span>{{ $badge['icon'] }}</span>
                                                <span>{{ $badge['label'] }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- 5. بيان وملخص العملية -->
                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-slate-900 leading-relaxed text-xs">
                                        {{ $log->description }}
                                    </p>
                                    @if(!empty($log->details['voucher_no']))
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded bg-slate-100 text-[10px] font-mono text-slate-600">
                                            سند: {{ $log->details['voucher_no'] }}
                                        </span>
                                    @endif
                                    @if(!empty($log->details['amount']))
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded bg-emerald-50 text-[10px] font-bold text-emerald-700 border border-emerald-200">
                                            {{ number_format((float)$log->details['amount'], 2) }} {{ $log->details['currency'] ?? '' }}
                                        </span>
                                    @endif
                                </td>

                                <!-- 6. مقارنة القيم (قبل وبعد التعديل) -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($log->has_diff)
                                        <button type="button" onclick="openDiffModal({{ $log->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-black border border-indigo-200 transition shadow-xs">
                                            <span>⚖️</span>
                                            <span>عرض (قبل / بعد)</span>
                                        </button>
                                    @elseif($badge['type'] === 'delete')
                                        <button type="button" onclick="openDiffModal({{ $log->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-black border border-rose-200 transition shadow-xs">
                                            <span>🗑️</span>
                                            <span>بيانات المحذوف</span>
                                        </button>
                                    @else
                                        <span class="text-slate-300 text-xs font-bold">—</span>
                                    @endif
                                </td>

                                <!-- 7. زر فتح التفاصيل الكاملة -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" onclick="openDiffModal({{ $log->id }})" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition" title="عرض السجل الكامل">
                                        <span class="text-base">👁️</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <span class="text-4xl">🛡️</span>
                                        <p class="font-bold text-sm text-slate-700">لا توجد سجلات رقابية مطابقة للفلاتر المحددة</p>
                                        <p class="text-xs text-slate-400">جرب توسيع نطاق البحث أو تفريغ الفلاتر</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- الترقيم (Pagination) -->
            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: نافذة مقارنة القيم البصرية (قبل التعديل / بعد التعديل) والتفاصيل   -->
<!-- ========================================================================= -->
<div id="diffModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" dir="rtl">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-150">
        <!-- رأس المودال -->
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/70 sticky top-0 bg-white/95 backdrop-blur-xs z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold">
                    ⚖️
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900" id="modalTitle">
                        تفاصيل العملية ومقارنة القيم
                    </h3>
                    <p class="text-xs text-slate-500" id="modalSubtitle">
                        توثيق القيم قبل وبعد التعديل أو تفاصيل المستند المحذوف
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeDiffModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-sm">
                ✕
            </button>
        </div>

        <div class="p-6 space-y-6">
            <!-- بيان العملية -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="block text-[11px] font-bold text-slate-500 mb-1">بيان الإجراء الموثق:</span>
                <p class="font-black text-sm text-slate-900" id="modalDescription"></p>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
                    <span id="modalUser" class="font-bold text-slate-700"></span>
                    <span>•</span>
                    <span id="modalTime" class="font-mono"></span>
                </div>
            </div>

            <!-- بطاقة الجهاز والـ IP -->
            <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-xl">💻</span>
                    <div>
                        <span class="block font-bold text-slate-800" id="modalDevice"></span>
                        <span class="block text-[10px] text-slate-500 font-mono" id="modalIp"></span>
                    </div>
                </div>
                <div id="modalBadges" class="flex items-center gap-1.5"></div>
            </div>

            <!-- المقارنة البصرية: قبل وبعد التعديل (Side-by-Side Diff) -->
            <div id="diffContainer" class="space-y-4">
                <h4 class="text-xs font-black text-slate-800 flex items-center gap-2">
                    <span>🔄</span>
                    <span>مقارنة القيم والتغييرات:</span>
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- عمود الحالة السابقة (قبل التعديل / المحذوف) -->
                    <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200 space-y-3">
                        <div class="flex items-center justify-between text-rose-800 font-black text-xs pb-2 border-b border-rose-200/60">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                <span id="oldColumnTitle">القيم قبل التعديل</span>
                            </span>
                            <span class="text-[10px] bg-rose-200/50 px-2 py-0.5 rounded text-rose-900 font-bold">الحالة السابقة</span>
                        </div>
                        <div id="oldValuesList" class="space-y-2 text-xs"></div>
                    </div>

                    <!-- عمود الحالة الجديدة (بعد التعديل / الأثر) -->
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-3">
                        <div class="flex items-center justify-between text-emerald-800 font-black text-xs pb-2 border-b border-emerald-200/60">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span id="newColumnTitle">القيم بعد التعديل</span>
                            </span>
                            <span class="text-[10px] bg-emerald-200/50 px-2 py-0.5 rounded text-emerald-900 font-bold">الحالة الجديدة</span>
                        </div>
                        <div id="newValuesList" class="space-y-2 text-xs"></div>
                    </div>
                </div>

                <!-- الفارق / الأثر الحسابي إن وجد -->
                <div id="diffImpactBox" class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 hidden">
                    <span class="font-bold">📊 الأثر والفارق المحسوب: </span>
                    <span id="diffImpactText" class="font-mono font-bold"></span>
                </div>
            </div>

            <!-- كود JSON الخام الكامل (للتدقيق الأمني المتقدم) -->
            <details class="text-xs">
                <summary class="font-bold text-slate-500 cursor-pointer hover:text-slate-800">
                    عرض تفاصيل السجل الخام (JSON Payload)
                </summary>
                <pre id="modalRawJson" class="mt-2 p-3 rounded-xl bg-slate-900 text-slate-200 font-mono text-[11px] overflow-x-auto dir-ltr text-left"></pre>
            </details>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
            <button type="button" onclick="closeDiffModal()" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                إغلاق
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Modal: تنظيف وأرشفة السجلات القديمة للأمان                                 -->
<!-- ========================================================================= -->
<div id="cleanArchiveModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" dir="rtl">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-2xl font-bold">
                🧹
            </div>
            <div>
                <h3 class="text-base font-black text-slate-900">تنظيف وأرشفة السجلات القديمة</h3>
                <p class="text-xs text-slate-500">حذف السجلات غير الحساسة الأقدم من فترة محددة</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs leading-relaxed">
            <span class="font-bold">⚠️ تنبيه أمان:</span>
            سندات القبض والصرف، تسوية الصناديق، والعمليات المالية الحساسة تظل محمية ولن تُحذف لضمان سلامة النزاهة المحاسبية.
        </div>

        <form action="{{ route('admin.audit_logs.clear') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">حذف السجلات الأقدم من:</label>
                <select name="days" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500 outline-none">
                    <option value="30">30 يوماً</option>
                    <option value="60">60 يوماً</option>
                    <option value="90" selected>90 يوماً (موصى به)</option>
                    <option value="180">180 يوماً (نصف سنة)</option>
                    <option value="365">سنة كاملة</option>
                </select>
            </div>

            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700">
                <input type="checkbox" name="keep_financial" value="1" checked class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                <span class="font-bold">حماية واستثناء السندات والعمليات المالية من الحذف (موصى به دائماً)</span>
            </label>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeCleanArchiveModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition">
                    تأكيد التنظيف
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSettingsPanel() {
    const panel = document.getElementById('settingsPanel');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function openCleanArchiveModal() {
    document.getElementById('cleanArchiveModal').classList.remove('hidden');
}

function closeCleanArchiveModal() {
    document.getElementById('cleanArchiveModal').classList.add('hidden');
}

function openDiffModal(logId) {
    const modal = document.getElementById('diffModal');
    modal.classList.remove('hidden');

    // تفريغ المحتويات السابقة أثناء التحميل
    document.getElementById('modalTitle').textContent = 'جارٍ جلب تفاصيل العملية...';
    document.getElementById('modalDescription').textContent = 'تحميل...';
    document.getElementById('oldValuesList').innerHTML = '<div class="text-slate-400 py-4 text-center">جارٍ التحميل...</div>';
    document.getElementById('newValuesList').innerHTML = '<div class="text-slate-400 py-4 text-center">جارٍ التحميل...</div>';
    document.getElementById('diffImpactBox').classList.add('hidden');

    fetch(`{{ url('admin/audit-logs') }}/${logId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('modalTitle').textContent = `تفاصيل حركة التدقيق #${data.id}`;
        document.getElementById('modalSubtitle').textContent = data.action;
        document.getElementById('modalDescription').textContent = data.description;
        document.getElementById('modalUser').textContent = data.user ? `${data.user.name} (${data.user.role})` : 'النظام تلقائي';
        document.getElementById('modalTime').textContent = data.formatted_time;
        document.getElementById('modalDevice').textContent = data.device;
        document.getElementById('modalIp').textContent = `IP: ${data.ip}`;

        // الشارات
        document.getElementById('modalBadges').innerHTML = `
            <span class="px-2 py-0.5 rounded text-[10px] font-black ${data.module.badge_bg}">${data.module.name}</span>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold ${data.badge.bg}">${data.badge.label}</span>
        `;

        // إدراج القيم السابقة
        const oldBox = document.getElementById('oldValuesList');
        oldBox.innerHTML = '';
        if (data.old_values && Object.keys(data.old_values).length > 0) {
            for (const [key, val] of Object.entries(data.old_values)) {
                oldBox.innerHTML += `
                    <div class="flex items-center justify-between p-2 rounded-lg bg-white/80 border border-rose-100">
                        <span class="font-bold text-slate-600">${key}:</span>
                        <span class="font-black text-rose-700">${typeof val === 'object' ? JSON.stringify(val) : val}</span>
                    </div>
                `;
            }
        } else {
            oldBox.innerHTML = '<div class="text-slate-400 py-3 text-center text-[11px]">لا توجد بيانات سابقة مسجلة</div>';
        }

        // إدراج القيم الجديدة
        const newBox = document.getElementById('newValuesList');
        newBox.innerHTML = '';
        if (data.new_values && Object.keys(data.new_values).length > 0) {
            for (const [key, val] of Object.entries(data.new_values)) {
                newBox.innerHTML += `
                    <div class="flex items-center justify-between p-2 rounded-lg bg-white/80 border border-emerald-100">
                        <span class="font-bold text-slate-600">${key}:</span>
                        <span class="font-black text-emerald-700">${typeof val === 'object' ? JSON.stringify(val) : val}</span>
                    </div>
                `;
            }
        } else {
            newBox.innerHTML = '<div class="text-slate-400 py-3 text-center text-[11px]">لا توجد بيانات جديدة معدلة</div>';
        }

        // صندوق الفارق والأثر
        if (data.diff) {
            const impactBox = document.getElementById('diffImpactBox');
            impactBox.classList.remove('hidden');
            let diffStr = '';
            if (typeof data.diff === 'object') {
                diffStr = Object.entries(data.diff).map(([k, v]) => `${k}: ${v}`).join(' | ');
            } else {
                diffStr = data.diff;
            }
            document.getElementById('diffImpactText').textContent = diffStr;
        }

        // تفاصيل JSON
        document.getElementById('modalRawJson').textContent = JSON.stringify(data.details, null, 2);
    })
    .catch(err => {
        document.getElementById('oldValuesList').innerHTML = '<div class="text-rose-500 py-3 text-center">تعذر تحميل التفاصيل</div>';
        document.getElementById('newValuesList').innerHTML = '<div class="text-rose-500 py-3 text-center">تعذر تحميل التفاصيل</div>';
    });
}

function closeDiffModal() {
    document.getElementById('diffModal').classList.add('hidden');
}

// إغلاق المودال بالضغط على زر الهروب ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDiffModal();
        closeCleanArchiveModal();
    }
});
</script>
@endsection
