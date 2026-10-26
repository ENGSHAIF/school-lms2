@extends('layouts.app', ['title' => 'اختبار تحديد المستوى والقبول'])

@section('content')
<div class="max-w-3xl mx-auto my-6">

    <!-- شريط معلومات الاختبار والوقت -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-2xl p-6 shadow-xl mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-bold border border-indigo-400/20 inline-block mb-2">
                اختبار إلزامي لقياس الكفاءة
            </span>
            <h1 class="text-xl font-black">{{ $exam->title }}</h1>
            <p class="text-xs text-slate-300 mt-1">يجب الإجابة على جميع الأسئلة وتسجيل مقطع الصوت ليتمكن النظام من تحديد مستواك.</p>
        </div>

        <!-- العداد التنازلي -->
        <div class="bg-slate-800/80 border border-slate-700 px-5 py-3 rounded-2xl text-center min-w-[140px] shadow-inner">
            <span class="text-[11px] text-slate-400 block font-bold mb-0.5">الوقت المتبقي</span>
            <span id="countdown" class="text-2xl font-black text-amber-400 tracking-wider font-mono">30:00</span>
        </div>
    </div>

    <!-- نموذج الاختبار -->
    <form id="examForm" action="{{ route('placement.test.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @foreach($exam->questions as $index => $q)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <span class="text-xs font-black bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg">
                        السؤال {{ $index + 1 }} من {{ count($exam->questions) }}
                    </span>
                    <span class="text-xs font-bold text-slate-400">
                        {{ $q->points }} درجة
                    </span>
                </div>

                <h3 class="text-base font-bold text-slate-800 mb-4 leading-relaxed">
                    {{ $q->question_text }}
                </h3>

                @if($q->type === 'mcq')
                    <!-- خيارات الاختيار من متعدد -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if(is_array($q->options))
                            @foreach($q->options as $option)
                                <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/40 cursor-pointer transition">
                                    <input type="radio" name="q_{{ $q->id }}" value="{{ $option }}" required
                                        class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <span class="text-sm font-semibold text-slate-700">{{ $option }}</span>
                                </label>
                            @endforeach
                        @endif
                    </div>

                @elseif($q->type === 'speaking_ai')
                    <!-- قسم فحص الصوت والنطق بالذكاء الاصطناعي -->
                    <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-5 mt-2">
                        <div class="mb-3">
                            <span class="text-xs font-bold text-indigo-600 block mb-1">النص المطلوب قراءته بدقة:</span>
                            <div class="bg-white p-4 rounded-xl border border-indigo-200 text-slate-900 font-bold text-base leading-relaxed tracking-wide shadow-sm" dir="ltr">
                                "{{ $q->expected_speech_text }}"
                            </div>
                        </div>

                        <!-- أزرار التسجيل الصوتي التفاعلية -->
                        <div class="flex flex-wrap items-center gap-3 mt-4">
                            <button type="button" id="startRecBtn" onclick="startRecording({{ $q->id }})"
                                class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition">
                                <span class="w-3 h-3 bg-white rounded-full animate-pulse"></span>
                                بدء تسجيل الصوت
                            </button>

                            <button type="button" id="stopRecBtn" onclick="stopRecording({{ $q->id }})" disabled
                                class="hidden flex items-center gap-2 bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition">
                                ⏹️ إنهاء وحفظ التسجيل
                            </button>

                            <span id="recordingStatus" class="text-xs font-bold text-slate-500">
                                اضغط على "بدء تسجيل الصوت" واقرأ الجملة أعلاه بوضوح.
                            </span>
                        </div>

                        <!-- مشغل الصوت للمراجعة -->
                        <div id="audioPlayerContainer" class="hidden mt-4 pt-3 border-t border-indigo-100">
                            <span class="text-xs font-bold text-emerald-700 block mb-1">تم التقاط صوتك بنجاح! استمع إليه قبل التسليم:</span>
                            <audio id="audioPreview" controls class="w-full h-10 rounded-lg"></audio>
                        </div>

                        <!-- الحقل الخفي لتمرير ملف الصوت مع الفورم -->
                        <input type="file" id="speech_audio_{{ $q->id }}" name="speech_audio_{{ $q->id }}" class="hidden" accept="audio/*">
                    </div>
                @endif
            </div>
        @endforeach

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
            <span class="text-xs text-slate-500">تأكد من مراجعة كافة الإجابات قبل التسليم النهائي.</span>
            <button type="submit" 
                class="w-full sm:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition">
                تسليم الاختبار واعتماد النتيجة 🚀
            </button>
        </div>
    </form>
