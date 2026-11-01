@extends('layouts.app', ['title' => 'تهيئة بيانات العملات والمصارفة - المتكامل بلاس ERP'])

@section('content')
<div class="space-y-6">

    @include('accounting.partials.nav')

    <!-- الترويسة وبطاقات الإحصائيات -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-400/30">
                    نظام إدارة النظام • تهيئة العملات (ERP Plus v6) 🪙
                </span>
                <span class="text-xs text-indigo-300 font-bold font-mono">الفصل الأول - ص 27-29</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">بيانات العملات وأسعار الصرف والمصارفة الآلية</h1>
            <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                ترميز العملات المحلية والأجنبية، تحديد أسعار التحويل (سعر الصرف)، أعلى وأدنى حد للتحويل، أسعار الشراء والبيع، الفكة، وفئات العملة. يقوم النظام بالمصارفة التلقائية لكافة الحركات وكشوفات الحسابات بين العملات.
            </p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <button type="button" onclick="openAddCurrencyModal()" class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                <span>➕</span>
                <span>إضافة عملة جديدة (ERP Plus)</span>
            </button>
        </div>
    </div>

    <!-- بطاقات الـ KPI السريعة -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">العملة المحلية الأساسية للمنشأة</span>
            <div class="text-xl font-black text-emerald-700 font-mono flex items-center gap-2">
                <span>🇾🇪</span>
                <span>{{ $localCurrency?->name ?? 'ريال يمني' }}</span>
                <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold">رئيسية (1.0)</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">إجمالي العملات المرمزة</span>
            <div class="text-2xl font-black text-slate-900 font-mono flex items-center gap-1.5">
                <span>🪙</span>
                <span>{{ $currencies->count() }}</span>
                <span class="text-xs font-normal text-slate-400">عملات</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">سعر صرف الريال السعودي (SAR)</span>
            <div class="text-2xl font-black text-indigo-700 font-mono flex items-center gap-1.5">
                <span>🇸🇦</span>
                <span>{{ \App\Models\Currency::getRate('SAR') }}</span>
                <span class="text-xs font-normal text-slate-400">ريال يمني</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">سعر صرف الدولار الأمريكي (USD)</span>
            <div class="text-2xl font-black text-blue-700 font-mono flex items-center gap-1.5">
                <span>🇺🇸</span>
                <span>{{ \App\Models\Currency::getRate('USD') }}</span>
                <span class="text-xs font-normal text-slate-400">ريال يمني</span>
            </div>
        </div>
    </div>

    <!-- حاسبة ومصارفة العملات الفورية المباشرة -->
    <div class="bg-gradient-to-r from-amber-500/10 via-indigo-500/10 to-emerald-500/10 p-5 rounded-3xl border border-amber-200/60 shadow-2xs space-y-3">
        <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
            <span class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                <span>⚡</span>
                <span>حاسبة المصارفة الفورية والتحويل التلقائي (Live Multi-Currency Converter):</span>
            </span>
            <span class="text-[10px] text-slate-500 font-mono">محتسبة تلقائياً بأسعار الصرف المعتمدة أعلاه</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">المبلغ المراد تحويله:</label>
                <input type="number" id="calc_amount" value="1000" min="0.01" step="any" oninput="runLiveConversion()" class="w-full bg-white border border-slate-300 rounded-xl p-2 font-mono font-bold text-slate-900 outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">من عملة:</label>
                <select id="calc_from" onchange="runLiveConversion()" class="w-full bg-white border border-slate-300 rounded-xl p-2 font-bold text-slate-800 outline-none">
                    @foreach($currencies as $c)
                        <option value="{{ $c->currency_code }}" {{ $c->currency_code === 'SAR' ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->currency_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">إلى عملة:</label>
                <select id="calc_to" onchange="runLiveConversion()" class="w-full bg-white border border-slate-300 rounded-xl p-2 font-bold text-slate-800 outline-none">
                    @foreach($currencies as $c)
                        <option value="{{ $c->currency_code }}" {{ $c->currency_code === 'YER' ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->currency_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">المبلغ المصارف المقابل:</label>
                <div id="calc_result" class="w-full bg-white border border-emerald-300 text-emerald-700 rounded-xl p-2 font-mono font-black text-sm flex items-center justify-between">
                    <span>142,000.00</span>
                    <span class="text-xs text-slate-500 font-bold">ريال يمني</span>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول بيانات العملات التفصيلي (معايير المتكامل بلاس ERP) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
            <div>
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <span>📋</span>
                    <span>جدول تهيئة بيانات العملات وأسعار الصرف المقارنة (ERP Plus Currency Master)</span>
                </h3>
                <span class="text-xs text-slate-400">تعديل فوري لسعر الصرف، أعلى وأدنى حد مسموح به، وأسعار الشراء والبيع</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200 font-bold">
                        <th class="py-3 px-3">رقم العملة</th>
                        <th class="py-3 px-3">اسم العملة</th>
                        <th class="py-3 px-3 text-center">الرمز</th>
                        <th class="py-3 px-3 text-center">الفكة</th>
                        <th class="py-3 px-3 text-center">نوع العملة</th>
                        <th class="py-3 px-3 text-center bg-amber-50/70 border-x border-amber-200 text-amber-950">
                            سعر التحويل (الصرف) ⚡
                        </th>
                        <th class="py-3 px-3 text-center">أدنى / أعلى سعر تحويل</th>
                        <th class="py-3 px-3 text-center">شراء / بيع</th>
                        <th class="py-3 px-3 text-center">حالة العملة</th>
                        <th class="py-3 px-3 text-center">الإجراءات والتحكم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($currencies as $currency)
                        <tr class="hover:bg-slate-50/70 transition {{ $currency->is_stopped ? 'opacity-60 bg-rose-50/20' : '' }}">
                            <td class="py-3 px-3 font-mono font-bold text-slate-500">
                                #{{ $currency->id }}
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">{{ match($currency->currency_code) { 'YER' => '🇾🇪', 'SAR' => '🇸🇦', 'USD' => '🇺🇸', 'AED' => '🇦🇪', 'OMR' => '🇴🇲', 'QAR' => '🇶🇦', 'KWD' => '🇰🇼', default => '🪙' } }}</span>
                                    <div>
                                        <span>{{ $currency->name }}</span>
                                        @if($currency->name_en)
                                            <span class="text-[10px] text-slate-400 font-mono block font-normal">{{ $currency->name_en }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-black text-indigo-700">
                                {{ $currency->currency_code }}
                                <span class="text-[10px] text-slate-400 block font-normal">({{ $currency->symbol }})</span>
                            </td>
                            <td class="py-3 px-3 text-center text-slate-600 font-bold">
                                {{ $currency->fraction_name }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black border {{ $currency->type_badge_class }}">
                                    {{ $currency->type_label }}
                                </span>
                            </td>

                            <!-- تعديل سريع لسعر التحويل (سعر الصرف) -->
                            <td class="py-3 px-3 text-center bg-amber-50/30 border-x border-amber-100">
                                @if($currency->is_local)
                                    <span class="font-mono font-black text-emerald-700 text-sm">1.0000</span>
                                    <span class="text-[9px] text-slate-400 block font-bold">العملة الأساسية</span>
                                @else
                                    <form action="{{ route('accounting.currencies.quick_rate', $currency->id) }}" method="POST" class="flex items-center justify-center gap-1">
                                        @csrf
                                        <input type="number" name="exchange_rate" value="{{ $currency->exchange_rate }}" step="any" min="0.0001" 
                                            class="w-24 text-center font-mono font-black text-slate-900 border border-slate-300 rounded-lg p-1 text-xs focus:ring-1 focus:ring-amber-500 outline-none">
                                        <button type="submit" class="p-1 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-lg transition" title="حفظ سعر الصرف الفوري">
                                            💾
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <!-- الحدود (أعلى وأدنى سعر تحويل) -->
                            <td class="py-3 px-3 text-center font-mono text-[11px] text-slate-600">
                                @if($currency->is_local)
                                    <span class="text-slate-400">-</span>
                                @else
                                    <div><span class="text-emerald-700 font-bold">أدنى:</span> {{ $currency->min_exchange_rate ?: 'غير محدد' }}</div>
                                    <div><span class="text-rose-700 font-bold">أعلى:</span> {{ $currency->max_exchange_rate ?: 'غير محدد' }}</div>
                                @endif
                            </td>

                            <!-- الشراء والبيع -->
                            <td class="py-3 px-3 text-center font-mono text-[11px] text-slate-600">
                                @if($currency->is_local)
                                    <span class="text-slate-400">-</span>
                                @else
                                    <div><span class="text-indigo-700 font-bold">شراء:</span> {{ $currency->buy_rate ?: '-' }}</div>
                                    <div><span class="text-amber-700 font-bold">بيع:</span> {{ $currency->sell_rate ?: '-' }}</div>
                                @endif
                            </td>

                            <!-- الحالة -->
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $currency->status_badge_class }}">
                                    {{ $currency->status_label }}
                                </span>
                                @if($currency->is_default_transfer)
                                    <span class="block text-[9px] text-indigo-700 font-bold mt-0.5">افتراضية للحوالات 📦</span>
                                @endif
                            </td>

                            <!-- الإجراءات -->
                            <td class="py-3 px-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- تعديل تفصيلي -->
                                    <button type="button" onclick='openEditCurrencyModal(@json($currency))' class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition font-bold" title="تعديل تفصيلي للعملة">
                                        ✏️
                                    </button>

                                    <!-- إيقاف / تنشيط -->
                                    @if(!$currency->is_local)
                                        <form action="{{ route('accounting.currencies.toggle_status', $currency->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg transition {{ $currency->is_stopped ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}" title="{{ $currency->is_stopped ? 'تنشيط التعامل' : 'إيقاف العملة' }}">
                                                {{ $currency->is_stopped ? '🟢' : '🚫' }}
                                            </button>
                                        </form>

                                        <!-- حذف -->
                                        <form action="{{ route('accounting.currencies.destroy', $currency->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه العملة [{{ $currency->name }}]؟ لن يقبل النظام الحذف في حال وجود حركات.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition" title="حذف العملة">
                                                🗑️
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">لا توجد عملات معرفة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- =============================================================== -->
<!-- Modal: إضافة عملة جديدة (مطابق لشاشة المتكامل بلاس ERP) -->
<!-- =============================================================== -->
<div id="addCurrencyModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full p-6 sm:p-8 space-y-6 my-8 text-right max-h-[90vh] overflow-y-auto">
        
        <div class="flex justify-between items-start border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-md border border-amber-200">
                    تهيئة نظام المتكامل بلاس ERP 🇾🇪
                </span>
                <h3 class="text-lg font-black text-slate-900 mt-1">إضافة عملة جديدة وضبط أسعار الصرف</h3>
            </div>
            <button type="button" onclick="closeAddCurrencyModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold">✕</button>
        </div>

        <form action="{{ route('accounting.currencies.store') }}" method="POST" class="space-y-5 text-xs">
            @csrf

            <!-- 1. البيانات الأساسية -->
            <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <span class="font-black text-slate-900 block text-xs">1. البيانات الأساسية للعملة (بدون ال التعريف):</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">اسم العملة بالعربية (بدون الـ): *</label>
                        <input type="text" name="name" required placeholder="مثال: ريال سعودي، دولار أمريكي، درهم إماراتي" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">اسم العملة بالإنجليزية:</label>
                        <input type="text" name="name_en" placeholder="مثال: Saudi Riyal, US Dollar" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">رمز العملة (Code): *</label>
                        <input type="text" name="currency_code" required placeholder="مثال: SAR, USD, AED, EUR" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono font-bold uppercase outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الرمز المختصر (Symbol): *</label>
                        <input type="text" name="symbol" required placeholder="مثال: ر.س، $، د.إ، €" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">اسم الفكة (بدون الـ): *</label>
                        <input type="text" name="fraction_name" required placeholder="مثال: هللة، سنت، فلس" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                            <input type="checkbox" name="is_local" value="1" class="rounded text-emerald-600 h-4 w-4">
                            <span>عملة محلية رئيسية للمنشأة (سعرها 1.0)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- 2. أسعار الصرف والتحويل والحدود -->
            <div class="space-y-3 bg-amber-50/50 p-4 rounded-2xl border border-amber-200">
                <span class="font-black text-amber-950 block text-xs">2. أسعار التحويل والصرف مقارنة بالعملة المحلية (الريال اليمني):</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">سعر التحويل (سعر الصرف): *</label>
                        <input type="number" name="exchange_rate" required step="any" min="0.0001" value="1.0000" class="w-full bg-white border border-amber-300 rounded-xl p-2.5 font-mono font-black text-amber-900 outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">أدنى سعر تحويل (حد أدنى):</label>
                        <input type="number" name="min_exchange_rate" step="any" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">أعلى سعر تحويل (حد أعلى):</label>
                        <input type="number" name="max_exchange_rate" step="any" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">سعر الشراء:</label>
                        <input type="number" name="buy_rate" step="any" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">سعر البيع:</label>
                        <input type="number" name="sell_rate" step="any" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">فئات العملة النقدية (مفصولة بفواصل):</label>
                        <input type="text" name="denominations_str" placeholder="مثال: 500, 200, 100, 50, 20, 10, 5, 1" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                </div>
            </div>

            <!-- خيارات إضافية -->
            <div class="flex items-center gap-4 flex-wrap pt-2">
                <label class="flex items-center gap-1.5 cursor-pointer font-bold text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-indigo-600">
                    <span>مستخدمة في النظام 🟢</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer font-bold text-slate-700">
                    <input type="checkbox" name="is_default_transfer" value="1" class="rounded text-indigo-600">
                    <span>عملة افتراضية للحوالات</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeAddCurrencyModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">إلغاء</button>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl shadow-md transition">حفظ العملة 💾</button>
            </div>
        </form>
    </div>
</div>

<!-- =============================================================== -->
<!-- Modal: تعديل بيانات العملة والأسعار -->
<!-- =============================================================== -->
<div id="editCurrencyModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full p-6 sm:p-8 space-y-6 my-8 text-right max-h-[90vh] overflow-y-auto">
        
        <div class="flex justify-between items-start border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-md border border-indigo-200">
                    تعديل العملة (ERP Plus)
                </span>
                <h3 class="text-lg font-black text-slate-900 mt-1" id="editModalTitle">تعديل بيانات وأسعار العملة</h3>
            </div>
            <button type="button" onclick="closeEditCurrencyModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold">✕</button>
        </div>

        <form id="editCurrencyForm" method="POST" class="space-y-5 text-xs">
            @csrf

            <!-- 1. البيانات الأساسية -->
            <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">اسم العملة بالعربية: *</label>
                        <input type="text" name="name" id="edit_name" required class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-bold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">اسم العملة بالإنجليزية:</label>
                        <input type="text" name="name_en" id="edit_name_en" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الرمز المختصر (Symbol): *</label>
                        <input type="text" name="symbol" id="edit_symbol" required class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-bold outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">اسم الفكة: *</label>
                        <input type="text" name="fraction_name" id="edit_fraction_name" required class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-bold outline-none">
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                            <input type="checkbox" name="is_local" id="edit_is_local" value="1" class="rounded text-emerald-600 h-4 w-4">
                            <span>عملة محلية رئيسية (سعر الصرف 1.0)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- 2. أسعار الصرف والتحويل والحدود -->
            <div class="space-y-3 bg-amber-50/50 p-4 rounded-2xl border border-amber-200">
                <span class="font-black text-amber-950 block text-xs">أسعار التحويل والصرف والحدود:</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">سعر التحويل (سعر الصرف): *</label>
                        <input type="number" name="exchange_rate" id="edit_exchange_rate" required step="any" min="0.0001" class="w-full bg-white border border-amber-300 rounded-xl p-2.5 font-mono font-black text-amber-900 outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">أدنى سعر تحويل:</label>
                        <input type="number" name="min_exchange_rate" id="edit_min_exchange_rate" step="any" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">أعلى سعر تحويل:</label>
                        <input type="number" name="max_exchange_rate" id="edit_max_exchange_rate" step="any" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">سعر الشراء:</label>
                        <input type="number" name="buy_rate" id="edit_buy_rate" step="any" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">سعر البيع:</label>
                        <input type="number" name="sell_rate" id="edit_sell_rate" step="any" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">فئات العملة النقدية:</label>
                        <input type="text" name="denominations_str" id="edit_denominations_str" class="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono outline-none">
                    </div>
                </div>
            </div>

            <!-- خيارات إضافية -->
            <div class="flex items-center gap-4 flex-wrap pt-2">
                <label class="flex items-center gap-1.5 cursor-pointer font-bold text-slate-700">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded text-indigo-600">
                    <span>مستخدمة في النظام 🟢</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer font-bold text-slate-700">
                    <input type="checkbox" name="is_stopped" id="edit_is_stopped" value="1" class="rounded text-rose-600">
                    <span>إيقاف العملة 🚫</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer font-bold text-slate-700">
                    <input type="checkbox" name="is_default_transfer" id="edit_is_default_transfer" value="1" class="rounded text-indigo-600">
                    <span>عملة افتراضية للحوالات</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeEditCurrencyModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">إلغاء</button>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-xl shadow-md transition">حفظ التعديلات 💾</button>
            </div>
        </form>
    </div>
</div>

<script>
const CURRENCY_RATES = {
    @foreach($currencies as $c)
        "{{ $c->currency_code }}": {{ (float)$c->exchange_rate }},
    @endforeach
};

function runLiveConversion() {
    const amount = parseFloat(document.getElementById('calc_amount').value) || 0;
    const fromCode = document.getElementById('calc_from').value;
    const toCode = document.getElementById('calc_to').value;

    const rateFrom = CURRENCY_RATES[fromCode] || 1;
    const rateTo = CURRENCY_RATES[toCode] || 1;

    // تحويل إلى العملة المحلية أولاً ثم إلى الهدف
    const inLocal = amount * rateFrom;
    const result = rateTo > 0 ? (inLocal / rateTo) : inLocal;

    const formatted = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(result);
    document.getElementById('calc_result').innerHTML = `<span>${formatted}</span> <span class="text-xs text-slate-500 font-bold">${toCode}</span>`;
}

function openAddCurrencyModal() {
    document.getElementById('addCurrencyModal').classList.remove('hidden');
}

function closeAddCurrencyModal() {
    document.getElementById('addCurrencyModal').classList.add('hidden');
}

function openEditCurrencyModal(cur) {
    document.getElementById('editModalTitle').textContent = `تعديل بيانات عملة: [${cur.name}] (${cur.currency_code})`;
    document.getElementById('editCurrencyForm').action = `/accounting/currencies/${cur.id}/update`;

    document.getElementById('edit_name').value = cur.name || '';
    document.getElementById('edit_name_en').value = cur.name_en || '';
    document.getElementById('edit_symbol').value = cur.symbol || '';
    document.getElementById('edit_fraction_name').value = cur.fraction_name || '';
    document.getElementById('edit_is_local').checked = !!cur.is_local;
    document.getElementById('edit_exchange_rate').value = cur.exchange_rate || 1;
    document.getElementById('edit_min_exchange_rate').value = cur.min_exchange_rate || '';
    document.getElementById('edit_max_exchange_rate').value = cur.max_exchange_rate || '';
    document.getElementById('edit_buy_rate').value = cur.buy_rate || '';
    document.getElementById('edit_sell_rate').value = cur.sell_rate || '';
    document.getElementById('edit_is_active').checked = !!cur.is_active;
    document.getElementById('edit_is_stopped').checked = !!cur.is_stopped;
    document.getElementById('edit_is_default_transfer').checked = !!cur.is_default_transfer;

    if (Array.isArray(cur.denominations)) {
        document.getElementById('edit_denominations_str').value = cur.denominations.join(', ');
    } else {
        document.getElementById('edit_denominations_str').value = '';
    }

    document.getElementById('editCurrencyModal').classList.remove('hidden');
}

function closeEditCurrencyModal() {
    document.getElementById('editCurrencyModal').classList.add('hidden');
}
</script>
@endsection
