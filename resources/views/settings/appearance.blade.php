@extends('layouts.app', ['title' => 'المظهر والخطوط والتخصيص - Appearance & Customization'])

@section('content')
<div class="max-w-6xl mx-auto space-y-10 my-6">

    <!-- رأس الصفحة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                    نظام التخصيص البصري • Theme & Font Customization
                </span>
                <span class="text-xs text-slate-400 font-mono">تطبيق فوري عبر كامل المنصة</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900">المظهر والخطوط (Appearance & Fonts)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">اختر القالب اللوني ونوع الخط المفضل لديك؛ يتم تطبيقه فوراً عبر كافة صفحات المنصة وحسابك.</p>
        </div>

        <form action="{{ route('settings.appearance.reset') }}" method="POST">
            @csrf
            <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                <span>🔄</span>
                <span>استعادة المظهر والخط الافتراضي</span>
            </button>
        </form>
    </div>

    <!-- 1. قسم اختيار نوع الخط العربي (Arabic Font Family) -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
        <div>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span>✍️</span>
                    <span>نوع الخط المعتمد للمنصة (Typography):</span>
                </h2>
                <span class="text-xs text-indigo-600 font-bold bg-indigo-50 px-2.5 py-1 rounded-lg">الخط النشط حالياً: {{ $activeFont }}</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">اضغط على أي خط لاختياره وسيتم تحويل خطوط الموقع بالكامل إليه فوراً.</p>
        </div>

        @php
            $fontsList = [
                'Cairo' => [
                    'name_ar' => 'خط القاهرة (Cairo)',
                    'desc' => 'الخط الافتراضي الرصين، متوازن وواضح جداً في القراءة والعناوين الأكاديمية.',
                    'family' => "'Cairo', sans-serif",
                ],
                'Almarai' => [
                    'name_ar' => 'خط المراعي (Almarai)',
                    'desc' => 'خط هندسي عصري فائق النقاء والأناقة ومناسب للمنصات الرقمية الحديثة.',
                    'family' => "'Almarai', sans-serif",
                ],
                'Tajawal' => [
                    'name_ar' => 'خط تجوال (Tajawal)',
                    'desc' => 'خط ناعم وانسيابي مريح جداً للعين أثناء جلسات الدراسة والقراءة الطويلة.',
                    'family' => "'Tajawal', sans-serif",
                ],
                'IBM Plex Sans Arabic' => [
                    'name_ar' => 'خط آي بي إم بلكس (IBM Plex)',
                    'desc' => 'طابع تقني ومؤسسي عالي الدقة يعكس الاحترافية والانضباط الأكاديمي.',
                    'family' => "'IBM Plex Sans Arabic', sans-serif",
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
            @foreach($fontsList as $fontKey => $f)
                @php $isFontActive = ($activeFont === $fontKey); @endphp
                <form action="{{ route('settings.appearance.font') }}" method="POST" class="h-full">
                    @csrf
                    <input type="hidden" name="font" value="{{ $fontKey }}">
                    <button type="submit" class="w-full h-full text-right p-5 rounded-2xl border-2 transition flex flex-col justify-between group {{ $isFontActive ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500/20 shadow-md' : 'border-slate-200 hover:border-indigo-300 hover:bg-slate-50' }}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-black text-slate-900 group-hover:text-indigo-600 transition">{{ $f['name_ar'] }}</span>
                                @if($isFontActive)
                                    <span class="text-[10px] font-black bg-indigo-600 text-white px-2 py-0.5 rounded-full">نشط ✓</span>
                                @endif
                            </div>
                            <!-- معاينة نصية حية بنفس الخط -->
                            <div class="py-3 px-3 my-2 bg-white rounded-xl border border-slate-200 text-sm text-slate-800 leading-relaxed font-bold" style="font-family: {{ $f['family'] }};">
                                أبجد هوز حطي كلمن • ASOTS
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ $f['desc'] }}</p>
                        </div>
                        <div class="pt-3 mt-3 border-t border-slate-200/60 flex items-center justify-between text-[11px] font-bold text-indigo-600">
                            <span>{{ $isFontActive ? 'الخط المعتمد حالياً' : 'تطبيق هذا الخط ⚡' }}</span>
                            <span>←</span>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    <!-- 2. شبكة القوالب اللونية العشرة (10 Theme Profiles) -->
    <div class="space-y-4">
        <div>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span>🎨</span>
                    <span>القوالب اللونية المعتمدة (10 Theme Profiles):</span>
                </h2>
                <span class="text-xs text-amber-600 font-bold bg-amber-50 px-2.5 py-1 rounded-lg">القالب النشط: {{ $themes[$activeTheme]['name_ar'] ?? $activeTheme }}</span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">اختر القالب اللوني المناسب لك؛ اضغط على أي بطاقة لتطبيقه مباشرة على المنصة بالكامل.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($themes as $themeId => $t)
                @php
                    $isActive = ($activeTheme === $themeId);
                @endphp
                <form action="{{ route('settings.appearance.update') }}" method="POST" class="h-full">
                    @csrf
                    <input type="hidden" name="theme" value="{{ $themeId }}">
                    <button type="submit"
                        class="w-full text-right h-full bg-white rounded-3xl border-2 {{ $isActive ? 'border-indigo-600 ring-4 ring-indigo-500/10 shadow-lg' : 'border-slate-200 hover:border-indigo-300 hover:shadow-md' }} p-6 cursor-pointer transition flex flex-col justify-between group relative overflow-hidden">

                        @if($isActive)
                            <div class="absolute top-0 left-0 bg-indigo-600 text-white text-[10px] font-black px-3.5 py-1 rounded-br-2xl flex items-center gap-1">
                                <span>✓</span>
                                <span>القالب المعتمد</span>
                            </div>
                        @endif

                        <div class="w-full">
                            <!-- باليتة ألوان مصغرة -->
                            <div class="flex items-center gap-1.5 mb-4">
                                <span class="w-7 h-7 rounded-lg shadow-sm border border-black/10 inline-block" style="background-color: {{ $t['primary'] }}" title="اللون الأساسي"></span>
                                <span class="w-7 h-7 rounded-lg shadow-sm border border-black/10 inline-block" style="background-color: {{ $t['cta'] }}" title="لون الإجراء CTA"></span>
                                <span class="w-7 h-7 rounded-lg shadow-sm border border-black/10 inline-block" style="background-color: {{ $t['bg'] }}" title="لون الخلفية"></span>
                                <span class="w-7 h-7 rounded-lg shadow-sm border border-black/10 inline-block" style="background-color: {{ $t['badge'] }}" title="شارة التمييز"></span>
                            </div>

                            <div class="flex justify-between items-start mb-1">
                                <h3 class="font-black text-base text-slate-900 group-hover:text-indigo-600 transition">{{ $t['name_ar'] }}</h3>
                                <span class="text-[11px] font-bold text-slate-400 font-sans" dir="ltr">{{ $t['name_en'] }}</span>
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">{{ $t['desc_ar'] }}</p>
                        </div>

                        <div class="w-full pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-indigo-600 group-hover:underline">
                                {{ $isActive ? 'القالب مفعل الآن ✓' : 'اضغط للتطبيق الفوري ⚡' }}
                            </span>
                            <div class="w-5 h-5 rounded-full border-2 {{ $isActive ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300' }} flex items-center justify-center">
                                @if($isActive)
                                    <span class="w-2 h-2 bg-white rounded-full"></span>
                                @endif
                            </div>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    <!-- 3. إعدادات إمكانية الوصول وتكبير الخط (Accessibility) -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="font-black text-sm text-slate-900 mb-0.5">حجم الخط وإمكانية الوصول (Text Size & Accessibility)</h3>
            <p class="text-xs text-slate-500">يمكنك تعديل حجم النصوص في كامل المنصة لتسهيل القراءة للأطفال أو في الجلسات الطويلة.</p>
        </div>

        <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
            <button onclick="setFontSize('normal')" id="fontNormalBtn" class="px-4 py-1.5 rounded-xl text-xs font-bold bg-white text-slate-800 shadow-sm transition">
                عادي (100%)
            </button>
            <button onclick="setFontSize('large')" id="fontLargeBtn" class="px-4 py-1.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                كبير ومريح (115%)
            </button>
        </div>
    </div>

</div>

<!-- سكريبت التحكم بحجم الخط واستعادة التفضيل -->
<script>
function setFontSize(size) {
    if (size === 'large') {
        document.documentElement.style.fontSize = '18px';
        localStorage.setItem('asots_font_size', 'large');
        document.getElementById('fontLargeBtn')?.classList.add('bg-white', 'text-slate-800', 'shadow-sm');
        document.getElementById('fontNormalBtn')?.classList.remove('bg-white', 'text-slate-800', 'shadow-sm');
    } else {
        document.documentElement.style.fontSize = '16px';
        localStorage.setItem('asots_font_size', 'normal');
        document.getElementById('fontNormalBtn')?.classList.add('bg-white', 'text-slate-800', 'shadow-sm');
        document.getElementById('fontLargeBtn')?.classList.remove('bg-white', 'text-slate-800', 'shadow-sm');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const savedFont = localStorage.getItem('asots_font_size');
    if (savedFont === 'large') setFontSize('large');
});
</script>
@endsection
