@extends('layouts.app', ['title' => 'إدارة التسعير المالي والخدمات وعقود المعلمين'])

@section('content')
<div class="space-y-8">

    <!-- الترويسة العليا -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 text-xs font-black rounded-lg border border-indigo-200">
                    الإدارة المالية والتعاقدات
                </span>
                <span class="text-xs text-slate-400 font-bold">• شاشة التسعير المركزية</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900">تسعيرة الدورات والخدمات وعقود المعلمين</h1>
            <p class="text-xs text-slate-500 mt-1">
                تحديد رسوم تسجيل الطلاب، أتعاب تدريس المعلمين لكل دورة، دليل الخدمات والأنشطة، وضبط نمط التعاقد (موظف براتب أو متعاقد)
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('accounting.dashboard') }}" class="text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                <span>📊</span>
                <span>لوحة الحسابات والسندات</span>
            </a>
            <a href="{{ route('accounting.dashboard') }}#entitlementsTab" class="text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                <span>💼</span>
                <span>إثبات مستحقات المعلمين</span>
            </a>
        </div>
    </div>

    <!-- بطاقات الإحصاءات العامة -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 block">إجمالي الدورات المسعرة</span>
            <span class="text-2xl font-black text-slate-800 font-mono mt-1 block">{{ $courses->count() }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">دورات تدريبية ولغات ودبلومات</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 block">دليل الخدمات والأنشطة التعليمية</span>
            <span class="text-2xl font-black text-indigo-600 font-mono mt-1 block">{{ $services->count() }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">بنوك أسئلة، تصحيح، إشراف، ورش عمل</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 block">معلمون بنظام راتب شهري ثابت</span>
            @php $fixedCount = $teachers->filter(fn($t) => $t->teacherContract?->contract_type === 'fixed_salary')->count(); @endphp
            <span class="text-2xl font-black text-emerald-600 font-mono mt-1 block">{{ $fixedCount }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">موظفون بعقد شهري مستقر</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 block">معلمون بنظام الدورات / الساعات</span>
            @php $contractCount = $teachers->count() - $fixedCount; @endphp
            <span class="text-2xl font-black text-amber-600 font-mono mt-1 block">{{ $contractCount }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">متعاقدون بحسب الدورات المنجزة</span>
        </div>
    </div>

    <!-- حاوية التبويبات الثلاثة -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        
        <!-- أزرار التبويبات -->
        <div class="flex items-center justify-between border-b border-slate-200 pb-4 flex-wrap gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="switchMainTab('courses')" id="tabBtnCourses" class="px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-indigo-600 text-white shadow-sm">
                    <span>📚</span>
                    <span>تسعيرة الدورات والتدريس</span>
                </button>
                <button type="button" onclick="switchMainTab('services')" id="tabBtnServices" class="px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <span>⚙️</span>
                    <span>دليل الخدمات والأنشطة التعليمية</span>
                </button>
                <button type="button" onclick="switchMainTab('teachers')" id="tabBtnTeachers" class="px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <span>👨‍🏫</span>
                    <span>عقود المعلمين والمشرفين والكادر</span>
                </button>
            </div>
            <div class="flex items-center gap-2">
                <div id="courseActionBtn">
                    <button type="button" onclick="openCreateCourseModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-sm transition flex items-center gap-1.5">
                        <span>➕</span>
                        <span>إضافة دورة جديدة وتسعيرها</span>
                    </button>
                </div>

                <div id="serviceActionBtn" class="hidden">
                    <button type="button" onclick="openCreateServiceModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow-sm transition flex items-center gap-1.5">
                        <span>➕</span>
                        <span>إضافة خدمة تعليمية جديدة</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 1. تبويب تسعيرة الدورات والتدريس -->
        <div id="tabContentCourses" class="space-y-4">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="text-xs text-slate-600">
                    <span class="font-black text-slate-900 block">تسعيرة الدورات والمناهج المعتمدة:</span>
                    حدد رسوم اشتراك الطالب، وأجر المعلم المعتمد لتدريس الدورة بالكامل، وأجر الساعة التدريبية لجميع برامج ومستويات المنصة.
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto flex-wrap">
                    <select id="courseLevelFilter" onchange="filterCoursesTable()" class="text-xs px-3.5 py-2 rounded-xl border border-slate-300 outline-none focus:ring-2 focus:ring-indigo-500 bg-white font-bold text-slate-700">
                        <option value="">جميع المستويات والمناهج ({{ $courses->count() }})</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl->id }}">{{ $lvl->name_ar ?: $lvl->name }} ({{ $lvl->courses->count() }} دورات)</option>
                        @endforeach
                    </select>
                    <input type="text" id="courseSearchInput" onkeyup="filterCoursesTable()" placeholder="🔍 ابحث عن دورة أو منهج..." class="text-xs px-3.5 py-2 rounded-xl border border-slate-300 w-full sm:w-56 outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-right text-xs" id="coursesPricingTable">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3.5">#</th>
                            <th class="p-3.5">الدورة التدريبية</th>
                            <th class="p-3.5">المستوى الأكاديمي (المنهج)</th>
                            <th class="p-3.5 text-center">الساعات</th>
                            <th class="p-3.5 text-indigo-700 font-black">رسوم الطالب</th>
                            <th class="p-3.5 text-emerald-700 font-black">أجر المعلم (للدورة)</th>
                            <th class="p-3.5 text-amber-700 font-black">أجر الساعة للمدرس</th>
                            <th class="p-3.5 text-center">النسبة</th>
                            <th class="p-3.5 text-center">الحالة</th>
                            <th class="p-3.5 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($courses as $idx => $course)
                        @php
                            $pct = $course->fee_amount > 0 ? round(($course->teacher_fee / $course->fee_amount) * 100) : 0;
                            $searchKey = strtolower(($course->name_ar ?: '') . ' ' . ($course->name_en ?: '') . ' ' . ($course->name ?: '') . ' ' . ($course->level?->name_ar ?: '') . ' ' . ($course->level?->name_en ?: ''));
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition course-row" data-name="{{ $searchKey }}" data-level="{{ $course->level_id }}">
                            <td class="p-3.5 font-mono text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs shrink-0">📚</span>
                                    <div>
                                        <span class="font-black text-slate-900 block text-xs">{{ $course->name_ar ?: $course->name }}</span>
                                        @if($course->name_en)
                                            <span class="text-[11px] text-indigo-600 font-mono font-bold block">{{ $course->name_en }}</span>
                                        @endif
                                        <span class="text-[10px] text-slate-400 font-mono">معرف: #{{ $course->id }} | ترتيب: {{ $course->course_order ?: '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                @if($course->level)
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded-lg border border-blue-200 text-[11px] inline-block">
                                        {{ $course->level->name_ar ?: $course->level->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">غير محدد</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center font-mono font-bold text-slate-700">
                                {{ $course->hours ?: 20 }} س
                            </td>
                            <td class="p-3.5 font-mono font-black text-indigo-700">
                                {{ number_format($course->fee_amount ?? 0, 2) }}
                                <span class="text-[10px] font-sans text-slate-500">{{ $course->currency ?: 'YER' }}</span>
                            </td>
                            <td class="p-3.5 font-mono font-black text-emerald-700">
                                {{ number_format($course->teacher_fee ?? 0, 2) }}
                                <span class="text-[10px] font-sans text-slate-500">{{ $course->currency ?: 'YER' }}</span>
                            </td>
                            <td class="p-3.5 font-mono font-black text-amber-700">
                                {{ number_format($course->teacher_hourly_rate ?? 0, 2) }}
                                <span class="text-[10px] font-sans text-slate-500">{{ $course->currency ?: 'YER' }}</span>
                            </td>
                            <td class="p-3.5 text-center font-mono font-bold">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md text-[11px]">{{ $pct }}%</span>
                            </td>
                            <td class="p-3.5 text-center">
                                @if($course->is_active)
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[10px]">نشطة</span>
                                @else
                                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-full font-bold text-[10px]">معطلة</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <button type="button" 
                                    onclick='openEditCoursePricingModal(@json($course))' 
                                    class="px-3 py-1.5 bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 font-bold rounded-lg transition text-[11px] shadow-sm flex items-center gap-1 mx-auto">
                                    <span>✏️</span>
                                    <span>تعديل المحتوى والتسعيرة</span>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-slate-400 text-xs">
                                لا توجد دورات مسجلة في المناهج حتى الآن.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. تبويب دليل الخدمات والأنشطة التعليمية -->
        <div id="tabContentServices" class="space-y-4 hidden">
            <div class="bg-indigo-50/70 p-4 rounded-2xl border border-indigo-200 text-xs text-indigo-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <span class="font-black block mb-0.5">دليل الخدمات والأنشطة والمهام الإضافية:</span>
                    خدمات يُمكن قيدها كمستحقات مالية للمعلمين (مثل إعداد كويزات أو اختبارات، تصحيح أوراق، إشراف أكاديمي، ورش عمل، أو تدريس خصوصي).
                </div>
                <button type="button" onclick="openCreateServiceModal()" class="shrink-0 px-4 py-2 bg-indigo-700 hover:bg-indigo-800 text-white rounded-xl text-xs font-black shadow-sm transition flex items-center gap-1.5">
                    <span>➕</span>
                    <span>إضافة خدمة تعليمية</span>
                </button>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3.5">الرمز</th>
                            <th class="p-3.5">اسم الخدمة التعليمية</th>
                            <th class="p-3.5">نوع التصنيف</th>
                            <th class="p-3.5 text-emerald-700 font-black">السعر الافتراضي المعتمد</th>
                            <th class="p-3.5">وحدة الاحتساب</th>
                            <th class="p-3.5">الحالة</th>
                            <th class="p-3.5 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($services as $srv)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3.5 font-mono font-bold text-slate-500">{{ $srv->service_code }}</td>
                            <td class="p-3.5">
                                <span class="font-black text-slate-900 block">{{ $srv->name }}</span>
                                @if($srv->description)
                                    <span class="text-[11px] text-slate-400 block">{{ $srv->description }}</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg font-bold text-[11px]">
                                    {{ $srv->service_type_label }}
                                </span>
                            </td>
                            <td class="p-3.5 font-mono font-black text-emerald-700 text-sm">
                                {{ number_format($srv->default_fee, 2) }}
                                <span class="text-xs font-sans text-slate-600">{{ $srv->currency }}</span>
                            </td>
                            <td class="p-3.5 font-bold text-slate-600">{{ $srv->unit_type_label }}</td>
                            <td class="p-3.5">
                                @if($srv->is_active)
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[10px]">نشط</span>
                                @else
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full font-bold text-[10px]">معطل</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick='openEditServiceModal(@json($srv))' class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition text-[11px]">
                                        ✏️ تعديل
                                    </button>
                                    <form action="{{ route('accounting.services.destroy', $srv->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف أو تعطيل هذه الخدمة؟');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg transition text-[11px]">
                                            🗑️ حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-xs">
                                لا توجد خدمات مضافة حتى الآن. اضغط زر "إضافة خدمة تعليمية جديدة" لإضافة أول خدمة.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. تبويب عقود المعلمين وأنماط التوظيف -->
        <div id="tabContentTeachers" class="space-y-4 hidden">
            <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-200 text-xs text-amber-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <span class="font-black block mb-0.5">أنماط توظيف المعلمين والمشرفين والتعاقدات المالية:</span>
                    حدد لكل معلم أو مشرف أكاديمي هل هو موظف رسمي براتب شهري ثابت أو متعاقد بتدريس الدورات أو بالساعة أو الخدمات لإثبات المستحقات بدقة.
                </div>
                <span class="text-[11px] text-amber-700 font-bold bg-white px-3 py-1.5 rounded-xl border border-amber-200">
                    عدد المعلمين والمشرفين: {{ $teachers->count() }}
                </span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3.5">#</th>
                            <th class="p-3.5">الاسم / الدور الوظيفي</th>
                            <th class="p-3.5">نمط التعاقد المالي</th>
                            <th class="p-3.5 text-emerald-700 font-black">الراتب الشهري (للموظف)</th>
                            <th class="p-3.5 text-amber-700 font-black">أجر الساعة الافتراضي</th>
                            <th class="p-3.5 text-indigo-700 font-black">رصيد المطالبة الحالي</th>
                            <th class="p-3.5">المجموعات المسندة</th>
                            <th class="p-3.5 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($teachers as $idx => $teacher)
                        @php
                            $contract = $teacher->teacherContract;
                            $account = $teacher->financialAccount;
                            $roleBadge = match($teacher->role) {
                                'supervisor' => ['bg' => 'bg-purple-100 text-purple-700', 'label' => '🛡️ مشرف أكاديمي'],
                                'admin' => ['bg' => 'bg-rose-100 text-rose-700', 'label' => '👑 إدارة'],
                                'accountant' => ['bg' => 'bg-blue-100 text-blue-700', 'label' => '💼 محاسب'],
                                'staff' => ['bg' => 'bg-slate-100 text-slate-700', 'label' => '👔 موظف'],
                                default => ['bg' => 'bg-indigo-50 text-indigo-700', 'label' => '👨‍🏫 معلم']
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3.5 font-mono text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-700 shrink-0">
                                        {{ mb_substr($teacher->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-black text-slate-900 block">{{ $teacher->name }}</span>
                                            <span class="px-2 py-0.5 {{ $roleBadge['bg'] }} font-bold text-[10px] rounded-full">{{ $roleBadge['label'] }}</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-mono block">{{ $teacher->email }} | 📱 {{ $teacher->phone_whatsapp ?? 'لا يوجد واتساب' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                @if($contract?->contract_type === 'fixed_salary')
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-black text-[10px]">
                                        💼 موظف براتب ثابت
                                    </span>
                                @elseif($contract?->contract_type === 'hourly')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-bold text-[10px]">
                                        ⏱️ متعاقد بالساعة
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 rounded-full font-bold text-[10px]">
                                        📚 متعاقد بالدورات
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-black text-emerald-700">
                                @if($contract?->contract_type === 'fixed_salary')
                                    {{ number_format($contract->monthly_salary, 2) }} {{ $contract->currency }}
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-black text-amber-700">
                                {{ number_format($contract?->default_hourly_rate ?? 0, 2) }} {{ $contract?->currency ?? 'YER' }}
                            </td>
                            <td class="p-3.5 font-mono font-black text-indigo-700">
                                {{ number_format($account?->current_balance ?? 0, 2) }} {{ $account?->currency ?? 'YER' }}
                            </td>
                            <td class="p-3.5 font-mono text-slate-600">
                                <span class="font-bold">{{ $teacher->teachingGroups->count() }}</span> مجموعات
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" 
                                        onclick='openEditTeacherContractModal(@json($teacher), @json($contract))'
                                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition text-[11px]">
                                        📝 تعديل العقد
                                    </button>
                                    <a href="{{ route('accounting.teacher.statement', $teacher->id) }}" target="_blank" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition text-[11px]">
                                        📊 كشف الحساب
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                                لا يوجد معلمون مسجلون في النظام.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- نافذة مودال: تعديل محتوى الدورة والتسعيرة المعتمدة -->
<div id="coursePricingModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-slate-100 my-8">
        <div class="flex items-center justify-between border-b pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>✏️</span>
                    <span>تعديل محتوى الدورة والتسعيرة المعتمدة</span>
                </h3>
                <p id="modalCourseTitle" class="text-xs text-indigo-600 font-bold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeEditCoursePricingModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold">✕</button>
        </div>

        <form id="coursePricingForm" method="POST" action="" class="space-y-4 text-xs">
            @csrf
            
            <!-- المستوى الأكاديمي التابع له في المناهج -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">المستوى الأكاديمي / المنهج الحاضن <span class="text-rose-500">*</span></label>
                <select name="level_id" id="modalLevelId" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    @foreach($levels as $lvl)
                        <option value="{{ $lvl->id }}">{{ $lvl->name_ar ?: $lvl->name }} ({{ $lvl->name_en ?: 'Level' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- أسماء الدورة بالعربية والإنجليزية -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسم الدورة بالعربية <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_ar" id="modalCourseNameAr" required placeholder="مثال: المرحلة 2B أو لغة إنجليزية للأعمال" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسم الدورة بالإنجليزية</label>
                    <input type="text" name="name_en" id="modalCourseNameEn" placeholder="مثال: Level 2B or Business English" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-mono focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <!-- الساعات والعملة ورسوم اشتراك الطالب -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">ساعات الدورة <span class="text-rose-500">*</span></label>
                    <input type="number" name="hours" id="modalCourseHours" min="1" required oninput="calcHourlyFromTotal()" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-mono font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-indigo-800 mb-1">رسوم الطالب <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="fee_amount" id="modalFeeAmount" required class="w-full border border-indigo-200 bg-indigo-50/20 rounded-xl px-3.5 py-2.5 font-mono font-bold text-indigo-900 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة <span class="text-rose-500">*</span></label>
                    <select name="currency" id="modalCourseCurrency" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                        <option value="YER">🇾🇪 ريال يمني (YER)</option>
                        <option value="SAR">🇸🇦 ريال سعودي (SAR)</option>
                        <option value="USD">💵 دولار أمريكي (USD)</option>
                    </select>
                </div>
            </div>

            <!-- أجور المعلم والاحتساب الذكي -->
            <div class="p-3.5 bg-emerald-50/50 rounded-2xl border border-emerald-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-black text-emerald-900 text-xs">مستحقات وأجور تدريس المعلم:</span>
                    <button type="button" onclick="calcHourlyFromTotal()" class="text-[10px] text-emerald-700 font-bold bg-white px-2.5 py-1 rounded-lg border border-emerald-200 hover:bg-emerald-100 transition shadow-xs">
                        ⚡ احتساب أجر الساعة تلقائياً
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-emerald-900 mb-1">أجر المعلم للدورة بالكامل <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="teacher_fee" id="modalTeacherFee" required oninput="calcHourlyFromTotal()" class="w-full border border-emerald-300 bg-white rounded-xl px-3.5 py-2.5 font-mono font-black text-emerald-900 focus:ring-2 focus:ring-emerald-500 outline-none">
                        <span class="text-[10px] text-slate-500 block mt-1">يُصرف عند إنهاء تدريس المجموعة</span>
                    </div>

                    <div>
                        <label class="block font-bold text-amber-900 mb-1">أجر الساعة التدريبية للمعلم</label>
                        <input type="number" step="0.01" name="teacher_hourly_rate" id="modalTeacherHourlyRate" oninput="calcTotalFromHourly()" class="w-full border border-amber-300 bg-white rounded-xl px-3.5 py-2.5 font-mono font-black text-amber-900 focus:ring-2 focus:ring-amber-500 outline-none">
                        <span class="text-[10px] text-slate-500 block mt-1">للمتعاقدين بنظام الساعات</span>
                    </div>
                </div>
            </div>

            <!-- الوصف والحالة -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات أو وصف الدورة (اختياري)</label>
                <textarea name="description" id="modalCourseDescription" rows="2" placeholder="وصف محتوى الدورة ومتطلباتها..." class="w-full border border-slate-300 rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" id="modalCourseIsActive" value="1" class="w-4 h-4 text-indigo-600 rounded">
                    <span class="font-bold text-slate-700">دورة نشطة ومتاحة للتسجيل وإثبات المستحقات</span>
                </label>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeEditCoursePricingModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md">حفظ التعديلات والتسعيرة 💾</button>
            </div>
        </form>
    </div>
</div>

<!-- نافذة مودال: إضافة دورة تعليمية جديدة وتسعيرها في المناهج -->
<div id="courseCreateModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-slate-100 my-8">
        <div class="flex items-center justify-between border-b pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>➕</span>
                    <span>إضافة دورة تعليمية جديدة وتسعيرها</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">ستتم إضافة الدورة إلى المناهج الدراسية مع اعتماد تسعيرتها للطلاب والمعلمين</p>
            </div>
            <button type="button" onclick="closeCreateCourseModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold">✕</button>
        </div>

        <form method="POST" action="{{ route('accounting.pricing.course.store') }}" class="space-y-4 text-xs">
            @csrf
            
            <!-- المستوى الأكاديمي التابع له -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">المستوى الأكاديمي / المنهج التابع له <span class="text-rose-500">*</span></label>
                <select name="level_id" id="createLevelId" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="">-- اختر المستوى الأكاديمي --</option>
                    @foreach($levels as $lvl)
                        <option value="{{ $lvl->id }}">{{ $lvl->name_ar ?: $lvl->name }} ({{ $lvl->name_en ?: 'Level' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- أسماء الدورة بالعربية والإنجليزية -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسم الدورة بالعربية <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_ar" id="createCourseNameAr" required placeholder="مثال: دورة الترجمة التحريرية أو المستوى 1A" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسم الدورة بالإنجليزية</label>
                    <input type="text" name="name_en" id="createCourseNameEn" placeholder="مثال: Translation Course or Level 1A" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-mono focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <!-- الساعات والعملة ورسوم اشتراك الطالب -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">ساعات الدورة <span class="text-rose-500">*</span></label>
                    <input type="number" name="hours" id="createCourseHours" min="1" value="40" required oninput="calcCreateHourlyFromTotal()" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-mono font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-indigo-800 mb-1">رسوم الطالب <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="fee_amount" id="createFeeAmount" required placeholder="مثال: 25000" class="w-full border border-indigo-200 bg-indigo-50/20 rounded-xl px-3.5 py-2.5 font-mono font-bold text-indigo-900 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة <span class="text-rose-500">*</span></label>
                    <select name="currency" id="createCourseCurrency" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                        <option value="YER">🇾🇪 ريال يمني (YER)</option>
                        <option value="SAR">🇸🇦 ريال سعودي (SAR)</option>
                        <option value="USD">💵 دولار أمريكي (USD)</option>
                    </select>
                </div>
            </div>

            <!-- أجور المعلم والاحتساب الذكي -->
            <div class="p-3.5 bg-emerald-50/50 rounded-2xl border border-emerald-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-black text-emerald-900 text-xs">مستحقات وأجور تدريس المعلم:</span>
                    <button type="button" onclick="calcCreateHourlyFromTotal()" class="text-[10px] text-emerald-700 font-bold bg-white px-2.5 py-1 rounded-lg border border-emerald-200 hover:bg-emerald-100 transition shadow-xs">
                        ⚡ احتساب أجر الساعة تلقائياً
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-emerald-900 mb-1">أجر تدريس المعلم للدورة بالكامل <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="teacher_fee" id="createTeacherFee" required placeholder="مثال: 15000" oninput="calcCreateHourlyFromTotal()" class="w-full border border-emerald-300 bg-white rounded-xl px-3.5 py-2.5 font-mono font-black text-emerald-900 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-amber-900 mb-1">أجر الساعة التدريبية للمعلم</label>
                        <input type="number" step="0.01" name="teacher_hourly_rate" id="createTeacherHourlyRate" placeholder="مثال: 375" oninput="calcCreateTotalFromHourly()" class="w-full border border-amber-300 bg-white rounded-xl px-3.5 py-2.5 font-mono font-black text-amber-900 focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                </div>
            </div>

            <!-- الوصف -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات أو وصف الدورة (اختياري)</label>
                <textarea name="description" id="createCourseDescription" rows="2" placeholder="وصف محتوى الدورة ومتطلباتها..." class="w-full border border-slate-300 rounded-xl px-3.5 py-2 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeCreateCourseModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md">إضافة الدورة واعتماد التسعيرة 💾</button>
            </div>
        </form>
    </div>
</div>

<!-- نافذة مودال: إضافة / تعديل خدمة تعليمية -->
<div id="serviceModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 id="serviceModalTitle" class="text-base font-black text-slate-900">إضافة خدمة تعليمية جديدة</h3>
            <button type="button" onclick="closeServiceModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold">✕</button>
        </div>

        <form id="serviceForm" method="POST" action="{{ route('accounting.services.store') }}" class="space-y-4 text-xs">
            @csrf
            <div id="serviceMethodField"></div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">اسم الخدمة التعليمية / المهمة <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="serviceName" required placeholder="مثال: إعداد بنك أسئلة نهائي، ورشة محادثة" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">نوع التصنيف <span class="text-rose-500">*</span></label>
                    <select name="service_type" id="serviceType" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                        <option value="exam_prep">إعداد اختبار وبنك أسئلة</option>
                        <option value="grading">تصحيح ومراجعة كشوفات درجات</option>
                        <option value="supervision">إشراف أكاديمي وتنسيق</option>
                        <option value="workshop">ورشة عمل تدريبية</option>
                        <option value="tutoring">تدريس وتقوية خاصة</option>
                        <option value="content_dev">تطوير وتأليف مناهج</option>
                        <option value="other">خدمات تعليمية أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">وحدة الاحتساب <span class="text-rose-500">*</span></label>
                    <select name="unit_type" id="serviceUnitType" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                        <option value="per_exam">لكل اختبار / كويز</option>
                        <option value="per_hour">بالساعة التدريبية</option>
                        <option value="per_student">لكل طالب</option>
                        <option value="per_workshop">لكل ورشة عمل</option>
                        <option value="fixed">مبلغ مقطوع ثابت</option>
                        <option value="monthly">شهرياً</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-emerald-800 mb-1">السعر الافتراضي المعتمد <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="default_fee" id="serviceDefaultFee" required placeholder="مثال: 15000" class="w-full border border-emerald-300 bg-emerald-50/30 rounded-xl px-3.5 py-2.5 font-mono font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة <span class="text-rose-500">*</span></label>
                    <select name="currency" id="serviceCurrency" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                        <option value="YER">🇾🇪 ريال يمني (YER)</option>
                        <option value="SAR">🇸🇦 ريال سعودي (SAR)</option>
                        <option value="USD">💵 دولار أمريكي (USD)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">وصف تفصيلي للخدمة (اختياري)</label>
                <textarea name="description" id="serviceDescription" rows="2" placeholder="تفاصيل متطلبات الخدمة..." class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeServiceModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md">حفظ الخدمة 💾</button>
            </div>
        </form>
    </div>
</div>

<!-- نافذة مودال: تعديل عقد المعلم ونمط التوظيف -->
<div id="teacherContractModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between border-b pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900">تعديل العقد المالي ونمط التوظيف للمعلم</h3>
                <p id="contractTeacherName" class="text-xs text-indigo-600 font-bold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeTeacherContractModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold">✕</button>
        </div>

        <form id="teacherContractForm" method="POST" action="" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">نمط التعاقد الوظيفي <span class="text-rose-500">*</span></label>
                <select name="contract_type" id="contractTypeSelect" onchange="toggleContractFields()" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="per_course">📚 متعاقد بحسب الدورات والمجموعات (السعر المعتمد للدورة)</option>
                    <option value="fixed_salary">💼 موظف رسمي براتب شهري ثابت</option>
                    <option value="hourly">⏱️ متعاقد بنظام الساعة التدريبية</option>
                    <option value="percentage">📊 متعاقد بنسبة من الإيرادات</option>
                    <option value="services_only">⚙️ خدمات ومهام تدريبية فقط</option>
                </select>
            </div>

            <!-- حقل الراتب الشهري الثابت -->
            <div id="fixedSalaryBox" class="bg-emerald-50/50 p-3.5 rounded-2xl border border-emerald-200 space-y-2">
                <label class="block font-bold text-emerald-900">مبلغ الراتب الشهري الثابت</label>
                <input type="number" step="0.01" name="monthly_salary" id="contractMonthlySalary" placeholder="مثال: 120000" class="w-full border border-emerald-300 rounded-xl px-3.5 py-2.5 font-mono font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                <span class="text-[10px] text-emerald-700 block">يتم قيد هذا الراتب شهرياً كاستحقاق مالي للمعلم</span>
            </div>

            <!-- حقل أجر الساعة الافتراضي -->
            <div id="hourlyRateBox" class="bg-amber-50/50 p-3.5 rounded-2xl border border-amber-200 space-y-2">
                <label class="block font-bold text-amber-900">أجر الساعة التدريبية الافتراضي</label>
                <input type="number" step="0.01" name="default_hourly_rate" id="contractHourlyRate" placeholder="مثال: 2500" class="w-full border border-amber-300 rounded-xl px-3.5 py-2.5 font-mono font-bold text-amber-900 focus:ring-2 focus:ring-amber-500 outline-none bg-white">
                <span class="text-[10px] text-amber-700 block">يُطبق تلقائياً عند احتساب الساعات التدريبية</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">العملة المعتمدة للعقد <span class="text-rose-500">*</span></label>
                <select name="currency" id="contractCurrency" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="YER">🇾🇪 ريال يمني (YER)</option>
                    <option value="SAR">🇸🇦 ريال سعودي (SAR)</option>
                    <option value="USD">💵 دولار أمريكي (USD)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات وشروط إضافية للعقد</label>
                <textarea name="notes" id="contractNotes" rows="2" placeholder="أي بنود أو شروط خاصة بالمعلم..." class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeTeacherContractModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md">حفظ العقد 💾</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchMainTab(tab) {
        const cTab = document.getElementById('tabContentCourses');
        const sTab = document.getElementById('tabContentServices');
        const tTab = document.getElementById('tabContentTeachers');

        const btnC = document.getElementById('tabBtnCourses');
        const btnS = document.getElementById('tabBtnServices');
        const btnT = document.getElementById('tabBtnTeachers');
        const cBtn = document.getElementById('courseActionBtn');
        const sBtn = document.getElementById('serviceActionBtn');

        cTab.classList.add('hidden');
        sTab.classList.add('hidden');
        tTab.classList.add('hidden');
        if (cBtn) cBtn.classList.add('hidden');
        if (sBtn) sBtn.classList.add('hidden');

        btnC.className = "px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200";
        btnS.className = "px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200";
        btnT.className = "px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200";

        if (tab === 'courses') {
            cTab.classList.remove('hidden');
            if (cBtn) cBtn.classList.remove('hidden');
            btnC.className = "px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-indigo-600 text-white shadow-sm";
        } else if (tab === 'services') {
            sTab.classList.remove('hidden');
            if (sBtn) sBtn.classList.remove('hidden');
            btnS.className = "px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-emerald-600 text-white shadow-sm";
        } else if (tab === 'teachers') {
            tTab.classList.remove('hidden');
            btnT.className = "px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-amber-600 text-white shadow-sm";
        }
    }

    function filterCoursesTable() {
        const textInput = (document.getElementById('courseSearchInput').value || '').toLowerCase().trim();
        const levelFilter = document.getElementById('courseLevelFilter').value;
        const rows = document.querySelectorAll('.course-row');

        rows.forEach(r => {
            const name = (r.getAttribute('data-name') || '').toLowerCase();
            const lvl = r.getAttribute('data-level') || '';
            const matchesText = !textInput || name.includes(textInput);
            const matchesLevel = !levelFilter || lvl === levelFilter;
            if (matchesText && matchesLevel) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    // Modal: Course Pricing Edit
    function openEditCoursePricingModal(course) {
        document.getElementById('coursePricingForm').action = '/accounting/pricing/course/' + course.id;
        const courseName = course.name_ar || course.name;
        const lvlName = course.level ? (course.level.name_ar || course.level.name) : '';
        document.getElementById('modalCourseTitle').innerText = courseName + (lvlName ? ' (' + lvlName + ')' : '');
        
        document.getElementById('modalLevelId').value = course.level_id || '';
        document.getElementById('modalCourseNameAr').value = course.name_ar || course.name || '';
        document.getElementById('modalCourseNameEn').value = course.name_en || '';
        document.getElementById('modalCourseHours').value = course.hours || 40;
        document.getElementById('modalFeeAmount').value = course.fee_amount || 0;
        document.getElementById('modalTeacherFee').value = course.teacher_fee || 0;
        document.getElementById('modalTeacherHourlyRate').value = course.teacher_hourly_rate || (course.hours > 0 ? (course.teacher_fee / course.hours).toFixed(2) : 0);
        document.getElementById('modalCourseCurrency').value = course.currency || 'YER';
        document.getElementById('modalCourseDescription').value = course.description || '';
        document.getElementById('modalCourseIsActive').checked = Boolean(course.is_active);

        document.getElementById('coursePricingModal').classList.remove('hidden');
    }

    function closeEditCoursePricingModal() {
        document.getElementById('coursePricingModal').classList.add('hidden');
    }

    function calcHourlyFromTotal() {
        const hours = parseFloat(document.getElementById('modalCourseHours').value) || 0;
        const fee = parseFloat(document.getElementById('modalTeacherFee').value) || 0;
        if (hours > 0) {
            document.getElementById('modalTeacherHourlyRate').value = (fee / hours).toFixed(2);
        }
    }

    function calcTotalFromHourly() {
        const hours = parseFloat(document.getElementById('modalCourseHours').value) || 0;
        const rate = parseFloat(document.getElementById('modalTeacherHourlyRate').value) || 0;
        if (hours > 0 && rate > 0) {
            document.getElementById('modalTeacherFee').value = (rate * hours).toFixed(2);
        }
    }

    // Modal: Course Create
    function openCreateCourseModal() {
        document.getElementById('createCourseNameAr').value = '';
        document.getElementById('createCourseNameEn').value = '';
        document.getElementById('createCourseHours').value = 40;
        document.getElementById('createFeeAmount').value = '';
        document.getElementById('createTeacherFee').value = '';
        document.getElementById('createTeacherHourlyRate').value = '';
        document.getElementById('createCourseCurrency').value = 'YER';
        document.getElementById('createCourseDescription').value = '';
        document.getElementById('courseCreateModal').classList.remove('hidden');
    }

    function closeCreateCourseModal() {
        document.getElementById('courseCreateModal').classList.add('hidden');
    }

    function calcCreateHourlyFromTotal() {
        const hours = parseFloat(document.getElementById('createCourseHours').value) || 0;
        const fee = parseFloat(document.getElementById('createTeacherFee').value) || 0;
        if (hours > 0) {
            document.getElementById('createTeacherHourlyRate').value = (fee / hours).toFixed(2);
        }
    }

    function calcCreateTotalFromHourly() {
        const hours = parseFloat(document.getElementById('createCourseHours').value) || 0;
        const rate = parseFloat(document.getElementById('createTeacherHourlyRate').value) || 0;
        if (hours > 0 && rate > 0) {
            document.getElementById('createTeacherFee').value = (rate * hours).toFixed(2);
        }
    }

    // Modal: Educational Service
    function openCreateServiceModal() {
        document.getElementById('serviceForm').action = "{{ route('accounting.services.store') }}";
        document.getElementById('serviceMethodField').innerHTML = '';
        document.getElementById('serviceModalTitle').innerText = "إضافة خدمة تعليمية جديدة";
        document.getElementById('serviceName').value = '';
        document.getElementById('serviceType').value = 'exam_prep';
        document.getElementById('serviceUnitType').value = 'per_exam';
        document.getElementById('serviceDefaultFee').value = '';
        document.getElementById('serviceCurrency').value = 'YER';
        document.getElementById('serviceDescription').value = '';
        document.getElementById('serviceModal').classList.remove('hidden');
    }

    function openEditServiceModal(srv) {
        document.getElementById('serviceForm').action = '/accounting/services/' + srv.id + '/update';
        document.getElementById('serviceMethodField').innerHTML = '<input type="hidden" name="_method" value="POST">';
        document.getElementById('serviceModalTitle').innerText = "تعديل الخدمة: " + srv.name;
        document.getElementById('serviceName').value = srv.name;
        document.getElementById('serviceType').value = srv.service_type;
        document.getElementById('serviceUnitType').value = srv.unit_type;
        document.getElementById('serviceDefaultFee').value = srv.default_fee;
        document.getElementById('serviceCurrency').value = srv.currency || 'YER';
        document.getElementById('serviceDescription').value = srv.description || '';
        document.getElementById('serviceModal').classList.remove('hidden');
    }

    function closeServiceModal() {
        document.getElementById('serviceModal').classList.add('hidden');
    }

    // Modal: Teacher Contract
    function openEditTeacherContractModal(teacher, contract) {
        document.getElementById('teacherContractForm').action = '/accounting/teacher-contract/' + teacher.id;
        document.getElementById('contractTeacherName').innerText = teacher.name;
        if (contract) {
            document.getElementById('contractTypeSelect').value = contract.contract_type || 'per_course';
            document.getElementById('contractMonthlySalary').value = contract.monthly_salary || 0;
            document.getElementById('contractHourlyRate').value = contract.default_hourly_rate || 2500;
            document.getElementById('contractCurrency').value = contract.currency || 'YER';
            document.getElementById('contractNotes').value = contract.notes || '';
        } else {
            document.getElementById('contractTypeSelect').value = 'per_course';
            document.getElementById('contractMonthlySalary').value = 0;
            document.getElementById('contractHourlyRate').value = 2500;
            document.getElementById('contractCurrency').value = 'YER';
            document.getElementById('contractNotes').value = '';
        }
        toggleContractFields();
        document.getElementById('teacherContractModal').classList.remove('hidden');
    }

    function closeTeacherContractModal() {
        document.getElementById('teacherContractModal').classList.add('hidden');
    }

    function toggleContractFields() {
        const type = document.getElementById('contractTypeSelect').value;
        const fixedBox = document.getElementById('fixedSalaryBox');
        const hourlyBox = document.getElementById('hourlyRateBox');

        if (type === 'fixed_salary') {
            fixedBox.classList.remove('hidden');
            hourlyBox.classList.remove('hidden');
        } else if (type === 'hourly') {
            fixedBox.classList.add('hidden');
            hourlyBox.classList.remove('hidden');
        } else {
            fixedBox.classList.add('hidden');
            hourlyBox.classList.remove('hidden');
        }
    }

    // Hash navigation
    if (window.location.hash === '#services') {
        switchMainTab('services');
    } else if (window.location.hash === '#teachers') {
        switchMainTab('teachers');
    }
</script>
@endsection
