@extends('layouts.app', ['title' => 'إدارة المستويات والدورات والمناهج والأسعار - ASOTS'])

@section('content')
<div class="space-y-8">

    <!-- ترويسة الصفحة وإحصائيات الهيكل الأكاديمي بتدرج الهوية الرسمية الأزرق الداكن -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] p-6 sm:p-8 rounded-3xl border border-[#0066A1]/40 shadow-xl text-white">
        <div>
            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                <span class="text-xs font-black text-white bg-white/15 px-3 py-1 rounded-full border border-white/20">
                    الهيكل الأكاديمي والتسعيرة • Curriculum & Academic Levels
                </span>
                <span class="text-xs font-black text-[#F58220] bg-[#F58220]/25 px-2.5 py-0.5 rounded-full border border-[#F58220]/40">
                    معتمد رسمياً في ASOTS ⭐
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white">إدارة المستويات والدورات والمناهج والأسعار</h1>
            <p class="text-xs sm:text-sm text-slate-100 mt-1 max-w-2xl leading-relaxed">
                كل مستوى أكاديمي يحتوي على مجموعة دورات بساعات وتكاليف معتمدة، مع بنك مناهج رقمي متكامل (كتب، صوتيات، فيديوهات، صور، أنشطة، وكويزات) لكل دورة.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('help.index') }}" title="دليل وشرح استخدام هذه الشاشة" class="px-3.5 py-2.5 bg-white/15 hover:bg-white/25 text-white border border-white/20 text-xs font-black rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                <span>📖</span>
                <span>دليل الشاشة</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-slate-100 border border-white/20 text-xs font-bold rounded-xl transition">
                ← المستخدمين والصلاحيات
            </a>
            <button type="button" onclick="openAddLevelModal()" class="px-4 py-2.5 bg-white/15 hover:bg-white/25 text-white border border-white/25 text-xs font-black rounded-xl shadow-xs transition flex items-center gap-1.5">
                <span>➕</span>
                <span>إضافة مستوى جديد</span>
            </button>
            <button type="button" onclick="openAddCourseModal()" class="px-5 py-2.5 bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] hover:to-[#b85807] text-white text-xs font-black rounded-xl shadow-md transition flex items-center gap-1.5">
                <span>➕</span>
                <span>إضافة دورة لمستوى</span>
            </button>
        </div>
    </div>

    <!-- كروت ملخص إحصائي سريع -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
            <span class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-xl font-black">
                🏛️
            </span>
            <div>
                <span class="text-[11px] text-slate-400 block font-bold">المستويات الأكاديمية</span>
                <span class="text-2xl font-black text-slate-900 font-mono">{{ $levels->count() }}</span>
                <span class="text-[10px] text-slate-400 block">مستويات رئيسية</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
            <span class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl font-black">
                📚
            </span>
            <div>
                <span class="text-[11px] text-slate-400 block font-bold">إجمالي الدورات</span>
                <span class="text-2xl font-black text-slate-900 font-mono">{{ $levels->sum(fn($l) => $l->courses->count()) }}</span>
                <span class="text-[10px] text-emerald-600 font-bold block">دورة معتمدة</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
            <span class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-xl font-black">
                ⏱️
            </span>
            <div>
                <span class="text-[11px] text-slate-400 block font-bold">إجمالي الساعات</span>
                <span class="text-2xl font-black text-amber-600 font-mono">{{ number_format($levels->sum(fn($l) => $l->total_hours)) }}</span>
                <span class="text-[10px] text-slate-400 block">ساعة تدريبية</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-3">
            <span class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-xl font-black">
                💳
            </span>
            <div>
                <span class="text-[11px] text-slate-400 block font-bold">باقات الحصص الفردية</span>
                <span class="text-2xl font-black text-purple-700 font-mono">{{ $packages->count() }}</span>
                <span class="text-[10px] text-slate-400 block">باقة اشتراك 1-on-1</span>
            </div>
        </div>
    </div>

    <!-- =============================================================== -->
    <!-- القسم الأول: باقات الحصص الفردية 1-on-1 (Packages Management) -->
    <!-- =============================================================== -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
            <div>
                <span class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>💳</span>
                    <span>باقات الحصص الفردية المباشرة (1-on-1 Live Sessions)</span>
                </span>
                <p class="text-xs text-slate-500">تعديل أسعار باقات الحصص التفاعلية الفردية وعدد الحصص والعملة.</p>
            </div>
            <button type="button" onclick="openAddPackageModal()" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200 transition flex items-center gap-1.5">
                <span>➕</span>
                <span>إضافة باقة جديدة</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($packages as $pkg)
                <div class="border-2 border-slate-200 hover:border-indigo-500 rounded-2xl p-6 transition bg-slate-50/50 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full font-black text-[10px]">
                                {{ $pkg->lessons_count }} حصة 25 دقيقة
                            </span>
                            <span class="font-mono text-xl font-black text-slate-900">
                                {{ $pkg->formatted_price }}
                            </span>
                        </div>

                        <h3 class="font-black text-slate-900 text-sm mb-1">{{ $pkg->title }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $pkg->description }}</p>
                    </div>

                    <div class="pt-4 border-t border-slate-200 flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-mono text-[11px]">
                            {{ \App\Helpers\ArabicCurrencyHelper::format(round($pkg->price / $pkg->lessons_count, 1), $pkg->currency, 1) }} / للحصة
                        </span>
                        <button type="button" onclick='openEditPackageModal(@json($pkg))' class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition text-xs shadow-xs">
                            ✏️ تعديل السعر
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- =============================================================== -->
    <!-- القسم الثاني: المستويات الأكاديمية والدورات التابعة لها والأسعار والساعات -->
    <!-- =============================================================== -->
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 px-2">
            <div>
                <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                    <span>🏛️</span>
                    <span>المستويات الأكاديمية والدورات التدريبية المعتمدة (ASOTS Official Curriculum)</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    المستويات مرتبة أسفل بعضها، اضغط على زر العرض في نهاية أي مستوى لفتح دوراته وساعاته وبنك المناهج.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="expandAllLevels()" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200 transition flex items-center gap-1.5 shadow-2xs">
                    <span>📂</span>
                    <span>توسيع جميع المستويات</span>
                </button>
                <button type="button" onclick="collapseAllLevels()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition flex items-center gap-1.5 shadow-2xs">
                    <span>📁</span>
                    <span>طي وإخفاء الكل</span>
                </button>
                <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-2 rounded-xl border border-slate-200">
                    {{ $levels->count() }} مستويات
                </span>
            </div>
        </div>

        <!-- قائمة المستويات والدورات -->
        <div class="space-y-4">
            @foreach($levels as $lvl)
                <div class="bg-white rounded-3xl border-2 border-slate-200 hover:border-indigo-400 transition shadow-sm overflow-hidden">
                    
                    <!-- ترويسة المستوى الأكاديمي بتدرج الهوية الرسمية الأزرق الداكن -->
                    <div class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white p-6 sm:p-7 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 transition hover:brightness-105 border-b border-[#0066A1]/40">
                        
                        <!-- معلومات المستوى مع إمكانية الضغط على العنوان للفتح/الإغلاق -->
                        <div class="space-y-1.5 max-w-2xl cursor-pointer select-none group/title" onclick="toggleLevelCourses({{ $lvl->id }})" title="اضغط لاستعراض أو إخفاء دورات هذا المستوى">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="w-8 h-8 rounded-xl bg-[#F58220] text-slate-950 font-black flex items-center justify-center text-sm shadow-sm group-hover/title:scale-105 transition">
                                    #{{ $lvl->level_order }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full bg-white/15 text-amber-200 font-mono font-bold text-xs border border-white/20" dir="ltr">
                                    {{ $lvl->name_en }}
                                </span>
                                <span class="text-xs font-bold text-white bg-white/10 px-2.5 py-0.5 rounded-full">
                                    {{ $lvl->courses->count() }} دورات تابعة
                                </span>
                                <span class="text-xs font-mono font-bold text-amber-200 bg-[#F58220]/25 px-2.5 py-0.5 rounded-full border border-[#F58220]/40">
                                    ⏱️ {{ $lvl->total_hours }} ساعة إجمالية
                                </span>
                            </div>

                            <h3 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2 group-hover/title:text-amber-300 transition">
                                <span>{{ $lvl->name_ar ?: $lvl->name }}</span>
                            </h3>

                            @if($lvl->description)
                                <p class="text-xs text-slate-100 leading-relaxed max-w-2xl">
                                    {{ $lvl->description }}
                                </p>
                            @endif
                        </div>

                        <!-- تسعيرة المستوى وإجراءات الإدارة وزر العرض/الإخفاء بالنهاية -->
                        <div class="flex items-center gap-3 w-full lg:w-auto justify-between lg:justify-end border-t lg:border-t-0 border-white/10 pt-4 lg:pt-0 flex-wrap shrink-0">
                            <div class="text-right lg:text-left font-mono">
                                <span class="text-[10px] text-slate-200 block font-bold">رسوم المستوى الشاملة:</span>
                                <span class="text-xl font-black text-[#F58220] block font-mono">{{ $lvl->formatted_fee }}</span>
                                <span class="text-[10px] text-slate-200 block">{{ $lvl->currency_name }}</span>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <button type="button" onclick="openAddCourseModal({{ $lvl->id }}, '{{ addslashes($lvl->name_en) }}')" class="px-3.5 py-2 bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] hover:to-[#b85807] text-white font-black text-xs rounded-xl shadow-sm transition flex items-center gap-1">
                                    <span>➕</span>
                                    <span>إضافة دورة</span>
                                </button>

                                <button type="button" onclick='openEditLevelModal(@json($lvl))' class="px-2.5 py-2 bg-white/15 hover:bg-white/25 text-white font-bold rounded-xl text-xs border border-white/20 transition flex items-center gap-1 shadow-2xs" title="تعديل الرسوم">
                                    <span>✏️</span>
                                    <span>تعديل السعر</span>
                                </button>

                                <!-- زر العرض / الإخفاء في نهاية شريط المستوى بناءً على طلب المستخدم -->
                                <button type="button" onclick="toggleLevelCourses({{ $lvl->id }})" id="btn-toggle-lvl-{{ $lvl->id }}" class="px-3.5 py-2 bg-[#002b45] hover:bg-[#001f33] text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer border border-white/20">
                                    <span id="txt-toggle-lvl-{{ $lvl->id }}">عرض الدورات ({{ $lvl->courses->count() }})</span>
                                    <span id="icon-toggle-lvl-{{ $lvl->id }}" class="font-mono text-xs">▼</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- جدول وبطاقات الدورات التابعة لهذا المستوى (مخفية بالافتراضي للترتيب الجمالي) -->
                    <div id="level-courses-{{ $lvl->id }}" data-count="{{ $lvl->courses->count() }}" class="level-courses-container hidden p-6 space-y-4 bg-slate-50/70 border-t border-slate-200 transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-700 flex items-center gap-1.5">
                                <span>📖</span>
                                <span>الدورات المعتمدة التابعة للمستوى (Courses in this Level):</span>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                {{ $lvl->courses->count() }} دورات مسجلة
                            </span>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            @forelse($lvl->courses as $crs)
                                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 hover:border-indigo-400 transition shadow-xs flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                                    
                                    <!-- تفاصيل الدورة والاسم والساعات -->
                                    <div class="space-y-2 max-w-xl">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center font-mono">
                                                {{ $crs->course_order }}
                                            </span>
                                            <span class="font-black text-base text-slate-900">
                                                {{ $crs->name_en }}
                                            </span>
                                            @if($crs->name_ar && $crs->name_ar !== $crs->name_en)
                                                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                                                    {{ $crs->name_ar }}
                                                </span>
                                            @endif
                                            <span class="px-2.5 py-0.5 rounded-full font-mono font-black text-[11px] bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1">
                                                <span>⏱️</span>
                                                <span>{{ $crs->hours }} ساعة معتمدة</span>
                                            </span>
                                            @if(!$crs->is_active)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    غير نشط
                                                </span>
                                            @endif
                                        </div>

                                        @if($crs->description)
                                            <p class="text-xs text-slate-500 line-clamp-1 leading-relaxed">
                                                {{ $crs->description }}
                                            </p>
                                        @endif

                                        <!-- إحصائيات الموارد المرفوعة في هذه الدورة -->
                                        @php
                                            $crsBooks = $crs->bookResources->count();
                                            $crsAudios = $crs->audioResources->count();
                                            $crsVideos = $crs->videoResources->count();
                                            $crsImages = $crs->imageResources->count();
                                            $crsWorksheets = $crs->worksheetResources->count();
                                            $crsQuizzes = $crs->quizResources->count();
                                            $crsTotalRes = $crs->resources->count();
                                        @endphp
                                        <div class="flex items-center gap-1.5 flex-wrap text-xs pt-0.5">
                                            <span class="text-[10px] px-2 py-0.5 rounded-md font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="الكتب والمناهج PDF">
                                                📚 {{ $crsBooks }} كتب
                                            </span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md font-bold bg-purple-50 text-purple-700 border border-purple-200" title="التسجيلات الصوتية MP3">
                                                🎧 {{ $crsAudios }} صوتيات
                                            </span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md font-bold bg-blue-50 text-blue-700 border border-blue-200" title="محاضرات الفيديو">
                                                🎥 {{ $crsVideos }} فيديو
                                            </span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md font-bold bg-pink-50 text-pink-700 border border-pink-200" title="صور وبطاقات الأنشطة">
                                                🖼️ {{ $crsImages }} صور
                                            </span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md font-bold bg-amber-50 text-amber-700 border border-amber-200" title="أوراق العمل">
                                                📝 {{ $crsWorksheets }} أنشطة
                                            </span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md font-bold bg-rose-50 text-rose-700 border border-rose-200" title="الكويزات والاختبارات">
                                                🎯 {{ $crsQuizzes }} كويزات
                                            </span>
                                        </div>
                                    </div>

                                    <!-- تسعيرة الدورة الفردية وأزرار الإجراءات وبنك المناهج -->
                                    <div class="flex items-center gap-3 w-full lg:w-auto justify-between lg:justify-end border-t lg:border-t-0 border-slate-100 pt-3 lg:pt-0 shrink-0">
                                        <div class="text-right font-mono">
                                            <span class="text-[10px] text-slate-400 block font-bold">تسعيرة الدورة الفردية:</span>
                                            <span class="text-base font-black text-emerald-600 block">{{ $crs->formatted_fee }}</span>
                                            <span class="text-[10px] text-slate-400 block">{{ $crs->currency_name }}</span>
                                        </div>

                                        <div class="flex items-center gap-2 flex-wrap">
                                            <!-- زر الدخول لبنك موارد ومناهج الدورة -->
                                            <a href="{{ route('admin.courses.resources', $crs->id) }}" class="px-3.5 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                                                <span>📚 بنك المناهج والموارد</span>
                                                <span class="bg-white/25 px-1.5 py-0.2 rounded-full text-[10px] font-mono">{{ $crsTotalRes }}</span>
                                            </a>

                                            <!-- زر إضافة مورد سريع -->
                                            <a href="{{ route('admin.courses.resources', $crs->id) }}?action=add" class="px-2.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl text-xs transition border border-indigo-200" title="إضافة كتاب أو ملف سريع">
                                                ➕ مورد
                                            </a>

                                            <!-- زر تعديل الدورة والتسعيرة والساعات -->
                                            <button type="button" onclick='openEditCourseModal(@json($crs))' class="px-3 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold rounded-xl text-xs transition shadow-2xs flex items-center gap-1">
                                                <span>✏️</span>
                                                <span>تعديل السعر والساعات</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            @empty
                                <div class="bg-white p-6 rounded-2xl border border-dashed border-slate-300 text-center text-xs text-slate-400">
                                    لا توجد دورات مسجلة بعد في هذا المستوى. يمكنك إضافة دورة الآن عبر زر (إضافة دورة للمستوى).
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </div>

</div>

<!-- =============================================================== -->
<!-- 1. مودال إضافة دورة جديدة لمستوى (Add Course Modal) -->
<!-- =============================================================== -->
<div id="addCourseModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 cursor-pointer" onclick="closeAddCourseModal()"></div>
    <div class="relative bg-white w-full max-w-lg rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200 p-4 sm:p-6 space-y-4 sm:space-y-5 min-w-0 my-auto z-10">
        <div class="flex justify-between items-center border-b pb-3 gap-2 min-w-0">
            <div class="min-w-0">
                <h3 class="font-black text-slate-900 text-sm truncate">إضافة دورة تدريبية جديدة</h3>
                <p class="text-[11px] text-slate-500 truncate">إضافة دورة إلى مستوى محدد مع تحديد الساعات والرسوم.</p>
            </div>
            <button type="button" onclick="closeAddCourseModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold shrink-0 p-1">✕</button>
        </div>

        <form action="{{ route('admin.courses.store') }}" method="POST" class="space-y-4 text-xs min-w-0">
            @csrf

            <div class="min-w-0">
                <label class="block font-bold text-slate-700 mb-1">المستوى الأكاديمي الحاضن <span class="text-rose-500">*</span></label>
                <select name="level_id" id="addCourseLevelId" required class="w-full max-w-full truncate px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                    @foreach($allLevels as $lvl)
                        <option value="{{ $lvl->id }}">{{ $lvl->name_en }} - {{ $lvl->name_ar }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-0">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسم الدورة بالإنجليزي <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_en" required placeholder="مثال: Team Together 1 أو Access A" dir="ltr" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الاسم بالعربي (اختياري)</label>
                    <input type="text" name="name_ar" placeholder="مثال: فريق معاً 1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 min-w-0">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">عدد الساعات <span class="text-rose-500">*</span></label>
                    <input type="number" name="hours" value="40" required min="1" max="500" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">رسوم الدورة الفردية <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="fee_amount" value="20000" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold text-emerald-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة <span class="text-rose-500">*</span></label>
                    <select name="currency" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="YER" selected>ريال يمني (YER)</option>
                        <option value="SAR">ريال سعودي (SAR)</option>
                        <option value="USD">دولار أمريكي (USD)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">وصف الدورة وأهدافها (اختياري)</label>
                <textarea name="description" rows="2" placeholder="وصف محتوى الدورة والمهارات المكتسبة..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeAddCourseModal()" class="w-full sm:w-auto px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl text-center">إلغاء</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm text-center">حفظ وإضافة الدورة 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- 2. مودال تعديل الدورة والتسعيرة والساعات (Edit Course Modal) -->
<!-- =============================================================== -->
<div id="editCourseModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 cursor-pointer" onclick="closeEditCourseModal()"></div>
    <div class="relative bg-white w-full max-w-lg rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200 p-4 sm:p-6 space-y-4 sm:space-y-5 min-w-0 my-auto z-10">
        <div class="flex justify-between items-center border-b pb-3 gap-2 min-w-0">
            <div class="min-w-0">
                <h3 class="font-black text-slate-900 text-sm truncate">تعديل بيانات الدورة والرسوم والساعات</h3>
                <p class="text-[11px] text-slate-500 truncate">تحديث تفاصيل وساعات وتسعيرة الدورة التدريبية.</p>
            </div>
            <button type="button" onclick="closeEditCourseModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold shrink-0 p-1">✕</button>
        </div>

        <form id="editCourseForm" method="POST" action="" class="space-y-4 text-xs min-w-0">
            @csrf

            <div class="min-w-0">
                <label class="block font-bold text-slate-700 mb-1">المستوى الأكاديمي الحاضن</label>
                <select name="level_id" id="editCourseLevelId" required class="w-full max-w-full truncate px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                    @foreach($allLevels as $lvl)
                        <option value="{{ $lvl->id }}">{{ $lvl->name_en }} - {{ $lvl->name_ar }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-0">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسم الدورة بالإنجليزي <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_en" id="editCourseNameEn" required dir="ltr" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الاسم بالعربي</label>
                    <input type="text" name="name_ar" id="editCourseNameAr" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 min-w-0">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">عدد الساعات <span class="text-rose-500">*</span></label>
                    <input type="number" name="hours" id="editCourseHours" required min="1" max="500" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">رسوم الدورة الفردية <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="fee_amount" id="editCourseFee" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold text-emerald-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة <span class="text-rose-500">*</span></label>
                    <select name="currency" id="editCourseCurrency" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="YER">ريال يمني (YER)</option>
                        <option value="SAR">ريال سعودي (SAR)</option>
                        <option value="USD">دولار أمريكي (USD)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الوصف والأهداف</label>
                <textarea name="description" id="editCourseDescription" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="editCourseIsActive" value="1" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 shrink-0">
                <label for="editCourseIsActive" class="font-bold text-slate-700 cursor-pointer">الدورة نشطة ومتاحة للتسكين والتسجيل</label>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeEditCourseModal()" class="w-full sm:w-auto px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl text-center">إلغاء</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm text-center">حفظ التعديلات 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- 3. مودال إضافة مستوى جديد (Add Level Modal) -->
<!-- =============================================================== -->
<div id="addLevelModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 cursor-pointer" onclick="closeAddLevelModal()"></div>
    <div class="relative bg-white w-full max-w-lg rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200 p-4 sm:p-6 space-y-4 sm:space-y-5 min-w-0 my-auto z-10">
        <div class="flex justify-between items-center border-b pb-3 gap-2 min-w-0">
            <h3 class="font-black text-slate-900 text-sm truncate">إضافة مستوى أكاديمي رئيسي جديد</h3>
            <button type="button" onclick="closeAddLevelModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold shrink-0 p-1">✕</button>
        </div>

        <form action="{{ route('admin.levels.store') }}" method="POST" class="space-y-4 text-xs min-w-0">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-0">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الاسم بالإنجليزي (English) <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_en" required placeholder="مثال: ADVANCED 3" dir="ltr" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الاسم بالعربي</label>
                    <input type="text" name="name_ar" placeholder="مثال: المستوى المتقدم الثالث" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 min-w-0">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">رسوم المستوى الشاملة</label>
                    <input type="number" step="0.01" name="fee_amount" required value="50000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold text-emerald-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة</label>
                    <select name="currency" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="YER" selected>ريال يمني (YER)</option>
                        <option value="SAR">ريال سعودي (SAR)</option>
                        <option value="USD">دولار أمريكي (USD)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الترتيب الرقمي</label>
                    <input type="number" name="level_order" value="{{ $levels->count() + 1 }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">وصف المستوى</label>
                <textarea name="description" rows="2" placeholder="أهداف ومخرجات التعلم للمستوى..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeAddLevelModal()" class="w-full sm:w-auto px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl text-center">إلغاء</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm text-center">حفظ المستوى الأكاديمي 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- 4. مودال تعديل تسعيرة وبيانات المستوى (Edit Level Modal) -->
<!-- =============================================================== -->
<div id="editLevelModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 cursor-pointer" onclick="closeEditLevelModal()"></div>
    <div class="relative bg-white w-full max-w-lg rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200 p-4 sm:p-6 space-y-4 sm:space-y-5 min-w-0 my-auto z-10">
        <div class="flex justify-between items-center border-b pb-3 gap-2 min-w-0">
            <h3 class="font-black text-slate-900 text-sm truncate">تعديل تسعيرة ورسوم المستوى الأكاديمي</h3>
            <button type="button" onclick="closeEditLevelModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold shrink-0 p-1">✕</button>
        </div>

        <form id="editLevelForm" method="POST" action="" class="space-y-4 text-xs min-w-0">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-0">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الاسم بالإنجليزي (English) <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_en" id="editLvlNameEn" required dir="ltr" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الاسم بالعربي</label>
                    <input type="text" name="name_ar" id="editLvlNameAr" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-0">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">رسوم المستوى الشاملة</label>
                    <input type="number" step="0.01" name="fee_amount" id="editLvlFee" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold text-emerald-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة</label>
                    <select name="currency" id="editLvlCurrency" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="YER">ريال يمني (YER)</option>
                        <option value="SAR">ريال سعودي (SAR)</option>
                        <option value="USD">دولار أمريكي (USD)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">وصف المستوى</label>
                <textarea name="description" id="editLvlDescription" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeEditLevelModal()" class="w-full sm:w-auto px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl text-center">إلغاء</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm text-center">حفظ وتحديث الرسوم 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- 5. مودال تعديل سعر الباقة (Edit Package Modal) -->
<!-- =============================================================== -->
<div id="editPackageModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 cursor-pointer" onclick="closeEditPackageModal()"></div>
    <div class="relative bg-white w-full max-w-md rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200 p-4 sm:p-6 space-y-4 sm:space-y-5 min-w-0 my-auto z-10">
        <div class="flex justify-between items-center border-b pb-3 gap-2 min-w-0">
            <h3 class="font-black text-slate-900 text-sm truncate">تعديل سعر وبيانات الباقة</h3>
            <button type="button" onclick="closeEditPackageModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold shrink-0 p-1">✕</button>
        </div>

        <form id="editPackageForm" method="POST" action="" class="space-y-4 text-xs min-w-0">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">عنوان الباقة</label>
                <input type="text" name="title" id="editPkgTitle" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-0">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">السعر الإجمالي</label>
                    <input type="number" step="0.01" name="price" id="editPkgPrice" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة</label>
                    <select name="currency" id="editPkgCurrency" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="YER">ريال يمني (YER)</option>
                        <option value="SAR">ريال سعودي (SAR)</option>
                        <option value="USD">دولار أمريكي (USD)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">عدد الحصص (25 دقيقة)</label>
                <input type="number" name="lessons_count" id="editPkgLessons" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الوصف والمميزات</label>
                <textarea name="description" id="editPkgDesc" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeEditPackageModal()" class="w-full sm:w-auto px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl text-center">إلغاء</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm text-center">حفظ التعديل 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- 6. مودال إضافة باقة جديدة (Add Package Modal) -->
<!-- =============================================================== -->
<div id="addPackageModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 cursor-pointer" onclick="closeAddPackageModal()"></div>
    <div class="relative bg-white w-full max-w-md rounded-2xl sm:rounded-3xl shadow-2xl border border-slate-200 p-4 sm:p-6 space-y-4 sm:space-y-5 min-w-0 my-auto z-10">
        <div class="flex justify-between items-center border-b pb-3 gap-2 min-w-0">
            <h3 class="font-black text-slate-900 text-sm truncate">إضافة باقة حصص جديدة</h3>
            <button type="button" onclick="closeAddPackageModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold shrink-0 p-1">✕</button>
        </div>

        <form action="{{ route('admin.packages.store') }}" method="POST" class="space-y-4 text-xs min-w-0">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">عنوان الباقة</label>
                <input type="text" name="title" required placeholder="مثال: باقة الانطلاقة (24 حصة)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-0">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">السعر الإجمالي</label>
                    <input type="number" step="0.01" name="price" required placeholder="مثال: 55000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة</label>
                    <select name="currency" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="YER" selected>ريال يمني (YER)</option>
                        <option value="SAR">ريال سعودي (SAR)</option>
                        <option value="USD">دولار أمريكي (USD)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">عدد الحصص (25 دقيقة)</label>
                <input type="number" name="lessons_count" value="12" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الوصف والمميزات</label>
                <textarea name="description" rows="2" placeholder="وصف الباقة وشروطها..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeAddPackageModal()" class="w-full sm:w-auto px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl text-center">إلغاء</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm text-center">إضافة الباقة 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- سكربت التحكم بالنوافذ المنبثقة -->
<!-- =============================================================== -->
<script>
    // باقات الحصص
    function openEditPackageModal(pkg) {
        document.getElementById('editPackageForm').action = '/admin/packages/' + pkg.id + '/update';
        document.getElementById('editPkgTitle').value = pkg.title || '';
        document.getElementById('editPkgPrice').value = pkg.price || '';
        document.getElementById('editPkgCurrency').value = pkg.currency || 'YER';
        document.getElementById('editPkgLessons').value = pkg.lessons_count || 12;
        document.getElementById('editPkgDesc').value = pkg.description || '';
        document.getElementById('editPackageModal').classList.remove('hidden');
    }

    function closeEditPackageModal() {
        document.getElementById('editPackageModal').classList.add('hidden');
    }

    function openAddPackageModal() {
        document.getElementById('addPackageModal').classList.remove('hidden');
    }

    function closeAddPackageModal() {
        document.getElementById('addPackageModal').classList.add('hidden');
    }

    // المستويات الأكاديمية
    function openAddLevelModal() {
        document.getElementById('addLevelModal').classList.remove('hidden');
    }

    function closeAddLevelModal() {
        document.getElementById('addLevelModal').classList.add('hidden');
    }

    function openEditLevelModal(lvl) {
        document.getElementById('editLevelForm').action = '/admin/levels/' + lvl.id + '/update';
        document.getElementById('editLvlNameEn').value = lvl.name_en || '';
        document.getElementById('editLvlNameAr').value = lvl.name_ar || lvl.name || '';
        document.getElementById('editLvlDescription').value = lvl.description || '';
        document.getElementById('editLvlFee').value = lvl.fee_amount || '';
        document.getElementById('editLvlCurrency').value = lvl.currency || 'YER';
        document.getElementById('editLevelModal').classList.remove('hidden');
    }

    function closeEditLevelModal() {
        document.getElementById('editLevelModal').classList.add('hidden');
    }

    // الدورات التدريبية
    function openAddCourseModal(levelId = null, levelName = null) {
        if (levelId) {
            document.getElementById('addCourseLevelId').value = levelId;
        }
        document.getElementById('addCourseModal').classList.remove('hidden');
    }

    function closeAddCourseModal() {
        document.getElementById('addCourseModal').classList.add('hidden');
    }

    function openEditCourseModal(crs) {
        document.getElementById('editCourseForm').action = '/admin/courses/' + crs.id + '/update';
        document.getElementById('editCourseLevelId').value = crs.level_id || '';
        document.getElementById('editCourseNameEn').value = crs.name_en || crs.name || '';
        document.getElementById('editCourseNameAr').value = crs.name_ar || '';
        document.getElementById('editCourseHours').value = crs.hours || 40;
        document.getElementById('editCourseFee').value = crs.fee_amount || '';
        document.getElementById('editCourseCurrency').value = crs.currency || 'YER';
        document.getElementById('editCourseDescription').value = crs.description || '';
        document.getElementById('editCourseIsActive').checked = crs.is_active ? true : false;
        document.getElementById('editCourseModal').classList.remove('hidden');
    }

    function closeEditCourseModal() {
        document.getElementById('editCourseModal').classList.add('hidden');
    }

    // التحكم في إظهار وإخفاء الدورات التابعة للمستوى (Accordion Toggle)
    function toggleLevelCourses(levelId) {
        const container = document.getElementById('level-courses-' + levelId);
        const txt = document.getElementById('txt-toggle-lvl-' + levelId);
        const icon = document.getElementById('icon-toggle-lvl-' + levelId);
        const btn = document.getElementById('btn-toggle-lvl-' + levelId);

        if (!container) return;

        const count = container.getAttribute('data-count') || '';

        if (container.classList.contains('hidden')) {
            // فتح الدورات
            container.classList.remove('hidden');
            if (txt) txt.innerText = 'إخفاء الدورات (' + count + ')';
            if (icon) icon.innerText = '▲';
            if (btn) {
                btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-500');
                btn.classList.add('bg-slate-800', 'hover:bg-slate-700');
            }
        } else {
            // إخفاء الدورات
            container.classList.add('hidden');
            if (txt) txt.innerText = 'عرض الدورات (' + count + ')';
            if (icon) icon.innerText = '▼';
            if (btn) {
                btn.classList.remove('bg-slate-800', 'hover:bg-slate-700');
                btn.classList.add('bg-indigo-600', 'hover:bg-indigo-500');
            }
        }
    }

    // توسيع جميع المستويات
    function expandAllLevels() {
        document.querySelectorAll('.level-courses-container').forEach(c => {
            c.classList.remove('hidden');
            const levelId = c.id.replace('level-courses-', '');
            const count = c.getAttribute('data-count') || '';
            const txt = document.getElementById('txt-toggle-lvl-' + levelId);
            const icon = document.getElementById('icon-toggle-lvl-' + levelId);
            const btn = document.getElementById('btn-toggle-lvl-' + levelId);

            if (txt) txt.innerText = 'إخفاء الدورات (' + count + ')';
            if (icon) icon.innerText = '▲';
            if (btn) {
                btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-500');
                btn.classList.add('bg-slate-800', 'hover:bg-slate-700');
            }
        });
    }

    // طي وإخفاء جميع المستويات
    function collapseAllLevels() {
        document.querySelectorAll('.level-courses-container').forEach(c => {
            c.classList.add('hidden');
            const levelId = c.id.replace('level-courses-', '');
            const count = c.getAttribute('data-count') || '';
            const txt = document.getElementById('txt-toggle-lvl-' + levelId);
            const icon = document.getElementById('icon-toggle-lvl-' + levelId);
            const btn = document.getElementById('btn-toggle-lvl-' + levelId);

            if (txt) txt.innerText = 'عرض الدورات (' + count + ')';
            if (icon) icon.innerText = '▼';
            if (btn) {
                btn.classList.remove('bg-slate-800', 'hover:bg-slate-700');
                btn.classList.add('bg-indigo-600', 'hover:bg-indigo-500');
            }
        });
    }
</script>
@endsection
