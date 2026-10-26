@extends('layouts.app', ['title' => 'تصميم كويز أو اختبار جديد'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- شريط الترويسة والرجوع -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-indigo-900/50">
        <div>
            <div class="flex items-center gap-2 text-xs text-amber-400 font-bold mb-2">
                <a href="{{ route('admin.exams.index') }}" class="hover:underline flex items-center gap-1">
                    <span>←</span>
                    <span>الرجوع لبنك الاختبارات والكويزات</span>
                </a>
                @if($level)
                    <span>•</span>
                    <span>{{ $level->course?->displayName ?: $level->course?->name }} - {{ $level->name }}</span>
                @endif
            </div>
            <h1 class="text-2xl sm:text-3xl font-black flex items-center gap-2">
                <span>🎯</span>
                <span>تصميم كويز أو اختبار جديد</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                تحديد الاسم والنوع (كويز أو اختبار)، اختيار الدورة التدريبية والمستوى، وضبط قواعد التقييم وزمن الاختبار وبنك الأسئلة.
            </p>
        </div>

        <a href="{{ route('admin.exams.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition">
            إلغاء والعودة
        </a>
    </div>

    <!-- بطاقة النموذج -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ $level ? route('admin.levels.exams.store', $level->id) : route('admin.exams.store') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- 1. البيانات الأساسية وتحديد الدورة والمستوى -->
            <div class="space-y-4">
                <h3 class="font-black text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                    <span>📋</span>
                    <span>1. معلومات وتسمية الاختبار / الكويز وتحديد الدورة</span>
                </h3>

                <!-- أزرار الاختيار السريع للاسم والتصنيف (كويز أو اختبار) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-2">تسمية وتصنيف الاختبار (اختر تسمية سريعة أو اكتب الاسم الذي تريده):</label>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" onclick="setExamPreset('كويز أسبوعي', 'weekly', 'quiz')" class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-200 font-bold text-xs transition">
                            📅 كويز أسبوعي
                        </button>
                        <button type="button" onclick="setExamPreset('اختبار منتصف الدورة (نصفي)', 'midterm', 'exam')" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 font-bold text-xs transition">
                            🎯 اختبار نصفي
                        </button>
                        <button type="button" onclick="setExamPreset('اختبار نهاية الدورة (نهائي)', 'final', 'exam')" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 font-bold text-xs transition">
                            🏆 اختبار نهائي
                        </button>
                        <button type="button" onclick="setExamPreset('كويز قصير ومفاجئ', 'pop_quiz', 'quiz')" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-bold text-xs transition">
                            ⚡ كويز قصير
                        </button>
                        <button type="button" onclick="setExamPreset('اختبار تحديد وقياس مستوى', 'placement', 'exam')" class="px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200 font-bold text-xs transition">
                            🔍 اختبار تحديد مستوى
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- عنوان الاختبار -->
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">اسم الكويز أو الاختبار <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" id="examTitleInput" required value="{{ old('title') }}" placeholder="مثال: كويز القواعد والمفردات Unit 1 / اختبار نهاية دورة التوفل..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs font-bold">
                    </div>

                    <!-- اختيار الدورة التدريبية -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الدورة التدريبية المرتبطة <span class="text-rose-500">*</span></label>
                        <select name="course_id" id="courseSelect" onchange="filterLevelsByCourse(this.value)" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-xs">
                            <option value="">-- اختر الدورة التدريبية --</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ (old('course_id') == $c->id || ($level && $level->course_id == $c->id) || (isset($selectedCourse) && $selectedCourse?->id == $c->id)) ? 'selected' : '' }}>
                                    📚 {{ $c->displayName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- اختيار المستوى الأكاديمي (اختياري) -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">المستوى الأكاديمي (اختياري للدورة كاملة أو لمستوى محدد)</label>
                        <select name="level_id" id="levelSelect" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-xs">
                            <option value="">-- عام لكافة مستويات الدورة --</option>
                            @foreach($levels as $lvl)
                                <option value="{{ $lvl->id }}" data-course-id="{{ $lvl->course_id }}" {{ (old('level_id') == $lvl->id || ($level && $level->id == $lvl->id)) ? 'selected' : '' }}>
                                    🏷️ {{ $lvl->name }} ({{ $lvl->course?->displayName ?: $lvl->course?->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- نوع الاختبار / موقعه من الدورة -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">نوع الاختبار / الفترة <span class="text-rose-500">*</span></label>
                        <select name="quiz_period" id="quizPeriodSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-xs">
                            <option value="weekly" {{ old('quiz_period') === 'weekly' ? 'selected' : '' }}>📅 كويز أسبوعي (Weekly Quiz)</option>
                            <option value="midterm" {{ old('quiz_period') === 'midterm' ? 'selected' : '' }}>🎯 اختبار منتصف الدورة (نصفي - Midterm)</option>
                            <option value="final" {{ old('quiz_period') === 'final' ? 'selected' : '' }}>🏆 اختبار نهاية الدورة (نهائي - Final Exam)</option>
                            <option value="pop_quiz" {{ old('quiz_period') === 'pop_quiz' ? 'selected' : '' }}>⚡ كويز قصير مفاجئ (Pop Quiz)</option>
                            <option value="placement" {{ old('quiz_period') === 'placement' ? 'selected' : '' }}>🔍 اختبار تقييم مستوى</option>
                        </select>
                    </div>

                    <!-- الترم الدراسي -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الفصل الدراسي / الترم</label>
                        <input type="text" name="term" list="termsDatalist" value="{{ old('term') }}" placeholder="مثال: الترم الأول 2026" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs font-bold">
                        <datalist id="termsDatalist">
                            <option value="الترم الأول 2026">
                            <option value="الترم الثاني 2026">
                            <option value="الترم الصيفي 2026">
                            @foreach($allTerms as $t)
                                <option value="{{ $t }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">وصف أو تعليمات الاختبار للطلاب</label>
                    <textarea name="description" rows="2" placeholder="اكتب تعليمات الاختبار (مثال: أجب عن كافة الأسئلة خلال الوقت المحدد، ركز في الاختيارات)..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- 2. التوقيت والدرجات ونظام الأسئلة العشوائية -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="font-black text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                    <span>⏱️</span>
                    <span>2. المدة والدرجات ونظام الاختيارات العشوائية من البنك</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">مدة الاختبار (بالدقائق) <span class="text-rose-500">*</span></label>
                        <input type="number" name="duration_minutes" required min="1" max="300" value="{{ old('duration_minutes', 20) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-xs font-bold">
                        <span class="text-[10px] text-slate-400 block mt-1">يظهر للطالب عداد تنازلي بالدقائق.</span>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">درجة النجاح % <span class="text-rose-500">*</span></label>
                        <input type="number" name="passing_score" required min="1" max="100" value="{{ old('passing_score', 60) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-xs font-bold">
                        <span class="text-[10px] text-slate-400 block mt-1">النسبة المئوية لاعتبار الطالب ناجحاً.</span>
                    </div>

                    <div class="bg-indigo-50/60 p-3 rounded-2xl border border-indigo-200">
                        <label class="block font-black text-indigo-950 mb-1">عدد الأسئلة المعروضة للطالب</label>
                        <input type="number" name="random_questions_count" min="1" value="{{ old('random_questions_count') }}" placeholder="اتركه فارغاً لعرض كل الأسئلة" class="w-full px-3 py-2 rounded-xl border border-indigo-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-xs bg-white">
                        <span class="text-[10px] text-indigo-700 block mt-1">مثلاً: تضع 30 سؤالاً في البنك، ويظهر لكل طالب 10 أسئلة عشوائية فقط!</span>
                    </div>
                </div>

                <!-- خيارات العشوائية ومنع الغش -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <label class="flex items-start gap-3 p-3.5 bg-slate-50 hover:bg-indigo-50/40 rounded-2xl border border-slate-200 cursor-pointer transition">
                        <input type="checkbox" name="shuffle_questions" value="1" checked class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <div>
                            <span class="font-bold text-slate-900 block">خلط ترتيب الأسئلة عشوائياً (Shuffle Questions)</span>
                            <span class="text-[11px] text-slate-500">يتغير ترتيب الأسئلة لكل طالب لمنع الغش والتواطؤ.</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 bg-slate-50 hover:bg-indigo-50/40 rounded-2xl border border-slate-200 cursor-pointer transition">
                        <input type="checkbox" name="shuffle_options" value="1" checked class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <div>
                            <span class="font-bold text-slate-900 block">خلط خيارات الإجابة (Shuffle Options A,B,C,D)</span>
                            <span class="text-[11px] text-slate-500">تغيير مواقع الخيارات لكل سؤال عشوائياً بين الطلاب.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 3. صلاحيات إظهار النتيجة للطالب -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h3 class="font-black text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                    <span>🔒</span>
                    <span>3. صلاحيات وموعد إظهار النتيجة للطالب</span>
                </h3>

                <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl space-y-3">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="show_result_immediately" value="1" class="mt-0.5 rounded text-amber-600 focus:ring-amber-500 border-slate-300">
                        <div>
                            <span class="font-black text-amber-950 block text-xs">إظهار النتيجة للطالب فور الانتهاء تلقائياً</span>
                            <span class="text-[11px] text-amber-800">
                                إذا تم تفعيل هذا الخيار: يرى الطالب درجته فور الضغط على تسليم الاختبار.
                            </span>
                            <span class="text-[11px] text-slate-600 block mt-1 font-bold">
                                🛡️ إذا لم تفعل هذا الخيار (الافتراضي): يحجب النظام النتيجة عن الطالب، وتصل الإجابات للإدارة والمعلم لمراجعتها واعتماد إصدار النتيجة في الوقت المحدد.
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="pt-4 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('admin.exams.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                    <span>🚀</span>
                    <span>إنشاء الاختبار والانتقال لإضافة وتعديل الأسئلة</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    // تعبئة سريعة للاسم والنوع
    function setExamPreset(name, period, type) {
        document.getElementById('examTitleInput').value = name;
        document.getElementById('quizPeriodSelect').value = period;
    }

    // فلترة المستويات بحسب الدورة المختارة
    function filterLevelsByCourse(courseId) {
        const levelSelect = document.getElementById('levelSelect');
        const options = levelSelect.querySelectorAll('option');

        options.forEach(opt => {
            if (!opt.value) {
                opt.style.display = 'block';
                return;
            }
            const optCourseId = opt.getAttribute('data-course-id');
            if (!courseId || optCourseId === courseId) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    // تشغيل الفلترة عند التحميل الأولي إذا كانت الدورة محددة
    document.addEventListener('DOMContentLoaded', function() {
        const selectedCourseId = document.getElementById('courseSelect').value;
        if (selectedCourseId) {
            filterLevelsByCourse(selectedCourseId);
        }
    });
</script>
@endsection
