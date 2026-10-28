@extends('layouts.app', ['title' => 'الفترات المحاسبية وإقفال الشهور - يمن سوفت ERP'])

@section('content')
<div class="space-y-6">

    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>📅</span>
                <span>الفترات المحاسبية وإقفال الشهور (Accounting Periods)</span>
            </h1>
            <p class="text-xs text-slate-500">وفق دليل نظام المتكامل بلاس ERP: التحكم بحالة الشهور المالية الـ 12، إيقاف العمليات، والإقفال الدوري والسنوي لمنع أي تلاعب أو ترحيل غير مصرح به.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-black bg-amber-100 text-amber-800 px-3 py-1.5 rounded-xl border border-amber-300">
                السنة المالية: {{ $year }}
            </span>
        </div>
    </div>

    <!-- جدول الفترات -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>🗓️</span>
                <span>جدول فترات العام المالي {{ $year }} (12 شهراً)</span>
            </h2>
            <span class="text-xs text-slate-500">الفترات المقفلة تمنع إضافة أو تعديل السندات والقيود تلقائياً</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-100/80 text-slate-700 font-black border-b border-slate-200">
                        <th class="p-3">الفترة #</th>
                        <th class="p-3">اسم الفترة</th>
                        <th class="p-3">تاريخ البداية</th>
                        <th class="p-3">تاريخ النهاية</th>
                        <th class="p-3">الحالة الحالية</th>
                        <th class="p-3">تاريخ الإقفال / المسؤول</th>
                        <th class="p-3">ملاحظات الإقفال</th>
                        <th class="p-3 text-center">إجراءات التحكم (يمن سوفت)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($periods as $period)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="p-3 font-bold text-slate-900">#{{ $period->period_number }}</td>
                        <td class="p-3 font-black text-indigo-900 flex items-center gap-1.5">
                            <span>📅</span>
                            <span>{{ $period->name }}</span>
                        </td>
                        <td class="p-3 font-mono text-slate-600">{{ $period->start_date->format('Y-m-d') }}</td>
                        <td class="p-3 font-mono text-slate-600">{{ $period->end_date->format('Y-m-d') }}</td>
                        <td class="p-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-black border {{ $period->status_badge_class }}">
                                {{ $period->status_label }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-500 text-[11px]">
                            @if($period->closed_at)
                                <div class="font-bold text-slate-800">{{ $period->closed_at->format('Y-m-d H:i') }}</div>
                                <div>بواسطة: {{ $period->closer?->name ?? 'المسؤول المالي' }}</div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="p-3 text-slate-600 text-[11px]">
                            {{ $period->notes ?? '-' }}
                        </td>
                        <td class="p-3 text-center">
                            <form action="{{ route('accounting.periods.toggle', $period->id) }}" method="POST" class="inline-flex items-center gap-1">
                                @csrf
                                <select name="status" class="text-xs rounded-lg border-slate-300 py-1 px-2 font-bold bg-white focus:ring-amber-500 focus:border-amber-500">
                                    <option value="open" {{ $period->status === 'open' ? 'selected' : '' }}>فتح (مفتوحة)</option>
                                    <option value="suspended" {{ $period->status === 'suspended' ? 'selected' : '' }}>إيقاف مؤقت</option>
                                    <option value="closed" {{ $period->status === 'closed' ? 'selected' : '' }}>إقفال نهائي</option>
                                </select>
                                <button type="submit" class="px-2.5 py-1 text-xs font-black bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition">
                                    تطبيق
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-400">لا توجد فترات مالية مسجلة لهذا العام.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
