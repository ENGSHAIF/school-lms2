@extends('layouts.app', ['title' => 'مركز التقارير والإحصائيات الشامل'])

@section('content')
<div class="space-y-6">

    <!-- ترويسة الصفحة والأزرار الإدارية السريعة -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="p-2.5 bg-amber-500/20 text-amber-400 rounded-2xl text-2xl border border-amber-500/30">📊</span>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">مركز التقارير والإحصائيات الشامل</h1>
                        <p class="text-xs sm:text-sm text-slate-300">متابعة إجمالية وتفصيلية للمجموعات، الدورات والتسعيرة، الصناديق والسندات، وكشوفات الحسابات</p>
                    </div>
                </div>
            </div>

            <!-- أزرار التقارير الفورية الأكثر طلباً للإدارة (One-Click Presets) -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.reports.index', ['preset' => 'active_groups']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm {{ $tab === 'groups' && request('group_status') === 'active' ? 'bg-purple-600 text-white ring-2 ring-purple-400' : 'bg-slate-800 hover:bg-slate-700 text-purple-200 border border-slate-700' }}">
                    <span>👥</span>
                    <span>المجموعات النشطة</span>
                </a>

                <a href="{{ route('admin.reports.term.print') }}" target="_blank"
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm bg-indigo-800 hover:bg-indigo-700 text-indigo-100 border border-indigo-700">
                    <span>🗓️</span>
                    <span>كشف طلاب الترم والمقارنة</span>
                </a>

                <a href="{{ route('admin.reports.index', ['preset' => 'courses_pricing']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm {{ $tab === 'courses' ? 'bg-amber-600 text-white ring-2 ring-amber-400' : 'bg-slate-800 hover:bg-slate-700 text-amber-200 border border-slate-700' }}">
                    <span>📚</span>
                    <span>تسعيرة الدورات للمراجعة</span>
                </a>

                <a href="{{ route('admin.reports.index', ['preset' => 'vouchers_cash']) }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm {{ $tab === 'vouchers' ? 'bg-emerald-600 text-white ring-2 ring-emerald-400' : 'bg-slate-800 hover:bg-slate-700 text-emerald-200 border border-slate-700' }}">
                    <span>🧾</span>
                    <span>حركة الصناديق والسندات</span>
                </a>

                @php
                    $currentPrintType = match($tab) {
                        'courses' => 'courses',
                        'vouchers' => 'vouchers',
                        'students' => 'students',
                        default => 'groups'
                    };
                    $printParams = array_merge(request()->all(), ['type' => $currentPrintType]);
                @endphp
                <a href="{{ route('admin.reports.print', $printParams) }}" target="_blank"
                   class="px-4 py-2 rounded-xl text-xs font-black bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 transition flex items-center gap-1.5 shadow-lg shadow-amber-500/20">
                    <span>🖨️</span>
                    <span>طباعة التقرير الحالي</span>
                </a>
            </div>
        </div>

        <!-- شريط المؤشرات الرقمية السريعة (Executive KPIs) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mt-6 pt-6 border-t border-slate-800/80">
            <div class="bg-slate-800/60 rounded-2xl p-3 border border-slate-700/50">
                <span class="text-[11px] font-bold text-slate-400 block mb-1">إجمالي الطلاب</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black text-white">{{ $kpis['total_students'] }}</span>
                    <span class="text-[10px] text-emerald-400 font-bold">({{ $kpis['active_students'] }} نشط)</span>
                </div>
            </div>

            <div class="bg-slate-800/60 rounded-2xl p-3 border border-slate-700/50">
                <span class="text-[11px] font-bold text-slate-400 block mb-1">المجموعات الدراسية</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black text-purple-400">{{ $kpis['total_groups'] }}</span>
                    <span class="text-[10px] text-purple-300 font-bold">({{ $kpis['active_groups'] }} نشطة)</span>
                </div>
            </div>

            <div class="bg-slate-800/60 rounded-2xl p-3 border border-slate-700/50">
                <span class="text-[11px] font-bold text-slate-400 block mb-1">الدورات والمستويات</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black text-amber-400">{{ $kpis['total_courses'] }}</span>
                    <span class="text-[10px] text-amber-300 font-bold">({{ $kpis['total_levels'] }} مستوى)</span>
                </div>
            </div>

            <div class="bg-slate-800/60 rounded-2xl p-3 border border-slate-700/50">
                <span class="text-[11px] font-bold text-slate-400 block mb-1">الكادر التعليمي</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-black text-blue-400">{{ $kpis['total_teachers'] }}</span>
                    <span class="text-[10px] text-blue-300 font-bold">معلم / {{ $kpis['total_supervisors'] }} مشرف</span>
                </div>
            </div>

            <div class="bg-slate-800/60 rounded-2xl p-3 border border-slate-700/50">
                <span class="text-[11px] font-bold text-slate-400 block mb-1">رصيد الصندوق النقدي</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-lg font-black text-emerald-400">{{ number_format($kpis['cash_box_balance'], 0) }}</span>
                    <span class="text-[10px] text-slate-300">YER</span>
                </div>
            </div>

            <div class="bg-slate-800/60 rounded-2xl p-3 border border-slate-700/50">
                <span class="text-[11px] font-bold text-slate-400 block mb-1">إجمالي المقبوضات</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-lg font-black text-teal-400">{{ number_format($kpis['receipts_sum'], 0) }}</span>
                    <span class="text-[10px] text-teal-300 font-bold">({{ $kpis['receipts_count'] }} سند)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- أشرطة التبويبات الرئيسية (Tabs Navigation) -->
    <div class="bg-white rounded-2xl p-2 border border-slate-200 shadow-sm overflow-x-auto">
        <div class="flex items-center gap-1.5 min-w-max">
            <a href="{{ route('admin.reports.index', ['tab' => 'overview']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'overview' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <span>📊</span>
                <span>نظرة عامة وإحصائيات</span>
            </a>

            <a href="{{ route('admin.reports.index', ['tab' => 'groups']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'groups' ? 'bg-purple-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <span>👥</span>
                <span>المجموعات الدراسية (نشطة / مغلقة / نطاق)</span>
            </a>

            <a href="{{ route('admin.reports.index', ['tab' => 'courses']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'courses' ? 'bg-amber-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <span>📚</span>
                <span>الدورات والتسعيرة للمراجعة</span>
            </a>

            <a href="{{ route('admin.reports.index', ['tab' => 'vouchers']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'vouchers' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <span>🧾</span>
                <span>السندات والصناديق (من رقم إلى رقم)</span>
            </a>

            <a href="{{ route('admin.reports.index', ['tab' => 'students']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'students' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <span>🎓</span>
                <span>الطلاب وحالة التسكين</span>
            </a>

            <a href="{{ route('admin.reports.index', ['tab' => 'staff']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'staff' ? 'bg-teal-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <span>👨‍🏫</span>
                <span>الكادر التعليمي والمستخدمين</span>
            </a>

            <a href="{{ route('admin.reports.index', ['tab' => 'statements']) }}" 
               class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $tab === 'statements' ? 'bg-slate-800 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                <span>💳</span>
                <span>كشوفات الحساب والأرصدة</span>
            </a>
        </div>
    </div>

    <!-- قسم الفلاتر التفاعلية (Dynamic Filter Box) -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-base">🔍</span>
                    <span class="text-sm font-black text-slate-800">خيارات الفلترة المتقدمة</span>
                    <span class="text-xs text-slate-400">| تخصيص التقرير والبحث الدقيق</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reports.index', ['tab' => $tab]) }}" class="text-xs font-bold text-slate-500 hover:text-red-500 transition">
                        إعادة تعيين الفلاتر ↺
                    </a>
                </div>
            </div>

            <!-- فلاتر تبويب المجموعات الدراسية -->
            @if($tab === 'groups' || $tab === 'overview')
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">الترم / الفصل الدراسي</label>
                        <select name="term" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-purple-500 focus:ring focus:ring-purple-200">
                            <option value="">كافة الأترام</option>
                            @foreach($allTerms as $t)
                                <option value="{{ $t }}" {{ request('term') == $t ? 'selected' : '' }}>🗓️ {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">حالة المجموعات</label>
                        <select name="group_status" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-purple-500 focus:ring focus:ring-purple-200">
                            <option value="all" {{ request('group_status') === 'all' ? 'selected' : '' }}>جميع المجموعات (الكل)</option>
                            <option value="active" {{ request('group_status', 'all') === 'active' ? 'selected' : '' }}>المجموعات النشطة فقط 🟢</option>
                            <option value="closed" {{ request('group_status') === 'closed' ? 'selected' : '' }}>المجموعات المغلقة / المكتملة ⚪</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">الدورة التعليمية</label>
                        <select name="course_id" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-purple-500 focus:ring focus:ring-purple-200">
                            <option value="">كافة الدورات</option>
                            @foreach($allCourses as $c)
                                <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">المعلم المسؤول</label>
                        <select name="teacher_id" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-purple-500 focus:ring focus:ring-purple-200">
                            <option value="">كافة المعلمين</option>
                            @foreach($allTeachers as $t)
                                <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">المشرف الأكاديمي</label>
                        <select name="supervisor_id" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-purple-500 focus:ring focus:ring-purple-200">
                            <option value="">كافة المشرفين</option>
                            @foreach($allSupervisors as $s)
                                <option value="{{ $s->id }}" {{ request('supervisor_id') == $s->id ? 'selected' : '' }}>🛡️ {{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">من مجموعة رقم (#ID)</label>
                        <input type="number" name="from_group" value="{{ request('from_group') }}" placeholder="مثال: 1" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-purple-500 focus:ring focus:ring-purple-200">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">إلى مجموعة رقم (#ID)</label>
                        <input type="number" name="to_group" value="{{ request('to_group') }}" placeholder="مثال: 10" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-purple-500 focus:ring focus:ring-purple-200">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">بحث سريع</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم المجموعة، الدفعة..." class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-purple-500 focus:ring focus:ring-purple-200">
                    </div>
                </div>
            @endif

            <!-- فلاتر تبويب الدورات والتسعيرة -->
            @if($tab === 'courses')
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">حالة الدورة</label>
                        <select name="course_status" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-amber-500 focus:ring focus:ring-amber-200">
                            <option value="all" {{ request('course_status') === 'all' ? 'selected' : '' }}>كافة الدورات</option>
                            <option value="active" {{ request('course_status') === 'active' ? 'selected' : '' }}>الدورات النشطة فقط</option>
                            <option value="inactive" {{ request('course_status') === 'inactive' ? 'selected' : '' }}>الدورات المعطلة</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">الدورة المحددة</label>
                        <select name="course_id" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-amber-500 focus:ring focus:ring-amber-200">
                            <option value="">جميع الدورات</option>
                            @foreach($allCourses as $c)
                                <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-black bg-amber-600 hover:bg-amber-700 text-white shadow-md transition">
                            عرض تسعيرة الدورات 🔍
                        </button>
                    </div>
                </div>
            @endif

            <!-- فلاتر تبويب السندات والصناديق (من رقم إلى رقم ومن تاريخ إلى تاريخ) -->
            @if($tab === 'vouchers')
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">نوع السند</label>
                        <select name="voucher_type" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            <option value="all" {{ request('voucher_type') === 'all' ? 'selected' : '' }}>كافة السندات (قبض وصرف)</option>
                            <option value="receipt" {{ request('voucher_type') === 'receipt' ? 'selected' : '' }}>سندات قبض فقط 🟢</option>
                            <option value="payment" {{ request('voucher_type') === 'payment' ? 'selected' : '' }}>سندات صرف فقط 🔴</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">من رقم سند (From #)</label>
                        <input type="text" name="voucher_from" value="{{ request('voucher_from') }}" placeholder="مثال: 1 أو REC-00001" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">إلى رقم سند (To #)</label>
                        <input type="text" name="voucher_to" value="{{ request('voucher_to') }}" placeholder="مثال: 10 أو REC-00010" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">طريقة الدفع</label>
                        <select name="payment_method" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                            <option value="all">كافة الطرق</option>
                            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>نقداً كاش</option>
                            <option value="kuraimi" {{ request('payment_method') === 'kuraimi' ? 'selected' : '' }}>الكريمي</option>
                            <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        </select>
                    </div>
                </div>
            @endif

            <!-- فلاتر تبويب الطلاب -->
            @if($tab === 'students')
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">الترم الدراسي</label>
                        <select name="term" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">كافة الأترام</option>
                            @foreach($allTerms as $t)
                                <option value="{{ $t }}" {{ request('term') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">الدورة التدريبية</label>
                        <select name="course_id" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">كافة الدورات</option>
                            @foreach($allCourses as $c)
                                <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">معلم الطالب</label>
                        <select name="teacher_id" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">كافة المعلمين</option>
                            @foreach($allTeachers as $t)
                                <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">حالة التسكين والدراسة</label>
                        <select name="student_status" class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="all" {{ request('student_status') === 'all' ? 'selected' : '' }}>كافة الطلاب</option>
                            <option value="in_group" {{ request('student_status') === 'in_group' ? 'selected' : '' }}>مسكن في مجموعة 👥</option>
                            <option value="not_in_group" {{ request('student_status') === 'not_in_group' ? 'selected' : '' }}>بانتظار التسكين ⏳</option>
                            <option value="active" {{ request('student_status') === 'active' ? 'selected' : '' }}>الحسابات النشطة ✅</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">نطاق الكود (من - إلى ID)</label>
                        <div class="flex items-center gap-1">
                            <input type="number" name="from_id" value="{{ request('from_id') }}" placeholder="من" class="w-1/2 text-xs font-semibold rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <input type="number" name="to_id" value="{{ request('to_id') }}" placeholder="إلى" class="w-1/2 text-xs font-semibold rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">بحث بالاسم أو الهاتف</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="الاسم، الهاتف، أو الكود..." class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="submit" class="px-6 py-2 rounded-xl text-xs font-black bg-slate-900 hover:bg-slate-800 text-white shadow-md transition flex items-center gap-1.5">
                    <span>⚡</span>
                    <span>تطبيق الفلترة وعرض النتائج</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================== -->
    <!-- التبويب 1: نظرة عامة وإحصائيات (Overview) -->
    <!-- ========================================== -->
    @if($tab === 'overview')
        <div class="space-y-6">
            <!-- بطاقات المؤشرات المالية والنقدية للمؤسسة -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-emerald-600 to-teal-800 rounded-3xl p-6 text-white shadow-lg">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-xs font-bold text-emerald-200 uppercase tracking-wider">الصندوق الرئيسي النقدية (Cash Box)</span>
                        <span class="p-2 bg-emerald-500/30 rounded-xl text-lg">💵</span>
                    </div>
                    <div class="text-3xl font-black mb-1">
                        {{ number_format($kpis['cash_box_balance'], 2) }} <span class="text-sm font-bold text-emerald-200">YER</span>
                    </div>
                    <p class="text-xs text-emerald-100">رصيد النقدية الجاهزة بالصندوق الداخلي للمنصة</p>
                </div>

                <div class="bg-gradient-to-br from-indigo-600 to-blue-800 rounded-3xl p-6 text-white shadow-lg">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-xs font-bold text-indigo-200 uppercase tracking-wider">إجمالي حركة المقبوضات (Receipts)</span>
                        <span class="p-2 bg-indigo-500/30 rounded-xl text-lg">🧾</span>
                    </div>
                    <div class="text-3xl font-black mb-1">
                        {{ number_format($kpis['receipts_sum'], 2) }} <span class="text-sm font-bold text-indigo-200">YER</span>
                    </div>
                    <p class="text-xs text-indigo-100">إجمالي سندات القبض الصادرة ({{ $kpis['receipts_count'] }} سند)</p>
                </div>

                <div class="bg-gradient-to-br from-purple-700 to-indigo-900 rounded-3xl p-6 text-white shadow-lg">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-xs font-bold text-purple-200 uppercase tracking-wider">صافي حركة السيولة (Net Cash Flow)</span>
                        <span class="p-2 bg-purple-500/30 rounded-xl text-lg">📈</span>
                    </div>
                    <div class="text-3xl font-black mb-1">
                        {{ number_format($kpis['net_vouchers_cash'], 2) }} <span class="text-sm font-bold text-purple-200">YER</span>
                    </div>
                    <p class="text-xs text-purple-100">فارق المقبوضات عن المصروفات المقيدة بالسندات</p>
                </div>
            </div>

            <!-- جدول ملخص المجموعات النشطة السريع -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-800">المجموعات الدراسية النشطة حالياً</h2>
                        <p class="text-xs text-slate-500">متابعة إشغال المقاعد والمعلمين والبث الحي</p>
                    </div>
                    <a href="{{ route('admin.reports.index', ['tab' => 'groups', 'group_status' => 'active']) }}" class="text-xs font-bold text-purple-600 hover:text-purple-700">
                        عرض تقرير المجموعات المفصل ←
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3">#ID</th>
                                <th class="p-3">اسم المجموعة</th>
                                <th class="p-3">الدورة والمستوى</th>
                                <th class="p-3">المعلم المسؤول</th>
                                <th class="p-3">المقاعد والطلاب</th>
                                <th class="p-3">التسعيرة</th>
                                <th class="p-3">البث الحي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($groupsData['groups'] as $grp)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-3 font-bold text-slate-500">#{{ $grp->id }}</td>
                                    <td class="p-3 font-bold text-slate-900">{{ $grp->name }}</td>
                                    <td class="p-3 text-slate-600">
                                        <span class="block font-medium text-slate-800">{{ $grp->course?->name }}</span>
                                        <span class="text-[11px] text-slate-500">{{ $grp->level?->name }}</span>
                                    </td>
                                    <td class="p-3 font-medium text-slate-700">{{ $grp->teacher?->name ?? 'غير محدد' }}</td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900">{{ $grp->students->count() }}/{{ $grp->max_students }}</span>
                                            <div class="w-16 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                @php $pct = $grp->max_students > 0 ? min(100, round(($grp->students->count() / $grp->max_students) * 100)) : 0; @endphp
                                                <div class="h-full bg-purple-600" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3 font-bold text-amber-700">{{ $grp->level?->formatted_fee ?? '-' }}</td>
                                    <td class="p-3">
                                        @if($grp->is_live)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> مباشر الآن
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-medium text-[11px]">متوقف</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-slate-400">لا توجد مجموعات مسجلة حالياً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- التبويب 2: تقرير المجموعات الدراسية (Study Groups Report) -->
    <!-- ======================================================== -->
    @if($tab === 'groups')
        <div class="space-y-4">
            <!-- شريط ملخص بيانات المجموعات المعروضة -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-purple-50 p-4 rounded-2xl border border-purple-100">
                    <span class="text-xs font-bold text-purple-600 block">المجموعات المفروزة</span>
                    <span class="text-2xl font-black text-purple-900">{{ $groupsData['total_groups'] }}</span>
                </div>
                <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100">
                    <span class="text-xs font-bold text-indigo-600 block">إجمالي المقاعد والطلاب</span>
                    <span class="text-2xl font-black text-indigo-900">{{ $groupsData['total_enrolled'] }} <span class="text-xs font-bold text-indigo-500">/ {{ $groupsData['total_capacity'] }}</span></span>
                </div>
                <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                    <span class="text-xs font-bold text-emerald-600 block">المقاعد الشاغرة المتاحة</span>
                    <span class="text-2xl font-black text-emerald-900">{{ $groupsData['total_available_seats'] }}</span>
                </div>
                <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100">
                    <span class="text-xs font-bold text-amber-600 block">نسبة الإشغال الكلية</span>
                    <span class="text-2xl font-black text-amber-900">{{ $groupsData['occupancy_rate'] }}%</span>
                </div>
            </div>

            <!-- جدول المجموعات التفصيلي -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <h2 class="text-base font-black text-slate-900">تقرير المجموعات الدراسية والدفعات التفصيلي</h2>
                        <p class="text-xs text-slate-500">عرض المجموعات حسب الحالة، المعلم، المستوى، والتسعيرة الرسمية المقررة</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.groups.index') }}" class="text-xs font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 px-3 py-1.5 rounded-xl border border-purple-200 transition">
                            إدارة المجموعات ⚙️
                        </a>
                        <a href="{{ route('admin.reports.print', array_merge(request()->all(), ['type' => 'groups'])) }}" target="_blank" class="text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-xl transition flex items-center gap-1">
                            <span>🖨️</span>
                            <span>طباعة هذا الكشف</span>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3.5">#ID</th>
                                <th class="p-3.5">اسم المجموعة</th>
                                <th class="p-3.5">الدورة والمستوى التعليمي</th>
                                <th class="p-3.5">التسعيرة المعتمدة للمستوى</th>
                                <th class="p-3.5">الأستاذ والمشرف</th>
                                <th class="p-3.5">المقاعد والشاغر</th>
                                <th class="p-3.5">المواعيد والأيام</th>
                                <th class="p-3.5">البث الحي</th>
                                <th class="p-3.5">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($groupsData['groups'] as $grp)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-3.5 font-bold text-slate-500">#{{ $grp->id }}</td>
                                    <td class="p-3.5">
                                        <span class="font-black text-slate-900 block">{{ $grp->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $grp->room_id }}</span>
                                    </td>
                                    <td class="p-3.5">
                                        <span class="font-bold text-indigo-700 block">{{ $grp->course?->name }}</span>
                                        <span class="text-slate-500 text-[11px]">{{ $grp->level?->name }}</span>
                                    </td>
                                    <td class="p-3.5 font-black text-amber-700 text-sm">
                                        {{ $grp->level?->formatted_fee ?? 'غير محدد' }}
                                    </td>
                                    <td class="p-3.5">
                                        <span class="font-bold text-slate-800 block">👨‍🏫 {{ $grp->teacher?->name ?? 'غير محدد' }}</span>
                                        <span class="text-slate-500 text-[11px]">🛡️ {{ $grp->supervisor?->name ?? 'غير محدد' }}</span>
                                    </td>
                                    <td class="p-3.5">
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between text-[11px] font-bold">
                                                <span class="text-slate-700">{{ $grp->students->count() }} مسجل</span>
                                                <span class="text-emerald-600">{{ max(0, $grp->max_students - $grp->students->count()) }} شاغر</span>
                                            </div>
                                            <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                @php $pct = $grp->max_students > 0 ? min(100, round(($grp->students->count() / $grp->max_students) * 100)) : 0; @endphp
                                                <div class="h-full {{ $pct >= 100 ? 'bg-rose-500' : 'bg-purple-600' }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3.5 text-slate-600">
                                        <span class="block font-medium">{{ $grp->schedule_days ?? 'مرن' }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $grp->schedule_time ?? '-' }}</span>
                                    </td>
                                    <td class="p-3.5">
                                        @if($grp->is_live)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> مباشر الآن
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-medium">متوقف</span>
                                        @endif
                                    </td>
                                    <td class="p-3.5">
                                        @if($grp->status === 'active')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                نشطة 🟢
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                                مغلقة / مكتملة ⚪
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-8 text-center text-slate-400">
                                        لا توجد مجموعات تطابق معايير الفلترة المحددة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ================================================================ -->
    <!-- التبويب 3: تقرير الدورات والتسعيرة والمناهج (Courses & Pricing) -->
    <!-- ================================================================ -->
    @if($tab === 'courses')
        <div class="space-y-6">
            <!-- شريط معلومات تسعيرة الدورات -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100">
                    <span class="text-xs font-bold text-amber-600 block">إجمالي البرامج والدورات</span>
                    <span class="text-2xl font-black text-amber-900">{{ $coursesData['total_courses'] }}</span>
                </div>
                <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100">
                    <span class="text-xs font-bold text-indigo-600 block">المستويات التعليمية الرسمية</span>
                    <span class="text-2xl font-black text-indigo-900">{{ $coursesData['total_levels'] }}</span>
                </div>
                <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                    <span class="text-xs font-bold text-emerald-600 block">إجمالي الحصص والمقررات</span>
                    <span class="text-2xl font-black text-emerald-900">{{ $coursesData['total_lessons'] }}</span>
                </div>
                <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                    <span class="text-xs font-bold text-blue-600 block">باقات الحصص الإضافية</span>
                    <span class="text-2xl font-black text-blue-900">{{ $coursesData['packages']->count() }}</span>
                </div>
            </div>

            <!-- جدول الدورات والمستويات والتسعيرة للمراجعة الإدارية -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <h2 class="text-base font-black text-slate-900">تقرير تسعيرة الدورات والمستويات للمراجعة الإدارية</h2>
                        <p class="text-xs text-slate-500">كشف شامل لكافة المقررات والمستويات ورسوم التسجيل المعتمدة بالريال اليمني والدولار</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.courses.index') }}" class="text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-1.5 rounded-xl border border-amber-200 transition">
                            إدارة المناهج والأسعار ⚙️
                        </a>
                        <a href="{{ route('admin.reports.print', ['type' => 'courses']) }}" target="_blank" class="text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-xl transition flex items-center gap-1">
                            <span>🖨️</span>
                            <span>طباعة تسعيرة الدورات</span>
                        </a>
                    </div>
                </div>

                <div class="divide-y divide-slate-200">
                    @forelse($coursesData['courses'] as $course)
                        <div class="p-5 hover:bg-slate-50/50 transition">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-black text-slate-900">{{ $course->name }}</span>
                                        @if($course->is_active)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">منشورة ونشطة</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">مسودة / معطلة</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $course->description ?? 'لا يوجد وصف مدخل للدورة' }}</p>
                                </div>
                                <div class="flex items-center gap-3 text-xs">
                                    <span class="px-3 py-1 rounded-xl bg-purple-50 text-purple-700 font-bold border border-purple-100">
                                        {{ $course->studyGroups->count() }} مجموعة تابعة
                                    </span>
                                    <span class="px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 font-bold border border-indigo-100">
                                        {{ $course->levels->count() }} مستويات
                                    </span>
                                </div>
                            </div>

                            <!-- مستويات الدورة وتسعيرتها -->
                            <div class="overflow-x-auto bg-slate-50 rounded-xl p-3 border border-slate-200/80 mt-3">
                                <table class="w-full text-right text-xs">
                                    <thead>
                                        <tr class="text-slate-500 font-bold border-b border-slate-200">
                                            <th class="pb-2">المستوى</th>
                                            <th class="pb-2">الترتيب</th>
                                            <th class="pb-2">رسوم المستوى والتسعيرة الرسمية</th>
                                            <th class="pb-2">الدروس والحصص</th>
                                            <th class="pb-2">الكتب والمراجع</th>
                                            <th class="pb-2">حالة المستوى</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200/60 font-medium">
                                        @forelse($course->levels as $lvl)
                                            <tr>
                                                <td class="py-2.5 font-bold text-slate-900">{{ $lvl->name }}</td>
                                                <td class="py-2.5 text-slate-600">المستوى {{ $lvl->level_order }}</td>
                                                <td class="py-2.5 font-black text-amber-700 text-sm">
                                                    {{ $lvl->formatted_fee }}
                                                </td>
                                                <td class="py-2.5 text-slate-600">{{ $lvl->lessons->count() }} حصة</td>
                                                <td class="py-2.5 text-slate-600">{{ $lvl->books->count() }} كتاب</td>
                                                <td class="py-2.5">
                                                    @if($lvl->is_active)
                                                        <span class="text-emerald-700 font-bold text-[11px]">متاح للتسجيل ✅</span>
                                                    @else
                                                        <span class="text-slate-400 font-bold text-[11px]">معلق</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-3 text-center text-slate-400">لا توجد مستويات مدخلة لهذه الدورة بعد.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">لا توجد دورات مسجلة بالنظام.</div>
                    @endforelse
                </div>
            </div>

            <!-- جدول باقات الحصص الإضافية (Packages Pricing) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-base font-black text-slate-900">تسعيرة باقات الحصص التفاعلية (25 دقيقة)</h2>
                        <p class="text-xs text-slate-500">الباقات الفردية الترويجية للحصص المباشرة مع المعلمين</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3">#ID</th>
                                <th class="p-3">اسم الباقة</th>
                                <th class="p-3">عدد الحصص</th>
                                <th class="p-3">سعر الباقة</th>
                                <th class="p-3">متوسط سعر الحصة الواحدة</th>
                                <th class="p-3">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($coursesData['packages'] as $pkg)
                                <tr>
                                    <td class="p-3 text-slate-500 font-bold">#{{ $pkg->id }}</td>
                                    <td class="p-3 font-bold text-slate-900">{{ $pkg->title }}</td>
                                    <td class="p-3 text-slate-700 font-bold">{{ $pkg->lessons_count }} حصة</td>
                                    <td class="p-3 font-black text-emerald-700 text-sm">{{ $pkg->formatted_price }}</td>
                                    <td class="p-3 text-slate-600">
                                        @php
                                            $perLesson = $pkg->lessons_count > 0 ? round($pkg->price / $pkg->lessons_count, 2) : 0;
                                        @endphp
                                        {{ $perLesson }} {{ $pkg->currency }} / حصة
                                    </td>
                                    <td class="p-3">
                                        @if($pkg->is_active)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">نشطة</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">معطلة</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- التبويب 4: تقرير السندات والصناديق (Vouchers & Cash Funds)       -->
    <!-- ============================================================== -->
    @if($tab === 'vouchers')
        <div class="space-y-6">
            <!-- بطاقات الصناديق وحركة السندات المفلترة -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                    <span class="text-xs font-bold text-emerald-600 block">إجمالي المقبوضات المفلترة</span>
                    <span class="text-2xl font-black text-emerald-900">{{ number_format($vouchersData['total_receipts_amount'], 2) }}</span>
                    <span class="text-[10px] text-emerald-700 block font-semibold">({{ $vouchersData['total_receipts_count'] }} سند قبض)</span>
                </div>

                <div class="bg-rose-50 p-4 rounded-2xl border border-rose-100">
                    <span class="text-xs font-bold text-rose-600 block">إجمالي المصروفات المفلترة</span>
                    <span class="text-2xl font-black text-rose-900">{{ number_format($vouchersData['total_payments_amount'], 2) }}</span>
                    <span class="text-[10px] text-rose-700 block font-semibold">({{ $vouchersData['total_payments_count'] }} سند صرف)</span>
                </div>

                <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100">
                    <span class="text-xs font-bold text-indigo-600 block">صافي الحركة للفترة/النطاق</span>
                    <span class="text-2xl font-black text-indigo-900">{{ number_format($vouchersData['net_amount'], 2) }}</span>
                    <span class="text-[10px] text-indigo-700 block font-semibold">فارق القبض عن الصرف</span>
                </div>

                <div class="bg-slate-900 p-4 rounded-2xl text-white">
                    <span class="text-xs font-bold text-amber-400 block">رصيد الصندوق النقدي الحالي</span>
                    <span class="text-2xl font-black text-white">{{ number_format($kpis['cash_box_balance'], 2) }}</span>
                    <span class="text-[10px] text-slate-300 block">YER في الخزينة</span>
                </div>
            </div>

            <!-- جدول السندات المفصل -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <h2 class="text-base font-black text-slate-900">سجل وحركة السندات المالية (سندات القبض والصرف)</h2>
                        <p class="text-xs text-slate-500">فلترة من رقم سند إلى رقم سند، من تاريخ إلى تاريخ، مع روابط الطباعة الفورية</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('accounting.dashboard') }}" class="text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-xl border border-emerald-200 transition">
                            لوحة المحاسبة 💼
                        </a>
                        <a href="{{ route('admin.reports.print', array_merge(request()->all(), ['type' => 'vouchers'])) }}" target="_blank" class="text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-xl transition flex items-center gap-1">
                            <span>🖨️</span>
                            <span>طباعة كشف السندات</span>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3">رقم السند</th>
                                <th class="p-3">الرقم الدفتري</th>
                                <th class="p-3">التاريخ والوقت</th>
                                <th class="p-3">نوع السند</th>
                                <th class="p-3">الحساب / المستفيد</th>
                                <th class="p-3">البيان والشرح</th>
                                <th class="p-3">المبلغ والعملة</th>
                                <th class="p-3">طريقة الدفع</th>
                                <th class="p-3">الموظف</th>
                                <th class="p-3 text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($vouchersData['vouchers'] as $v)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-3 font-mono font-bold text-slate-900">{{ $v->voucher_no }}</td>
                                    <td class="p-3 text-slate-500 font-mono">{{ $v->manual_no ?? '-' }}</td>
                                    <td class="p-3 text-slate-600">{{ $v->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="p-3">
                                        @if($v->voucher_type === 'receipt')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                                سند قبض 🟢
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800">
                                                سند صرف 🔴
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 font-bold text-slate-800">
                                        {{ $v->account?->user?->name ?? $v->account?->account_name ?? 'حساب مالي' }}
                                    </td>
                                    <td class="p-3 text-slate-600 max-w-xs truncate" title="{{ $v->statement }}">
                                        {{ $v->statement }}
                                    </td>
                                    <td class="p-3 font-black text-sm {{ $v->voucher_type === 'receipt' ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ number_format($v->amount, 2) }} {{ $v->currency }}
                                    </td>
                                    <td class="p-3 text-slate-600">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-bold">
                                            {{ $v->payment_method === 'cash' ? 'نقداً كاش' : ($v->payment_method === 'kuraimi' ? 'الكريمي' : 'تحويل بنكي') }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-500 text-[11px]">{{ $v->creator?->name ?? 'مدير النظام' }}</td>
                                    <td class="p-3 text-center">
                                        <a href="{{ route('accounting.voucher.print', $v->id) }}" target="_blank" class="p-1.5 text-slate-600 hover:text-indigo-600 transition" title="طباعة السند">
                                            🖨️
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="p-8 text-center text-slate-400">
                                        لا توجد سندات مالية مطابقة للشروط أو النطاق المحدد.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- التبويب 5: تقرير الطلاب والملتحقين (Students Report)          -->
    <!-- ============================================================== -->
    @if($tab === 'students')
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                    <span class="text-xs font-bold text-blue-600 block">إجمالي الطلاب في هذا الكشف</span>
                    <span class="text-2xl font-black text-blue-900">{{ $studentsData['total_students'] }}</span>
                </div>
                <div class="bg-purple-50 p-4 rounded-2xl border border-purple-100">
                    <span class="text-xs font-bold text-purple-600 block">المسكنين في مجموعات دراسية</span>
                    <span class="text-2xl font-black text-purple-900">{{ $studentsData['in_groups_count'] }}</span>
                </div>
                <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100">
                    <span class="text-xs font-bold text-amber-600 block">بانتظار التسكين في مجموعة</span>
                    <span class="text-2xl font-black text-amber-900">{{ $studentsData['pending_placement_count'] }}</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-base font-black text-slate-900">كشف الطلاب والملتحقين بالمجموعات والأرصدة</h2>
                        <p class="text-xs text-slate-500">حالة التسكين في المجموعات، الحساب المالي، وأرقام التواصل</p>
                    </div>
                    <a href="{{ route('admin.reports.print', array_merge(request()->all(), ['type' => 'students'])) }}" target="_blank" class="text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-xl transition flex items-center gap-1">
                        <span>🖨️</span>
                        <span>طباعة كشف الطلاب</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3">#ID</th>
                                <th class="p-3">اسم الطالب</th>
                                <th class="p-3">رقم الهاتف / الواتساب</th>
                                <th class="p-3">المجموعة الدراسية الحالية</th>
                                <th class="p-3">الرصيد المالي الحالي</th>
                                <th class="p-3">تاريخ الانضمام</th>
                                <th class="p-3">كشف الحساب</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($studentsData['students'] as $stu)
                                <tr>
                                    <td class="p-3 font-bold text-slate-500">#{{ $stu->id }}</td>
                                    <td class="p-3">
                                        <span class="font-bold text-slate-900 block">{{ $stu->name }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $stu->email }}</span>
                                    </td>
                                    <td class="p-3 font-mono text-slate-700">{{ $stu->phone_whatsapp ?? '-' }}</td>
                                    <td class="p-3">
                                        @php
                                            $activeGroup = $stu->studyGroups->where('pivot.status', 'active')->first();
                                        @endphp
                                        @if($activeGroup)
                                            <span class="font-bold text-purple-700 block">👥 {{ $activeGroup->name }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $activeGroup->course?->name }}</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                بانتظار التسكين ⏳
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 font-bold">
                                        @php
                                            $bal = $stu->financialAccount?->current_balance ?? 0;
                                        @endphp
                                        @if($bal > 0)
                                            <span class="text-rose-600">مدين: {{ number_format($bal, 2) }} YER</span>
                                        @elseif($bal < 0)
                                            <span class="text-blue-600">دائن: {{ number_format(abs($bal), 2) }} YER</span>
                                        @else
                                            <span class="text-emerald-600">مسدد بالكامل (0.00)</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-500">{{ $stu->created_at->format('Y-m-d') }}</td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <a href="{{ route('admin.reports.student.transcript', $stu->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] border border-indigo-200 transition shadow-2xs whitespace-nowrap">
                                                📜 السجل الشامل 360
                                            </a>
                                            <a href="{{ route('accounting.statement', $stu->id) }}" target="_blank" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition whitespace-nowrap">
                                                كشف حساب 🧾
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-400">لا يوجد طلاب يطابقون شروط البحث.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- =================================================================== -->
    <!-- التبويب 6: تقرير الكادر والمستخدمين (Staff & Users Report)          -->
    <!-- =================================================================== -->
    @if($tab === 'staff')
        <div class="space-y-6">
            <!-- توزيع المستخدمين بحسب الأدوار -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <h2 class="text-base font-black text-slate-900 mb-4">توزيع مستخدمي المنصة بحسب الأدوار والصلاحيات</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    @foreach($staffData['roles_distribution'] as $roleKey => $r)
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 text-center">
                            <span class="text-xs font-bold text-slate-500 block mb-1">{{ $r['label'] }}</span>
                            <span class="text-2xl font-black text-slate-900">{{ $r['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- جدول المعلمين والمشرفين والمجموعات المسندة إليهم -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="text-base font-black text-slate-900">تقرير الكادر التعليمي والمعلمين</h2>
                    <p class="text-xs text-slate-500">عدد المجموعات، الطلاب تحت الإشراف، والأرصدة المستحقة</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3">#ID</th>
                                <th class="p-3">اسم المعلم</th>
                                <th class="p-3">البريد الإلكتروني</th>
                                <th class="p-3">رقم الهاتف</th>
                                <th class="p-3">المجموعات المسندة</th>
                                <th class="p-3">إجمالي الطلاب</th>
                                <th class="p-3">رصيد الحساب المالي</th>
                                <th class="p-3">الحالة</th>
                                <th class="p-3 text-center">كشف الحساب</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($staffData['teachers_data'] as $item)
                                <tr>
                                    <td class="p-3 text-slate-500 font-bold">#{{ $item['teacher']->id }}</td>
                                    <td class="p-3 font-bold text-slate-900">{{ $item['teacher']->name }}</td>
                                    <td class="p-3 text-slate-600">{{ $item['teacher']->email }}</td>
                                    <td class="p-3 font-mono text-slate-600">{{ $item['teacher']->phone_whatsapp ?? '-' }}</td>
                                    <td class="p-3 font-bold text-purple-700">{{ $item['groups_count'] }} مجموعة ({{ $item['active_groups_count'] }} نشطة)</td>
                                    <td class="p-3 font-bold text-slate-800">{{ $item['students_count'] }} طالب</td>
                                    <td class="p-3 font-black text-sm text-slate-900">{{ number_format($item['balance'], 2) }} YER</td>
                                    <td class="p-3">
                                        @if($item['teacher']->status === 'active')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">نشط</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">معطل</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        <a href="{{ route('admin.reports.teacher.statement', $item['teacher']->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] border border-indigo-200 transition shadow-2xs whitespace-nowrap">
                                            💳 كشف الحساب والحركة
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- =================================================================== -->
    <!-- التبويب 7: كشوفات الحساب والأرصدة (Statements & Balances)          -->
    <!-- =================================================================== -->
    @if($tab === 'statements')
        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-rose-50 p-5 rounded-3xl border border-rose-100">
                    <span class="text-xs font-bold text-rose-600 block mb-1">إجمالي ديون الطلاب المستحقة (مدينون)</span>
                    <span class="text-3xl font-black text-rose-900">{{ number_format($statementsData['total_receivables'], 2) }} YER</span>
                    <p class="text-xs text-rose-700 mt-1">مجموع المبالغ غير المسددة من رسوم المستويات</p>
                </div>

                <div class="bg-indigo-50 p-5 rounded-3xl border border-indigo-100">
                    <span class="text-xs font-bold text-indigo-600 block mb-1">مستحقات المعلمين والكادر (دائنون)</span>
                    <span class="text-3xl font-black text-indigo-900">{{ number_format($statementsData['total_payables'], 2) }} YER</span>
                    <p class="text-xs text-indigo-700 mt-1">أرصدة المعلمين المستحقة للصرف</p>
                </div>
            </div>

            <!-- جدول الطلاب أصحاب الذمم والمديونيات -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="text-base font-black text-slate-900">أرصدة حسابات الطلاب والذمم المالية</h2>
                    <p class="text-xs text-slate-500">كشوفات الأرصدة الحالية للطلاب مع روابط لطباعة كشف الحساب التفصيلي</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-3">رقم الحساب</th>
                                <th class="p-3">اسم الطالب</th>
                                <th class="p-3">الرصيد الافتتاحي</th>
                                <th class="p-3">الرصيد الحالي</th>
                                <th class="p-3">الحالة المالية</th>
                                <th class="p-3 text-center">كشف الحساب</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($statementsData['student_accounts'] as $acc)
                                <tr>
                                    <td class="p-3 font-mono font-bold text-slate-600">{{ $acc->account_code }}</td>
                                    <td class="p-3 font-bold text-slate-900">{{ $acc->user?->name ?? $acc->account_name }}</td>
                                    <td class="p-3 font-mono text-slate-600">{{ number_format($acc->opening_balance, 2) }} {{ $acc->currency }}</td>
                                    <td class="p-3 font-black text-sm {{ $acc->current_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                        {{ number_format($acc->current_balance, 2) }} {{ $acc->currency }}
                                    </td>
                                    <td class="p-3">
                                        @if($acc->current_balance > 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800">مدين للمنصة ⚠️</span>
                                        @elseif($acc->current_balance < 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800">له رصيد دائن</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">مخلص الحساب ✅</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($acc->user_id)
                                            <a href="{{ route('accounting.statement', $acc->user_id) }}" target="_blank" class="px-3 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold transition">
                                                طباعة الكشف 🧾
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
