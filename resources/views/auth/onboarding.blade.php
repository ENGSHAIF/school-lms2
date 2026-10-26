@extends('layouts.app', ['title' => 'مسار التسجيل الذكي - Guided Onboarding'])

@section('content')
<div class="max-w-4xl mx-auto my-6">

    <!-- شريط التقدم التفاعلي للخطوات الـ 5 -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm mb-6">
        <div class="flex justify-between items-center text-xs font-bold text-slate-400 mb-3 px-2">
            <span id="stepLabel1" class="text-indigo-600">1. الحساب</span>
            <span id="stepLabel2">2. المرحلة التعليمية</span>
            <span id="stepLabel3">3. الخيارات والأهداف</span>
            <span id="stepLabel4">4. المواعيد</span>
            <span id="stepLabel5">5. خطتك والتوجيه</span>
        </div>
        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
            <div id="progressBar" class="bg-gradient-to-r from-amber-500 to-indigo-600 h-full w-1/5 transition-all duration-500 rounded-full"></div>
        </div>
    </div>

    <!-- بطاقة النموذج التفاعلي المتعدد الخطوات -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-xl relative overflow-hidden">

        <!-- =================== الخطوة 1: إنشاء الحساب الأساسي =================== -->
        <div id="step1" class="step-pane space-y-6">
            <div class="text-center space-y-2 mb-6">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                    الخطوة 1 من 4 • Step 1
                </span>
                <h2 class="text-2xl font-black text-slate-900">أنشئ حسابك لبدء التجربة التعليمية</h2>
                <p class="text-xs text-slate-500">ادخل بياناتك الأساسية لتفعيل مسارك التعليمي المخصص</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الاسم الكامل *</label>
                    <input type="text" id="inputName" required placeholder="مثال: محمد العولقي" 
                        class="w-full border border-slate-300 rounded-xl p-3 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">رقم هاتف الواتساب (لإرسال التقارير والسندات) *</label>
                    <input type="text" id="inputPhone" required placeholder="+967 ..." 
                        class="w-full border border-slate-300 rounded-xl p-3 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">البريد الإلكتروني *</label>
                    <input type="email" id="inputEmail" required placeholder="student@example.com" 
                        class="w-full border border-slate-300 rounded-xl p-3 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">كلمة المرور *</label>
                    <input type="password" id="inputPassword" required placeholder="••••••••" 
                        class="w-full border border-slate-300 rounded-xl p-3 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الدولة أو المنطقة الزمنية</label>
                    <select id="inputCountry" class="w-full border border-slate-300 rounded-xl p-3 focus:ring-1 focus:ring-indigo-500 outline-none font-bold">
                        <option value="اليمن (GMT+3)">اليمن (GMT+3)</option>
                        <option value="السعودية (GMT+3)">المملكة العربية السعودية (GMT+3)</option>
                        <option value="الإمارات (GMT+4)">الإمارات العربية المتحدة (GMT+4)</option>
                        <option value="مصر (GMT+2)">جمهورية مصر العربية (GMT+2)</option>
                        <option value="أخرى">دولة أخرى</option>
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="inputConsent" class="w-4 h-4 text-indigo-600 rounded">
                        <span class="text-xs text-slate-600 font-bold">أوافق على سياسة الخصوصية والشروط (وموافقة ولي الأمر إذا كان المتعلم طفلاً)</span>
                    </label>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="goToStep(2)" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs px-8 py-3.5 rounded-xl shadow-md transition flex items-center gap-2">
                    <span>التالي: اختيار المرحلة التعليمية</span>
                    <span>←</span>
                </button>
            </div>
        </div>

        <!-- =================== الخطوة 2: اختيار المرحلة التعليمية =================== -->
        <div id="step2" class="step-pane hidden space-y-6">
            <div class="text-center space-y-2 mb-6">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                    الخطوة 2 من 4 • Step 2
                </span>
                <h2 class="text-2xl font-black text-slate-900">من هو المستفيد من التعلم؟</h2>
                <p class="text-xs text-slate-500">اختر الفئة أو المرحلة التعليمية لنقوم بتخصيص المنهج ونوع المعلم المناسب لك</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div onclick="selectStage('kindergarten')" class="stage-card p-5 rounded-2xl border-2 border-slate-200 hover:border-indigo-500 hover:shadow-md cursor-pointer transition text-center space-y-2" data-stage="kindergarten">
                    <div class="text-3xl">🧸</div>
                    <strong class="block text-xs text-slate-900 font-black">طفل روضة</strong>
                    <span class="text-[10px] text-slate-400 block">3 - 5 سنوات</span>
                </div>

                <div onclick="selectStage('primary')" class="stage-card p-5 rounded-2xl border-2 border-slate-200 hover:border-indigo-500 hover:shadow-md cursor-pointer transition text-center space-y-2" data-stage="primary">
                    <div class="text-3xl">🎒</div>
                    <strong class="block text-xs text-slate-900 font-black">طالب ابتدائي</strong>
                    <span class="text-[10px] text-slate-400 block">6 - 11 سنة (الصف 1-6)</span>
                </div>

                <div onclick="selectStage('middle_school')" class="stage-card p-5 rounded-2xl border-2 border-slate-200 hover:border-indigo-500 hover:shadow-md cursor-pointer transition text-center space-y-2" data-stage="middle_school">
                    <div class="text-3xl">🏫</div>
                    <strong class="block text-xs text-slate-900 font-black">طالب أساسي / متوسط</strong>
                    <span class="text-[10px] text-slate-400 block">12 - 14 سنة</span>
                </div>

                <div onclick="selectStage('high_school')" class="stage-card p-5 rounded-2xl border-2 border-slate-200 hover:border-indigo-500 hover:shadow-md cursor-pointer transition text-center space-y-2" data-stage="high_school">
                    <div class="text-3xl">🎓</div>
                    <strong class="block text-xs text-slate-900 font-black">طالب ثانوي</strong>
                    <span class="text-[10px] text-slate-400 block">15 - 17 سنة (توفل وجامعة)</span>
                </div>

                <div onclick="selectStage('university')" class="stage-card p-5 rounded-2xl border-2 border-slate-200 hover:border-indigo-500 hover:shadow-md cursor-pointer transition text-center space-y-2" data-stage="university">
                    <div class="text-3xl">🏛️</div>
                    <strong class="block text-xs text-slate-900 font-black">طالب جامعي</strong>
                    <span class="text-[10px] text-slate-400 block">تحضير TOEFL & IELTS</span>
                </div>

                <div onclick="selectStage('adult')" class="stage-card p-5 rounded-2xl border-2 border-slate-200 hover:border-indigo-500 hover:shadow-md cursor-pointer transition text-center space-y-2" data-stage="adult">
                    <div class="text-3xl">💼</div>
                    <strong class="block text-xs text-slate-900 font-black">موظف أو بالغ</strong>
                    <span class="text-[10px] text-slate-400 block">إنجليزية الأعمال والمحادثة</span>
                </div>

                <div onclick="selectStage('parent')" class="stage-card p-5 rounded-2xl border-2 border-slate-200 hover:border-indigo-500 hover:shadow-md cursor-pointer transition text-center space-y-2" data-stage="parent">
                    <div class="text-3xl">👨‍👩‍👦</div>
                    <strong class="block text-xs text-slate-900 font-black">ولي أمر يسجل لأبنائه</strong>
                    <span class="text-[10px] text-slate-400 block">متابعة عدة أطفال</span>
                </div>

                <div onclick="selectStage('other')" class="stage-card p-5 rounded-2xl border-2 border-slate-200 hover:border-indigo-500 hover:shadow-md cursor-pointer transition text-center space-y-2" data-stage="other">
                    <div class="text-3xl">🌐</div>
                    <strong class="block text-xs text-slate-900 font-black">مسار خاص / غير ذلك</strong>
                    <span class="text-[10px] text-slate-400 block">استشارات ومهارات</span>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-between">
                <button type="button" onclick="goToStep(1)" class="text-slate-500 hover:text-slate-800 text-xs font-bold px-4 py-2">
                    → رجوع
                </button>
                <button type="button" onclick="goToStep(3)" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs px-8 py-3.5 rounded-xl shadow-md transition flex items-center gap-2">
                    <span>التالي: تحديد الأهداف والمهارات</span>
                    <span>←</span>
                </button>
            </div>
        </div>

        <!-- =================== الخطوة 3: الخيارات الشرطية حسب المرحلة =================== -->
        <div id="step3" class="step-pane hidden space-y-6">
            <div class="text-center space-y-2 mb-6">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                    الخطوة 3 من 4 • Step 3
                </span>
                <h2 class="text-2xl font-black text-slate-900" id="step3Title">الخيارات المخصصة لمرحلتك</h2>
                <p class="text-xs text-slate-500">حدد أهدافك الرئيسية لنقوم بتوجيه المعلم للتركيز عليها في الحصص</p>
            </div>

            <!-- خيارات الأطفال والروضة -->
            <div id="optionsKindergarten" class="conditional-box hidden space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">مستوى الطفل الحالي:</label>
                    <select id="kgSubstage" class="w-full border border-slate-300 rounded-xl p-3 outline-none">
                        <option value="روضة تمهيدي (لم يدرس الإنجليزية سابقاً)">روضة تمهيدي (لم يدرس الإنجليزية سابقاً)</option>
                        <option value="مبتدئ (يعرف بعض الحروف والكلمات)">مبتدئ (يعرف بعض الحروف والكلمات)</option>
                        <option value="متقدم (يستطيع التحدث بكلمات بسيطة)">متقدم (يستطيع التحدث بكلمات بسيطة)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-2">أهداف ولي الأمر الأساسية:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer bg-slate-50"><input type="checkbox" name="goals_kg" value="نطق الحروف والأصوات الصحيحة" checked> نطق الحروف والأصوات الصحيحة</label>
                        <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer bg-slate-50"><input type="checkbox" name="goals_kg" value="حب اللغة والمحادثة بالألعاب" checked> حب اللغة والمحادثة بالألعاب</label>
                        <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer bg-slate-50"><input type="checkbox" name="goals_kg" value="الاستعداد للمدرسة"> الاستعداد للمدرسة</label>
                    </div>
                </div>
            </div>

            <!-- خيارات الابتدائي -->
            <div id="optionsPrimary" class="conditional-box hidden space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الصف الدراسي الحالي:</label>
                    <select id="primaryGrade" class="w-full border border-slate-300 rounded-xl p-3 outline-none">
                        <option value="الصف الأول الابتدائي">الصف الأول الابتدائي</option>
                        <option value="الصف الثاني الابتدائي">الصف الثاني الابتدائي</option>
                        <option value="الصف الثالث الابتدائي">الصف الثالث الابتدائي</option>
                        <option value="الصف الرابع الابتدائي">الصف الرابع الابتدائي</option>
                        <option value="الصف الخامس الابتدائي">الصف الخامس الابتدائي</option>
                        <option value="الصف السادس الابتدائي">الصف السادس الابتدائي</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-2">المهارة ذات الأولوية القصوى:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer bg-slate-50"><input type="checkbox" name="goals_pri" value="الطلاقة والمحادثة 1-on-1" checked> الطلاقة والمحادثة 1-on-1</label>
                        <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer bg-slate-50"><input type="checkbox" name="goals_pri" value="تقوية القراءة وفهم النصوص"> تقوية القراءة وفهم النصوص</label>
                        <label class="p-3 border rounded-xl flex items-center gap-2 cursor-pointer bg-slate-50"><input type="checkbox" name="goals_pri" value="دعم الواجبات والمنهج المدرسي"> دعم الواجبات والمنهج المدرسي</label>
                    </div>
                </div>
            </div>

            <!-- خيارات الثانوي والجامعي والتوفل -->
            <div id="optionsAdvanced" class="conditional-box space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الهدف الأكاديمي الرئيسي:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <label class="p-3.5 border-2 border-indigo-200 bg-indigo-50/40 rounded-xl flex items-center gap-2 cursor-pointer"><input type="checkbox" name="goals_adv" value="اجتياز اختبار TOEFL iBT بدرجة عالية" checked> <strong>اجتياز اختبار TOEFL iBT بدرجة عالية ⭐</strong></label>
                        <label class="p-3.5 border rounded-xl flex items-center gap-2 cursor-pointer bg-slate-50"><input type="checkbox" name="goals_adv" value="التحضير لاختبار IELTS والقبول الجامعي"> التحضير لاختبار IELTS والقبول الجامعي</label>
                        <label class="p-3.5 border rounded-xl flex items-center gap-2 cursor-pointer bg-slate-50"><input type="checkbox" name="goals_adv" value="المحادثة المتقدمة وكسر حاجز الخوف"> المحادثة المتقدمة وكسر حاجز الخوف</label>
                        <label class="p-3.5 border rounded-xl flex items-center gap-2 cursor-pointer bg-slate-50"><input type="checkbox" name="goals_adv" value="إنجليزية الأعمال ومقابلات العمل والوظائف"> إنجليزية الأعمال ومقابلات العمل</label>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-between">
                <button type="button" onclick="goToStep(2)" class="text-slate-500 hover:text-slate-800 text-xs font-bold px-4 py-2">
                    → رجوع
                </button>
                <button type="button" onclick="goToStep(4)" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs px-8 py-3.5 rounded-xl shadow-md transition flex items-center gap-2">
                    <span>التالي: المواعيد والتفضيلات</span>
                    <span>←</span>
                </button>
            </div>
        </div>

        <!-- =================== الخطوة 4: المواعيد والتكرار =================== -->
        <div id="step4" class="step-pane hidden space-y-6">
            <div class="text-center space-y-2 mb-6">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                    الخطوة 4 من 4 • Step 4
                </span>
                <h2 class="text-2xl font-black text-slate-900">الأوقات المفضلة للدراسة</h2>
                <p class="text-xs text-slate-500">جلسات الحصص مدتها 25 دقيقة ويمكنك جدولتها في أي وقت يناسبك</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الفترة الزمنية المفضلة للحصص:</label>
                    <select id="prefTime" class="w-full border border-slate-300 rounded-xl p-3 outline-none font-bold">
                        <option value="مسائي (5:00 م - 9:00 م)">مسائي (5:00 م - 9:00 م) - الأكثر طلباً</option>
                        <option value="ليلي (9:00 م - 11:30 م)">ليلي (9:00 م - 11:30 م)</option>
                        <option value="صباحي (9:00 ص - 1:00 م)">صباحي (9:00 ص - 1:00 م)</option>
                        <option value="نهاية الأسبوع فقط">نهاية الأسبوع فقط (الجمعة والسبت)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">تكرار الحصص المقترح أسبوعياً:</label>
                    <select id="prefFreq" class="w-full border border-slate-300 rounded-xl p-3 outline-none font-bold">
                        <option value="3 حصص أسبوعياً (مثالي للتقدم السريع)">3 حصص أسبوعياً (مثالي للتقدم السريع)</option>
                        <option value="حصتان أسبوعياً (متوازن)">حصتان أسبوعياً (متوازن)</option>
                        <option value="مكثف يومي (للاستعداد السريع للتوفل)">مكثف يومي (للاستعداد السريع للتوفل)</option>
                    </select>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-3">
                <span class="text-2xl">🎁</span>
                <div>
                    <strong class="block">هدية ترحيبية فورية:</strong>
                    <span>بمجرد تأكيد التسجيل، ستُودع في حسابك <strong>حصة تجريبية مجانية 1-on-1 (25 دقيقة)</strong> مع معلم معتمد!</span>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                <button type="button" onclick="goToStep(3)" class="text-slate-500 hover:text-slate-800 text-xs font-bold px-4 py-2">
                    → رجوع
                </button>
                <button type="button" id="submitBtn" onclick="submitOnboarding()" class="bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black text-xs px-10 py-4 rounded-xl shadow-xl transition flex items-center gap-2">
                    <span>إتمام التسجيل وتوليد خطتي الشخصية 🚀</span>
                </button>
            </div>
        </div>

        <!-- =================== الخطوة 5: النتيجة والتوجيه المخصص (Personalized Result) =================== -->
        <div id="step5" class="step-pane hidden space-y-8 text-center">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-3xl flex items-center justify-center text-4xl mx-auto shadow-inner">
                🎉
            </div>

            <div class="space-y-2">
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                    تم إنشاء حسابك وتخصيص مسارك التعليمي بنجاح
                </span>
                <h2 class="text-3xl font-black text-slate-900" id="resUserName">أهلاً بك في مكتب النجم الآسيوي ASOTS!</h2>
                <p class="text-xs text-slate-500" id="resCode"></p>
            </div>

            <!-- بطاقة ملخص المسار المخصص للطالب -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 text-right text-xs space-y-3 max-w-lg mx-auto">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-slate-500">المسار والمرحلة المعتمدة:</span>
                    <strong class="text-indigo-700" id="resStageLabel"></strong>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-slate-500">رصيد الحصص التجريبية الممنوح:</span>
                    <strong class="text-emerald-600 font-mono">1 حصة حية مجانية 1-on-1 (25 دقيقة)</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">حالة تحديد المستوى:</span>
                    <strong class="text-amber-600">بانتظار أداء اختبار القبول الصوتي</strong>
                </div>
            </div>

            <!-- أزرار الإجراءات الثلاثة الأساسية (CTAs) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 max-w-2xl mx-auto">
                <a id="ctaPlacement" href="{{ route('placement.test') }}" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs py-3.5 px-4 rounded-xl shadow-lg transition flex flex-col items-center justify-center gap-1">
                    <span class="text-base">🎙️</span>
                    <span>1. اختبار تحديد المستوى</span>
                    <span class="text-[10px] font-normal text-indigo-200">فحص فوري لنطقك وقواعدك</span>
                </a>

                <a id="ctaBooking" href="{{ route('student.booking') }}" 
                    class="bg-slate-900 hover:bg-slate-800 text-white font-black text-xs py-3.5 px-4 rounded-xl shadow-lg transition flex flex-col items-center justify-center gap-1">
                    <span class="text-base">📅</span>
                    <span>2. حجز الحصة التجريبية</span>
                    <span class="text-[10px] font-normal text-slate-300">مقابلة المعلم 1-on-1</span>
                </a>

                <a id="ctaPackages" href="{{ route('packages.index') }}" 
                    class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs py-3.5 px-4 rounded-xl shadow-lg transition flex flex-col items-center justify-center gap-1">
                    <span class="text-base">💳</span>
                    <span>3. استكشاف باقات الحصص</span>
                    <span class="text-[10px] font-normal text-slate-900">سندات القبض المعتمدة</span>
                </a>
            </div>
        </div>

    </div>

