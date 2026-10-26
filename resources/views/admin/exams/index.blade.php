@extends('layouts.app', ['title' => 'بنك الاختبارات والكويزات للدورات والمستويات'])

@section('content')
<div class="space-y-6">

    <!-- شريط الترويسة الرئيسي -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-indigo-900/50">
        <div>
            <div class="flex items-center gap-2 text-xs text-amber-400 font-bold mb-2">
                <a href="{{ route('admin.courses.index') }}" class="hover:underline">المناهج والأسعار</a>
                @if($level)
                    <span>/</span>
                    <a href="{{ route('admin.levels.resources', $level->id) }}" class="hover:underline">{{ $level->course?->displayName ?: $level->course?->name }} - {{ $level->name }}</a>
                @endif
                <span>/</span>
                <span class="text-white">بنك الاختبارات والكويزات</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black flex items-center gap-2.5">
                <span>🎯</span>
                <span>بنك الاختبارات والكويزات الإلكترونية</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                إدارة وتصميم الكويزات الأسبوعية، الامتحانات النصفية والنهائية للدورات والمستويات، تعديل وتحديث الأسئلة والخيارات والإجابات الصحيحة في بنك الأسئلة مع ميزة الخلط العشوائي.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap w-full md:w-auto">
            <a href="{{ $level ? route('admin.levels.exams.create', $level->id) : route('admin.exams.create') }}"
               class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                <span>➕</span>
                <span>تصميم كويز أو اختبار جديد</span>
            </a>
            @if($level)
                <a href="{{ route('admin.levels.resources', $level->id) }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition">
                    ← بنك موارد المستوى
                </a>
            @else
                <a href="{{ route('admin.exams_management.index') }}" class="px-4 py-2.5 bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-200 border border-indigo-400/30 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <span>🎓</span>
                    <span>مركز إدارة الاختبارات والشهائد</span>
                </a>
            @endif
        </div>
    </div>

    <!-- رسائل النجاح والخطأ -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-black">✕</button>
        </div>
    @endif

    <!-- شريط الفلترة والبحث -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ $level ? route('admin.levels.exams.index', $level->id) : route('admin.exams.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">

            <!-- اختيار الدورة التعليمية -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">الدورة التدريبية:</label>
                <select name="course_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs bg-white font-medium" onchange="this.form.submit()">
                    <option value="">جميع الدورات التدريبية</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->displayName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- اختيار المستوى -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">المستوى التعليمي:</label>
                <select name="level_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs bg-white font-medium" onchange="this.form.submit()">
                    <option value="">جميع المستويات</option>
                    @foreach($levels as $l)
                        <option value="{{ $l->id }}" {{ (request('level_id') == $l->id || ($level && $level->id == $l->id)) ? 'selected' : '' }}>
                            {{ $l->course?->name }} - {{ $l->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- نوع الاختبار -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">نوع الكويز / التوقيت:</label>
                <select name="quiz_period" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs bg-white font-medium" onchange="this.form.submit()">
                    <option value="all">جميع الأنواع</option>
                    <option value="weekly" {{ request('quiz_period') == 'weekly' ? 'selected' : '' }}>📅 كويز أسبوعي (Weekly)</option>
                    <option value="midterm" {{ request('quiz_period') == 'midterm' ? 'selected' : '' }}>🎯 نصفي (Midterm)</option>
                    <option value="final" {{ request('quiz_period') == 'final' ? 'selected' : '' }}>🏆 نهائي (Final)</option>
                    <option value="pop_quiz" {{ request('quiz_period') == 'pop_quiz' ? 'selected' : '' }}>⚡ كويز مفاجئ (Pop Quiz)</option>
                    <option value="placement" {{ request('quiz_period') == 'placement' ? 'selected' : '' }}>🔍 تحديد مستوى</option>
                </select>
            </div>

            <!-- البحث بالاسم -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">البحث في العنوان:</label>
                <div class="flex items-center gap-1.5">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم..." class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs font-medium">
                    <button type="submit" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition">
                        🔍
                    </button>
                </div>
            </div>

            <div class="flex items-end">
                <a href="{{ $level ? route('admin.levels.exams.index', $level->id) : route('admin.exams.index') }}" class="w-full py-2 px-3 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    إعادة ضبط الفلاتر ✕
                </a>
            </div>
        </form>
    </div>

    <!-- شبكة بطاقات الاختبارات والكويزات -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($exams as $exam)
            <div class="bg-white rounded-3xl border border-slate-200 hover:border-indigo-400 transition shadow-sm flex flex-col justify-between overflow-hidden">

                <!-- رأس البطاقة -->
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black border {{ $exam->quiz_period_badge }}">
                            {{ $exam->quiz_period_label }}
                        </span>

                        @if($exam->term)
                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full">
                                🎓 {{ $exam->term }}
                            </span>
                        @endif
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-indigo-600 block mb-1">
                            {{ $exam->course?->displayName ?: ($exam->level?->course?->displayName ?: 'دورة عامة') }}
                            @if($exam->level)
                                • مستوى: {{ $exam->level->name }}
                            @else
                                • عام للدورة
                            @endif
                        </span>
                        <h3 class="font-black text-base text-slate-900 leading-snug">
                            {{ $exam->title }}
                        </h3>
                    </div>

                    @if($exam->description)
                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                            {{ $exam->description }}
                        </p>
                    @endif

                    <!-- مؤشرات سريعة للبطاقة -->
                    <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-100 text-center font-mono text-[11px]">
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block font-sans">الأسئلة</span>
                            <strong class="text-slate-900 font-black">{{ $exam->questions->count() }}</strong>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block font-sans">الدرجة</span>
                            <strong class="text-indigo-700 font-black">{{ $exam->total_points }} د</strong>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block font-sans">المدة</span>
                            <strong class="text-slate-700 font-black">{{ $exam->duration_minutes }} د</strong>
                        </div>
                    </div>

                    <!-- إحصائيات المشاركات والمحاولات -->
                    @php $submissionsCount = $exam->attempts->count(); @endphp
                    <div class="bg-indigo-50/60 p-3 rounded-2xl border border-indigo-100 flex items-center justify-between text-xs">
                        <span class="font-bold text-indigo-900">محاولات الطلاب:</span>
                        <a href="{{ route('admin.exams.attempts', $exam->id) }}" class="px-2.5 py-1 bg-indigo-600 text-white rounded-lg font-black text-xs hover:bg-indigo-700 transition">
                            {{ $submissionsCount }} محاولة 👁️
                        </a>
                    </div>
                </div>

                <!-- أسفل البطاقة: الإجراءات وأزرار التحكم -->
                <div class="p-4 bg-slate-50 border-t border-slate-100 grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.exams.edit', $exam->id) }}" class="py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs text-center transition flex items-center justify-center gap-1 shadow-sm">
                        <span>✏️</span>
                        <span>بنك الأسئلة والاسم</span>
                    </a>

                    <a href="{{ route('admin.exams.attempts', $exam->id) }}" class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs text-center transition flex items-center justify-center gap-1 shadow-sm">
                        <span>📊</span>
                        <span>النتائج والاعتماد</span>
                    </a>

                    <form method="POST" action="{{ route('admin.exams.destroy', $exam->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكويز وبنك أسئلته؟ سيتم حذف جميع المحاولات المرتبطة به أيضاً!');" class="col-span-2 pt-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-center text-[11px] text-rose-500 hover:text-rose-700 hover:underline font-bold">
                            حذف الاختبار نهائياً ✕
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-3xl border border-dashed border-slate-300 text-center space-y-4">
                <span class="text-4xl block">📝</span>
                <h3 class="text-base font-bold text-slate-800">لا توجد اختبارات أو كويزات مطابقة للبحث</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    يمكنك الآن تصميم كويزات أسبوعية، امتحانات نصفية أو نهائية وفق كتاب الدورة ومشاركتها مع المجموعات الدراسية.
                </p>
                <a href="{{ route('admin.exams.create') }}" class="inline-block px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                    ➕ تصميم كويز أو اختبار جديد
                </a>
            </div>
        @endforelse
    </div>

    <!-- ترقيم الصفحات -->
    @if($exams->hasPages())
        <div class="pt-4">
            {{ $exams->withQueryString()->links() }}
        </div>
    @endif

</div>
@endsection
