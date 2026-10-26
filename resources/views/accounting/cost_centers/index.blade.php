@extends('layouts.app', ['title' => 'شجرة مراكز التكلفة ومؤشرات الربحية'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>🏢</span>
                <span>شجرة مراكز التكلفة والربحية (Cost Centers)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                توزيع ومتابعة إيرادات ومصروفات كل قسم ومشروع ودورة تدريبية على حدة لتقييم الجدوى وصافي الأرباح
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddCCModal()" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                <span>➕</span>
                <span>إضافة مركز تكلفة جديد</span>
            </button>
            <a href="{{ route('accounting.reports.cost_centers') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                <span>📊</span>
                <span>تقرير أرباح وخسائر مراكز التكلفة</span>
            </a>
        </div>
    </div>

    <!-- شبكة مراكز التكلفة الرئيسية -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($rootCenters as $root)
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-black px-2 py-0.5 rounded bg-indigo-50 text-indigo-700">
                            {{ $root->code }}
                        </span>
                        <span class="text-xs font-bold text-slate-400">قطاع رئيسي</span>
                    </div>
                    <h3 class="text-base font-black text-slate-900 mt-2">{{ $root->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $root->description }}</p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-600">
                    <span>الفروع والبرامج:</span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-900 font-mono">{{ $root->children->count() }} مركز فرعي</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- جدول مراكز التكلفة التفصيلي -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-base font-black text-slate-800 flex items-center gap-2">
                <span>📋</span>
                <span>دليل مراكز التكلفة التفصيلي</span>
            </h2>
            <span class="text-xs font-bold text-slate-500">إجمالي ({{ $totalCostCenters }}) مركز تكلفة</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-700 font-black border-b border-slate-200">
                    <tr>
                        <th class="p-4">الكود</th>
                        <th class="p-4">اسم مركز التكلفة</th>
                        <th class="p-4">المستوى / التبعية</th>
                        <th class="p-4">إجمالي الإيرادات</th>
                        <th class="p-4">إجمالي المصروفات</th>
                        <th class="p-4">صافي الربح / الخسارة</th>
                        <th class="p-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($allCenters as $cc)
                        @php
                            $rev = $cc->total_revenues;
                            $exp = $cc->total_expenses;
                            $net = $rev - $exp;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-black text-indigo-700">{{ $cc->code }}</td>
                            <td class="p-4 font-bold text-slate-900">
                                @if($cc->parent_id)
                                    <span class="text-slate-400 mr-2">↳</span>
                                @endif
                                {{ $cc->name }}
                            </td>
                            <td class="p-4 text-slate-600">
                                @if($cc->parent)
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-[11px] font-bold">تابع لـ: {{ $cc->parent->name }}</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-800 text-[11px] font-bold">مستوى رئيسي عام</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono font-bold text-emerald-600">
                                {{ number_format($rev, 2) }} ر.ي
                            </td>
                            <td class="p-4 font-mono font-bold text-rose-600">
                                {{ number_format($exp, 2) }} ر.ي
                            </td>
                            <td class="p-4 font-mono font-black {{ $net >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ number_format($net, 2) }} ر.ي
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('accounting.cost_centers.destroy', $cc->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف أو تعطيل مركز التكلفة؟');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs" title="حذف">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 font-bold">لا توجد مراكز تكلفة مسجلة حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: إضافة مركز تكلفة جديد -->
<div id="addCCModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-6 bg-slate-900 text-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xl">➕</span>
                <h3 class="font-black text-base">إضافة مركز تكلفة جديد</h3>
            </div>
            <button type="button" onclick="closeAddCCModal()" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>
        <form action="{{ route('accounting.cost_centers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">القطاع / المركز الرئيسي الأب</label>
                <select name="parent_id" class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                    <option value="">— مستوى رئيسي مستقل (لا يوجد أب) —</option>
                    @foreach($rootCenters as $r)
                        <option value="{{ $r->id }}">[{{ $r->code }}] {{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">الكود (اختياري)</label>
                    <input type="text" name="code" placeholder="توليد تلقائي" class="w-full text-xs font-mono font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">اسم مركز التكلفة *</label>
                    <input type="text" name="name" required placeholder="مثال: دورات الترجمة الفورية" class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">الوصف والغرض</label>
                <textarea name="description" rows="2" placeholder="وصف الدورات أو النشاط التابع لهذا المركز..." class="w-full text-xs rounded-xl border-slate-300 p-2.5 focus:border-indigo-500"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                <button type="button" onclick="closeAddCCModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">إلغاء</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm">حفظ المركز</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddCCModal() {
        document.getElementById('addCCModal').classList.remove('hidden');
    }
    function closeAddCCModal() {
        document.getElementById('addCCModal').classList.add('hidden');
    }
</script>
@endsection
