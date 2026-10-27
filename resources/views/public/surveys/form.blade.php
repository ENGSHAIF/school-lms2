@extends('layouts.app', ['title' => 'استبانة تقييم الطالب - ASOTS'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">

    <!-- كارت الترويسة الأكاديمية والترحيب -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-10 shadow-2xl border border-slate-800">
        <div class="absolute -top-24 -left-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold border border-indigo-500/30">
                    <span>🌟</span>
                    <span>منظومة قياس جودة التدريب والتعليم الأكاديمي</span>
                </div>
                <span class="text-xs text-amber-400 font-bold flex items-center gap-1.5">
                    <span>بإشراف د. فخرالدين الصوفي</span>
                    <span>🏛️</span>
                </span>
            </div>

            <div class="space-y-2">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight" id="survey-title-display">
                    @if($type === 'daily')
                        📝 تقييم سريع لحصة اليوم — ASOTS
                    @elseif($type === 'weekly')
                        📈 استبانة التقييم الأسبوعي — ASOTS
                    @else
                        🎓 استبانة تقييم تجربة التعلم النهائية — ASOTS
                    @endif
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-2xl" id="survey-desc-display">
                    @if($type === 'daily')
                        عزيزي الطالب، رأيك يصنع الفرق! خصّص دقيقة واحدة لتقييم حصة اليوم لتحسين جودة الحصص القادمة.
                    @elseif($type === 'weekly')
                        أهلاً بك، نريد أن نعرف كيف كانت تجربتك التعليمية هذا الأسبوع ومشاركتك في تطويرها.
                    @else
                        وصلت إلى محطة الإنجاز! 🎉 نرجو أن تمنحنا دقائق لتقييم تجربتك الكاملة بكل شفافية ومصداقية، فآراؤك تُعامل بسرية تامة وتُسهم مباشرة في تطوير برامجنا.
                    @endif
                </p>
            </div>

            <div class="pt-2 flex flex-wrap items-center gap-4 text-xs text-slate-400 border-t border-slate-800/80">
                <span class="flex items-center gap-1.5">
                    <span class="text-emerald-400">🔒</span>
                    <span>بياناتك وآراؤك تُعامل بسرية تامة</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="text-amber-400">⏱️</span>
                    <span id="survey-time-estimate">
                        @if($type === 'daily') يستغرق دقيقة واحدة @elseif($type === 'weekly') يستغرق دقيقتين @else يستغرق 3 دقائق @endif
                    </span>
                </span>
            </div>
        </div>
    </div>

    <!-- مبدل أنواع الاستبيان (Tabs) -->
    <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-2xs flex flex-wrap sm:flex-nowrap gap-2">
        <a href="{{ route('surveys.create', array_merge(request()->all(), ['type' => 'daily'])) }}"
           class="flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold text-center transition flex items-center justify-center gap-2 {{ $type === 'daily' ? 'bg-amber-500 text-slate-950 shadow-md font-black' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>⚡</span>
            <span>التقييم اليومي السريع</span>
            <span class="text-[10px] opacity-75 hidden md:inline">(نهاية الحصة)</span>
        </a>

        <a href="{{ route('surveys.create', array_merge(request()->all(), ['type' => 'weekly'])) }}"
           class="flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold text-center transition flex items-center justify-center gap-2 {{ $type === 'weekly' ? 'bg-indigo-600 text-white shadow-md font-black' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>📊</span>
            <span>التقييم الأسبوعي الشامل</span>
            <span class="text-[10px] opacity-75 hidden md:inline">(نهاية الأسبوع)</span>
        </a>

        <a href="{{ route('surveys.create', array_merge(request()->all(), ['type' => 'diploma_final'])) }}"
           class="flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold text-center transition flex items-center justify-center gap-2 {{ $type === 'diploma_final' ? 'bg-emerald-600 text-white shadow-md font-black' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>🎓</span>
            <span>التقييم النهائي للدبلوم</span>
            <span class="text-[10px] opacity-75 hidden md:inline">(نهاية البرنامج)</span>
        </a>
    </div>

    <!-- النموذج الرئيسي للاستبيان -->
    <form action="{{ route('surveys.store') }}" method="POST" id="survey-form" class="space-y-6">
        @csrf
        <input type="hidden" name="survey_type" value="{{ $type }}">

        <!-- 1. بيانات الطالب والارتباط الأكاديمي -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-black">1</span>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900">البيانات الأساسية وتحديد الأستاذ</h2>
                    <p class="text-xs text-slate-500">اختر الأستاذ والمجموعة لتوجيه تقييمك بدقة</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- اختيار الأستاذ -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">
                        الأستاذ / المدرّس المعني بالتقييم <span class="text-rose-500">*</span>
                    </label>
                    <select name="teacher_id" required class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-xs sm:text-sm font-bold text-slate-800 bg-slate-50 transition">
                        <option value="">-- اضغط لاختيار الأستاذ --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ (string)$defaultTeacherId === (string)$teacher->id ? 'selected' : '' }}>
                                👨‍🏫 {{ $teacher->name }} {{ $teacher->user_code ? '(' . $teacher->user_code . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- اختيار المجموعة أو الدورة -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">
                        المجموعة الدراسية / الدورة (اختياري)
                    </label>
                    <select name="study_group_id" class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-xs sm:text-sm text-slate-800 bg-slate-50 transition">
                        <option value="">-- حدد المجموعة إن كنت ملتحقاً بها --</option>
                        @foreach($studyGroups as $grp)
                            <option value="{{ $grp->id }}" {{ (string)$defaultGroupId === (string)$grp->id ? 'selected' : '' }}>
                                👥 {{ $grp->name_ar ?: $grp->name }} {{ $grp->teacher ? '- ' . $grp->teacher->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- اسم الطالب -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">
                        اسم الطالب (اختياري)
                    </label>
                    <input type="text" name="student_name"
                           value="{{ $currentUser ? $currentUser->name : old('student_name') }}"
                           placeholder="اسمك الكريم أو اتركه فارغاً"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-xs sm:text-sm text-slate-800 bg-slate-50 transition">
                </div>

                <!-- رقم الواتساب -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">
                        رقم الواتساب (اختياري للتواصل والتحفيز)
                    </label>
                    <input type="tel" name="student_phone" dir="ltr"
                           value="{{ $currentUser ? $currentUser->phone_whatsapp : old('student_phone') }}"
                           placeholder="+967..."
                           class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-xs sm:text-sm text-slate-800 bg-slate-50 transition">
                </div>
            </div>

            <!-- خيار السرية التامة -->
            <div class="pt-2">
                <label class="inline-flex items-center gap-3 cursor-pointer p-3 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition">
                    <input type="checkbox" name="is_anonymous" value="1" class="w-5 h-5 rounded-lg border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <div class="text-xs">
                        <span class="font-bold text-slate-800 block">إرسال التقييم بهوية سرية ومجهولة 🕵️‍♂️</span>
                        <span class="text-slate-500 text-[11px]">لن يظهر اسمك أو رقمك للأستاذ إطلاقاً لضمان كامل الشفافية والمصداقية.</span>
                    </div>
                </label>
            </div>
        </div>

        @if($type === 'daily')
            <!-- ===================== التقييم اليومي السريع ===================== -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-amber-200 shadow-2xs space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-black">⭐</span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">تقييم المدرّس وحصة اليوم</h2>
                            <p class="text-xs text-slate-500">5 أسئلة سريعة لا تتطلب سوى نقرة واحدة</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200">
                        ⚡ دقيقة واحدة
                    </span>
                </div>

                <div class="space-y-6 divide-y divide-slate-100">
                    <!-- سؤال 1 -->
                    <div class="pt-4 first:pt-0 space-y-3">
                        <label class="block text-xs sm:text-sm font-black text-slate-800">
                            1️⃣ هل كان شرح المدرّس واضحاً اليوم؟ (١–٥ نجوم) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-2 star-rating" data-target="input-teacher-clarity">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" data-val="{{ $i }}" class="star-btn text-3xl sm:text-4xl text-slate-300 hover:text-amber-400 transition transform hover:scale-110">★</button>
                            @endfor
                            <span class="rating-label text-xs font-bold text-slate-500 mr-3">حدد تقييمك</span>
                            <input type="hidden" name="teacher_clarity" id="input-teacher-clarity" required value="">
                        </div>
                    </div>

                    <!-- سؤال 2 -->
                    <div class="pt-5 space-y-3">
                        <label class="block text-xs sm:text-sm font-black text-slate-800">
                            2️⃣ هل شعرت أنك استفدت من الحصة؟ <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-3 max-w-md">
                            <label class="cursor-pointer">
                                <input type="radio" name="daily_benefited" value="yes" required class="peer sr-only">
                                <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 text-center font-bold text-xs sm:text-sm text-slate-700 peer-checked:text-emerald-800 transition">
                                    <span class="text-xl block mb-1">😍</span>
                                    <span>نعم استفدت</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="daily_benefited" value="partial" class="peer sr-only">
                                <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 text-center font-bold text-xs sm:text-sm text-slate-700 peer-checked:text-amber-800 transition">
                                    <span class="text-xl block mb-1">🙂</span>
                                    <span>جزئياً</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="daily_benefited" value="no" class="peer sr-only">
                                <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 text-center font-bold text-xs sm:text-sm text-slate-700 peer-checked:text-rose-800 transition">
                                    <span class="text-xl block mb-1">😞</span>
                                    <span>لا لم أستفد</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- سؤال 3 -->
                    <div class="pt-5 space-y-3">
                        <label class="block text-xs sm:text-sm font-black text-slate-800">
                            3️⃣ ما مدى رضاك عن التفاعل والأسئلة داخل الصف؟ (١–٥) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-2 star-rating" data-target="input-teacher-interaction">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" data-val="{{ $i }}" class="star-btn text-3xl sm:text-4xl text-slate-300 hover:text-amber-400 transition transform hover:scale-110">★</button>
                            @endfor
                            <span class="rating-label text-xs font-bold text-slate-500 mr-3">حدد تقييمك</span>
                            <input type="hidden" name="teacher_interaction" id="input-teacher-interaction" required value="">
                        </div>
                    </div>

                    <!-- سؤال 4 -->
                    <div class="pt-5 space-y-3">
                        <label class="block text-xs sm:text-sm font-black text-slate-800">
                            4️⃣ هل كانت المادة المقدمة مناسبة لمستواك؟ <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3 max-w-xs">
                            <label class="cursor-pointer">
                                <input type="radio" name="daily_level_appropriate" value="yes" required class="peer sr-only">
                                <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 text-center font-bold text-xs sm:text-sm text-slate-700 peer-checked:text-indigo-800 transition">
                                    <span class="text-xl block mb-1">👍</span>
                                    <span>نعم مناسبة</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="daily_level_appropriate" value="no" class="peer sr-only">
                                <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 text-center font-bold text-xs sm:text-sm text-slate-700 peer-checked:text-rose-800 transition">
                                    <span class="text-xl block mb-1">👎</span>
                                    <span>لا (صعبة أو سهلة جداً)</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- سؤال 5 -->
                    <div class="pt-5 space-y-2">
                        <label class="block text-xs sm:text-sm font-black text-slate-800">
                            5️⃣ ملاحظة سريعة أو اقتراح لتحسين الحصة القادمة:
                        </label>
                        <textarea name="notes_suggestions" rows="3" placeholder="أخبرنا بما يمكننا تحسينه في الحصة القادمة..."
                                  class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-100 text-xs sm:text-sm text-slate-800 bg-slate-50 transition"></textarea>
                    </div>
                </div>
            </div>

        @elseif($type === 'weekly')
            <!-- ===================== التقييم الأسبوعي الشامل ===================== -->
            <div class="space-y-6">
                <!-- محور المدرس -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="text-xl">🔹</span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">محور المدرّس</h2>
                            <p class="text-xs text-slate-500">قيّم أداء وكفاءة الأستاذ خلال هذا الأسبوع (1 = ضعيف جداً … 5 = ممتاز)</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">الكفاءة العلمية والتمكن من المادة (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-weekly-competence">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="teacher_competence" id="input-weekly-competence" required value="">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">وضوح الشرح وتبسيط المعلومة (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-weekly-clarity">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="teacher_clarity" id="input-weekly-clarity" required value="">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">أسلوب التدريس والتفاعل داخل الصف (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-weekly-interaction">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="teacher_interaction" id="input-weekly-interaction" required value="">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">الالتزام بالوقت والمواعيد (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-weekly-punctuality">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="teacher_punctuality" id="input-weekly-punctuality" required value="">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- محور المنهج -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="text-xl">🔹</span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">محور المنهج والمحتوى التعليمي</h2>
                            <p class="text-xs text-slate-500">تقييم المادة المقررة ومدى ملاءمتها</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">مناسبة المحتوى لمستواك العلمي (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-weekly-suitability">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="curriculum_suitability" id="input-weekly-suitability" required value="">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">توافق المحتوى مع أهداف الدورة وتطوير مهاراتك (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-weekly-alignment">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="curriculum_alignment" id="input-weekly-alignment" required value="">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- محور التحديات -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="text-xl">🔹</span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">محور التحديات والصعوبات</h2>
                            <p class="text-xs text-slate-500">ما أكبر تحدٍّ واجهك هذا الأسبوع؟ (يمكنك اختيار أكثر من مهارة)</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 pt-2">
                        @php
                            $challenges = [
                                'speaking' => ['🗣️', 'التحدث (Speaking)'],
                                'listening' => ['👂', 'الاستماع (Listening)'],
                                'reading' => ['📖', 'القراءة (Reading)'],
                                'writing' => ['✍️', 'الكتابة (Writing)'],
                                'grammar' => ['📐', 'القواعد (Grammar)'],
                            ];
                        @endphp
                        @foreach($challenges as $chKey => $chInfo)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="learning_challenges[]" value="{{ $chKey }}" class="peer sr-only">
                                <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-indigo-600 peer-checked:bg-indigo-50/70 text-center text-xs font-bold text-slate-700 peer-checked:text-indigo-900 transition flex flex-col items-center justify-center gap-1.5">
                                    <span class="text-2xl">{{ $chInfo[0] }}</span>
                                    <span>{{ $chInfo[1] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- محور الدعم والنظام -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="text-xl">🔹</span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">محور الدعم والتعلم عن بُعد</h2>
                            <p class="text-xs text-slate-500">مستوى تواصل الإدارة الأكاديمية وسهولة المنصة</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">جودة تواصل الإدارة الأكاديمية وسرعة الرد (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-weekly-admin">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="admin_communication" id="input-weekly-admin" required value="">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">سهولة نظام التعلم عن بُعد والدخول للفصول (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-weekly-remote">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="remote_learning_ease" id="input-weekly-remote" required value="">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">توضيح إن واجهتك أي صعوبة في نظام التعلم عن بعد:</label>
                        <input type="text" name="remote_learning_difficulties" placeholder="مثلاً: انقطاع الصوت، صعوبة تحميل ملف معين..."
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-xs sm:text-sm text-slate-800 bg-slate-50 transition">
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <label class="block text-xs sm:text-sm font-black text-slate-900">💡 اقتراحك لهذا الأسبوع:</label>
                        <textarea name="notes_suggestions" rows="3" placeholder="اقتراحات لتطوير التجربة التعليمية في الأسابيع المقبلة..."
                                  class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-xs sm:text-sm text-slate-800 bg-slate-50 transition"></textarea>
                    </div>
                </div>
            </div>

        @else
            <!-- ===================== التقييم النهائي للدبلوم ===================== -->
            <div class="space-y-6">
                <!-- أولاً: تقييم المدرس -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="text-xl">📌</span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">أولاً: تقييم المدرّس</h2>
                            <p class="text-xs text-slate-500">تقييم شامل لأداء الأستاذ على مدار برنامج الدبلوم كاملاً</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">1. الكفاءة العلمية (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-final-competence">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="teacher_competence" id="input-final-competence" required value="">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">2. وضوح الشرح (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-final-clarity">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="teacher_clarity" id="input-final-clarity" required value="">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">3. أساليب التدريس والتفاعل (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-final-interaction">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="teacher_interaction" id="input-final-interaction" required value="">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">4. التعامل والتحفيز والتشجيع (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-final-punctuality">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="teacher_punctuality" id="input-final-punctuality" required value="">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ثانياً: المنهج -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="text-xl">📌</span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">ثانياً: المنهج والمحتوى</h2>
                            <p class="text-xs text-slate-500">تقييم المناهج والمواد العلمية المقدمة في الدبلوم</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">5. ملاءمة المنهج لمستواك وتطورك (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-final-suitability">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="curriculum_suitability" id="input-final-suitability" required value="">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">6. توافق المحتوى مع أهداف الدورة (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-final-alignment">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="curriculum_alignment" id="input-final-alignment" required value="">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ثالثاً: تحديات التعلم -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="text-xl">📌</span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">ثالثاً: تحديات التعلم</h2>
                            <p class="text-xs text-slate-500">7. ما أبرز تحدٍّ واجهك طوال فترة الدبلوم؟</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 pt-2">
                        @php
                            $challenges = [
                                'speaking' => ['🗣️', 'التحدث (Speaking)'],
                                'listening' => ['👂', 'الاستماع (Listening)'],
                                'reading' => ['📖', 'القراءة (Reading)'],
                                'writing' => ['✍️', 'الكتابة (Writing)'],
                                'grammar' => ['📐', 'القواعد (Grammar)'],
                            ];
                        @endphp
                        @foreach($challenges as $chKey => $chInfo)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="learning_challenges[]" value="{{ $chKey }}" class="peer sr-only">
                                <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50/70 text-center text-xs font-bold text-slate-700 peer-checked:text-emerald-900 transition flex flex-col items-center justify-center gap-1.5">
                                    <span class="text-2xl">{{ $chInfo[0] }}</span>
                                    <span>{{ $chInfo[1] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- رابعاً: الإدارة والدعم -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="text-xl">📌</span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">رابعاً: الإدارة والدعم</h2>
                            <p class="text-xs text-slate-500">تقييم الخدمات الإدارية والأكاديمية ونظام التعلم</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">8. دعم الإدارة الأكاديمية وجودة التواصل (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-final-admin">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="admin_communication" id="input-final-admin" required value="">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">9. التنظيم والمتابعة الأكاديمية (١–٥)</label>
                            <div class="flex items-center gap-1.5 star-rating" data-target="input-final-org">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-val="{{ $i }}" class="star-btn text-2xl sm:text-3xl text-slate-300 hover:text-amber-400 transition">★</button>
                                @endfor
                                <input type="hidden" name="management_organization" id="input-final-org" required value="">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">10. صعوبات في نظام التعلم عن بُعد؟ (نعم/لا + توضيح):</label>
                        <input type="text" name="remote_learning_difficulties" placeholder="هل واجهتك صعوبة تقنية وما هي؟"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-xs sm:text-sm text-slate-800 bg-slate-50 transition">
                    </div>
                </div>

                <!-- خامساً: الانطباع العام -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <span class="text-xl">📌</span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900">خامساً: الانطباع العام والرأي الشامل</h2>
                            <p class="text-xs text-slate-500">رأيك الصادق يساعدنا في تطوير التجربة للطلاب القادمين</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs sm:text-sm font-bold text-slate-800">11. أبرز ميزة لاحظتها في ASOTS:</label>
                        <input type="text" name="best_feature" placeholder="مثلاً: كفاءة المعلمين، المتابعة، المنصة، المرونة..."
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-xs sm:text-sm text-slate-800 bg-slate-50 transition">
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="block text-xs sm:text-sm font-bold text-slate-800">12. هل توصي بـ ASOTS للآخرين؟</label>
                        <div class="grid grid-cols-3 gap-3 max-w-md">
                            <label class="cursor-pointer">
                                <input type="radio" name="recommend_institute" value="yes" required class="peer sr-only">
                                <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 text-center font-bold text-xs sm:text-sm text-slate-700 peer-checked:text-emerald-800 transition">
                                    <span class="text-xl block mb-1">😍</span>
                                    <span>نعم بكل تأكيد</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="recommend_institute" value="maybe" class="peer sr-only">
                                <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 text-center font-bold text-xs sm:text-sm text-slate-700 peer-checked:text-amber-800 transition">
                                    <span class="text-xl block mb-1">🤔</span>
                                    <span>ربما</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="recommend_institute" value="no" class="peer sr-only">
                                <div class="p-3.5 rounded-2xl border-2 border-slate-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 text-center font-bold text-xs sm:text-sm text-slate-700 peer-checked:text-rose-800 transition">
                                    <span class="text-xl block mb-1">😞</span>
                                    <span>لا أوصي</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">ولماذا؟ (سبب التوصية أو عدمها):</label>
                        <input type="text" name="recommend_reason" placeholder="اذكر السبب باختصار..."
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-xs sm:text-sm text-slate-800 bg-slate-50 transition">
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <label class="block text-xs sm:text-sm font-black text-slate-900">13. ملاحظات ومقترحات إضافية تود إيصالها للإدارة:</label>
                        <textarea name="notes_suggestions" rows="3" placeholder="أي كلمة أو مقترح تشاركنا إياه بكل صدق وشفافية..."
                                  class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 text-xs sm:text-sm text-slate-800 bg-slate-50 transition"></textarea>
                    </div>
                </div>
            </div>
        @endif

        <!-- زر الإرسال والخاتمة -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-5 text-center">
            <div class="space-y-2 max-w-xl mx-auto">
                <span class="text-3xl block">🤍</span>
                <h3 class="text-lg font-black text-slate-900">شكراً لوقتك — فخورون بك وبإنجازك!</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    رأيك أمانة في أعناقنا وسيكون محل دراسة مباشرة من قِبل إدارة المكتب والمشرف الأكاديمي.
                </p>
            </div>

            <button type="submit" class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm sm:text-base shadow-xl shadow-emerald-600/25 transition transform hover:scale-[1.02] flex items-center justify-center gap-3 mx-auto">
                <span>إرسال التقييم رسمياً</span>
                <span>🚀</span>
            </button>
        </div>
    </form>

    <!-- معلومات التواصل وقناة الواتساب الرسمية -->
    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-200/70 text-center space-y-3">
        <p class="text-xs font-bold text-slate-600">
            للتواصل المباشر مع المؤسس والمدير د. فخرالدين الصوفي:
            <span class="font-mono text-indigo-600 mr-1" dir="ltr">+967 735 999 101 | +967 775 342 886</span>
        </p>
        <a href="https://whatsapp.com/channel/0029Vajnq1y7DAWxCf429S2T" target="_blank"
           class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 bg-emerald-100/70 px-4 py-2 rounded-xl hover:bg-emerald-200/80 transition">
            <span>📢 قناتنا الرسمية على واتساب</span>
            <span>↗</span>
        </a>
    </div>

</div>

<!-- تفاعلية النجوم والأزرار (Vanilla JS) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const starContainers = document.querySelectorAll('.star-rating');
    const labels = ['', 'ضعيف جداً 😞', 'مقبول 😐', 'جيد 🙂', 'جيد جداً 😊', 'ممتاز 🌟'];

    starContainers.forEach(container => {
        const targetId = container.getAttribute('data-target');
        const hiddenInput = document.getElementById(targetId);
        const stars = container.querySelectorAll('.star-btn');
        const labelSpan = container.querySelector('.rating-label');

        function updateStars(val) {
            stars.forEach((s, idx) => {
                if (idx < val) {
                    s.classList.remove('text-slate-300');
                    s.classList.add('text-amber-400');
                } else {
                    s.classList.remove('text-amber-400');
                    s.classList.add('text-slate-300');
                }
            });
            if (labelSpan && labels[val]) {
                labelSpan.textContent = labels[val];
                labelSpan.classList.add('text-amber-600', 'font-black');
            }
        }

        stars.forEach(star => {
            star.addEventListener('click', function () {
                const val = parseInt(this.getAttribute('data-val'));
                hiddenInput.value = val;
                updateStars(val);
            });

            star.addEventListener('mouseenter', function () {
                const val = parseInt(this.getAttribute('data-val'));
                updateStars(val);
            });
        });

        container.addEventListener('mouseleave', function () {
            const currentVal = parseInt(hiddenInput.value) || 0;
            updateStars(currentVal);
            if (labelSpan && currentVal === 0) {
                labelSpan.textContent = 'حدد تقييمك';
                labelSpan.classList.remove('text-amber-600', 'font-black');
            }
        });
    });

    // التحقق قبل الإرسال من النجوم المطلوبة
    const form = document.getElementById('survey-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const requiredHiddens = form.querySelectorAll('input[type="hidden"][required]');
            let missing = false;
            requiredHiddens.forEach(input => {
                if (!input.value) {
                    missing = true;
                    const ratingBox = input.closest('.star-rating');
                    if (ratingBox) {
                        ratingBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        ratingBox.classList.add('ring-2', 'ring-rose-500', 'p-2', 'rounded-xl', 'bg-rose-50');
                        setTimeout(() => {
                            ratingBox.classList.remove('ring-2', 'ring-rose-500', 'p-2', 'rounded-xl', 'bg-rose-50');
                        }, 3000);
                    }
                }
            });
            if (missing) {
                e.preventDefault();
                alert('يرجى تحديد تقييم النجوم لجميع الأسئلة المطلوبة قبل الإرسال ⭐');
            }
        });
    }
});
</script>
@endsection
