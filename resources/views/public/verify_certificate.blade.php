<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من صحة ومصداقية الشهادة الأكاديمية - ASOTS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0b0f19; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex flex-col justify-between p-4 sm:p-8">

    <div class="max-w-3xl mx-auto w-full my-auto space-y-6">

        <!-- شعار المؤسسة الرسمي -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-400/30 text-amber-400 text-xs font-bold">
                <span>🛡️</span>
                <span>البوابة الرسمية للتحقق من الشهائد والوثائق الصادرة</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white">
                The Asian Star Office for Translation, Training, & Consultancy Services
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 font-medium">
                مكتب النجم الآسيوي للترجمة والتدريب وخدمات الاستشارات الأكاديمية (ASOTS)
            </p>
        </div>

        @if($certificate)
            <!-- بطاقة التأكيد والنجاح للشهادة الصحيحة -->
            <div class="bg-slate-900 border-2 border-emerald-500/40 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <!-- شارة التوثيق الأخضر -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl border border-emerald-500/30">
                            ✓
                        </div>
                        <div>
                            <span class="text-xs text-emerald-400 font-bold block">شهادة معتمدة وموثقة رسمياً</span>
                            <h2 class="text-lg font-black text-white">الوثيقة الأكاديمية صحيحة وسارية</h2>
                        </div>
                    </div>

                    <div class="text-left font-mono">
                        <span class="text-[11px] text-slate-400 block font-bold">رقم الشهادة المعتمد</span>
                        <strong class="text-sm font-black text-amber-400">{{ $certificate->certificate_number }}</strong>
                    </div>
                </div>

                <!-- بيانات الطالب والشهادة -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="bg-slate-800/60 p-4 rounded-2xl border border-slate-700/60 space-y-1">
                        <span class="text-slate-400 font-bold block">اسم الطالب (عربي):</span>
                        <strong class="text-base font-black text-white block">{{ $certificate->student_name_ar }}</strong>
                        <span class="text-slate-400 font-mono text-[11px] block">{{ $certificate->student_name_en }}</span>
                    </div>

                    <div class="bg-slate-800/60 p-4 rounded-2xl border border-slate-700/60 space-y-1">
                        <span class="text-slate-400 font-bold block">الدورة / البرنامج المعتمد:</span>
                        <strong class="text-sm font-black text-amber-300 block">{{ $certificate->course_name_ar }}</strong>
                        <span class="text-slate-400 font-sans text-[11px] block">{{ $certificate->course_name_en }}</span>
                    </div>
                </div>

                <!-- تفاصيل الدرجات والمعدل والساعات -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center font-mono">
                    <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-700">
                        <span class="text-[10px] text-slate-400 block font-bold">الساعات التدريبية</span>
                        <strong class="text-sm font-black text-white">{{ $certificate->hours }} ساعة</strong>
                    </div>
                    <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-700">
                        <span class="text-[10px] text-slate-400 block font-bold">المجموع الكلي</span>
                        <strong class="text-sm font-black text-indigo-400">{{ $certificate->total_score }} / 100</strong>
                    </div>
                    <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-700">
                        <span class="text-[10px] text-slate-400 block font-bold">النسبة المئوية</span>
                        <strong class="text-sm font-black text-emerald-400">{{ $certificate->percentage }}%</strong>
                    </div>
                    <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-700">
                        <span class="text-[10px] text-slate-400 block font-bold">التقدير العام</span>
                        <strong class="text-xs font-black text-amber-300">{{ $certificate->grade_letter }}</strong>
                    </div>
                </div>

                <!-- التواريخ والاعتماد -->
                <div class="pt-4 border-t border-slate-800 flex flex-wrap items-center justify-between text-xs text-slate-400 font-medium gap-2">
                    <div>
                        <span>تاريخ الإصدار: </span>
                        <strong class="text-slate-200 font-mono">{{ $certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '—' }}</strong>
                    </div>
                    <div class="flex items-center gap-1.5 text-emerald-400 font-bold">
                        <span>🛡️</span>
                        <span>مصدقة إلكترونياً برقم تحقق فريد</span>
                    </div>
                </div>
            </div>
        @else
            <!-- رسالة عدم العثور على الشهادة -->
            <div class="bg-slate-900 border-2 border-rose-500/40 rounded-3xl p-8 text-center space-y-4 shadow-2xl">
                <div class="w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-3xl mx-auto border border-rose-500/30">
                    ✕
                </div>
                <h2 class="text-lg font-black text-white">لم يتم العثور على شهادة بهذا الرقم</h2>
                <p class="text-xs sm:text-sm text-slate-400 max-w-md mx-auto">
                    الرقم التسلسلي المدخل <strong class="text-amber-400 font-mono">«{{ $serial }}»</strong> غير مسجل في قاعدة بيانات الشهائد الأكاديمية المعتمدة لدى مكتب النجم الآسيوي (ASOTS).
                </p>
                <div class="pt-2">
                    <a href="{{ route('home') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition inline-block">
                        الرجوع للصفحة الرئيسية
                    </a>
                </div>
            </div>
        @endif

        <!-- التذييل الرسمي -->
        <div class="text-center text-[11px] text-slate-500 font-mono space-y-1">
            <p>The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)</p>
            <p>© {{ date('Y') }} جميع الحقوق محفوظة • بوابة التحقق الأكاديمي الرقمية</p>
        </div>

    </div>

</body>
</html>
