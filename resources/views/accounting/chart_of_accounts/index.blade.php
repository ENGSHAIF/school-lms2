@extends('layouts.app', ['title' => 'الدليل المحاسبي الشجري العام (COA)'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة الرئيسية -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>🌳</span>
                <span>الدليل المحاسبي الشجري العام (Chart of Accounts)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                الهيكل القياسي للحسابات المالية (أصول، خصوم، حقوق ملكية، إيرادات، مصروفات) متعدد المستويات مع التوليد الآلي لأكواد الحسابات
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddAccountModal()" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                <span>➕</span>
                <span>إضافة حساب محاسبي جديد</span>
            </button>
            <button type="button" onclick="expandAllTree()" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                <span>📂 توسيع الكل</span>
            </button>
            <button type="button" onclick="collapseAllTree()" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                <span>📁 طي الكل</span>
            </button>
        </div>
    </div>

    <!-- بطاقات المؤشرات السريعة -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400">إجمالي الحسابات بالدليل</span>
                <span class="text-xl">📚</span>
            </div>
            <span class="text-2xl font-black text-slate-800 font-mono mt-2 block">{{ $totalAccountsCount }}</span>
            <span class="text-[11px] text-slate-500 mt-1 block">منها ({{ $leafAccountsCount }}) حساب فرعي يقبل الحركات</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-blue-200 bg-blue-50/20 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-800">إجمالي الأصول الحالية (1)</span>
                <span class="text-xl">💼</span>
            </div>
            <span class="text-2xl font-black text-blue-600 font-mono mt-2 block">
                {{ \App\Helpers\ArabicCurrencyHelper::format($totalAssets, 'YER') }}
            </span>
            <span class="text-[11px] text-blue-600 mt-1 block">نقدية، بنوك، وذمم رسوم الطلاب</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-amber-200 bg-amber-50/20 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-800">إجمالي الالتزامات والخصوم (2)</span>
                <span class="text-xl">💳</span>
            </div>
            <span class="text-2xl font-black text-amber-600 font-mono mt-2 block">
                {{ \App\Helpers\ArabicCurrencyHelper::format($totalLiabilities, 'YER') }}
            </span>
            <span class="text-[11px] text-amber-600 mt-1 block">مستحقات أجور المعلمين والكادر</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-emerald-200 bg-emerald-50/20 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-800">هيكل الدليل المحاسبي</span>
                <span class="text-xl">🏛️</span>
            </div>
            <span class="text-xl font-black text-emerald-700 font-mono mt-2 block">نظام الرتبة 5 الصارم</span>
            <span class="text-[11px] text-emerald-600 mt-1 block">الرتب (1-4) رئيسية • الرتبة (5) فرعية فقط</span>
        </div>
    </div>

    <!-- شجرة الحسابات التفاعلية -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        
        <div class="flex items-center justify-between border-b border-slate-200 pb-4 flex-wrap gap-3">
            <div>
                <h2 class="text-base font-black text-slate-800 flex items-center gap-2">
                    <span>🌲</span>
                    <span>استعراض شجرة الحسابات التفاعلية (5 رتب قياسية)</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    الرتب 1 إلى 4 هي حسابات رئيسية تجميعية للميزانية، والرتبة 5 هي الحسابات الفرعية الحركية الحصرية للسندات والقيود
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-600">
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> 1. أصول
                    <span class="w-3 h-3 rounded-full bg-amber-500 inline-block mr-2"></span> 2. خصوم
                    <span class="w-3 h-3 rounded-full bg-purple-500 inline-block mr-2"></span> 3. حقوق ملكية
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block mr-2"></span> 4. إيرادات
                    <span class="w-3 h-3 rounded-full bg-rose-500 inline-block mr-2"></span> 5. مصروفات
                </div>
            </div>
        </div>

        <!-- عرض الشجرة التكراري -->
        <div class="space-y-2 pt-2">
            @foreach($rootAccounts as $root)
                @include('accounting.chart_of_accounts.node', ['account' => $root, 'level' => 1])
            @endforeach
        </div>

    </div>

</div>

<!-- Modal: إضافة حساب جديد -->
<div id="addAccountModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-6 bg-slate-900 text-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xl">➕</span>
                <div>
                    <h3 class="font-black text-base">إضافة حساب جديد في الدليل المحاسبي</h3>
                    <p class="text-[11px] text-slate-300">يتم تحديد الرتبة آلياً: رتب (1-4) رئيسي، رتبة 5 فرعي حركي</p>
                </div>
            </div>
            <button type="button" onclick="closeAddAccountModal()" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>
        <form action="{{ route('accounting.chart_of_accounts.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">الحساب الأب (المستوى الأعلى) *</label>
                <select name="parent_id" id="modalParentSelect" onchange="updateParentLevelInfo()" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500 bg-white">
                    @foreach($parentCandidates as $p)
                        <option value="{{ $p->id }}" data-level="{{ $p->level }}">{{ str_repeat('— ', $p->level - 1) }}[{{ $p->account_code }}] {{ $p->account_name }} (رتبة {{ $p->level }} - {{ $p->account_category_label }})</option>
                    @endforeach
                </select>
            </div>

            <!-- بطاقة بيان الرتبة الناتجة آلياً -->
            <div id="accountLevelInfoBox">
                <!-- يتم التحديث عبر JavaScript -->
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">نوع الحساب التحليلي (Analytical Type) *</label>
                    <select name="analytical_type" id="modalAnalyticalType" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500 bg-white">
                        <option value="general">عام / غير محدد (General)</option>
                        <option value="cash_box">🏦 صندوق نقدي وخزينة (Cash Box)</option>
                        <option value="bank">💳 بنك ومحفظة إلكترونية (Bank / Wallet)</option>
                        <option value="student">🎓 طالب / دارس (Student)</option>
                        <option value="teacher">👨‍🏫 مدرس / مدرب (Teacher)</option>
                        <option value="employee">💼 موظف / كادر إداري (Employee)</option>
                        <option value="parent">👨‍👩‍👦 ولي أمر (Parent / Guardian)</option>
                        <option value="customer">🏢 عميل / شركات متعاقدة (Customer)</option>
                        <option value="supplier">📦 مورد / التزامات خارجية (Supplier)</option>
                        <option value="revenue">📈 إيراد نشاط أكاديمي (Revenue)</option>
                        <option value="expense">📉 مصروف تشغيلي وإداري (Expense)</option>
                    </select>
                    <span class="text-[10px] text-slate-400 block mt-0.5">إذا اخترت (صندوق) أو (بنك)، سيظهر كحساب رئيسي في شاشة الصناديق لتفريغ صناديق تحته.</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">طبيعة الحساب *</label>
                    <select name="nature" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500 bg-white">
                        <option value="debit">مدين بطبيعته (Debit)</option>
                        <option value="credit">دائن بطبيعته (Credit)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">كود الحساب (اختياري)</label>
                    <input type="text" name="account_code" placeholder="توليد تلقائي قياسي إن ترك فارغاً" class="w-full text-xs font-mono font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">اسم الحساب المحاسبي *</label>
                    <input type="text" name="account_name" required placeholder="مثال: إيرادات دورات اللغة التركية" class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <input type="checkbox" name="allow_cost_center" value="1" id="allowCcCheck" class="rounded border-slate-300 text-indigo-600">
                    <label for="allowCcCheck" class="text-xs font-bold text-slate-700 cursor-pointer">
                        ربط الحساب بمراكز التكلفة (اختياري)
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">الرصيد الافتتاحي (إن وجد)</label>
                <input type="number" step="0.01" name="opening_balance" value="0.00" class="w-full text-xs font-mono font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">وصف وملاحظات الحساب</label>
                <textarea name="description" rows="2" placeholder="ملاحظات حول طبيعة واستخدام الحساب..." class="w-full text-xs rounded-xl border-slate-300 p-2.5 focus:border-indigo-500"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                <button type="button" onclick="closeAddAccountModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">إلغاء</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm">حفظ الحساب</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: تعديل بيانات حساب محاسبي وتحديد نوعه التحليلي -->
<div id="editAccountModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-6 bg-slate-900 text-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xl">✏️</span>
                <div>
                    <h3 class="font-black text-base" id="editModalTitle">تعديل بيانات الحساب المحاسبي</h3>
                    <p class="text-[11px] text-slate-300" id="editModalSubTitle">—</p>
                </div>
            </div>
            <button type="button" onclick="closeEditAccountModal()" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>
        <form id="editAccountForm" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">اسم الحساب المحاسبي *</label>
                <input type="text" name="account_name" id="editAccountName" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">نوع الحساب التحليلي (Analytical Type) *</label>
                    <select name="analytical_type" id="editAnalyticalType" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500 bg-white">
                        <option value="general">عام / غير محدد (General)</option>
                        <option value="cash_box">🏦 صندوق نقدي وخزينة (Cash Box)</option>
                        <option value="bank">💳 بنك ومحفظة إلكترونية (Bank / Wallet)</option>
                        <option value="student">🎓 طالب / دارس (Student)</option>
                        <option value="teacher">👨‍🏫 مدرس / مدرب (Teacher)</option>
                        <option value="employee">💼 موظف / كادر إداري (Employee)</option>
                        <option value="parent">👨‍👩‍👦 ولي أمر (Parent / Guardian)</option>
                        <option value="customer">🏢 عميل / شركات متعاقدة (Customer)</option>
                        <option value="supplier">📦 مورد / التزامات خارجية (Supplier)</option>
                        <option value="revenue">📈 إيراد نشاط أكاديمي (Revenue)</option>
                        <option value="expense">📉 مصروف تشغيلي وإداري (Expense)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">طبيعة الحساب *</label>
                    <select name="nature" id="editNature" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500 bg-white">
                        <option value="debit">مدين بطبيعته (Debit)</option>
                        <option value="credit">دائن بطبيعته (Credit)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <input type="checkbox" name="allow_cost_center" value="1" id="editAllowCc" class="rounded border-slate-300 text-indigo-600">
                    <label for="editAllowCc" class="text-xs font-bold text-slate-700 cursor-pointer">
                        ربط بمركز تكلفة
                    </label>
                </div>
                <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <input type="checkbox" name="is_active" value="1" id="editIsActive" class="rounded border-slate-300 text-indigo-600">
                    <label for="editIsActive" class="text-xs font-bold text-slate-700 cursor-pointer">
                        الحساب نشط وفعال
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">وصف وملاحظات الحساب</label>
                <textarea name="description" id="editDescription" rows="2" class="w-full text-xs rounded-xl border-slate-300 p-2.5 focus:border-indigo-500"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                <button type="button" onclick="closeEditAccountModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">إلغاء</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditAccountModal(acc) {
        document.getElementById('editAccountForm').action = '/accounting/chart-of-accounts/' + acc.id + '/update';
        document.getElementById('editModalTitle').innerText = 'تعديل الحساب: [' + acc.account_code + '] ' + acc.account_name;
        document.getElementById('editModalSubTitle').innerText = 'الرتبة ' + acc.level + ' (' + (acc.is_parent ? 'رئيسي' : 'فرعي') + ')';
        document.getElementById('editAccountName').value = acc.account_name || '';
        document.getElementById('editAnalyticalType').value = acc.analytical_type || 'general';
        document.getElementById('editNature').value = acc.nature || 'debit';
        document.getElementById('editAllowCc').checked = !!acc.allow_cost_center;
        document.getElementById('editIsActive').checked = !!acc.is_active;
        document.getElementById('editDescription').value = acc.description || '';
        document.getElementById('editAccountModal').classList.remove('hidden');
    }

    function closeEditAccountModal() {
        document.getElementById('editAccountModal').classList.add('hidden');
    }

    function updateParentLevelInfo() {
        const sel = document.getElementById('modalParentSelect');
        if (!sel || sel.selectedIndex < 0) return;
        const opt = sel.options[sel.selectedIndex];
        const lvl = parseInt(opt.getAttribute('data-level') || '1');
        const newLvl = lvl + 1;
        const infoBox = document.getElementById('accountLevelInfoBox');
        if (!infoBox) return;

        if (newLvl === 5) {
            infoBox.innerHTML = `
                <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🎯</span>
                        <div>
                            <div>الرتبة الناتجة: <strong>الرتبة 5 (فرعي حركي)</strong></div>
                            <div class="text-[10px] text-emerald-700 font-normal">يقبل قيود اليومية وسندات القبض والصرف مباشرة</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-black bg-emerald-200/70 text-emerald-900 px-2.5 py-1 rounded-md">حساب فرعي نهائي</span>
                </div>
            `;
        } else {
            infoBox.innerHTML = `
                <div class="p-3 rounded-2xl bg-slate-100 border border-slate-200 text-slate-800 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📁</span>
                        <div>
                            <div>الرتبة الناتجة: <strong>الرتبة ${newLvl} (رئيسي تجميعي)</strong></div>
                            <div class="text-[10px] text-slate-500 font-normal">حساب رقابي للميزانية ولا يقبل القيود المباشرة إلا بعد التفريع</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-black bg-slate-200 text-slate-700 px-2.5 py-1 rounded-md">حساب رئيسي</span>
                </div>
            `;
        }
    }

    function openAddAccountModal(parentId = null) {
        if (parentId) {
            document.getElementById('modalParentSelect').value = parentId;
        }
        updateParentLevelInfo();
        document.getElementById('addAccountModal').classList.remove('hidden');
    }

    function closeAddAccountModal() {
        document.getElementById('addAccountModal').classList.add('hidden');
    }

    function toggleNode(id) {
        const childrenContainer = document.getElementById('children-' + id);
        const icon = document.getElementById('icon-' + id);
        if (childrenContainer) {
            if (childrenContainer.classList.contains('hidden')) {
                childrenContainer.classList.remove('hidden');
                if (icon) icon.textContent = '📂';
            } else {
                childrenContainer.classList.add('hidden');
                if (icon) icon.textContent = '📁';
            }
        }
    }

    function expandAllTree() {
        document.querySelectorAll('.tree-children').forEach(el => el.classList.remove('hidden'));
        document.querySelectorAll('.tree-folder-icon').forEach(el => el.textContent = '📂');
    }

    function collapseAllTree() {
        document.querySelectorAll('.tree-children').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tree-folder-icon').forEach(el => el.textContent = '📁');
    }
</script>
@endsection
