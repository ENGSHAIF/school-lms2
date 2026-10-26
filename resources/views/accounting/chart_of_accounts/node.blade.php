@php
    $hasChildren = $account->children && $account->children->count() > 0;
    $indent = ($level - 1) * 20;

    $categoryBorderColor = match($account->account_category) {
        'asset'     => 'border-blue-200 bg-blue-50/10 hover:bg-blue-50/30',
        'liability' => 'border-amber-200 bg-amber-50/10 hover:bg-amber-50/30',
        'equity'    => 'border-purple-200 bg-purple-50/10 hover:bg-purple-50/30',
        'revenue'   => 'border-emerald-200 bg-emerald-50/10 hover:bg-emerald-50/30',
        'expense'   => 'border-rose-200 bg-rose-50/10 hover:bg-rose-50/30',
        default     => 'border-slate-200 bg-white hover:bg-slate-50',
    };

    $badgeColor = match($account->account_category) {
        'asset'     => 'bg-blue-100 text-blue-800',
        'liability' => 'bg-amber-100 text-amber-800',
        'equity'    => 'bg-purple-100 text-purple-800',
        'revenue'   => 'bg-emerald-100 text-emerald-800',
        'expense'   => 'bg-rose-100 text-rose-800',
        default     => 'bg-slate-100 text-slate-800',
    };
@endphp

<div class="border {{ $categoryBorderColor }} rounded-2xl transition p-3 space-y-2" style="margin-right: {{ $indent }}px;">
    <div class="flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-2.5">
            @if($hasChildren)
                <button type="button" onclick="toggleNode({{ $account->id }})" class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-xs hover:bg-slate-100 shadow-2xs">
                    <span id="icon-{{ $account->id }}" class="tree-folder-icon">📂</span>
                </button>
            @else
                <span class="w-7 h-7 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center text-xs text-slate-400">📄</span>
            @endif

            <span class="font-mono text-xs font-black px-2 py-0.5 rounded-md bg-slate-900 text-white">
                {{ $account->account_code }}
            </span>

            <span class="text-xs font-black text-slate-900 {{ $account->is_parent ? 'text-sm' : '' }}">
                {{ $account->account_name }}
            </span>

            <span class="text-[10px] font-black px-2 py-0.5 rounded-full {{ $badgeColor }}">
                {{ $account->account_category_label }}
            </span>

            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $account->nature === 'credit' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                {{ $account->nature === 'credit' ? 'دائن' : 'مدين' }}
            </span>

            <!-- شارة الرتبة المحاسبية (1 إلى 4 رئيسي، 5 فرعي) -->
            @if($account->level < 5)
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                    رتبة {{ $account->level }} (رئيسي)
                </span>
            @else
                <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                    رتبة 5 (فرعي حركي)
                </span>
            @endif

            <!-- نوع الحساب التحليلي (طالب، مدرس، موظف، ولي أمر، صندوق، بنك، إلخ) -->
            @if($account->analytical_type && $account->analytical_type !== 'general')
                <span class="text-[10px] font-black px-2 py-0.5 rounded-md border {{ $account->analytical_type_badge }}">
                    🔍 تحليلي: {{ $account->analytical_type_label }}
                </span>
            @endif

            @if($account->allow_cost_center)
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700">🏢 مركز تكلفة</span>
            @endif
        </div>

        <div class="flex items-center gap-3">
            @if(!$account->is_parent)
                <div class="text-left font-mono font-bold text-xs">
                    <span class="{{ $account->current_balance < 0 ? 'text-rose-600' : 'text-slate-800' }}">
                        {{ number_format($account->current_balance, 2) }} {{ $account->currency }}
                    </span>
                </div>
            @else
                <span class="text-[11px] text-slate-400 font-bold">حساب تجميعي ({{ $account->children->count() }} فرع)</span>
            @endif

            <div class="flex items-center gap-1.5">
                <button type="button" onclick='openEditAccountModal(@json($account))' title="تعديل الحساب وتحديد نوعه التحليلي" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition border border-slate-200" title="تعديل الحساب والنوع التحليلي">
                    <span>✏️</span>
                    <span class="hidden sm:inline text-[10px]">تعديل</span>
                </button>

                @if($account->level < 5)
                    <button type="button" onclick="openAddAccountModal({{ $account->id }})" title="إضافة حساب تحته في الرتبة {{ $account->level + 1 }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition flex items-center gap-1 border border-indigo-200">
                        <span>➕</span>
                        <span>إضافة رتبة {{ $account->level + 1 }}</span>
                    </button>
                @else
                    <span class="text-[10px] text-emerald-600 font-bold px-2 py-0.5 rounded bg-emerald-50/70 border border-emerald-200">
                        ✓ حساب فرعي نهائي
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

@if($hasChildren)
    <div id="children-{{ $account->id }}" class="tree-children space-y-2 pt-1">
        @foreach($account->children as $child)
            @include('accounting.chart_of_accounts.node', ['account' => $child, 'level' => $level + 1])
        @endforeach
    </div>
@endif
