@extends('layouts.app', ['title' => 'مراجعة إجابات الطالب: ' . $attempt->user->name])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- شريط الترويسة الرئيسي والرجوع -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-indigo-900/50">
        <div>
            <div class="flex items-center gap-2 text-xs text-amber-400 font-bold mb-2">
                <a href="{{ route('admin.exams.attempts', $exam->id) }}" class="hover:underline flex items-center gap-1">
                    <span>←</span>
                    <span>الرجوع لقائمة نتائج الاختبار ({{ $exam->title }})</span>
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black flex items-center gap-2.5">
                <span>🔍</span>
                <span>مراجعة وتدقيق إجابات الطالب</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                استعراض إجابات الطالب بالتفصيل، مقارنتها بالإجابات الصحيحة النموذجية، وتعديل الدرجة واعتماد النتيجة.
            </p>
        </div>

        <a href="{{ route('admin.exams.attempts', $exam->id) }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition">
            عودة للنتائج
        </a>
    </div>

    <!-- بطاقة ملخص الطالب والنتيجة العامة -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
            
            <!-- معلومات الطالب -->
            <div class="md:col-span-2 space-y-2 border-b md:border-b-0 md:border-l border-slate-100 pb-4 md:pb-0 md:pl-6">
                <div class="flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-black text-xl">
                        👤
                    </span>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">{{ $attempt->user->name }}</h2>
                        <span class="text-xs text-slate-400 font-mono">{{ $attempt->user->email }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 pt-2">
                    <div>
                        <span class="text-slate-400 block text-[10px]">المجموعة الدراسية:</span>
                        <strong class="text-slate-800">{{ $attempt->studyGroup?->name ?? 'بدون مجموعة' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">المستوى والكتاب:</span>
                        <strong class="text-indigo-600">{{ $exam->level?->name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">تاريخ ووقت التسليم:</span>
                        <span class="font-mono text-slate-700">{{ $attempt->submitted_at?->format('Y-m-d h:i A') ?? 'قيد الأداء' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">حالة النتيجة الحالية:</span>
                        @if($attempt->is_published)
                            <span class="text-teal-700 font-bold bg-teal-50 border border-teal-200 px-2 py-0.5 rounded-full text-[10px]">
                                ✅ معلنة للطالب
                            </span>
                        @else
                            <span class="text-amber-700 font-bold bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full text-[10px]">
                                🔒 محجوبة بانتظار الاعتماد
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- بطاقة الدرجة والنسبة المئوية -->
            @php
                $isPassed = $attempt->total_score >= $exam->passing_score;
                $scorePercentage = $exam->total_points > 0 ? round(($attempt->total_score / $exam->total_points) * 100, 1) : 0;
            @endphp
            <div class="md:col-span-2 flex flex-col sm:flex-row items-center justify-around gap-4 text-center bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <div>
                    <span class="text-xs text-slate-400 block font-bold mb-1">الدرجة المحرزة</span>
                    <div class="text-3xl font-black font-mono {{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $attempt->total_score }}
                        <span class="text-sm text-slate-400 font-normal">/ {{ $exam->total_points }}</span>
                    </div>
                    <span class="text-xs font-bold font-mono text-slate-500 mt-0.5 block">({{ $scorePercentage }}%)</span>
                </div>

                <div class="border-t sm:border-t-0 sm:border-r border-slate-200 pt-3 sm:pt-0 sm:pr-6">
                    <span class="text-xs text-slate-400 block font-bold mb-1">التقييم الأكاديمي</span>
                    @if($isPassed)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span>✓</span>
                            <span>اجتاز بنجاح (ناجح)</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-200">
                            <span>✕</span>
                            <span>لم يجتز (راسب)</span>
                        </span>
                    @endif
                    <span class="text-[10px] text-slate-400 block mt-1">درجة النجاح المطلوبة: {{ $exam->passing_score }}</span>
                </div>
            </div>

        </div>
    </div>

    <!-- نموذج اعتماد النتيجة وتعديل الدرجات وملاحظات المعلم -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h3 class="font-black text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
            <span>📝</span>
            <span>اعتماد النتيجة وإصدارها للطالب / كتابة ملاحظات المعلم</span>
        </h3>

        <form action="{{ route('admin.exams.publish_result', $attempt->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الدرجة النهائية المعتمدة <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.5" min="0" max="{{ $exam->total_points ?: 100 }}" name="adjusted_score" value="{{ old('adjusted_score', $attempt->total_score) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-sm font-bold">
                    <span class="text-[10px] text-slate-400 mt-1 block">يمكنك تعديل الدرجة في حال وجود أسئلة تتطلب تقديراً خاصاً.</span>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">ملاحظات المعلم / المشرف للطالب (تظهر في شهادة وكشف نتيجة الطالب):</label>
                    <textarea name="teacher_notes" rows="2" placeholder="اكتب ملاحظاتك التشجيعية أو التوجيهية للطالب هنا..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">{{ old('teacher_notes', $attempt->teacher_notes) }}</textarea>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between flex-wrap gap-3">
                <div class="text-[11px] text-slate-500">
                    @if($attempt->reviewer)
                        <span>تمت المراجعة والاعتماد بواسطة: <strong class="text-slate-800">{{ $attempt->reviewer->name }}</strong> بتاريخ {{ $attempt->reviewed_at?->format('Y-m-d') }}</span>
                    @else
                        <span>لم يتم اعتماد وإعلان هذه النتيجة رسمياً للطالب بعد.</span>
                    @endif
                </div>

                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                    <span>📢</span>
                    <span>اعتماد وإصدار النتيجة للطالب الآن</span>
                </button>
            </div>
        </form>
    </div>

    <!-- التفصيل الكامل لكل سؤال وإجابة الطالب -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                <span>📑</span>
                <span>تفاصيل الأسئلة وإجابات الطالب (سؤال بسؤال)</span>
            </h3>
            <span class="text-xs text-slate-400 font-bold">
                إجمالي الأسئلة في هذا الاختبار: {{ count($attempt->questions_order ?: $exam->questions) }}
            </span>
        </div>

        @php
            // استخراج الأسئلة بالترتيب الذي ظهر للطالب
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

                <div class="bg-white rounded-3xl border {{ $isCorrect ? 'border-emerald-200' : ($isUnanswered ? 'border-amber-200' : 'border-rose-200') }} shadow-xs p-6 space-y-4">
                    
                    <!-- ترويسة السؤال والدرجة المحرزة -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs font-mono">
                                {{ $idx + 1 }}
                            </span>
                            <span class="font-bold text-xs text-slate-500">
                                درجة السؤال: <strong class="text-slate-800 font-mono">{{ $q->points }}</strong>
                            </span>
                        </div>

                        <div>
                            @if($isCorrect)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <span>✓</span>
                                    <span>إجابة صحيحة (+{{ $ans->points_awarded }} درجة)</span>
                                </span>
                            @elseif($isUnanswered)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200">
                                    <span>⚠️</span>
                                    <span>لم يجب الطالب (0 درجة)</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-200">
                                    <span>✕</span>
                                    <span>إجابة غير صحيحة (0 درجة)</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- نص الفقرة التوضيحية إن وجد (Reading Passage) -->
                    @if($q->context_passage)
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-xs text-slate-700 leading-relaxed font-serif">
                            <span class="text-[10px] font-bold text-slate-400 block mb-1">📖 نص القراءة / الفقرة:</span>
                            {{ $q->context_passage }}
                        </div>
                    @endif

                    <!-- نص السؤال -->
                    <h4 class="font-black text-sm sm:text-base text-slate-900 leading-snug">
                        {{ $q->question_text }}
                    </h4>

                    <!-- خيارات الإجابة واستعراض اختيار الطالب -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                        @foreach($q->options ?? [] as $optIdx => $optText)
                            @php
                                $isStudentSelected = (trim(mb_strtolower($studentChoice)) === trim(mb_strtolower($optText)));
                                $isThisCorrectAnswer = (trim(mb_strtolower($q->correct_answer)) === trim(mb_strtolower($optText)));
                                
                                $cardClass = 'border-slate-200 bg-slate-50 text-slate-700';
                                if ($isThisCorrectAnswer && $isStudentSelected) {
                                    $cardClass = 'border-emerald-500 bg-emerald-50 text-emerald-950 ring-2 ring-emerald-500/30';
                                } elseif ($isStudentSelected && !$isThisCorrectAnswer) {
                                    $cardClass = 'border-rose-500 bg-rose-50 text-rose-950 ring-2 ring-rose-500/30';
                                } elseif ($isThisCorrectAnswer) {
                                    $cardClass = 'border-emerald-500 bg-emerald-50/70 text-emerald-900 border-dashed';
                                }
                            @endphp

                            <div class="p-3 rounded-2xl border flex items-center justify-between text-xs {{ $cardClass }}">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-white border border-slate-300 flex items-center justify-center font-bold text-[11px] font-mono shrink-0 shadow-2xs">
                                        {{ chr(65 + $optIdx) }}
                                    </span>
                                    <span class="font-medium">{{ $optText }}</span>
                                </div>

                                <div class="shrink-0 flex items-center gap-1 text-[10px] font-black">
                                    @if($isStudentSelected && $isThisCorrectAnswer)
                                        <span class="text-emerald-700 bg-emerald-200/60 px-2 py-0.5 rounded-full">
                                            اختيار الطالب (صحيح ✓)
                                        </span>
                                    @elseif($isStudentSelected && !$isThisCorrectAnswer)
                                        <span class="text-rose-700 bg-rose-200/60 px-2 py-0.5 rounded-full">
                                            اختيار الطالب (خطأ ✕)
                                        </span>
                                    @elseif($isThisCorrectAnswer)
                                        <span class="text-emerald-700 bg-emerald-200/60 px-2 py-0.5 rounded-full">
                                            الإجابة الصحيحة النموذجية ✓
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
