@extends('layouts.app', ['title' => 'اتصل بنا وإستمارة التسجيل - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-10 max-w-5xl mx-auto my-6">

    <!-- رسالة النجاح إن وُجدت -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-emerald-500/15 via-teal-500/15 to-emerald-500/15 border-2 border-emerald-500/40 p-6 rounded-3xl shadow-lg space-y-3">
            <div class="flex items-center gap-3">
                <span class="text-3xl">✅</span>
                <div>
                    <h3 class="font-black text-emerald-950 text-base">تم استلام طلبك بنجاح!</h3>
                    <p class="text-xs text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
            @if(session('whatsapp_url'))
                <a href="{{ session('whatsapp_url') }}" target="_blank" rel="noopener noreferrer" 
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs py-3 px-5 rounded-xl shadow transition">
                    <span>📲 إرسال نسخة الاستمارة إلى واتساب القبول والتسجيل فوراً</span>
                </a>
            @endif
        </div>
    @endif

    <!-- رأس الصفحة الرسمي الموحد -->
    <div class="text-center space-y-3">
        <span class="inline-flex items-center gap-2 text-xs font-black text-[#0066A1] bg-sky-50 px-4 py-1.5 rounded-full border border-sky-200 shadow-sm">
            <span>📋</span>
            <span>القبول والتسجيل والتواصل • Admission & Contact</span>
        </span>
        <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">
            مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات – ASOTS
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 max-w-2xl mx-auto leading-relaxed">
            اختر إستمارة التسجيل للالتحاق ببرامج ودبلومات اللغة الإنجليزية والترم 40، أو أرسل استفسارك لخدمات الترجمة المعتمدة والاستشارات الأكاديمية.
        </p>
    </div>

    <!-- شريط التبديل بين إستمارة التسجيل والمراسلة العامة -->
    <div class="flex items-center justify-center gap-2 bg-slate-200/80 p-1.5 rounded-2xl max-w-md mx-auto">
        <button type="button" id="tabBtnRegistration" onclick="switchContactTab('registration')" 
            class="flex-1 py-2.5 px-4 rounded-xl text-xs font-black transition-all shadow-sm bg-white text-[#0066A1]">
            📋 إستمارة التسجيل للطلاب
        </button>
        <button type="button" id="tabBtnInquiry" onclick="switchContactTab('inquiry')" 
            class="flex-1 py-2.5 px-4 rounded-xl text-xs font-black transition-all text-slate-600 hover:text-slate-900">
            ✉️ استفسار أو مراسلة عامة
        </button>
    </div>

    <!-- شبكة معلومات التواصل ونموذج المراسلة -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- العمود الجانبي: بطاقة معلومات التواصل المعتمدة والأرقام الثلاثة والقناة -->
        <div class="bg-gradient-to-br from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-6 sm:p-8 space-y-6 shadow-xl">
            <div>
                <span class="text-[10px] font-black uppercase text-[#F58220] tracking-wider block mb-1">ASOTS DIRECT CHANNELS</span>
                <h3 class="text-lg font-black text-white">قنوات التواصل المعتمدة</h3>
                <p class="text-xs text-sky-100 mt-1">نسعد بخدمتكم والرد على كافة الاستفسارات.</p>
            </div>

            <!-- الأرقام الثلاثة الموحدة -->
            <div class="space-y-3 text-xs">
                <a href="https://wa.me/967735999101" target="_blank" rel="noopener noreferrer" class="block bg-white/10 hover:bg-white/20 p-3 rounded-2xl border border-white/15 transition group">
                    <span class="text-[10px] text-[#F58220] font-black block">التسجيل والتواصل المباشر 💬</span>
                    <strong class="text-sm font-mono text-white block mt-0.5 group-hover:text-amber-300" dir="ltr">+967 735 999 101</strong>
                </a>

                <a href="https://wa.me/967775342886" target="_blank" rel="noopener noreferrer" class="block bg-white/10 hover:bg-white/20 p-3 rounded-2xl border border-white/15 transition group">
                    <span class="text-[10px] text-amber-300 font-black block">الإدارة الأكاديمية والقيادة 👨‍🏫</span>
                    <strong class="text-sm font-mono text-white block mt-0.5 group-hover:text-amber-300" dir="ltr">+967 775 342 886</strong>
                    <span class="text-[10px] text-sky-200 block">د. فخر الدين الصوفي</span>
                </a>

                <a href="https://wa.me/967738837887" target="_blank" rel="noopener noreferrer" class="block bg-white/10 hover:bg-white/20 p-3 rounded-2xl border border-white/15 transition group">
                    <span class="text-[10px] text-emerald-300 font-black block">الجودة والمتابعة وشؤون الطلاب 🛡️</span>
                    <strong class="text-sm font-mono text-white block mt-0.5 group-hover:text-amber-300" dir="ltr">+967 738 837 887</strong>
                </a>

                <a href="https://whatsapp.com/channel/0029Vajnq1y7DAWxCf429S2T" target="_blank" rel="noopener noreferrer" class="block bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 p-3 rounded-2xl text-center font-black transition shadow-md">
                    📢 انضم لقناة الطلاب على واتساب
                </a>
            </div>

            <!-- معلومات المقر والبريد -->
            <div class="pt-4 border-t border-white/15 space-y-3 text-xs text-sky-100">
                <div class="flex items-start gap-2.5">
                    <span>✉️</span>
                    <div>
                        <strong class="text-white block">البريد الإلكتروني:</strong>
                        <span class="font-mono text-[11px]">info@asots.edu.ye</span>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <span>📍</span>
                    <div>
                        <strong class="text-white block">المركز الرئيسي:</strong>
                        <span class="text-[11px] leading-relaxed">صنعاء - الجمهورية اليمنية</span>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <span>🕒</span>
                    <div>
                        <strong class="text-white block">مدة البرامج:</strong>
                        <span class="text-[11px]">شهر واحد • 5 أيام أسبوعياً (السبت إلى الأربعاء)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- العمود الرئيسي: استمارة التسجيل الرسمية أو نموذج الاستفسار العام -->
        <div class="lg:col-span-2">
            
            <!-- 1. نموذج إستمارة التسجيل الرسمية للطلاب -->
            <div id="sectionRegistration" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-9 shadow-sm space-y-6">
                
                <div class="border-b border-slate-100 pb-4 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-[#F58220] flex items-center justify-center text-lg font-black">📋</span>
                        <h2 class="text-xl font-black text-slate-900">إستمارة التسجيل</h2>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed font-bold text-[#0066A1]">
                        🔸️ يرجى ملء البيانات التالية :
                    </p>
                </div>

                <form action="{{ route('student.registration.store') }}" method="POST" id="contactRegistrationForm" class="space-y-6 text-xs sm:text-sm">
                    @csrf

                    <!-- الحقول المطلوبة -->
                    <div class="space-y-4">
                        
                        <div>
                            <label class="block font-black text-slate-800 text-xs mb-1">
                                * الاسم الكامل بالعربية: <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name_ar" id="c_name_ar" required 
                                value="{{ old('name_ar', auth()->user()?->name) }}"
                                placeholder="محمد شائف عبدالواحد علي" 
                                class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-bold text-slate-800">
                        </div>

                        <div>
                            <label class="block font-black text-slate-800 text-xs mb-1">
                                * الاسم الكامل بالإنجليزية (مطابق للجواز):
                            </label>
                            <input type="text" name="name_en" id="c_name_en" dir="ltr"
                                value="{{ old('name_en', auth()->user()?->name_en) }}"
                                placeholder="mohammed shaif abdulwahid ali" 
                                class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-medium text-slate-800">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-black text-slate-800 text-xs mb-1">
                                    * مكان وتاريخ الميلاد:
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="birth_place" id="c_birth_place" placeholder="مكان الميلاد" 
                                        class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs">
                                    <input type="date" name="birth_date" id="c_birth_date" 
                                        class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-mono">
                                </div>
                            </div>

                            <div>
                                <label class="block font-black text-slate-800 text-xs mb-1">
                                    * المستوى التعليمي للطالب:
                                </label>
                                <select name="educational_level" id="c_educational_level" 
                                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-bold">
                                    <option value="">-- اختر المستوى التعليمي --</option>
                                    <option value="طالب جامعي (بكالوريوس)">طالب جامعي (بكالوريوس)</option>
                                    <option value="خريج جامعي / موظف">خريج جامعي / موظف</option>
                                    <option value="دراسات عليا (ماجستير / دكتوراه)">دراسات عليا (ماجستير / دكتوراه)</option>
                                    <option value="ثانوية عامة">ثانوية عامة</option>
                                    <option value="مرحلة أساسية / إعدادية">مرحلة أساسية / إعدادية</option>
                                    <option value="أخرى / تدريب حر">أخرى / تدريب حر</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-black text-slate-800 text-xs mb-1">
                                    * رقم الهاتف المخصص للدراسة (واتساب): <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="study_phone_whatsapp" id="c_study_phone" required dir="ltr"
                                    value="{{ old('study_phone_whatsapp', auth()->user()?->phone_whatsapp) }}"
                                    placeholder="+967 " 
                                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-mono font-bold">
                            </div>

                            <div>
                                <label class="block font-black text-slate-800 text-xs mb-1">
                                    * رقم ولي الأمر:
                                </label>
                                <input type="text" name="parent_phone" id="c_parent_phone" dir="ltr"
                                    placeholder="+967 " 
                                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-mono font-bold">
                            </div>
                        </div>

                        <!-- هل تحتاج لاختبار تحديد مستوى؟ -->
                        <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-4 space-y-2">
                            <label class="block font-black text-amber-950 text-xs">
                                * هل تحتاج لاختبار تحديد مستوى؟
                            </label>
                            <div class="flex flex-wrap gap-4 text-xs font-bold">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="needs_placement_test" id="c_placement_yes" value="1" checked class="text-[#F58220] focus:ring-[#F58220]">
                                    <span>نعم (اختبار مجاني عبر المنصة) ✅</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="needs_placement_test" id="c_placement_no" value="0" class="text-[#F58220] focus:ring-[#F58220]">
                                    <span>لا (أعرف مستواي / تمهيدي)</span>
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- ملاحظة هامة مطابقة الوثائق -->
                    <div class="bg-rose-50 border border-rose-200 rounded-2xl p-3.5 flex items-start gap-2.5 text-xs">
                        <span class="text-xl shrink-0">⚠️</span>
                        <div class="space-y-0.5">
                            <strong class="font-black text-rose-950 block">ملاحظة هامة:</strong>
                            <p class="text-rose-900 leading-relaxed font-medium">
                                يرجى التأكد من دقة جميع البيانات ومطابقتها للوثائق الرسمية (جواز السفر أو الهوية)، وذلك لضمان إصدار الشهادات بشكل صحيح ومعتمد.
                            </p>
                        </div>
                    </div>

                    <!-- بيانات خاصة بالإدارة (يُعبئها القسم المختص) -->
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3 font-mono text-[11px]">
                        <strong class="font-black text-slate-800 block font-sans">📑 بيانات خاصة بالإدارة (يُعبئها القسم المختص):</strong>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 font-sans">
                            <div class="bg-white p-2 rounded-lg border border-slate-200">
                                <span class="text-[10px] text-slate-400 block">الترم:</span>
                                <strong class="text-[#0066A1]">Term: 40</strong>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-slate-200">
                                <span class="text-[10px] text-slate-400 block">الرقم الأكاديمي:</span>
                                <strong class="text-slate-600">Academic Number: (آلياً)</strong>
                            </div>
                            <div class="bg-white p-2 rounded-lg border border-slate-200">
                                <span class="text-[10px] text-slate-400 block">المستوى:</span>
                                <strong class="text-slate-600">Course: (حسب التحديد)</strong>
                            </div>
                        </div>

                        <div class="bg-sky-50 p-2.5 rounded-xl border border-sky-100 text-sky-900 font-sans">
                            <strong>🕒 مدة البرنامج:</strong>
                            <span>شهر واحد — خمسة أيام أسبوعياً (من السبت إلى الأربعاء)</span>
                        </div>
                    </div>

                    <!-- خاتمة الشكر -->
                    <p class="text-center font-black text-sm text-[#0066A1] pt-1">
                        شكراً لثقتكم بنا .. فأنتم بأعيننا 🤍
                    </p>

                    <!-- أزرار الإجراءات -->
                    <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                        <button type="submit" class="w-full sm:flex-1 bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] text-white font-black text-xs py-3.5 px-6 rounded-xl shadow-md transition">
                            إرسال الاستمارة وتثبيت الطلب 🚀
                        </button>
                        <button type="button" onclick="sendContactViaWhatsapp()" class="w-full sm:w-auto bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 text-white font-black text-xs py-3.5 px-5 rounded-xl shadow transition">
                            إرسال عبر الواتساب مباشرة 📲
                        </button>
                    </div>

                </form>
            </div>

            <!-- 2. نموذج المراسلة والاستفسارات العامة (مخفي افتراضياً) -->
            <div id="sectionInquiry" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-9 shadow-sm space-y-6 hidden">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-xl font-black text-slate-900">أرسل لنا استفساراً عاماً</h2>
                    <p class="text-xs text-slate-500 mt-1">خاص باستفسارات الترجمة المعتمدة، الاستشارات الأكاديمية، والخدمات المهنية.</p>
                </div>

                <form action="#" method="POST" onsubmit="event.preventDefault(); alert('شكراً لتواصلك! تم استلام رسالتك وسيتواصل معك فريق مكتب النجم الآسيوي قريباً.');" class="space-y-4 text-xs sm:text-sm">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1 text-xs">الاسم الكامل *</label>
                            <input type="text" required placeholder="محمد شائف عبدالواحد علي" class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-1 focus:ring-[#0066A1] text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1 text-xs">رقم هاتف الواتساب *</label>
                            <input type="text" required placeholder="+967 " class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-1 focus:ring-[#0066A1] text-xs font-mono" dir="ltr">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1 text-xs">نوع الخدمة المطلوبة</label>
                        <select class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-1 focus:ring-[#0066A1] text-xs font-bold">
                            <option>ترجمة وثائق ومستندات رسمية معتمدة</option>
                            <option>استشارات أكاديمية وبحثية وتدقيق لغوي</option>
                            <option>برامج التوفل (TOEFL) والآيلتس (IELTS)</option>
                            <option>استفسار إداري أو عام</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1 text-xs">نص الرسالة أو الاستفسار *</label>
                        <textarea rows="4" required placeholder="اكتب تفاصيل طلبك هنا..." class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-1 focus:ring-[#0066A1] text-xs"></textarea>
                    </div>

                    <button type="submit" class="bg-gradient-to-r from-[#0066A1] to-[#004e7c] hover:from-[#005586] text-white font-black text-xs py-3.5 px-8 rounded-xl shadow-md transition">
                        إرسال الرسالة الآن 🚀
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>

<script>
    function switchContactTab(tab) {
        const secReg = document.getElementById('sectionRegistration');
        const secInq = document.getElementById('sectionInquiry');
        const btnReg = document.getElementById('tabBtnRegistration');
        const btnInq = document.getElementById('tabBtnInquiry');

        if (tab === 'registration') {
            secReg.classList.remove('hidden');
            secInq.classList.add('hidden');
            btnReg.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-black transition-all shadow-sm bg-white text-[#0066A1]';
            btnInq.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-black transition-all text-slate-600 hover:text-slate-900';
        } else {
            secReg.classList.add('hidden');
            secInq.classList.remove('hidden');
            btnInq.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-black transition-all shadow-sm bg-white text-[#0066A1]';
            btnReg.className = 'flex-1 py-2.5 px-4 rounded-xl text-xs font-black transition-all text-slate-600 hover:text-slate-900';
        }
    }

    function sendContactViaWhatsapp() {
        const nameAr = document.getElementById('c_name_ar').value.trim();
        const nameEn = document.getElementById('c_name_en').value.trim();
        const birthPlace = document.getElementById('c_birth_place').value.trim();
        const birthDate = document.getElementById('c_birth_date').value.trim();
        const eduLevel = document.getElementById('c_educational_level').value;
        const studyPhone = document.getElementById('c_study_phone').value.trim();
        const parentPhone = document.getElementById('c_parent_phone').value.trim();
        const needsPlacement = document.getElementById('c_placement_yes').checked ? 'نعم (✅)' : 'لا (❌)';

        if (!nameAr) {
            alert('يرجى كتابة الاسم الكامل بالعربية');
            document.getElementById('c_name_ar').focus();
            return;
        }

        if (!studyPhone) {
            alert('يرجى كتابة رقم الهاتف المخصص للدراسة (واتساب)');
            document.getElementById('c_study_phone').focus();
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
