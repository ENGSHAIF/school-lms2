@extends('layouts.app', ['title' => 'نتائج ومحاولات: ' . $exam->title])

@section('content')
<div class="space-y-6">

    <!-- شريط الترويسة الرئيسي -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-indigo-900/50">
        <div>
            <div class="flex items-center gap-2 text-xs text-amber-400 font-bold mb-2">
                <a href="{{ route('admin.exams.index') }}" class="hover:underline">بنك الاختبارات والكويزات</a>
                <span>/</span>
                @if($exam->level)
                    <a href="{{ route('admin.levels.resources', $exam->level_id) }}" class="hover:underline">{{ $exam->level->name }}</a>
                    <span>/</span>
                @endif
                <span class="text-white">نتائج الطلاب</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black flex items-center gap-2.5">
                <span>📊</span>
                <span>نتائج ومحاولات: {{ $exam->title }}</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                مراجعة إجابات الطلاب التفصيلية، تدقيق الدرجات، وإصدار واعتماد النتائج لإظهارها للطلاب في بواباتهم.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap w-full md:w-auto">
            <a href="{{ route('admin.exams.edit', $exam->id) }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <span>✏️</span>
                <span>إدارة بنك الأسئلة</span>
            </a>

            @if($pendingPublishCount > 0)
                <form action="{{ route('admin.exams.publish_all', $exam->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من اعتماد وإصدار كافة النتائج المعلقة ({{ $pendingPublishCount }} نتيجة) وإظهارها للطلاب فوراً؟');">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                        <span>📢</span>
                        <span>اعتماد وإصدار كافة النتائج ({{ $pendingPublishCount }})</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- شريط الإحصائيات العامة للاختبار -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">إجمالي المختبرين</span>
            <span class="text-2xl font-black text-slate-900 font-mono">{{ $totalAttempts }}</span>
            <span class="text-[10px] text-slate-500 block">طالب مسلّم</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">نسبة النجاح العامة</span>
            <span class="text-2xl font-black text-emerald-600 font-mono">{{ $passRate }}%</span>
            <span class="text-[10px] text-slate-500 block">درجة النجاح: {{ $exam->passing_score }}</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">متوسط الدرجات</span>
            <span class="text-2xl font-black text-indigo-600 font-mono">{{ $avgScore }}</span>
            <span class="text-[10px] text-slate-500 block">من إجمالي {{ $exam->total_points }} درجة</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">نتائج معلنة للطلاب</span>
            <span class="text-2xl font-black text-teal-600 font-mono">{{ $publishedCount }}</span>
            <span class="text-[10px] text-teal-700 font-bold block">متاحة في بوابة الطالب</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs text-center space-y-1">
            <span class="text-xs text-slate-400 block font-bold">قيد المراجعة / معلقة</span>
            <span class="text-2xl font-black text-amber-600 font-mono">{{ $pendingPublishCount }}</span>
            <span class="text-[10px] text-amber-700 font-bold block">محجوبة عن الطالب</span>
        </div>
    </div>

    <!-- بطاقة معلومات وسياسات الاختبار -->
    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="font-bold text-slate-700">نوع الكويز:</span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $exam->quiz_period_badge }}">{{ $exam->quiz_period_label }}</span>
            
            <span class="text-slate-300">•</span>
            <span class="font-bold text-slate-700">المدة الزمنية:</span>
            <span class="font-mono font-black text-slate-900">{{ $exam->duration_minutes }} دقيقة</span>

            <span class="text-slate-300">•</span>
            <span class="font-bold text-slate-700">سياسة ظهور النتيجة:</span>
            @if($exam->show_result_immediately)
                <span class="text-blue-700 font-bold bg-blue-100/70 px-2 py-0.5 rounded-full text-[10px]">⚡ ظهور فوري للطالب بعد التسليم</span>
            @else
                <span class="text-amber-800 font-bold bg-amber-100/70 px-2 py-0.5 rounded-full text-[10px]">🔒 محجوبة بانتظار مراجعة واعتماد الإدارة/المعلم</span>
            @endif
        </div>

        <div class="text-[11px] text-slate-500 font-medium">
            مستوى الاختبار: <strong class="text-indigo-600">{{ $exam->level?->name }}</strong> ({{ $exam->level?->course?->name }})
        </div>
    </div>

    <!-- شريط الفلترة والبحث في المحاولات -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.exams.attempts', $exam->id) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            
            <!-- فلترة المجموعة -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">المجموعة الدراسية:</label>
                <select name="study_group_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs bg-white font-medium" onchange="this.form.submit()">
                    <option value="">جميع المجموعات</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp->id }}" {{ request('study_group_id') == $grp->id ? 'selected' : '' }}>
                            {{ $grp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- حالة النتيجة والاعتماد -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">حالة ظهور النتيجة:</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs bg-white font-medium" onchange="this.form.submit()">
                    <option value="all">جميع الحالات</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>✅ معلنة ومصرحة للطالب</option>
                    <option value="pending_publish" {{ request('status') === 'pending_publish' ? 'selected' : '' }}>⏳ معلقة بانتظار الاعتماد</option>
                </select>
            </div>

            <!-- البحث باسم الطالب -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">اسم الطالب أو بريده:</label>
                <div class="flex items-center gap-1.5">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطالب..." class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs font-medium">
                    <button type="submit" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">
                        🔍
                    </button>
                </div>
            </div>

            <div class="flex items-end">
                <a href="{{ route('admin.exams.attempts', $exam->id) }}" class="w-full py-2 px-3 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    إعادة ضبط الفلترة
                </a>
            </div>
        </form>
    </div>

    <!-- جدول المحاولات والنتائج -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-black border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">الطالب</th>
                        <th class="py-3.5 px-4">المجموعة الدراسية</th>
                        <th class="py-3.5 px-4">وقت التسليم</th>
                        <th class="py-3.5 px-4 text-center">الدرجة المحرزة</th>
                        <th class="py-3.5 px-4 text-center">الحالة الأكاديمية</th>
                        <th class="py-3.5 px-4 text-center">حالة النتيجة</th>
                        <th class="py-3.5 px-4">المراجع / المعتمد</th>
                        <th class="py-3.5 px-4 text-center">الإجراءات والاعتماد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attempts as $index => $att)
                        @php
                            $isPassed = $att->total_score >= $exam->passing_score;
                            $scorePercentage = $exam->total_points > 0 ? round(($att->total_score / $exam->total_points) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono text-slate-400">
                                {{ $attempts->firstItem() + $index }}
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-xs">{{ $att->user?->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $att->user?->email }}</div>
                            </td>

                            <td class="py-3.5 px-4 font-medium text-slate-700">
                                {{ $att->studyGroup?->name ?? 'مباشر (بدون مجموعة)' }}
                            </td>

                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">
                                @if($att->submitted_at)
                                    <div>{{ $att->submitted_at->format('Y-m-d') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $att->submitted_at->format('h:i A') }}</div>
                                @else
                                    <span class="text-amber-600 font-bold">قيد الاختبار ⏱️</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center font-mono font-black text-sm">
                                <span class="{{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $att->total_score }}
                                </span>
                                <span class="text-slate-400 text-xs font-normal">/ {{ $exam->total_points }}</span>
                                <span class="text-[10px] text-slate-400 block font-normal">({{ $scorePercentage }}%)</span>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($isPassed)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span>✓</span>
                                        <span>ناجح</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-200">
                                        <span>✕</span>
                                        <span>راسب</span>
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($att->is_published)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-teal-100 text-teal-800 border border-teal-200">
                                        <span>👁️</span>
                                        <span>معلنة للطالب</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-200">
                                        <span>🔒</span>
                                        <span>محجوبة (قيد المراجعة)</span>
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-[11px]">
                                @if($att->reviewer)
                                    <span class="font-bold text-slate-700 block">{{ $att->reviewer->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $att->reviewed_at?->format('Y-m-d') }}</span>
                                @else
                                    <span class="text-slate-400 text-[10px]">تصحيح آلي</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <!-- زر مراجعة إجابات الطالب بالتفصيل -->
                                    <a href="{{ route('admin.exams.review_attempt', $att->id) }}" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-black text-xs transition flex items-center gap-1">
                                        <span>🔍</span>
                                        <span>مراجعة الإجابات</span>
                                    </a>

                                    <!-- زر اعتماد النتيجة السريع إن لم تكن معلنة -->
                                    @if(!$att->is_published)
                                        <form action="{{ route('admin.exams.publish_result', $att->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition flex items-center gap-1" title="إصدار النتيجة وإظهارها للطالب">
                                                <span>✓</span>
                                                <span>اعتماد النتيجة</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <span class="text-3xl block mb-2">📭</span>
                                <span class="font-bold text-slate-600 block">لا توجد محاولات تسليم مسجلة لهذا الاختبار حتى الآن</span>
                                <span class="text-xs text-slate-400 block mt-1">عندما يؤدي الطلاب الاختبار من بواباتهم ستظهر إجاباتهم ودرجاتهم هنا فوراً.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $attempts->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
