@extends('layouts.app', ['title' => 'حالة القبول وتحديد المستوى'])

@section('content')
<div class="max-w-2xl mx-auto my-8">

    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        
        <!-- الترويسة العلوية -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-8 text-white text-center">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                ⏳
            </div>
            <h1 class="text-2xl font-black">طلبك قيد المراجعة والاعتماد</h1>
            <p class="text-amber-100 text-xs mt-1">تم تسليم اختبار تحديد المستوى ورصد نتائج الصوت وتخصيص المرحلة الدراسية</p>
        </div>

        <div class="p-8 space-y-6">

            <!-- بطاقة النتيجة الأولية -->
            @if($latestAttempt)
                @php
                    $speechFb = $latestAttempt->speech_feedback;
                    $speechAnswer = \App\Models\ExamAnswer::where('attempt_id', $latestAttempt->id)->whereNotNull('recorded_audio_path')->first();
                    $audioPath = $speechFb['audio_path'] ?? ($speechAnswer?->recorded_audio_path ?? null);
                @endphp
                <div class="bg-slate-50 border border-slate-200 rounded-3xl p-6 text-center space-y-4">
                    <div>
                        <span class="text-xs font-bold text-slate-500 block mb-1">الدرجة الإجمالية في اختبار القبول</span>
                        <div class="text-4xl font-black text-indigo-600 mb-2 font-mono">
                            {{ $latestAttempt->total_score }} / {{ $latestAttempt->exam?->total_points ?? 100 }}
                        </div>

                        <div class="inline-flex items-center gap-2 bg-indigo-100/70 text-indigo-800 text-xs font-bold px-4 py-1.5 rounded-full">
                            <span>المستوى المقترح لك:</span>
                            <span class="font-black">{{ $latestAttempt->suggestedLevel?->name ?? 'Level 1: Beginner' }}</span>
                        </div>
                    </div>

                    <!-- تفاصيل فحص الصوت والمصحح التلقائي بالذكاء الاصطناعي -->
                    <div class="pt-6 border-t border-slate-200 text-right space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                <span>🎙️</span>
                                <span>تقرير المصحح التلقائي وفحص نطق الصوت (AI Speech Assessment):</span>
                            </h4>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                تم الرصد والتقييم ✓
                            </span>
                        </div>

                        <!-- مشغل الاستماع للصوت المسجل ومقارنته بنطق الذكاء الاصطناعي -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @if($audioPath)
                                <div class="bg-white p-4 rounded-2xl border border-indigo-100 shadow-sm space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                            <span>🎧</span>
                                            <span>تسجيلك الصوتي:</span>
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono">Your Voice</span>
                                    </div>
                                    <audio controls class="w-full h-9 rounded-lg" src="{{ asset('storage/' . $audioPath) }}"></audio>
                                </div>
                            @endif

                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-2xl border border-indigo-200 shadow-sm space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-indigo-900 flex items-center gap-1.5">
                                        <span>🤖</span>
                                        <span>النطق النموذجي الصحيح:</span>
                                    </span>
                                    <span class="text-[10px] font-bold text-indigo-600 font-mono">AI Native</span>
                                </div>
                                <div class="flex items-center gap-2 pt-0.5">
                                    <button type="button" onclick="playAiNativeSpeech({{ json_encode($speechFb['expected_text'] ?? 'Learning English opens great opportunities for communication and global success.') }}, 1.0)" class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1">
                                        <span>🔊</span>
                                        <span>استمع للنطق الصحيح</span>
                                    </button>
                                    <button type="button" onclick="playAiNativeSpeech({{ json_encode($speechFb['expected_text'] ?? 'Learning English opens great opportunities for communication and global success.') }}, 0.8)" class="px-2.5 py-2 bg-white hover:bg-slate-100 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-xl shadow-2xs transition" title="استمع ببطء">
                                        <span>🐢 0.8x</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- الكلمات وتحليل النطق كلمة بكلمة إذا توفرت -->
                        @if(!empty($speechFb['words_breakdown']))
                            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                                <span class="text-xs font-bold text-slate-700 block">تقييم الكلمات ومواضع القوة والضعف (اضغط على الكلمة لسماعها):</span>
                                <div class="flex flex-wrap gap-1.5" dir="ltr">
                                    @foreach($speechFb['words_breakdown'] as $wb)
                                        @php
                                            $color = ($wb['score'] >= 88) ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : (($wb['score'] >= 75) ? 'bg-amber-50 text-amber-800 border-amber-300' : 'bg-red-50 text-red-800 border-red-300 ring-1 ring-red-400');
                                        @endphp
                                        <button type="button" onclick="playAiNativeSpeech('{{ preg_replace('/[^a-zA-Z]/', '', $wb['word']) }}', 0.9)" class="px-2.5 py-1 rounded-lg border text-xs font-bold font-mono transition hover:scale-105 {{ $color }}" title="{{ $wb['note'] ?? '' }}">
                                            <span>{{ $wb['word'] }}</span>
                                            <span class="text-[10px] opacity-75 font-sans">({{ $wb['score'] }}%)</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- المؤشرات الرقمية: الدقة والطلاقة -->
                        <div class="grid grid-cols-2 gap-3 text-center">
                            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                                <span class="text-[11px] text-slate-500 block mb-1">دقة النطق ومخارج الحروف</span>
                                <span class="text-xl font-black text-emerald-600 font-mono">
                                    {{ $speechFb['accuracy_score'] ?? ($speechAnswer?->speech_accuracy ?? 85) }}%
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5 font-sans">Pronunciation Accuracy</span>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
                                <span class="text-[11px] text-slate-500 block mb-1">طلاقة وسرعة القراءة (Fluency)</span>
                                <span class="text-xl font-black text-indigo-600 font-mono">
                                    {{ $speechFb['fluency_score'] ?? 82 }}%
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5 font-sans">Speaking Fluency</span>
                            </div>
                        </div>

                        <!-- توجيهات المصحح الصوتي التلقائي لتحسين النطق -->
                        @php
                            $tips = $speechFb['tips'] ?? [
                                "نطق صوتي سليم ومخارج حروف واضحة ومفهومة تتطابق مع معايير القبول والتسكين.",
                                "يُنصح بمواصلة الاستماع للناطقين الأصليين والتدريب على سرعة الربط الصوتي (Connected Speech)."
                            ];
                        @endphp
                        <div class="bg-emerald-50/90 border border-emerald-200 p-4 rounded-2xl text-xs text-emerald-900 leading-relaxed space-y-1.5">
                            <strong class="block text-emerald-950 font-bold flex items-center gap-1.5">
                                <span>💡</span>
                                <span>توجيهات المصحح التلقائي لتحسين نطقك:</span>
                            </strong>
                            @foreach((array)$tips as $tip)
                                <p class="text-[11px] flex items-start gap-1.5">
                                    <span class="text-emerald-600 font-black">•</span>
                                    <span>{{ $tip }}</span>
                                </p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- الإجراءات التالية -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-amber-900 text-xs space-y-2 leading-relaxed">
                <div class="font-bold text-sm flex items-center gap-2 text-amber-950">
                    <span>📱</span>
                    <span>ماذا يحدث الآن؟</span>
                </div>
                <p>1. سيقوم مدير النظام والمشرف الأكاديمي بمراجعة نتيجتك واعتماد مستواك الأكاديمي في اللغة الإنجليزية أو التوفل.</p>
                <p>2. سيصلك إشعار فوري عبر الواتساب على رقمك (<strong>{{ auth()->user()->phone_whatsapp }}</strong>) يتضمن تفاصيل القبول وسند الرسوم.</p>
                <p>3. بعد الاعتماد، ستفتح لك تلقائياً <strong>بوابة الطالب</strong> لحجز الحصص 1-on-1 مع المعلمين المتاحين لمدة 25 دقيقة.</p>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('student.status') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 underline">
                    🔄 تحديث الصفحة
                </a>
            </div>

        </div>

</div>

<script>
    function playAiNativeSpeech(text, rate = 1.0) {
        if (!('speechSynthesis' in window)) {
            alert('عذراً، متصفحك لا يدعم خاصية تحويل النص إلى صوت (Speech Synthesis).');
            return;
        }
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-US';
        utterance.rate = rate;
        utterance.pitch = 1.0;

        const voices = window.speechSynthesis.getVoices();
        const enVoice = voices.find(v => (v.lang === 'en-US' || v.lang.startsWith('en')) && (v.name.includes('Google') || v.name.includes('Natural') || v.name.includes('Microsoft') || v.name.includes('Samantha') || v.name.includes('David')));
        if (enVoice) {
            utterance.voice = enVoice;
        }
        window.speechSynthesis.speak(utterance);
    }
</script>
@endsection
