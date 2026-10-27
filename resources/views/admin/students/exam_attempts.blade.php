@extends('layouts.app', ['title' => 'سجل اختبارات ونتائج الطالب: ' . $student->name])

@section('content')
<div class="space-y-6">

    <!-- شريط الترويسة وبطاقة الطالب -->
    <div class="rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-[#004e7c]" style="background: linear-gradient(135deg, #004e7c 0%, #0066A1 60%, #002d48 100%);">
        <div>
            <div class="flex items-center gap-2 text-xs text-amber-300 font-bold mb-2">
                <a href="{{ route('admin.students.index') }}" class="hover:underline">إدارة الطلاب</a>
                <span>/</span>
                <a href="{{ route('admin.exams.all_attempts') }}" class="hover:underline">سجل نتائج الاختبارات</a>
                <span>/</span>
                <span class="text-white">{{ $student->name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black flex items-center gap-2.5">
                <span>📝</span>
                <span>سجل الاختبارات والنتائج: {{ $student->name }}</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-200 mt-1">
                الكود الأكاديمي: <span class="font-mono font-bold text-amber-300">{{ $student->student_code ?? $student->user_code }}</span> • البريد: {{ $student->email }} • الهاتف: {{ $student->phone_whatsapp ?? 'غير مسجل' }}
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.exams.all_attempts') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 border border-white/15">
                <span>←</span>
                <span>سجل كافة الاختبارات</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl text-xs font-black shadow-md transition flex items-center gap-1.5">
                <span>👥</span>
                <span>العودة للطلاب</span>
            </a>
        </div>
    </div>

    <!-- بطاقات المؤشرات الأكاديمية للطالب -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-xs font-bold text-slate-500 block">إجمالي الاختبارات المكتملة</span>
            <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $totalAttempts }}</div>
            <span class="text-[10px] text-slate-400 block">تحديد مستوى وكويزات وامتحانات</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-xs font-bold text-emerald-600 block">الاختبارات المجتازة</span>
            <div class="text-2xl sm:text-3xl font-black text-emerald-700 font-mono">{{ $passedCount }}</div>
            <span class="text-[10px] text-emerald-500 block">حقق درجة النجاح فأعلى</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-xs font-bold text-indigo-600 block">نسبة النجاح العامة</span>
            <div class="text-2xl sm:text-3xl font-black text-indigo-700 font-mono">{{ $passRate }}%</div>
            <span class="text-[10px] text-indigo-500 block">معدل الاجتياز من المحاولات</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-xs font-bold text-[#F58220] block">متوسط الدرجات المحققة</span>
            <div class="text-2xl sm:text-3xl font-black text-[#F58220] font-mono">{{ $avgScore }}</div>
            <span class="text-[10px] text-slate-400 block">نقطة في كافة الاختبارات</span>
        </div>
    </div>

    <!-- شريط تصفية نوع الاختبار -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between flex-wrap gap-2 text-xs">
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="font-bold text-slate-500 ml-2">نوع الاختبار:</span>
            <a href="{{ route('admin.students.exam_attempts', $student->id) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ !request('quiz_period') || request('quiz_period') === 'all' ? 'bg-[#0066A1] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                الكل ({{ $attempts->count() }})
            </a>
            <a href="{{ route('admin.students.exam_attempts', ['studentId' => $student->id, 'quiz_period' => 'placement']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ request('quiz_period') === 'placement' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                🎯 تحديد المستوى
            </a>
            <a href="{{ route('admin.students.exam_attempts', ['studentId' => $student->id, 'quiz_period' => 'weekly']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ request('quiz_period') === 'weekly' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                🟣 كويزات أسبوعية
            </a>
            <a href="{{ route('admin.students.exam_attempts', ['studentId' => $student->id, 'quiz_period' => 'midterm']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ request('quiz_period') === 'midterm' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                🔵 اختبارات نصفية
            </a>
            <a href="{{ route('admin.students.exam_attempts', ['studentId' => $student->id, 'quiz_period' => 'final']) }}" class="px-3 py-1.5 rounded-xl font-bold transition {{ request('quiz_period') === 'final' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                🔴 اختبارات نهائية
            </a>
        </div>
    </div>

    <!-- بطاقات وقائمة محاولات الطالب -->
    <div class="space-y-4">
        @forelse($attempts as $att)
            @php
                $exam = $att->exam;
                $isPlacement = ($exam?->type === 'placement_test' || $exam?->quiz_period === 'placement');
                $totalPoints = ($exam?->total_points > 0) ? $exam->total_points : 100;
                $scorePercent = $totalPoints > 0 ? round(($att->total_score / $totalPoints) * 100, 1) : 0;
                $isPassed = $att->total_score >= ($exam?->passing_score ?? 50);
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:border-indigo-300 transition space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">
                                {{ $isPlacement ? '🎯' : ($exam?->quiz_period === 'final' ? '🔴' : ($exam?->quiz_period === 'midterm' ? '🔵' : '🟣')) }}
                            </span>
                            <h3 class="font-black text-base text-slate-900">{{ $exam?->title }}</h3>
                        </div>
                        <p class="text-xs text-slate-500">
                            {{ $exam?->course?->displayName ?: $exam?->course?->name }} • {{ $exam?->level?->name }} • {{ $att->studyGroup?->name ?? 'مباشر' }}
                        </p>
                    </div>

                    <!-- أزرار الإجراءات السريعة للمحاولة -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('admin.exams.review_attempt', $att->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <span>🔍</span>
                            <span>استعراض وتدقيق الإجابات</span>
                        </a>

                        <a href="{{ route('admin.exams.attempts.error_report', $att->id) }}" target="_blank" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <span>🖨️</span>
                            <span>كشف أخطاء وتقرير الاختبار</span>
                        </a>
                    </div>
                </div>

                <!-- شبكة ملخص نتائج الاختبار -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[11px]">الدرجة المحققة:</span>
                        <div class="text-base font-black font-mono {{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $att->total_score }} <span class="text-xs text-slate-400 font-normal">/ {{ $totalPoints }}</span>
                            <span class="text-xs font-bold text-slate-500">({{ $scorePercent }}%)</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[11px]">النتيجة الأكاديمية:</span>
                        @if($isPlacement)
                            <strong class="text-indigo-600 font-bold block mt-0.5">
                                المقترح: {{ $att->suggestedLevel?->name ?? 'المستوى التأسيسي' }}
                            </strong>
                        @elseif($isPassed)
                            <span class="text-emerald-700 font-black block mt-0.5">✓ اجتاز بنجاح (ناجح)</span>
                        @else
                            <span class="text-rose-700 font-black block mt-0.5">✕ لم يجتز (راسب)</span>
                        @endif
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[11px]">حالة إعلان النتيجة:</span>
                        @if($att->is_published)
                            <span class="text-teal-700 font-bold block mt-0.5">✅ معلنة للطالب</span>
                        @else
                            <span class="text-amber-700 font-bold block mt-0.5">🔒 محجوبة بانتظار الاعتماد</span>
                        @endif
                    </div>

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block text-[11px]">تاريخ ووقت الأداء:</span>
                        <span class="font-mono text-slate-700 font-bold block mt-0.5">
                            {{ $att->submitted_at?->format('Y-m-d h:i A') ?? $att->created_at->format('Y-m-d h:i A') }}
                        </span>
                    </div>
                </div>

                <!-- ملاحظات المعلم والمشرف إن وجدت -->
                @if($att->teacher_notes)
                    <div class="p-3 bg-indigo-50/60 border border-indigo-100 rounded-xl text-xs text-indigo-950 flex items-start gap-2">
                        <span>💬</span>
                        <div>
                            <span class="font-black block text-indigo-900">ملاحظات المعلم / المشرف:</span>
                            <p class="leading-relaxed mt-0.5 text-slate-700">{{ $att->teacher_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-dashed border-slate-200 p-12 text-center text-slate-400 space-y-2">
                <span class="text-4xl block">📭</span>
                <p class="text-xs font-bold">لا توجد اختبارات أو محاولات مسجلة لهذا الطالب حتى الآن.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
