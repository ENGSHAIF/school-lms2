@extends('layouts.app', ['title' => 'تحديث كلمة المرور الإلزامي - ASOTS'])

@section('content')
<div class="max-w-lg mx-auto my-10 px-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
        
        <!-- الترويسة التحذيرية التوضيحية -->
        <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 p-6 sm:p-8 text-white text-center relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-36 h-36 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3 shadow-lg border border-white/20">
                🔐
            </div>
            <h1 class="text-xl sm:text-2xl font-black">تعيين كلمة مرور جديدة لحسابك</h1>
            <p class="text-xs sm:text-sm text-amber-100 mt-1 max-w-sm mx-auto">
                تم تغيير أو إعادة تعيين كلمة المرور لحسابك من قبل إدارة النظام، ولأمانك وخصوصيتك يلزم تعيين كلمة مرور شخصية جديدة للمتابعة.
            </p>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            
            <!-- بطاقة معلومات المستخدم المسجل -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 text-amber-400 font-black flex items-center justify-center text-base shadow-sm">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-slate-900 text-sm">{{ $user->name }}</span>
                            <span class="text-xs">{{ $user->gender_icon }}</span>
                        </div>
                        <span class="text-xs text-slate-500 font-mono block" dir="ltr">{{ $user->email }}</span>
                    </div>
                </div>
                <div class="text-left">
                    <span class="text-[10px] text-slate-400 block font-bold">الرقم الفريد للمستخدم</span>
                    <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 font-mono font-black text-xs rounded-lg inline-block">
                        {{ $user->user_code }}
                    </span>
                </div>
            </div>

            @if(session('warning'))
                <div class="bg-amber-50 border-r-4 border-amber-500 p-4 rounded-xl text-amber-800 text-xs font-bold leading-relaxed">
                    {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border-r-4 border-rose-500 p-4 rounded-xl text-rose-800 text-xs font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- نموذج تغيير كلمة المرور الإلزامي -->
            <form action="{{ route('password.force_change.update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        كلمة المرور الجديدة <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="forcePassword" required minlength="6"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm text-slate-800 font-mono pl-10"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassVisibility('forcePassword', this)" class="absolute left-3 top-3 text-slate-400 hover:text-slate-600 text-sm">
                            👁️
                        </button>
                    </div>
                    <span class="text-[11px] text-slate-400 block mt-1">يجب أن تتكون من 6 أحرف أو أرقام على الأقل.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        تأكيد كلمة المرور الجديدة <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="forcePasswordConf" required minlength="6"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm text-slate-800 font-mono pl-10"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassVisibility('forcePasswordConf', this)" class="absolute left-3 top-3 text-slate-400 hover:text-slate-600 text-sm">
                            👁️
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-black rounded-xl shadow-lg shadow-indigo-600/30 transition text-sm flex items-center justify-center gap-2">
                        <span>💾</span>
                        <span>حفظ كلمة المرور والدخول إلى حسابي</span>
                    </button>
                </div>
            </form>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>تريد تسجيل الخروج والعودة لاحقاً؟</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="font-bold text-rose-600 hover:underline">
                        تسجيل الخروج 🚪
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
function togglePassVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerText = '🙈';
    } else {
        input.type = 'password';
        btn.innerText = '👁️';
    }
}
</script>
@endsection