</div>

<!-- سكريبت إدارة خطوات الـ Wizard والخيارات الشرطية والتسجيل -->
<script>
let currentStep = 1;
let selectedStage = 'high_school';

function goToStep(step) {
    // التحقق من بيانات الخطوة 1
    if (step === 2 && currentStep === 1) {
        const name = document.getElementById('inputName').value.trim();
        const email = document.getElementById('inputEmail').value.trim();
        const phone = document.getElementById('inputPhone').value.trim();
        const pass = document.getElementById('inputPassword').value.trim();
        if (!name || !email || !phone || !pass) {
            alert('يرجى ملء جميع الحقول الإلزامية في الخطوة الأولى.');
            return;
        }
    }

    // إخفاء كافة الخطوات وإظهار الخطوة المطلوبة
    document.querySelectorAll('.step-pane').forEach(el => el.classList.add('hidden'));
    document.getElementById(`step${step}`).classList.remove('hidden');

    // تحديث شريط التقدم والـ Labels
    const progressEl = document.getElementById('progressBar');
    progressEl.style.width = `${(step / 5) * 100}%`;

    for (let i = 1; i <= 5; i++) {
        const lbl = document.getElementById(`stepLabel${i}`);
        if (lbl) {
            if (i <= step) {
                lbl.classList.add('text-indigo-600');
                lbl.classList.remove('text-slate-400');
            } else {
                lbl.classList.remove('text-indigo-600');
                lbl.classList.add('text-slate-400');
            }
        }
    }

    currentStep = step;
    window.scrollTo({ top: 100, behavior: 'smooth' });
}

