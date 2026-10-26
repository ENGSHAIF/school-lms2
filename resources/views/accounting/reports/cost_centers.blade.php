@extends('layouts.app', ['title' => 'تقرير ربحية مراكز التكلفة'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>🏢</span>
                <span>تقرير ربحية مراكز التكلفة والمشاريع (Cost Centers P&L)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                تحليل دقيق لصافي الأرباح المحققة وتكلفة كل دورة تدريبية وقسم مهني على حدة
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.reports.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                ← العودة لمركز التقارير
            </a>
        </div>
    </div>

    <!-- شبكة تقارير القطاعات والمراكز -->
    <div class="space-y-6">
        @foreach($report as $sec)
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 bg-slate-900 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-black px-2 py-0.5 rounded bg-indigo-500 text-white">
                                {{ $sec['main_center']->code }}
                            </span>
                            <h2 class="text-base font-black">{{ $sec['main_center']->name }}</h2>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">{{ $sec['main_center']->description }}</p>
                    </div>

                    <div class="flex items-center gap-6 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px]">إجمالي الإيرادات:</span>
                            <span class="font-mono font-black text-emerald-400 text-sm">{{ number_format($sec['total_rev'], 2) }} ر.ي</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">إجمالي المصروفات:</span>
                            <span class="font-mono font-black text-rose-400 text-sm">{{ number_format($sec['total_exp'], 2) }} ر.ي</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10">
                            <span class="text-slate-300 block text-[10px]">صافي ربح القطاع:</span>
                            <span class="font-mono font-black {{ $sec['net_profit'] >= 0 ? 'text-emerald-300' : 'text-rose-300' }} text-sm">
                                {{ number_format($sec['net_profit'], 2) }} ر.ي
                            </span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-black border-b border-slate-200">
                            <tr>
                                <th class="p-4 w-28">كود المركز</th>
                                <th class="p-4">اسم البرنامج / الدورة / مركز التكلفة</th>
                                <th class="p-4 text-left w-36">إجمالي الإيرادات</th>
                                <th class="p-4 text-left w-36">إجمالي المصروفات</th>
                                <th class="p-4 text-left w-36">صافي الربح / الخسارة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($sec['children'] as $child)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-4 font-mono font-black text-indigo-700">{{ $child['code'] }}</td>
                                    <td class="p-4 font-bold text-slate-900">{{ $child['name'] }}</td>
                                    <td class="p-4 font-mono font-bold text-left text-emerald-600">
                                        {{ number_format($child['revenues'], 2) }} ر.ي
                                    </td>
                                    <td class="p-4 font-mono font-bold text-left text-rose-600">
                                        {{ number_format($child['expenses'], 2) }} ر.ي
                                    </td>
                                    <td class="p-4 font-mono font-black text-left {{ $child['profit'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ number_format($child['profit'], 2) }} ر.ي
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400 font-bold">لا توجد مراكز تكلفة فرعية مسجلة تحت هذا القطاع.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
