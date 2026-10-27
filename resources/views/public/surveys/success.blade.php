@extends('layouts.app', ['title' => 'تم استلام التقييم بنجاح - ASOTS'])

@section('content')
<div class="max-w-xl mx-auto py-12 px-4 text-center space-y-6">

    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/80 shadow-xl space-y-6">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center text-4xl mx-auto shadow-inner animate-bounce">
            ✓
        </div>

        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                {{ $survey->type_label }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900">تم إرسال تقييمك بنجاح! 🎉</h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-md mx-auto">
                شكراً لمشاركتك القيّمة. صوتك ورأيك يُسهمان مباشرة في رفع جودة الأداء الأكاديمي والتدريبي في عائلة ASOTS.
            </p>
        </div>

        <!-- بطاقة ملخص الاستبيان -->
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/70 text-right space-y-2.5 text-xs text-slate-700">
            <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                <span class="text-slate-400 font-bold">الأستاذ المعني:</span>
                <span class="font-bold text-slate-900">{{ $survey->teacher ? $survey->teacher->name : 'غير محدد' }}</span>
            </div>
            @if($survey->studyGroup)
                <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                    <span class="text-slate-400 font-bold">المجموعة:</span>
                    <span class="font-bold text-slate-900">{{ $survey->studyGroup->name_ar ?: $survey->studyGroup->name }}</span>
                </div>
            @endif
            <div class="flex justify-between items-center py-1 border-b border-slate-200/50">
                <span class="text-slate-400 font-bold">تاريخ التقييم:</span>
                <span class="font-bold text-slate-900 font-mono">{{ $survey->survey_date->format('Y-m-d') }}</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-slate-400 font-bold">حالة التقييم:</span>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">معتمد ومسجل 🔒</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            @if(auth()->check())
                <a href="{{ route('student.portal') }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/20 transition">
                    العودة لبوابة الطالب 🏠
                </a>
                <a href="{{ route('student.surveys.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                    سجل تقييماتي 📋
                </a>
            @else
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/20 transition">
                    الصفحة الرئيسية 🏠
                </a>
                <a href="{{ route('surveys.create', ['type' => $survey->survey_type]) }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                    إرسال تقييم جديد ➕
                </a>
            @endif
        </div>
    </div>

</div>
@endsection
