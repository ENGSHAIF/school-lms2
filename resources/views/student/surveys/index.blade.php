@extends('layouts.app', ['title' => 'مركز استبيانات وتقييمات الطالب - ASOTS'])

@section('content')
<div class="space-y-6 max-w-5xl mx-auto pb-12">

    <!-- ترويسة مركز التقييمات للطلاب -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold border border-indigo-400/30">
                    صوت الطالب أمانة ورسالة 🌟
                </span>
                <span class="text-xs text-amber-400 font-bold">بإشراف د. فخرالدين الصوفي</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">استبيانات وتقييمات الطالب للمدرس والمنهج</h1>
            <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                رأيك يساهم مباشرة في تطوير كفاءة التدريس واختيار أفضل الأساليب لمجموعتك. اختر نوع التقييم المناسب للتوقيت الحالي وقدم رأيك بكل أريحية وسرية.
            </p>
        </div>

        <a href="{{ route('student.portal') }}" class="px-4 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
            <span>←</span>
            <span>العودة لبوابة الطالب</span>
        </a>
    </div>

    <!-- البطاقات الثلاث لاختيار وتعبئة التقييم -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- 1. التقييم اليومي السريع -->
        <div class="bg-white rounded-3xl p-6 border-2 border-amber-200 hover:border-amber-400 shadow-md transition transform hover:-translate-y-1 space-y-4 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex justify-between items-start">
                    <span class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl font-black">
                        ⚡
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 text-[11px] font-black border border-amber-200">
                        نهاية كل حصة
                    </span>
                </div>
                <h3 class="text-lg font-black text-slate-900">التقييم اليومي السريع</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    5 أسئلة مباشرة وسريعة لتقييم وضوح شرح الأستاذ في حصة اليوم ومدى استفادتك منها (يستغرق دقيقة واحدة).
                </p>
            </div>

            <a href="{{ route('surveys.create', ['type' => 'daily', 'group_id' => $studyGroup?->id, 'teacher_id' => $studyGroup?->teacher_id]) }}"
               class="w-full py-3 px-4 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs text-center shadow-md shadow-amber-500/20 transition flex items-center justify-center gap-2">
                <span>تعبئة تقييم حصة اليوم</span>
                <span>⭐</span>
            </a>
        </div>

        <!-- 2. التقييم الأسبوعي الشامل -->
        <div class="bg-white rounded-3xl p-6 border-2 border-indigo-200 hover:border-indigo-400 shadow-md transition transform hover:-translate-y-1 space-y-4 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex justify-between items-start">
                    <span class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl font-black">
                        📊
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-800 text-[11px] font-black border border-indigo-200">
                        نهاية كل أسبوع
                    </span>
                </div>
                <h3 class="text-lg font-black text-slate-900">التقييم الأسبوعي الشامل</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    استبانة أسبوعية تتناول كفاءة الشرح، ملاءمة المنهج، وتحديد المهارات والتحديات التي تحتاج دعماً إضافياً.
                </p>
            </div>

            <a href="{{ route('surveys.create', ['type' => 'weekly', 'group_id' => $studyGroup?->id, 'teacher_id' => $studyGroup?->teacher_id]) }}"
               class="w-full py-3 px-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs text-center shadow-md shadow-indigo-600/20 transition flex items-center justify-center gap-2">
                <span>تعبئة تقييم نهاية الأسبوع</span>
                <span>📈</span>
            </a>
        </div>

        <!-- 3. التقييم النهائي للدبلوم -->
        <div class="bg-white rounded-3xl p-6 border-2 border-emerald-200 hover:border-emerald-400 shadow-md transition transform hover:-translate-y-1 space-y-4 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex justify-between items-start">
                    <span class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl font-black">
                        🎓
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-black border border-emerald-200">
                        نهاية البرنامج
                    </span>
                </div>
                <h3 class="text-lg font-black text-slate-900">التقييم النهائي للدبلوم</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    تقييم المسار التدريبي الكامل وتجربة التخرج، لقياس الأثر التعليمي ومدى تحقيق أهدافك في ASOTS.
                </p>
            </div>

            <a href="{{ route('surveys.create', ['type' => 'diploma_final', 'group_id' => $studyGroup?->id, 'teacher_id' => $studyGroup?->teacher_id]) }}"
               class="w-full py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs text-center shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                <span>تعبئة التقييم النهائي للتخرج</span>
                <span>🏆</span>
            </a>
        </div>

    </div>

    <!-- سجل التقييمات السابقة للطالب -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-2xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span>📋</span>
                <span>سجل تقييماتي المرسلة سابقاً (آخر 15 تقييم)</span>
            </h2>
            <span class="text-xs text-slate-400 font-mono">{{ $previousSurveys->count() }} تقييم مُسجل</span>
        </div>

        @if($previousSurveys->isEmpty())
            <div class="text-center py-10 text-slate-400 space-y-2">
                <span class="text-4xl block">📝</span>
                <p class="text-xs font-bold">لم تقم بإرسال أي تقييم بعد. اختر أحد التقييمات أعلاه للبدء.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200/80">
                            <th class="p-3">نوع التقييم</th>
                            <th class="p-3">الأستاذ</th>
                            <th class="p-3">المجموعة</th>
                            <th class="p-3">تاريخ الإرسال</th>
                            <th class="p-3">التقييم العام</th>
                            <th class="p-3">الهوية</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($previousSurveys as $s)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="p-3 font-bold text-slate-900">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black border {{ $s->type_badge_class }}">
                                        {{ $s->type_icon }} {{ $s->type_label }}
                                    </span>
                                </td>
                                <td class="p-3 text-slate-700 font-bold">
                                    {{ $s->teacher ? $s->teacher->name : 'غير محدد' }}
                                </td>
                                <td class="p-3 text-slate-500">
                                    {{ $s->studyGroup ? ($s->studyGroup->name_ar ?: $s->studyGroup->name) : 'عام' }}
                                </td>
                                <td class="p-3 font-mono text-slate-600">
                                    {{ $s->survey_date->format('Y-m-d') }}
                                </td>
                                <td class="p-3">
                                    @if($s->overall_score)
                                        <span class="font-bold text-amber-500">★ {{ number_format($s->overall_score, 1) }}</span>
                                        <span class="text-[10px] text-slate-400">/ 5</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="p-3 text-slate-500">
                                    @if($s->is_anonymous)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px]">مجهول 🕵️</span>
                                    @else
                                        <span class="text-[10px] text-emerald-600 font-bold">باسمك الرسمي ✓</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
