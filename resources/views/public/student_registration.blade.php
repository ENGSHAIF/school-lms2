@extends('layouts.app', ['title' => 'إستمارة التسجيل الرسمية - ' . config('asots.name_ar') . ' (ASOTS)'])

@section('content')
<div class="space-y-8 max-w-4xl mx-auto my-6">

    <!-- رسالة النجاح وتأكيد الإرسال -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-emerald-500/15 via-teal-500/15 to-emerald-500/15 border-2 border-emerald-500/40 p-6 sm:p-8 rounded-3xl shadow-lg space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl font-black shrink-0">
                    ✅
                </div>
                <div>
                    <h3 class="font-black text-emerald-950 text-base sm:text-lg">تم استلام طلب تسجيلك بنجاح!</h3>
                    <p class="text-xs text-emerald-800 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>

            @if(session('whatsapp_url'))
                <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                    <a href="{{ session('whatsapp_url') }}" target="_blank" rel="noopener noreferrer" 
                        class="w-full sm:w-auto flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs sm:text-sm py-3.5 px-6 rounded-2xl text-center shadow-lg transition flex items-center justify-center gap-2">
                        <span>📲 إرسال نسخة الاستمارة إلى واتساب القبول والتسجيل فوراً</span>
                        <span>💬</span>
                    </a>

                    <button type="button" onclick="window.print()" class="w-full sm:w-auto bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs py-3.5 px-6 rounded-2xl shadow-sm transition flex items-center justify-center gap-1.5">
                        <span>🖨️ طباعة الاستمارة</span>
                    </button>
                </div>
            @endif
        </div>
    @endif

    <!-- بطاقة استمارة التسجيل الرسمية الكبرى -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden print:border-none print:shadow-none">
        
        <!-- رأس الاستمارة الرسمي -->
        <div class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white p-8 sm:p-10 text-center relative overflow-hidden">
            <div class="relative z-10 space-y-3">
                <span class="inline-block text-[11px] font-black uppercase text-[#F58220] bg-white/10 px-4 py-1.5 rounded-full border border-white/20">
                    ⭐ The Asian Star Office for Translation, Training, & Consultancy Services
                </span>

                <h1 class="text-2xl sm:text-3xl font-black tracking-tight leading-snug">
                    مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات – ASOTS
                </h1>

                <div class="inline-flex items-center gap-2 bg-[#F58220] text-slate-950 px-6 py-2 rounded-2xl font-black text-sm sm:text-base shadow-lg">
                    <span>📋</span>
                    <span>إستمارة التسجيل</span>
                </div>

                <p class="text-xs sm:text-sm text-sky-100 font-medium pt-1">
                    🔸️ يرجى ملء البيانات التالية بدقة ومطابقتها للوثائق الرسمية
                </p>
            </div>

            <!-- علامة مائية خلفية -->
            <span class="text-8xl opacity-5 absolute -left-6 -bottom-6 font-black font-sans select-none">ASOTS</span>
        </div>

        <!-- جسم الاستمارة والنموذج التفاعلي -->
        <form action="{{ route('student.registration.store') }}" method="POST" id="studentRegistrationForm" class="p-6 sm:p-10 space-y-8">
            @csrf

            <!-- 1. البيانات الشخصية والتعليمية -->
            <div class="space-y-6">
                
                <!-- الاسم بالعربية والاسم بالإنجليزية -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-black text-slate-900 text-xs sm:text-sm mb-1.5">
                            * الاسم الكامل بالعربية: <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name_ar" id="input_name_ar" required 
                            value="{{ old('name_ar', auth()->user()?->name) }}"
                            placeholder="مثال: أحمد محمد علي الصوفي" 
                            class="w-full bg-slate-50 border border-slate-300 rounded-2xl p-3.5 outline-none focus:bg-white focus:ring-2 focus:ring-[#0066A1] text-xs sm:text-sm font-bold text-slate-800 transition">
                        <span class="text-[11px] text-slate-400 block mt-1">يُرجى كتابة الاسم الثلاثي أو الرباعي كما في الهوية.</span>
                    </div>

                    <div>
                        <label class="block font-black text-slate-900 text-xs sm:text-sm mb-1.5">
                            * الاسم الكامل بالإنجليزية (مطابق للجواز):
                        </label>
                        <input type="text" name="name_en" id="input_name_en" dir="ltr"
                            value="{{ old('name_en', auth()->user()?->name_en) }}"
                            placeholder="e.g. Ahmed Mohammed Ali Al-Sofi" 
                            class="w-full bg-slate-50 border border-slate-300 rounded-2xl p-3.5 outline-none focus:bg-white focus:ring-2 focus:ring-[#0066A1] text-xs sm:text-sm font-medium text-slate-800 transition">
                        <span class="text-[11px] text-slate-400 block mt-1">يُشترط مطابقته لجواز السفر لضمان صحة الشهادات الدولية.</span>
                    </div>
                </div>

                <!-- مكان وتاريخ الميلاد -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-black text-slate-900 text-xs sm:text-sm mb-1.5">
                            * مكان وتاريخ الميلاد:
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="birth_place" id="input_birth_place" value="{{ old('birth_place') }}"
                                placeholder="مكان الميلاد (مثال: صنعاء)" 
                                class="w-full bg-slate-50 border border-slate-300 rounded-2xl p-3.5 outline-none focus:bg-white focus:ring-2 focus:ring-[#0066A1] text-xs font-bold text-slate-800 transition">
                            
                            <input type="date" name="birth_date" id="input_birth_date" value="{{ old('birth_date') }}"
                                class="w-full bg-slate-50 border border-slate-300 rounded-2xl p-3.5 outline-none focus:bg-white focus:ring-2 focus:ring-[#0066A1] text-xs font-mono text-slate-800 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block font-black text-slate-900 text-xs sm:text-sm mb-1.5">
                            * المستوى التعليمي للطالب:
                        </label>
                        <select name="educational_level" id="input_educational_level" 
                            class="w-full bg-slate-50 border border-slate-300 rounded-2xl p-3.5 outline-none focus:bg-white focus:ring-2 focus:ring-[#0066A1] text-xs sm:text-sm font-bold text-slate-800 transition">
                            <option value="">-- اختر المستوى التعليمي --</option>
                            <option value="طالب جامعي (بكالوريوس)">طالب جامعي (بكالوريوس)</option>
                            <option value="خريج جامعي / موظف">خريج جامعي / موظف</option>
                            <option value="دراسات عليا (ماجستير / دكتوراه)">دراسات عليا (ماجستير / دكتوراه)</option>
                            <option value="ثانوية عامة">ثانوية عامة</option>
                            <option value="مرحلة أساسية / إعدادية">مرحلة أساسية / إعدادية</option>
                            <option value="تعليم مبكر / أطفال">تعليم مبكر / أطفال</option>
                            <option value="أخرى / تدريب حر">أخرى / تدريب حر</option>
                        </select>
                    </div>
                </div>

                <!-- أرقام الهواتف (هاتف الدراسة + هاتف ولي الأمر) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-black text-slate-900 text-xs sm:text-sm mb-1.5">
                            * رقم الهاتف المخصص للدراسة (واتساب): <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="study_phone_whatsapp" id="input_study_phone" required dir="ltr"
                            value="{{ old('study_phone_whatsapp', auth()->user()?->phone_whatsapp) }}"
                            placeholder="+967735999101" 
                            class="w-full bg-slate-50 border border-slate-300 rounded-2xl p-3.5 outline-none focus:bg-white focus:ring-2 focus:ring-[#0066A1] text-xs sm:text-sm font-mono text-slate-800 transition">
                        <span class="text-[11px] text-slate-400 block mt-1">الرقم المعتمد لإضافتك إلى المجموعة الدراسية وإرسال الروابط.</span>
                    </div>

                    <div>
                        <label class="block font-black text-slate-900 text-xs sm:text-sm mb-1.5">
                            * رقم ولي الأمر:
                        </label>
                        <input type="text" name="parent_phone" id="input_parent_phone" dir="ltr"
                            value="{{ old('parent_phone') }}"
                            placeholder="+967775342886" 
                            class="w-full bg-slate-50 border border-slate-300 rounded-2xl p-3.5 outline-none focus:bg-white focus:ring-2 focus:ring-[#0066A1] text-xs sm:text-sm font-mono text-slate-800 transition">
                        <span class="text-[11px] text-slate-400 block mt-1">خاص بمتابعة شؤون الطلاب والتواصل مع أولياء الأمور.</span>
                    </div>
                </div>

                <!-- هل تحتاج لاختبار تحديد مستوى؟ -->
                <div class="bg-amber-50/70 border-2 border-amber-300/80 rounded-2xl p-5 space-y-3">
                    <label class="block font-black text-amber-950 text-xs sm:text-sm">
                        * هل تحتاج لاختبار تحديد مستوى؟
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 bg-white p-3.5 rounded-xl border-2 border-amber-200 cursor-pointer hover:border-amber-400 transition has-[:checked]:border-amber-600 has-[:checked]:bg-amber-100/50">
                            <input type="radio" name="needs_placement_test" id="placement_yes" value="1" checked class="text-[#F58220] focus:ring-[#F58220] w-4 h-4">
                            <div>
                                <strong class="text-xs font-black text-slate-900 block">- نعم (أحتاج لاختبار تحديد مستوى مجاني) ✅</strong>
                                <span class="text-[10px] text-slate-500">يقوم النظام بتحديد مستواك الدقيق في CEFR لتسكينك في الصف المناسب.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 bg-white p-3.5 rounded-xl border-2 border-amber-200 cursor-pointer hover:border-amber-400 transition has-[:checked]:border-amber-600 has-[:checked]:bg-amber-100/50">
                            <input type="radio" name="needs_placement_test" id="placement_no" value="0" class="text-[#F58220] focus:ring-[#F58220] w-4 h-4">
                            <div>
                                <strong class="text-xs font-black text-slate-900 block">- لا (أرغب بالبدء من المستوى التمهيدي أو أعرف مستواي)</strong>
                                <span class="text-[10px] text-slate-500">التسكين التلقائي في المستوى التمهيدي أو بناءً على شهادة سابقة معتمدة.</span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            <!-- تنبيه وملاحظة هامة مطابقة الوثائق -->
            <div class="bg-gradient-to-r from-rose-50 to-orange-50 border-2 border-rose-200 p-4 sm:p-5 rounded-2xl flex items-start gap-3">
                <span class="text-2xl shrink-0">⚠️</span>
                <div class="space-y-1">
                    <strong class="font-black text-rose-950 text-xs sm:text-sm block">ملاحظة هامة:</strong>
                    <p class="text-xs text-rose-900 leading-relaxed font-medium">
                        يرجى التأكد من دقة جميع البيانات ومطابقتها للوثائق الرسمية (جواز السفر أو الهوية الوطنية)، وذلك لضمان إصدار الشهادات الأكاديمية وسندات القبض بشكل صحيح ومعتمد دولياً.
                    </p>
                </div>
            </div>

            <!-- بيانات خاصة بالإدارة (يُعبئها القسم المختص) -->
            <div class="bg-slate-50 border-2 border-slate-200 rounded-3xl p-6 sm:p-8 space-y-4 font-mono text-xs">
                <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
                    <span class="text-lg">📑</span>
                    <strong class="text-slate-900 text-xs sm:text-sm font-sans font-black">بيانات خاصة بالإدارة (يُعبئها القسم المختص):</strong>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white p-3 rounded-xl border border-slate-200">
                        <span class="text-[10px] text-slate-400 block font-sans">الترم الدراسي المقرر:</span>
                        <strong class="text-[#0066A1] text-sm font-black">Term: 40</strong>
                    </div>

                    <div class="bg-white p-3 rounded-xl border border-slate-200">
                        <span class="text-[10px] text-slate-400 block font-sans">الرقم الأكاديمي للطالب:</span>
                        <strong class="text-slate-700 text-xs font-sans">Academic Number: يُمنح آلياً بعد الاعتماد</strong>
                    </div>

                    <div class="bg-white p-3 rounded-xl border border-slate-200">
                        <span class="text-[10px] text-slate-400 block font-sans">الدورة والمستوى الدراسي:</span>
                        <strong class="text-slate-700 text-xs font-sans">Course: حسب نتيجة تحديد المستوى</strong>
                    </div>
                </div>

                <!-- مدة البرنامج الرسمية -->
                <div class="bg-sky-50 border border-sky-200 rounded-2xl p-4 flex items-center gap-3 font-sans">
                    <span class="text-2xl shrink-0">🕒</span>
                    <div class="space-y-0.5">
                        <strong class="font-black text-sky-950 text-xs sm:text-sm block">مدة البرنامج:</strong>
                        <p class="text-xs text-sky-900 font-bold">
                            شهر واحد • خمسة أيام أسبوعياً (من السبت إلى الأربعاء)
                        </p>
                    </div>
                </div>
            </div>

            <!-- خاتمة الشكر والتقدير -->
            <div class="text-center py-2 space-y-1">
                <p class="text-sm sm:text-base font-black text-[#0066A1]">
                    شكراً لثقتكم بنا .. فأنتم بأعيننا 🤍
                </p>
                <p class="text-xs text-slate-400 font-medium">
                    مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)
                </p>
            </div>

            <!-- أزرار الإجراءات التفاعلية الثلاثة -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                
                <!-- زر الحفظ والإرسال في النظام -->
                <button type="submit" 
                    class="w-full sm:w-auto flex-1 bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] hover:to-[#b85807] text-white font-black text-xs sm:text-sm py-4 px-8 rounded-2xl shadow-xl shadow-[#F58220]/25 transition flex items-center justify-center gap-2">
                    <span>إرسال الاستمارة وتثبيت الطلب 🚀</span>
                </button>

                <!-- زر الإرسال المباشر للواتساب -->
                <button type="button" onclick="sendViaWhatsappDirectly()" 
                    class="w-full sm:w-auto bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 text-white font-black text-xs sm:text-sm py-4 px-6 rounded-2xl shadow-lg transition flex items-center justify-center gap-2 whitespace-nowrap">
                    <span>إرسال عبر الواتساب مباشرة 📲</span>
                </button>

                <!-- زر الطباعة -->
                <button type="button" onclick="window.print()" 
                    class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs py-4 px-5 rounded-2xl transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                    <span>🖨️ طباعة</span>
                </button>

            </div>

        </form>

    </div>

    <!-- بطاقة التواصل المباشر مع أرقام القبول والتسجيل المعتمدة -->
    <div class="bg-gradient-to-br from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 print:hidden">
        <div class="space-y-1 text-center md:text-right">
            <h3 class="font-black text-base sm:text-lg">هل تحتاج لمساعدة أثناء تعبئة الاستمارة؟</h3>
            <p class="text-xs text-sky-100">فريق القبول والتسجيل متواجد لمساعدتك هاتفياً أو عبر الواتساب على مدار الساعة.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="https://wa.me/967735999101?text={{ urlencode('مرحباً مكتب ASOTS، لدي استفسار بخصوص استمارة التسجيل للطلاب') }}" target="_blank" rel="noopener noreferrer" class="bg-gradient-to-r from-[#F58220] to-[#d96e14] text-white font-black text-xs px-5 py-3 rounded-xl shadow-md transition flex items-center gap-1.5">
                <span>واتساب التسجيل: +967 735 999 101</span>
                <span>💬</span>
            </a>
            <a href="https://wa.me/967775342886" target="_blank" rel="noopener noreferrer" class="bg-white/10 hover:bg-white/20 text-white border border-white/25 font-bold text-xs px-5 py-3 rounded-xl transition flex items-center gap-1.5">
                <span>الإدارة الأكاديمية: +967 775 342 886</span>
            </a>
        </div>
    </div>

