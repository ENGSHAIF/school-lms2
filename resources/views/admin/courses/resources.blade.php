@extends('layouts.app', ['title' => 'بنك موارد ومناهج دورة ' . $course->name_en . ' - ASOTS'])

@section('content')
<div class="space-y-8">

    <!-- مسار التنقل والترويسة الرئيسية -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-2 flex-wrap">
                <a href="{{ route('admin.courses.index') }}" class="hover:text-indigo-600 transition">المناهج والأسعار</a>
                <span>/</span>
                <span class="text-slate-600 font-bold">{{ $course->level?->name_en ?: 'المستوى' }}</span>
                <span>/</span>
                <span class="text-indigo-600 font-black">{{ $course->name_en }}</span>
            </div>

            <div class="flex items-center gap-3">
                <span class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-2xl font-black shadow-xs shrink-0">
                    📚
                </span>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">
                        بنك موارد ومناهج: {{ $course->name_en }}
                        @if($course->name_ar && $course->name_ar !== $course->name_en)
                            <span class="text-base text-slate-500 font-bold block mt-0.5">({{ $course->name_ar }})</span>
                        @endif
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        إدارة محتوى الدورة المقسم حسب العناوين والأيام والوحدات (كتب، صوتيات، فيديوهات، صور، أوراق عمل، كويزات).
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap w-full md:w-auto">
            <a href="{{ route('admin.courses.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                <span>←</span>
                <span>المناهج والأسعار</span>
            </a>

            <!-- زر أهداف المنهج والأساسيات -->
            <button type="button" onclick="openEditCourseObjectivesModal()" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                <span>🎯</span>
                <span>أهداف المنهج والأساسيات</span>
            </button>

            <!-- زر إضافة مورد عام ثابت للدورة -->
            <button type="button" onclick="openAddResourceModal(null, 'مورد عام للدورة', true, 'teacher')" class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                <span>🏛️</span>
                <span>إضافة مورد عام للدورة</span>
            </button>

            @if($course->level_id)
                <a href="{{ route('admin.exams.create', $course->level_id) }}" class="px-4 py-2.5 bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>🎯</span>
                    <span>تصميم كويز جديد</span>
                </a>
            @endif

            <!-- زر إضافة عنوان / يوم دراسي جديد -->
            <button type="button" onclick="openAddSectionModal()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                <span>📌</span>
                <span>إضافة يوم / عنوان دراسي</span>
            </button>

            <!-- زر إضافة مورد تعليمي -->
            <button type="button" onclick="openAddResourceModal()" class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                <span>➕</span>
                <span>إضافة مورد تعليمي</span>
            </button>
        </div>
    </div>

    <!-- بطاقة معلومات الدورة والتسعيرة والساعات والمستوى التابعة له -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 rounded-3xl shadow-lg border border-indigo-900/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="space-y-1.5 max-w-2xl">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-400 bg-amber-400/10 px-2.5 py-0.5 rounded-full border border-amber-400/20">
                    دورة تابعة لمستوى: {{ $course->level?->name_en }} ({{ $course->level?->name_ar }})
                </span>
                <span class="text-[11px] font-mono font-bold text-amber-300 bg-amber-400/20 px-2.5 py-0.5 rounded-full border border-amber-400/30">
                    ⏱️ {{ $course->hours }} ساعة معتمدة
                </span>
                <span class="text-[11px] font-mono font-bold text-indigo-300 bg-indigo-400/20 px-2.5 py-0.5 rounded-full border border-indigo-400/30">
                    📌 {{ $sections->count() }} أقسام / أيام دراسية
                </span>
            </div>

            <h2 class="text-xl font-black text-white">
                {{ $course->name_en }}
                @if($course->name_ar)
                    <span class="text-sm font-normal text-slate-300 mr-2">({{ $course->name_ar }})</span>
                @endif
            </h2>

            <p class="text-xs text-slate-300 leading-relaxed">
                {{ $course->description ?: "دورة معتمدة بواقع {$course->hours} ساعة تدريبية تفاعلية مباشرة مع بنك مناهج رقمي شامل مقسم حسب الأيام والدروس." }}
            </p>
        </div>

        <div class="flex items-center gap-4 border-t md:border-t-0 md:border-r border-white/10 pt-3 md:pt-0 md:pr-6 shrink-0">
            <div>
                <span class="text-[11px] text-slate-400 block font-bold">تسعيرة الدورة الفردية:</span>
                <span class="text-2xl font-black text-amber-400 font-mono">{{ $course->formatted_fee }}</span>
                <span class="text-[10px] text-slate-300 block">{{ $course->currency_name }}</span>
            </div>
            @if($course->level)
                <div class="border-r border-white/10 pr-4">
                    <span class="text-[11px] text-slate-400 block font-bold">رسوم المستوى كاملاً:</span>
                    <span class="text-lg font-black text-emerald-400 font-mono">{{ $course->level->formatted_fee }}</span>
                    <span class="text-[10px] text-slate-300 block">{{ $course->level->currency_name }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- =============================================================== -->
    <!-- 1. بطاقة أهداف المنهج والدورة والأساسيات الثابتة (Objectives & Syllabus) -->
    <!-- =============================================================== -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7 space-y-5">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-xl font-black shadow-xs shrink-0">
                    🎯
                </span>
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-slate-900 leading-tight">
                        أهداف المنهج والدورة والأساسيات الثابتة
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        الأهداف العامة للدورة، مخرجات التعلم، مفردات المنهج التأسيسية، والمتطلبات السابقة.
                    </p>
                </div>
            </div>

            <button type="button" onclick="openEditCourseObjectivesModal()" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-black text-xs rounded-xl transition border border-indigo-200 flex items-center gap-1.5 shrink-0">
                <span>✏️</span>
                <span>تعديل الأهداف والأساسيات</span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- 1. أهداف المنهج والدورة -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-indigo-50/50 via-white to-indigo-50/20 border border-indigo-100 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-indigo-950 flex items-center gap-1.5">
                        <span>🎯</span>
                        <span>أهداف المنهج والدورة (Objectives)</span>
                    </span>
                    @if($course->course_objectives)
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" title="تم تحديد الأهداف"></span>
                    @endif
                </div>
                @if($course->course_objectives)
                    <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-white/80 p-3.5 rounded-xl border border-indigo-100/60 max-h-48 overflow-y-auto">
                        {{ $course->course_objectives }}
                    </div>
                @else
                    <div class="p-6 rounded-xl border border-dashed border-indigo-200 text-center text-xs text-indigo-400 space-y-2">
                        <span class="text-2xl block">🎯</span>
                        <p class="font-bold">لم تسجل أهداف الدورة بعد</p>
                        <button type="button" onclick="openEditCourseObjectivesModal()" class="text-[11px] text-indigo-600 font-black hover:underline">
                            + كتابة أهداف المنهج
                        </button>
                    </div>
                @endif
            </div>

            <!-- 2. مفردات وخطة المنهج التأسيسية -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-purple-50/50 via-white to-purple-50/20 border border-purple-100 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-purple-950 flex items-center gap-1.5">
                        <span>📋</span>
                        <span>مفردات وخطة المنهج (Syllabus)</span>
                    </span>
                    @if($course->course_syllabus)
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" title="تم تحديد المفردات"></span>
                    @endif
                </div>
                @if($course->course_syllabus)
                    <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-white/80 p-3.5 rounded-xl border border-purple-100/60 max-h-48 overflow-y-auto">
                        {{ $course->course_syllabus }}
                    </div>
                @else
                    <div class="p-6 rounded-xl border border-dashed border-purple-200 text-center text-xs text-purple-400 space-y-2">
                        <span class="text-2xl block">📋</span>
                        <p class="font-bold">لم تسجل مفردات المنهج بعد</p>
                        <button type="button" onclick="openEditCourseObjectivesModal()" class="text-[11px] text-purple-600 font-black hover:underline">
                            + كتابة مفردات المنهج
                        </button>
                    </div>
                @endif
            </div>

            <!-- 3. المتطلبات والأساسيات الهامة -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-amber-50/50 via-white to-amber-50/20 border border-amber-100 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-amber-950 flex items-center gap-1.5">
                        <span>📌</span>
                        <span>الأساسيات والمتطلبات (Prerequisites)</span>
                    </span>
                    @if($course->course_prerequisites)
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" title="تم تحديد المتطلبات"></span>
                    @endif
                </div>
                @if($course->course_prerequisites)
                    <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-white/80 p-3.5 rounded-xl border border-amber-100/60 max-h-48 overflow-y-auto">
                        {{ $course->course_prerequisites }}
                    </div>
                @else
                    <div class="p-6 rounded-xl border border-dashed border-amber-200 text-center text-xs text-amber-400 space-y-2">
                        <span class="text-2xl block">📌</span>
                        <p class="font-bold">لم تسجل المتطلبات بعد</p>
                        <button type="button" onclick="openEditCourseObjectivesModal()" class="text-[11px] text-amber-600 font-black hover:underline">
                            + كتابة المتطلبات الأساسية
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- =============================================================== -->
    <!-- 2. بنك الموارد العامة والتأسيسية الثابتة للدورة كامل (General Fixed Resources) -->
    <!-- =============================================================== -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7 space-y-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 border-b border-slate-100 pb-5">
            <div class="flex items-center gap-3">
                <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-2xl font-black shadow-md shrink-0">
                    🏛️
                </span>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
                            بنك الموارد العامة والتأسيسية الثابتة للدورة كامل
                        </h2>
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-mono">
                            {{ $generalResources->count() }} موارد ثابتة
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        موارد عامة تأسيسية ثابتة للدورة من البداية: موارد خاصة بالأستاذ (محمية)، موارد خاصة بالطالب، وموارد مشتركة.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="openAddResourceModal(null, 'مورد عام للدورة', true, 'teacher')" class="px-3.5 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                    <span>🔒</span>
                    <span>+ مورد للأستاذ</span>
                </button>
                <button type="button" onclick="openAddResourceModal(null, 'مورد عام للدورة', true, 'student')" class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                    <span>📘</span>
                    <span>+ مورد للطالب</span>
                </button>
                <button type="button" onclick="openAddResourceModal(null, 'مورد عام للدورة', true, 'all')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>➕</span>
                    <span>إضافة مورد عام ثابت</span>
                </button>
            </div>
        </div>

        <!-- الأقسام الثلاثة للموارد العامة (أستاذ / طالب / للكل) -->
        <div class="space-y-6">
            
            <!-- أ) موارد خاصة بالأستاذ والمعلم (Teacher Only - محمي 🔒) -->
            <div class="rounded-2xl border border-purple-200 bg-purple-50/20 p-5 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-purple-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-sm">
                            👨‍🏫
                        </span>
                        <div>
                            <h3 class="font-black text-sm text-purple-950 flex items-center gap-2">
                                <span>موارد خاصة بالأستاذ والمعلم فقط</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                    🔒 محمي - محجوب عن حسابات الطلاب
                                </span>
                            </h3>
                            <p class="text-[11px] text-purple-700">دليل المعلم، توزيع الخطة الأسبوعية، بنك نماذج الاختبارات ومفاتيح الإجابة، المصادر الإرشادية.</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-purple-800 font-mono bg-white px-2.5 py-1 rounded-lg border border-purple-200">
                        {{ $teacherGeneralResources->count() }} ملفات
                    </span>
                </div>

                @if($teacherGeneralResources->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($teacherGeneralResources as $res)
                            @include('admin.courses.partials.resource_card', ['res' => $res, 'studyGroups' => $studyGroups])
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center bg-white rounded-xl border border-dashed border-purple-200 text-xs text-purple-600 space-y-2">
                        <span class="text-2xl block">📁</span>
                        <p class="font-bold">لا توجد موارد خاصة بالأستاذ مضافة بعد.</p>
                        <p class="text-[11px] text-slate-400">يمكنك رفع دليل المعلم أو خطة التحضير أو نماذج الاختبارات هنا وستكون محجوبة عن الطلاب تماماً.</p>
                        <button type="button" onclick="openAddResourceModal(null, 'مورد عام للأستاذ', true, 'teacher')" class="mt-2 px-4 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-lg transition inline-flex items-center gap-1">
                            <span>➕</span>
                            <span>رفع دليل المعلم أو مورد خاص بالأستاذ</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- ب) موارد خاصة بالطلاب (Student Only 👨‍🎓) -->
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/20 p-5 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-emerald-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                            👨‍🎓
                        </span>
                        <div>
                            <h3 class="font-black text-sm text-emerald-950 flex items-center gap-2">
                                <span>موارد خاصة بالطلاب</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    📘 يظهر في بوابة الطالب
                                </span>
                            </h3>
                            <p class="text-[11px] text-emerald-700">الكتاب المقرر للدورة، القاموس المعتمد، ملخص القواعد، جداول التدريبات الإثرائية.</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-emerald-800 font-mono bg-white px-2.5 py-1 rounded-lg border border-emerald-200">
                        {{ $studentGeneralResources->count() }} ملفات
                    </span>
                </div>

                @if($studentGeneralResources->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($studentGeneralResources as $res)
                            @include('admin.courses.partials.resource_card', ['res' => $res, 'studyGroups' => $studyGroups])
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center bg-white rounded-xl border border-dashed border-emerald-200 text-xs text-emerald-600 space-y-2">
                        <span class="text-2xl block">📚</span>
                        <p class="font-bold">لا توجد موارد مخصصة للطلاب مرفوعة هنا بعد.</p>
                        <p class="text-[11px] text-slate-400">يمكنك رفع الكتاب المقرر PDF أو القاموس أو مذكرات القواعد للدورة كاملة من البداية.</p>
                        <button type="button" onclick="openAddResourceModal(null, 'مورد عام للطلاب', true, 'student')" class="mt-2 px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition inline-flex items-center gap-1">
                            <span>➕</span>
                            <span>رفع الكتاب المقرر أو مورد للطلاب</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- ج) موارد عامة مشتركة (Shared Resources 🌐) -->
            <div class="rounded-2xl border border-blue-200 bg-blue-50/20 p-5 space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-blue-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-sm">
                            👥
                        </span>
                        <div>
                            <h3 class="font-black text-sm text-blue-950 flex items-center gap-2">
                                <span>موارد عامة مشتركة</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    🌐 متاح للجميع
                                </span>
                            </h3>
                            <p class="text-[11px] text-blue-700">التوجيهات والتعليمات العامة، متطلبات النجاح، ومصادر تفاعلية مشتركة للأستاذ والطلاب.</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-blue-800 font-mono bg-white px-2.5 py-1 rounded-lg border border-blue-200">
                        {{ $sharedGeneralResources->count() }} ملفات
                    </span>
                </div>

                @if($sharedGeneralResources->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($sharedGeneralResources as $res)
                            @include('admin.courses.partials.resource_card', ['res' => $res, 'studyGroups' => $studyGroups])
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center bg-white rounded-xl border border-dashed border-blue-200 text-xs text-blue-600 space-y-2">
                        <span class="text-2xl block">🌐</span>
                        <p class="font-bold">لا توجد موارد عامة مشتركة مرفوعة بعد.</p>
                        <p class="text-[11px] text-slate-400">يمكنك رفع تعليمات الدورة أو المراجع المشتركة هنا.</p>
                        <button type="button" onclick="openAddResourceModal(null, 'مورد عام مشترك', true, 'all')" class="mt-2 px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition inline-flex items-center gap-1">
                            <span>➕</span>
                            <span>رفع مورد عام مشترك</span>
                        </button>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- =============================================================== -->
    <!-- 3. ترويسة قسم موارد الأيام والحصص الدراسية (Daily Sessions Divider) -->
    <!-- =============================================================== -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pt-2">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-base">
                    📅
                </span>
                <h2 class="text-xl font-black text-slate-900">
                    الحصص والأيام الدراسية للدورة (المحتوى اليومي والأسبوعي)
                </h2>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                إضافة موارد وواجبات وفيديوهات مخصصة لكل يوم أو حصة دراسية معينة.
            </p>
        </div>

        <button type="button" onclick="openAddSectionModal()" class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5 shrink-0">
            <span>📌</span>
            <span>إضافة عنوان / يوم دراسي جديد</span>
        </button>
    </div>

    <!-- شريط فلترة وتبويبات الموارد وإحصائياتها والتحكم بالطي/التوسيع -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" onclick="filterResources('all')" id="tab-all" class="filter-tab active px-3.5 py-2 rounded-xl text-xs font-black bg-indigo-600 text-white transition flex items-center gap-1.5 shadow-xs">
                <span>📁</span>
                <span>الكل ({{ $stats['total'] }})</span>
            </button>
            <button type="button" onclick="filterResources('book')" id="tab-book" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>📚</span>
                <span>الكتب ({{ $stats['books'] }})</span>
            </button>
            <button type="button" onclick="filterResources('audio')" id="tab-audio" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>🎧</span>
                <span>الصوتيات ({{ $stats['audios'] }})</span>
            </button>
            <button type="button" onclick="filterResources('video')" id="tab-video" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>🎥</span>
                <span>الفيديوهات ({{ $stats['videos'] }})</span>
            </button>
            <button type="button" onclick="filterResources('image')" id="tab-image" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>🖼️</span>
                <span>الصور ({{ $stats['images'] ?? 0 }})</span>
            </button>
            <button type="button" onclick="filterResources('worksheet')" id="tab-worksheet" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>📝</span>
                <span>أوراق العمل ({{ $stats['worksheets'] }})</span>
            </button>
            <button type="button" onclick="filterResources('quiz')" id="tab-quiz" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>🎯</span>
                <span>الكويزات ({{ $stats['quizzes'] }})</span>
            </button>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="expandAllSections()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1">
                <span>📂</span>
                <span>توسيع الكل</span>
            </button>
            <button type="button" onclick="collapseAllSections()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1">
                <span>📁</span>
                <span>طي الكل</span>
            </button>
        </div>
    </div>

    <!-- =============================================================== -->
    <!-- قائمة العناوين والأيام الدراسية التفاعلية (Accordion Sections) -->
    <!-- =============================================================== -->
    <div class="space-y-5" id="sectionsContainer">

        @forelse($sections as $section)
            <div class="section-card bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden transition-all duration-200" id="section-card-{{ $section->id }}">
                
                <!-- رأس القسم / اليوم الدراسي (Accordion Header) -->
                <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-50 via-white to-slate-50 border-b border-slate-200 cursor-pointer flex flex-col md:flex-row justify-between items-start md:items-center gap-4 select-none hover:bg-slate-100/50 transition" onclick="toggleSection({{ $section->id }})">
                    
                    <div class="flex items-center gap-3.5">
                        <span id="chevron-{{ $section->id }}" class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-black transition-transform duration-300">
                            ▼
                        </span>

                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[11px] font-mono font-bold text-slate-400 bg-slate-200/70 px-2 py-0.5 rounded-md">
                                    #{{ $section->order_index }}
                                </span>
                                <h3 class="text-base sm:text-lg font-black text-slate-900 leading-snug">
                                    {{ $section->title }}
                                </h3>
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ $section->resources->count() > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $section->resources->count() }} مورد
                                </span>
                            </div>

                            @if($section->description)
                                <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                                    {{ $section->description }}
                                </p>
                            @endif

                            <!-- شارات سريعة بأنواع الموارد في هذا القسم -->
                            @if($section->resources->count() > 0)
                                <div class="flex items-center gap-2 mt-2 flex-wrap text-[10px] text-slate-500 font-bold">
                                    @if($section->resources->where('type', 'video')->count() > 0)
                                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100">
                                            🎥 {{ $section->resources->where('type', 'video')->count() }} فيديو
                                        </span>
                                    @endif
                                    @if($section->resources->where('type', 'audio')->count() > 0)
                                        <span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded border border-purple-100">
                                            🎧 {{ $section->resources->where('type', 'audio')->count() }} صوت
                                        </span>
                                    @endif
                                    @if($section->resources->where('type', 'book')->count() > 0)
                                        <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-100">
                                            📚 {{ $section->resources->where('type', 'book')->count() }} كتاب
                                        </span>
                                    @endif
                                    @if($section->resources->where('type', 'worksheet')->count() > 0)
                                        <span class="bg-amber-50 text-amber-700 px-2 py-0.5 rounded border border-amber-100">
                                            📝 {{ $section->resources->where('type', 'worksheet')->count() }} أوراق عمل
                                        </span>
                                    @endif
                                    @if($section->resources->where('type', 'quiz')->count() > 0)
                                        <span class="bg-rose-50 text-rose-700 px-2 py-0.5 rounded border border-rose-100">
                                            🎯 {{ $section->resources->where('type', 'quiz')->count() }} كويز
                                        </span>
                                    @endif
                                    @if($section->resources->where('type', 'image')->count() > 0)
                                        <span class="bg-pink-50 text-pink-700 px-2 py-0.5 rounded border border-pink-100">
                                            🖼️ {{ $section->resources->where('type', 'image')->count() }} صور
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- أزرار الإجراءات على القسم (مع إيقاف حدث النقر منعاً لغلق الأكورديون) -->
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0" onclick="event.stopPropagation()">
                        <!-- زر إضافة مورد لهذا القسم بالتحديد -->
                        <button type="button" onclick="openAddResourceModal({{ $section->id }}, '{{ addslashes($section->title) }}')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1" title="إضافة ملف أو فيديو أو تسجيل صوتي لهذا العنوان">
                            <span>➕</span>
                            <span>إضافة مورد هنا</span>
                        </button>

                        <!-- زر تعديل العنوان -->
                        <button type="button" onclick="openEditSectionModal({{ $section->id }}, '{{ addslashes($section->title) }}', '{{ addslashes($section->description ?? '') }}', {{ $section->order_index }})" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs rounded-xl font-bold transition" title="تعديل اسم العنوان أو ترتيبه">
                            ✏️
                        </button>

                        <!-- زر حذف العنوان إن كان فارغاً -->
                        @if($section->resources->count() === 0)
                            <form action="{{ route('admin.courses.sections.destroy', $section->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنوان [{{ addslashes($section->title) }}]؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition text-xs font-bold" title="حذف هذا العنوان الفارغ">
                                    🗑️
                                </button>
                            </form>
                        @else
                            <button type="button" onclick="alert('لا يمكن حذف هذا العنوان لأنه يحتوي على ({{ $section->resources->count() }}) مورد تعليمي. قم بحذف أو نقل موارده أولاً.');" class="p-2 text-slate-300 cursor-not-allowed rounded-xl text-xs font-bold" title="لا يمكن حذف عنوان يحتوي على موارد">
                                🗑️
                            </button>
                        @endif
                    </div>
                </div>

                <!-- جسم القسم الحاوي على بطاقات الموارد (Accordion Body) -->
                <div id="section-body-{{ $section->id }}" class="section-body p-6">
                    @if($section->resources->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($section->resources as $res)
                                @include('admin.courses.partials.resource_card', ['res' => $res, 'studyGroups' => $studyGroups])
                            @endforeach

                            <!-- بطاقة إضافة مورد جديد داخل هذا اليوم مباشرة -->
                            <div class="border-2 border-dashed border-indigo-200 hover:border-indigo-500 bg-indigo-50/20 hover:bg-indigo-50/60 rounded-3xl p-6 transition flex flex-col items-center justify-center text-center space-y-3 cursor-pointer group min-h-[240px]" onclick="openAddResourceModal({{ $section->id }}, '{{ addslashes($section->title) }}')">
                                <span class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl font-black group-hover:scale-110 transition shadow-xs">
                                    ➕
                                </span>
                                <div>
                                    <h4 class="font-black text-sm text-indigo-950 group-hover:text-indigo-600 transition">إضافة مورد جديد لهذا اليوم</h4>
                                    <p class="text-[11px] text-slate-500 mt-1">يمكنك إضافة أي عدد تريده من الموارد والفيديوهات دون حد أقصى</p>
                                </div>
                                <span class="px-4 py-2 bg-indigo-600 group-hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-xs transition">
                                    ➕ رفع مورد إضافي
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 space-y-3">
                            <span class="text-3xl block">📂</span>
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-700">لا توجد موارد مضافة في هذا اليوم / العنوان بعد</h4>
                                <p class="text-xs text-slate-400">يمكنك رفع عدد غير محدود من الفيديوهات والصوتيات والكتب وأوراق العمل وتخصيصها لهذا اليوم.</p>
                            </div>
                            <button type="button" onclick="openAddResourceModal({{ $section->id }}, '{{ addslashes($section->title) }}')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                                <span>➕</span>
                                <span>إضافة مورد لـ {{ $section->title }}</span>
                            </button>
                        </div>
                    @endif
                </div>

            </div>
        @empty
            @if($unassignedResources->count() === 0)
                <div class="bg-white rounded-3xl border border-dashed border-slate-300 p-12 text-center space-y-4">
                    <span class="text-5xl block">📌</span>
                    <div class="space-y-1">
                        <h3 class="text-lg font-black text-slate-900">لم يتم تقسيم الدورة إلى عناوين أو أيام دراسية بعد</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            يمكنك البدء بإنشاء أول يوم دراسي أو وحدة تعليمية (مثال: "اليوم الأول"، "الوحدة الأولى: Unit 1"، "الدرس الأول")، ثم رفع المحتويات والفيديوهات بداخلها بسهولة.
                        </p>
                    </div>
                    <div class="flex items-center justify-center gap-3 flex-wrap">
                        <button type="button" onclick="openAddSectionModal()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition inline-flex items-center gap-1.5">
                            <span>📌</span>
                            <span>إضافة أول عنوان / يوم دراسي الآن</span>
                        </button>
                        <button type="button" onclick="openAddResourceModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition inline-flex items-center gap-1.5">
                            <span>➕</span>
                            <span>رفع مورد مباشر للدورة</span>
                        </button>
                    </div>
                </div>
            @endif
        @endforelse
    </div>

</div>

<!-- =============================================================== -->
<!-- مودال إضافة عنوان / يوم دراسي جديد للدورة (Add Section Modal) -->
<!-- =============================================================== -->
<div id="addSectionModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-5">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h3 class="font-black text-slate-900 text-sm">إضافة عنوان / يوم دراسي جديد للدورة</h3>
                <p class="text-[11px] text-slate-500">تقسيم محتوى الدورة حسب الأيام أو الدروس أو الوحدات لسهولة التصفح والوصول.</p>
            </div>
            <button type="button" onclick="closeAddSectionModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
        </div>

        <form action="{{ route('admin.courses.sections.store', $course->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <!-- عنوان القسم -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">اسم العنوان أو اليوم الدراسي <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="مثال: اليوم الأول - المحادثة والترحيب، أو الدرس الأول، أو Unit 1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
            </div>

            <!-- الوصف أو ملاحظات اليوم -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">وصف أو أهداف هذا اليوم / الدرس (اختياري)</label>
                <textarea name="description" rows="2" placeholder="أهداف الدرس، الواجبات المطلوبة، أو ملخص سريع للأنشطة..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <!-- الترتيب -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">ترتيب الظهور الرقمي</label>
                <input type="number" name="order_index" value="{{ $sections->count() + 1 }}" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                <span class="text-[10px] text-slate-400 mt-0.5 block">يحدد موضع هذا العنوان بين الأيام والدروس بالترتيب التصاعدي.</span>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeAddSectionModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm">حفظ العنوان 📌</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- مودال تعديل عنوان / يوم دراسي (Edit Section Modal) -->
<!-- =============================================================== -->
<div id="editSectionModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-5">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h3 class="font-black text-slate-900 text-sm">تعديل بيانات العنوان / اليوم الدراسي</h3>
                <p class="text-[11px] text-slate-500">تحديث مسمى اليوم أو الوحدة أو الترتيب.</p>
            </div>
            <button type="button" onclick="closeEditSectionModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
        </div>

        <form id="editSectionForm" method="POST" action="" class="space-y-4 text-xs">
            @csrf

            <!-- عنوان القسم -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">اسم العنوان أو اليوم الدراسي <span class="text-rose-500">*</span></label>
                <input type="text" name="title" id="editSectionTitle" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
            </div>

            <!-- الوصف -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">وصف أو أهداف اليوم (اختياري)</label>
                <textarea name="description" id="editSectionDescription" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <!-- الترتيب -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">ترتيب الظهور الرقمي</label>
                <input type="number" name="order_index" id="editSectionOrder" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeEditSectionModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm">حفظ التعديلات 💾</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- مودال إضافة مورد تعليمي جديد للدورة (Upload Resource Modal) -->
<!-- =============================================================== -->
<div id="addResourceModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-5">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h3 class="font-black text-slate-900 text-sm">إضافة مورد تعليمي جديد لبنك الدورة (غير محدود)</h3>
                <p class="text-[11px] text-slate-500">رفع فيديو، تسجيل صوتي MP3، كتاب PDF، صورة، ورقة عمل، أو ربط كويز (بدون حد أقصى للعدد).</p>
            </div>
            <button type="button" onclick="closeAddResourceModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
        </div>

        <div id="modalSelectedSectionBadge" class="hidden text-xs font-bold text-indigo-800 bg-indigo-50 border border-indigo-200 px-3.5 py-2.5 rounded-xl"></div>

        <form action="{{ route('admin.courses.resources.store', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- 1. موضع وتصنيف المورد (عام للدورة vs تابع ليوم/حصة محددة) -->
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 space-y-2">
                <label class="block font-bold text-slate-800">موضع المورد الأكاديمي:</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-indigo-400 select-none">
                        <input type="radio" name="resource_scope" value="general" id="scopeGeneral" onchange="toggleResourceScopeFields()" checked class="text-indigo-600">
                        <div>
                            <span class="font-black text-slate-800 block text-[11px]">🏛️ مورد عام للدورة كامل</span>
                            <span class="text-[10px] text-slate-400 block">ثابت للدورة (كتاب، دليل، قاموس)</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-indigo-400 select-none">
                        <input type="radio" name="resource_scope" value="section" id="scopeSection" onchange="toggleResourceScopeFields()" class="text-indigo-600">
                        <div>
                            <span class="font-black text-slate-800 block text-[11px]">📌 تابع ليوم / حصة</span>
                            <span class="text-[10px] text-slate-400 block">واجب، شرح درس، نشاط يومي</span>
                        </div>
                    </label>
                </div>
                <input type="hidden" name="is_general" id="addIsGeneralInput" value="1">
            </div>

            <!-- اختيار العنوان / اليوم الدراسي التابع له (يظهر فقط إذا اختير تابع ليوم) -->
            <div id="sectionSelectContainer" class="hidden">
                <label class="block font-bold text-slate-700 mb-1">العنوان / اليوم الدراسي التابع له <span class="text-indigo-600 font-normal">(اختر اليوم لتنظيم المورد داخله)</span></label>
                <select name="course_section_id" id="resourceSectionSelect" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-slate-800">
                    <option value="">-- اختر العنوان / اليوم الدراسي --</option>
                    @foreach($sections as $sec)
                        <option value="{{ $sec->id }}">📌 {{ $sec->title }} (#{{ $sec->order_index }})</option>
                    @endforeach
                </select>
            </div>

            <!-- الجمهور المستهدف -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">الجمهور المستهدف (صلاحية الرؤية) <span class="text-rose-500">*</span></label>
                <select name="target_audience" id="resourceAudienceSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-slate-800">
                    <option value="all">👥 متاح للجميع (عام - يظهر للأستاذ والطلاب)</option>
                    <option value="teacher">🔒 خاص بالأستاذ فقط (محمي - دليل المعلم، خطة التحضير، بنك الأسئلة)</option>
                    <option value="student">👨‍🎓 خاص بالطلاب (الكتاب المقرر، القاموس، التكاليف المدرسية)</option>
                </select>
                <span class="text-[10px] text-slate-400 mt-0.5 block">الموارد الخاصة بالأستاذ فقط تُحجب تماماً عن حسابات وبوابات الطلاب وقاعاتهم.</span>
            </div>

            <!-- عنوان المورد -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">عنوان المورد أو الملف <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="مثال: فيديو شرح الدرس الأول، أو تسجيل Unit 1 MP3، أو كتاب الطالب PDF" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
            </div>

            <!-- نوع المورد -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">نوع المورد التعليمي <span class="text-rose-500">*</span></label>
                <select name="type" id="resourceTypeSelect" onchange="toggleResourceTypeFields()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                    <option value="video">🎥 محاضرة أو شرح مرئي (Video / Link)</option>
                    <option value="audio">🎧 تسجيل صوتي للوحدة أو المحادثة (MP3 / Audio)</option>
                    <option value="book">📚 كتاب ومقرر دراسي (PDF / Document)</option>
                    <option value="image">🖼️ صورة نشاط أو بطاقة تعليمية (Image)</option>
                    <option value="worksheet">📝 ورقة عمل وتطبيق منزلي (Worksheet)</option>
                    <option value="quiz">🎯 كويز واختبار قصير (Quiz / Exam)</option>
                </select>
            </div>

            <!-- رفع الملف المرفق -->
            <div id="fileUploadContainer">
                <label class="block font-bold text-slate-700 mb-1">الملف المرفق (فيديو MP4, صوت MP3, مستند PDF, صورة PNG/JPG... حتى 100MB)</label>
                <input type="file" name="attachment" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 text-slate-600 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <!-- رابط خارجي للفيديو أو المستندات -->
            <div id="urlContainer">
                <label class="block font-bold text-slate-700 mb-1">أو رابط المحاضرة / الفيديو الخارجي (YouTube / Google Drive)</label>
                <input type="url" name="external_url" placeholder="https://..." dir="ltr" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- مدة المورد بالساعات -->
                <div id="durationContainer">
                    <label class="block font-bold text-slate-700 mb-1">المدة المقدرة (بالساعات)</label>
                    <input type="number" name="duration_hours" step="0.1" min="0.1" max="1000" placeholder="مثال: 2 أو 1.5 أو 0.5" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>

                <!-- ترتيب المورد -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الترتيب الرقمي في العرض</label>
                    <input type="number" name="order_index" value="{{ $course->resources->count() + 1 }}" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>
            </div>

            <!-- اختيار الكويز إن كان النوع كويز -->
            <div id="examSelectContainer" class="hidden">
                <label class="block font-bold text-slate-700 mb-1">اختر الكويز / الاختبار المرتبط من بنك الأسئلة</label>
                <select name="exam_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="">-- اختياري: ربط باختبار موجود --</option>
                    @foreach($availableExams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->title }} ({{ $exam->total_points }} درجة)</option>
                    @endforeach
                </select>
            </div>

            <!-- وصف المورد -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات وتعليمات للمدرسين والطلاب</label>
                <textarea name="description" rows="2" placeholder="وصف محتوى الملف وكيفية استخدامه في الحصة..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeAddResourceModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm">حفظ المورد في بنك الدورة 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- مودال مشاركة المورد لمجموعة محددة (Share Modal) -->
<!-- =============================================================== -->
<div id="shareModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-5">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h3 class="font-black text-slate-900 text-sm">مشاركة المورد لمجموعة دراسية</h3>
                <p class="text-[11px] text-slate-500" id="shareResourceTitle">تحديد المجموعة التي سيظهر لها هذا الملف</p>
            </div>
            <button type="button" onclick="closeShareModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
        </div>

        <form id="shareForm" method="POST" action="" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="resource_id" id="shareResourceId">
            <input type="hidden" name="level_resource_id" id="shareLevelResourceId">

            <div>
                <label class="block font-bold text-slate-700 mb-1">المجموعة الدراسية المستهدفة</label>
                <select name="study_group_id" id="shareGroupIdSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold" onchange="updateShareActionUrl()">
                    @foreach($studyGroups as $grp)
                        <option value="{{ $grp->id }}">
                            {{ $grp->name }}
                            @if($grp->course_id === $course->id)
                                (مجموعة الدورة الحالية 🌟)
                            @elseif($grp->level_id === $course->level_id)
                                (مجموعة نفس المستوى 🎓)
                            @endif
                            - ({{ $grp->students->count() }} طالب)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeShareModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm">تأكيد المشاركة للمجموعة 📢</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- مودال تعديل بيانات المورد التعليمي (Edit Resource Modal) -->
<!-- =============================================================== -->
<div id="editResourceModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-5">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h3 class="font-black text-slate-900 text-sm">تعديل بيانات المورد التعليمي</h3>
                <p class="text-[11px] text-slate-500">تحديث العنوان أو موضع المورد أو تغيير الجمهور المستهدف أو استبدال الملف.</p>
            </div>
            <button type="button" onclick="closeEditResourceModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
        </div>

        <form id="editResourceForm" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- 1. موضع وتصنيف المورد (عام للدورة vs تابع ليوم/حصة محددة) -->
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 space-y-2">
                <label class="block font-bold text-slate-800">موضع المورد الأكاديمي:</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-indigo-400 select-none">
                        <input type="radio" name="resource_scope" value="general" id="editScopeGeneral" onchange="toggleEditResourceScopeFields()" class="text-indigo-600">
                        <div>
                            <span class="font-black text-slate-800 block text-[11px]">🏛️ مورد عام للدورة كامل</span>
                            <span class="text-[10px] text-slate-400 block">ثابت للدورة (كتاب، دليل، قاموس)</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-indigo-400 select-none">
                        <input type="radio" name="resource_scope" value="section" id="editScopeSection" onchange="toggleEditResourceScopeFields()" class="text-indigo-600">
                        <div>
                            <span class="font-black text-slate-800 block text-[11px]">📌 تابع ليوم / حصة</span>
                            <span class="text-[10px] text-slate-400 block">واجب، شرح درس، نشاط يومي</span>
                        </div>
                    </label>
                </div>
                <input type="hidden" name="is_general" id="editIsGeneralInput" value="0">
            </div>

            <!-- اختيار العنوان / اليوم الدراسي التابع له -->
            <div id="editSectionSelectContainer" class="hidden">
                <label class="block font-bold text-slate-700 mb-1">العنوان / اليوم الدراسي التابع له</label>
                <select name="course_section_id" id="editResourceSectionSelect" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-slate-800">
                    <option value="">-- اختر العنوان / اليوم الدراسي --</option>
                    @foreach($sections as $sec)
                        <option value="{{ $sec->id }}">📌 {{ $sec->title }} (#{{ $sec->order_index }})</option>
                    @endforeach
                </select>
            </div>

            <!-- الجمهور المستهدف -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">الجمهور المستهدف (صلاحية الرؤية) <span class="text-rose-500">*</span></label>
                <select name="target_audience" id="editResourceAudienceSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-slate-800">
                    <option value="all">👥 متاح للجميع (عام - يظهر للأستاذ والطلاب)</option>
                    <option value="teacher">🔒 خاص بالأستاذ فقط (محمي - دليل المعلم، خطة التحضير، بنك الأسئلة)</option>
                    <option value="student">👨‍🎓 خاص بالطلاب (الكتاب المقرر، القاموس، التكاليف المدرسية)</option>
                </select>
            </div>

            <!-- عنوان المورد -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">عنوان المورد أو الملف <span class="text-rose-500">*</span></label>
                <input type="text" name="title" id="editResourceTitle" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
            </div>

            <!-- نوع المورد -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">نوع المورد التعليمي <span class="text-rose-500">*</span></label>
                <select name="type" id="editResourceTypeSelect" onchange="toggleEditResourceTypeFields()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                    <option value="video">🎥 محاضرة أو شرح مرئي (Video / Link)</option>
                    <option value="audio">🎧 تسجيل صوتي للوحدة أو المحادثة (MP3 / Audio)</option>
                    <option value="book">📚 كتاب ومقرر دراسي (PDF / Document)</option>
                    <option value="image">🖼️ صورة نشاط أو بطاقة تعليمية (Image)</option>
                    <option value="worksheet">📝 ورقة عمل وتطبيق منزلي (Worksheet)</option>
                    <option value="quiz">🎯 كويز واختبار قصير (Quiz / Exam)</option>
                </select>
            </div>

            <!-- استبدال الملف المرفق -->
            <div id="editFileUploadContainer">
                <label class="block font-bold text-slate-700 mb-1">استبدال الملف المرفق (اختياري - اتركه فارغاً للاحتفاظ بالملف الحالي)</label>
                <input type="file" name="attachment" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 text-slate-600 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <!-- رابط خارجي للفيديو أو المستندات -->
            <div id="editUrlContainer">
                <label class="block font-bold text-slate-700 mb-1">رابط المحاضرة / الفيديو الخارجي (YouTube / Google Drive)</label>
                <input type="url" name="external_url" id="editResourceExternalUrl" placeholder="https://..." dir="ltr" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- مدة المورد بالساعات -->
                <div id="editDurationContainer">
                    <label class="block font-bold text-slate-700 mb-1">المدة المقدرة (بالساعات)</label>
                    <input type="number" name="duration_hours" id="editResourceDurationHours" step="0.1" min="0.1" max="1000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>

                <!-- ترتيب المورد -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الترتيب الرقمي في العرض</label>
                    <input type="number" name="order_index" id="editResourceOrderIndex" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>
            </div>

            <!-- وصف المورد -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات وتعليمات للمدرسين والطلاب</label>
                <textarea name="description" id="editResourceDescription" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeEditResourceModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm">حفظ التعديلات 💾</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- مودال تعديل أهداف ومفردات المنهج والأساسيات للدورة -->
<!-- =============================================================== -->
<div id="editCourseObjectivesModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 p-6 sm:p-7 space-y-5">
        <div class="flex justify-between items-center border-b pb-3">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🎯</span>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base">تعديل أهداف ومفردات المنهج والأساسيات الثابتة للدورة</h3>
                    <p class="text-[11px] text-slate-500">دورة: {{ $course->name_en }} ({{ $course->name_ar }})</p>
                </div>
            </div>
            <button type="button" onclick="closeEditCourseObjectivesModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
        </div>

        <form action="{{ route('admin.courses.objectives.update', $course->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <!-- 1. أهداف المنهج والدورة -->
            <div>
                <label class="block font-bold text-indigo-950 mb-1 flex items-center gap-1.5">
                    <span>🎯</span>
                    <span>أهداف المنهج والدورة ومخرجات التعلم (Course Objectives)</span>
                </label>
                <textarea name="course_objectives" rows="4" placeholder="اكتب أهداف الدورة وما سيكتسبه الطالب عند إتمامها..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none leading-relaxed">{{ $course->course_objectives }}</textarea>
                <span class="text-[10px] text-slate-400">يمكنك كتابة كل هدف بسطر مستقل أو بنقاط واضحة.</span>
            </div>

            <!-- 2. مفردات وخطة المنهج التأسيسية -->
            <div>
                <label class="block font-bold text-purple-950 mb-1 flex items-center gap-1.5">
                    <span>📋</span>
                    <span>مفردات وخطة المنهج التأسيسية (Course Syllabus & Units)</span>
                </label>
                <textarea name="course_syllabus" rows="4" placeholder="المحاور الرئيسية، قائمة الوحدات والدروس، وتوزيع الساعات التدريبية..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 outline-none leading-relaxed">{{ $course->course_syllabus }}</textarea>
                <span class="text-[10px] text-slate-400">ملخص لمفردات المنهج وخطة تدريس الوحدات بالدورة.</span>
            </div>

            <!-- 3. المتطلبات والأساسيات الهامة -->
            <div>
                <label class="block font-bold text-amber-950 mb-1 flex items-center gap-1.5">
                    <span>📌</span>
                    <span>المتطلبات الأساسية والتعليمات الهامة (Prerequisites & Essentials)</span>
                </label>
                <textarea name="course_prerequisites" rows="3" placeholder="المعارف السابقة المطلوبة، شروط الحضور، الأدوات المطلوبة..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500 outline-none leading-relaxed">{{ $course->course_prerequisites }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeEditCourseObjectivesModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition">حفظ أهداف المنهج والأساسيات 💾</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- سكربت التحكم بالأكورديون والفلترة والمودالات -->
<!-- =============================================================== -->
<script>
    // طي وتوسيع قسم محدد
    function toggleSection(sectionId) {
        const body = document.getElementById('section-body-' + sectionId);
        const chevron = document.getElementById('chevron-' + sectionId);
        if (!body) return;

        if (body.classList.contains('hidden')) {
            body.classList.remove('hidden');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        } else {
            body.classList.add('hidden');
            if (chevron) chevron.style.transform = 'rotate(-90deg)';
        }
    }

    // توسيع جميع الأقسام
    function expandAllSections() {
        document.querySelectorAll('.section-body').forEach(body => {
            body.classList.remove('hidden');
        });
        document.querySelectorAll('[id^="chevron-"]').forEach(chevron => {
            chevron.style.transform = 'rotate(0deg)';
        });
    }

    // طي جميع الأقسام
    function collapseAllSections() {
        document.querySelectorAll('.section-body').forEach(body => {
            body.classList.add('hidden');
        });
        document.querySelectorAll('[id^="chevron-"]').forEach(chevron => {
            chevron.style.transform = 'rotate(-90deg)';
        });
    }

    // فلترة الموارد حسب النوع (فيديو، صوت، كتاب...)
    function filterResources(type) {
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.classList.remove('active', 'bg-indigo-600', 'text-white');
            tab.classList.add('text-slate-600');
        });

        const activeTab = document.getElementById('tab-' + type);
        if (activeTab) {
            activeTab.classList.add('active', 'bg-indigo-600', 'text-white');
            activeTab.classList.remove('text-slate-600');
        }

        const cards = document.querySelectorAll('.resource-card');
        cards.forEach(card => {
            if (type === 'all' || card.dataset.type === type) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });

        // عند الفلترة، وسّع الأقسام تلقائياً حتى يرى المستخدم النتائج
        expandAllSections();
    }

    function toggleResourceTypeFields() {
        const type = document.getElementById('resourceTypeSelect').value;
        const examContainer = document.getElementById('examSelectContainer');

        if (type === 'quiz') {
            examContainer.classList.remove('hidden');
        } else {
            examContainer.classList.add('hidden');
        }
    }

    function toggleEditResourceTypeFields() {
        const type = document.getElementById('editResourceTypeSelect').value;
        const urlContainer = document.getElementById('editUrlContainer');
        const fileContainer = document.getElementById('editFileUploadContainer');
        const durationContainer = document.getElementById('editDurationContainer');

        if (type === 'video') {
            if (urlContainer) urlContainer.classList.remove('hidden');
            if (durationContainer) durationContainer.classList.remove('hidden');
        }
    }

    // تبديل موضع المورد في مودال الإضافة (عام للدورة vs تابع ليوم)
    function toggleResourceScopeFields() {
        const isGeneral = document.getElementById('scopeGeneral').checked;
        const sectionContainer = document.getElementById('sectionSelectContainer');
        const hiddenInput = document.getElementById('addIsGeneralInput');
        const select = document.getElementById('resourceSectionSelect');

        if (isGeneral) {
            sectionContainer.classList.add('hidden');
            hiddenInput.value = '1';
            if (select) select.value = '';
        } else {
            sectionContainer.classList.remove('hidden');
            hiddenInput.value = '0';
        }
    }

    // تبديل موضع المورد في مودال التعديل
    function toggleEditResourceScopeFields() {
        const isGeneral = document.getElementById('editScopeGeneral').checked;
        const sectionContainer = document.getElementById('editSectionSelectContainer');
        const hiddenInput = document.getElementById('editIsGeneralInput');
        const select = document.getElementById('editResourceSectionSelect');

        if (isGeneral) {
            sectionContainer.classList.add('hidden');
            hiddenInput.value = '1';
            if (select) select.value = '';
        } else {
            sectionContainer.classList.remove('hidden');
            hiddenInput.value = '0';
        }
    }

    // مودال إضافة مورد (مع إمكانية تحديد قسم مسبق أو تعيينه عام وتحديد الجمهور)
    function openAddResourceModal(sectionId = null, sectionTitle = '', isGeneral = null, targetAudience = 'all') {
        const select = document.getElementById('resourceSectionSelect');
        const badge = document.getElementById('modalSelectedSectionBadge');
        const audienceSelect = document.getElementById('resourceAudienceSelect');

        if (audienceSelect && targetAudience) {
            audienceSelect.value = targetAudience;
        }

        if (sectionId) {
            document.getElementById('scopeSection').checked = true;
            document.getElementById('scopeGeneral').checked = false;
            if (select) select.value = sectionId;
        } else if (isGeneral === true) {
            document.getElementById('scopeGeneral').checked = true;
            document.getElementById('scopeSection').checked = false;
            if (select) select.value = '';
        } else {
            document.getElementById('scopeGeneral').checked = true;
            document.getElementById('scopeSection').checked = false;
        }

        toggleResourceScopeFields();

        if (badge) {
            if (sectionTitle) {
                badge.innerText = '📌 سيتم إدراج هذا المورد داخل: ' + sectionTitle;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        document.getElementById('addResourceModal').classList.remove('hidden');
        toggleResourceTypeFields();
    }

    function closeAddResourceModal() {
        document.getElementById('addResourceModal').classList.add('hidden');
    }

    // مودال تعديل المورد الأكاديمي
    function openEditResourceModal(res) {
        document.getElementById('editResourceForm').action = '/admin/levels/resources/' + res.id + '/update';
        document.getElementById('editResourceTitle').value = res.title || '';
        document.getElementById('editResourceTypeSelect').value = res.type || 'book';
        document.getElementById('editResourceAudienceSelect').value = res.target_audience || 'all';
        document.getElementById('editResourceDescription').value = res.description || '';
        document.getElementById('editResourceExternalUrl').value = res.external_url || '';
        document.getElementById('editResourceDurationHours').value = res.duration_hours || '';
        document.getElementById('editResourceOrderIndex').value = res.order_index || 1;

        if (res.is_general || !res.course_section_id) {
            document.getElementById('editScopeGeneral').checked = true;
            document.getElementById('editScopeSection').checked = false;
        } else {
            document.getElementById('editScopeSection').checked = true;
            document.getElementById('editScopeGeneral').checked = false;
            document.getElementById('editResourceSectionSelect').value = res.course_section_id;
        }

        toggleEditResourceScopeFields();
        document.getElementById('editResourceModal').classList.remove('hidden');
    }

    function closeEditResourceModal() {
        document.getElementById('editResourceModal').classList.add('hidden');
    }

    // مودال أهداف الدورة ومفردات المنهج
    function openEditCourseObjectivesModal() {
        document.getElementById('editCourseObjectivesModal').classList.remove('hidden');
    }

    function closeEditCourseObjectivesModal() {
        document.getElementById('editCourseObjectivesModal').classList.add('hidden');
    }

    // مودال إضافة عنوان / يوم دراسي
    function openAddSectionModal() {
        document.getElementById('addSectionModal').classList.remove('hidden');
    }

    function closeAddSectionModal() {
        document.getElementById('addSectionModal').classList.add('hidden');
    }

    // مودال تعديل عنوان / يوم دراسي
    function openEditSectionModal(id, title, description, order) {
        document.getElementById('editSectionForm').action = '/admin/courses/sections/' + id + '/update';
        document.getElementById('editSectionTitle').value = title;
        document.getElementById('editSectionDescription').value = description || '';
        document.getElementById('editSectionOrder').value = order || 1;
        document.getElementById('editSectionModal').classList.remove('hidden');
    }

    function closeEditSectionModal() {
        document.getElementById('editSectionModal').classList.add('hidden');
    }

    // مودال مشاركة المورد للمجموعة
    function openShareModal(resourceId, resourceTitle) {
        document.getElementById('shareResourceId').value = resourceId;
        const fallback = document.getElementById('shareLevelResourceId');
        if (fallback) fallback.value = resourceId;
        document.getElementById('shareResourceTitle').innerText = resourceTitle;
        updateShareActionUrl();
        document.getElementById('shareModal').classList.remove('hidden');
    }

    function updateShareActionUrl() {
        const select = document.getElementById('shareGroupIdSelect');
        if (select && select.value) {
            document.getElementById('shareForm').action = '/groups/' + select.value + '/share-resource';
        }
    }

    function closeShareModal() {
        document.getElementById('shareModal').classList.add('hidden');
    }

    // فتح المودال تلقائياً إذا كان الرابط يحتوي على ?action=add
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'add') {
            openAddResourceModal();
        } else if (urlParams.get('action') === 'add_section') {
            openAddSectionModal();
        } else if (urlParams.get('action') === 'objectives') {
            openEditCourseObjectivesModal();
        }
    });
</script>
@endsection
