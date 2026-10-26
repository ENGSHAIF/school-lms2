<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند قيد يومية محاسبي - {{ $entry->entry_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; background: white !important; }
            .print-page { border: none !important; box-shadow: none !important; width: 100% !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 text-slate-900">

    <!-- شريط الطباعة العلوي -->
    <div class="no-print max-w-4xl mx-auto mb-4 flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-2">
            <span class="text-xl">🖨️</span>
            <span class="font-bold text-xs">معاينة طباعة سند قيد اليومية العامة</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition">طباعة المستند (Print)</button>
            <button onclick="window.close()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition">إغلاق</button>
        </div>
    </div>

    <!-- صفحة المستند A4 -->
    <div class="print-page max-w-4xl mx-auto bg-white p-8 rounded-3xl shadow-lg border border-slate-200 space-y-6">
        
        <!-- الترويسة الرسمية -->
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-5">
            <div>
                <h1 class="text-lg font-black text-slate-900">مكتب النجم الآسيوي للترجمة والتدريب والاستشارات</h1>
                <span class="text-[11px] text-slate-500 font-mono block">THE ASIAN STAR OFFICE FOR TRANSLATION & TRAINING</span>
                <span class="text-xs font-bold text-indigo-700 block mt-1">الإدارة المالية والحسابات العامة</span>
            </div>
            <div class="text-left">
                <div class="border-2 border-slate-900 px-4 py-2 rounded-xl text-center bg-slate-50">
                    <span class="text-[11px] font-bold text-slate-500 block">سند قيد يومية عامة</span>
                    <span class="font-mono text-base font-black text-slate-900">{{ $entry->entry_no }}</span>
                </div>
            </div>
        </div>

        <!-- بيانات القيد -->
        <div class="grid grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
            <div>
                <span class="text-slate-500 font-bold">تاريخ القيد:</span>
                <span class="font-mono font-black mr-1">{{ $entry->entry_date->format('Y/m/d') }}</span>
            </div>
            <div>
                <span class="text-slate-500 font-bold">نوع القيد:</span>
                <span class="font-black mr-1">{{ $entry->reference_type_label }}</span>
            </div>
            <div>
                <span class="text-slate-500 font-bold">الحالة:</span>
                <span class="font-bold text-emerald-700 mr-1">{{ $entry->is_posted ? 'مرحل ومطابق' : 'مسودة' }}</span>
            </div>
            <div class="col-span-3 pt-2 border-t border-slate-200">
                <span class="text-slate-500 font-bold">البيان العام:</span>
                <span class="font-bold mr-1">{{ $entry->description }}</span>
            </div>
        </div>

        <!-- جدول الأسطر -->
        <table class="w-full text-right text-xs border border-slate-300">
            <thead class="bg-slate-100 text-slate-800 font-black border-b border-slate-300">
                <tr>
                    <th class="p-3 border-l border-slate-300 w-12 text-center">#</th>
                    <th class="p-3 border-l border-slate-300 w-24">رقم الحساب</th>
                    <th class="p-3 border-l border-slate-300">اسم الحساب المحاسبي</th>
                    <th class="p-3 border-l border-slate-300 w-32">مركز التكلفة</th>
                    <th class="p-3 border-l border-slate-300 w-28 text-left">مدين (Debit)</th>
                    <th class="p-3 border-l border-slate-300 w-28 text-left">دائن (Credit)</th>
                    <th class="p-3">البيان والشرح</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($entry->lines as $idx => $line)
                    <tr>
                        <td class="p-3 text-center border-l border-slate-200 font-mono">{{ $idx + 1 }}</td>
                        <td class="p-3 font-mono font-bold border-l border-slate-200">{{ $line->account?->account_code }}</td>
                        <td class="p-3 font-bold border-l border-slate-200">{{ $line->account?->account_name }}</td>
                        <td class="p-3 border-l border-slate-200 text-slate-600">{{ $line->costCenter?->name ?? '—' }}</td>
                        <td class="p-3 font-mono font-bold text-left border-l border-slate-200">
                            {{ $line->debit > 0 ? number_format($line->debit, 2) : '—' }}
                        </td>
                        <td class="p-3 font-mono font-bold text-left border-l border-slate-200">
                            {{ $line->credit > 0 ? number_format($line->credit, 2) : '—' }}
                        </td>
                        <td class="p-3 text-slate-600">{{ $line->line_description ?: $entry->description }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-100 font-black border-t-2 border-slate-900">
                <tr>
                    <td colspan="4" class="p-3 text-right border-l border-slate-300">الإجمالي العام المتزن:</td>
                    <td class="p-3 text-left font-mono border-l border-slate-300">{{ number_format($entry->total_debit, 2) }}</td>
                    <td class="p-3 text-left font-mono border-l border-slate-300">{{ number_format($entry->total_credit, 2) }}</td>
                    <td class="p-3 text-center text-emerald-800 text-[11px]">متطابق 100%</td>
                </tr>
            </tfoot>
        </table>

        <!-- تفقيط المبلغ بالعربية -->
        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700">
            <span>المبلغ كتابةً: </span>
            <span>{{ \App\Helpers\ArabicCurrencyHelper::tafqeet($entry->total_debit, 'YER') }} فقط لا غير.</span>
        </div>

        <!-- التوقيعات والاعتماد الرسمي -->
        <div class="grid grid-cols-3 gap-6 pt-10 text-center text-xs">
            <div class="space-y-8">
                <span class="font-bold text-slate-600 block">إعداد وتنظيم المحاسب</span>
                <span class="font-mono text-slate-400 block">..................................</span>
                <span class="font-bold text-slate-800 block">{{ $entry->creator?->name ?? 'المحاسب' }}</span>
            </div>
            <div class="space-y-8">
                <span class="font-bold text-slate-600 block">المراجعة والتدقيق المالي</span>
                <span class="font-mono text-slate-400 block">..................................</span>
                <span class="font-bold text-slate-800 block">قسم الرقابة والتدقيق</span>
            </div>
            <div class="space-y-8">
                <span class="font-bold text-slate-600 block">اعتماد المدير المالي / العام</span>
                <span class="font-mono text-slate-400 block">..................................</span>
                <span class="font-bold text-slate-800 block">الإدارة العامة</span>
            </div>
        </div>

    </div>

</body>
</html>
