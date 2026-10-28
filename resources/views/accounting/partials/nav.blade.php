@php
    $currentRoute = Route::currentRouteName();
@endphp

<div class="bg-white/90 backdrop-blur border border-slate-200/80 rounded-2xl p-2.5 shadow-xs mb-6 space-y-2">
    <!-- السطر الأول: العمليات الأساسية والدليل -->
    <div class="flex items-center gap-1.5 overflow-x-auto min-w-max pb-1">
        <a href="{{ route('accounting.dashboard') }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.dashboard') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>💼</span>
            <span>لوحة السندات والصناديق</span>
        </a>

        <a href="{{ route('accounting.chart_of_accounts.index') }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.chart_of_accounts') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>🌳</span>
            <span>الدليل المحاسبي (COA)</span>
        </a>

        <a href="{{ route('accounting.cost_centers.index') }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.cost_centers') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>🏢</span>
            <span>مراكز التكلفة</span>
        </a>

        <a href="{{ route('accounting.payment_requests.index') }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.payment_requests') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>📝</span>
            <span>طلبات الصرف</span>
        </a>

        <a href="{{ route('accounting.journal_entries.index') }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.journal_entries') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>⚖️</span>
            <span>دفتر اليومية (GL)</span>
        </a>

        <a href="{{ route('accounting.reports.index') }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-2 {{ str_starts_with($currentRoute, 'accounting.reports') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>📊</span>
            <span>القوائم الختامية</span>
        </a>

        <div class="h-4 w-px bg-slate-200 mx-1"></div>

        <a href="{{ route('accounting.cash_boxes.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.cash_boxes') ? 'bg-emerald-600 text-white shadow-sm' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
            <span>🏦</span>
            <span>الصناديق والبنوك</span>
        </a>

        <a href="{{ route('accounting.pricing.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.pricing') ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            <span>🏷️</span>
            <span>التسعيرة والعقود</span>
        </a>
    </div>

    <!-- السطر الثاني: موديولات نظام المتكامل بلاس ERP (يمن سوفت) -->
    <div class="flex items-center gap-1.5 overflow-x-auto min-w-max pt-1 border-t border-slate-100">
        <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200 flex items-center gap-1">
            <span>🇾🇪</span>
            <span>المتكامل بلاس ERP:</span>
        </span>

        <a href="{{ route('accounting.periods.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.periods') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
            <span>📅</span>
            <span>الفترات المالية</span>
        </a>

        <a href="{{ route('accounting.options.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.options') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
            <span>⚙️</span>
            <span>خيارات النظام</span>
        </a>

        <a href="{{ route('accounting.opening_balances.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.opening_balances') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
            <span>⚖️</span>
            <span>الأرصدة الافتتاحية</span>
        </a>

        <a href="{{ route('accounting.budgets.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.budgets') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
            <span>📈</span>
            <span>الموازنة التقديرية</span>
        </a>

        <a href="{{ route('accounting.cheques.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.cheques') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
            <span>💳</span>
            <span>محفظة الشيكات</span>
        </a>

        <a href="{{ route('accounting.daily_portfolios.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.daily_portfolios') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
            <span>📋</span>
            <span>حوافظ اليومية</span>
        </a>

        <a href="{{ route('accounting.notes.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.notes') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
            <span>📑</span>
            <span>إشعارات مدين/دائن</span>
        </a>

        <a href="{{ route('accounting.cash_counts.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.cash_counts') ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-900' }}">
            <span>💵</span>
            <span>جرد الخزينة بالفئات</span>
        </a>

        <a href="{{ route('accounting.posting.index') }}" 
           class="px-3 py-1.5 rounded-xl text-xs font-black transition flex items-center gap-1.5 {{ str_starts_with($currentRoute, 'accounting.posting') ? 'bg-rose-600 text-white shadow-sm' : 'text-rose-700 bg-rose-50 hover:bg-rose-100' }}">
            <span>🔒</span>
            <span>الترحيل النهائي</span>
        </a>
    </div>
</div>
