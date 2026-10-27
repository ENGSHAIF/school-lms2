@extends('layouts.app', ['title' => $announcement->title . ' - ' . config('asots.name_ar') . ' (ASOTS)'])

@section('content')
<div class="space-y-8 max-w-5xl mx-auto my-6">

    <!-- شريط المسار (Breadcrumb) -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-bold" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-[#0066A1] transition">الرئيسية</a>
        <span>›</span>
        <a href="{{ route('announcements.index') }}" class="hover:text-[#0066A1] transition">الإعلانات والأنشطة</a>
        <span>›</span>
        <span class="text-slate-800 line-clamp-1 max-w-xs">{{ $announcement->title }}</span>
    </nav>

    <!-- شريط الإدارة للمشرفين والمدراء -->
    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor() || auth()->user()->hasPermission('view_admin_dashboard')))
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <span class="font-bold text-amber-900 flex items-center gap-1.5">
                <span>👑</span>
                <span>أنت تستعرض هذا الإعلان بصفة إدارية / إشرافية (المعرف: #{{ $announcement->id }})</span>
            </span>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.banners.index') }}" class="bg-amber-600 hover:bg-amber-700 text-white font-black px-4 py-1.5 rounded-xl transition">
                    ✏️ تعديل في لوحة الإدارة
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <!-- القسم الرئيسي: تفاصيل الإعلان الكاملة -->
        <article class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm p-6 sm:p-10 space-y-8">
            
            <!-- ترويسة الإعلان -->
            <div class="space-y-4 border-b border-slate-100 pb-6">
                <div class="flex flex-wrap items-center gap-2">
                    @if($announcement->isEvent())
                        <span class="bg-purple-100 text-purple-900 text-xs font-black px-3.5 py-1 rounded-full border border-purple-200 shadow-sm">
                            🎓 فعالية / حفل تخرج معتمد
                        </span>
                        @if($announcement->batch_name)
                            <span class="bg-purple-50 text-purple-800 text-xs font-bold px-3 py-1 rounded-full border border-purple-200">
                                🏷️ {{ $announcement->batch_name }}
                            </span>
                        @endif
                        @if($announcement->event_date)
                            <span class="text-xs text-purple-700 font-bold bg-slate-100 px-3 py-1 rounded-full">
                                📅 تاريخ الفعالية: {{ $announcement->event_date->translatedFormat('d F Y') }}
                            </span>
                        @endif
                        @if($announcement->event_location)
                            <span class="text-xs text-slate-600 font-bold bg-slate-100 px-3 py-1 rounded-full">
                                📍 المكان: {{ $announcement->event_location }}
                            </span>
                        @endif
                    @else
                        <span class="bg-[#0066A1]/10 text-[#0066A1] text-xs font-black px-3 py-1 rounded-full border border-[#0066A1]/20">
                            {{ $announcement->icon }} إعلان رسمي معتمد
                        </span>
                        <span class="text-xs text-slate-400">
                            📅 تاريخ النشر: {{ $announcement->created_at ? $announcement->created_at->translatedFormat('d F Y') : 'مستمر' }}
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-snug">
                    {{ $announcement->title }}
                </h1>

                @if($announcement->title_en)
                    <p class="text-xs sm:text-sm font-semibold text-slate-500 font-sans tracking-wide" dir="ltr">
                        {{ $announcement->title_en }}
                    </p>
                @endif

                @if($announcement->subtitle)
                    <p class="text-sm text-slate-600 leading-relaxed font-medium bg-sky-50/60 p-4 rounded-2xl border border-sky-100">
                        {{ $announcement->subtitle }}
                    </p>
                @endif
            </div>

            <!-- بوستر الإعلان إن وُجد -->
            @if($announcement->image_path)
                <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-md bg-slate-900">
                    <img src="{{ asset($announcement->image_path) }}" alt="{{ $announcement->title }}" class="w-full max-h-[480px] object-contain mx-auto">
                </div>
            @endif

            <!-- نص المحتوى الكامل -->
            <div class="space-y-4 text-slate-800 leading-relaxed text-sm sm:text-base">
                @if($announcement->content)
                    <div class="prose max-w-none text-slate-700 whitespace-pre-line leading-relaxed font-medium bg-slate-50/70 p-6 rounded-2xl border border-slate-200/80">
{{ $announcement->content }}
                    </div>
                @else
                    <p class="text-slate-500 italic">
                        لا يوجد تفاصيل نصية إضافية لهذا الإعلان. يمكنك التواصل مباشرة عبر الأزرار المتاحة أدناه.
                    </p>
                @endif
            </div>

            <!-- ألبوم صور فعاليات الحفل كاملاً في حال كان المحتوى فعالية تخرج -->
            @if($announcement->isEvent())
                @php
                    $albumPhotos = \App\Models\Banner::active()->events()->orderBy('sort_order')->get();
                @endphp
                @if($albumPhotos->count() > 1)
                    <div class="space-y-4 pt-6 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                                <span>📸</span>
                                <span>ألبوم صور فعاليات حفل التخرج كاملاً ({{ $albumPhotos->count() }} صور موثقة)</span>
                            </h3>
                            <span class="text-xs text-purple-800 font-bold bg-purple-50 px-3 py-1 rounded-full border border-purple-200">
                                توثيق رسمي معتمد 🎓
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($albumPhotos as $pic)
                                <a href="{{ route('announcements.show', $pic->id) }}" class="group relative rounded-2xl overflow-hidden border-2 {{ $pic->id === $announcement->id ? 'border-purple-600 ring-2 ring-purple-400' : 'border-slate-200 hover:border-purple-400' }} bg-slate-950 aspect-[4/3] block shadow-sm hover:shadow-md transition">
                                    <img src="{{ asset($pic->image_path) }}" alt="{{ $pic->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex items-end p-2.5">
                                        <span class="text-[10px] sm:text-xs text-white font-bold line-clamp-1">
                                            {{ $pic->title }}
                                        </span>
                                    </div>
                                    @if($pic->id === $announcement->id)
                                        <span class="absolute top-2 right-2 bg-purple-600 text-white text-[9px] font-black px-2 py-0.5 rounded shadow">
                                            الصورة المعروضة
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- أزرار الإجراءات السريعة والمشاركة -->
            <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                @if($announcement->link_url)
                    <a href="{{ $announcement->link_url }}" target="_blank" rel="noopener noreferrer" class="bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] hover:to-[#b85807] text-white font-black text-sm px-8 py-4 rounded-2xl shadow-xl shadow-[#F58220]/25 transition flex items-center gap-2">
                        <span>{{ $announcement->button_text ?: 'التسجيل والاستفسار المباشر 💬' }}</span>
                    </a>
                @endif

                <button type="button" onclick="navigator.clipboard.writeText(window.location.href); alert('تم نسخ رابط الإعلان إلى الحافظة بنجاح! 📋');" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-5 py-3.5 rounded-2xl transition flex items-center gap-2">
                    <span>مشاركة ونسخ الرابط</span>
                    <span>🔗</span>
                </button>
            </div>

        </article>

        <!-- العمود الجانبي: بطاقة التواصل المباشر وإعلانات أخرى -->
        <aside class="space-y-6">

            <!-- بطاقة الاستفسار والتسجيل عبر واتساب -->
            <div class="bg-gradient-to-br from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-6 sm:p-7 shadow-lg space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl">
                        📞
                    </div>
                    <div>
                        <h3 class="font-black text-sm sm:text-base text-white">التسجيل والاستفسار</h3>
                        <p class="text-[11px] text-sky-200">مكتب النجم الآسيوي (ASOTS)</p>
                    </div>
                </div>

                <p class="text-xs text-sky-100 leading-relaxed">
                    تواصل مباشرة مع إدارة القبول والتسجيل أو إدارة التدريب والترجمة للاستفسار عن هذا الإعلان.
                </p>

                @php
                    $p1 = site_setting('leadership_direct_phone_2', '+967 735 999 101');
                    $cleanP1 = preg_replace('/[^0-9]/', '', $p1);
                    $p2 = site_setting('leadership_direct_phone_1', '+967 775 342 886');
                    $cleanP2 = preg_replace('/[^0-9]/', '', $p2);
                @endphp

                <div class="space-y-2 pt-2">
                    <a href="https://wa.me/967735999101?text={{ urlencode('مرحباً مكتب ASOTS، أرغب بالاستفسار عن الإعلان: ' . $announcement->title) }}" target="_blank" rel="noopener noreferrer" class="w-full bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] text-white font-black text-xs py-3 px-4 rounded-xl text-center shadow-md transition flex items-center justify-center gap-2">
                        <span>واتساب التسجيل المباشر: +967 735 999 101</span>
                        <span>💬</span>
                    </a>

                    <a href="https://wa.me/967775342886?text={{ urlencode('مرحباً مكتب ASOTS، أرغب بالاستفسار عن الإعلان: ' . $announcement->title) }}" target="_blank" rel="noopener noreferrer" class="w-full bg-white/10 hover:bg-white/20 text-white border border-white/25 font-bold text-xs py-2.5 px-4 rounded-xl text-center transition flex items-center justify-center gap-2">
                        <span>واتساب القيادة والإدارة: +967 775 342 886</span>
                    </a>

                    <a href="https://wa.me/967738837887?text={{ urlencode('مرحباً قسم الجودة وشؤون الطلاب، أرغب بالاستفسار عن الإعلان: ' . $announcement->title) }}" target="_blank" rel="noopener noreferrer" class="w-full bg-white/10 hover:bg-white/20 text-white border border-white/25 font-bold text-xs py-2.5 px-4 rounded-xl text-center transition flex items-center justify-center gap-2">
                        <span>الجودة وشؤون الطلاب: +967 738 837 887</span>
                    </a>

                    <a href="https://whatsapp.com/channel/0029Vajnq1y7DAWxCf429S2T" target="_blank" rel="noopener noreferrer" class="w-full bg-emerald-600/80 hover:bg-emerald-600 text-white font-black text-xs py-2.5 px-4 rounded-xl text-center transition flex items-center justify-center gap-2 mt-1">
                        <span>📢 قناة الطلاب الرسمية على واتساب</span>
                    </a>
                </div>

                <div class="pt-3 border-t border-white/15 text-[11px] text-sky-200 space-y-1">
                    <p>✉️ البريد: info@asots.edu.ye</p>
                    <p>📍 المركز الرئيسي: صنعاء - الجمهورية اليمنية</p>
                </div>
            </div>

            <!-- إعلانات أخرى ذات صلة -->
            @if($otherAnnouncements->count() > 0)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-black text-sm text-slate-800 flex items-center gap-2">
                        <span>📢</span>
                        <span>إعلانات وفرص أخرى</span>
                    </h3>

                    <div class="divide-y divide-slate-100">
                        @foreach($otherAnnouncements as $oa)
                            <a href="{{ route('announcements.show', $oa->id) }}" class="block py-3 hover:bg-slate-50 rounded-xl px-2 transition group">
                                <span class="text-[10px] text-slate-400 block mb-0.5">{{ $oa->created_at ? $oa->created_at->translatedFormat('d M Y') : '' }}</span>
                                <h4 class="text-xs font-black text-slate-800 group-hover:text-[#0066A1] leading-snug">
                                    {{ $oa->icon }} {{ $oa->title }}
                                </h4>
                            </a>
                        @endforeach
                    </div>

                    <a href="{{ route('announcements.index') }}" class="block text-center text-xs font-black text-[#0066A1] hover:underline pt-2">
                        استعراض كافة الإعلانات ←
                    </a>
                </div>
            @endif

        </aside>

    </div>

</div>
@endsection
