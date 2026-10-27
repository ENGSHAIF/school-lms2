<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إستمارة تسجيل طالب - {{ $application->name_ar }} - ASOTS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: white; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 min-h-screen">

    <div class="max-w-3xl mx-auto mb-4 no-print flex items-center justify-between">
        <a href="{{ route('admin.student_applications.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
            <span>←</span>
            <span>العودة لسجل الاستمارات</span>
        </a>
        <button onclick="window.print()" class="bg-[#0066A1] hover:bg-[#004e7c] text-white font-black text-xs px-6 py-2.5 rounded-xl shadow transition">
            🖨️ طباعة الاستمارة الرسمية
        </button>
    </div>

    <!-- وثيقة الاستمارة الرسمية المعتمدة -->
    <div class="max-w-3xl mx-auto bg-white border-2 border-slate-300 p-8 sm:p-12 shadow-xl rounded-2xl relative">
        
        <!-- الترويسة الرسمية -->
        <div class="border-b-2 border-[#0066A1] pb-6 mb-6 flex justify-between items-center gap-4">
            <div class="space-y-1">
                <span class="text-[10px] font-black text-[#F58220] tracking-wider block">THE ASIAN STAR OFFICE (ASOTS)</span>
                <h1 class="text-base sm:text-lg font-black text-slate-900">
                    مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات – ASOTS
                </h1>
                <p class="text-xs text-slate-500">إشراف: أ.د. فخر الدين ناجي ردمان عبده الصوفي</p>
            </div>
            
            <div class="text-left shrink-0">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center p-2 shadow-inner">
                    <img src="{{ asset('images/logo.png') }}" alt="ASOTS" class="max-h-full max-w-full object-contain">
                </div>
            </div>
        </div>

        <div class="text-center my-6 space-y-1">
            <h2 class="text-xl font-black text-slate-900 inline-block bg-slate-100 px-6 py-2 rounded-xl border border-slate-300">
                📋 إستمارة التسجيل الرسمية
            </h2>
            <div class="flex items-center justify-center gap-3 text-xs text-slate-500 font-mono mt-2">
                <span>طلب رقم: #{{ $appId = str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span>•</span>
                <span>تاريخ التقديم: {{ $application->created_at ? $application->created_at->format('Y-m-d') : '' }}</span>
            </div>
        </div>

        <!-- بيانات الطالب الأساسية -->
        <div class="space-y-4 text-xs sm:text-sm">
            <div class="text-xs font-black text-[#0066A1] border-b pb-1">🔸️ بيانات المتقدم / الطالب:</div>

            <table class="w-full border-collapse border border-slate-300 text-xs">
                <tr>
                    <td class="bg-slate-50 p-2.5 font-bold border border-slate-300 w-1/3">الاسم الكامل بالعربية:</td>
                    <td class="p-2.5 font-black border border-slate-300 text-slate-900">{{ $application->name_ar }}</td>
                </tr>
                <tr>
                    <td class="bg-slate-50 p-2.5 font-bold border border-slate-300">الاسم بالإنجليزية (مطابق للجواز):</td>
                    <td class="p-2.5 font-medium border border-slate-300 font-sans" dir="ltr">{{ $application->name_en ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="bg-slate-50 p-2.5 font-bold border border-slate-300">مكان وتاريخ الميلاد:</td>
                    <td class="p-2.5 border border-slate-300 font-bold">
                        {{ $application->birth_place ?: '—' }}
                        @if($application->birth_date)
                            ({{ $application->birth_date->format('Y-m-d') }})
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="bg-slate-50 p-2.5 font-bold border border-slate-300">المستوى التعليمي للطالب:</td>
                    <td class="p-2.5 border border-slate-300 font-bold">{{ $application->educational_level ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="bg-slate-50 p-2.5 font-bold border border-slate-300">رقم الهاتف المخصص للدراسة (واتساب):</td>
                    <td class="p-2.5 border border-slate-300 font-mono font-bold" dir="ltr">{{ $application->study_phone_whatsapp }}</td>
                </tr>
                <tr>
                    <td class="bg-slate-50 p-2.5 font-bold border border-slate-300">رقم ولي الأمر:</td>
                    <td class="p-2.5 border border-slate-300 font-mono" dir="ltr">{{ $application->parent_phone ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="bg-slate-50 p-2.5 font-bold border border-slate-300">هل تحتاج لاختبار تحديد مستوى؟</td>
                    <td class="p-2.5 border border-slate-300 font-bold">
                        {{ $application->needs_placement_test ? 'نعم (يحتاج لاختبار تحديد مستوى)' : 'لا (يبدأ من التمهيدي أو محدد مسبقاً)' }}
                    </td>
                </tr>
            </table>

            <!-- الملاحظة الهامة -->
            <div class="bg-amber-50 border border-amber-300 p-3 rounded-xl text-[11px] text-amber-950">
                <strong>⚠️ ملاحظة هامة:</strong>
                تم التأكيد على مطابقة البيانات للوثائق الرسمية (جواز السفر أو الهوية) لضمان إصدار الشهادات الأكاديمية والشهائد المعنية بدقة واعتماد رسمي.
            </div>

            <!-- بيانات خاصة بالإدارة -->
            <div class="border-2 border-slate-400 rounded-xl p-4 space-y-3 bg-slate-50/50">
                <div class="text-xs font-black text-slate-900 border-b border-slate-300 pb-1 flex justify-between items-center">
                    <span>📑 بيانات خاصة بالإدارة (يُعبئها القسم المختص):</span>
                    <span class="text-[10px] text-slate-500 font-mono">حالة الطلب: {{ $application->status_label }}</span>
                </div>

                <div class="grid grid-cols-3 gap-3 text-xs">
                    <div class="bg-white border border-slate-300 p-2.5 rounded-lg">
                        <span class="text-[10px] text-slate-400 block">Term:</span>
                        <strong class="text-sm font-black text-[#0066A1]">{{ $application->term }}</strong>
                    </div>

                    <div class="bg-white border border-slate-300 p-2.5 rounded-lg">
                        <span class="text-[10px] text-slate-400 block">Academic Number:</span>
                        <strong class="text-sm font-mono font-bold text-slate-900">{{ $application->academic_number ?: 'قيد التوليد' }}</strong>
                    </div>

                    <div class="bg-white border border-slate-300 p-2.5 rounded-lg">
                        <span class="text-[10px] text-slate-400 block">Course:</span>
                        <strong class="text-xs font-bold text-slate-900">{{ $application->course_assigned ?: 'قيد التسكين' }}</strong>
                    </div>
                </div>

                <div class="bg-sky-50 border border-sky-200 p-2 rounded-lg text-xs text-sky-950">
                    <strong>🕒 مدة البرنامج:</strong>
                    <span>{{ $application->program_duration }}</span>
                </div>
            </div>

            <!-- التوقيعات والأختام الرسمية -->
            <div class="pt-8 grid grid-cols-3 gap-4 text-center text-xs">
                <div>
                    <span class="block text-slate-400 text-[10px] mb-8">توقيع الطالب / ولي الأمر</span>
                    <div class="border-t border-slate-400 pt-1 font-bold">{{ $application->name_ar }}</div>
                </div>

                <div>
                    <span class="block text-slate-400 text-[10px] mb-8">إدارة القبول والتسجيل</span>
                    <div class="border-t border-slate-400 pt-1 font-bold">قسم شؤون الطلاب (AQED)</div>
                </div>

                <div>
                    <span class="block text-slate-400 text-[10px] mb-8">اعتماد الإدارة والختم الرسمي</span>
                    <div class="border-t border-slate-400 pt-1 font-bold">مكتب النجم الآسيوي (ASOTS)</div>
                </div>
            </div>

            <div class="text-center pt-6 text-[11px] text-slate-400 border-t border-slate-200">
                شكراً لثقتكم بنا .. فأنتم بأعيننا 🤍 • مكتب النجم الآسيوي للترجمة والتدريب والاستشارات • صنعاء
            </div>
        </div>

    </div>

</body>
</html>
