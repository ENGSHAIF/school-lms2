@extends('layouts.app', ['title' => 'بنك موارد ومناهج ' . $level->name . ' - ASOTS'])

@section('content')
<div class="space-y-8">

    <!-- مسار التنقل والترويسة الرئيسية -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-2 flex-wrap">
                <a href="{{ route('admin.courses.index') }}" class="hover:text-indigo-600 transition">المناهج والأسعار</a>
                <span>/</span>
                <span class="text-slate-600 font-bold">{{ $level->course?->name }}</span>
                <span>/</span>
                <span class="text-indigo-600 font-black">{{ $level->name }}</span>
            </div>

            <div class="flex items-center gap-3">
                <span class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-2xl font-black shadow-xs shrink-0">
                    📚
                </span>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">
                        بنك موارد ومناهج: {{ $level->name_ar ?: $level->name }}
                        @if($level->name_en)
                            <span class="text-sm font-mono text-indigo-600 font-bold block mt-1" dir="ltr">{{ $level->name_en }}</span>
                        @endif
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        المكتبة المركزية الدائمة للمستوى (كتب، تسجيلات صوتية، محاضرات فيديو، أوراق عمل، وكويزات) القابلة للمشاركة للمجموعات يومياً.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap w-full md:w-auto">
            <a href="{{ route('admin.courses.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                <span>←</span>
                <span>المناهج والأسعار</span>
            </a>

            <a href="{{ route('admin.exams.create', $level->id) }}" class="px-4 py-2.5 bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                <span>🎯</span>
                <span>تصميم كويز / اختبار جديد</span>
            </a>

            <button type="button" onclick="openAddResourceModal()" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                <span>➕</span>
                <span>إضافة مورد تعليمي جديد</span>
            </button>
        </div>
    </div>

    <!-- بطاقة معلومات المستوى والتسعيرة -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 rounded-3xl shadow-lg border border-indigo-900/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="space-y-1 max-w-2xl">
            <span class="text-[10px] font-black uppercase tracking-wider text-amber-400 bg-amber-400/10 px-2.5 py-0.5 rounded-full border border-amber-400/20">
                بيانات المستوى الأكاديمي
            </span>
            <h2 class="text-lg font-black text-white">
                {{ $level->name_ar ?: $level->name }}
                @if($level->name_en)
                    <span class="text-xs font-mono text-amber-300 font-normal mr-2" dir="ltr">({{ $level->name_en }})</span>
                @endif
            </h2>
            <p class="text-xs text-slate-300 leading-relaxed">{{ $level->description ?: 'منهج أكاديمي معتمد وفق معايير الإطار الأوروبي المشترك للغات CEFR.' }}</p>
        </div>

        <div class="flex items-center gap-4 border-t md:border-t-0 md:border-r border-white/10 pt-3 md:pt-0 md:pr-6 shrink-0">
            <div>
                <span class="text-[11px] text-slate-400 block font-bold">الرسوم الدراسية المعتمدة:</span>
                <span class="text-2xl font-black text-amber-400 font-mono">{{ $level->formatted_fee }}</span>
                <span class="text-[10px] text-slate-300 block">{{ $level->currency_name }}</span>
            </div>
            <a href="{{ route('admin.groups.index', ['course_id' => $level->course_id]) }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/15 transition flex items-center gap-1.5">
                <span>👥</span>
                <span>المجموعات ({{ $studyGroups->count() }})</span>
            </a>
        </div>
    </div>

    <!-- شريط الإحصائيات والأرقام السريعة للمستوى -->
    <!-- شريط الإحصائيات والأرقام السريعة للمستوى -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">إجمالي الموارد</span>
            <span class="text-2xl font-black text-slate-900 font-mono">{{ $stats['total'] }}</span>
            <span class="text-[10px] text-indigo-600 font-bold block">مورد دائم</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">الكتب والمقررات</span>
            <span class="text-2xl font-black text-emerald-600 font-mono">{{ $stats['books'] }}</span>
            <span class="text-[10px] text-slate-400 block">ملفات PDF</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">التسجيلات الصوتية</span>
            <span class="text-2xl font-black text-purple-600 font-mono">{{ $stats['audios'] }}</span>
            <span class="text-[10px] text-slate-400 block">تسجيلات MP3</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">محاضرات الفيديو</span>
            <span class="text-2xl font-black text-blue-600 font-mono">{{ $stats['videos'] }}</span>
            <span class="text-[10px] text-slate-400 block">فيديوهات شرح</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">صور وبطاقات</span>
            <span class="text-2xl font-black text-pink-600 font-mono">{{ $stats['images'] ?? 0 }}</span>
            <span class="text-[10px] text-slate-400 block">أنشطة بصرية</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">أوراق العمل</span>
            <span class="text-2xl font-black text-amber-600 font-mono">{{ $stats['worksheets'] }}</span>
            <span class="text-[10px] text-slate-400 block">أنشطة وتطبيقات</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">الكويزات والاختبارات</span>
            <span class="text-2xl font-black text-rose-600 font-mono">{{ $stats['quizzes'] }}</span>
            <span class="text-[10px] text-slate-400 block">اختبارات إلكترونية</span>
        </div>
    </div>

    <!-- أزرار الفلترة حسب نوع المورد -->
    <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-3 flex-wrap">
        <div class="flex items-center gap-1.5 overflow-x-auto py-1">
            <button type="button" onclick="filterResources('all')" id="tab-all" class="filter-tab px-4 py-2 rounded-xl text-xs font-black bg-slate-900 text-white shadow-sm transition">
                الكل ({{ $stats['total'] }})
            </button>
            <button type="button" onclick="filterResources('book')" id="tab-book" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>📚</span>
                <span>الكتب والمقررات ({{ $stats['books'] }})</span>
            </button>
            <button type="button" onclick="filterResources('audio')" id="tab-audio" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>🎧</span>
                <span>التسجيلات الصوتية ({{ $stats['audios'] }})</span>
            </button>
            <button type="button" onclick="filterResources('video')" id="tab-video" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>🎥</span>
                <span>محاضرات الفيديو ({{ $stats['videos'] }})</span>
            </button>
            <button type="button" onclick="filterResources('image')" id="tab-image" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>🖼️</span>
                <span>صور وبطاقات الأنشطة ({{ $stats['images'] ?? 0 }})</span>
            </button>
            <button type="button" onclick="filterResources('worksheet')" id="tab-worksheet" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>📝</span>
                <span>أوراق العمل والأنشطة ({{ $stats['worksheets'] }})</span>
            </button>
            <button type="button" onclick="filterResources('quiz')" id="tab-quiz" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>🎯</span>
                <span>الكويزات والاختبارات ({{ $stats['quizzes'] }})</span>
            </button>
        </div>

        <div class="text-xs text-slate-500 font-bold bg-amber-50 text-amber-900 border border-amber-200 px-3 py-1.5 rounded-xl">
            💡 يمكنك إضافة أكثر من كتاب وتسجيل وفيديو وصورة نشاط لنفس المستوى ومشاركتها مع المجموعات يومياً.
        </div>
    </div>

    <!-- شبكة بطاقات الموارد التعليمية (Cards Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="resourcesGrid">
        @forelse($level->resources as $res)
            <div class="resource-card bg-white rounded-3xl border border-slate-200 hover:border-indigo-400 transition shadow-xs flex flex-col justify-between overflow-hidden" data-type="{{ $res->type }}">
                
                <!-- رأس البطاقة: النوع والترتيب والحالة -->
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black border flex items-center gap-1.5 {{ $res->type_badge_class }}">
                            <span>{{ $res->type_icon }}</span>
                            <span>{{ $res->type_label }}</span>
                        </span>

                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">
                                #{{ $res->order_index }}
                            </span>
                            @if($res->is_active)
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" title="نشط ومتاح"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-300" title="معطل مؤقتاً"></span>
                            @endif
                        </div>
                    </div>

                    <!-- عنوان المورد -->
                    <h3 class="font-black text-sm sm:text-base text-slate-900 leading-snug">
                        {{ $res->title }}
                    </h3>

                    <!-- الوصف أو التعليمات -->
                    @if($res->description)
                        <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed">
                            {{ $res->description }}
                        </p>
                    @endif

                    <!-- مشغل الصوت التفاعلي (إذا كان صوتياً) -->
                    @if($res->type === 'audio' && $res->file_path)
                        <div class="bg-purple-50 p-3 rounded-2xl border border-purple-100 space-y-1.5">
                            <span class="text-[10px] font-bold text-purple-900 block flex items-center gap-1">
                                <span>▶️</span>
                                <span>استماع للتسجيل الصوتي للمستوى:</span>
                            </span>
                            <audio controls class="w-full h-8">
                                <source src="{{ asset($res->file_path) }}" type="audio/mpeg">
                                متصفحك لا يدعم مشغل الصوت.
                            </audio>
                        </div>
                    @endif

                    <!-- معاينة الفيديو ورابط المحاضرة (إذا كان فيديو) -->
                    @if($res->type === 'video')
                        <div class="bg-blue-50 p-3 rounded-2xl border border-blue-100 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-blue-900 font-bold flex items-center gap-1">
                                    <span>🎥</span>
                                    <span>محاضرة مرئية</span>
                                </span>
                                @if($res->duration_hours || $res->duration_minutes)
                                    <span class="text-[10px] font-mono text-blue-700 bg-blue-100 px-2 py-0.5 rounded font-bold">
                                        ⏱️ {{ $res->formatted_duration }}
                                    </span>
                                @endif
                            </div>

                            @if($res->external_url)
                                <a href="{{ $res->external_url }}" target="_blank" class="w-full py-1.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                                    <span>▶️</span>
                                    <span>مشاهدة فيديو الشرح والمحاضرة</span>
                                </a>
                            @elseif($res->file_path)
                                <a href="{{ asset($res->file_path) }}" target="_blank" class="w-full py-1.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                                    <span>▶️</span>
                                    <span>تشغيل ملف الفيديو المرفوع</span>
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- صور وبطاقات الأنشطة (معاينة وتحميل) -->
                    @if($res->type === 'image' && $res->file_path)
                        <div class="bg-pink-50/70 p-3 rounded-2xl border border-pink-200 space-y-2">
                            <a href="{{ asset($res->file_path) }}" target="_blank" class="block relative rounded-xl overflow-hidden group/img aspect-video bg-slate-900 border border-pink-200">
                                <img src="{{ asset($res->file_path) }}" alt="{{ $res->title }}" class="w-full h-full object-cover group-hover/img:scale-105 transition duration-300">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1">
                                    <span>🔍</span>
                                    <span>عرض الصورة بالحجم الكامل</span>
                                </div>
                            </a>
                            <div class="flex items-center justify-between text-xs pt-0.5">
                                <span class="text-[10px] text-slate-500 font-mono truncate max-w-[180px]">{{ $res->file_name ?? 'صورة تعليمية' }}</span>
                                <a href="{{ asset($res->file_path) }}" download target="_blank" class="px-2.5 py-1 rounded-lg bg-pink-600 hover:bg-pink-700 text-white font-bold text-[10px] transition shrink-0 flex items-center gap-1">
                                    <span>📥</span>
                                    <span>تحميل</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- الكتب وأوراق العمل (تحميل ومعاينة) -->
                    @if(in_array($res->type, ['book', 'worksheet']) && $res->file_path)
                        <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-200 flex items-center justify-between text-xs">
                            <div class="truncate pl-2">
                                <span class="font-bold text-slate-800 block truncate">{{ $res->file_name ?? 'ملف رقمي' }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $res->file_size ?? 'PDF' }}</span>
                            </div>
                            <a href="{{ asset($res->file_path) }}" download target="_blank" class="px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs transition shrink-0 flex items-center gap-1">
                                <span>📥</span>
                                <span>تحميل</span>
                            </a>
                        </div>
                    @endif

                    <!-- الكويزات والاختبارات -->
                    @if($res->type === 'quiz' && $res->exam)
                        <div class="bg-rose-50 p-3.5 rounded-2xl border border-rose-100 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-rose-950">{{ $res->exam->title }}</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $res->exam->quiz_period_badge }}">
                                    {{ $res->exam->quiz_period_label }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-rose-800 font-mono">
                                <span>الأسئلة: {{ $res->exam->questions->count() }} (يظهر {{ $res->exam->random_questions_count ?: $res->exam->questions->count() }})</span>
                                <span>الدرجة: {{ $res->exam->total_points }} • المدة: {{ $res->exam->duration_minutes }}د</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 pt-1">
                                <a href="{{ route('admin.exams.edit', $res->exam->id) }}" class="py-1.5 px-2 bg-white hover:bg-rose-100 border border-rose-200 text-rose-900 rounded-xl font-bold text-center text-[11px] transition flex items-center justify-center gap-1">
                                    <span>✏️</span>
                                    <span>بنك الأسئلة</span>
                                </a>
                                <a href="{{ route('admin.exams.attempts', $res->exam->id) }}" class="py-1.5 px-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-black text-center text-[11px] transition flex items-center justify-center gap-1">
                                    <span>📊</span>
                                    <span>النتائج ({{ $res->exam->attempts->count() }})</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- أسفل البطاقة: زر المشاركة للمجموعة والإجراءات -->
                <div class="p-4 bg-slate-50 border-t border-slate-100 space-y-2">
                    <!-- عدد المجموعات التي تمت مشاركة هذا المورد معها -->
                    @php $sharedGroupsCount = $res->groupActivities->count(); @endphp
                    <div class="flex items-center justify-between text-[11px] text-slate-500">
                        <span>الحالة في المجموعات:</span>
                        @if($sharedGroupsCount > 0)
                            <span class="text-emerald-700 font-bold bg-emerald-100/70 px-2 py-0.5 rounded-full font-mono">
                                ✅ منشور في {{ $sharedGroupsCount }} مجموعات
                            </span>
                        @else
                            <span class="text-slate-400 bg-slate-200/60 px-2 py-0.5 rounded-full">
                                ⏳ في بنك المستوى فقط
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-2 pt-1">
                        <!-- زر المشاركة الفورية للمجموعة -->
                        <button type="button" onclick='openShareModal(@json($res))' class="col-span-2 py-2 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-black text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                            <span>🚀</span>
                            <span>مشاركة لمجموعة اليوم</span>
                        </button>

                        <!-- زر التعديل -->
                        <button type="button" onclick='openEditResourceModal(@json($res))' class="py-2 px-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs transition flex items-center justify-center" title="تعديل المورد">
                            <span>✏️</span>
                        </button>

                        <!-- زر الحذف -->
                        <form method="POST" action="{{ route('admin.levels.resources.destroy', $res->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد نهائياً من بنك المستوى؟');" class="col-span-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-1 text-center text-[10px] text-rose-500 hover:text-rose-700 hover:underline">
                                حذف المورد من بنك المستوى ✕
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-3xl border border-dashed border-slate-300 text-center space-y-3">
                <span class="text-4xl block">📦</span>
                <h3 class="text-base font-bold text-slate-700">لا توجد موارد مضافة بعد في بنك هذا المستوى</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto">
                    ابدأ الآن برفع الكتب الرقمية أو التسجيلات الصوتية أو محاضرات الفيديو أو أوراق العمل التفاعلية لتصبح متاحة للمدرسين والمشرفين لمشاركتها مع الطلاب يومياً.
                </p>
                <button type="button" onclick="openAddResourceModal()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                    ➕ إضافة أول مورد للمستوى
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- =============================================================== -->
<!-- 1. مودال إضافة مورد تعليمي جديد للمستوى (Add Resource Modal) -->
<!-- =============================================================== -->
<div id="addResourceModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="fixed inset-0 cursor-pointer" onclick="closeAddResourceModal()"></div>

    <div class="relative bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 flex flex-col max-h-[92vh] overflow-hidden z-10">
        <!-- ترويسة المودال -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shadow-xs">
                    📥
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">إضافة مورد تعليمي جديد إلى بنك المستوى</h2>
                    <p class="text-xs text-slate-500 mt-0.5">المستوى المستهدف: [{{ $level->name }}]</p>
                </div>
            </div>
            <button type="button" onclick="closeAddResourceModal()" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center text-sm font-bold transition">✕</button>
        </div>

        <!-- جسم النموذج القابل للتمرير -->
        <form method="POST" action="{{ route('admin.levels.resources.store', $level->id) }}" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden">
            @csrf
            <div class="overflow-y-auto flex-1 p-6 space-y-4 text-xs">
                
                <!-- نوع المورد التعليمي -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block font-bold text-slate-700">نوع المورد التعليمي <span class="text-red-500">*</span></label>
                        <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            ✓ يمكنك إضافة عدد غير محدود من كل نوع
                        </span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
                        <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition flex flex-col items-center gap-1 hover:border-indigo-500" id="type-opt-book">
                            <input type="radio" name="type" value="book" checked onchange="onTypeSelected(this.value)" class="sr-only">
                            <span class="text-xl">📚</span>
                            <span class="font-bold text-[11px] text-slate-800">كتاب / مقرر</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition flex flex-col items-center gap-1 hover:border-indigo-500" id="type-opt-audio">
                            <input type="radio" name="type" value="audio" onchange="onTypeSelected(this.value)" class="sr-only">
                            <span class="text-xl">🎧</span>
                            <span class="font-bold text-[11px] text-slate-800">تسجيل صوتي</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition flex flex-col items-center gap-1 hover:border-indigo-500" id="type-opt-video">
                            <input type="radio" name="type" value="video" onchange="onTypeSelected(this.value)" class="sr-only">
                            <span class="text-xl">🎥</span>
                            <span class="font-bold text-[11px] text-slate-800">محاضرة فيديو</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition flex flex-col items-center gap-1 hover:border-indigo-500" id="type-opt-image">
                            <input type="radio" name="type" value="image" onchange="onTypeSelected(this.value)" class="sr-only">
                            <span class="text-xl">🖼️</span>
                            <span class="font-bold text-[11px] text-slate-800">صورة نشاط</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition flex flex-col items-center gap-1 hover:border-indigo-500" id="type-opt-worksheet">
                            <input type="radio" name="type" value="worksheet" onchange="onTypeSelected(this.value)" class="sr-only">
                            <span class="text-xl">📝</span>
                            <span class="font-bold text-[11px] text-slate-800">ورقة عمل</span>
                        </label>

                        <label class="cursor-pointer border-2 rounded-xl p-2.5 text-center transition flex flex-col items-center gap-1 hover:border-indigo-500" id="type-opt-quiz">
                            <input type="radio" name="type" value="quiz" onchange="onTypeSelected(this.value)" class="sr-only">
                            <span class="text-xl">🎯</span>
                            <span class="font-bold text-[11px] text-slate-800">كويز واختبار</span>
                        </label>
                    </div>
                </div>

                <!-- عنوان المورد -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">عنوان المورد أو اسم الكتاب / الحصة <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="مثال: كتاب الطالب Level 1 - الوحدة الأولى أو حوار الاستماع Listening Unit 1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <!-- الوصف أو ملاحظات المعلم -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الوصف أو التعليمات التوجيهية للطلاب</label>
                    <textarea name="description" rows="2" placeholder="اكتب نبذة عن هذا المورد وما يتطلبه من الطالب..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>

                <!-- حقل رفع الملف (للكتب، الصوتيات، الفيديوهات المرفوعة، وأوراق العمل) -->
                <div id="fileUploadContainer">
                    <label class="block font-bold text-slate-700 mb-1" id="fileUploadLabel">
                        ملف المورد الرقمي (PDF, MP3, MP4, Word)
                    </label>
                    <input type="file" name="attachment" id="fileInput" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 focus:ring-2 focus:ring-indigo-500 outline-none text-xs">
                    <span class="text-[10px] text-slate-400 mt-1 block">يدعم كتب PDF، تسجيلات MP3/WAV، مقاطع MP4، ومستندات Word (حتى 100 ميجابايت).</span>
                </div>

                <!-- حقل رابط الفيديو الخارجي (يوتيوب أو فيميو أو درايف) -->
                <div id="videoUrlContainer" class="hidden">
                    <label class="block font-bold text-slate-700 mb-1">أو رابط المحاضرة / الفيديو التعليمي (YouTube / Drive / Vimeo)</label>
                    <input type="url" name="external_url" placeholder="https://www.youtube.com/watch?v=..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono" dir="ltr">
                </div>

                <!-- ربط باختبار أو كويز -->
                <div id="quizSelectContainer" class="hidden">
                    <label class="block font-bold text-slate-700 mb-1">اختر الاختبار / الكويز المرتبط</label>
                    <select name="exam_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="">-- اختر من قائمة الاختبارات المتاحة --</option>
                        @foreach($availableExams as $ex)
                            <option value="{{ $ex->id }}">{{ $ex->title }} ({{ $ex->total_points }} درجة)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">المدة التقديرية (بالساعات)</label>
                        <input type="number" name="duration_hours" step="0.1" min="0.1" max="1000" placeholder="مثال: 2 أو 1.5 أو 40" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">ترتيب الظهور في القائمة</label>
                        <input type="number" name="order_index" value="{{ $stats['total'] + 1 }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                    </div>
                </div>

            </div>

            <!-- أزرار الإجراءات في الأسفل -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 shrink-0">
                <button type="button" onclick="closeAddResourceModal()" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-200 bg-white border border-slate-300 transition">
                    إلغاء ✕
                </button>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>🚀</span>
                    <span>حفظ وإضافة المورد لبنك المستوى</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- 2. مودال مشاركة المورد التعليمي لمجموعة محددة (Share to Group Modal) -->
<!-- =============================================================== -->
<div id="shareResourceModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="fixed inset-0 cursor-pointer" onclick="closeShareModal()"></div>

    <div class="relative bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-200 flex flex-col max-h-[90vh] overflow-hidden z-10">
        <!-- ترويسة المودال -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-emerald-50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-xs">
                    🚀
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">مشاركة المورد لطلاب المجموعة اليوم</h2>
                    <p class="text-xs text-slate-500 mt-0.5" id="shareModalSubtitle">استدعاء ونشر المورد من بنك المستوى لداخل المجموعة</p>
                </div>
            </div>
            <button type="button" onclick="closeShareModal()" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center text-sm font-bold transition">✕</button>
        </div>

        <form id="shareResourceForm" method="POST" action="" class="flex flex-col flex-1 min-h-0 overflow-hidden">
            @csrf
            <div class="overflow-y-auto flex-1 p-6 space-y-4 text-xs">
                
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 block">المورد المراد مشاركته:</span>
                    <strong class="text-sm font-black text-slate-900 block" id="shareResourceTitle">--</strong>
                </div>

                <!-- اختيار المجموعة الدراسية -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">المجموعة الدراسية المستهدفة <span class="text-red-500">*</span></label>
                    <select name="study_group_id" id="shareGroupIdSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-xs">
                        @forelse($studyGroups as $grp)
                            <option value="{{ $grp->id }}">
                                👥 {{ $grp->name }} ({{ $grp->students->count() }} طالب • الأستاذ: {{ $grp->teacher?->name ?? 'غير محدد' }})
                            </option>
                        @empty
                            <option value="" disabled>لا توجد مجموعات نشطة مسجلة في هذا المستوى بعد</option>
                        @endforelse
                    </select>
                </div>

                <!-- تعليمات أو واجب المعلم المخصص لهذا اليوم -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">ملاحظات وتعليمات المعلم لطلاب المجموعة لهذا اليوم</label>
                    <textarea name="custom_instructions" id="shareCustomInstructions" rows="3" placeholder="مثال: يا شباب، يرجى الاستماع لهذا التسجيل وحل الصفحة 12 ومناقشتها في الحصة القادمة..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>

                <!-- موعد التسليم إن كان واجباً -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ وموعد التسليم / الانتهاء (اختياري)</label>
                    <input type="date" name="due_date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>

                <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-amber-900 text-[11px] leading-relaxed">
                    ✨ <strong>ميزة الاستدعاء المتكرر:</strong> يبقى هذا المورد محفوظاً دائماً في بنك المستوى، ويمكن للمعلم نشره لمجموعات أخرى أو لأي دفعة جديدة مستقبلاً في أي وقت بنقرة واحدة!
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 shrink-0">
                <button type="button" onclick="closeShareModal()" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-200 bg-white border border-slate-300 transition">
                    إلغاء ✕
                </button>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>🚀</span>
                    <span>نشر ومشاركة للمجموعة الآن</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- 3. مودال تعديل المورد (Edit Resource Modal) -->
<!-- =============================================================== -->
<div id="editResourceModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="fixed inset-0 cursor-pointer" onclick="closeEditResourceModal()"></div>

    <div class="relative bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 flex flex-col max-h-[92vh] overflow-hidden z-10">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl shadow-xs">
                    ✏️
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 leading-tight">تعديل بيانات المورد الأكاديمي</h2>
                    <p class="text-xs text-slate-500 mt-0.5">تحديث الملف أو البيانات في بنك المستوى</p>
                </div>
            </div>
            <button type="button" onclick="closeEditResourceModal()" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center text-sm font-bold transition">✕</button>
        </div>

        <form id="editResourceForm" method="POST" action="" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden">
            @csrf
            <div class="overflow-y-auto flex-1 p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">نوع المورد</label>
                    <select name="type" id="editResourceType" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="book">📚 كتاب / مقرر دراسي</option>
                        <option value="audio">🎧 تسجيل صوتي</option>
                        <option value="video">🎥 محاضرة فيديو تعليمية</option>
                        <option value="image">🖼️ صورة نشاط وتدريب بصري</option>
                        <option value="worksheet">📝 ورقة عمل ونشاط</option>
                        <option value="quiz">🎯 كويز واختبار قصير</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">عنوان المورد <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="editResourceTitle" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الوصف أو التعليمات التوجيهية</label>
                    <textarea name="description" id="editResourceDescription" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">استبدال الملف الرقمي (اختياري - اتركه فارغاً للاحتفاظ بالملف الحالي)</label>
                    <input type="file" name="attachment" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 focus:ring-2 focus:ring-indigo-500 outline-none text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">رابط الفيديو الخارجي (إن وجد)</label>
                    <input type="url" name="external_url" id="editResourceExternalUrl" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono" dir="ltr">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">المدة التقديرية (بالساعات)</label>
                        <input type="number" name="duration_hours" id="editResourceDurationHours" step="0.1" min="0.1" max="1000" placeholder="مثال: 2 أو 1.5" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الترتيب</label>
                        <input type="number" name="order_index" id="editResourceOrder" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="editResourceIsActive" value="1" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <label for="editResourceIsActive" class="font-bold text-slate-800 cursor-pointer">المورد نشط ومتاح في قائمة المستوى</label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 shrink-0">
                <button type="button" onclick="closeEditResourceModal()" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-200 bg-white border border-slate-300 transition">
                    إلغاء ✕
                </button>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>💾</span>
                    <span>حفظ التعديلات</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // فلترة الموارد بحسب النوع
    function filterResources(type) {
        document.querySelectorAll('.filter-tab').forEach(btn => {
            btn.classList.remove('bg-slate-900', 'text-white', 'shadow-sm');
            btn.classList.add('text-slate-600');
        });

        const activeBtn = document.getElementById('tab-' + type);
        if (activeBtn) {
            activeBtn.classList.add('bg-slate-900', 'text-white', 'shadow-sm');
            activeBtn.classList.remove('text-slate-600');
        }

        document.querySelectorAll('.resource-card').forEach(card => {
            if (type === 'all' || card.getAttribute('data-type') === type) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // إدارة نوافذ المودال
    function openAddResourceModal() {
        document.getElementById('addResourceModal').classList.remove('hidden');
        onTypeSelected('book');
    }
    function closeAddResourceModal() {
        document.getElementById('addResourceModal').classList.add('hidden');
    }

    function onTypeSelected(type) {
        document.querySelectorAll('[id^="type-opt-"]').forEach(el => {
            el.classList.remove('border-indigo-600', 'bg-indigo-50/50');
            el.classList.add('border-slate-200');
        });

        const selectedOpt = document.getElementById('type-opt-' + type);
        if (selectedOpt) {
            selectedOpt.classList.add('border-indigo-600', 'bg-indigo-50/50');
            selectedOpt.classList.remove('border-slate-200');
        }

        const videoContainer = document.getElementById('videoUrlContainer');
        const quizContainer = document.getElementById('quizSelectContainer');
        const fileLabel = document.getElementById('fileUploadLabel');

        if (type === 'video') {
            videoContainer.classList.remove('hidden');
            quizContainer.classList.add('hidden');
            fileLabel.innerText = 'أو رفع ملف الفيديو مباشرة (MP4)';
        } else if (type === 'quiz') {
            videoContainer.classList.add('hidden');
            quizContainer.classList.remove('hidden');
            fileLabel.innerText = 'ملف إضافي أو نموذج PDF (اختياري)';
        } else if (type === 'image') {
            videoContainer.classList.add('hidden');
            quizContainer.classList.add('hidden');
            fileLabel.innerText = 'ملف الصورة التعليمية أو ورقة النشاط (JPG, PNG, WebP)';
        } else if (type === 'audio') {
            videoContainer.classList.add('hidden');
            quizContainer.classList.add('hidden');
            fileLabel.innerText = 'ملف التسجيل الصوتي (MP3/WAV)';
        } else {
            videoContainer.classList.add('hidden');
            quizContainer.classList.add('hidden');
            fileLabel.innerText = 'ملف الكتاب أو المقرر الرقمي (PDF/Word)';
        }
    }

    // مودال مشاركة المورد لمجموعة
    function openShareModal(resource) {
        const form = document.getElementById('shareResourceForm');
        form.action = `/groups/${resource.id}/share-resource`.replace(`${resource.id}`, resource.id);
        // Note: form action will post to /groups/{groupId}/share-resource or we can set it to resource share route:
        form.action = `/groups/placeholder/share-resource`;
        // In our route: Route::post('/groups/{groupId}/share-resource', ...) so groupId is the param!
        // We will update the action dynamically when the group is selected:
        updateShareFormAction(resource.id);

        document.getElementById('shareResourceTitle').innerText = resource.title;
        document.getElementById('shareCustomInstructions').value = resource.description || '';
        document.getElementById('shareResourceModal').classList.remove('hidden');
    }

    function updateShareFormAction(resourceId) {
        const select = document.getElementById('shareGroupIdSelect');
        const form = document.getElementById('shareResourceForm');
        if (select && select.value) {
            form.action = `/groups/${select.value}/share-resource?resource_id=${resourceId}`;
            // Add a hidden input for resource_id if not present
            let hiddenRes = form.querySelector('input[name="resource_id"]');
            if (!hiddenRes) {
                hiddenRes = document.createElement('input');
                hiddenRes.type = 'hidden';
                hiddenRes.name = 'resource_id';
                form.appendChild(hiddenRes);
            }
            hiddenRes.value = resourceId;
        }
    }

    document.getElementById('shareGroupIdSelect')?.addEventListener('change', function() {
        const resId = document.querySelector('input[name="resource_id"]')?.value;
        if (resId) {
            document.getElementById('shareResourceForm').action = `/groups/${this.value}/share-resource`;
        }
    });

    function closeShareModal() {
        document.getElementById('shareResourceModal').classList.add('hidden');
    }

    // مودال تعديل المورد
    function openEditResourceModal(resource) {
        const form = document.getElementById('editResourceForm');
        form.action = `/admin/levels/resources/${resource.id}/update`;

        document.getElementById('editResourceTitle').value = resource.title;
        document.getElementById('editResourceType').value = resource.type;
        document.getElementById('editResourceDescription').value = resource.description || '';
        document.getElementById('editResourceExternalUrl').value = resource.external_url || '';
        document.getElementById('editResourceDurationHours').value = resource.duration_hours || (resource.duration_minutes ? (resource.duration_minutes / 60).toFixed(1) : '');
        document.getElementById('editResourceOrder').value = resource.order_index || 1;
        document.getElementById('editResourceIsActive').checked = resource.is_active;

        document.getElementById('editResourceModal').classList.remove('hidden');
    }
    function closeEditResourceModal() {
        document.getElementById('editResourceModal').classList.add('hidden');
    }

    // الفتح التلقائي لمودال الإضافة عند التوجيه المباشر
    document.addEventListener('DOMContentLoaded', function () {
        if (new URLSearchParams(window.location.search).get('action') === 'add') {
            openAddResourceModal();
        }
    });
</script>
@endsection
