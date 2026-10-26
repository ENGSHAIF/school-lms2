@extends('layouts.app', ['title' => 'قائمة الدخل والأرباح والخسائر'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>📈</span>
                <span>قائمة الدخل والأرباح والخسائر (Income Statement / P&L)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                تحليل نتيجة النشاط وصافي الأرباح: (إجمالي الإيرادات - إجمالي المصروفات التشغيلية والتدريبية)
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.reports.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                ← العودة لمركز التقارير
            </a>
        </div>
    </div>

    <!-- بطاقة نتيجة النشاط الرئيسية (صافي الربح أو الخسارة) -->
    <div class="p-8 rounded-3xl border {{ $data['is_profit'] ? 'bg-gradient-to-br from-emerald-900 to-teal-950 border-emerald-500/30' : 'bg-gradient-to-br from-rose-900 to-slate-950 border-rose-500/30' }} text-white shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
        <div class="space-y-2">
            <span class="text-xs font-bold text-slate-300 block tracking-wider uppercase">نتيجة النشاط التشغيلي للفترة</span>
            <h2 class="text-3xl sm:text-4xl font-black font-mono">
                {{ \App\Helpers\ArabicCurrencyHelper::format($data['net_profit'], 'YER') }}
            </h2>
            <div class="flex items-center gap-2 text-xs font-bold {{ $data['is_profit'] ? 'text-emerald-300' : 'text-rose-300' }}">
                <span>{{ $data['is_profit'] ? '🚀 صافي أرباح تشغيلية محققة' : '⚠️ صافي عجز / خسائر تشغيلية' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 w-full sm:w-auto">
            <div class="bg-white/10 backdrop-blur p-4 rounded-2xl border border-white/10 text-center">
                <span class="text-[11px] text-slate-300 block">إجمالي الإيرادات</span>
                <span class="text-lg font-black font-mono text-emerald-300 block mt-1">
                    {{ number_format($data['total_revenues'], 2) }}
                </span>
            </div>
            <div class="bg-white/10 backdrop-blur p-4 rounded-2xl border border-white/10 text-center">
                <span class="text-[11px] text-slate-300 block">إجمالي المصروفات</span>
                <span class="text-lg font-black font-mono text-rose-300 block mt-1">
                    {{ number_format($data['total_expenses'], 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- تفصيل الإيرادات والمصروفات -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- 1. بنود الإيرادات -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-200 bg-emerald-50/50 flex justify-between items-center">
                    <h3 class="font-black text-sm text-emerald-950 flex items-center gap-2">
                        <span>💰</span>
                        <span>إيرادات النشاط والخدمات (Revenues)</span>
                    </h3>
                    <span class="font-mono font-bold text-xs text-emerald-700">{{ number_format($data['total_revenues'], 2) }} ر.ي</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($data['revenues'] as $rev)
                        <div class="p-4 flex justify-between items-center hover:bg-slate-50 transition text-xs">
                            <div>
                                <span class="font-mono font-bold text-indigo-700 ml-1">[{{ $rev['code'] }}]</span>
                                <span class="font-bold text-slate-900">{{ $rev['name'] }}</span>
                            </div>
                            <span class="font-mono font-bold text-emerald-600">{{ number_format($rev['amount'], 2) }} ر.ي</span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 font-bold text-xs">لا توجد إيرادات مسجلة.</div>
                    @endforelse
                </div>
            </div>
            <div class="p-4 bg-emerald-50 border-t border-emerald-100 flex justify-between items-center font-black text-xs text-emerald-900">
                <span>مجموع الإيرادات:</span>
                <span class="font-mono text-sm">{{ number_format($data['total_revenues'], 2) }} ر.ي</span>
            </div>
        </div>

        <!-- 2. بنود المصروفات -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-200 bg-rose-50/50 flex justify-between items-center">
                    <h3 class="font-black text-sm text-rose-950 flex items-center gap-2">
                        <span>💸</span>
                        <span>المصروفات والتكاليف التدريبية (Expenses)</span>
                    </h3>
                    <span class="font-mono font-bold text-xs text-rose-700">{{ number_format($data['total_expenses'], 2) }} ر.ي</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($data['expenses'] as $exp)
                        <div class="p-4 flex justify-between items-center hover:bg-slate-50 transition text-xs">
                            <div>
                                <span class="font-mono font-bold text-indigo-700 ml-1">[{{ $exp['code'] }}]</span>
                                <span class="font-bold text-slate-900">{{ $exp['name'] }}</span>
                            </div>
                            <span class="font-mono font-bold text-rose-600">{{ number_format($exp['amount'], 2) }} ر.ي</span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 font-bold text-xs">لا توجد مصروفات مسجلة.</div>
                    @endforelse
                </div>
            </div>
            <div class="p-4 bg-rose-50 border-t border-rose-100 flex justify-between items-center font-black text-xs text-rose-900">
                <span>مجموع المصروفات:</span>
                <span class="font-mono text-sm">{{ number_format($data['total_expenses'], 2) }} ر.ي</span>
            </div>
        </div>

    </div>

</div>
@endsection
