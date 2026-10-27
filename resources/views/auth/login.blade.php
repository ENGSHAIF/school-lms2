@extends('layouts.app', ['title' => 'تسجيل الدخول - ASOTS'])

@section('content')
<div class="max-w-md mx-auto my-8 px-4">
    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6 sm:p-8">
        
        <div class="text-center mb-6">
            <div class="w-24 h-24 bg-white rounded-3xl p-2 mx-auto mb-3 shadow-xl border border-slate-100 flex items-center justify-center ring-4 ring-[#0271a6]/20">
                <img src="{{ asset('images/logo.png') }}" alt="ASOTS Logo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl font-black text-slate-900">بوابة الدخول | ASOTS</h1>
            <p class="text-slate-700 text-xs mt-1.5 font-bold">مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات</p>
            <p class="text-slate-400 text-[11px] mt-0.5">The Asian Star Office for Translation, Training, & Consultancy Services</p>
        </div>

        @if(session('success_reset_request'))
            <div class="mb-5 bg-emerald-50 border-r-4 border-emerald-500 text-emerald-800 text-xs p-3.5 rounded-xl font-bold leading-relaxed">
                ✅ {{ session('success_reset_request') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-5 bg-amber-50 border-r-4 border-amber-500 text-amber-800 text-xs p-3.5 rounded-xl font-bold leading-relaxed">
                ⚠️ {{ session('warning') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 bg-red-50 border-r-4 border-red-500 text-red-700 text-xs p-3.5 rounded-xl font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        البريد الإلكتروني أو الرقم الفريد للمستخدم
                    </label>
                    <span class="text-[10px] text-indigo-600 font-mono font-bold bg-indigo-50 px-2 py-0.5 rounded-md">
                        Email or User ID
                    </span>
                </div>
                <input type="text" id="loginInput" name="login" value="{{ old('login', old('email')) }}" required 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition text-sm text-slate-800 font-medium"
                    placeholder="example@school.com أو STU-2026-0001">
                <span class="text-[11px] text-slate-400 block mt-1">يمكنك استخدام بريدك الإلكتروني أو كود حسابك الفريد (مثل: STU-2026-0001).</span>
            </div>

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-xs font-bold text-slate-700">كلمة المرور</label>
                    <button type="button" onclick="openResetModal()" class="text-[11px] font-bold text-amber-600 hover:text-amber-700 hover:underline">
                        نسيت كلمة المرور؟
                    </button>
                </div>
                <input type="password" id="password" name="password" required 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition text-sm text-slate-800 font-mono"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span>تذكر بيانات دخولي</span>
                </label>
                <button type="button" onclick="openResetModal()" class="text-xs text-indigo-600 font-bold hover:underline flex items-center gap-1">
                    <span>📩</span>
                    <span>طلب استعادة الحساب من الإدارة</span>
                </button>
            </div>

            <button type="submit" 
                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition text-sm">
                دخول للنظام
            </button>
        </form>

        <div class="text-center mt-6 pt-5 border-t border-slate-100">
            <span class="text-xs text-slate-500">طالب جديد وتريد الانضمام؟</span>
            <a href="{{ route('register') }}" class="text-xs font-bold text-indigo-600 hover:underline mr-1">أنشئ حسابك وقدم اختبار القبول</a>
        </div>

    </div>
</div>

<!-- =============================================================== -->
<!-- نافذة طلب استرجاع وتغيير كلمة المرور من الإدارة (Reset Request Modal) -->
<!-- =============================================================== -->
<div id="resetRequestModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="fixed inset-0 cursor-pointer" onclick="closeResetModal()"></div>
    <div class="relative bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 p-6 z-10">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                    🔑
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">طلب استرجاع الحساب من الإدارة</h3>
                    <p class="text-[11px] text-slate-500">سيتم إرسال تنبيه لإدارة النظام لتغيير كلمة المرور والتواصل معك.</p>
                </div>
            </div>
            <button type="button" onclick="closeResetModal()" class="text-slate-400 hover:text-slate-700 text-base font-bold">✕</button>
        </div>

        <form action="{{ route('password.reset_request.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    البريد الإلكتروني أو الرقم الفريد للمستخدم <span class="text-red-500">*</span>
                </label>
                <input type="text" name="identifier" required placeholder="مثال: user@example.com أو STU-2026-0001"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs text-slate-800">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الاسم الكامل (اختياري)</label>
                <input type="text" name="name" placeholder="اسمك المسجل في النظام"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs text-slate-800">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">رقم الهاتف والواتساب للتواصل (مهم)</label>
                <input type="text" name="phone" placeholder="+967735999101"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs text-slate-800 font-mono" dir="ltr">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات إضافية للإدارة (اختياري)</label>
                <textarea name="user_notes" rows="2" placeholder="نسيت كلمة المرور، يرجى إعادة تعيينها وتزويدي بها عبر الواتساب..."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs text-slate-800"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeResetModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>📨</span>
                    <span>إرسال الطلب للإدارة</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openResetModal() {
    document.getElementById('resetRequestModal').classList.remove('hidden');
}

function closeResetModal() {
    document.getElementById('resetRequestModal').classList.add('hidden');
}

window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeResetModal();
});
</script>
@endsection

