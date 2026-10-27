<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الشهادة الأكاديمية المعتمدة - ASOTS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,700&family=Cairo:wght@400;600;700;800;900&family=Cinzel:wght@600;700;800;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,600;0,800;1,600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0f172a; margin: 0; padding: 0; }
        .font-amiri { font-family: 'Amiri', serif; }
        .font-cinzel { font-family: 'Cinzel', serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-script { font-family: 'Great Vibes', cursive; }

        .cert-container {
            width: 297mm;
            min-height: 209mm;
            max-height: 209mm;
            margin: 0 auto;
            background: #fffdf9;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }

        .cert-gold-border {
            border: 4px solid #b48c36;
            outline: 2px solid #dfc17b;
            outline-offset: -10px;
        }

        .cert-inner-frame {
            border: 1px solid rgba(180, 140, 54, 0.4);
        }

        .cert-corner {
            position: absolute;
            width: 50px;
            height: 50px;
            pointer-events: none;
            z-index: 10;
        }
        .corner-tl { top: 14px; left: 14px; border-top: 3px solid #b48c36; border-left: 3px solid #b48c36; }
        .corner-tr { top: 14px; right: 14px; border-top: 3px solid #b48c36; border-right: 3px solid #b48c36; }
        .corner-bl { bottom: 14px; left: 14px; border-bottom: 3px solid #b48c36; border-left: 3px solid #b48c36; }
        .corner-br { bottom: 14px; right: 14px; border-bottom: 3px solid #b48c36; border-right: 3px solid #b48c36; }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; margin: 0 !important; padding: 0 !important; }
            .cert-page {
                page-break-after: always;
                page-break-inside: avoid;
                margin: 0 !important;
                padding: 0 !important;
                height: 100vh !important;
            }
            .cert-container {
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body class="py-6 px-4">

    <!-- شريط الإجراءات العلوي (إخفاء عند الطباعة) -->
    <div class="no-print max-w-[297mm] mx-auto mb-6 bg-slate-900 border border-slate-800 text-white p-4 rounded-2xl shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.exams_management.index', ['tab' => 'certificates', 'group_id' => $group?->id]) }}"
               class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                <span>←</span>
                <span>الرجوع للمركز</span>
            </a>
            <span class="text-xs text-slate-400 font-bold">|</span>
            <div>
                <span class="text-xs font-black text-amber-400 block">
                    عرض وطباعة الشهائد الأكاديمية الملكية ({{ $certificates->count() }} شهادة)
                </span>
                <span class="text-[11px] text-slate-400">
                    تم ضبط القياسات بدقة A4 Landscape للطباعة بجودة فائقة بدون حواف زائدة.
                </span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs sm:text-sm rounded-xl shadow-lg transition flex items-center gap-2">
                <span>🖨️</span>
                <span>طباعة كافة الشهائد / Save PDF</span>
            </button>
        </div>
    </div>

    <!-- تكرار طباعة الشهائد (تدعم شهادة فردية أو طباعة المجموعة دفعة واحدة) -->
    @foreach($certificates as $index => $cert)
        <div class="cert-page mb-8 print:mb-0">
            <div class="cert-container cert-gold-border rounded-xl shadow-2xl p-8 relative flex flex-col justify-between">

                <!-- أركان الزخرفة الذهبية الملكية -->
                <div class="cert-corner corner-tl"></div>
                <div class="cert-corner corner-tr"></div>
                <div class="cert-corner corner-bl"></div>
                <div class="cert-corner corner-br"></div>

                <!-- علامة مائية ناعمة في الخلفية -->
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
                    <span class="font-cinzel text-[160px] font-black text-slate-900 tracking-widest">ASOTS</span>
                </div>

                <!-- الإطار الداخلي الرقيق -->
                <div class="cert-inner-frame absolute inset-4 rounded pointer-events-none"></div>

                <!-- 1. رأس الشهادة الرسمي والترويسة الثنائية -->
                <div class="relative z-10 text-center space-y-1 pt-1">
                    <div class="flex items-center justify-center gap-3">
                        <span class="h-[1px] w-16 bg-gradient-to-r from-transparent to-amber-600"></span>
                        <span class="text-amber-600 font-bold text-sm tracking-widest font-cinzel">ASIAN STAR ACADEMY & CONSULTANCY</span>
                        <span class="h-[1px] w-16 bg-gradient-to-l from-transparent to-amber-600"></span>
                    </div>

                    <h1 class="font-cinzel text-base sm:text-lg font-black text-slate-900 tracking-wider uppercase">
                        The Asian Star Office for Translation, Training, & Consultancy Services
                    </h1>
                    <h2 class="font-amiri text-base font-bold text-slate-700">
                        مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)
                    </h2>

                    <!-- شريط العنوان للشهادة -->
                    <div class="pt-2">
                        <div class="inline-block relative">
                            <span class="text-xs uppercase tracking-[0.3em] font-cinzel font-black text-amber-800 block">
                                {{ match($cert->type) {
                                    'diploma' => 'OFFICIAL DIPLOMA OF EXCELLENCE',
                                    'level' => 'LEVEL COMPLETION CERTIFICATE',
                                    default => 'CERTIFICATE OF ACHIEVEMENT & COMPLETION',
                                } }}
                            </span>
                            <span class="font-amiri text-2xl sm:text-3xl font-black text-amber-900 block leading-tight">
                                {{ match($cert->type) {
                                    'diploma' => 'شهادة تخرج ودبلوم معتمد',
                                    'level' => 'شهادة إتمام مستوى دراسي',
                                    default => 'شهادة إتمام وتفوق دورة تدريبية',
                                } }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2. جسم الشهادة والنصوص المركزية -->
                <div class="relative z-10 text-center my-auto space-y-3 py-2">
                    <p class="font-amiri text-sm sm:text-base text-slate-600 font-bold">
                        يشهد مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات بأن الطالب / الطالبة:
                    </p>
                    <p class="font-cinzel text-xs text-slate-400 tracking-widest">
                        This is to proudly certify that
                    </p>

                    <!-- اسم الطالب المكرّم بالخط الملكي -->
                    <div class="py-1">
                        <div class="text-2xl sm:text-4xl font-amiri font-black text-slate-950 tracking-wide">
                            {{ $cert->student_name_ar }}
                        </div>
                        <div class="text-base sm:text-xl font-cinzel font-bold text-amber-900 tracking-wider mt-0.5">
                            {{ $cert->student_name_en }}
                        </div>
                        <div class="w-48 h-[2px] bg-gradient-to-r from-transparent via-amber-600 to-transparent mx-auto mt-2"></div>
                    </div>

                    <p class="font-amiri text-sm sm:text-base text-slate-700 max-w-3xl mx-auto leading-relaxed">
                        قد اجتاز(ت) بنجاح واقتدار كافة متطلبات الدورة التدريبية المقررة بعنوان:
                        <strong class="text-slate-950 font-black text-base sm:text-lg block mt-1">
                            « {{ $cert->course_name_ar }} »
                        </strong>
                        <span class="text-xs font-cinzel text-slate-600 font-bold block mt-0.5">
                            ({{ $cert->course_name_en }})
                        </span>
                    </p>

                    <!-- تفاصيل الساعات والتقدير والنسبة المئوية -->
                    <div class="flex items-center justify-center gap-6 pt-2 font-mono text-xs">
                        <div class="bg-amber-50/80 border border-amber-300 px-4 py-1.5 rounded-xl text-center">
                            <span class="text-[10px] text-slate-500 font-bold block">الساعات المعتمدة</span>
                            <strong class="text-slate-900 text-sm font-black">{{ $cert->hours }} Hours</strong>
                        </div>
                        <div class="bg-amber-50/80 border border-amber-300 px-4 py-1.5 rounded-xl text-center">
                            <span class="text-[10px] text-slate-500 font-bold block">النسبة المئوية</span>
                            <strong class="text-emerald-800 text-sm font-black">{{ $cert->percentage }}%</strong>
                        </div>
                        <div class="bg-amber-50/80 border border-amber-300 px-4 py-1.5 rounded-xl text-center">
                            <span class="text-[10px] text-slate-500 font-bold block">التقدير العام</span>
                            <strong class="text-amber-900 text-sm font-black font-amiri">{{ $cert->grade_letter }}</strong>
                        </div>
                    </div>
                </div>

                <!-- 3. أسفل الشهادة: الختم الذهبي، الباركود، والتواقيع المعتمدة -->
                <div class="relative z-10 pt-2 border-t border-amber-300/60">
                    <div class="grid grid-cols-4 items-end gap-4 text-center">

                        <!-- التوقيع 1: مدرب الدورة -->
                        <div class="space-y-4">
                            <div class="font-script text-2xl text-slate-800 transform -rotate-3 select-none">
                                {{ $cert->studyGroup?->teacher?->name ?: 'Academic Faculty' }}
                            </div>
                            <div class="border-t border-slate-400 pt-1">
                                <span class="font-bold text-xs text-slate-900 block font-amiri">مدرب الدورة المعتمد</span>
                                <span class="text-[10px] text-slate-500 font-cinzel block">Lead Course Instructor</span>
                            </div>
                        </div>

                        <!-- الميدالية الذهبية والشعار الرسمي -->
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-amber-600 via-amber-400 to-amber-600 p-[2px] shadow-md flex items-center justify-center">
                                <div class="w-full h-full rounded-full bg-white border border-amber-500 flex items-center justify-center p-1">
                                    <img src="{{ asset('images/logo.png') }}" alt="ASOTS" class="w-12 h-12 object-contain">
                                </div>
                            </div>
                            <span class="text-[9px] text-slate-400 font-mono mt-1">VERIFIED ACCREDITED</span>
                        </div>

                        <!-- كود التحقق الرقمي والباركود QR -->
                        <div class="flex flex-col items-center justify-center">
                            <div class="p-1 bg-white border border-slate-300 rounded-lg shadow-sm">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=65x65&data={{ urlencode($cert->verification_url) }}"
                                     alt="QR Verification"
                                     class="w-14 h-14 object-contain">
                            </div>
                            <span class="font-mono text-[9px] font-bold text-slate-600 mt-1 block">
                                {{ $cert->certificate_number }}
                            </span>
                            <span class="text-[8px] text-slate-400">امسح للتحقق الفوري</span>
                        </div>

                        <!-- التوقيع 2: المدير العام والأكاديمي -->
                        <div class="space-y-4">
                            <div class="font-script text-2xl text-slate-800 transform rotate-2 select-none">
                                ASOTS Directorate
                            </div>
                            <div class="border-t border-slate-400 pt-1">
                                <span class="font-bold text-xs text-slate-900 block font-amiri">المدير الأكاديمي والتوثيق</span>
                                <span class="text-[10px] text-slate-500 font-cinzel block">Academic Dean & Registrar</span>
                            </div>
                        </div>

                    </div>

                    <!-- شريط رقم الشهادة وتاريخ الإصدار السفلي -->
                    <div class="mt-3 pt-1 border-t border-slate-200 flex items-center justify-between text-[10px] font-mono text-slate-500 px-2">
                        <span>تاريخ الإصدار: <strong>{{ $cert->issue_date ? $cert->issue_date->format('Y/m/d') : date('Y/m/d') }}</strong> ({{ $cert->issue_date ? $cert->issue_date->format('F d, Y') : date('F d, Y') }})</span>
                        <span class="font-bold text-slate-700">رابط التحقق: <span class="text-indigo-600">{{ $cert->verification_url }}</span></span>
                        <span>ASOTS ACADEMIC REGISTRY • PAGE {{ $index + 1 }} OF {{ $certificates->count() }}</span>
                    </div>
                </div>

            </div>
        </div>
    @endforeach

</body>
</html>
