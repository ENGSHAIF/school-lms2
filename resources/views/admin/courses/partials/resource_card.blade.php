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
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $res->target_audience_badge_class }}">
                {{ $res->target_audience_label }}
            </span>
            @if($res->is_general)
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                    🏛️ مورد عام ثابت للدورة
                </span>
            @endif
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
                    <span>استماع للتسجيل الصوتي:</span>
                </span>
                <audio controls preload="metadata" class="w-full h-8">
                    <source src="{{ route('resources.stream', $res->id) }}" type="audio/mpeg">
                    متصفحك لا يدعم مشغل الصوت.
                </audio>
            </div>
        @endif

        <!-- مشغل الفيديو ومعاينة المحاضرة ورابط التحميل (إذا كان فيديو) -->
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

                @if($res->file_path)
                    <div class="rounded-xl overflow-hidden bg-black/95">
                        <video controls preload="metadata" class="w-full max-h-48 object-cover">
                            <source src="{{ route('resources.stream', $res->id) }}">
                            متصفحك لا يدعم تشغيل هذا الفيديو.
                        </video>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('resources.stream', $res->id) }}" target="_blank" class="flex-1 py-1.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                            <span>▶️</span>
                            <span>فتح بالكامل</span>
                        </a>
                        <a href="{{ route('resources.download', $res->id) }}" download target="_blank" class="py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-2xs" title="تحميل الفيديو لجهازك">
                            <span>⬇️</span>
                            <span>تحميل</span>
                        </a>
                    </div>
                @elseif($res->external_url)
                    <a href="{{ $res->external_url }}" target="_blank" class="w-full py-1.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                        <span>▶️</span>
                        <span>مشاهدة فيديو الشرح والمحاضرة</span>
                    </a>
                @endif
            </div>
        @endif

        <!-- صور وبطاقات الأنشطة (معاينة وتحميل) -->
        @if($res->type === 'image' && $res->file_path)
            <div class="bg-pink-50/70 p-3 rounded-2xl border border-pink-200 space-y-2">
                <a href="{{ route('resources.stream', $res->id) }}" target="_blank" class="block relative rounded-xl overflow-hidden group/img aspect-video bg-slate-900 border border-pink-200">
                    <img src="{{ route('resources.stream', $res->id) }}" alt="{{ $res->title }}" class="w-full h-full object-cover group-hover/img:scale-105 transition duration-300">
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
        <div class="flex items-center gap-1.5 flex-wrap">
            @if($res->file_path)
                <a href="{{ route('resources.download', $res->id) }}" download class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-slate-700 font-bold text-xs transition shadow-2xs flex items-center gap-1" title="تحميل الملف لجهازك">
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
            @if(isset($studyGroups) && $studyGroups->count() > 0)
                <button type="button" onclick="openShareModal({{ $res->id }}, '{{ addslashes($res->title) }}')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1" title="مشاركة هذا المورد لمجموعة معينة لتظهر في جدولهم وبوابتهم">
                    <span>📢</span>
                    <span>مشاركة</span>
                </button>
            @endif
        </div>

        <div class="flex items-center gap-1">
            <!-- زر تعديل المورد -->
            <button type="button" onclick="openEditResourceModal({{ json_encode([
                'id' => $res->id,
                'title' => $res->title,
                'type' => $res->type,
                'is_general' => (bool)$res->is_general,
                'target_audience' => $res->target_audience ?? 'all',
                'course_section_id' => $res->course_section_id,
                'description' => $res->description,
                'external_url' => $res->external_url,
                'duration_hours' => $res->duration_hours,
                'duration_minutes' => $res->duration_minutes,
                'order_index' => $res->order_index,
                'is_active' => (bool)$res->is_active,
            ]) }})" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition text-xs font-bold" title="تعديل بيانات المورد">
                ✏️
            </button>

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

</div>
