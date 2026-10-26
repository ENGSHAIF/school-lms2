@extends('layouts.app', ['title' => 'إدارة المحتوى البصري والإعلانات - Visual CMS'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto my-4">

    <!-- شريط رأس الصفحة -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 bg-indigo-500/20 text-indigo-300 text-xs px-3.5 py-1 rounded-full font-bold border border-indigo-400/30">
                <span>🎨</span>
                <span>نظام إدارة المحتوى البصري والإعلانات الترويجية (Visual CMS)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">إدارة البانرات والعروض الترويجية</h1>
            <p class="text-xs text-slate-300">تحكم كامل في الإعلانات المجدولة، شريط التنبيهات العلوي، النوافذ المنبثقة، وبانرات البوابات الطلابية.</p>
        </div>

        <button type="button" onclick="document.getElementById('modalAddBanner').classList.remove('hidden')" 
            class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs px-6 py-3.5 rounded-2xl shadow-lg transition flex items-center gap-2 whitespace-nowrap">
            <span>➕</span>
            <span>إضافة بانر / إعلان جديد</span>
        </button>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                🖼️
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">إجمالي الإعلانات</span>
                <strong class="text-2xl font-black text-slate-900 font-mono">{{ $banners->count() }}</strong>
                <span class="text-[10px] text-slate-500 block">مسجلة في النظام</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                ✅
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">الإعلانات النشطة</span>
                <strong class="text-2xl font-black text-emerald-600 font-mono">{{ $banners->where('is_active', true)->count() }}</strong>
                <span class="text-[10px] text-slate-500 block">تظهر للزوار حالياً</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                📢
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">الشريط العلوي</span>
                <strong class="text-2xl font-black text-amber-600 font-mono">{{ $banners->where('placement', 'top_announcement')->count() }}</strong>
                <span class="text-[10px] text-slate-500 block">أعلى كافة الصفحات</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-rose-50 border border-rose-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                🎁
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">النوافذ المنبثقة</span>
                <strong class="text-2xl font-black text-rose-600 font-mono">{{ $banners->where('placement', 'popup_modal')->count() }}</strong>
                <span class="text-[10px] text-slate-500 block">ترويج فوري</span>
            </div>
        </div>
    </div>

    <!-- ================= قائمة البانرات التفاعلية ================= -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex justify-between items-center border-b pb-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span>📑</span>
                    <span>سجل البانرات والحملات الإعلانية النشطة</span>
                </h2>
                <p class="text-xs text-slate-500">يمكنك تشغيل أو إيقاف أي حملة بنقرة زر واحدة أو تعديل موضع ظهورها</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($banners as $b)
                <div class="rounded-3xl border-2 {{ $b->is_active ? 'border-slate-200 bg-white hover:border-indigo-400' : 'border-dashed border-slate-300 bg-slate-50/70 opacity-75' }} p-6 shadow-sm flex flex-col justify-between space-y-4 transition">
                    <div class="space-y-3">
                        <div class="flex justify-between items-start">
                            <span class="text-3xl p-2 rounded-2xl bg-slate-50 border border-slate-100 shadow-inner">{{ $b->icon }}</span>
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full {{ $b->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $b->is_active ? 'نشط ويعمل' : 'معطل' }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-400">
                                    {{ $b->placement === 'top_announcement' ? 'الشريط العلوي' : ($b->placement === 'hero_slider' ? 'سلايدر الرئيسية' : ($b->placement === 'popup_modal' ? 'نافذة منبثقة' : 'الشريط الجانبي')) }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-black text-sm text-slate-900 leading-snug">{{ $b->title }}</h3>
                            @if($b->subtitle)
                                <p class="text-xs text-slate-500 leading-relaxed mt-1">{{ $b->subtitle }}</p>
                            @endif
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 text-[11px] space-y-1 font-mono">
                            <div class="flex justify-between text-slate-500">
                                <span>الرابط المستهدف:</span>
                                <span class="text-indigo-600 font-bold truncate max-w-[150px]">{{ $b->link_url }}</span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>نص الزر:</span>
                                <strong class="text-slate-800">{{ $b->button_text }}</strong>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>صلاحية العرض:</span>
                                <span>{{ $b->ends_at ? $b->ends_at->format('Y-m-d') : 'دائم' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات والتحكم -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <form action="{{ route('admin.banners.toggle', $b->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-xl border {{ $b->is_active ? 'border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100' : 'border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }} transition">
                                {{ $b->is_active ? 'إيقاف مؤقت ⏸️' : 'تفعيل العرض ▶️' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.banners.destroy', $b->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا البانر نهائياً؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 p-1 font-bold">
                                حذف 🗑️
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-12 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
                    لا توجد إعلانات مسجلة. أضف إعلانك الأول للبدء.
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- مودال إنشاء بانر ترويجي جديد -->
<div id="modalAddBanner" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 p-8 max-w-lg w-full shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-black text-base text-slate-900">إنشاء بانر / حملة إعلانية جديدة</h3>
            <button type="button" onclick="document.getElementById('modalAddBanner').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>

        <form action="{{ route('admin.banners.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">عنوان الإعلان الرئيسي (بالعربية) *</label>
                <input type="text" name="title" required placeholder="مثال: خصم 20% على باقة الـ 24 حصة لتطوير المحادثة والتوفل" 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">النص الفرعي أو تفاصيل العرض</label>
                <textarea name="subtitle" rows="2" placeholder="تفاصيل العرض والمميزات الإضافية..." 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">موضع الظهور في المنصة *</label>
                    <select name="placement" class="w-full border border-slate-300 rounded-xl p-3 outline-none font-bold">
                        <option value="top_announcement">شريط الإعلانات العلوي</option>
                        <option value="hero_slider">سلايدر الصفحة الرئيسية</option>
                        <option value="popup_modal">نافذة منبثقة ترويجية (Popup)</option>
                        <option value="sidebar">الشريط الجانبي للبوابات</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">النمط اللوني *</label>
                    <select name="theme_style" class="w-full border border-slate-300 rounded-xl p-3 outline-none font-bold">
                        <option value="amber">ذهبي وأصفر (Amber)</option>
                        <option value="indigo">نيلي احترافي (Indigo)</option>
                        <option value="emerald">زمردي هادئ (Emerald)</option>
                        <option value="rose">وردي جذاب (Rose)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الرابط المستهدف *</label>
                    <input type="text" name="link_url" required value="/onboarding" 
                        class="w-full border border-slate-300 rounded-xl p-3 outline-none font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">نص الزر التفاعلي (CTA) *</label>
                    <input type="text" name="button_text" required value="احجز حصتك الآن 🚀" 
                        class="w-full border border-slate-300 rounded-xl p-3 outline-none font-bold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">استهداف الأجهزة</label>
                    <select name="device_target" class="w-full border border-slate-300 rounded-xl p-3 outline-none font-bold">
                        <option value="all">كافة الأجهزة (All)</option>
                        <option value="desktop">أجهزة الكمبيوتر فقط</option>
                        <option value="mobile">الهواتف الذكية فقط</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">أيقونة البانر</label>
                    <input type="text" name="icon" value="⭐" 
                        class="w-full border border-slate-300 rounded-xl p-3 outline-none text-center text-lg">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ البدء</label>
                    <input type="date" name="starts_at" value="{{ date('Y-m-d') }}" 
                        class="w-full border border-slate-300 rounded-xl p-3 outline-none font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الانتهاء</label>
                    <input type="date" name="ends_at" value="{{ date('Y-m-d', strtotime('+30 days')) }}" 
                        class="w-full border border-slate-300 rounded-xl p-3 outline-none font-mono">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="document.getElementById('modalAddBanner').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-bold">إلغاء</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-6 py-2.5 rounded-xl shadow-md transition">حفظ ونشر الإعلان 🚀</button>
            </div>
        </form>
    </div>
</div>
@endsection
