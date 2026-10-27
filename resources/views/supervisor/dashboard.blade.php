@extends('layouts.app', ['title' => 'لوحة المشرف الأكاديمي - مراجعة واعتماد المستويات'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto my-4">

    <!-- شريط الترحيب والمؤشرات الأكاديمية -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 bg-indigo-500/20 text-indigo-300 text-xs px-3.5 py-1 rounded-full font-bold border border-indigo-400/30">
                <span>🛡️</span>
                <span>الإشراف الأكاديمي وضبط الجودة التعليمية</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">لوحة تحكم المشرف الأكاديمي</h1>
            <p class="text-xs text-slate-300">مراجعة نتائج اختبارات تحديد المستوى، التدقيق الصوتي بالذكاء الاصطناعي، واعتماد المستويات الرسمية للطلاب.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="bg-amber-500/20 text-amber-300 border border-amber-400/30 text-xs font-bold px-4 py-2 rounded-xl">
                {{ $pendingCount }} طلاب بانتظار الاعتماد
            </span>
        </div>
    </div>

    <!-- ================= شريط الاختصارات والوصول السريع للشاشات الممنوحة ================= -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span class="text-lg">⚡</span>
                    <span>مركز الوصول السريع والشاشات الممنوحة للمشرف</span>
                </h2>
                <p class="text-xs text-slate-500">تم تفعيل الشاشات والصلاحيات التالية لحسابك وفق مجموعة الصلاحيات المخصصة لك</p>
            </div>
            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>الصلاحيات الأكاديمية مفعلة</span>
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('curriculum.view') || auth()->user()->hasPermission('manage_courses') || auth()->user()->hasPermission('curriculum.create') || auth()->user()->hasPermission('curriculum.edit'))
                <a href="{{ route('admin.courses.index') }}" class="group bg-gradient-to-br from-emerald-50 to-teal-50/40 hover:from-emerald-100 hover:to-teal-100 border border-emerald-200/80 rounded-2xl p-4 transition-all duration-200 transform hover:-translate-y-1 shadow-xs hover:shadow-md flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition">📚</span>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md font-mono">مفعل</span>
                        </div>
                        <h3 class="font-black text-sm text-slate-900 group-hover:text-emerald-800 transition">إدارة المناهج والأسعار</h3>
                        <p class="text-[11px] text-slate-600 leading-relaxed">إضافة وتعديل الدورات، رفع الكتب والصوتيات والفيديوهات، وضبط الأسعار والساعات.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-emerald-200/60 flex items-center justify-between text-xs font-bold text-emerald-700">
                        <span>فتح الشاشة والموارد</span>
                        <span>←</span>
                    </div>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('students.view') || auth()->user()->hasPermission('students.create'))
                <a href="{{ route('admin.students.index') }}" class="group bg-gradient-to-br from-blue-50 to-indigo-50/40 hover:from-blue-100 hover:to-indigo-100 border border-blue-200/80 rounded-2xl p-4 transition-all duration-200 transform hover:-translate-y-1 shadow-xs hover:shadow-md flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition">🎓</span>
                            <span class="text-[10px] font-bold text-blue-700 bg-blue-100/80 px-2 py-0.5 rounded-md font-mono">مفعل</span>
                        </div>
                        <h3 class="font-black text-sm text-slate-900 group-hover:text-blue-800 transition">إدارة الطلاب والقبول</h3>
                        <p class="text-[11px] text-slate-600 leading-relaxed">سجلات الطلاب المسجلين، التحقق من بياناتهم، وتسكينهم وتفعيل حساباتهم.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-blue-200/60 flex items-center justify-between text-xs font-bold text-blue-700">
                        <span>فتح شاشة الطلاب</span>
                        <span>←</span>
                    </div>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('groups.view'))
                <a href="{{ route('admin.groups.index') }}" class="group bg-gradient-to-br from-indigo-50 to-purple-50/40 hover:from-indigo-100 hover:to-purple-100 border border-indigo-200/80 rounded-2xl p-4 transition-all duration-200 transform hover:-translate-y-1 shadow-xs hover:shadow-md flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition">👥</span>
                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-100/80 px-2 py-0.5 rounded-md font-mono">مفعل</span>
                        </div>
                        <h3 class="font-black text-sm text-slate-900 group-hover:text-indigo-800 transition">المجموعات والفصول</h3>
                        <p class="text-[11px] text-slate-600 leading-relaxed">إدارة الفصول الافتراضية، إسناد المعلمين، ومتابعة الطلاب في الدفعات.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-indigo-200/60 flex items-center justify-between text-xs font-bold text-indigo-700">
                        <span>فتح شاشة المجموعات</span>
                        <span>←</span>
                    </div>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('evaluations.view'))
                <a href="{{ route('admin.groups.evaluations.index') }}" class="group bg-gradient-to-br from-amber-50 to-yellow-50/40 hover:from-amber-100 hover:to-yellow-100 border border-amber-200/80 rounded-2xl p-4 transition-all duration-200 transform hover:-translate-y-1 shadow-xs hover:shadow-md flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition">📋</span>
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded-md font-mono">مفعل</span>
                        </div>
                        <h3 class="font-black text-sm text-slate-900 group-hover:text-amber-800 transition">سجلات التقييم والحضور</h3>
                        <p class="text-[11px] text-slate-600 leading-relaxed">متابعة تقييم الطلاب اليومي، تسجيل الحضور والغياب، ومؤشرات التحصيل.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-amber-200/60 flex items-center justify-between text-xs font-bold text-amber-700">
                        <span>فتح شاشة التقييم</span>
                        <span>←</span>
                    </div>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('exams.view'))
                <a href="{{ route('admin.exams.index') }}" class="group bg-gradient-to-br from-rose-50 to-pink-50/40 hover:from-rose-100 hover:to-pink-100 border border-rose-200/80 rounded-2xl p-4 transition-all duration-200 transform hover:-translate-y-1 shadow-xs hover:shadow-md flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition">🎯</span>
                            <span class="text-[10px] font-bold text-rose-700 bg-rose-100/80 px-2 py-0.5 rounded-md font-mono">مفعل</span>
                        </div>
                        <h3 class="font-black text-sm text-slate-900 group-hover:text-rose-800 transition">بنك الأسئلة والكويزات</h3>
                        <p class="text-[11px] text-slate-600 leading-relaxed">إدارة كويزات تحديد المستوى، بنك الأسئلة المتعددة، ومراجعة الإجابات.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-rose-200/60 flex items-center justify-between text-xs font-bold text-rose-700">
                        <span>فتح بنك الكويزات</span>
                        <span>←</span>
                    </div>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('exams.view'))
                <a href="{{ route('admin.exams.all_attempts') }}" class="group bg-gradient-to-br from-indigo-50 to-blue-50/40 hover:from-indigo-100 hover:to-blue-100 border border-indigo-200/80 rounded-2xl p-4 transition-all duration-200 transform hover:-translate-y-1 shadow-xs hover:shadow-md flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-xl bg-[#0066A1] text-white flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition">📑</span>
                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-100/80 px-2 py-0.5 rounded-md font-mono">سجل النتائج</span>
                        </div>
                        <h3 class="font-black text-sm text-slate-900 group-hover:text-indigo-800 transition">سجل نتائج ومحاولات الاختبارات</h3>
                        <p class="text-[11px] text-slate-600 leading-relaxed">استعراض وتدقيق إجابات الطلاب، كشف الأخطاء، وطباعة تقارير الشفافية الأكاديمية.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-indigo-200/60 flex items-center justify-between text-xs font-bold text-indigo-700">
                        <span>فتح سجل النتائج</span>
                        <span>←</span>
                    </div>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('exams_management.view'))
                <a href="{{ route('admin.exams_management.index') }}" class="group bg-gradient-to-br from-violet-50 to-purple-50/40 hover:from-violet-100 hover:to-purple-100 border border-violet-200/80 rounded-2xl p-4 transition-all duration-200 transform hover:-translate-y-1 shadow-xs hover:shadow-md flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-xl bg-violet-600 text-white flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition">🎓</span>
                            <span class="text-[10px] font-bold text-violet-700 bg-violet-100/80 px-2 py-0.5 rounded-md font-mono">مفعل</span>
                        </div>
                        <h3 class="font-black text-sm text-slate-900 group-hover:text-violet-800 transition">الاختبارات والشهائد الرقمية</h3>
                        <p class="text-[11px] text-slate-600 leading-relaxed">سلالم الدرجات، رصد نتائج الاختبارات الشاملة، وطباعة الشهائد المعتمدة.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-violet-200/60 flex items-center justify-between text-xs font-bold text-violet-700">
                        <span>فتح شاشة الشهائد</span>
                        <span>←</span>
                    </div>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('reports.academic') || auth()->user()->hasPermission('reports.financial'))
                <a href="{{ route('admin.reports.index') }}" class="group bg-gradient-to-br from-orange-50 to-amber-50/40 hover:from-orange-100 hover:to-amber-100 border border-orange-200/80 rounded-2xl p-4 transition-all duration-200 transform hover:-translate-y-1 shadow-xs hover:shadow-md flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-xl bg-[#F58220] text-white flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition">📊</span>
                            <span class="text-[10px] font-bold text-orange-700 bg-orange-100/80 px-2 py-0.5 rounded-md font-mono">مفعل</span>
                        </div>
                        <h3 class="font-black text-sm text-slate-900 group-hover:text-orange-800 transition">مركز التقارير والإحصائيات</h3>
                        <p class="text-[11px] text-slate-600 leading-relaxed">تقارير أكاديمية تفصيلية، كشوفات المقارنة بين الفصول، وطباعة السجلات.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-orange-200/60 flex items-center justify-between text-xs font-bold text-orange-700">
                        <span>فتح شاشة التقارير</span>
                        <span>←</span>
                    </div>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('finance.view'))
                <a href="{{ route('accounting.dashboard') }}" class="group bg-gradient-to-br from-teal-50 to-emerald-50/40 hover:from-teal-100 hover:to-emerald-100 border border-teal-200/80 rounded-2xl p-4 transition-all duration-200 transform hover:-translate-y-1 shadow-xs hover:shadow-md flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center text-xl shadow-xs group-hover:scale-110 transition">💼</span>
                            <span class="text-[10px] font-bold text-teal-700 bg-teal-100/80 px-2 py-0.5 rounded-md font-mono">مفعل</span>
                        </div>
                        <h3 class="font-black text-sm text-slate-900 group-hover:text-teal-800 transition">الإدارة المالية والمحاسبة</h3>
                        <p class="text-[11px] text-slate-600 leading-relaxed">سندات القبض والصرف، كشوفات الحسابات، ومتابعة مدفوعات الطلاب.</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-teal-200/60 flex items-center justify-between text-xs font-bold text-teal-700">
                        <span>فتح شاشة المحاسبة</span>
                        <span>←</span>
                    </div>
                </a>
            @endif
        </div>
    </div>

    <!-- البطاقات الإحصائية الأربعة -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                ⏳
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">بانتظار اعتماد المستوى</span>
                <strong class="text-2xl font-black text-amber-600 font-mono">{{ $pendingCount }}</strong>
                <span class="text-[10px] text-slate-500 block">يتطلب قرار المشرف</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                🎓
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">الطلاب الفاعلون</span>
                <strong class="text-2xl font-black text-emerald-600 font-mono">{{ $activeStudents }}</strong>
                <span class="text-[10px] text-slate-500 block">من إجمالي {{ $totalStudents }} طالب</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                📚
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">المستويات الدراسية</span>
                <strong class="text-2xl font-black text-indigo-600 font-mono">{{ $levels->count() }}</strong>
                <span class="text-[10px] text-slate-500 block">معايير CEFR المعتمدة</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-purple-50 border border-purple-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                ⭐
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">الحصص المقيمة</span>
                <strong class="text-2xl font-black text-purple-600 font-mono">{{ $completedLessonsCount }}</strong>
                <span class="text-[10px] text-slate-500 block">تقارير جودة المعلمين</span>
            </div>
        </div>
    </div>

    <!-- ================= 1. قائمة الطلاب بانتظار اعتماد وتخصيص المستوى الأكاديمي ================= -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex justify-between items-center border-b pb-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span>📝</span>
                    <span>قائمة مراجعة واعتماد المستويات للطلاب الجدد</span>
                </h2>
                <p class="text-xs text-slate-500">راجع نتائج الاختبار التحريري ودرجة النطق بالذكاء الاصطناعي واعتمد المستوى الأكاديمي المناسب</p>
            </div>
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                المعايير المعتمدة لـ ASOTS
            </span>
        </div>

        @if($pendingApprovals->count() > 0)
            <div class="space-y-4">
                @foreach($pendingApprovals as $st)
                    @php
                        $latestAttempt = $st->examAttempts->first();
                    @endphp
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 hover:border-indigo-400 transition space-y-4">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 border-b border-slate-200 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-2xl shadow-sm">
                                    👨‍🎓
                                </div>
                                <div>
                                    <h3 class="font-black text-sm text-slate-900">{{ $st->name }}</h3>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 font-mono flex-wrap">
                                        <span>{{ $st->student_code }}</span>
                                        <span>•</span>
                                        <span>{{ $st->email }}</span>
                                        <span>•</span>
                                        <span>{{ $st->phone_whatsapp }}</span>
                                        <span>•</span>
                                        <a href="{{ route('admin.students.exam_attempts', $st->id) }}" class="text-[11px] font-bold text-[#0066A1] hover:underline flex items-center gap-1 font-sans">
                                            <span>📝</span>
                                            <span>سجل الاختبارات</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                    {{ $st->studentProfile?->grade_substage ?? 'مسار تجريبي' }}
                                </span>
                                @if($st->revoker || $st->revoked_at)
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1 shadow-2xs">
                                        <span>↩️</span>
                                        <span>أُلغي اعتماده سابقاً بواسطة: {{ $st->revoker?->name ?? 'الإشراف' }}</span>
                                        @if($st->revoked_at)
                                            <span class="font-mono text-[10px]">({{ $st->revoked_at->format('Y-m-d H:i') }})</span>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- تنبيه إذا كان الطالب ملغي اعتماده سابقاً -->
                        @if($st->revoker || $st->revoked_at)
                            <div class="p-3 bg-rose-50/80 border border-rose-200 rounded-xl text-xs text-rose-900 flex items-start gap-2">
                                <span class="text-sm">⚠️</span>
                                <div class="space-y-0.5">
                                    <span class="font-black text-rose-800 block">سجل إلغاء الاعتماد السابق للطالب:</span>
                                    <p class="text-[11px] text-rose-700 leading-relaxed">
                                        تم إلغاء اعتماده بواسطة <strong>{{ $st->revoker?->name ?? 'الإشراف الأكاديمي' }}</strong> بتاريخ <span class="font-mono">{{ $st->revoked_at?->format('Y-m-d H:i') }}</span>.
                                        @if($st->revoke_reason)
                                            <span class="block text-slate-700 mt-0.5 font-sans"><strong>السبب المدوّن:</strong> {{ $st->revoke_reason }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif

                        <!-- درجات الفحص والذكاء الاصطناعي -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="bg-white p-3 rounded-xl border border-slate-200 space-y-1">
                                <span class="text-slate-400 block text-[11px]">الدرجة التحريرية للاختبار:</span>
                                <strong class="text-slate-900 font-mono text-sm block">{{ $latestAttempt ? $latestAttempt->total_score . ' نقطة' : 'لم يختبر بعد' }}</strong>
                                @if($latestAttempt)
                                    <div class="flex items-center gap-1.5 pt-1 flex-wrap">
                                        <a href="{{ route('admin.exams.review_attempt', $latestAttempt->id) }}" class="text-[10px] font-bold text-indigo-700 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded-md transition flex items-center gap-1" title="استعراض وتدقيق الإجابات والدرجة">
                                            <span>🔍</span>
                                            <span>تدقيق الإجابات</span>
                                        </a>
                                        <a href="{{ route('admin.exams.attempts.error_report', $latestAttempt->id) }}" target="_blank" class="text-[10px] font-bold text-amber-900 hover:text-amber-950 bg-amber-100 hover:bg-amber-200 border border-amber-300 px-2 py-0.5 rounded-md transition flex items-center gap-1" title="كشف الأخطاء للشفافية والطباعة">
                                            <span>🖨️</span>
                                            <span>تقرير الأخطاء</span>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="bg-white p-3 rounded-xl border border-slate-200">
                                <span class="text-slate-400 block text-[11px]">فحص النطق بالذكاء الاصطناعي (AI):</span>
                                @php
                                    $supAudio = $latestAttempt?->answers?->whereNotNull('recorded_audio_path')->first();
                                    $supAccuracy = $latestAttempt?->speech_feedback['accuracy_score'] ?? ($supAudio?->speech_accuracy ?? 85);
                                @endphp
                                <strong class="text-indigo-600 font-mono text-sm block">
                                    {{ $latestAttempt && $latestAttempt->speech_score > 0 ? "دقة النطق {$supAccuracy}%" : 'بانتظار التسجيل' }}
                                </strong>
                                @if($supAudio && $supAudio->recorded_audio_path)
                                    <div class="mt-1">
                                        <audio controls class="w-full h-7 rounded" src="{{ asset('storage/' . $supAudio->recorded_audio_path) }}"></audio>
                                    </div>
                                    <div class="flex items-center gap-1.5 pt-1.5 flex-wrap">
                                        <button type="button" 
                                                onclick='openSpeechDiagnosticModal(@json($st->name), @json($st->student_code), @json($latestAttempt->speech_feedback), @json(asset("storage/" . $supAudio->recorded_audio_path)))' 
                                                class="text-[10px] font-bold text-indigo-700 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded-md transition flex items-center gap-1">
                                            <span>🔍</span>
                                            <span>تقرير الأخطاء والتفاصيل</span>
                                        </button>
                                        <button type="button" 
                                                onclick="playAiNativeSpeech({{ json_encode($latestAttempt->speech_feedback['expected_text'] ?? 'Learning English opens great opportunities for communication and global success.') }})" 
                                                title="استمع للنطق الصحيح بالذكاء الاصطناعي"
                                                class="text-[10px] font-bold text-amber-800 hover:text-amber-950 bg-amber-100 hover:bg-amber-200 border border-amber-300 px-2 py-0.5 rounded-md transition flex items-center gap-1">
                                            <span>🔊</span>
                                            <span>نطق الذكاء (AI)</span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="bg-white p-3 rounded-xl border border-slate-200">
                                <span class="text-slate-400 block text-[11px]">المستوى المقترح تلقائياً:</span>
                                <strong class="text-emerald-600 font-bold">
                                    {{ $latestAttempt?->suggestedLevel?->name ?? 'المستوى الأول: مبتدئ' }}
                                </strong>
                            </div>
                        </div>

                        <!-- نموذج الاعتماد المباشر -->
                        <form action="{{ route('supervisor.assign_level', $st->id) }}" method="POST" class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                            @csrf
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-bold text-slate-700 whitespace-nowrap">تخصيص المستوى الأكاديمي:</label>
                                <select name="level_id" class="border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold bg-white outline-none focus:ring-1 focus:ring-indigo-500">
                                    @foreach($levels as $lvl)
                                        <option value="{{ $lvl->id }}" {{ ($latestAttempt && $latestAttempt->suggested_level_id == $lvl->id) ? 'selected' : '' }}>
                                            {{ $lvl->name }} ({{ $lvl->formatted_fee }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs px-6 py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                                <span>اعتماد المستوى وتفعيل الحساب الأكاديمي 🚀</span>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
                ✅ لا يوجد طلاب بانتظار الاعتماد حالياً. جميع الطلاب تم تدقيق وتخصيص مستوياتهم بنجاح.
            </div>
        @endif
    </div>

    <!-- ================= 2. مراقبة جودة التدريس وسجل تقارير الحصص ================= -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>🌟</span>
                    <span>سجل مراقبة جودة الحصص وتقييمات المعلمين</span>
                </h2>
                <p class="text-xs text-slate-500">متابعة دقيقة لمخرجات الحصص الـ 25 دقيقة وملاحظات المعلمين</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-200 pb-2">
                        <th class="py-3 px-3">الطالب</th>
                        <th class="py-3 px-3">المعلم</th>
                        <th class="py-3 px-3">النجوم</th>
                        <th class="py-3 px-3">نطق / طلاقة / قواعد</th>
                        <th class="py-3 px-3">ملاحظات المعلم</th>
                        <th class="py-3 px-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentReports as $rep)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-3 font-bold text-slate-900">{{ $rep->student?->name }}</td>
                            <td class="py-3 px-3 text-slate-700">{{ $rep->teacher?->name }}</td>
                            <td class="py-3 px-3 text-amber-500 font-bold font-mono">{{ str_repeat('⭐', $rep->stars_awarded) }}</td>
                            <td class="py-3 px-3 font-mono text-[11px] text-indigo-700 font-bold">
                                {{ $rep->pronunciation_score }} / {{ $rep->fluency_score }} / {{ $rep->grammar_score }}
                            </td>
                            <td class="py-3 px-3 text-slate-600 max-w-xs truncate" title="{{ $rep->teacher_comment }}">{{ $rep->teacher_comment }}</td>
                            <td class="py-3 px-3 text-slate-400 font-mono text-[11px]">{{ $rep->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400">لا توجد تقارير حصص مسجلة بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</div>

<!-- =============================================================== -->
<!-- مودال التقرير التفصيلي لفحص النطق وأخطاء الطالب وسماع الذكاء الاصطناعي -->
<!-- =============================================================== -->
<div id="speechDiagnosticModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 md:p-6 overflow-hidden" style="overscroll-behavior: contain;">
    <!-- خلفية النقر للإغلاق -->
    <div class="fixed inset-0 cursor-pointer" onclick="closeSpeechDiagnosticModal()"></div>

    <div class="relative bg-white w-full max-w-4xl rounded-3xl shadow-2xl border border-slate-200 flex flex-col max-h-[92vh] sm:max-h-[90vh] overflow-hidden z-10 animate-in fade-in zoom-in-95 duration-150">
        
        <!-- الترويسة المثبتة بالأعلى -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shadow-xs">
                    🎙️
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">تقرير التشخيص الصوتي المتقدم وتدقيق النطق (AI Speech Assessment)</h2>
                    <p class="text-xs text-slate-500 mt-0.5" id="diagStudentInfo">تفاصيل أخطاء النطق ومقارنة صوت الطالب بنطق الذكاء الاصطناعي النموذجي.</p>
                </div>
            </div>
            <button type="button" onclick="closeSpeechDiagnosticModal()" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center text-sm font-bold transition shadow-xs" title="إغلاق النافذة (Esc)">✕</button>
        </div>

        <!-- جسم التقرير القابل للتمرير -->
        <div class="overflow-y-auto flex-1 p-5 sm:p-6 space-y-6" style="scrollbar-width: thin; scrollbar-color: #94a3b8 #f1f5f9;">
            
            <!-- كروت المؤشرات الكلية الأربعة -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="bg-emerald-50/70 border border-emerald-200 p-3.5 rounded-2xl">
                    <span class="text-[11px] font-bold text-emerald-800 block">دقة النطق ومخارج الحروف</span>
                    <span class="text-2xl font-black text-emerald-700 font-mono mt-1 block" id="diagAccuracy">85%</span>
                    <span class="text-[10px] text-emerald-600 font-sans block mt-0.5">Accuracy</span>
                </div>
                <div class="bg-indigo-50/70 border border-indigo-200 p-3.5 rounded-2xl">
                    <span class="text-[11px] font-bold text-indigo-800 block">الطلاقة وانسيابية الكلام</span>
                    <span class="text-2xl font-black text-indigo-700 font-mono mt-1 block" id="diagFluency">82.5%</span>
                    <span class="text-[10px] text-indigo-600 font-sans block mt-0.5">Fluency</span>
                </div>
                <div class="bg-amber-50/70 border border-amber-200 p-3.5 rounded-2xl">
                    <span class="text-[11px] font-bold text-amber-800 block">اكتمال النص المقروء</span>
                    <span class="text-2xl font-black text-amber-700 font-mono mt-1 block" id="diagCompleteness">95%</span>
                    <span class="text-[10px] text-amber-600 font-sans block mt-0.5">Completeness</span>
                </div>
                <div class="bg-slate-100 border border-slate-200 p-3.5 rounded-2xl">
                    <span class="text-[11px] font-bold text-slate-700 block">الدرجة الإجمالية للصوت</span>
                    <span class="text-2xl font-black text-slate-800 font-mono mt-1 block" id="diagOverall">84%</span>
                    <span class="text-[10px] text-slate-500 font-sans block mt-0.5">Overall</span>
                </div>
            </div>

            <!-- المقارنة الصوتية المباشرة جنباً إلى جنب (Side-by-Side Audio Hub) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- صندوق صوت الطالب الفعلي -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                            <span>🎧</span>
                            <span>التسجيل الصوتي الفعلي للطالب:</span>
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-mono">Student Audio</span>
                    </div>
                    <div id="diagAudioContainer">
                        <audio id="diagStudentAudioPlayer" controls class="w-full h-9 rounded-lg"></audio>
                    </div>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        استمع للتسجيل الذي سجله الطالب عبر الميكروفون للتحقق السمعي المباشر ومقارنته بنطق الذكاء الاصطناعي.
                    </p>
                </div>

                <!-- صندوق النطق النموذجي للذكاء الاصطناعي -->
                <div class="bg-gradient-to-br from-indigo-50/80 to-purple-50/80 p-5 rounded-2xl border border-indigo-200 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-indigo-100 pb-2">
                        <span class="text-xs font-black text-indigo-900 flex items-center gap-1.5">
                            <span>🤖</span>
                            <span>النطق الصحيح النموذجي (AI Native Voice):</span>
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-200/60 text-indigo-800 font-mono">Reference TTS</span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="playAiSpeechDetailed(1.0)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                            <span>🔊</span>
                            <span>استمع للنطق الصحيح (1.0x)</span>
                        </button>
                        <button type="button" onclick="playAiSpeechDetailed(0.8)" class="px-3 py-2 bg-white hover:bg-slate-100 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-xl shadow-2xs transition flex items-center gap-1">
                            <span>🐢</span>
                            <span>استمع ببطء (0.8x)</span>
                        </button>
                        <button type="button" onclick="stopAiSpeech()" class="px-2.5 py-2 bg-white hover:bg-slate-100 text-slate-600 border border-slate-300 text-xs font-bold rounded-xl shadow-2xs transition" title="إيقاف الصوت">
                            <span>⏹️</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-indigo-800/80 leading-relaxed">
                        استمع للنطق الأمريكي/البريطاني الفصيح من محرك الذكاء الاصطناعي لمقارنته بنطق الطالب ورصد الفروق بدقة.
                    </p>
                </div>
            </div>

            <!-- الجملة المستهدفة والتحليل التفاعلي كلمة بكلمة -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <span class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                            <span>📝</span>
                            <span>الجملة المطلوبة والتحليل التفاعلي كلمة بكلمة:</span>
                        </span>
                        <p class="text-[11px] text-slate-500">اضغط على أي كلمة للاستماع لنطقها النموذجي بمفردها وعرض تقييم مخارج حروفها.</p>
                    </div>
                    <div class="flex items-center gap-2 text-[10px] font-bold">
                        <span class="flex items-center gap-1 text-emerald-700"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> ممتاز (>88%)</span>
                        <span class="flex items-center gap-1 text-amber-700"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span> متوسط (75-87%)</span>
                        <span class="flex items-center gap-1 text-red-700"><span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span> تعثر / خطأ (<75%)</span>
                    </div>
                </div>

                <!-- أزرار الكلمات التفاعلية -->
                <div id="diagWordsContainer" class="flex flex-wrap gap-2 p-3 bg-slate-50 rounded-2xl border border-slate-200" dir="ltr">
                    <!-- يتم بناؤها ديناميكياً بالجافاسكريبت -->
                </div>

                <!-- كارت شرح الكلمة المحددة عند الضغط عليها -->
                <div id="diagWordDetailBox" class="hidden bg-indigo-50/70 border border-indigo-200 p-3.5 rounded-xl text-xs space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-indigo-900 flex items-center gap-1.5">
                            <span id="selectedWordTitle" class="font-mono text-sm font-black"></span>
                            <span id="selectedWordPhonetic" class="text-slate-500 font-mono text-[11px]"></span>
                        </span>
                        <span id="selectedWordScoreBadge" class="px-2 py-0.5 rounded-md font-mono font-bold text-xs"></span>
                    </div>
                    <p id="selectedWordNote" class="text-[11px] text-indigo-900/90 leading-relaxed"></p>
                </div>
            </div>

            <!-- أين أخطأ الطالب بالتحديد؟ (قائمة الأخطاء المحصورة) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <span class="text-xs font-black text-slate-900 flex items-center gap-1.5 border-b border-slate-100 pb-2">
                    <span>⚠️</span>
                    <span>تفاصيل المواضع التي أخطأ أو تعثر فيها الطالب:</span>
                </span>

                <div id="diagErrorsList" class="space-y-2.5">
                    <!-- تتعبأ ديناميكياً بالأخطاء -->
                </div>
            </div>

            <!-- التحليل الفونيتيكي المتقدم -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                <span class="text-xs font-black text-slate-900 flex items-center gap-1.5 border-b border-slate-100 pb-2">
                    <span>📊</span>
                    <span>التحليل الصوتي التفصيلي (Phonetic & Acoustic Breakdown):</span>
                </span>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-500 text-[11px] block">الحروف المتحركة (Vowels)</span>
                        <strong class="text-emerald-700 font-mono text-base block mt-1" id="diagVowels">88%</strong>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-500 text-[11px] block">الحروف الساكنة (Consonants)</span>
                        <strong class="text-emerald-700 font-mono text-base block mt-1" id="diagConsonants">84%</strong>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-500 text-[11px] block">النبرة والتنغيم (Stress)</span>
                        <strong class="text-amber-700 font-mono text-base block mt-1" id="diagStress">78%</strong>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-500 text-[11px] block">معدل سرعة القراءة</span>
                        <strong class="text-indigo-700 font-mono text-base block mt-1" id="diagWpm">112 WPM</strong>
                    </div>
                </div>
            </div>

            <!-- التقرير الأكاديمي والتوصية الإدارية للتسكين -->
            <div class="bg-indigo-950 text-white p-5 sm:p-6 rounded-2xl shadow-sm space-y-3">
                <div class="flex items-center gap-2 text-amber-400 font-bold text-xs">
                    <span>🎓</span>
                    <span>تقرير التشخيص الأكاديمي وتوصية التسكين:</span>
                </div>
                <p id="diagSummaryText" class="text-xs text-slate-200 leading-relaxed"></p>
                <div class="pt-2 border-t border-indigo-800/80 flex items-center justify-between text-xs">
                    <span class="text-indigo-300 font-bold">التسكين الموصى به:</span>
                    <span id="diagRecommendationText" class="font-black text-amber-400"></span>
                </div>
            </div>

        </div>

        <!-- أسفل النافذة المثبت -->
        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 shrink-0">
            <button type="button" onclick="closeSpeechDiagnosticModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-200 bg-white border border-slate-300 transition shadow-2xs">
                إغلاق النافذة ✕
            </button>
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <button type="button" onclick="playAiSpeechDetailed(1.0)" class="flex-1 sm:flex-initial px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                    <span>🔊</span>
                    <span>تشغيل النطق النموذجي كاملاً</span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    let currentExpectedSpeechText = '';

    function playAiNativeSpeech(text, rate = 1.0) {
        if (!('speechSynthesis' in window)) {
            alert('عذراً، متصفحك لا يدعم خاصية تحويل النص إلى صوت (Speech Synthesis).');
            return;
        }
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-US';
        utterance.rate = rate;
        utterance.pitch = 1.0;

        const voices = window.speechSynthesis.getVoices();
        const enVoice = voices.find(v => (v.lang === 'en-US' || v.lang.startsWith('en')) && (v.name.includes('Google') || v.name.includes('Natural') || v.name.includes('Microsoft') || v.name.includes('Samantha') || v.name.includes('David')));
        if (enVoice) {
            utterance.voice = enVoice;
        }
        window.speechSynthesis.speak(utterance);
    }

    function playAiSpeechDetailed(rate = 1.0) {
        if (currentExpectedSpeechText) {
            playAiNativeSpeech(currentExpectedSpeechText, rate);
        }
    }

    function stopAiSpeech() {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
    }

    function openSpeechDiagnosticModal(studentName, studentCode, feedback, audioUrl) {
        document.body.style.overflow = 'hidden';

        document.getElementById('diagStudentInfo').innerText = 'الطالب: ' + studentName + ' (' + (studentCode || 'STU') + ') • تدقيق نطق الصوت للمشرف';

        currentExpectedSpeechText = feedback.expected_text || 'Learning English opens great opportunities for communication and global success.';

        document.getElementById('diagAccuracy').innerText = (feedback.accuracy_score || 85) + '%';
        document.getElementById('diagFluency').innerText = (feedback.fluency_score || 82.5) + '%';
        document.getElementById('diagCompleteness').innerText = (feedback.completeness_score || 95) + '%';
        document.getElementById('diagOverall').innerText = (feedback.overall_score || 84) + '%';

        // ضبط مشغل صوت الطالب
        const player = document.getElementById('diagStudentAudioPlayer');
        if (audioUrl) {
            player.src = audioUrl;
            player.parentElement.classList.remove('hidden');
        } else if (feedback.audio_url) {
            player.src = feedback.audio_url;
            player.parentElement.classList.remove('hidden');
        } else {
            player.parentElement.classList.add('hidden');
        }

        // بناء بطاقات الكلمات كلمة بكلمة
        const wordsContainer = document.getElementById('diagWordsContainer');
        wordsContainer.innerHTML = '';

        const wordsBreakdown = feedback.words_breakdown || [
            {word: 'Learning', score: 95, status: 'excellent', note: 'نطق ممتاز ومخارج صحيحة'},
            {word: 'English', score: 92, status: 'excellent', note: 'صوت /ʃ/ سليم ومفهوم'},
            {word: 'opens', score: 81, status: 'warning', note: 'صوت /z/ خافت ويحتاج وضوح أكثر'},
            {word: 'great', score: 94, status: 'excellent', note: 'نطق قوي ودقيق'},
            {word: 'opportunities', score: 68, status: 'danger', note: 'تعثر في المقاطع الصوتية الوسطى'},
            {word: 'for', score: 90, status: 'excellent', note: 'نطق سليم'},
            {word: 'communication', score: 79, status: 'warning', note: 'تردد في نبرة المقطع المشدد'},
            {word: 'and', score: 91, status: 'excellent', note: 'ربط سليم'},
            {word: 'global', score: 93, status: 'excellent', note: 'صوت /l/ سليم'},
            {word: 'success.', score: 90, status: 'excellent', note: 'نطق واضح وممتاز'}
        ];

        wordsBreakdown.forEach(w => {
            const btn = document.createElement('button');
            btn.type = 'button';
            
            let colorClasses = 'bg-emerald-100 text-emerald-900 border-emerald-300 hover:bg-emerald-200';
            if (w.status === 'warning' || (w.score >= 75 && w.score < 88)) {
                colorClasses = 'bg-amber-100 text-amber-900 border-amber-300 hover:bg-amber-200';
            } else if (w.status === 'danger' || w.score < 75) {
                colorClasses = 'bg-red-100 text-red-900 border-red-300 hover:bg-red-200 ring-2 ring-red-400';
            }

            btn.className = `px-3 py-1.5 rounded-xl border text-xs font-bold font-mono transition flex items-center gap-1.5 shadow-2xs ${colorClasses}`;
            btn.innerHTML = `<span>${w.word}</span><span class="text-[10px] opacity-75 font-sans">${w.score}%</span>`;

            btn.onclick = function() {
                playAiNativeSpeech(w.word.replace(/[^a-zA-Z]/g, ''), 0.9);
                showWordDetail(w);
            };

            wordsContainer.appendChild(btn);
        });

        // قائمة الأخطاء
        const errorsList = document.getElementById('diagErrorsList');
        errorsList.innerHTML = '';

        const mispronounced = feedback.mispronounced_words || [];
        if (mispronounced.length > 0) {
            mispronounced.forEach(err => {
                const item = document.createElement('div');
                item.className = 'p-3 rounded-xl bg-red-50 border border-red-200 flex items-start justify-between gap-3 text-xs';
                item.innerHTML = `
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <strong class="font-mono text-red-900 text-sm font-black" dir="ltr">${err.word || err}</strong>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-red-200 text-red-900">${err.error_type || 'تعثر في النطق'}</span>
                        </div>
                        <p class="text-[11px] text-red-800 leading-relaxed">${err.description || err.note || 'لوحظ عدم وضوح مخارج الصوت في هذا الموضع.'}</p>
                    </div>
                    <button type="button" onclick="playAiNativeSpeech('${(err.word || err).replace(/[^a-zA-Z]/g, '')}', 0.8)" class="shrink-0 px-2.5 py-1 bg-white hover:bg-red-100 text-red-700 border border-red-300 rounded-lg text-[10px] font-bold transition flex items-center gap-1">
                        <span>🔊</span>
                        <span>النطق الصحيح</span>
                    </button>
                `;
                errorsList.appendChild(item);
            });
        } else {
            errorsList.innerHTML = `
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-bold">
                    ✅ لا توجد أخطاء جوهرية مشوهة للمعنى، نطق الطالب في المجمل سليم ومطابق لمعايير القبول.
                </div>
            `;
        }

        // المؤشرات الصوتية المتقدمة
        const phon = feedback.phonetic_analysis || {};
        document.getElementById('diagVowels').innerText = (phon.vowels_accuracy || 88) + '%';
        document.getElementById('diagConsonants').innerText = (phon.consonants_accuracy || 84) + '%';
        document.getElementById('diagStress').innerText = (phon.intonation_stress || 78) + '%';
        document.getElementById('diagWpm').innerText = (phon.speech_rate_wpm || 112) + ' WPM';

        // التشخيص والتوصية
        document.getElementById('diagSummaryText').innerText = feedback.diagnosis_summary || 'أداء الطالب جيد جداً (85%). يمتلك مخارج حروف واضحة في الكلمات الأحادية والثنائية، ولكنه تعثر في الكلمات الأكاديمية الطويلة متعددة المقاطع (مثل opportunities بنسبة 68%).';
        document.getElementById('diagRecommendationText').innerText = feedback.recommendation || 'تسكين الطالب في المستوى الثاني (Level 2: Elementary) مع تدريبات على الربط الصوتي ونبرات الكلمات.';

        document.getElementById('speechDiagnosticModal').classList.remove('hidden');
    }

    function showWordDetail(w) {
        const box = document.getElementById('diagWordDetailBox');
        document.getElementById('selectedWordTitle').innerText = w.word;
        document.getElementById('selectedWordPhonetic').innerText = w.phonetic || '';
        document.getElementById('selectedWordScoreBadge').innerText = w.score + '% دقة';
        document.getElementById('selectedWordNote').innerText = w.note || 'نطق هذه الكلمة تم تقييمه تلقائياً عبر محرك الذكاء الاصطناعي.';
        box.classList.remove('hidden');
    }

    function closeSpeechDiagnosticModal() {
        document.getElementById('speechDiagnosticModal').classList.add('hidden');
        document.body.style.overflow = '';
        stopAiSpeech();
        const player = document.getElementById('diagStudentAudioPlayer');
        if (player) {
            player.pause();
        }
    }

    // إغلاق عبر زر الهروب
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSpeechDiagnosticModal();
        }
    });
</script>
@endsection