</div>

<script>
    function sendViaWhatsappDirectly() {
        const nameAr = document.getElementById('input_name_ar').value.trim();
        const nameEn = document.getElementById('input_name_en').value.trim();
        const birthPlace = document.getElementById('input_birth_place').value.trim();
        const birthDate = document.getElementById('input_birth_date').value.trim();
        const eduLevel = document.getElementById('input_educational_level').value;
        const studyPhone = document.getElementById('input_study_phone').value.trim();
        const parentPhone = document.getElementById('input_parent_phone').value.trim();
        const needsPlacement = document.getElementById('placement_yes').checked ? 'نعم (✅)' : 'لا (❌)';

        if (!nameAr) {
            alert('يرجى كتابة الاسم الكامل بالعربية أولاً');
            document.getElementById('input_name_ar').focus();
            return;
        }

        if (!studyPhone) {
            alert('يرجى كتابة رقم الهاتف المخصص للدراسة (واتساب)');
            document.getElementById('input_study_phone').focus();
            return;
        }

        let msg = "مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات – ASOTS\n\n" +
            "📋 *إستمارة التسجيل*\n\n" +
            "🔸️ يرجى ملء البيانات التالية :\n\n" +
            "* *الاسم الكامل بالعربية:* " + nameAr + "\n\n" +
            "* *الاسم الكامل بالإنجليزية (مطابق للجواز):* " + (nameEn || '—') + "\n\n" +
            "* *مكان وتاريخ الميلاد:* " + (birthPlace || '—') + " | " + (birthDate || '—') + "\n\n" +
            "* *المستوى التعليمي للطالب:* " + (eduLevel || '—') + "\n\n" +
            "* *رقم الهاتف المخصص للدراسة (واتساب):* " + studyPhone + "\n\n" +
            "* *رقم ولي الأمر:* " + (parentPhone || '—') + "\n\n" +
            "* *هل تحتاج لاختبار تحديد مستوى؟*\n" +
            "- " + needsPlacement + "\n\n" +
            "⚠️ *ملاحظة هامة:*\n" +
            "يرجى التأكد من دقة جميع البيانات ومطابقتها للوثائق الرسمية (جواز السفر أو الهوية)، وذلك لضمان إصدار الشهادات بشكل صحيح ومعتمد.\n\n" +
            "📑 *بيانات خاصة بالإدارة (يُعبئها القسم المختص):*\n\n" +
            "Term: 40\n" +
            "Academic Number:\n" +
            "Course:\n\n" +
            "🕒 *مدة البرنامج:*\n" +
            "شهر واحد\n" +
            "خمسة أيام أسبوعياً (من السبت إلى الأربعاء)\n\n" +
            "شكراً لثقتكم بنا .. فأنتم بأعيننا 🤍";

        const url = "https://wa.me/967735999101?text=" + encodeURIComponent(msg);
        window.open(url, '_blank');
    }
</script>
@endsection
