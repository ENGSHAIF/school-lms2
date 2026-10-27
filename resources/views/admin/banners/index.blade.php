@extends('layouts.app', ['title' => 'إدارة المحتوى البصري والإعلانات - Visual CMS'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto my-4">

    <!-- شريط رأس الصفحة الرسمي -->
    <div class="bg-gradient-to-r from-slate-900 via-[#004e7c] to-[#0066A1] text-white p-7 sm:p-9 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 bg-[#F58220]/25 text-[#F58220] text-xs px-3.5 py-1 rounded-full font-bold border border-[#F58220]/40">
                <span>📢 🎓</span>
                <span>نظام إدارة المحتوى البصري والإعلانات ومعرض الفعاليات (Visual CMS & Events Gallery)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">إدارة الإعلانات ومعرض حفلات التخرج والفعاليات</h1>
            <p class="text-xs text-sky-100 max-w-2xl leading-relaxed">
                تحكم متكامل في محتوى المنصة البصري: نشر الإعلانات الترويجية، وإضافة وتوثيق صور حفلات التخرج والفعاليات المعتمدة لتظهر بسلايدر مستقل في الصفحة الرئيسية.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('announcements.index') }}" target="_blank"
                class="bg-white/10 hover:bg-white/20 text-white border border-white/25 font-bold text-xs px-4 py-3 rounded-2xl transition flex items-center gap-2 whitespace-nowrap">
                <span>👁️</span>
                <span>معاينة صفحة الإعلانات بالمنصة</span>
            </a>

            <button type="button" onclick="openAddModal('ad')" 
                class="bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] text-white font-black text-xs px-5 py-3.5 rounded-2xl shadow-lg transition flex items-center gap-2 whitespace-nowrap">
                <span>📢</span>
                <span>إضافة إعلان جديد</span>
            </button>

            <button type="button" onclick="openAddModal('event_gallery')" 
                class="bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 text-white font-black text-xs px-5 py-3.5 rounded-2xl shadow-lg transition flex items-center gap-2 whitespace-nowrap">
                <span>🎓</span>
                <span>إضافة فعالية / حفلة تخرج</span>
            </button>
        </div>
    </div>

    <!-- رسائل التنبيه والنجاح أو أخطاء الإدخال -->
    @if(session('success'))
        <div class="bg-emerald-50 border-2 border-emerald-400 text-emerald-900 px-6 py-4 rounded-3xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="text-2xl">✅</span>
                <span class="font-black text-sm">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-black text-lg">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border-2 border-rose-400 text-rose-900 px-6 py-4 rounded-3xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="text-2xl">❌</span>
                <span class="font-black text-sm">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-black text-lg">✕</button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border-2 border-rose-400 text-rose-900 px-6 py-4 rounded-3xl shadow-sm space-y-2">
            <div class="flex items-center gap-2 font-black text-sm">
                <span class="text-xl">⚠️</span>
                <span>تنبيه: لم يتم حفظ المحتوى بسبب أخطاء في البيانات المدخلة:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 text-rose-800 font-bold mr-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- بطاقات الإحصائيات السريعة -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-sky-50 border border-sky-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner text-[#0066A1]">
                🖼️
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">إجمالي المحتويات</span>
                <strong class="text-2xl font-black text-slate-900 font-mono">{{ $counts['total'] ?? $banners->count() }}</strong>
                <span class="text-[10px] text-slate-500 block">إعلانات وفعاليات مسجلة</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner text-[#F58220]">
                📢
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">الإعلانات الترويجية</span>
                <strong class="text-2xl font-black text-amber-600 font-mono">{{ $counts['ads'] ?? 0 }}</strong>
                <span class="text-[10px] text-slate-500 block">عروض ودورات وقنوات</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-purple-50 border border-purple-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner text-purple-700">
                🎓
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">حفلات التخرج والفعاليات</span>
                <strong class="text-2xl font-black text-purple-700 font-mono">{{ $counts['events'] ?? 0 }}</strong>
                <span class="text-[10px] text-slate-500 block">معرض الصور والألبومات</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner text-emerald-600">
                ✅
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">المحتوى النشط</span>
                <strong class="text-2xl font-black text-emerald-600 font-mono">{{ $counts['active'] ?? 0 }}</strong>
                <span class="text-[10px] text-slate-500 block">يظهر للزوار حالياً</span>
            </div>
        </div>
    </div>

    <!-- ================= بطاقة التحكم السريع في إعلان البداية العلوي (Top Announcement) ================= -->
    @php
        $topBannerItem = $banners->where('placement', 'top_announcement')->first();
    @endphp
    @if($topBannerItem)
        <div class="bg-gradient-to-r from-amber-50 via-orange-50 to-amber-100 border-2 border-amber-300 rounded-3xl p-6 sm:p-8 shadow-md space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-amber-200/80 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-[#F58220] text-white flex items-center justify-center text-2xl font-black shadow-md shrink-0">
                        🎁
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base sm:text-lg font-black text-amber-950">إعلان البداية والشريط العلوي في المنصة (Top Banner)</h2>
                            <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full {{ $topBannerItem->is_active ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700' }}">
                                {{ $topBannerItem->is_active ? 'نشط ويعمل' : 'متوقف حالياً' }}
                            </span>
                        </div>
                        <p class="text-xs text-amber-800 mt-0.5">هذا هو الإعلان الذي يظهر في أعلى شريط في كل صفحات المنصة (مثل عرض تعلم الإنجليزية وحصص التجربة المجانية).</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="openEditModal({{ json_encode($topBannerItem) }})" 
                        class="bg-[#0066A1] hover:bg-[#004e7c] text-white font-black text-xs px-5 py-2.5 rounded-xl shadow transition flex items-center gap-1.5">
                        <span>✏️</span>
                        <span>تعديل صيغة إعلان البداية</span>
                    </button>
                    <form action="{{ route('admin.banners.toggle', $topBannerItem->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-black px-4 py-2.5 rounded-xl border {{ $topBannerItem->is_active ? 'bg-amber-200/80 hover:bg-amber-300 text-amber-900 border-amber-300' : 'bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600' }} transition">
                            {{ $topBannerItem->is_active ? 'إيقاف مؤقت ⏸️' : 'تفعيل فوري ▶️' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- نموذج تعديل سريع مباشر لإعلان البداية -->
            <form action="{{ route('admin.banners.update', $topBannerItem->id) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="placement" value="top_announcement">
                <input type="hidden" name="theme_style" value="{{ $topBannerItem->theme_style }}">
                <input type="hidden" name="device_target" value="{{ $topBannerItem->device_target }}">
                <input type="hidden" name="icon" value="{{ $topBannerItem->icon }}">
                <input type="hidden" name="sort_order" value="{{ $topBannerItem->sort_order }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block font-bold text-amber-950 text-xs mb-1">صيغة وعنوان إعلان البداية (العربي):</label>
                        <input type="text" name="title" value="{{ $topBannerItem->title }}" required 
                            class="w-full bg-white border border-amber-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-bold text-slate-800 shadow-sm">
                    </div>
                    <div>
                        <label class="block font-bold text-amber-950 text-xs mb-1">نص زر البداية (CTA):</label>
                        <input type="text" name="button_text" value="{{ $topBannerItem->button_text }}" required 
                            class="w-full bg-white border border-amber-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-bold text-slate-800 shadow-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-amber-950 text-xs mb-1">النص الفرعي / التوضيحي المصاحب لإعلان البداية:</label>
                        <input type="text" name="subtitle" value="{{ $topBannerItem->subtitle }}" 
                            class="w-full bg-white border border-amber-300 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs text-slate-700 shadow-sm">
                    </div>
                    <div>
                        <label class="block font-bold text-amber-950 text-xs mb-1">الرابط المستهدف عند النقر على الزر:</label>
                        <input type="text" name="link_url" value="{{ $topBannerItem->link_url }}" required 
                            class="w-full bg-white border border-amber-300 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-mono text-slate-700 shadow-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center gap-2 text-xs text-amber-900 font-bold">
                        <span>معاينة فورية للشريط:</span>
                        <span class="bg-amber-600 text-slate-950 px-2 py-0.5 rounded font-black">{{ $topBannerItem->icon }} {{ $topBannerItem->title }}</span>
                        <span class="bg-slate-950 text-white px-2 py-0.5 rounded text-[10px]">{{ $topBannerItem->button_text }}</span>
                    </div>

                    <button type="submit" class="bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] text-white font-black text-xs px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
                        <span>💾</span>
                        <span>حفظ تعديل صيغة إعلان البداية</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- ================= قائمة وسجل كافة الإعلانات والفعاليات المسجلة ================= -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b pb-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span>📑</span>
                    <span>سجل الإعلانات وحفلات التخرج (تحكم شامل: تعديل، حذف، تفعيل، ومعاينة)</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">يمكنك إضافة وتعديل أي إعلان أو فعالية تخرج، ورفع بوسترات وصور الحفلات وموضع ظهورها في أي وقت.</p>
            </div>

            <!-- تبويبات التصفية السريعة (Filter Tabs) -->
            <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl border border-slate-200 shrink-0">
                <a href="{{ route('admin.banners.index', ['category' => 'all']) }}" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-black transition {{ ($currentCategory ?? 'all') === 'all' ? 'bg-[#0066A1] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    الكل ({{ $counts['total'] ?? $banners->count() }})
                </a>
                <a href="{{ route('admin.banners.index', ['category' => 'ad']) }}" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-black transition {{ ($currentCategory ?? '') === 'ad' ? 'bg-[#F58220] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    📢 الإعلانات ({{ $counts['ads'] ?? 0 }})
                </a>
                <a href="{{ route('admin.banners.index', ['category' => 'event_gallery']) }}" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-black transition {{ ($currentCategory ?? '') === 'event_gallery' ? 'bg-purple-700 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    🎓 حفلات التخرج ({{ $counts['events'] ?? 0 }})
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($banners as $b)
                <div class="rounded-3xl border-2 {{ $b->is_active ? ($b->isEvent() ? 'border-purple-200 bg-white hover:border-purple-500' : 'border-slate-200 bg-white hover:border-[#0066A1]') : 'border-dashed border-slate-300 bg-slate-50/70 opacity-80' }} p-6 shadow-sm flex flex-col justify-between space-y-5 transition duration-200">
                    <div class="space-y-4">
                        
                        <!-- الشريط العلوي للبطاقة -->
                        <div class="flex justify-between items-start">
                            <span class="text-3xl p-2 rounded-2xl {{ $b->isEvent() ? 'bg-purple-50 border-purple-100 text-purple-700' : 'bg-slate-50 border-slate-100' }} border shadow-inner shrink-0">{{ $b->icon }}</span>
                            <div class="flex flex-col items-end gap-1">
                                <div class="flex items-center gap-1.5">
                                    @if($b->isEvent())
                                        <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                                            🎓 حفلة / فعالية تخرج
                                        </span>
                                    @else
                                        <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-200">
                                            📢 إعلان ترويجي
                                        </span>
                                    @endif
                                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full {{ $b->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $b->is_active ? 'نشط ✅' : 'معطل ⏸️' }}
                                    </span>
                                </div>

                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                    {{ $b->isEvent() ? 'سلايدر معرض الفعاليات' : ($b->placement === 'top_announcement' ? 'الشريط العلوي (البداية)' : ($b->placement === 'hero_slider' ? 'سلايدر الرئيسية' : ($b->placement === 'popup_modal' ? 'نافذة منبثقة' : 'الشريط الجانبي'))) }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">ترتيب: #{{ $b->sort_order }}</span>
                            </div>
                        </div>

                        <!-- صورة البوستر المرفقة إن وُجدت -->
                        @if($b->image_path)
                            <div class="h-36 rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 shadow-inner group relative">
                                <img src="{{ asset($b->image_path) }}" alt="{{ $b->title }}" class="w-full h-full object-cover">
                                <span class="absolute bottom-2 right-2 bg-black/60 backdrop-blur-md text-white text-[9px] font-bold px-2 py-0.5 rounded">
                                    {{ $b->isEvent() ? 'صورة الفعالية 📸' : 'بوستر مرفق 🖼️' }}
                                </span>
                            </div>
                        @else
                            <div class="h-20 rounded-2xl bg-gradient-to-r from-sky-50 to-indigo-50 border border-dashed border-sky-200 flex items-center justify-center text-slate-400 text-xs gap-1.5 font-bold">
                                <span>🖼️</span>
                                <span>بدون صورة (نصوص فقط)</span>
                            </div>
                        @endif

                        <div>
                            <h3 class="font-black text-sm text-slate-900 leading-snug line-clamp-2">{{ $b->title }}</h3>
                            
                            <!-- تفاصيل خاصة بحفلات التخرج والفعاليات -->
                            @if($b->isEvent())
                                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                    @if($b->batch_name)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-purple-50 text-purple-700 px-2.5 py-0.5 rounded-lg border border-purple-200">
                                            <span>🏷️</span>
                                            <span>{{ $b->batch_name }}</span>
                                        </span>
                                    @endif
                                    @if($b->event_date)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-lg">
                                            <span>📅</span>
                                            <span>{{ $b->event_date->translatedFormat('d M Y') }}</span>
                                        </span>
                                    @endif
                                    @if($b->event_location)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-lg">
                                            <span>📍</span>
                                            <span>{{ $b->event_location }}</span>
                                        </span>
                                    @endif
                                </div>
                            @endif

                            @if($b->subtitle)
                                <p class="text-xs text-slate-500 leading-relaxed mt-1.5 line-clamp-2">{{ $b->subtitle }}</p>
                            @endif
                            @if($b->content)
                                <span class="inline-block mt-2 text-[10px] text-[#0066A1] font-bold bg-sky-50 border border-sky-100 px-2 py-0.5 rounded-md">
                                    يتضمن نصاً تفصيلياً معتمداً 📄
                                </span>
                            @endif
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 text-[11px] space-y-1 font-mono">
                            <div class="flex justify-between text-slate-500">
                                <span>زر الإجراء:</span>
                                <strong class="text-slate-800">{{ $b->button_text }}</strong>
                            </div>
                            <div class="flex justify-between text-slate-500 truncate">
                                <span>الرابط:</span>
                                <span class="text-[#0066A1] font-bold truncate max-w-[170px]" title="{{ $b->link_url }}">{{ $b->link_url }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات والتحكم الكامل في الإعلان -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <!-- زر التعديل الشامل -->
                            <button type="button" onclick="openEditModal({{ json_encode($b) }})" 
                                class="text-xs font-bold px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition flex items-center gap-1" title="تعديل كاملاً">
                                <span>✏️</span>
                                <span>تعديل</span>
                            </button>

                            <!-- زر التفعيل / الإيقاف -->
                            <form action="{{ route('admin.banners.toggle', $b->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-bold px-2.5 py-1.5 rounded-xl border {{ $b->is_active ? 'border-amber-300 text-amber-800 bg-amber-50 hover:bg-amber-100' : 'border-emerald-300 text-emerald-800 bg-emerald-50 hover:bg-emerald-100' }} transition" title="{{ $b->is_active ? 'إيقاف مؤقت' : 'تفعيل' }}">
                                    {{ $b->is_active ? '⏸️' : '▶️' }}
                                </button>
                            </form>

                            <!-- زر المعاينة في المنصة -->
                            <a href="{{ route('announcements.show', $b->id) }}" target="_blank" class="text-xs font-bold px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="معاينة بالمنصة">
                                👁️
                            </a>
                        </div>

                        <!-- زر الحذف النهائي -->
                        <form action="{{ route('admin.banners.destroy', $b->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنصر نهائياً من المنصة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-2 py-1.5 rounded-xl font-bold transition flex items-center gap-1" title="حذف">
                                <span>🗑️</span>
                                <span>حذف</span>
                            </button>
                        </form>
                    </div>

                </div>
            @empty
                <div class="col-span-3 p-12 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
                    لا توجد محتويات مسجلة في هذا التبويب. أضف إعلاناً أو فعالية تخرج للبدء.
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- ================= مودال تعديل إعلان موجود (Edit Modal) ================= -->
<div id="modalEditBanner" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 max-w-2xl w-full shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-3">
            <div class="flex items-center gap-2">
                <span class="text-xl">✏️</span>
                <h3 class="font-black text-base text-slate-900" id="editModalHeaderTitle">تعديل بيانات الإعلان</h3>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 font-bold text-xl px-2">✕</button>
        </div>

        <form id="editBannerForm" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- تحديد نوع المحتوى: إعلان عادي أم حفلة تخرج وفعالية -->
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 space-y-2">
                <label class="block font-bold text-slate-800">نوع المحتوى المراد عرضه وتحديده بالمنصة *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="category" id="edit_category_ad" value="ad" class="sr-only peer" onchange="toggleEditCategoryFields('ad')">
                        <div class="p-3 rounded-xl border-2 border-slate-200 peer-checked:border-[#0066A1] peer-checked:bg-sky-50 text-slate-700 peer-checked:text-[#0066A1] flex items-center gap-2 font-bold transition">
                            <span class="text-xl">📢</span>
                            <div>
                                <span class="block text-xs font-black">إعلان ترويجي</span>
                                <span class="block text-[10px] text-slate-500 font-normal">سلايدر الإعلانات العلوي</span>
                            </div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="category" id="edit_category_event" value="event_gallery" class="sr-only peer" onchange="toggleEditCategoryFields('event_gallery')">
                        <div class="p-3 rounded-xl border-2 border-slate-200 peer-checked:border-purple-600 peer-checked:bg-purple-50 text-slate-700 peer-checked:text-purple-700 flex items-center gap-2 font-bold transition">
                            <span class="text-xl">🎓</span>
                            <div>
                                <span class="block text-xs font-black">حفلة تخرج / فعالية</span>
                                <span class="block text-[10px] text-slate-500 font-normal">معرض الفعاليات تحت الإعلانات</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- بيانات تفصيلية خاصة بحفلات التخرج والفعاليات (Event Meta) -->
            <div id="edit_event_fields" class="hidden bg-purple-50/70 border border-purple-200 p-3.5 rounded-2xl space-y-3">
                <div class="flex items-center gap-2 text-purple-900 font-black text-xs">
                    <span>🎓</span>
                    <span>بيانات وتوثيق الفعالية / حفلة التخرج:</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">اسم الدفعة / البرنامج:</label>
                        <input type="text" name="batch_name" id="edit_batch_name" placeholder="مثال: الدفعة 38 - دبلوم الإنجليزية" class="w-full border border-purple-200 rounded-xl p-2.5 outline-none font-bold text-xs bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تاريخ إقامة الفعالية / الحفل:</label>
                        <input type="date" name="event_date" id="edit_event_date" class="w-full border border-purple-200 rounded-xl p-2.5 outline-none font-mono text-xs bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">مكان إقامة الفعالية / القاعة:</label>
                        <input type="text" name="event_location" id="edit_event_location" placeholder="مثال: صنعاء - القاعة الكبرى" class="w-full border border-purple-200 rounded-xl p-2.5 outline-none text-xs bg-white">
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1" id="edit_title_label">عنوان الإعلان الرئيسي (بالعربية) *</label>
                <input type="text" name="title" id="edit_title" required 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-bold text-slate-800">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">العنوان بالإنجليزية (English Title - اختياري)</label>
                <input type="text" name="title_en" id="edit_title_en" dir="ltr"
                    class="w-full border border-slate-300 rounded-xl p-2.5 outline-none focus:ring-1 focus:ring-[#0066A1] text-xs">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الموجز الترويجي / الوصف المصاحب</label>
                <input type="text" name="subtitle" id="edit_subtitle" 
                    class="w-full border border-slate-300 rounded-xl p-2.5 outline-none focus:ring-1 focus:ring-[#0066A1] text-xs">
            </div>

            <!-- صورة البوستر أو صورة الحفلة الحالية واستبدالها -->
            <div class="bg-sky-50/70 p-4 rounded-2xl border border-sky-200 space-y-2">
                <label class="block font-bold text-[#0066A1]" id="edit_image_label">صورة البوستر أو صورة الفعالية المرفقة (JPG, PNG, WEBP):</label>
                <div id="edit_image_preview_box" class="hidden items-center gap-4 bg-white p-2.5 rounded-xl border border-sky-200">
                    <img id="edit_image_preview" src="" alt="Poster" class="w-20 h-14 object-cover rounded-lg border">
                    <div class="flex-1 text-[11px] text-slate-600">
                        <span class="font-bold block">توجد صورة مرفقة حالياً لهذا المحتوى.</span>
                        <label class="inline-flex items-center gap-1 text-rose-600 font-bold mt-1 cursor-pointer">
                            <input type="checkbox" name="remove_image" value="1" class="rounded">
                            <span>حذف الصورة الحالية وجعل المحتوى نصياً فقط</span>
                        </label>
                    </div>
                </div>
                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-600 file:ml-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:bg-[#0066A1] file:text-white file:text-xs file:font-bold hover:file:bg-[#004e7c]">
                <span class="text-[10px] text-slate-500 block">ارفع صورة جديدة لاستبدال الصورة الحالية.</span>
            </div>

            <!-- النص الكامل والتفصيلي -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">البيان والنص التفصيلي (تفاصيل الفعالية، المكرمين، المميزات):</label>
                <textarea name="content" id="edit_content" rows="5" 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] leading-relaxed font-sans text-xs"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">موضع الظهور في المنصة *</label>
                    <select name="placement" id="edit_placement" class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-bold text-xs">
                        <option value="hero_slider">سلايدر الرئيسية / معرض الفعاليات</option>
                        <option value="top_announcement">شريط الإعلانات العلوي (إعلان البداية)</option>
                        <option value="popup_modal">نافذة منبثقة ترويجية (Popup)</option>
                        <option value="sidebar">الشريط الجانبي للبوابات</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">النمط اللوني *</label>
                    <select name="theme_style" id="edit_theme_style" class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-bold text-xs">
                        <option value="amber">ذهبي وبرتقالي رسمي (Amber/Gold)</option>
                        <option value="indigo">أزرق محيطي (Ocean Blue)</option>
                        <option value="emerald">زمردي هادئ (Emerald)</option>
                        <option value="rose">وردي جذاب (Rose)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الأجهزة المستهدفة *</label>
                    <select name="device_target" id="edit_device_target" class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-bold text-xs">
                        <option value="all">كافة الأجهزة (كمبيوتر وجوال)</option>
                        <option value="desktop">أجهزة الكمبيوتر فقط</option>
                        <option value="mobile">الهواتف الذكية فقط</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الأيقونة التعبيرية</label>
                    <input type="text" name="icon" id="edit_icon" class="w-full border border-slate-300 rounded-xl p-2.5 outline-none text-center text-sm font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الرابط المستهدف (أو رابط الواتساب/الألبوم) *</label>
                    <input type="text" name="link_url" id="edit_link_url" required 
                        class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-mono text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">نص الزر التفاعلي (CTA) *</label>
                    <input type="text" name="button_text" id="edit_button_text" required 
                        class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-bold text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ البدء</label>
                    <input type="date" name="starts_at" id="edit_starts_at" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-mono text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الانتهاء</label>
                    <input type="date" name="ends_at" id="edit_ends_at" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-mono text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">ترتيب العرض (Sort Order)</label>
                    <input type="number" name="sort_order" id="edit_sort_order" min="0" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-mono text-xs">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-slate-500 font-bold text-xs hover:text-slate-700">إلغاء</button>
                <button type="submit" class="bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] text-white font-black px-7 py-3 rounded-xl shadow-md transition text-xs">
                    حفظ كافة التعديلات 💾
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= مودال إنشاء إعلان أو فعالية تخرج جديدة (Create Modal) ================= -->
<div id="modalAddBanner" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 max-w-xl w-full shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-black text-base text-slate-900" id="addModalHeaderTitle">➕ إضافة محتوى جديد (إعلان / حفلة تخرج)</h3>
            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 font-bold text-xl px-2">✕</button>
        </div>

        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- اختيار نوع المحتوى: إعلان عادي أو حفلة تخرج -->
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 space-y-2">
                <label class="block font-bold text-slate-800">نوع المحتوى المراد إضافته *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="category" id="add_category_ad" value="ad" checked class="sr-only peer" onchange="toggleAddCategoryFields('ad')">
                        <div class="p-3 rounded-xl border-2 border-slate-200 peer-checked:border-[#0066A1] peer-checked:bg-sky-50 text-slate-700 peer-checked:text-[#0066A1] flex items-center gap-2 font-bold transition">
                            <span class="text-xl">📢</span>
                            <div>
                                <span class="block text-xs font-black">إعلان ترويجي</span>
                                <span class="block text-[10px] text-slate-500 font-normal">سلايدر الإعلانات الرئيسي</span>
                            </div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="category" id="add_category_event" value="event_gallery" class="sr-only peer" onchange="toggleAddCategoryFields('event_gallery')">
                        <div class="p-3 rounded-xl border-2 border-slate-200 peer-checked:border-purple-600 peer-checked:bg-purple-50 text-slate-700 peer-checked:text-purple-700 flex items-center gap-2 font-bold transition">
                            <span class="text-xl">🎓</span>
                            <div>
                                <span class="block text-xs font-black">حفلة تخرج / فعالية</span>
                                <span class="block text-[10px] text-slate-500 font-normal">معرض الفعاليات تحت الإعلانات</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- حقول إضافية خاصة بحفلات التخرج والفعاليات (Event Meta) -->
            <div id="add_event_fields" class="hidden bg-purple-50/70 border border-purple-200 p-3.5 rounded-2xl space-y-3">
                <div class="flex items-center gap-2 text-purple-900 font-black text-xs">
                    <span>🎓</span>
                    <span>بيانات وتوثيق الفعالية / حفلة التخرج:</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">اسم الدفعة / البرنامج:</label>
                        <input type="text" name="batch_name" id="add_batch_name" placeholder="مثال: الدفعة 38 - دبلوم الإنجليزية" class="w-full border border-purple-200 rounded-xl p-2.5 outline-none font-bold text-xs bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تاريخ إقامة الفعالية / الحفل:</label>
                        <input type="date" name="event_date" id="add_event_date" value="{{ date('Y-m-d') }}" class="w-full border border-purple-200 rounded-xl p-2.5 outline-none font-mono text-xs bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">مكان إقامة الفعالية / القاعة:</label>
                        <input type="text" name="event_location" id="add_event_location" placeholder="مثال: صنعاء - القاعة الكبرى" class="w-full border border-purple-200 rounded-xl p-2.5 outline-none text-xs bg-white">
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1" id="add_title_label">عنوان الإعلان الرئيسي (بالعربية) *</label>
                <input type="text" name="title" id="add_title_input" required placeholder="مثال: حفل تخرج طلاب الدفعة 38 - دبلوم اللغة الإنجليزية الشامل" 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] text-xs font-bold">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">العنوان بالإنجليزية (English Title - اختياري)</label>
                <input type="text" name="title_en" placeholder="Graduation Ceremony of Batch 38..." dir="ltr"
                    class="w-full border border-slate-300 rounded-xl p-2.5 outline-none focus:ring-1 focus:ring-[#0066A1] text-xs">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">موجز وصفي مختصر (Subtitle)</label>
                <input type="text" name="subtitle" id="add_subtitle_input" placeholder="موجز جذاب يظهر في البطاقات وشريط التنبيهات..." 
                    class="w-full border border-slate-300 rounded-xl p-2.5 outline-none focus:ring-1 focus:ring-[#0066A1] text-xs">
            </div>

            <!-- إرفاق صورة البوستر أو صورة الفعالية -->
            <div class="bg-indigo-50/60 p-3.5 rounded-2xl border border-indigo-200 space-y-1">
                <label class="block font-bold text-indigo-950" id="add_image_label">صورة الإعلان أو ألبوم الحفلة (JPG, PNG, WEBP - حتى 10MB):</label>
                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-600 file:ml-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:bg-[#0066A1] file:text-white file:text-xs file:font-bold hover:file:bg-[#004e7c]">
                <span class="text-[10px] text-slate-500 block">عند إرفاق صورة، ستظهر بدقة عالية في السلايدر المخصص ومعرض الصور.</span>
            </div>

            <!-- النص الكامل والتفصيلي -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">البيان والنص التفصيلي (تفاصيل الفعالية، المكرمين، المميزات):</label>
                <textarea name="content" rows="5" placeholder="اكتب تفاصيل الفعالية أو شروط التسجيل، المميزات، وأرقام التواصل..." 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-[#0066A1] leading-relaxed font-sans text-xs"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">موضع الظهور في المنصة *</label>
                    <select name="placement" id="add_placement_select" class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-bold text-xs">
                        <option value="hero_slider" selected>سلايدر الرئيسية / معرض الفعاليات</option>
                        <option value="top_announcement">شريط الإعلانات العلوي (إعلان البداية)</option>
                        <option value="popup_modal">نافذة منبثقة ترويجية (Popup)</option>
                        <option value="sidebar">الشريط الجانبي للبوابات</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">النمط اللوني *</label>
                    <select name="theme_style" class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-bold text-xs">
                        <option value="amber">ذهبي وبرتقالي رسمي (Amber/Gold)</option>
                        <option value="indigo">أزرق محيطي (Ocean Blue)</option>
                        <option value="emerald">زمردي هادئ (Emerald)</option>
                        <option value="rose">وردي جذاب (Rose)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الأجهزة المستهدفة *</label>
                    <select name="device_target" class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-bold text-xs">
                        <option value="all" selected>كافة الأجهزة (كمبيوتر وجوال)</option>
                        <option value="desktop">أجهزة الكمبيوتر فقط</option>
                        <option value="mobile">الهواتف الذكية فقط</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الرابط المستهدف *</label>
                    <input type="text" name="link_url" id="add_link_url" value="/onboarding" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-mono text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">نص الزر التفاعلي (CTA) *</label>
                    <input type="text" name="button_text" id="add_button_text" value="احجز حصتك الآن 🚀" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-bold text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ البدء</label>
                    <input type="date" name="starts_at" value="{{ date('Y-m-d') }}" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-mono text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الانتهاء</label>
                    <input type="date" name="ends_at" value="{{ date('Y-m-d', strtotime('+365 days')) }}" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 outline-none font-mono text-xs">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-slate-500 font-bold text-xs">إلغاء</button>
                <button type="submit" class="bg-[#0066A1] hover:bg-[#004e7c] text-white font-black px-6 py-2.5 rounded-xl shadow-md transition text-xs">
                    حفظ ونشر في المنصة 🚀
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal(category = 'ad') {
        const modal = document.getElementById('modalAddBanner');
        modal.classList.remove('hidden');
        if (category === 'event_gallery') {
            document.getElementById('add_category_event').checked = true;
            toggleAddCategoryFields('event_gallery');
        } else {
            document.getElementById('add_category_ad').checked = true;
            toggleAddCategoryFields('ad');
        }
    }

    function closeAddModal() {
        document.getElementById('modalAddBanner').classList.add('hidden');
    }

    function toggleAddCategoryFields(type) {
        const eventFields = document.getElementById('add_event_fields');
        const headerTitle = document.getElementById('addModalHeaderTitle');
        const titleLabel = document.getElementById('add_title_label');
        const titleInput = document.getElementById('add_title_input');
        const imgLabel = document.getElementById('add_image_label');
        const linkInput = document.getElementById('add_link_url');
        const btnInput = document.getElementById('add_button_text');

        if (type === 'event_gallery') {
            eventFields.classList.remove('hidden');
            headerTitle.innerText = '🎓 إضافة وتوثيق فعالية / حفلة تخرج جديدة';
            titleLabel.innerText = 'عنوان الفعالية أو حفل التخرج الرئيسي *';
            titleInput.placeholder = 'مثال: حفل تخرج وتكريم طلاب الدفعة 38 - دبلوم اللغة الإنجليزية الشامل';
            imgLabel.innerText = 'صورة أو بوستر الحفل التذكاري (JPG, PNG, WEBP) * :';
            if (linkInput.value === '/onboarding' || linkInput.value === '') {
                linkInput.value = '#gallery';
            }
            if (btnInput.value === 'احجز حصتك الآن 🚀' || btnInput.value === '') {
                btnInput.value = 'مشاهدة ألبوم الحفل 📸';
            }
        } else {
            eventFields.classList.add('hidden');
            headerTitle.innerText = '➕ إضافة إعلان أو حملة ترويجية جديدة';
            titleLabel.innerText = 'عنوان الإعلان الرئيسي (بالعربية) *';
            titleInput.placeholder = 'مثال: فتح باب التسجيل في دورات اللغة الإنجليزية من الصفر حتى الاحتراف';
            imgLabel.innerText = 'صورة الإعلان أو البوستر الترويجي (JPG, PNG, WEBP):';
            if (linkInput.value === '#gallery') {
                linkInput.value = '/onboarding';
            }
            if (btnInput.value === 'مشاهدة ألبوم الحفل 📸') {
                btnInput.value = 'احجز حصتك الآن 🚀';
            }
        }
    }

    function toggleEditCategoryFields(type) {
        const eventFields = document.getElementById('edit_event_fields');
        const titleLabel = document.getElementById('edit_title_label');
        const imgLabel = document.getElementById('edit_image_label');

        if (type === 'event_gallery') {
            eventFields.classList.remove('hidden');
            titleLabel.innerText = 'عنوان الفعالية أو حفل التخرج الرئيسي *';
            imgLabel.innerText = 'صورة الحفل أو البوستر المرفق (JPG, PNG, WEBP):';
        } else {
            eventFields.classList.add('hidden');
            titleLabel.innerText = 'عنوان الإعلان الرئيسي (بالعربية) *';
            imgLabel.innerText = 'صورة البوستر المرفقة (JPG, PNG, WEBP):';
        }
    }

    function openEditModal(banner) {
        document.getElementById('editBannerForm').action = '/admin/banners/' + banner.id + '/update';
        document.getElementById('editModalHeaderTitle').innerText = 'تعديل المحتوى: #' + banner.id + ' (' + banner.title.substring(0, 30) + '...)';
        
        // ضبط نوع المحتوى (Category)
        const isEvent = (banner.category === 'event_gallery');
        if (isEvent) {
            document.getElementById('edit_category_event').checked = true;
            toggleEditCategoryFields('event_gallery');
        } else {
            document.getElementById('edit_category_ad').checked = true;
            toggleEditCategoryFields('ad');
        }

        // تعبئة حقول الفعالية
        document.getElementById('edit_batch_name').value = banner.batch_name || '';
        document.getElementById('edit_event_date').value = banner.event_date ? banner.event_date.substring(0, 10) : '';
        document.getElementById('edit_event_location').value = banner.event_location || '';

        // باقي الحقول
        document.getElementById('edit_title').value = banner.title || '';
        document.getElementById('edit_title_en').value = banner.title_en || '';
        document.getElementById('edit_subtitle').value = banner.subtitle || '';
        document.getElementById('edit_content').value = banner.content || '';
        document.getElementById('edit_icon').value = banner.icon || (isEvent ? '🎓' : '⭐');
        document.getElementById('edit_placement').value = banner.placement || 'hero_slider';
        document.getElementById('edit_theme_style').value = banner.theme_style || 'amber';
        document.getElementById('edit_device_target').value = banner.device_target || 'all';
        document.getElementById('edit_link_url').value = banner.link_url || '';
        document.getElementById('edit_button_text').value = banner.button_text || '';
        document.getElementById('edit_sort_order').value = banner.sort_order !== null ? banner.sort_order : 0;

        if (banner.starts_at) {
            document.getElementById('edit_starts_at').value = banner.starts_at.substring(0, 10);
        } else {
            document.getElementById('edit_starts_at').value = '';
        }

        if (banner.ends_at) {
            document.getElementById('edit_ends_at').value = banner.ends_at.substring(0, 10);
        } else {
            document.getElementById('edit_ends_at').value = '';
        }

        const previewBox = document.getElementById('edit_image_preview_box');
        const previewImg = document.getElementById('edit_image_preview');
        if (banner.image_path) {
            previewImg.src = '/' + banner.image_path;
            previewBox.classList.remove('hidden');
            previewBox.classList.add('flex');
        } else {
            previewBox.classList.add('hidden');
            previewBox.classList.remove('flex');
        }

        document.getElementById('modalEditBanner').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEditBanner').classList.add('hidden');
    }

    // إغلاق المودالات عند الضغط على Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
            closeAddModal();
        }
    });
</script>
@endsection
