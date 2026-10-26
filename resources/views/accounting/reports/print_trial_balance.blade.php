<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ميزان المراجعة بالمجاميع والأرصدة - ASOTS</title>
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

    <div class="no-print max-w-5xl mx-auto mb-4 flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <span class="font-bold text-xs">معاينة طباعة تقرير ميزان المراجعة</span>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition">طباعة (Print)</button>
            <button onclick="window.close()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs hover:bg-slate-200 transition">إغلاق</button>
        </div>
    </div>

    <div class="print-page max-w-5xl mx-auto bg-white p-8 rounded-3xl shadow-lg border border-slate-200 space-y-6">
        
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-5">
            <div>
                <h1 class="text-lg font-black text-slate-900">مكتب النجم الآسيوي للترجمة والتدريب والاستشارات</h1>
                <span class="text-[11px] text-slate-500 font-mono block">THE ASIAN STAR OFFICE FOR TRANSLATION & TRAINING</span>
                <span class="text-xs font-bold text-indigo-700 block mt-1">تقرير ميزان المراجعة العام بالمجاميع والأرصدة</span>
            </div>
            <div class="text-left text-xs space-y-1">
                <div><span class="text-slate-500">الفترة من:</span> <span class="font-mono font-bold">{{ $fromDate ?: 'بداية النشاط' }}</span></div>
                <div><span class="text-slate-500">إلى تاريخ:</span> <span class="font-mono font-bold">{{ $toDate ?: date('Y-m-d') }}</span></div>
                <div><span class="text-slate-500">تاريخ الطباعة:</span> <span class="font-mono">{{ date('Y-m-d H:i') }}</span></div>
            </div>
        </div>

        <table class="w-full text-right text-xs border border-slate-300">
            <thead class="bg-slate-100 text-slate-900 font-black text-center border-b border-slate-300">
                <tr>
                    <th rowspan="2" class="p-2 border-l border-slate-300 w-20">رقم الحساب</th>
                    <th rowspan="2" class="p-2 border-l border-slate-300 text-right">اسم الحساب المحاسبي</th>
                    <th rowspan="2" class="p-2 border-l border-slate-300 w-24">التصنيف</th>
                    <th colspan="2" class="p-2 border-l border-slate-300 bg-slate-200">مجاميع الحركات</th>
                    <th colspan="2" class="p-2 bg-slate-200">الأرصدة الختامية</th>
                </tr>
                <tr class="bg-slate-50 border-t border-slate-300 text-[10px]">
                    <th class="p-1 border-l border-slate-300 text-left w-24">مدين</th>
                    <th class="p-1 border-l border-slate-300 text-left w-24">دائن</th>
                    <th class="p-1 border-l border-slate-300 text-left w-24">رصيد مدين</th>
                    <th class="p-1 text-left w-24">رصيد دائن</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($data['rows'] as $r)
                    <tr>
                        <td class="p-2 text-center font-mono font-bold border-l border-slate-200">{{ $r['account_code'] }}</td>
                        <td class="p-2 font-bold border-l border-slate-200">{{ $r['account_name'] }}</td>
                        <td class="p-2 text-slate-500 text-[10px] border-l border-slate-200">{{ $r['account_category'] }}</td>
                        <td class="p-2 font-mono text-left border-l border-slate-200">{{ $r['total_debit'] > 0 ? number_format($r['total_debit'], 2) : '—' }}</td>
                        <td class="p-2 font-mono text-left border-l border-slate-200">{{ $r['total_credit'] > 0 ? number_format($r['total_credit'], 2) : '—' }}</td>
                        <td class="p-2 font-mono font-bold text-left border-l border-slate-200">{{ $r['balance_debit'] > 0 ? number_format($r['balance_debit'], 2) : '—' }}</td>
                        <td class="p-2 font-mono font-bold text-left">{{ $r['balance_credit'] > 0 ? number_format($r['balance_credit'], 2) : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-100 font-black border-t-2 border-slate-900 text-xs">
                <tr>
                    <td colspan="3" class="p-3 text-right border-l border-slate-300">الإجمالي العام لميزان المراجعة:</td>
                    <td class="p-3 text-left font-mono border-l border-slate-300">{{ number_format($data['total_debit_sum'], 2) }}</td>
                    <td class="p-3 text-left font-mono border-l border-slate-300">{{ number_format($data['total_credit_sum'], 2) }}</td>
                    <td class="p-3 text-left font-mono border-l border-slate-300">{{ number_format($data['total_debit_balance'], 2) }}</td>
                    <td class="p-3 text-left font-mono">{{ number_format($data['total_credit_balance'], 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- التوقيعات -->
        <div class="grid grid-cols-3 gap-6 pt-10 text-center text-xs">
            <div>
                <span class="font-bold text-slate-600 block">إعداد المحاسب</span>
                <span class="font-mono text-slate-400 block mt-6">..................................</span>
            </div>
            <div>
                <span class="font-bold text-slate-600 block">المراجعة والتدقيق</span>
                <span class="font-mono text-slate-400 block mt-6">..................................</span>
            </div>
            <div>
                <span class="font-bold text-slate-600 block">اعتماد المدير المالي</span>
                <span class="font-mono text-slate-400 block mt-6">..................................</span>
            </div>
        </div>

    </div>

</body>
</html>
