@extends('layouts.app', ['title' => 'باقات الحصص التفاعلية 1-on-1'])

@section('content')
<div class="space-y-8 max-w-5xl mx-auto my-4">

    <!-- الترويسة -->
    <div class="text-center max-w-2xl mx-auto">
        <span class="bg-indigo-100 text-indigo-700 text-xs px-3 py-1 rounded-full font-bold inline-block mb-3">
            باقات حصص ASOTS المعتمدة
        </span>
        <h1 class="text-3xl font-black text-slate-900 mb-2">اختر باقة الحصص المناسبة لمستواك</h1>
        <p class="text-slate-500 text-xs">
            جميع الحصص هي جلسات فردية حية ومباشرة (1-on-1) مدة كل جلسة 25 دقيقة مع تقييم نطق وتوفل مباشر.
        </p>
    </div>

    <!-- شبكة الباقات -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($packages as $pkg)
            <div class="bg-white rounded-3xl border {{ $pkg->lessons_count >= 24 ? 'border-indigo-500 shadow-xl ring-2 ring-indigo-500/20' : 'border-slate-200 shadow-sm' }} p-6 flex flex-col justify-between relative overflow-hidden">
                
                @if($pkg->lessons_count >= 24)
                    <div class="absolute top-0 left-0 bg-indigo-600 text-white text-[10px] font-black px-4 py-1 rounded-br-2xl uppercase tracking-wider">
                        الأكثر طلباً للتوفل ⭐
                    </div>
                @endif

                <div>
                    <h3 class="font-black text-lg text-slate-900 mb-1">{{ $pkg->title }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">{{ $pkg->description }}</p>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-6 text-center font-mono">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ $pkg->formatted_price }}</span>
                        <span class="text-xs text-slate-500 block sm:inline mt-1 sm:mt-0">/ {{ $pkg->lessons_count }} حصة حية</span>
                        <span class="text-[11px] text-indigo-600 font-bold block mt-1">({{ \App\Helpers\ArabicCurrencyHelper::format(round($pkg->price / $pkg->lessons_count, 1), $pkg->currency, 1) }} للحصة الواحدة)</span>
                    </div>

                    <!-- مميزات الباقة -->
                    <ul class="space-y-2.5 text-xs text-slate-700 mb-6">
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span><strong>{{ $pkg->lessons_count }} جلسة حية (1-on-1)</strong> مدة كل جلسة 25 دقيقة.</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span>اختيار وتغيير المعلم في كل حصة بحرية.</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span>تقييم النطق بمحرك الذكاء الاصطناعي الصوتي قبل وبعد الحصة.</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span>تقرير فوري يرسل إلى <strong>واتساب</strong> بعد كل جلسة.</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">✓</span>
                            <span>سند قبض مالي رسمي (المعايير المحاسبية المعتمدة) قابل للتحميل فوراً.</span>
                        </li>
                    </ul>
                </div>

                <!-- نموذج الشراء وتأكيد السند -->
                <form action="{{ route('packages.subscribe') }}" method="POST" class="pt-4 border-t border-slate-100 space-y-3">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">طريقة السداد المفضلة:</label>
                        <select name="payment_method" required class="w-full text-xs border border-slate-300 rounded-xl px-2.5 py-1.5 focus:ring-1 focus:ring-indigo-500 outline-none">
                            <option value="cash">نقداً عبر الصندوق</option>
                            <option value="bank_transfer">إيداع / حوالة بنكية</option>
                            <option value="electronic_wallet">محفظة إلكترونية (جوالي / ون كاش / كريمي)</option>
                        </select>
                    </div>

                    <button type="submit" 
                        class="w-full py-3 {{ $pkg->lessons_count >= 24 ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-slate-900 hover:bg-slate-800 text-white' }} font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2">
                        <span>⚡</span>
                        <span>شراء الباقة وشحن الحصص فوراً</span>
                    </button>
                </form>

            </div>
        @endforeach
    </div>

</div>
@endsection
