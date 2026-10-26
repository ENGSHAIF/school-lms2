@extends('layouts.app', ['title' => 'أداء الاختبار: ' . $exam->title])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- شريط معلومات الاختبار وساعة التوقيت الحي (Sticky Timer Header) -->
    <div class="sticky top-4 z-40 bg-slate-900/95 backdrop-blur-md rounded-3xl p-5 text-white shadow-2xl border border-indigo-500/30">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs text-amber-400 font-bold">
                    <span>{{ $exam->level?->course?->name }}</span>
                    <span>•</span>
                    <span>{{ $exam->level?->name }}</span>
                    @if($exam->term)
                        <span>•</span>
                        <span>{{ $exam->term }}</span>
                    @endif
                </div>
                <h1 class="text-lg sm:text-xl font-black text-white">
                    {{ $exam->title }}
                </h1>
            </div>

            <!-- ساعة التوقيت التنازلي التفاعلية -->
            <div class="flex items-center gap-3 bg-white/10 px-4 py-2.5 rounded-2xl border border-white/10 shrink-0">
                <span class="text-xl">⏱️</span>
                <div>
                    <span class="text-[10px] text-slate-300 block font-bold">الوقت المتبقي:</span>
                    <span id="countdownTimer" class="text-xl sm:text-2xl font-black font-mono tracking-widest text-emerald-400">
                        --:--
                    </span>
                </div>
            </div>

        </div>

        <!-- شريط التقدم ونقاط التنقل السريع بين الأسئلة -->
        <div class="mt-4 pt-3 border-t border-white/10 space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-slate-300">
                <span id="progressStats">أجبت على 0 من {{ count($questions) }} سؤال</span>
                <span id="progressPercent" class="font-mono text-amber-400">0%</span>
            </div>

            <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
                <div id="progressBar" class="bg-gradient-to-r from-emerald-400 to-teal-400 h-full w-0 transition-all duration-300"></div>
            </div>

            <!-- أرقام الأسئلة للتنقل السريع -->
            <div class="flex items-center gap-1.5 overflow-x-auto py-1 text-xs">
                @foreach($questions as $idx => $q)
                    <a href="#question-{{ $q->id }}" id="badge-q-{{ $q->id }}" class="question-nav-badge w-7 h-7 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white flex items-center justify-center font-mono font-bold shrink-0 transition">
                        {{ $idx + 1 }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- نموذج الاختبار وتسليم الإجابات -->
    <form id="examForm" action="{{ route('student.exam.submit', $exam->id) }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="study_group_id" value="{{ $groupId }}">

        @foreach($questions as $idx => $q)
            @php
                $options = isset($q->shuffled_options) ? $q->shuffled_options : ($q->options ?? []);
            @endphp

            <div id="question-{{ $q->id }}" class="question-card bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-5 transition duration-200 scroll-mt-36" data-question-id="{{ $q->id }}">
                
                <!-- رأس السؤال والدرجة -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-sm font-mono shadow-xs">
                            {{ $idx + 1 }}
                        </span>
                        <span class="font-bold text-xs text-slate-500">
                            السؤال رقم {{ $idx + 1 }} من أصل {{ count($questions) }}
                        </span>
                    </div>

                    <span class="text-xs font-mono font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-3 py-1 rounded-full">
                        {{ $q->points }} درجات
                    </span>
                </div>

                <!-- نص القراءة أو السياق (Reading Passage) إن وجد -->
                @if($q->context_passage)
                    <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-200/80 text-xs text-amber-950 leading-relaxed font-serif">
                        <span class="text-[10px] font-black uppercase text-amber-800 tracking-wider block mb-1">📖 نص القراءة / الفقرة المرجعية:</span>
                        {{ $q->context_passage }}
                    </div>
                @endif

                <!-- نص السؤال المطلوب -->
                <h3 class="font-black text-base sm:text-lg text-slate-900 leading-snug">
                    {{ $q->question_text }}
                </h3>

                <!-- خيارات الإجابة التفاعلية (Radio Cards) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    @foreach($options as $optIdx => $optionText)
                        @php
                            $radioId = "opt_{$q->id}_{$optIdx}";
                        @endphp
                        <label for="{{ $radioId }}" class="option-label group relative p-4 rounded-2xl border-2 border-slate-200 hover:border-indigo-400 bg-slate-50/60 hover:bg-indigo-50/30 cursor-pointer transition flex items-start gap-3 select-none">
                            <input type="radio" id="{{ $radioId }}" name="q_{{ $q->id }}" value="{{ $optionText }}" class="exam-radio mt-1 w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer" onchange="markQuestionAnswered({{ $q->id }})">
                            
                            <div class="flex-1 text-xs">
                                <span class="font-bold text-slate-400 group-hover:text-indigo-600 ml-1 font-mono">
                                    {{ chr(65 + $optIdx) }})
                                </span>
                                <span class="font-bold text-slate-800 leading-relaxed">
                                    {{ $optionText }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>

            </div>
        @endforeach

        <!-- بطاقة التسليم النهائي (Footer Submit Card) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h4 class="font-black text-base text-slate-900">جاهز لتسليم إجاباتك؟</h4>
                <p class="text-xs text-slate-500 mt-0.5" id="submitHelpText">
                    تأكد من مراجعة كافة الأسئلة قبل النقر على زر التسليم النهائي.
                </p>
            </div>

            <button type="button" onclick="confirmSubmit()" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-black text-sm rounded-2xl shadow-lg hover:shadow-xl transition flex items-center justify-center gap-2">
                <span>🚀</span>
                <span>تسليم الإجابات وإنهاء الاختبار</span>
            </button>
        </div>

    </form>

</div>

<!-- مودال تأكيد التسليم -->
<div id="confirmModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl p-6 sm:p-8 space-y-5 border border-slate-200 shadow-2xl text-center">
        <span class="text-4xl block">📝</span>
        <h3 class="font-black text-lg text-slate-900">تأكيد تسليم الاختبار</h3>
        
        <p id="confirmMessage" class="text-xs text-slate-600 leading-relaxed">
            هل أنت متأكد من رغبتك في تسليم كافة الإجابات الآن؟ لن تتمكن من تعديل الإجابات بعد التسليم.
        </p>

        <div class="grid grid-cols-2 gap-3 pt-2">
            <button type="button" onclick="document.getElementById('confirmModal').classList.add('hidden')" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                تراجع للمراجعة
            </button>
            <button type="button" onclick="executeSubmit()" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition">
                نعم، سلّم الآن ✓
            </button>
        </div>
    </div>
</div>

<script>
    // حساب التوقيت التنازلي بناءً على وقت البداية ومدة الاختبار
    const durationMinutes = {{ $exam->duration_minutes }};
    const totalQuestionsCount = {{ count($questions) }};
    
    // تاريخ بداية المحاولة
    @php
        $startedAtTimestamp = $attempt->started_at ? $attempt->started_at->timestamp : now()->timestamp;
        $nowTimestamp = now()->timestamp;
        $elapsedSeconds = max(0, $nowTimestamp - $startedAtTimestamp);
        $totalDurationSeconds = $exam->duration_minutes * 60;
        $remainingSeconds = max(0, $totalDurationSeconds - $elapsedSeconds);
    @endphp

    let timeLeft = {{ $remainingSeconds }};
    const timerElement = document.getElementById('countdownTimer');

    function updateTimer() {
        if (timeLeft <= 0) {
            timerElement.textContent = "00:00";
            timerElement.classList.remove('text-emerald-400', 'text-amber-400');
            timerElement.classList.add('text-rose-500', 'animate-pulse');
            alert('انتهى الوقت المحدد للاختبار! سيتم تسليم إجاباتك الآن تلقائياً.');
            document.getElementById('examForm').submit();
            return;
        }

        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;

        timerElement.textContent = 
            (minutes < 10 ? '0' : '') + minutes + ':' + 
            (seconds < 10 ? '0' : '') + seconds;

        // تغيير اللون عند اقتراب انتهاء الوقت
        if (timeLeft <= 300 && timeLeft > 60) {
            timerElement.classList.remove('text-emerald-400');
            timerElement.classList.add('text-amber-400');
        } else if (timeLeft <= 60) {
            timerElement.classList.remove('text-amber-400');
            timerElement.classList.add('text-rose-500', 'animate-pulse');
        }

        timeLeft--;
    }

    setInterval(updateTimer, 1000);
    updateTimer();

    // تحديث مؤشر الإجابة عن السؤال
    function markQuestionAnswered(questionId) {
        const badge = document.getElementById('badge-q-' + questionId);
        if (badge) {
            badge.classList.remove('bg-white/10', 'text-white');
            badge.classList.add('bg-emerald-500', 'text-slate-950', 'font-black');
        }

        // تحديث نمط البطاقة المحددة
        const card = document.getElementById('question-' + questionId);
        if (card) {
            card.querySelectorAll('.option-label').forEach(lbl => {
                const radio = lbl.querySelector('input[type="radio"]');
                if (radio && radio.checked) {
                    lbl.classList.add('border-indigo-600', 'bg-indigo-50/50', 'ring-2', 'ring-indigo-500/20');
                    lbl.classList.remove('border-slate-200', 'bg-slate-50/60');
                } else {
                    lbl.classList.remove('border-indigo-600', 'bg-indigo-50/50', 'ring-2', 'ring-indigo-500/20');
                    lbl.classList.add('border-slate-200', 'bg-slate-50/60');
                }
            });
        }

        updateProgressBar();
    }

    function updateProgressBar() {
        const answeredRadios = document.querySelectorAll('.exam-radio:checked');
        const answeredCount = answeredRadios.length;
        const percent = Math.round((answeredCount / totalQuestionsCount) * 100);

        document.getElementById('progressStats').textContent = `أجبت على ${answeredCount} من ${totalQuestionsCount} سؤال`;
        document.getElementById('progressPercent').textContent = `${percent}%`;
        document.getElementById('progressBar').style.width = `${percent}%`;
    }

    function confirmSubmit() {
        const answeredCount = document.querySelectorAll('.exam-radio:checked').length;
        const unanswered = totalQuestionsCount - answeredCount;
        const msgEl = document.getElementById('confirmMessage');

        if (unanswered > 0) {
            msgEl.innerHTML = `⚠️ <strong class="text-rose-600 font-bold">تنبيه هام:</strong> لديك <strong>(${unanswered})</strong> أسئلة لم تجب عليها بعد!<br><br>هل أنت متأكد من تسليم الاختبار الآن مع ترك هذه الأسئلة بدون إجابة؟`;
        } else {
            msgEl.innerHTML = `🎉 رائع! لقد أجبت على كافة الأسئلة (<strong>${totalQuestionsCount} من ${totalQuestionsCount}</strong>).<br><br>هل أنت متأكد من اعتماد وتسليم الإجابات النهائية؟`;
        }

        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function executeSubmit() {
        document.getElementById('confirmModal').classList.add('hidden');
        document.getElementById('examForm').submit();
    }
</script>

<style>
    /* تحسين استجابة الخيارات عند الاختيار */
    .option-label:has(input:checked) {
        border-color: rgb(79 70 229);
        background-color: rgb(238 242 255);
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }
</style>
@endsection
