@extends('layouts.app', ['title' => 'تسجيل حساب طالب جديد'])

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
        
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                📝
            </div>
            <h1 class="text-2xl font-black text-slate-900">تسجيل طالب جديد</h1>
            <p class="text-slate-500 text-xs mt-1">أنشئ حسابك الآن لتخوض اختبار تحديد المستوى والمحادثة</p>
        </div>

        @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-xs p-3 rounded-xl font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">الاسم الكامل بالعربية <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-sm text-slate-800"
                    placeholder="محمد شائف عبدالواحد علي">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">الاسم باللغة الإنجليزية (Full English Name) <span class="text-slate-400 font-normal">(اختياري)</span></label>
                <input type="text" name="name_en" value="{{ old('name_en') }}" 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-sm text-slate-800 font-medium"
                    placeholder="mohammed shaif abdulwahid ali" dir="ltr">
            </div>

            <!-- الجنس (إجباري) وتاريخ الميلاد (اختياري) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">الجنس (Gender) <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-500 has-[:checked]:text-emerald-800">
                            <input type="radio" name="gender" value="male" {{ old('gender', 'male') === 'male' ? 'checked' : '' }} required class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-bold">ذكر 👨</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 p-2 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-500 has-[:checked]:text-emerald-800">
                            <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }} required class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-bold">أنثى 👩</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">تاريخ الميلاد (Date of Birth) <span class="text-slate-400 font-normal">(اختياري)</span></label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" 
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-xs text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">البريد الإلكتروني <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-sm text-slate-800"
                    placeholder="student@example.com" dir="ltr">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">رقم هاتف الواتساب (مهم جداً) <span class="text-rose-500">*</span></label>
                <input type="text" name="phone_whatsapp" value="{{ old('phone_whatsapp') }}" required 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-sm text-slate-800 font-mono"
                    placeholder="+967 " dir="ltr">
                <span class="text-[11px] text-slate-400 block mt-1">ستصلك سندات القبض ونتائج الاختبارات عبر هذا الرقم تلقائياً.</span>
            </div>


            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">كلمة المرور</label>
                <input type="password" name="password" required 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-sm text-slate-800"
                    placeholder="••••••••">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" required 
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-sm text-slate-800"
                    placeholder="••••••••">
            </div>

            <button type="submit" 
                class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 transition text-sm mt-2">
                إنشاء الحساب وبدء اختبار القبول
            </button>
        </form>

        <div class="text-center mt-6 pt-6 border-t border-slate-100">
            <span class="text-xs text-slate-500">لديك حساب بالفعل؟</span>
            <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-600 hover:underline mr-1">سجّل دخولك</a>
        </div>

    </div>
</div>
@endsection
