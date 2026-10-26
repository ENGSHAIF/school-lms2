@php
    $currentRoute = Route::currentRouteName();
@endphp

<div class="bg-white/80 backdrop-blur border border-slate-200/80 rounded-2xl p-2 shadow-xs mb-6 overflow-x-auto">
    <div class="flex items-center gap-1.5 min-w-max">
        <a href="{{ route('accounting.dashboard') }}" 
           class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>💼</span>
            <span>لوحة السندات والصناديق</span>
        </a>

        <a href="{{ route('accounting.chart_of_accounts.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.chart_of_accounts') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>🌳</span>
            <span>الدليل المحاسبي الشجري (COA)</span>
        </a>

        <a href="{{ route('accounting.cost_centers.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.cost_centers') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>🏢</span>
            <span>شجرة مراكز التكلفة</span>
        </a>

        <a href="{{ route('accounting.payment_requests.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.payment_requests') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>📝</span>
            <span>دورة طلبات الصرف والاعتمادات</span>
        </a>

        <a href="{{ route('accounting.journal_entries.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.journal_entries') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>⚖️</span>
            <span>دفتر قيود اليومية العامة (GL)</span>
        </a>

        <a href="{{ route('accounting.reports.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.reports') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>📊</span>
            <span>التقارير والقوائم الختامية</span>
        </a>

        <div class="h-5 w-px bg-slate-200 mx-1"></div>

        <a href="{{ route('accounting.cash_boxes.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.cash_boxes') ? 'bg-emerald-600 text-white shadow-sm' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
            <span>🏦</span>
            <span>الصناديق والبنوك</span>
        </a>

        <a href="{{ route('accounting.pricing.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.pricing') ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>🏷️</span>
            <span>التسعيرة والعقود</span>
        </a>
    </div>
</div>
