@extends('layouts.app', ['title' => 'تفاصيل تقييم الحصة اليومية'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- ترويسة الجلسة -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border border-slate-800">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold border border-indigo-400/30">
                    تقرير الحصة المنعقدة 📋
                </span>
                <span class="text-xs text-amber-400 font-bold font-mono">حصة رقم #{{ $session->session_number }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black">{{ $session->lesson_topic }}</h1>
            <p class="text-xs text-slate-300 mt-1">
                المجموعة: <strong class="text-white">{{ $session->group?->name }}</strong> • 
                المعلم: <strong class="text-indigo-300">{{ $session->teacher?->name }}</strong> • 
                التاريخ: <strong class="text-amber-300 font-mono">{{ $session->session_date->format('Y-m-d') }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.groups.evaluations.print', ['session_id' => $session->id]) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <span>🖨️</span>
                <span>طباعة التقرير</span>
            </a>
            <a href="{{ route('admin.groups.evaluations.index', ['group_id' => $session->study_group_id]) }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition border border-slate-700">
                رجوع ✕
            </a>
        </div>
    </div>

    <!-- بطاقات إحصائيات الحضور للحصة -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-[10px] text-slate-400 font-bold block">إجمالي الطلاب</span>
            <span class="text-xl font-black text-slate-900 font-mono">{{ $session->total_students }}</span>
        </div>
        <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-200">
            <span class="text-[10px] text-emerald-800 font-bold block">حاضرون 🟢</span>
            <span class="text-xl font-black text-emerald-700 font-mono">{{ $session->present_count }}</span>
        </div>
        <div class="bg-rose-50 p-4 rounded-2xl border border-rose-200">
            <span class="text-[10px] text-rose-800 font-bold block">غائبون 🔴</span>
            <span class="text-xl font-black text-rose-700 font-mono">{{ $session->absent_count }}</span>
        </div>
        <div class="bg-slate-900 text-white p-4 rounded-2xl border border-slate-800">
            <span class="text-[10px] text-emerald-400 font-bold block">نسبة الحضور</span>
            <span class="text-xl font-black font-mono text-emerald-400">{{ $session->attendance_rate }}%</span>
        </div>
    </div>

    <!-- ملخص محتوى الدرس والواجب -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
        @if($session->lesson_summary)
            <div class="bg-white p-5 rounded-3xl border border-slate-200 space-y-2 shadow-2xs">
                <strong class="text-indigo-950 font-black block text-sm flex items-center gap-1.5">
                    <span>📖</span>
                    <span>ملخص محتوى الدرس والشرح:</span>
                </strong>
                <p class="text-slate-700 leading-relaxed bg-slate-50 p-3 rounded-2xl border border-slate-100">
                    {{ $session->lesson_summary }}
                </p>
            </div>
        @endif

        @if($session->homework_assigned)
            <div class="bg-white p-5 rounded-3xl border border-slate-200 space-y-2 shadow-2xs">
                <strong class="text-amber-950 font-black block text-sm flex items-center gap-1.5">
                    <span>📝</span>
                    <span>الواجب والتكليف المنزلي:</span>
                </strong>
                <p class="text-amber-900 leading-relaxed bg-amber-50/50 p-3 rounded-2xl border border-amber-200">
                    {{ $session->homework_assigned }}
                </p>
            </div>
        @endif
    </div>

    <!-- جدول تقييم ومشاركات الطلاب الفردية -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-2xs space-y-4">
        <h3 class="text-sm font-black text-slate-900 border-b border-slate-100 pb-3">
            كشف حضور وتقييم طلاب المجموعة في هذه الجلسة
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold">
                        <th class="py-3 px-3">الطالب</th>
                        <th class="py-3 px-3 text-center">حالة الحضور</th>
                        <th class="py-3 px-3 text-center">المشاركة (5)</th>
                        <th class="py-3 px-3 text-center">الاستيعاب (5)</th>
                        <th class="py-3 px-3 text-center">الواجب (5)</th>
                        <th class="py-3 px-3 text-center">نوع التفاعل</th>
                        <th class="py-3 px-3 text-center">النجوم الممنوحة</th>
                        <th class="py-3 px-3 text-center">الدرجة المحسوبة</th>
                        <th class="py-3 px-3">ملاحظة المعلم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($session->evaluations as $ev)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3 font-bold text-slate-900">
                                {{ $ev->student?->name }}
                                <span class="text-[10px] text-slate-400 block font-mono font-normal">
                                    {{ $ev->student?->student_code ?? $ev->student?->user_code }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $ev->attendance_badge_class }}">
                                    {{ $ev->attendance_label }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-bold">{{ $ev->participation_score }}/5</td>
                            <td class="py-3 px-3 text-center font-mono font-bold">{{ $ev->understanding_score }}/5</td>
                            <td class="py-3 px-3 text-center font-mono font-bold">{{ $ev->homework_score }}/5</td>
                            <td class="py-3 px-3 text-center text-[11px] text-slate-600">
                                {{ $ev->spoke_voice ? '🎙️ صوت' : '' }}
                                {{ $ev->wrote_chat ? '💬 شات' : '' }}
                                {{ $ev->submitted_task ? '📝 واجب' : '' }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-black text-amber-500">
                                ⭐ {{ $ev->stars_awarded }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-bold text-indigo-700">
                                {{ $ev->daily_total_score }}%
                            </td>
                            <td class="py-3 px-3 text-slate-500 text-[11px]">
                                {{ $ev->teacher_notes ?: '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
