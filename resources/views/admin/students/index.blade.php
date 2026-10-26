@extends('layouts.app', ['title' => 'إدارة الطلاب والقبول والتسجيل'])

@section('content')
<div class="space-y-6 text-slate-800">

    <!-- رسائل التنبيهات -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold">✕</button>
        </div>
    @endif

    <!-- الترويسة العليا والتنقل -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-indigo-500/20">
        <div class="space-y-2">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1 rounded-full text-xs font-bold bg-white/10 hover:bg-white/20 text-indigo-200 transition flex items-center gap-1 border border-white/10">
                    <span>⬅️</span>
                    <span>العودة للوحة الإدارة العامة</span>
                </a>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                    🎓 شاشة مستقلة متخصصة
                </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-white flex items-center gap-2.5">
                <span>🎓</span>
                <span>إدارة الطلاب والقبول والتسجيل</span>
            </h1>

            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                استعراض سجلات كافة الطلاب المسجلين، فحص ومراجعة نتائج اختبارات تحديد المستوى وفحص النطق الصوتي بالذكاء الاصطناعي، واعتماد التسكين الأكاديمي وتفعيل الحسابات.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap shrink-0">
            <a href="{{ route('accounting.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <span>💼</span>
                <span>النظام المحاسبي والسندات</span>
            </a>
            <a href="{{ route('admin.groups.index') }}" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <span>👥</span>
                <span>المجموعات الدراسية</span>
            </a>
        </div>
    </div>

    <!-- البطاقات الإحصائية للقبول والتسجيل -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 block">إجمالي الطلاب المسجلين</span>
                <span class="text-2xl font-black text-slate-800 font-mono">{{ number_format($totalStudents) }}</span>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl shrink-0">
                👥
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-amber-200 shadow-sm bg-gradient-to-br from-white to-amber-50/50 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-700 block">بانتظار الاعتماد والتسكين</span>
                <span class="text-2xl font-black text-amber-700 font-mono">{{ number_format($pendingApproval) }}</span>
            </div>
            <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center text-xl shrink-0">
                ⏳
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-blue-200 shadow-sm bg-gradient-to-br from-white to-blue-50/50 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-blue-700 block">بانتظار أداء الاختبار</span>
                <span class="text-2xl font-black text-blue-700 font-mono">{{ number_format($pendingTest) }}</span>
            </div>
            <div class="w-12 h-12 bg-blue-100 text-blue-700 rounded-2xl flex items-center justify-center text-xl shrink-0">
                📝
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-emerald-200 shadow-sm bg-gradient-to-br from-white to-emerald-50/50 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-700 block">الطلاب الفاعلون والمقبولون</span>
                <span class="text-2xl font-black text-emerald-700 font-mono">{{ number_format($activeStudents) }}</span>
            </div>
            <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-2xl flex items-center justify-center text-xl shrink-0">
                ✅
            </div>
        </div>
    </div>

    <!-- فلتر وبحث سجل الطلاب -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.students.index') }}" method="GET" class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex-1 w-full relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الطالب، رقم القيد الأكاديمي، الهاتف، أو البريد..." class="w-full pr-10 pl-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                <span class="absolute right-3.5 top-3 text-slate-400 text-sm">🔍</span>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <select name="status" onchange="this.form.submit()" class="border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 bg-white focus:ring-2 focus:ring-indigo-500 outline-none w-full md:w-auto">
                    <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>جميع الحالات</option>
                    <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>⏳ أنهى الاختبار (بانتظار الاعتماد)</option>
                    <option value="pending_test" {{ request('status') === 'pending_test' ? 'selected' : '' }}>📝 لم يؤد الاختبار بعد</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>✅ مفعل ومقبول</option>
                </select>

                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shrink-0">
                    تصفية
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.students.index') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition shrink-0">
                        إلغاء الفلتر
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- جدول سجل الطلاب والقبول -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <span class="font-black text-slate-900 text-sm flex items-center gap-2">
                <span>📋</span>
                <span>قائمة الطلاب المسجلين ونتائج القبول ({{ $students->total() }} طالب)</span>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 text-xs">
                    <tr>
                        <th class="p-4">رقم القيد الأكاديمي</th>
                        <th class="p-4">اسم الطالب والواتساب</th>
                        <th class="p-4">حالة القبول</th>
                        <th class="p-4">نتيجة اختبار القبول</th>
                        <th class="p-4">فحص نطق الصوت (AI)</th>
                        <th class="p-4 text-center">الإجراء: تخصيص المستوى واعتماده</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                        @php
                            $attempt = $student->examAttempts->first();
                            $activeEnrollment = $student->enrollments->where('status', 'active')->first();
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-4 font-mono font-bold text-xs text-indigo-700">
                                {{ $student->student_code ?? $student->user_code ?? ('STU-' . $student->id) }}
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-900 block leading-tight">{{ $student->name }}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-slate-500 font-mono" dir="ltr">{{ $student->phone_whatsapp ?: 'بدون هاتف' }}</span>
                                    @if($student->phone_whatsapp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->phone_whatsapp) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 text-xs font-bold" title="مراسلة فورية">
                                            💬
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                @if($student->status === 'active')
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        مفعل ومقبول ✅
                                    </span>
                                    @if($activeEnrollment && $activeEnrollment->level)
                                        <span class="text-[10px] text-slate-500 block mt-1 font-bold">
                                            المستوى: {{ $activeEnrollment->level->name }}
                                        </span>
                                    @endif
                                @elseif($student->status === 'pending_approval')
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                                        أنهى الاختبار (بانتظار الاعتماد) ⏳
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        لم يؤد الاختبار بعد 📝
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($attempt)
                                    <div class="space-y-0.5">
                                        <span class="font-black text-indigo-700 text-xs block font-mono">
                                            {{ $attempt->total_score }} / {{ $attempt->exam?->total_points ?? 100 }}
                                        </span>
                                        @if($attempt->suggestedLevel)
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 inline-block">
                                                المقترح: {{ $attempt->suggestedLevel->name }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs font-mono">-</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @php
                                    $audioAns = $attempt?->answers?->whereNotNull('recorded_audio_path')->first();
                                    $hasVoice = $attempt && ($attempt->speech_feedback || $attempt->speech_score > 0 || $audioAns);
                                @endphp
                                @if($hasVoice)
                                    <div class="space-y-1.5 min-w-[210px]">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="inline-flex items-center gap-1 font-bold text-xs text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                                🎙️ دقة النطق {{ $attempt->speech_feedback['accuracy_score'] ?? ($audioAns?->speech_accuracy ?? 85) }}%
                                            </span>
                                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-200">
                                                طلاقة {{ $attempt->speech_feedback['fluency_score'] ?? 82.5 }}%
                                            </span>
                                        </div>
                                        @if($audioAns && $audioAns->recorded_audio_path)
                                            <div class="pt-0.5">
                                                <audio controls class="w-44 h-7 rounded" src="{{ asset('storage/' . $audioAns->recorded_audio_path) }}"></audio>
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-1.5 pt-1">
                                            <button type="button" 
                                                    onclick='openSpeechDiagnosticModal(@json($student->name), @json($student->student_code ?? $student->user_code), @json($attempt->speech_feedback), @json(asset("storage/" . ($audioAns?->recorded_audio_path ?? ""))))' 
                                                    class="text-[11px] font-bold text-indigo-700 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2.5 py-1 rounded-lg transition flex items-center gap-1 shadow-2xs">
                                                <span>🔍</span>
                                                <span>تقرير الأخطاء والتفاصيل</span>
                                            </button>
                                            <button type="button" 
                                                    onclick="playAiNativeSpeech({{ json_encode($attempt->speech_feedback['expected_text'] ?? 'Learning English opens great opportunities for communication and global success.') }})" 
                                                    title="استمع للنطق الصحيح بالذكاء الاصطناعي"
                                                    class="text-[11px] font-bold text-amber-800 hover:text-amber-950 bg-amber-100 hover:bg-amber-200 border border-amber-300 px-2 py-1 rounded-lg transition flex items-center gap-1 shadow-2xs">
                                                <span>🔊</span>
                                                <span>النطق الصحيح (AI)</span>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">لم يسجل صوتاً بعد</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($student->status !== 'active')
                                        <form action="{{ route('admin.assign_level', $student->id) }}" method="POST" class="flex items-center gap-1.5">
                                            @csrf
                                            <select name="level_id" required class="text-xs border border-slate-300 rounded-lg px-2.5 py-1.5 focus:ring-1 focus:ring-indigo-500 outline-none bg-white font-bold">
                                                <option value="">-- اختر المستوى لتسكين الطالب --</option>
                                                @foreach($levels as $lvl)
                                                    <option value="{{ $lvl->id }}" {{ $attempt && $attempt->suggested_level_id == $lvl->id ? 'selected' : '' }}>
                                                        {{ $lvl->name }} ({{ $lvl->formatted_fee }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs px-3.5 py-1.5 rounded-lg shadow-sm transition shrink-0">
                                                اعتماد وتفعيل
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('accounting.statement', $student->id) }}" class="text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                                <span>📄</span>
                                                <span>كشف الحساب</span>
                                            </a>
                                            <!-- إمكانية ترقية أو تغيير المستوى -->
                                            <form action="{{ route('admin.assign_level', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('تعديل أو ترقية المستوى للطالب؟');">
                                                @csrf
                                                <select name="level_id" required onchange="if(confirm('تأكيد ترقية/تعديل مستوى الطالب إلى: ' + this.options[this.selectedIndex].text)) { this.form.submit(); }" class="text-[11px] border border-slate-200 rounded-lg px-2 py-1 bg-slate-50 text-slate-600 font-bold">
                                                    <option value="">تعديل المستوى...</option>
                                                    @foreach($levels as $lvl)
                                                        <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                لا يوجد طلاب يطابقون خيارات البحث الحالية.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>

<!-- =============================================================== -->
<!-- مودال التقرير التفصيلي لفحص النطق وأخطاء الطالب وسماع الذكاء الاصطناعي -->
<!-- =============================================================== -->
<div id="speechDiagnosticModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 md:p-6 overflow-hidden" style="overscroll-behavior: contain;">
    <div class="fixed inset-0 cursor-pointer" onclick="closeSpeechDiagnosticModal()"></div>

    <div class="relative bg-white w-full max-w-4xl rounded-3xl shadow-2xl border border-slate-200 flex flex-col max-h-[92vh] sm:max-h-[90vh] overflow-hidden z-10 animate-in fade-in zoom-in-95 duration-150">
        
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shadow-xs">
                    🎙️
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                        <span>تقرير التشخيص الصوتي المتقدم (AI Speech Diagnostic)</span>
                    </h3>
                    <p class="text-xs text-slate-500" id="diagStudentInfo">الطالب: جارٍ التحميل...</p>
                </div>
            </div>
            <button type="button" onclick="closeSpeechDiagnosticModal()" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-rose-100 hover:text-rose-600 text-slate-600 font-bold flex items-center justify-center transition">
                ✕
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-6 flex-1 text-right text-xs">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-3">
                    <span class="text-[11px] font-bold text-indigo-600 block">الدرجة الإجمالية للنطق</span>
                    <span class="text-xl font-black text-indigo-900 font-mono" id="diagOverallScore">0%</span>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-3">
                    <span class="text-[11px] font-bold text-emerald-600 block">دقة المخارج (Accuracy)</span>
                    <span class="text-xl font-black text-emerald-900 font-mono" id="diagAccuracyScore">0%</span>
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-3">
                    <span class="text-[11px] font-bold text-amber-600 block">الطلاقة والسرعة (Fluency)</span>
                    <span class="text-xl font-black text-amber-900 font-mono" id="diagFluencyScore">0%</span>
                </div>
                <div class="bg-purple-50 border border-purple-100 rounded-2xl p-3">
                    <span class="text-[11px] font-bold text-purple-600 block">اكتمال النص (Completeness)</span>
                    <span class="text-xl font-black text-purple-900 font-mono" id="diagCompletenessScore">0%</span>
                </div>
            </div>

            <!-- المقارنة النصية التفاعلية -->
            <div class="space-y-2">
                <span class="font-black text-slate-800 text-xs block">مقارنة النص المنطوق والمطلوب:</span>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 font-mono" dir="ltr">
                    <div class="text-[11px] text-slate-500 font-bold">Target Text: <span class="text-slate-800" id="diagExpectedText">-</span></div>
                    <div class="text-[11px] text-slate-500 font-bold">Recognized: <span class="text-emerald-700" id="diagRecognizedText">-</span></div>
                </div>
            </div>

            <!-- تفريغ الكلمات ومخارج الحروف -->
            <div class="space-y-2">
                <span class="font-black text-slate-800 text-xs block">تحليل الكلمات (Word-Level Evaluation):</span>
                <div id="diagWordsContainer" class="flex flex-wrap gap-2 pt-1" dir="ltr">
                    <!-- يتم بناؤها ديناميكياً -->
                </div>
            </div>
        </div>

        <div class="px-6 py-3 border-t border-slate-200 bg-slate-50 flex justify-end shrink-0">
            <button type="button" onclick="closeSpeechDiagnosticModal()" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                إغلاق التقرير
            </button>
        </div>
    </div>
</div>

<script>
    function openSpeechDiagnosticModal(studentName, studentCode, feedback, audioUrl) {
        document.getElementById('diagStudentInfo').innerText = 'الطالب: ' + studentName + ' [' + (studentCode || '') + ']';
        
        const fb = feedback || {};
        document.getElementById('diagOverallScore').innerText = Math.round(fb.overall_score || 0) + '%';
        document.getElementById('diagAccuracyScore').innerText = Math.round(fb.accuracy_score || 0) + '%';
        document.getElementById('diagFluencyScore').innerText = Math.round(fb.fluency_score || 0) + '%';
        document.getElementById('diagCompletenessScore').innerText = Math.round(fb.completeness_score || 0) + '%';
        document.getElementById('diagExpectedText').innerText = fb.expected_text || '-';
        document.getElementById('diagRecognizedText').innerText = fb.recognized_text || '(لم يتم التقاط صوت واضح)';

        const container = document.getElementById('diagWordsContainer');
        container.innerHTML = '';

        const words = fb.words_data || fb.words || [];
        if (words.length > 0) {
            words.forEach(w => {
                const score = w.score || 0;
                let bgClass = score >= 80 ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : (score >= 60 ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-rose-100 text-rose-800 border-rose-300');
                
                const span = document.createElement('span');
                span.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-bold ' + bgClass;
                span.innerHTML = `<span>${w.word}</span> <span class="text-[10px] opacity-75">(${score}%)</span>`;
                container.appendChild(span);
            });
        } else {
            container.innerHTML = '<span class="text-slate-400 text-xs">لا تتوفر تفاصيل تفكيك الكلمات لهذا الاختبار.</span>';
        }

        document.getElementById('speechDiagnosticModal').classList.remove('hidden');
    }

    function closeSpeechDiagnosticModal() {
        document.getElementById('speechDiagnosticModal').classList.add('hidden');
    }

    function playAiNativeSpeech(text) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'en-US';
            utterance.rate = 0.9;
            window.speechSynthesis.speak(utterance);
        } else {
            alert('متصفحك لا يدعم نطق الذكاء الاصطناعي المباشر.');
        }
    }
</script>
@endsection