</div>

<!-- كود التسجيل الصوتي والعداد الزمني عبر المتصفح -->
<script>
// 1. العداد التنازلي للاختبار
let totalSeconds = 30 * 60;
const countdownEl = document.getElementById('countdown');

setInterval(() => {
    if (totalSeconds <= 0) {
        document.getElementById('examForm').submit();
        return;
    }
    totalSeconds--;
    const mins = Math.floor(totalSeconds / 60);
    const secs = totalSeconds % 60;
    countdownEl.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
}, 1000);

// 2. تسجيل الصوت عبر MediaRecorder API بالمتصفح
let mediaRecorder;
let audioChunks = [];
let recordedMimeType = 'audio/webm';

async function startRecording(questionId) {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        
        // فحص صيغة التسجيل المدعومة في المتصفح
        let options = {};
        if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) {
            options = { mimeType: 'audio/webm;codecs=opus' };
            recordedMimeType = 'audio/webm';
        } else if (MediaRecorder.isTypeSupported('audio/webm')) {
            options = { mimeType: 'audio/webm' };
            recordedMimeType = 'audio/webm';
        } else if (MediaRecorder.isTypeSupported('audio/mp4')) {
            options = { mimeType: 'audio/mp4' };
            recordedMimeType = 'audio/mp4';
        }

        mediaRecorder = new MediaRecorder(stream, options);
        audioChunks = [];

        mediaRecorder.ondataavailable = e => {
            if (e.data.size > 0) audioChunks.push(e.data);
        };

        mediaRecorder.onstop = () => {
            const mime = recordedMimeType || 'audio/webm';
            const audioBlob = new Blob(audioChunks, { type: mime });
            const audioUrl = URL.createObjectURL(audioBlob);
            
            const player = document.getElementById('audioPreview');
            if (player) {
                player.src = audioUrl;
                document.getElementById('audioPlayerContainer')?.classList.remove('hidden');
            }

            // تحويل الـ Blob إلى File وتعيينه في input
            const ext = mime.includes('webm') ? 'webm' : (mime.includes('mp4') ? 'mp4' : 'wav');
            const file = new File([audioBlob], `student_voice_${questionId}.${ext}`, { type: mime });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            const inputEl = document.getElementById(`speech_audio_${questionId}`);
            if (inputEl) {
                inputEl.files = dataTransfer.files;
            }

            const statusEl = document.getElementById('recordingStatus');
            if (statusEl) {
                statusEl.textContent = '✅ تم تسجيل وحفظ الصوت بنجاح! جاهز للفحص والتقييم الآلي.';
                statusEl.className = 'text-xs font-bold text-emerald-600';
            }
        };

        mediaRecorder.start(250); // تجميع البيانات كل 250 مللي ثانية لضمان التقاط كل الأجزاء
        document.getElementById('startRecBtn')?.classList.add('hidden');
        document.getElementById('stopRecBtn')?.classList.remove('hidden');
        if (document.getElementById('stopRecBtn')) {
            document.getElementById('stopRecBtn').disabled = false;
        }
        const statusEl = document.getElementById('recordingStatus');
        if (statusEl) {
            statusEl.textContent = '🔴 جاري التسجيل الصوتي الآن... اقرأ الجملة بوضوح.';
            statusEl.className = 'text-xs font-bold text-red-600 animate-pulse';
        }

    } catch (err) {
        alert('يرجى السماح بالوصول للميكروفون في المتصفح لتتمكن من أداء اختبار التحدث والنطق.');
    }
}

function stopRecording(questionId) {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
        mediaRecorder.stream.getTracks().forEach(track => track.stop());
        document.getElementById('stopRecBtn')?.classList.add('hidden');
        document.getElementById('startRecBtn')?.classList.remove('hidden');
        const startBtn = document.getElementById('startRecBtn');
        if (startBtn) startBtn.innerHTML = '🔄 إعادة التسجيل الصوتي';
    }
}

// حماية إضافية: إيقاف التسجيل وحفظ الملف تلقائياً إذا ضغط الطالب تسليم الاختبار أثناء التسجيل
document.getElementById('examForm')?.addEventListener('submit', function (e) {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
        mediaRecorder.stream.getTracks().forEach(track => track.stop());
    }
});
</script>
@endsection
