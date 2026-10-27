@extends('layouts.app', ['title' => 'سجل نتائج ومحاولات الاختبارات والكويزات الشامل'])

@section('content')
<div class="space-y-6">

    <!-- شريط الترويسة الرئيسي المتناسق مع هوية المنصة -->
    <div class="rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-[#004e7c]" style="background: linear-gradient(135deg, #004e7c 0%, #0066A1 60%, #002d48 100%);">
        <div>
            <div class="flex items-center gap-2 text-xs text-amber-300 font-bold mb-2">
                <a href="{{ route('admin.exams.index') }}" class="hover:underline">بنك الاختبارات والكويزات</a>
                <span>/</span>
                <span class="text-white">سجل النتائج والمحاولات</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black flex items-center gap-2.5">
                <span>📑</span>
                <span>سجل نتائج ومحاولات الاختبارات الشامل</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-200 mt-1 max-w-3xl leading-relaxed">
                استعراض ومراجعة إجابات الطلاب بالتفصيل لكافة الاختبارات (تحديد المستوى، الكويزات الأسبوعية، الاختبارات النصفية والنهائية)، تدقيق الأخطاء، وطباعة تقارير الشفافية الأكاديمية حتى بعد إصدار النتائج.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.exams.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 border border-white/15">
                <span>🎯</span>
                <span>بنك الاختبارات</span>
            </a>
            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('students.view'))
                <a href="{{ route('admin.students.index') }}" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl text-xs font-black shadow-md transition flex items-center gap-1.5">
                    <span>👥</span>
                    <span>قائمة الطلاب</span>
                </a>
            @endif
        </div>
    </div>

    <!-- بطاقات الإحصائيات العامة للمحاولات -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-[11px] font-bold text-slate-500 block">إجمالي المحاولات المسجلة</span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $totalAttempts }}</div>
            <span class="text-[10px] text-slate-400 block">كافة الطلاب والاختبارات</span>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-[11px] font-bold text-indigo-600 block">اختبارات تحديد المستوى</span>
            <div class="text-2xl sm:text-3xl font-black text-indigo-700 font-mono">{{ $placementCount }}</div>
            <span class="text-[10px] text-slate-400 block">اختبارات القبول وتحديد المستوى</span>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-[11px] font-bold text-emerald-600 block">نتائج معلنة ومعتمدة</span>
            <div class="text-2xl sm:text-3xl font-black text-emerald-700 font-mono">{{ $publishedCount }}</div>
            <span class="text-[10px] text-emerald-500 block">متاحة ومصدرة للطلاب</span>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-[11px] font-bold text-amber-600 block">بانتظار الاعتماد والمراجعة</span>
            <div class="text-2xl sm:text-3xl font-black text-amber-700 font-mono">{{ $pendingPublishCount }}</div>
            <span class="text-[10px] text-amber-500 block">محجوبة لحين المراجعة</span>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1 col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold text-slate-500 block">متوسط الدرجات العام</span>
            <div class="text-2xl sm:text-3xl font-black text-[#0066A1] font-mono">{{ $avgScore }}</div>
            <span class="text-[10px] text-slate-400 block">نقطة عبر كافة الاختبارات</span>
        </div>
    </div>

    <!-- شريط البحث والفلترة المتقدمة -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.exams.all_attempts') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            
            <!-- البحث النصي -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">بحث بالطالب أو الاختبار:</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الطالب، كود، بريد، أو عنوان الاختبار..." class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-1 focus:ring-indigo-500 outline-none text-xs">
            </div>

            <!-- نوع / فترة الاختبار -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">نوع الاختبار / الفترة:</label>
                <select name="quiz_period" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-1 focus:ring-indigo-500 outline-none bg-white text-xs font-bold">
                    <option value="all">الكل (كافة الأنواع والفترات)</option>
                    <option value="placement" {{ request('quiz_period') === 'placement' ? 'selected' : '' }}>🎯 اختبار تحديد المستوى والقبول</option>
                    <option value="weekly" {{ request('quiz_period') === 'weekly' ? 'selected' : '' }}>🟣 كويز أسبوعي</option>
                    <option value="midterm" {{ request('quiz_period') === 'midterm' ? 'selected' : '' }}>🔵 اختبار منتصف الدورة (نصفي)</option>
                    <option value="final" {{ request('quiz_period') === 'final' ? 'selected' : '' }}>🔴 اختبار نهاية الدورة (نهائي)</option>
                    <option value="pop_quiz" {{ request('quiz_period') === 'pop_quiz' ? 'selected' : '' }}>🟡 كويز مفاجئ / قصير</option>
                </select>
            </div>

            <!-- حالة النتيجة -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">حالة النتيجة:</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-1 focus:ring-indigo-500 outline-none bg-white text-xs font-bold">
                    <option value="all">كافة الحالات</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>✅ معتمدة ومعلنة للطالب</option>
                    <option value="pending_publish" {{ request('status') === 'pending_publish' ? 'selected' : '' }}>🔒 محجوبة بانتظار الاعتماد</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>✍️ تمت مراجعتها</option>
                </select>
            </div>

            <!-- الدورة والمستوى -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">المستوى التعليمي:</label>
                <select name="level_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-1 focus:ring-indigo-500 outline-none bg-white text-xs">
                    <option value="">كافة المستويات</option>
                    @foreach($levels as $lvl)
                        <option value="{{ $lvl->id }}" {{ request('level_id') == $lvl->id ? 'selected' : '' }}>
                            {{ $lvl->course?->displayName ?: $lvl->course?->name }} • {{ $lvl->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- أزرار التطبيق والتفريغ -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-xs text-xs">
                    تصفية النتائج 🔍
                </button>
                <a href="{{ route('admin.exams.all_attempts') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition text-xs">
                    تفريغ
                </a>
            </div>

        </form>
    </div>

    <!-- جدول محاولات ونتائج الاختبارات -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-sm font-black text-slate-900 flex items-center gap-2">
                <span>📋</span>
                <span>قائمة نتائج ومحاولات الطلاب ({{ $attempts->total() }} محاولة)</span>
            </h2>
            <span class="text-xs text-slate-400">
                يتم حفظ وتوثيق إجابات الطالب بالكامل حتى بعد اعتماد ونشر النتيجة لضمان الشفافية.
            </span>
        </div>

        @if($attempts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50/80 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">الطالب</th>
                            <th class="py-3 px-4">الاختبار والنوع</th>
                            <th class="py-3 px-4">المستوى / المجموعة</th>
                            <th class="py-3 px-4 text-center">الدرجة المحققة</th>
                            <th class="py-3 px-4 text-center">التقييم / المستوى المقترح</th>
                            <th class="py-3 px-4 text-center">حالة النتيجة</th>
                            <th class="py-3 px-4">تاريخ التسليم</th>
                            <th class="py-3 px-4 text-center">الإجراءات والتقارير</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($attempts as $att)
                            @php
                                $exam = $att->exam;
                                $user = $att->user;
                                $isPlacement = ($exam?->type === 'placement_test' || $exam?->quiz_period === 'placement');
                                $totalPoints = ($exam?->total_points > 0) ? $exam->total_points : 100;
                                $scorePercent = $totalPoints > 0 ? round(($att->total_score / $totalPoints) * 100, 1) : 0;
                                $isPassed = $att->total_score >= ($exam?->passing_score ?? 50);
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- الطالب -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ mb_substr($user?->name ?? 'ط', 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.students.exam_attempts', $user?->id ?? 0) }}" class="font-black text-slate-900 hover:text-indigo-600 transition block">
                                                {{ $user?->name ?? 'طالب غير محدد' }}
                                            </a>
                                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-mono">
                                                <span>{{ $user?->student_code ?? $user?->user_code }}</span>
                                                @if($user?->phone_whatsapp)
                                                    <span>•</span>
                                                    <span>{{ $user->phone_whatsapp }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- الاختبار والنوع -->
                                <td class="py-3 px-4">
                                    <div class="space-y-1">
                                        <span class="font-bold text-slate-800 block">{{ $exam?->title ?? 'اختبار محذوف' }}</span>
                                        <div>
                                            @if($isPlacement)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                    🎯 تحديد مستوى
                                                </span>
                                            @elseif($exam?->quiz_period === 'weekly')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                                    كويز أسبوعي
                                                </span>
                                            @elseif($exam?->quiz_period === 'midterm')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                    اختبار نصفي
                                                </span>
                                            @elseif($exam?->quiz_period === 'final')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                    اختبار نهائي
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                    كويز تدريبي
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- المستوى / المجموعة -->
                                <td class="py-3 px-4">
                                    <div class="text-[11px] space-y-0.5">
                                        <span class="font-bold text-slate-700 block">
                                            {{ $exam?->level?->name ?? ($att->suggestedLevel?->name ?? 'عام') }}
                                        </span>
                                        <span class="text-slate-400 text-[10px] block">
                                            {{ $att->studyGroup?->name ?? 'بدون شعبة / مباشر' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- الدرجة المحققة -->
                                <td class="py-3 px-4 text-center">
                                    <div class="inline-block font-mono">
                                        <div class="text-sm font-black {{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $att->total_score }} <span class="text-[11px] text-slate-400 font-normal">/ {{ $totalPoints }}</span>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-400">({{ $scorePercent }}%)</span>
                                    </div>
                                </td>

                                <!-- التقييم / المستوى المقترح -->
                                <td class="py-3 px-4 text-center">
                                    @if($isPlacement)
                                        <div class="space-y-0.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                المقترح: {{ $att->suggestedLevel?->name ?? 'المستوى التأسيسي' }}
                                            </span>
                                            @if($att->speech_score > 0)
                                                <span class="block text-[9px] text-indigo-600 font-bold font-mono">🎙️ نطق AI: {{ round($att->speech_score, 1) }}</span>
                                            @endif
                                        </div>
                                    @elseif($isPassed)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                            <span>✓</span>
                                            <span>اجتاز بنجاح</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800">
                                            <span>✕</span>
                                            <span>لم يجتز</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- حالة النتيجة -->
                                <td class="py-3 px-4 text-center">
                                    @if($att->is_published)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                            <span>✅</span>
                                            <span>معلنة للطالب</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span>🔒</span>
                                            <span>بانتظار الاعتماد</span>
                                        </span>
                                    @endif
                                    @if($att->reviewer)
                                        <span class="block text-[9px] text-slate-400 mt-0.5">بواسطة: {{ $att->reviewer->name }}</span>
                                    @endif
                                </td>

                                <!-- تاريخ التسليم -->
                                <td class="py-3 px-4">
                                    <span class="font-mono text-[11px] text-slate-600 block">
                                        {{ $att->submitted_at?->format('Y-m-d') ?? $att->created_at->format('Y-m-d') }}
                                    </span>
                                    <span class="font-mono text-[10px] text-slate-400 block">
                                        {{ $att->submitted_at?->format('h:i A') ?? $att->created_at->format('h:i A') }}
                                    </span>
                                </td>

                                <!-- الإجراءات والتقارير -->
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <!-- زر تدقيق ومراجعة الإجابات الكاملة -->
                                        <a href="{{ route('admin.exams.review_attempt', $att->id) }}" 
                                           class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-[11px] font-bold transition flex items-center gap-1"
                                           title="استعراض وتدقيق الإجابات والدرجة">
                                            <span>🔍</span>
                                            <span>مراجعة الإجابات</span>
                                        </a>

                                        <!-- زر كشف أخطاء الطالب للطباعة والشفافية -->
                                        <a href="{{ route('admin.exams.attempts.error_report', $att->id) }}" target="_blank"
                                           class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-lg text-[11px] font-bold transition flex items-center gap-1"
                                           title="طباعة كشف مراجعة الأخطاء والشفافية للطالب">
                                            <span>🖨️</span>
                                            <span>تقرير الأخطاء</span>
                                        </a>

                                        <!-- زر سجل الطالب كاملاً -->
                                        <a href="{{ route('admin.students.exam_attempts', $user?->id ?? 0) }}"
                                           class="p-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] transition"
                                           title="سجل كافة اختبارات هذا الطالب">
                                            <span>👤</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- الترقيم والصفحات -->
            <div class="p-4 border-t border-slate-100">
                {{ $attempts->links() }}
            </div>
        @else
            <div class="p-12 text-center text-slate-400 space-y-2">
                <span class="text-4xl block">📭</span>
                <p class="text-xs font-bold">لا توجد محاولات أو نتائج مطابقة لمعايير البحث الحالية.</p>
            </div>
        @endif

    </div>

</div>
@endsection