function selectStage(stage) {
    selectedStage = stage;
    document.querySelectorAll('.stage-card').forEach(c => {
        if (c.getAttribute('data-stage') === stage) {
            c.classList.add('border-indigo-600', 'ring-4', 'ring-indigo-500/10', 'bg-indigo-50/20');
        } else {
            c.classList.remove('border-indigo-600', 'ring-4', 'ring-indigo-500/10', 'bg-indigo-50/20');
        }
    });

    // تحديث وتجهيز الخيارات الشرطية للخطوة 3
    document.querySelectorAll('.conditional-box').forEach(b => b.classList.add('hidden'));
    if (stage === 'kindergarten') {
        document.getElementById('optionsKindergarten').classList.remove('hidden');
    } else if (stage === 'primary') {
        document.getElementById('optionsPrimary').classList.remove('hidden');
    } else {
        document.getElementById('optionsAdvanced').classList.remove('hidden');
    }
}
selectStage('high_school'); // القيمة الافتراضية

// إرسال البيانات إلى الخادم
async function submitOnboarding() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = 'جاري إعداد حسابك ومحفظتك... ⏳';

    // جمع الأهداف المختارة
    const goals = [];
    document.querySelectorAll('input[name^="goals_"]:checked').forEach(c => goals.push(c.value));

    let gradeSubstage = '';
    if (selectedStage === 'kindergarten') gradeSubstage = document.getElementById('kgSubstage').value;
    else if (selectedStage === 'primary') gradeSubstage = document.getElementById('primaryGrade').value;

    const payload = {
        name: document.getElementById('inputName').value.trim(),
        email: document.getElementById('inputEmail').value.trim(),
        phone_whatsapp: document.getElementById('inputPhone').value.trim(),
        password: document.getElementById('inputPassword').value.trim(),
        country: document.getElementById('inputCountry').value,
        stage: selectedStage,
        grade_substage: gradeSubstage,
        learning_goals: goals,
        preferred_time: document.getElementById('prefTime').value,
        preferred_frequency: document.getElementById('prefFreq').value,
        parent_consent: document.getElementById('inputConsent').checked ? 1 : 0,
    };

    try {
        const response = await fetch('{{ route("onboarding.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.success) {
            // تعبئة الخطوة 5 بالنتائج
            document.getElementById('resUserName').textContent = `أهلاً بك، ${data.user.name} في ASOTS!`;
            document.getElementById('resCode').textContent = `رقم القيد الأكاديمي: ${data.user.code}`;
            document.getElementById('resStageLabel').textContent = data.profile.stage_label;

            goToStep(5);
        } else {
            alert(data.message || 'حدث خطأ أثناء التسجيل، يرجى التأكد من صحة البريد الإلكتروني.');
            btn.disabled = false;
            btn.innerHTML = 'إتمام التسجيل وتوليد خطتي الشخصية 🚀';
        }
    } catch (e) {
        alert('حدث خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.');
        btn.disabled = false;
        btn.innerHTML = 'إتمام التسجيل وتوليد خطتي الشخصية 🚀';
    }
}
</script>
@endsection
