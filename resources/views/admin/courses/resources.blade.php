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
                        المكتبة المركزية الدائمة للدورة (كتب PDF، تسجيلات صوتية، محاضرات فيديو، صور نشاط، أوراق عمل، وكويزات) القابلة للمشاركة للمجموعات يومياً.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap w-full md:w-auto">
            <a href="{{ route('admin.courses.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                <span>←</span>
                <span>المناهج والأسعار</span>
            </a>

            @if($course->level_id)
                <a href="{{ route('admin.exams.create', $course->level_id) }}" class="px-4 py-2.5 bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>🎯</span>
                    <span>تصميم كويز / اختبار جديد</span>
                </a>
            @endif

            <button type="button" onclick="openAddResourceModal()" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                <span>➕</span>
                <span>إضافة مورد تعليمي للدورة</span>
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
            </div>

            <h2 class="text-xl font-black text-white">
                {{ $course->name_en }}
                @if($course->name_ar)
                    <span class="text-sm font-normal text-slate-300 mr-2">({{ $course->name_ar }})</span>
                @endif
            </h2>

            <p class="text-xs text-slate-300 leading-relaxed">
                {{ $course->description ?: "دورة معتمدة بواقع {$course->hours} ساعة تدريبية تفاعلية مباشرة مع بنك مناهج رقمي شامل." }}
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

    <!-- شريط فلترة وتبويبات الموارد وإحصائياتها -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" onclick="filterResources('all')" id="tab-all" class="filter-tab active px-3.5 py-2 rounded-xl text-xs font-black bg-indigo-600 text-white transition flex items-center gap-1.5 shadow-xs">
                <span>📁</span>
                <span>الكل ({{ $stats['total'] }})</span>
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
                <span>أوراق العمل ({{ $stats['worksheets'] }})</span>
            </button>
            <button type="button" onclick="filterResources('quiz')" id="tab-quiz" class="filter-tab px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1">
                <span>🎯</span>
                <span>الكويزات والاختبارات ({{ $stats['quizzes'] }})</span>
            </button>
        </div>

        <div class="text-xs text-slate-500 font-bold bg-amber-50 text-amber-900 border border-amber-200 px-3 py-1.5 rounded-xl">
            💡 يمكنك رفع مناهج وكتب الدورة ومشاركتها مع مجموعات الطلاب بكل سهولة.
        </div>
    </div>

    <!-- شبكة بطاقات الموارد التعليمية (Cards Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="resourcesGrid">
        @forelse($course->resources as $res)
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
                                <span>استماع للتسجيل الصوتي للدورة:</span>
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
                                    <span>تكبير ومعاينة الصورة كاملة</span>
                                </div>
                            </a>
                        </div>
                    @endif

                    <!-- تفاصيل الكويز المربوط (إذا كان كويز) -->
                    @if($res->type === 'quiz' && $res->exam)
                        <div class="bg-rose-50 p-3 rounded-2xl border border-rose-100 space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-rose-900">اختبار كويز مربوط:</span>
                                <span class="font-mono text-[10px] text-rose-700 bg-rose-100 px-2 py-0.5 rounded font-bold">
                                    {{ $res->exam->total_points }} درجة
                                </span>
                            </div>
                            <span class="text-xs font-black text-slate-800 block">{{ $res->exam->title }}</span>
                            <a href="{{ route('admin.exams.edit', $res->exam_id) }}" class="text-[11px] text-rose-700 font-bold hover:underline block">
                                تعديل أسئلة الكويز ←
                            </a>
                        </div>
                    @endif

                    <!-- تفاصيل الملف المرفق -->
                    @if($res->file_path)
                        <div class="flex items-center justify-between text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                            <div class="flex items-center gap-2 truncate">
                                <span class="text-slate-400 font-bold">📎</span>
                                <span class="truncate font-mono text-[11px] text-slate-700" title="{{ $res->file_name }}">
                                    {{ $res->file_name ?: basename($res->file_path) }}
                                </span>
                            </div>
                            @if($res->file_size)
                                <span class="font-mono text-[10px] text-slate-400 shrink-0 font-bold mr-1">
                                    {{ $res->file_size }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- أسفل البطاقة: الإجراءات والمشاركة للمجموعات -->
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        @if($res->file_path)
                            <a href="{{ asset($res->file_path) }}" target="_blank" download class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-slate-700 font-bold text-xs transition shadow-2xs flex items-center gap-1" title="تحميل الملف">
                                <span>⬇️</span>
                                <span>تحميل</span>
                            </a>
                        @endif

                        @if($res->external_url)
                            <a href="{{ $res->external_url }}" target="_blank" class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-indigo-700 font-bold text-xs transition shadow-2xs flex items-center gap-1" title="فتح الرابط">
                                <span>🔗</span>
                                <span>فتح</span>
                            </a>
                        @endif

                        <!-- زر مشاركة المورد لمجموعة طلاب محددة -->
                        @if($studyGroups->count() > 0)
                            <button type="button" onclick="openShareModal({{ $res->id }}, '{{ addslashes($res->title) }}')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1" title="مشاركة هذا المورد لمجموعة معينة لتظهر في صفحتهم">
                                <span>📢</span>
                                <span>مشاركة لمجموعة</span>
                            </button>
                        @endif
                    </div>

                    <!-- زر حذف المورد -->
                    <form action="{{ route('admin.levels.resources.destroy', $res->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد نهائياً من بنك الدورة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition text-xs font-bold" title="حذف المورد">
                            🗑️
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl border border-dashed border-slate-300 p-12 text-center space-y-4">
                <span class="text-5xl block">📚</span>
                <div class="space-y-1">
                    <h3 class="text-lg font-black text-slate-900">بنك موارد الدورة فارغ حالياً</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        لم يتم رفع أي كتب أو صوتيات أو أنشطة لهذه الدورة بعد. يمكنك البدء الآن بالضغط على زر "إضافة مورد تعليمي للدورة".
                    </p>
                </div>
                <button type="button" onclick="openAddResourceModal()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition inline-flex items-center gap-1.5">
                    <span>➕</span>
                    <span>رفع أول كتاب أو تسجيل أو مورد الآن</span>
                </button>
            </div>
        @endforelse
    </div>

</div>

<!-- =============================================================== -->
<!-- مودال إضافة مورد تعليمي جديد للدورة (Upload Resource Modal) -->
<!-- =============================================================== -->
<div id="addResourceModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-5">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h3 class="font-black text-slate-900 text-sm">إضافة مورد تعليمي جديد لبنك الدورة</h3>
                <p class="text-[11px] text-slate-500">رفع كتاب PDF، تسجيل صوتي MP3، فيديو، صورة نشاط، أو ربط كويز.</p>
            </div>
            <button type="button" onclick="closeAddResourceModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
        </div>

        <form action="{{ route('admin.courses.resources.store', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- عنوان المورد -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">عنوان المورد أو الملف <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="مثال: كتاب الطالب Team Together 1 PDF أو تسجيل Unit 1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
            </div>

            <!-- نوع المورد -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">نوع المورد التعليمي <span class="text-rose-500">*</span></label>
                <select name="type" id="resourceTypeSelect" onchange="toggleResourceTypeFields()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                    <option value="book">📚 كتاب ومقرر دراسي (PDF / Document)</option>
                    <option value="audio">🎧 تسجيل صوتي للوحدة أو المحادثة (MP3 / Audio)</option>
                    <option value="video">🎥 محاضرة أو شرح مرئي (Video / Link)</option>
                    <option value="image">🖼️ صورة نشاط أو بطاقة تعليمية (Image)</option>
                    <option value="worksheet">📝 ورقة عمل وتطبيق منزلي (Worksheet)</option>
                    <option value="quiz">🎯 كويز واختبار قصير (Quiz / Exam)</option>
                </select>
            </div>

            <!-- رفع الملف المرفق -->
            <div id="fileUploadContainer">
                <label class="block font-bold text-slate-700 mb-1">الملف المرفق (PDF, MP3, MP4, PNG, JPG... حتى 100MB)</label>
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
                    <input type="number" name="duration_hours" step="0.1" min="0.1" max="1000" placeholder="مثال: 2 أو 1.5 أو 40" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
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
            <input type="hidden" name="level_resource_id" id="shareResourceId">

            <div>
                <label class="block font-bold text-slate-700 mb-1">المجموعة الدراسية المستهدفة</label>
                <select name="study_group_id" id="shareGroupIdSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold" onchange="updateShareActionUrl()">
                    @foreach($studyGroups as $grp)
                        <option value="{{ $grp->id }}">{{ $grp->name }} ({{ $grp->students->count() }} طالب)</option>
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
<!-- سكربت فلترة الموارد والنوافذ المنبثقة -->
<!-- =============================================================== -->
<script>
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

    function openAddResourceModal() {
        document.getElementById('addResourceModal').classList.remove('hidden');
        toggleResourceTypeFields();
    }

    function closeAddResourceModal() {
        document.getElementById('addResourceModal').classList.add('hidden');
    }

    function openShareModal(resourceId, resourceTitle) {
        document.getElementById('shareResourceId').value = resourceId;
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
        }
    });
</script>
@endsection
