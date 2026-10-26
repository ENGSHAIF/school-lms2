@extends('layouts.app', ['title' => 'لوحة تحكم مدير النظام - المركز العام'])

@section('content')
<div class="space-y-8 text-slate-800">

    <!-- الترويسة الرئيسية للمركز العام -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-indigo-500/20 relative overflow-hidden">
        <div class="space-y-2 z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1 rounded-full text-xs font-black bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>مركز التحكم والإدارة العامة (ERP Control Hub)</span>
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-slate-300 border border-white/10">
                    {{ now()->translatedFormat('l، d F Y') }}
                </span>
                @if($whatsappMasterOn)
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                        <span>💬</span>
                        <span>الواتساب التلقائي نشط</span>
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        الواتساب متوقف ✕
                    </span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-white flex items-center gap-2.5">
                <span>🎛️</span>
                <span>لوحة التحكم الرئيسية لكافة شاشات النظام</span>
            </h1>

            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                مرحباً بك في المركز الموحد لإدارة منصتك التعليمية: تم تنظيم كافة الشاشات والوظائف في بانلات وأشرطة ذكية قابلة للطي والتوسيع للوصول الفوري والسلس لكافة الأقسام.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap z-10 w-full sm:w-auto">
            <a href="{{ route('admin.students.index') }}" class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-lg shadow-emerald-950/40 transition flex items-center justify-center gap-2 border border-emerald-400/30">
                <span>🎓</span>
                <span>إدارة الطلاب والقبول</span>
                @if($pendingStudents > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-white text-emerald-900 font-black animate-pulse">
                        {{ $pendingStudents }}
                    </span>
                @endif
            </a>
            <a href="{{ route('accounting.dashboard') }}" class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-teal-600 hover:bg-teal-500 text-white font-black text-xs shadow-lg shadow-teal-950/40 transition flex items-center justify-center gap-2 border border-teal-400/30">
                <span>💼</span>
                <span>النظام المحاسبي والسندات</span>
            </a>
        </div>
    </div>

    <!-- بطاقات المؤشرات الإحصائية السريعة -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <a href="{{ route('admin.students.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs hover:border-indigo-400 transition group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-slate-400 group-hover:text-indigo-600 transition">إجمالي الطلاب</span>
                <span class="text-base">👥</span>
            </div>
            <span class="text-xl font-black text-slate-800 font-mono">{{ number_format($totalStudents) }}</span>
        </a>

        <a href="{{ route('admin.students.index') }}?status=pending_approval" class="bg-white p-4 rounded-2xl border border-amber-200 shadow-xs hover:border-amber-400 transition bg-gradient-to-br from-white to-amber-50/40 group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-amber-700">بانتظار الاعتماد</span>
                <span class="text-base">⏳</span>
            </div>
            <span class="text-xl font-black text-amber-700 font-mono">{{ number_format($pendingStudents) }}</span>
        </a>

        <a href="{{ route('admin.students.index') }}?status=active" class="bg-white p-4 rounded-2xl border border-emerald-200 shadow-xs hover:border-emerald-400 transition bg-gradient-to-br from-white to-emerald-50/40 group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-emerald-700">الطلاب المقبولون</span>
                <span class="text-base">✅</span>
            </div>
            <span class="text-xl font-black text-emerald-700 font-mono">{{ number_format($activeStudents) }}</span>
        </a>

        <a href="{{ route('admin.groups.index') }}" class="bg-white p-4 rounded-2xl border border-purple-200 shadow-xs hover:border-purple-400 transition bg-gradient-to-br from-white to-purple-50/40 group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-purple-700">المجموعات النشطة</span>
                <span class="text-base">🏫</span>
            </div>
            <span class="text-xl font-black text-purple-700 font-mono">{{ number_format($groupsCount) }}</span>
        </a>

        <a href="{{ route('accounting.dashboard') }}" class="bg-white p-4 rounded-2xl border border-teal-200 shadow-xs hover:border-teal-400 transition bg-gradient-to-br from-white to-teal-50/40 group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-teal-700">سندات المحاسبة</span>
                <span class="text-base">💳</span>
            </div>
            <span class="text-xl font-black text-teal-700 font-mono">{{ number_format($vouchersCount) }}</span>
        </a>

        <a href="{{ route('admin.users.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs hover:border-indigo-400 transition group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-bold text-slate-400 group-hover:text-indigo-600 transition">الكادر التعليمي</span>
                <span class="text-base">👨‍🏫</span>
            </div>
            <span class="text-xl font-black text-slate-800 font-mono">{{ number_format($totalTeachers) }}</span>
        </a>
    </div>

    <!-- شريط أدوات التحكم والبحث السريع في كافة الشاشات -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <!-- حقل البحث اللحظي -->
        <div class="relative flex-1">
            <input type="text" id="screenSearchInput" oninput="filterScreens(this.value)" placeholder="🔍 ابحث بالاسم عن أي شاشة أو وظيفة (مثلاً: كويزات، سندات، شهائد، حضور، واتساب)..."
                class="w-full bg-slate-50 border border-slate-300 rounded-2xl px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
            <span id="searchMatchCount" class="hidden absolute left-3 top-2.5 text-[11px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-200"></span>
        </div>

        <!-- أزرار طي وتوسيع كافة الأشرطة -->
        <div class="flex items-center gap-2 self-end sm:self-center">
            <button type="button" onclick="expandAllSections()" class="px-3.5 py-2 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 transition flex items-center gap-1.5 shadow-2xs">
                <span>📂</span>
                <span>فتح كافة الأقسام</span>
            </button>
            <button type="button" onclick="collapseAllSections()" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 hover:bg-slate-200 transition flex items-center gap-1.5">
                <span>📁</span>
                <span>طي كافة الأقسام</span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- قائمة الأقسام الرئيسية (Accordion Panels with Hidden Icons until Click) -->
    <!-- ========================================================================= -->
    <div class="space-y-4" id="sectionsContainer">

        <!-- ========================================================================= -->
        <!-- البانل 1: شاشات الأكاديميا، الطلاب، المجموعات، والتعليم -->
        <!-- ========================================================================= -->
        <div class="panel-section rounded-3xl border-2 border-emerald-200/80 bg-white shadow-xs overflow-hidden transition-all duration-300 hover:shadow-md" id="panel-academy">
            <!-- الشريط الرئيسي للقسم -->
            <button type="button" onclick="toggleSection('academy')" 
                class="w-full text-right p-5 sm:p-6 flex items-center justify-between gap-4 bg-gradient-to-r from-emerald-50 via-white to-indigo-50/40 hover:from-emerald-100/70 transition-all select-none">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-emerald-600 to-indigo-600 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-200 shrink-0">
                        🎓
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base sm:text-lg font-black text-slate-900">شاشات الأكاديميا والطلاب والتعليم</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                4 شاشات رئيسية
                            </span>
                            @if($pendingStudents > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                    {{ $pendingStudents }} بانتظار الاعتماد
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            القبول والتسجيل، المجموعات الدراسية، الحضور والتقييم اليومي، والمناهج والكتب الدراسية
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-xs font-bold text-emerald-700 hidden sm:inline-block section-status-label" id="label-academy">عرض المحتويات</span>
                    <div id="chevron-academy" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 transition-transform duration-300">
                        ▼
                    </div>
                </div>
            </button>

            <!-- محتويات البانل (الأيقونات والشاشات التفصيلية) -->
            <div id="content-academy" class="hidden p-5 sm:p-6 border-t border-slate-100 bg-slate-50/40">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- 1.1 إدارة الطلاب والقبول والتسجيل -->
                    <a href="{{ route('admin.students.index') }}" class="screen-card bg-white p-5 rounded-2xl border-2 border-emerald-300/80 hover:border-emerald-500 shadow-xs hover:shadow-lg transition-all group flex flex-col justify-between bg-gradient-to-br from-white via-white to-emerald-50/30">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-200 group-hover:scale-110 transition">
                                    🎓
                                </div>
                                @if($pendingStudents > 0)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                        {{ $pendingStudents }} بانتظار الاعتماد
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                        شاشة رئيسية ⭐
                                    </span>
                                @endif
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-emerald-700 transition screen-title">
                                    إدارة الطلاب والقبول والتسجيل
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    سجل الطلاب، نتائج اختبار القبول، فحص نطق الصوت بالذكاء الاصطناعي، واعتماد المستويات والتسكين.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition">
                            <span>فتح شاشة الطلاب والقبول</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 1.2 المجموعات الدراسية والدفعات -->
                    <a href="{{ route('admin.groups.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-purple-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    👥
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 font-mono">
                                    {{ $groupsCount }} مجموعة
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-purple-700 transition screen-title">
                                    المجموعات الدراسية والدفعات
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    إدارة الفصول الافتراضية، ربط المجموعات بالمعلم الرئيسي والمشرف، وتوزيع الطلاب ومواعيد الحصص.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-purple-700 group-hover:translate-x-1 transition">
                            <span>إدارة المجموعات والفصول</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 1.3 التقييم والحضور والغياب اليومي -->
                    <a href="{{ route('admin.groups.evaluations.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-amber-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    📋
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    يومي وأسبوعي
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-amber-700 transition screen-title">
                                    التقييم وسجلات الحضور
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    رصد الحضور والغياب، ملخص الحصص، رصد النجوم التحفيزية، وإرسال تقارير الحصص لواتساب الطلاب.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-amber-700 group-hover:translate-x-1 transition">
                            <span>فتح سجلات التقييم والحضور</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 1.4 المناهج والخطط الدراسية -->
                    <a href="{{ route('admin.courses.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-emerald-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    📚
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono">
                                    {{ $coursesCount }} برامج
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-emerald-700 transition screen-title">
                                    المناهج والخطط الدراسية
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    إدارة الدورات والمستويات، رفع الكتب وملفات الـ PDF، وتنظيم الدروس والمحتوى التعليمي.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition">
                            <span>استعراض المناهج والدروس</span>
                            <span>←</span>
                        </div>
                    </a>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- البانل 2: 🎯 شاشات الاختبارات والكويزات والشهائد (كما طلبها المستخدم بالتحديد) -->
        <!-- ========================================================================= -->
        <div class="panel-section rounded-3xl border-2 border-rose-200/90 bg-white shadow-xs overflow-hidden transition-all duration-300 hover:shadow-md" id="panel-exams">
            <!-- الشريط الرئيسي للقسم -->
            <button type="button" onclick="toggleSection('exams')" 
                class="w-full text-right p-5 sm:p-6 flex items-center justify-between gap-4 bg-gradient-to-r from-rose-50 via-white to-amber-50/40 hover:from-rose-100/70 transition-all select-none">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-rose-600 to-amber-500 text-white flex items-center justify-center text-2xl shadow-md shadow-rose-200 shrink-0">
                        🎯
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base sm:text-lg font-black text-slate-900">🎯 شاشات الاختبارات والكويزات والشهائد</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                3 شاشات متكاملة
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300 font-mono">
                                {{ $examsCount }} اختبار معتمد
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            الكويزات وبنوك الأسئلة، الاختبارات النهائية والشهائد المعتمدة، واختبار تحديد المستوى وفحص النطق
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-xs font-bold text-rose-700 hidden sm:inline-block section-status-label" id="label-exams">عرض المحتويات</span>
                    <div id="chevron-exams" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 transition-transform duration-300">
                        ▼
                    </div>
                </div>
            </button>

            <!-- محتويات البانل (الأيقونات والشاشات التفصيلية) -->
            <div id="content-exams" class="hidden p-5 sm:p-6 border-t border-slate-100 bg-rose-50/20">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <!-- 2.1 الكويزات وبنوك الأسئلة -->
                    <a href="{{ route('admin.exams.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-rose-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    🎯
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    تصحيح ذاتي ⚡
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-rose-700 transition screen-title">
                                    الكويزات وبنوك الأسئلة
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    إنشاء الكويزات الدورية، بنك الأسئلة متعددة الاختيارات، أسئلة الاستماع والصوت، والتصحيح الفوري.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-rose-700 group-hover:translate-x-1 transition">
                            <span>إدارة الكويزات والأسئلة</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 2.2 الاختبارات والشهائد المعتمدة -->
                    <a href="{{ route('admin.exams_management.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-amber-500 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    📜
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 font-mono">
                                    شهائد معتمدة بـ QR
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-amber-700 transition screen-title">
                                    الاختبارات والشهائد المعتمدة
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    إدارة اختبارات التوفل والبرامج العامة، نماذج الامتحانات النهائية، وإصدار شهائد التخرج الرسمية بـ QR Code.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-amber-700 group-hover:translate-x-1 transition">
                            <span>إدارة الاختبارات والشهائد</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 2.3 اختبار تحديد المستوى الإلزامي -->
                    <a href="{{ route('placement.test') }}" target="_blank" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-blue-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    🎙️
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    تجربة واجهة الطالب
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-blue-700 transition screen-title">
                                    اختبار تحديد المستوى (معاينة)
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    معاينة واجهة اختبار القبول وتحديد المستوى الذي يخوضه الطلاب لأول مرة مع فحص النطق الصوتي الذكي.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-700 group-hover:translate-x-1 transition">
                            <span>معاينة الاختبار الحي</span>
                            <span>↗</span>
                        </div>
                    </a>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- البانل 3: 💼 الإدارة المالية والمحاسبة وعقود المعلمين والتقارير -->
        <!-- ========================================================================= -->
        <div class="panel-section rounded-3xl border-2 border-teal-200/90 bg-white shadow-xs overflow-hidden transition-all duration-300 hover:shadow-md" id="panel-accounting">
            <!-- الشريط الرئيسي للقسم -->
            <button type="button" onclick="toggleSection('accounting')" 
                class="w-full text-right p-5 sm:p-6 flex items-center justify-between gap-4 bg-gradient-to-r from-teal-50 via-white to-emerald-50/40 hover:from-teal-100/70 transition-all select-none">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-600 text-white flex items-center justify-center text-2xl shadow-md shadow-teal-200 shrink-0">
                        💼
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base sm:text-lg font-black text-slate-900">💼 الإدارة المالية والمحاسبة القياسية والتقارير</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-100 text-teal-800 border border-teal-200">
                                4 شاشات رئيسية
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 font-mono">
                                ماستر وديتيل ⚡
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            سندات القبض والصرف (فردي وجماعي مركب)، دليل الصناديق والبنوك، التسعيرة والعقود، والتقارير الشاملة
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-xs font-bold text-teal-700 hidden sm:inline-block section-status-label" id="label-accounting">عرض المحتويات</span>
                    <div id="chevron-accounting" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 transition-transform duration-300">
                        ▼
                    </div>
                </div>
            </button>

            <!-- محتويات البانل (الأيقونات والشاشات التفصيلية) -->
            <div id="content-accounting" class="hidden p-5 sm:p-6 border-t border-slate-100 bg-teal-50/20">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    <!-- 3.1 النظام المحاسبي القياسي -->
                    <a href="{{ route('accounting.dashboard') }}" class="screen-card bg-white p-5 rounded-2xl border-2 border-teal-300/80 hover:border-teal-500 shadow-xs hover:shadow-lg transition-all group flex flex-col justify-between bg-gradient-to-br from-white to-teal-50/30">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-teal-600 text-white flex items-center justify-center text-2xl shadow-md shadow-teal-200 group-hover:scale-110 transition">
                                    💼
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-teal-100 text-teal-800 border border-teal-300 font-mono">
                                    {{ $vouchersCount }} سند
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-teal-700 transition screen-title">
                                    النظام المحاسبي والسندات
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    سندات القبض للطلاب، سندات الصرف للمعلمين، إثبات المستحقات، السندات المركبة (ماستر وديتيل)، وكشوفات الحساب.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-teal-700 group-hover:translate-x-1 transition">
                            <span>فتح اللوحة المالية والمحاسبية</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 3.2 دليل وهيكل الصناديق والخزائن والبنوك -->
                    <a href="{{ route('accounting.cash_boxes.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-emerald-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    🏦
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    شجرة الحسابات
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-emerald-700 transition screen-title">
                                    دليل الصناديق والبنوك
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    إدارة الصناديق الرئيسية والفرعية، تحديد طبيعة الصندوق (قبض / صرف / نقطة بيع)، ومتابعة كشوف الحركة.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition">
                            <span>فتح دليل الصناديق والبنوك</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 3.3 تسعيرة الدورات والخدمات وعقود المعلمين -->
                    <a href="{{ route('accounting.pricing.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-indigo-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    🏷️
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    تسعيرة & عقود
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-indigo-700 transition screen-title">
                                    تسعيرة الدورات وعقود المعلمين
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    تحديد رسوم الدورات والدبلومات للطلاب، وأجور التدريس للمعلمين (بالساعة أو الدورة أو راتب شهري ثابت).
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-indigo-700 group-hover:translate-x-1 transition">
                            <span>إدارة التسعيرة والعقود</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 3.4 مركز التقارير والإحصائيات الشاملة -->
                    <a href="{{ route('admin.reports.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-amber-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    📊
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    تقارير تفصيلية
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-amber-700 transition screen-title">
                                    التقارير والإحصائيات الشاملة
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    تقارير التحصيل المالي، موازين المراجعة، كشوف حسابات المعلمين والطلاب، وسجلات التدقيق والأمان.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-amber-700 group-hover:translate-x-1 transition">
                            <span>استعراض مركز التقارير</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 3.5 الدليل المحاسبي الشجري العام (COA) -->
                    <a href="{{ route('accounting.chart_of_accounts.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-indigo-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                                    🌳
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    5 مستويات
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-indigo-700 transition screen-title">
                                    الدليل المحاسبي الشجري (COA)
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    شجرة الحسابات المالية العامة القياسية (أصول، خصوم، حقوق ملكية، إيرادات، مصروفات) وتوليد الأكواد آلياً.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-indigo-700 group-hover:translate-x-1 transition">
                            <span>فتح الدليل المحاسبي</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 3.6 شجرة مراكز التكلفة والربحية -->
                    <a href="{{ route('accounting.cost_centers.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-amber-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                                    🏢
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    تحليل الربحية
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-amber-700 transition screen-title">
                                    مراكز التكلفة والربحية
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    توزيع الإيرادات والمصروفات حسب الأقسام (لغات، ترجمة، استشارات) والدورات لتقييم صافي أرباح كل دورة.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-amber-700 group-hover:translate-x-1 transition">
                            <span>إدارة مراكز التكلفة</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 3.7 دورة طلبات الصرف والقبض والاعتمادات -->
                    <a href="{{ route('accounting.payment_requests.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-teal-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                                    📝
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                    دورة مستندية
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-teal-700 transition screen-title">
                                    طلبات الصرف والاعتمادات
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    دورة الموافقات والاعتمادات الإدارية لطلبات الصرف والعهد قبل التنفيذ الفعلي والصرف من الصندوق.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-teal-700 group-hover:translate-x-1 transition">
                            <span>متابعة طلبات الصرف</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 3.8 دفتر قيود اليومية العامة المزدوجة -->
                    <a href="{{ route('accounting.journal_entries.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-blue-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                                    ⚖️
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                    القيد المزدوج
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-blue-700 transition screen-title">
                                    قيود اليومية العامة (GL)
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    دفتر القيود المزدوجة المتزنة الناتجة آلياً عن السندات، تسجيل قيود التسوية اليدوية، والقيود العكسية.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-700 group-hover:translate-x-1 transition">
                            <span>فتح دفتر اليومية العامة</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 3.9 مركز التقارير والقوائم الختامية -->
                    <a href="{{ route('accounting.reports.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-emerald-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                                    📊
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    ميزان & أرباح
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-emerald-700 transition screen-title">
                                    التقارير والقوائم المالية الختامية
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    ميزان المراجعة بالمجاميع والأرصدة، قائمة الدخل والأرباح والخسائر، ودفتر الأستاذ العام لكافة الحسابات.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition">
                            <span>فتح القوائم المالية</span>
                            <span>←</span>
                        </div>
                    </a>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- البانل 4: 💬 منظومة الواتساب الذكي والآلي والتواصل -->
        <!-- ========================================================================= -->
        <div class="panel-section rounded-3xl border-2 border-emerald-200/90 bg-white shadow-xs overflow-hidden transition-all duration-300 hover:shadow-md" id="panel-whatsapp">
            <!-- الشريط الرئيسي للقسم -->
            <button type="button" onclick="toggleSection('whatsapp')" 
                class="w-full text-right p-5 sm:p-6 flex items-center justify-between gap-4 bg-gradient-to-r from-emerald-50 via-white to-teal-50/40 hover:from-emerald-100/70 transition-all select-none">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-600 text-white flex items-center justify-center text-2xl shadow-md shadow-emerald-200 shrink-0">
                        💬
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base sm:text-lg font-black text-slate-900">💬 منظومة الواتساب الذكي والإشعارات الآلية</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                خادم محلي متزامن
                            </span>
                            @if($whatsappMasterOn)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    الإرسال التلقائي: نشط ⚡
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                                    متوقف مؤقتاً
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            ربط الرقم عبر QR Code، الإرسال الفوري لسندات القبض والصرف، القوالب الذكية، والحملات الجماعية
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-xs font-bold text-emerald-700 hidden sm:inline-block section-status-label" id="label-whatsapp">عرض المحتويات</span>
                    <div id="chevron-whatsapp" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 transition-transform duration-300">
                        ▼
                    </div>
                </div>
            </button>

            <!-- محتويات البانل (الأيقونات والشاشات التفصيلية) -->
            <div id="content-whatsapp" class="hidden p-5 sm:p-6 border-t border-slate-100 bg-emerald-50/20">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <!-- 4.1 لوحة إدارة الواتساب والربط المباشر -->
                    <a href="{{ route('admin.whatsapp.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-emerald-500 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    📱
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    QR Code مباشر
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-emerald-700 transition screen-title">
                                    لوحة تحكم خادم الواتساب
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    قراءة رمز الاستجابة السريعة QR Code، مراقبة حالة جلسة الاتصال، وفحص الإرسال التجريبي بنقرة واحدة.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition">
                            <span>فتح لوحة تحكم الواتساب</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 4.2 قوالب رسائل السندات والتقييم -->
                    <a href="{{ route('admin.whatsapp.index') }}#templatesSection" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-teal-500 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    📝
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                    تخصيص الصياغة
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-teal-700 transition screen-title">
                                    قوالب الرسائل والإشعارات
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    تخصيص نصوص رسائل سندات القبض والصرف، إشعارات تسجيل الطلاب، روابط الـ PDF، وبطاقات الحصص.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-teal-700 group-hover:translate-x-1 transition">
                            <span>تعديل قوالب الرسائل</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 4.3 سجل الإرسال والحملات الجماعية -->
                    <a href="{{ route('admin.whatsapp.index') }}#campaignsSection" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-indigo-500 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                    📢
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                    حملات موجهة
                                </span>
                            </div>

                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-indigo-700 transition screen-title">
                                    الحملات وسجل الرسائل المرسلة
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    إرسال إعلانات جماعية للطلاب حسب المجموعات أو الدفعات، مع متابعة تفصيلية لسجل وصول وتسليم الرسائل.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-indigo-700 group-hover:translate-x-1 transition">
                            <span>استعراض الحملات والسجل</span>
                            <span>←</span>
                        </div>
                    </a>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- البانل 5: ⚙️ شاشات التحكم العام، المستخدمين، والصلاحيات (RBAC) -->
        <!-- ========================================================================= -->
        <div class="panel-section rounded-3xl border-2 border-slate-300/80 bg-white shadow-xs overflow-hidden transition-all duration-300 hover:shadow-md" id="panel-users">
            <!-- الشريط الرئيسي للقسم -->
            <button type="button" onclick="toggleSection('users')" 
                class="w-full text-right p-5 sm:p-6 flex items-center justify-between gap-4 bg-gradient-to-r from-slate-100 via-white to-blue-50/40 hover:from-slate-200/60 transition-all select-none">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-slate-800 to-blue-600 text-white flex items-center justify-center text-2xl shadow-md shadow-slate-300 shrink-0">
                        ⚙️
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base sm:text-lg font-black text-slate-900">⚙️ شاشات التحكم العام والمستخدمين والصلاحيات</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                3 شاشات رئيسية
                            </span>
                            @if($pendingPasswordRequests > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300 animate-pulse">
                                    {{ $pendingPasswordRequests }} طلبات استعادة كلمة المرور
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            إدارة المستخدمين والكادر، مصفوفة الصلاحيات والأدوار (RBAC)، وسجلات الأمان والتدقيق العام
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-xs font-bold text-slate-700 hidden sm:inline-block section-status-label" id="label-users">عرض المحتويات</span>
                    <div id="chevron-users" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 transition-transform duration-300">
                        ▼
                    </div>
                </div>
            </button>

            <!-- محتويات البانل (الأيقونات والشاشات التفصيلية) -->
            <div id="content-users" class="hidden p-5 sm:p-6 border-t border-slate-100 bg-slate-50/40">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <!-- 5.1 إدارة المستخدمين والكادر -->
                    <a href="{{ route('admin.users.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-blue-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between relative">
                        @if($pendingPasswordRequests > 0)
                            <span class="absolute top-4 left-4 w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
                            <span class="absolute top-4 left-4 w-3 h-3 rounded-full bg-rose-500"></span>
                        @endif
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                👤
                            </div>
                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-blue-700 transition screen-title">
                                    إدارة المستخدمين والكادر
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    المدراء، المشرفين، المعلمين، والمحاسبين، إدارة الحسابات وطلبات تغيير واستعادة كلمات المرور.
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-700 group-hover:translate-x-1 transition">
                            <span>فتح دليل المستخدمين</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 5.2 الصلاحيات ومجموعات الأذونات (RBAC) -->
                    <a href="{{ route('admin.permission_groups.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-purple-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                🛡️
                            </div>
                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-purple-700 transition screen-title">
                                    الصلاحيات والأدوار الوظيفية
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    مصفوفة الصلاحيات المخصصة، إنشاء مجموعات الصلاحيات، حماية الشاشات والعمليات الحساسة.
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-purple-700 group-hover:translate-x-1 transition">
                            <span>إدارة مصفوفة الصلاحيات</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 5.3 مركز الرقابة الشاملة وسجل التدقيق والأمان -->
                    <a href="{{ route('admin.audit_logs.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-slate-500 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                                🛡️
                            </div>
                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-indigo-700 transition screen-title flex items-center gap-2">
                                    <span>غرفة الرقابة وتدقيق العمليات</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">مراقبة حية</span>
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    مراقبة دقيقة لكافة العمليات المالية، تغيير التسعيرات، حذف المستندات، مقارنة القيم قبل وبعد التعديل، واسم الجهاز والـ IP.
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-indigo-700 group-hover:translate-x-1 transition">
                            <span>فتح غرفة الرقابة والتقارير</span>
                            <span>←</span>
                        </div>
                    </a>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- البانل 6: 🎨 الهوية البصرية، إدارة المحتوى، ودليل النظام -->
        <!-- ========================================================================= -->
        <div class="panel-section rounded-3xl border-2 border-violet-200/80 bg-white shadow-xs overflow-hidden transition-all duration-300 hover:shadow-md" id="panel-branding">
            <!-- الشريط الرئيسي للقسم -->
            <button type="button" onclick="toggleSection('branding')" 
                class="w-full text-right p-5 sm:p-6 flex items-center justify-between gap-4 bg-gradient-to-r from-violet-50 via-white to-pink-50/40 hover:from-violet-100/70 transition-all select-none">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-violet-600 to-pink-500 text-white flex items-center justify-center text-2xl shadow-md shadow-violet-200 shrink-0">
                        🎨
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base sm:text-lg font-black text-slate-900">🎨 الهوية البصرية، إدارة المحتوى، ودليل النظام</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-violet-100 text-violet-800 border border-violet-200">
                                3 شاشات رئيسية
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            تخصيص الشعارات والخلفيات، شريط البانرات الإعلانية، ومركز الدليل المعرفي والمساعدة
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-xs font-bold text-violet-700 hidden sm:inline-block section-status-label" id="label-branding">عرض المحتويات</span>
                    <div id="chevron-branding" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 transition-transform duration-300">
                        ▼
                    </div>
                </div>
            </button>

            <!-- محتويات البانل (الأيقونات والشاشات التفصيلية) -->
            <div id="content-branding" class="hidden p-5 sm:p-6 border-t border-slate-100 bg-violet-50/20">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    <!-- 6.1 الهوية والمظهر -->
                    <a href="{{ route('admin.branding.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-pink-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-pink-100 text-pink-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                🖼️
                            </div>
                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-pink-700 transition screen-title">
                                    الهوية البصرية والمظهر وخلفيات الموقع
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    تخصيص الشعارات الرسمية، خلفيات الحرم والمكتبة، وتعديل نصوص الصفحة الرئيسية ومعلومات الاتصال.
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-pink-700 group-hover:translate-x-1 transition">
                            <span>تخصيص الهوية والمظهر</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 6.2 البانرات والإعلانات البصرية -->
                    <a href="{{ route('admin.banners.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-violet-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-violet-100 text-violet-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                🚩
                            </div>
                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-violet-700 transition screen-title">
                                    إدارة البانرات والإعلانات (Visual CMS)
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    إدارة شريط الإعلانات التفاعلي، نشر بانرات الحملات الترويجية للدورات والتسجيل مع روابط مباشرة.
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-violet-700 group-hover:translate-x-1 transition">
                            <span>إدارة البانرات والإعلانات</span>
                            <span>←</span>
                        </div>
                    </a>

                    <!-- 6.3 مركز المساعدة ودليل النظام -->
                    <a href="{{ route('help.index') }}" class="screen-card bg-white p-5 rounded-2xl border border-slate-200 hover:border-indigo-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                                ❓
                            </div>
                            <div>
                                <h3 class="font-black text-base text-slate-900 group-hover:text-indigo-700 transition screen-title">
                                    مركز المساعدة ودليل تشغيل النظام
                                </h3>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed screen-desc">
                                    دليل استخدام تفاعلي خطوة بخطوة لكافة شاشات ووظائف النظام حسب نوع الحساب مع مساعد ذكي.
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-indigo-700 group-hover:translate-x-1 transition">
                            <span>فتح مركز المساعدة</span>
                            <span>←</span>
                        </div>
                    </a>

                </div>
            </div>
        </div>

    </div>

</div>

<!-- السكربت التفاعلي للطي والتوسيع والبحث السريع -->
<script>
    const sectionIds = ['academy', 'exams', 'accounting', 'whatsapp', 'users', 'branding'];

    // طي أو فتح قسم محدد
    function toggleSection(id) {
        const content = document.getElementById('content-' + id);
        const chevron = document.getElementById('chevron-' + id);
        const label = document.getElementById('label-' + id);
        if (!content) return;

        const isHidden = content.classList.contains('hidden');

        if (isHidden) {
            content.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            if (label) label.innerText = 'إخفاء المحتويات';
        } else {
            content.classList.add('hidden');
            chevron.classList.remove('rotate-180');
            if (label) label.innerText = 'عرض المحتويات';
        }
    }

    // فتح كافة الأقسام دفعة واحدة
    function expandAllSections() {
        sectionIds.forEach(id => {
            const content = document.getElementById('content-' + id);
            const chevron = document.getElementById('chevron-' + id);
            const label = document.getElementById('label-' + id);
            if (content) {
                content.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
                if (label) label.innerText = 'إخفاء المحتويات';
            }
        });
    }

    // طي كافة الأقسام دفعة واحدة
    function collapseAllSections() {
        sectionIds.forEach(id => {
            const content = document.getElementById('content-' + id);
            const chevron = document.getElementById('chevron-' + id);
            const label = document.getElementById('label-' + id);
            if (content) {
                content.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
                if (label) label.innerText = 'عرض المحتويات';
            }
        });
    }

    // تصفية وبحث لحظي ذكي في كافة الشاشات
    function filterScreens(query) {
        const q = (query || '').trim().toLowerCase();
        const cards = document.querySelectorAll('.screen-card');
        const matchBadge = document.getElementById('searchMatchCount');
        let matchedCount = 0;

        if (q === '') {
            // استعادة العرض الطبيعي وإخفاء شارة البحث
            cards.forEach(card => card.classList.remove('hidden'));
            if (matchBadge) matchBadge.classList.add('hidden');
            collapseAllSections();
            return;
        }

        // فتح كافة الأقسام للبحث
        expandAllSections();

        cards.forEach(card => {
            const titleElem = card.querySelector('.screen-title');
            const descElem = card.querySelector('.screen-desc');
            const text = ((titleElem ? titleElem.innerText : '') + ' ' + (descElem ? descElem.innerText : '')).toLowerCase();

            if (text.includes(q)) {
                card.classList.remove('hidden');
                matchedCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        // إخفاء الأقسام التي لا تحتوي على أي بطاقة مطابقة
        sectionIds.forEach(id => {
            const content = document.getElementById('content-' + id);
            const panel = document.getElementById('panel-' + id);
            if (content && panel) {
                const visibleCards = content.querySelectorAll('.screen-card:not(.hidden)');
                if (visibleCards.length === 0) {
                    panel.classList.add('opacity-40', 'grayscale');
                } else {
                    panel.classList.remove('opacity-40', 'grayscale');
                }
            }
        });

        if (matchBadge) {
            matchBadge.innerText = 'تم العثور على ' + matchedCount + ' شاشة';
            matchBadge.classList.remove('hidden');
        }
    }
</script>
@endsection
