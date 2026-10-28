@extends('layouts.app', ['title' => 'خيارات النظام والحسابات الوسيطة - يمن سوفت ERP'])

@section('content')
<div class="space-y-6">

    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>⚙️</span>
                <span>تهيئة خيارات النظام والحسابات الوسيطة (System Options)</span>
            </h1>
            <p class="text-xs text-slate-500">وفق الفصل الأول من دليل المتكامل بلاس ERP: ضبط سياسات الرقابة المحاسبية الصارمة، منع السحب على المكشوف، وتعيين الحسابات الوسيطة للشيكات وفروق العملة.</p>
        </div>
    </div>

    <form action="{{ route('accounting.options.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. خيارات الرقابة والعمليات -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200">
                <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                    <span>🛡️</span>
                    <span>خيارات الرقابة المالية ومنع التجاوزات</span>
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                
                <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200/80 bg-slate-50/50">
                    <input type="checkbox" name="accounting.prevent_overdraft" value="1" id="opt_overdraft" class="rounded text-amber-600 focus:ring-amber-500 h-4 w-4 mt-0.5" {{ ($settingsRaw['accounting.prevent_overdraft'] ?? '1') === '1' ? 'checked' : '' }}>
                    <label for="opt_overdraft" class="cursor-pointer">
                        <div class="font-black text-slate-900 mb-1">منع السحب على المكشوف للصناديق (Cash Overdraft Lock)</div>
                        <div class="text-slate-500 leading-relaxed">منع حفظ وترحيل أي سند صرف نقدي في حال كان المبلغ المطلوب يتجاوز الرصيد الفعلي المتاح في الصندوق المحدد.</div>
                    </label>
                </div>

                <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200/80 bg-slate-50/50">
                    <input type="checkbox" name="accounting.strict_period_lock" value="1" id="opt_period_lock" class="rounded text-amber-600 focus:ring-amber-500 h-4 w-4 mt-0.5" {{ ($settingsRaw['accounting.strict_period_lock'] ?? '1') === '1' ? 'checked' : '' }}>
                    <label for="opt_period_lock" class="cursor-pointer">
                        <div class="font-black text-slate-900 mb-1">الربط الصارم بالفترات المالية (Strict Period Lock)</div>
                        <div class="text-slate-500 leading-relaxed">رفض تسجيل أي سند قبض أو صرف أو قيد في حال كان تاريخ الحركة يقع ضمن شهر مقفل أو موقوف مالياً.</div>
                    </label>
                </div>

                <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200/80 bg-slate-50/50">
                    <input type="checkbox" name="accounting.allow_suspended_entries" value="1" id="opt_suspended" class="rounded text-amber-600 focus:ring-amber-500 h-4 w-4 mt-0.5" {{ ($settingsRaw['accounting.allow_suspended_entries'] ?? '1') === '1' ? 'checked' : '' }}>
                    <label for="opt_suspended" class="cursor-pointer">
                        <div class="font-black text-slate-900 mb-1">السماح بالسندات والقيود المعلقة (Draft / Suspended Mode)</div>
                        <div class="text-slate-500 leading-relaxed">إتاحة خيار حفظ السند أو القيد كمسودة تحت المراجعة دون التأثير الفوري على كشوفات الحسابات حتى اعتماده نهائياً.</div>
                    </label>
                </div>

                <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200/80 bg-slate-50/50">
                    <input type="checkbox" name="accounting.allow_daily_portfolios" value="1" id="opt_portfolios" class="rounded text-amber-600 focus:ring-amber-500 h-4 w-4 mt-0.5" {{ ($settingsRaw['accounting.allow_daily_portfolios'] ?? '1') === '1' ? 'checked' : '' }}>
                    <label for="opt_portfolios" class="cursor-pointer">
                        <div class="font-black text-slate-900 mb-1">تفعيل حوافظ الصرف والقبض اليومية (Daily Portfolios)</div>
                        <div class="text-slate-500 leading-relaxed">تمكين أمناء الصناديق من تجميع مقبوضات ومدفوعات اليوم في حافظة واحدة وترحيلها إجمالياً بسند رسمي موحد.</div>
                    </label>
                </div>

            </div>
        </div>

        <!-- 2. ربط الحسابات الوسيطة للشيكات وفروق العملة -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200">
                <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                    <span>🏦</span>
                    <span>تعيين الحسابات الوسيطة في الدليل المحاسبي (Clearing Accounts)</span>
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                
                <div>
                    <label class="block font-black text-slate-700 mb-1.5">حساب أوراق القبض برسم التحصيل (الشيكات الواردة):</label>
                    <select name="accounting.clearing_cheque_receivable_account_id" class="w-full rounded-xl border-slate-300 py-2 text-xs font-bold bg-white focus:ring-amber-500 focus:border-amber-500">
                        <option value="">-- اختياري: اختر حساب أوراق القبض --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ ($settingsRaw['accounting.clearing_cheque_receivable_account_id'] ?? '') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->account_code }} - {{ $acc->account_name }} ({{ $acc->currency }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">يُقيد عليه مبلغ الشيكات الواردة حتى تاريخ استحقاقها وتحصيلها في البنك.</p>
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">حساب أوراق الدفع المستحقة (الشيكات الصادرة):</label>
                    <select name="accounting.clearing_cheque_payable_account_id" class="w-full rounded-xl border-slate-300 py-2 text-xs font-bold bg-white focus:ring-amber-500 focus:border-amber-500">
                        <option value="">-- اختياري: اختر حساب أوراق الدفع --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ ($settingsRaw['accounting.clearing_cheque_payable_account_id'] ?? '') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->account_code }} - {{ $acc->account_name }} ({{ $acc->currency }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">يُقيد عليه الشيكات الصادرة للمستفيدين حتى يتم خصمها فعلياً من كشف حساب البنك.</p>
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">حساب أرباح فروق أسعار العملة (إيرادات):</label>
                    <select name="accounting.currency_diff_gain_account_id" class="w-full rounded-xl border-slate-300 py-2 text-xs font-bold bg-white focus:ring-amber-500 focus:border-amber-500">
                        <option value="">-- اختياري: اختر حساب أرباح العملة --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ ($settingsRaw['accounting.currency_diff_gain_account_id'] ?? '') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->account_code }} - {{ $acc->account_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-black text-slate-700 mb-1.5">حساب خسائر فروق أسعار العملة (مصروفات):</label>
                    <select name="accounting.currency_diff_loss_account_id" class="w-full rounded-xl border-slate-300 py-2 text-xs font-bold bg-white focus:ring-amber-500 focus:border-amber-500">
                        <option value="">-- اختياري: اختر حساب خسائر العملة --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ ($settingsRaw['accounting.currency_diff_loss_account_id'] ?? '') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->account_code }} - {{ $acc->account_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                    <span>💾</span>
                    <span>حفظ وتطبيق إعدادات يمن سوفت ERP</span>
                </button>
            </div>
        </div>

    </form>

</div>
@endsection
