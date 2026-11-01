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
            <p class="text-xs text-slate-300 mt-1 flex flex-wrap items-center gap-x-2 gap-y-1">
                <span>المجموعة: <strong class="text-white">{{ $session->group?->name }}</strong></span> • 
                <span>المعلم: <strong class="text-indigo-300">{{ $session->teacher?->name }}</strong></span> • 
                <span>التاريخ: <strong class="text-amber-300 font-mono">{{ $session->session_date->format('Y-m-d') }}</strong></span>
                @if($session->evaluator_name)
                    • <span class="bg-indigo-500/30 text-amber-300 px-2.5 py-0.5 rounded-lg border border-indigo-400/30 font-bold">
                        👤 المقيم المعتمد: {{ $session->evaluator_name }} ({{ $session->evaluator_role ?: 'معلم' }})
                    </span>
                @endif
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

    <!-- أهداف الحصة والمنهج والملخص والواجب -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
        @if($session->lesson_objectives)
            <div class="bg-white p-5 rounded-3xl border border-slate-200 space-y-2 shadow-2xs">
                <strong class="text-indigo-950 font-black block text-sm flex items-center gap-1.5">
                    <span>🎯</span>
                    <span>أهداف الحصة (Lesson Objectives):</span>
                </strong>
                <p class="text-slate-700 leading-relaxed bg-indigo-50/40 p-3 rounded-2xl border border-indigo-100 whitespace-pre-line">
                    {{ $session->lesson_objectives }}
                </p>
            </div>
        @endif

        @if($session->curriculum_objectives)
            <div class="bg-white p-5 rounded-3xl border border-slate-200 space-y-2 shadow-2xs">
                <strong class="text-purple-950 font-black block text-sm flex items-center gap-1.5">
                    <span>📚</span>
                    <span>أهداف المنهج (Curriculum Objectives):</span>
                </strong>
                <p class="text-purple-900 leading-relaxed bg-purple-50/40 p-3 rounded-2xl border border-purple-100 whitespace-pre-line">
                    {{ $session->curriculum_objectives }}
                </p>
            </div>
        @endif

        @if($session->lesson_summary || $session->hasSummaryFile())
            <div class="bg-white p-5 rounded-3xl border border-slate-200 space-y-3 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <strong class="text-indigo-950 font-black block text-sm flex items-center gap-1.5">
                        <span>📖</span>
                        <span>ملخص محتوى الدرس والشرح:</span>
                    </strong>
                    @if($session->hasSummaryFile())
                        <a href="{{ route('groups.sessions.download', ['sessionId' => $session->id, 'type' => 'summary']) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200 transition shadow-2xs">
                            <span class="text-sm">{{ $session->summary_file_icon }}</span>
                            <span>تحميل ملف الملخص</span>
                            <span class="text-[10px] text-indigo-500 font-mono">({{ $session->lesson_summary_file_name ?: 'ملخص' }})</span>
                        </a>
                    @endif
                </div>
                @if($session->lesson_summary)
                    <p class="text-slate-700 leading-relaxed bg-slate-50 p-3 rounded-2xl border border-slate-100 whitespace-pre-line">
                        {{ $session->lesson_summary }}
                    </p>
                @endif
            </div>
        @endif

        @if($session->homework_assigned || $session->hasHomeworkFile())
            <div class="bg-white p-5 rounded-3xl border border-slate-200 space-y-3 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <strong class="text-amber-950 font-black block text-sm flex items-center gap-1.5">
                        <span>📝</span>
                        <span>الواجب والتكليف المنزلي:</span>
                    </strong>
                    @if($session->hasHomeworkFile())
                        <a href="{{ route('groups.sessions.download', ['sessionId' => $session->id, 'type' => 'homework']) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs rounded-xl border border-amber-200 transition shadow-2xs">
                            <span class="text-sm">{{ $session->homework_file_icon }}</span>
                            <span>تحميل ورقة الواجب والتكليف</span>
                            <span class="text-[10px] text-amber-600 font-mono">({{ $session->homework_file_name ?: 'تكليف' }})</span>
                        </a>
                    @endif
                </div>
                @if($session->homework_assigned)
                    <p class="text-amber-900 leading-relaxed bg-amber-50/50 p-3 rounded-2xl border border-amber-200 whitespace-pre-line">
                        {{ $session->homework_assigned }}
                    </p>
                @endif
            </div>
        @endif
    </div>

    <!-- جدول تقييم ومشاركات الطلاب الفردية (التقييم المزدوج) -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-2xs space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <span>⚖️</span>
                    <span>كشف التقييم المزدوج: تقييم النظام الآلي 🤖 مقابل تقييم المعلم المستقل 👨‍🏫</span>
                </h3>
                <span class="text-xs text-slate-400">تقييم موضوعي آلي مستند لنشاط الحضور والصوت والشات والتسليم + تقييم المعلم التربوي</span>
            </div>
            @if($session->evaluator_name)
                <span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-xl">
                    المسؤول عن الرصد: <strong>{{ $session->evaluator_name }}</strong> ({{ $session->evaluator_role ?: 'معلم' }})
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold">
                        <th class="py-3 px-3">الطالب</th>
                        <th class="py-3 px-3 text-center">حالة الحضور</th>
                        <th class="py-3 px-3 text-center bg-indigo-50/70 text-indigo-950 border-x border-indigo-100">
                            تقييم النظام الآلي (100) 🤖
                            <span class="block text-[9px] font-normal text-indigo-700">حضور (25) + صوت (25) + شات (25) + واجب (25)</span>
                        </th>
                        <th class="py-3 px-3 text-center bg-emerald-50/70 text-emerald-950 border-r border-emerald-100">
                            تقييم المعلم المستقل (100) 👨‍🏫
                            <span class="block text-[9px] font-normal text-emerald-700">مشاركة (5) + فهم (5) + واجب (5)</span>
                        </th>
                        <th class="py-3 px-3 text-center">النجوم ⭐</th>
                        <th class="py-3 px-3 text-center">الدرجة المعتمدة</th>
                        <th class="py-3 px-3">ملاحظات النظام والمعلم</th>
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

                            <!-- تقييم النظام التلقائي -->
                            <td class="py-3 px-3 text-center bg-indigo-50/30 border-x border-indigo-100">
                                <span class="text-sm font-black font-mono text-indigo-700 block">
                                    {{ $ev->system_total_score ?? 0 }}%
                                </span>
                                <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-500 font-mono mt-0.5">
                                    <span title="حضور">📍{{ $ev->system_attendance_score ?? 0 }}</span>
                                    <span title="مشاركة صوتية">🎙️{{ $ev->system_voice_score ?? 0 }}</span>
                                    <span title="تفاعل كتابي">💬{{ $ev->system_chat_score ?? 0 }}</span>
                                    <span title="تسليم تكليف">📝{{ $ev->system_task_score ?? 0 }}</span>
                                </div>
                            </td>

                            <!-- تقييم المعلم المستقل -->
                            <td class="py-3 px-3 text-center bg-emerald-50/30 border-r border-emerald-100">
                                <span class="text-sm font-black font-mono text-emerald-700 block">
                                    {{ $ev->teacher_total_score ?? $ev->daily_total_score }}%
                                </span>
                                <span class="text-[10px] text-slate-500 font-mono block mt-0.5">
                                    م: {{ $ev->participation_score }}/5 • ف: {{ $ev->understanding_score }}/5 • و: {{ $ev->homework_score }}/5
                                </span>
                            </td>

                            <td class="py-3 px-3 text-center font-mono font-black text-amber-500">
                                ⭐ {{ $ev->stars_awarded }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-black text-slate-900 text-sm">
                                {{ $ev->daily_total_score }}%
                            </td>
                            <td class="py-3 px-3 text-slate-600 text-[11px] space-y-1">
                                @if($ev->teacher_notes)
                                    <div><strong class="text-slate-700">المعلم:</strong> {{ $ev->teacher_notes }}</div>
                                @endif
                                @if($ev->system_notes)
                                    <div class="text-[10px] text-indigo-700 font-mono bg-indigo-50/60 p-1 rounded border border-indigo-100">
                                        {{ $ev->system_notes }}
                                    </div>
                                @endif
                                @if(!$ev->teacher_notes && !$ev->system_notes)
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
