@extends('layouts.app', ['title' => 'نتيجة الاختبار: ' . $exam->title])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(!$isResultVisible)
        <!-- =============================================================== -->
        <!-- شاشة النتيجة المحجوبة (قيد المراجعة والاعتماد من الإدارة/المعلم) -->
        <!-- =============================================================== -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 sm:p-12 text-center space-y-6">
            <div class="w-20 h-20 rounded-3xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-4xl mx-auto shadow-inner">
                ⏳
            </div>

            <div class="space-y-2 max-w-lg mx-auto">
                <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200 inline-block">
                    تم استلام الإجابات بنجاح
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">
                    النتيجة قيد المراجعة والاعتماد
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    شكراً لك! لقد تم تسليم إجاباتك للاختبار بنجاح. وفقاً لسياسة إدارة الدورة، ستتم مراجعة وتدقيق إجاباتك من قبل المعلم أو إدارة المنصة، وسيتم إعلان النتيجة وإظهارها في بوابتك فور اعتمادها.
                </p>
            </div>

            <!-- بطاقة ملخص التسليم -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 max-w-md mx-auto text-xs text-right space-y-2.5">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <span class="text-slate-500 font-bold">الاختبار:</span>
                    <strong class="text-slate-900">{{ $exam->title }}</strong>
                </div>

                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <span class="text-slate-500 font-bold">المستوى والدورة:</span>
                    <span class="text-indigo-600 font-bold">{{ $exam->level?->course?->name }} • {{ $exam->level?->name }}</span>
                </div>

                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <span class="text-slate-500 font-bold">وقت التسليم:</span>
                    <span class="font-mono text-slate-700">{{ $attempt->submitted_at?->format('Y-m-d h:i A') ?? now()->format('Y-m-d h:i A') }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-bold">المجموعة:</span>
                    <span class="text-slate-800 font-bold">{{ $attempt->studyGroup?->name ?? 'مباشر' }}</span>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-center gap-3">
                <a href="{{ route('student.portal') }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                    العودة للبوابة الرئيسية للطالب
                </a>
            </div>
        </div>

    @else
        <!-- =============================================================== -->
        <!-- شاشة النتيجة المعتمدة والمعلنة للطالب -->
        <!-- =============================================================== -->
        @php
            $isPassed = $attempt->total_score >= $exam->passing_score;
            $scorePercentage = $exam->total_points > 0 ? round(($attempt->total_score / $exam->total_points) * 100, 1) : 0;
        @endphp

        <!-- ترويسة النتيجة والتقييم -->
        <div class="bg-gradient-to-r {{ $isPassed ? 'from-emerald-900 via-teal-950 to-slate-900 border-emerald-500/30' : 'from-rose-950 via-slate-900 to-slate-900 border-rose-500/30' }} rounded-3xl p-6 sm:p-10 text-white shadow-xl border">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-right">
                
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black {{ $isPassed ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }}">
                        <span>{{ $isPassed ? '🎉' : '⚠️' }}</span>
                        <span>{{ $isPassed ? 'تم اجتياز الاختبار بنجاح' : 'لم يتم اجتياز الاختبار' }}</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black">
                        {{ $exam->title }}
                    </h1>
                    <p class="text-xs text-slate-300">
                        {{ $exam->level?->course?->name }} • {{ $exam->level?->name }} • {{ $exam->quiz_period_label }}
                    </p>
                </div>

                <!-- بطاقة الدرجة والنسبة -->
                <div class="bg-white/10 backdrop-blur-md px-8 py-5 rounded-3xl border border-white/15 text-center min-w-[200px]">
                    <span class="text-xs text-slate-300 block font-bold mb-1">الدرجة النهائية</span>
                    <div class="text-4xl sm:text-5xl font-black font-mono tracking-tight {{ $isPassed ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $attempt->total_score }}
                    </div>
                    <span class="text-xs text-slate-300 block mt-1 font-mono">
                        من أصل {{ $exam->total_points }} ({{ $scorePercentage }}%)
                    </span>
                    <span class="text-[10px] text-slate-400 block mt-0.5 font-bold">
                        درجة النجاح: {{ $exam->passing_score }}
                    </span>
                </div>

            </div>
        </div>

        <!-- ملاحظات وتوجيهات المعلم والمشرف إن وجدت -->
        @if($attempt->teacher_notes)
            <div class="bg-indigo-50 border border-indigo-200 rounded-3xl p-6 text-xs text-indigo-950 space-y-2 shadow-xs">
                <div class="flex items-center gap-2 font-black text-sm text-indigo-900">
                    <span>💬</span>
                    <span>ملاحظات وتوجيهات المعلم:</span>
                </div>
                <p class="leading-relaxed bg-white/70 p-4 rounded-2xl border border-indigo-100">
                    {{ $attempt->teacher_notes }}
                </p>
                @if($attempt->reviewer)
                    <div class="text-[10px] text-indigo-600 font-bold text-left">
                        المعتمد: {{ $attempt->reviewer->name }} ({{ $attempt->reviewed_at?->format('Y-m-d') }})
                    </div>
                @endif
            </div>
        @endif

        <!-- استعراض تفاصيل الأسئلة والإجابات -->
        <div class="space-y-4">
            <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                <span>📑</span>
                <span>تفاصيل إجاباتك في الاختبار</span>
            </h3>

            @php
                $questionIds = $attempt->questions_order ?: $exam->questions->pluck('id')->all();
                $questions = \App\Models\ExamQuestion::whereIn('id', $questionIds)->get()->sortBy(function($q) use ($questionIds) {
                    return array_search($q->id, $questionIds);
                });
                $answersMap = $attempt->answers->keyBy('question_id');
            @endphp

            <div class="space-y-4">
                @foreach($questions as $idx => $q)
                    @php
                        $ans = $answersMap->get($q->id);
                        $studentChoice = $ans?->student_answer;
                        $isCorrect = (bool)$ans?->is_correct;
                        $isUnanswered = empty($studentChoice);
                    @endphp

                    <div class="bg-white rounded-3xl border {{ $isCorrect ? 'border-emerald-200' : 'border-rose-200' }} shadow-xs p-6 space-y-4">
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs font-mono">
                                    {{ $idx + 1 }}
                                </span>
                                <span class="font-black text-xs text-slate-800">
                                    {{ $q->question_text }}
                                </span>
                            </div>

                            <div>
                                @if($isCorrect)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800">
                                        <span>✓</span>
                                        <span>إجابتك صحيحة (+{{ $ans->points_awarded }})</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-rose-100 text-rose-800">
                                        <span>✕</span>
                                        <span>إجابة خاطئة (0)</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- نص القراءة إن وجد -->
                        @if($q->context_passage)
                            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-xs text-slate-700 font-serif leading-relaxed">
                                {{ $q->context_passage }}
                            </div>
                        @endif

                        <!-- خيارات الإجابة -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            @foreach($q->options ?? [] as $optIdx => $optText)
                                @php
                                    $isStudentChoice = (trim(mb_strtolower($studentChoice)) === trim(mb_strtolower($optText)));
                                    $isCorrectAnswer = (trim(mb_strtolower($q->correct_answer)) === trim(mb_strtolower($optText)));
                                    
                                    $boxStyle = 'bg-slate-50 border-slate-200 text-slate-700';
                                    if ($isCorrectAnswer && $isStudentChoice) {
                                        $boxStyle = 'bg-emerald-50 border-emerald-500 text-emerald-950 font-bold ring-2 ring-emerald-500/30';
                                    } elseif ($isStudentChoice && !$isCorrectAnswer) {
                                        $boxStyle = 'bg-rose-50 border-rose-500 text-rose-950 font-bold ring-2 ring-rose-500/30';
                                    } elseif ($isCorrectAnswer) {
                                        $boxStyle = 'bg-emerald-50/70 border-emerald-400 text-emerald-900 border-dashed font-bold';
                                    }
                                @endphp

                                <div class="p-3 rounded-2xl border flex items-center justify-between {{ $boxStyle }}">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-white border border-slate-300 flex items-center justify-center font-bold text-[11px] font-mono shrink-0">
                                            {{ chr(65 + $optIdx) }}
                                        </span>
                                        <span>{{ $optText }}</span>
                                    </div>

                                    <div class="text-[10px] font-black shrink-0">
                                        @if($isStudentChoice && $isCorrectAnswer)
                                            <span class="text-emerald-700">إجابتك (صحيحة ✓)</span>
                                        @elseif($isStudentChoice && !$isCorrectAnswer)
                                            <span class="text-rose-700">إجابتك (غير صحيحة ✕)</span>
                                        @elseif($isCorrectAnswer)
                                            <span class="text-emerald-700">الإجابة الصحيحة ✓</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        <!-- أزرار الإجراءات والعودة -->
        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('student.portal') }}" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition">
                ← العودة لبوابة الطالب
            </a>

            <button type="button" onclick="window.print()" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                <span>🖨️</span>
                <span>طباعة النتيجة</span>
            </button>
        </div>

    @endif

</div>
@endsection
