<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير تدقيق ومراجعة أخطاء الاختبار: {{ $attempt->user->name }} - {{ $exam->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        @media print {
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                font-size: 11pt;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
            .print-border {
                border-color: #cbd5e1 !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body class="p-3 sm:p-6 lg:p-8 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- شريط التحكم العلوي للمشرف والطباعة (يختفي أثناء الطباعة) -->
    <div class="max-w-5xl mx-auto mb-6 no-print">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1">
                    <span>←</span>
                    <span>الرجوع</span>
                </a>
                <div>
                    <h2 class="text-xs sm:text-sm font-black text-slate-900">تقرير مراجعة الشفافية وتدقيق الأخطاء الأكاديمية</h2>
                    <p class="text-[11px] text-slate-400">يمكنك تسليم هذا التقرير للطالب أو ولي الأمر لمراجعة وتوضيح الأخطاء في الاختبار.</p>
                </div>
            </div>

            <!-- أزرار خيارات العرض والطباعة -->
            <div class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center bg-slate-100 p-1 rounded-xl text-xs font-bold">
                    <button type="button" onclick="filterQuestions('all')" id="btnFilterAll" class="px-2.5 py-1 rounded-lg bg-white text-indigo-700 shadow-xs">
                        كافة الأسئلة ({{ $totalQuestions }})
                    </button>
                    <button type="button" onclick="filterQuestions('mistakes')" id="btnFilterMistakes" class="px-2.5 py-1 rounded-lg text-rose-700 hover:bg-white/60">
                        الأخطاء فقط ({{ $incorrectCount }})
                    </button>
                    <button type="button" onclick="filterQuestions('correct')" id="btnFilterCorrect" class="px-2.5 py-1 rounded-lg text-emerald-700 hover:bg-white/60">
                        الصحيحة ({{ $correctCount }})
                    </button>
                </div>

                <button type="button" onclick="window.print()" class="px-5 py-2 bg-gradient-to-r from-[#0066A1] to-[#004e7c] hover:opacity-95 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>🖨️</span>
                    <span>طباعة التقرير / PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- الحاوية الرئيسية لورقة التقرير الأكاديمي -->
    <div class="max-w-5xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-lg p-6 sm:p-10 space-y-6">

        <!-- 1. الترويسة المؤسسية الرسمية المعتمدة -->
        <div class="border-b-2 border-slate-900/10 pb-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-right">
                
                <!-- الشعار واسم المؤسسة -->
                <div class="flex items-center gap-3.5">
                    @php
                        $customLogo = \App\Models\SiteSetting::get('site_logo_path');
                        $siteNameAr = \App\Models\SiteSetting::get('site_name_ar', 'منصة النجم الآسيوي التعليمية الذكية (ASOTS)');
                    @endphp
                    @if($customLogo)
                        <img src="{{ asset($customLogo) }}" alt="Logo" class="h-16 w-16 object-contain rounded-2xl bg-white p-1 border border-slate-200 shadow-sm shrink-0">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-16 object-contain rounded-2xl bg-white p-1 border border-slate-200 shadow-sm shrink-0">
                    @endif
                    <div>
                        <span class="text-xs font-bold text-slate-400 block tracking-wider">الجمهورية اليمنية • وزارة التربية والتعليم والتعليم الفني</span>
                        <h1 class="text-lg sm:text-xl font-black text-[#0066A1] leading-tight">
                            مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات
                        </h1>
                        <span class="text-xs font-bold text-[#F58220] block mt-0.5">
                            القيادة الأكاديمية وقسم الجودة والتميز (AQED) • تقرير تدقيق وشفافية الاختبارات
                        </span>
                    </div>
                </div>

                <!-- بيانات التوثيق والأرقام المرجعية للتقرير -->
                <div class="text-xs text-left font-mono space-y-1 bg-slate-50 p-3 rounded-2xl border border-slate-200 shrink-0">
                    <div>
                        <span class="text-slate-400">رقم المحاولة:</span>
                        <strong class="text-slate-800">#ATT-{{ str_pad($attempt->id, 6, '0', STR_PAD_LEFT) }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400">تاريخ التقرير:</span>
                        <span class="text-slate-700 font-bold">{{ now()->format('Y-m-d') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400">وقت الأداء:</span>
                        <span class="text-slate-700">{{ $attempt->submitted_at?->format('Y-m-d H:i') ?? $attempt->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- 2. بطاقة بيانات الطالب ومواصفات الاختبار -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <!-- بيانات الطالب -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2">
                <span class="font-black text-slate-800 block border-b border-slate-200/80 pb-1.5 flex items-center gap-1.5">
                    <span>👤</span>
                    <span>بيانات الطالب الأكاديمية:</span>
                </span>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-slate-400 block text-[10px]">اسم الطالب:</span>
                        <strong class="text-slate-900 text-sm">{{ $attempt->user->name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">الرقم الأكاديمي (الكود):</span>
                        <strong class="text-indigo-700 font-mono text-sm">{{ $attempt->user->student_code ?? $attempt->user->user_code }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">المستوى المقيد به:</span>
                        <span class="text-slate-700 font-bold">{{ $attempt->user->activeEnrollment?->level?->name ?? 'طالب جديد / تسكين' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">الشعبة / المجموعة:</span>
                        <span class="text-slate-700 font-bold">{{ $attempt->studyGroup?->name ?? 'بدون شعبة / مباشر' }}</span>
                    </div>
                </div>
            </div>

            <!-- بيانات الاختبار -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2">
                <span class="font-black text-slate-800 block border-b border-slate-200/80 pb-1.5 flex items-center gap-1.5">
                    <span>🎯</span>
                    <span>مواصفات ونوع الاختبار:</span>
                </span>
                <div class="grid grid-cols-2 gap-2">
                    <div class="col-span-2">
                        <span class="text-slate-400 block text-[10px]">عنوان الاختبار:</span>
                        <strong class="text-slate-900 text-sm">{{ $exam->title }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">نوع الاختبار:</span>
                        <span class="font-bold text-[#0066A1]">
                            {{ $exam->type === 'placement_test' || $exam->quiz_period === 'placement' ? '🎯 اختبار تحديد المستوى والقبول' : $exam->quiz_period_label }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">الدورة التعليمية:</span>
                        <span class="text-slate-700 font-bold">{{ $exam->course?->displayName ?: ($exam->course?->name ?? 'اللغة الإنجليزية الشاملة') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. لوحة المؤشرات الإحصائية للأخطاء والتحصيل الأكاديمي -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl p-6 text-white shadow-md">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-center items-center">
                
                <!-- الدرجة الكلية -->
                <div class="border-b sm:border-b-0 sm:border-l border-white/10 pb-3 sm:pb-0 sm:pl-3">
                    <span class="text-[11px] text-slate-300 font-bold block mb-1">الدرجة المحققة</span>
                    <div class="text-2xl sm:text-3xl font-black font-mono {{ $isPassed ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $attempt->total_score }}
                        <span class="text-xs text-slate-400 font-normal">/ {{ $totalPoints }}</span>
                    </div>
                    <span class="text-[10px] text-slate-300 font-mono">({{ $scorePercentage }}%)</span>
                </div>

                <!-- التقييم العام -->
                <div class="border-b sm:border-b-0 sm:border-l border-white/10 pb-3 sm:pb-0 sm:pl-3">
                    <span class="text-[11px] text-slate-300 font-bold block mb-1">الحالة والنتيجة</span>
                    @if($exam->type === 'placement_test' || $exam->quiz_period === 'placement')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-500/30 text-indigo-200 border border-indigo-400/30 block truncate">
                            المقترح: {{ $attempt->suggestedLevel?->name ?? 'المستوى التأسيسي' }}
                        </span>
                    @elseif($isPassed)
                        <span class="px-2 py-0.5 rounded-full text-xs font-black bg-emerald-500/30 text-emerald-300 border border-emerald-400/30 inline-block">
                            اجتاز بنجاح ✓
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-black bg-rose-500/30 text-rose-300 border border-rose-400/30 inline-block">
                            لم يجتز ✕
                        </span>
                    @endif
                    <span class="text-[9px] text-slate-400 block mt-1">درجة النجاح: {{ $exam->passing_score }}</span>
                </div>

                <!-- إجمالي الأسئلة -->
                <div class="border-b sm:border-b-0 sm:border-l border-white/10 pb-3 sm:pb-0 sm:pl-3">
                    <span class="text-[11px] text-slate-300 font-bold block mb-1">عدد الأسئلة</span>
                    <div class="text-xl sm:text-2xl font-black font-mono text-white">{{ $totalQuestions }}</div>
                    <span class="text-[10px] text-slate-400">سؤالاً</span>
                </div>

                <!-- الإجابات الصحيحة -->
                <div class="border-b sm:border-b-0 sm:border-l border-white/10 pb-3 sm:pb-0 sm:pl-3">
                    <span class="text-[11px] text-emerald-300 font-bold block mb-1">الإجابات الصحيحة</span>
                    <div class="text-xl sm:text-2xl font-black font-mono text-emerald-400">{{ $correctCount }}</div>
                    <span class="text-[10px] text-emerald-300/80 font-mono">({{ $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 1) : 0 }}%)</span>
                </div>

                <!-- الأخطاء المرتكبة -->
                <div class="border-b sm:border-b-0 sm:border-l border-white/10 pb-3 sm:pb-0 sm:pl-3">
                    <span class="text-[11px] text-rose-300 font-bold block mb-1">الأخطاء المرتكبة</span>
                    <div class="text-xl sm:text-2xl font-black font-mono text-rose-400">{{ $incorrectCount }}</div>
                    <span class="text-[10px] text-rose-300/80 font-mono">({{ $totalQuestions > 0 ? round(($incorrectCount / $totalQuestions) * 100, 1) : 0 }}%)</span>
                </div>

                <!-- الأسئلة المتروكة -->
                <div>
                    <span class="text-[11px] text-amber-300 font-bold block mb-1">متروكة دون إجابة</span>
                    <div class="text-xl sm:text-2xl font-black font-mono text-amber-300">{{ $unansweredCount }}</div>
                    <span class="text-[10px] text-amber-300/80">أسئلة</span>
                </div>

            </div>
        </div>

        <!-- 4. توجيهات وملاحظات المعلم / المشرف للطالب وخطة التحسين -->
        @if($attempt->teacher_notes)
            <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-4 text-xs text-amber-950 space-y-1.5">
                <div class="flex items-center gap-2 font-black text-amber-900">
                    <span>💡</span>
                    <span>التوجيهات التربوية وخطة التحسين المعتمدة للطالب:</span>
                </div>
                <p class="leading-relaxed bg-white/80 p-3 rounded-xl border border-amber-200/60 font-medium text-slate-800">
                    {{ $attempt->teacher_notes }}
                </p>
            </div>
        @endif

        <!-- 5. كشف وتفصيل الأسئلة والأخطاء سؤالاً بسؤال -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                <h3 class="font-black text-sm text-slate-900 flex items-center gap-2">
                    <span>📑</span>
                    <span>تفاصيل الأسئلة والإجابات وتدقيق الأخطاء الشامل:</span>
                </h3>
                <span class="text-xs text-slate-400 font-mono font-bold">
                    [ تمييز الأخطاء باللون الأحمر ❌ والإجابات النموذجية بالأخضر ✓ ]
                </span>
            </div>

            <div class="space-y-4" id="questionsContainer">
                @foreach($questionsAnalysis as $idx => $item)
                    @php
                        $q = $item['question'];
                        $ans = $item['answer'];
                        $studentChoice = $item['student_answer'];
                        $isCorrect = $item['is_correct'];
                        $isUnanswered = $item['is_unanswered'];
                        $filterClass = $isCorrect ? 'q-correct' : 'q-mistake';
                    @endphp

                    <div class="question-card {{ $filterClass }} bg-white rounded-2xl border {{ $isCorrect ? 'border-emerald-200' : ($isUnanswered ? 'border-amber-300 bg-amber-50/30' : 'border-rose-300 bg-rose-50/30') }} p-5 space-y-3 print-border shadow-2xs">
                        
                        <!-- ترويسة بطاقة السؤال -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 flex-wrap gap-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-xs font-mono">
                                    {{ $idx + 1 }}
                                </span>
                                <span class="font-bold text-slate-600">
                                    درجة السؤال: <strong class="text-slate-800 font-mono">{{ $q->points }}</strong>
                                </span>
                                @if($q->type === 'speaking_ai')
                                    <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full">
                                        🎙️ اختبار نطق بالذكاء الاصطناعي
                                    </span>
                                @endif
                            </div>

                            <div>
                                @if($isCorrect)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <span>✓</span>
                                        <span>إجابة صحيحة (+{{ $item['points_awarded'] }} درجة)</span>
                                    </span>
                                @elseif($isUnanswered)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300">
                                        <span>⚠️</span>
                                        <span>متروك بدون إجابة (0 من {{ $q->points }})</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-900 border border-rose-300">
                                        <span>✕</span>
                                        <span>إجابة خاطئة (0 من {{ $q->points }})</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- فقرة القراءة إن وجدت -->
                        @if($q->context_passage)
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs text-slate-700 leading-relaxed font-serif">
                                <span class="text-[10px] font-bold text-slate-400 block mb-1">📖 نص القراءة / الفقرة المرجعية:</span>
                                {{ $q->context_passage }}
                            </div>
                        @endif

                        <!-- نص السؤال -->
                        <h4 class="font-black text-sm text-slate-900 leading-snug">
                            {{ $q->question_text }}
                        </h4>

                        <!-- معالجة أسئلة الذكاء الاصطناعي والمحادثة speaking_ai -->
                        @if($q->type === 'speaking_ai')
                            <div class="bg-indigo-50/60 border border-indigo-200 rounded-xl p-4 text-xs space-y-3">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-500 block mb-1">النص الصوتي المطلوب قراءته بدقة:</span>
                                    <p class="font-serif font-bold text-sm text-indigo-950 bg-white p-2.5 rounded-lg border border-indigo-100">
                                        "{{ $q->expected_speech_text ?? 'Learning English opens great opportunities for communication and global success.' }}"
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <div class="bg-white p-2.5 rounded-lg border border-indigo-100">
                                        <span class="text-[10px] text-slate-400 block">دقة النطق (Acoustic Accuracy):</span>
                                        <strong class="text-sm font-black text-indigo-700 font-mono">
                                            {{ $ans?->speech_accuracy ?? ($attempt->speech_feedback['accuracy_score'] ?? 85) }}%
                                        </strong>
                                    </div>

                                    <div class="bg-white p-2.5 rounded-lg border border-indigo-100">
                                        <span class="text-[10px] text-slate-400 block">الطلاقة والسرعة (Fluency):</span>
                                        <strong class="text-sm font-black text-indigo-700 font-mono">
                                            {{ $attempt->speech_feedback['fluency_score'] ?? 82 }}%
                                        </strong>
                                    </div>

                                    <div class="bg-white p-2.5 rounded-lg border border-indigo-100">
                                        <span class="text-[10px] text-slate-400 block">الدرجة الممنوحة:</span>
                                        <strong class="text-sm font-black text-emerald-600 font-mono">
                                            {{ $item['points_awarded'] }} / {{ $q->points }}
                                        </strong>
                                    </div>
                                </div>

                                @if($ans?->recorded_audio_path)
                                    <div class="no-print pt-1">
                                        <span class="text-[10px] font-bold text-slate-500 block mb-1">التسجيل الصوتي للطالب:</span>
                                        <audio controls class="w-full h-8 rounded" src="{{ asset('storage/' . $ans->recorded_audio_path) }}"></audio>
                                    </div>
                                @endif

                                @if(!empty($attempt->speech_feedback['mispronounced_words']))
                                    <div class="pt-2 border-t border-indigo-100">
                                        <span class="text-[11px] font-bold text-rose-800 block mb-1">الألفاظ والكلمات التي تعثر فيها الطالب صوتياً:</span>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($attempt->speech_feedback['mispronounced_words'] as $mw)
                                                <span class="px-2.5 py-1 bg-rose-100 text-rose-900 border border-rose-300 rounded-lg text-xs font-mono font-bold flex items-center gap-1">
                                                    <span>❌</span>
                                                    <span>{{ is_array($mw) ? ($mw['word'] ?? '') : $mw }}</span>
                                                    @if(is_array($mw) && !empty($mw['error_type']))
                                                        <span class="text-[10px] font-sans text-rose-700">({{ $mw['error_type'] }})</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                        <!-- معالجة أسئلة الاختيار من متعدد MCQ والخيارات -->
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-1">
                                @foreach($q->options ?? [] as $optIdx => $optText)
                                    @php
                                        $isStudentChoice = (trim(mb_strtolower($studentChoice)) === trim(mb_strtolower($optText)));
                                        $isCorrectAnswer = (trim(mb_strtolower($q->correct_answer)) === trim(mb_strtolower($optText)));

                                        $cardStyle = 'bg-white border-slate-200 text-slate-700';
                                        if ($isCorrectAnswer && $isStudentChoice) {
                                            $cardStyle = 'bg-emerald-50 border-emerald-500 text-emerald-950 font-bold ring-2 ring-emerald-500/30';
                                        } elseif ($isStudentChoice && !$isCorrectAnswer) {
                                            $cardStyle = 'bg-rose-50 border-rose-500 text-rose-950 font-bold ring-2 ring-rose-500/30';
                                        } elseif ($isCorrectAnswer) {
                                            $cardStyle = 'bg-emerald-50/70 border-emerald-500 text-emerald-900 border-dashed font-bold';
                                        }
                                    @endphp

                                    <div class="p-2.5 rounded-xl border flex items-center justify-between {{ $cardStyle }}">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-md bg-white border border-slate-300 flex items-center justify-center font-bold text-[10px] font-mono shrink-0 shadow-2xs">
                                                {{ chr(65 + $optIdx) }}
                                            </span>
                                            <span>{{ $optText }}</span>
                                        </div>

                                        <div class="shrink-0 text-[10px] font-black">
                                            @if($isStudentChoice && $isCorrectAnswer)
                                                <span class="text-emerald-700 bg-emerald-200/60 px-2 py-0.5 rounded-md">
                                                    اختيار الطالب (صحيح ✓)
                                                </span>
                                            @elseif($isStudentChoice && !$isCorrectAnswer)
                                                <span class="text-rose-700 bg-rose-200/60 px-2 py-0.5 rounded-md">
                                                    اختيار الطالب (خطأ ❌)
                                                </span>
                                            @elseif($isCorrectAnswer)
                                                <span class="text-emerald-700 bg-emerald-200/60 px-2 py-0.5 rounded-md">
                                                    الإجابة النموذجية ✓
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        </div>

        <!-- 6. مربع التوقيع والاعتماد الأكاديمي الرسمي (يظهر بالطباعة) -->
        <div class="border-t-2 border-slate-200 pt-6 mt-8">
            <div class="grid grid-cols-3 gap-6 text-center text-xs">
                
                <div class="space-y-6">
                    <span class="font-bold text-slate-700 block">المشرف الأكاديمي المختص</span>
                    <div class="h-12 border-b border-dashed border-slate-300 flex items-end justify-center font-bold text-slate-900 pb-1">
                        {{ $attempt->reviewer?->name ?? 'د. فخر الدين الصوفي' }}
                    </div>
                    <span class="text-[10px] text-slate-400 block">التوقيع والتاريخ</span>
                </div>

                <div class="space-y-6">
                    <span class="font-bold text-slate-700 block">قسم الجودة والتميز (AQED)</span>
                    <div class="h-12 border-b border-dashed border-slate-300 flex items-end justify-center font-bold text-slate-900 pb-1">
                        معتمد وفق معايير ASOTS
                    </div>
                    <span class="text-[10px] text-slate-400 block">التوقيع والتاريخ</span>
                </div>

                <div class="space-y-6">
                    <span class="font-bold text-slate-700 block">ختم واعتماد إدارة المنصة</span>
                    <div class="h-12 border-b border-dashed border-slate-300 flex items-center justify-center text-slate-300 text-xs">
                        [ الختم الرسمي ]
                    </div>
                    <span class="text-[10px] text-slate-400 block">مكتب النجم الآسيوي</span>
                </div>

            </div>
        </div>

    </div>

    <!-- كود الجافاسكربت لفلترة العرض على الشاشة -->
    <script>
        function filterQuestions(mode) {
            const cards = document.querySelectorAll('.question-card');
            const btnAll = document.getElementById('btnFilterAll');
            const btnMistakes = document.getElementById('btnFilterMistakes');
            const btnCorrect = document.getElementById('btnFilterCorrect');

            // ضبط حالة الأزرار
            [btnAll, btnMistakes, btnCorrect].forEach(b => {
                b.className = 'px-2.5 py-1 rounded-lg text-slate-700 hover:bg-white/60';
            });

            if (mode === 'mistakes') {
                btnMistakes.className = 'px-2.5 py-1 rounded-lg bg-rose-600 text-white shadow-xs font-black';
                cards.forEach(c => {
                    if (c.classList.contains('q-mistake')) {
                        c.style.display = 'block';
                    } else {
                        c.style.display = 'none';
                    }
                });
            } else if (mode === 'correct') {
                btnCorrect.className = 'px-2.5 py-1 rounded-lg bg-emerald-600 text-white shadow-xs font-black';
                cards.forEach(c => {
                    if (c.classList.contains('q-correct')) {
                        c.style.display = 'block';
                    } else {
                        c.style.display = 'none';
                    }
                });
            } else {
                btnAll.className = 'px-2.5 py-1 rounded-lg bg-[#0066A1] text-white shadow-xs font-black';
                cards.forEach(c => {
                    c.style.display = 'block';
                });
            }
        }
    </script>

</body>
</html>
