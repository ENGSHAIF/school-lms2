@extends('layouts.app', ['title' => 'مركز الإعلانات والحملات الرسمية - ' . config('asots.name_ar') . ' (ASOTS)'])

@section('content')
<div class="space-y-10 max-w-6xl mx-auto my-6">

    <!-- شريط الإدارة للمشرفين والمدراء -->
    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor() || auth()->user()->hasPermission('view_admin_dashboard')))
        <div class="bg-gradient-to-r from-amber-50 via-orange-50 to-amber-100 border-2 border-amber-300/80 p-4 sm:p-5 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3 text-amber-950">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-900 flex items-center justify-center text-2xl font-black shrink-0">
                    📢
                </div>
                <div>
                    <h3 class="font-black text-sm sm:text-base text-amber-950">لوحة إدارة الإعلانات والحملات الترويجية</h3>
                    <p class="text-xs text-amber-800 mt-0.5">يمكنك إضافة إعلانات جديدة، رفع بوسترات وصور، وتحديد فترات العرض والأزرار التفاعلية.</p>
                </div>
            </div>
            <a href="{{ route('admin.banners.index') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#F58220] to-amber-500 hover:from-[#e0751a] hover:to-amber-600 text-white font-black text-xs shadow-md transition flex items-center gap-2 whitespace-nowrap">
                <span>➕</span>
                <span>إدارة وإضافة الإعلانات والبوسترات</span>
            </a>
        </div>
    @endif

    <!-- رأس الصفحة الرسمي -->
    <div class="text-center space-y-3">
        <span class="inline-flex items-center gap-2 text-xs font-black text-[#0066A1] bg-sky-50 px-4 py-1.5 rounded-full border border-sky-200 shadow-sm">
            <span>📢 🎓</span>
            <span>المركز الإعلامي ومعرض الأنشطة • Announcements & Events</span>
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
            مركز الإعلانات ومعرض حفلات التخرج والفعاليات
        </h1>
        <p class="text-sm text-slate-600 max-w-2xl mx-auto leading-relaxed">
            تابع آخر الإعلانات الرسمية، فتح القنوات والدورات، وتوثيق حفلات التخرج والفعاليات الأكاديمية بمكتب النجم الآسيوي (ASOTS).
        </p>

        <!-- تبويبات التصفية التفاعلية السريعة -->
        <div class="flex items-center justify-center gap-2 pt-2">
            <div class="inline-flex items-center p-1.5 bg-slate-100 rounded-2xl border border-slate-200 shadow-sm">
                <button type="button" onclick="filterAnnouncements('all')" id="tab_all" 
                    class="ann-tab-btn px-4 py-2 rounded-xl text-xs font-black transition bg-[#0066A1] text-white shadow-sm">
                    🌟 كافة الأنشطة ({{ $announcements->count() }})
                </button>
                <button type="button" onclick="filterAnnouncements('ad')" id="tab_ad" 
                    class="ann-tab-btn px-4 py-2 rounded-xl text-xs font-black transition text-slate-600 hover:text-slate-900">
                    📢 الإعلانات والعروض ({{ $announcements->where('category', '!=', 'event_gallery')->count() }})
                </button>
                <button type="button" onclick="filterAnnouncements('event_gallery')" id="tab_event" 
                    class="ann-tab-btn px-4 py-2 rounded-xl text-xs font-black transition text-slate-600 hover:text-slate-900">
                    🎓 حفلات التخرج والفعاليات ({{ $announcements->where('category', 'event_gallery')->count() }})
                </button>
            </div>
        </div>
    </div>

    <!-- شبكة الإعلانات والفعاليات النشطة -->
    @if($announcements->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8" id="announcementsGrid">
            @foreach($announcements as $ann)
                <div class="announcement-item bg-white rounded-3xl border-2 {{ $ann->isEvent() ? 'border-purple-200 hover:border-purple-500' : 'border-slate-200 hover:border-[#0066A1]' }} overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group" data-category="{{ $ann->category ?: 'ad' }}">
                    
                    <div>
                        <!-- صورة أو هيدر الإعلان -->
                        @if($ann->image_path)
                            <div class="relative h-60 bg-slate-900 overflow-hidden">
                                <img src="{{ asset($ann->image_path) }}" alt="{{ $ann->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-transparent to-black/20"></div>
                                
                                @if($ann->isEvent())
                                    <span class="absolute top-4 right-4 bg-purple-700/95 backdrop-blur-md text-white text-[10px] font-black px-3.5 py-1 rounded-full shadow border border-purple-300/40">
                                        🎓 حفلة / فعالية تخرج
                                    </span>
                                    @if($ann->batch_name)
                                        <span class="absolute top-4 left-4 bg-black/60 backdrop-blur-md text-purple-200 text-[10px] font-bold px-3 py-1 rounded-full border border-white/20">
                                            🏷️ {{ $ann->batch_name }}
                                        </span>
                                    @endif
                                @else
                                    <span class="absolute top-4 right-4 bg-[#0066A1]/90 backdrop-blur-md text-white text-[10px] font-black px-3 py-1 rounded-full shadow border border-white/20">
                                        {{ $ann->icon }} إعلان رسمي معتمد
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="h-32 {{ $ann->isEvent() ? 'bg-gradient-to-r from-purple-900 via-indigo-900 to-purple-800' : 'bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1]' }} p-6 flex items-center justify-between text-white relative overflow-hidden">
                                <div class="relative z-10 flex items-center gap-3">
                                    <span class="text-3xl">{{ $ann->icon }}</span>
                                    <span class="text-xs font-bold bg-white/20 px-3 py-1 rounded-full border border-white/30">
                                        {{ $ann->isEvent() ? '🎓 حفلة / فعالية تخرج معتمدة' : '📢 إعلان رسمي معتمد' }}
                                    </span>
                                </div>
                                <span class="text-6xl opacity-10 absolute -left-4 -bottom-4 font-black">ASOTS</span>
                            </div>
                        @endif

                        <!-- محتوى البطاقة -->
                        <div class="p-6 sm:p-7 space-y-4">
                            <div class="flex items-center gap-2 text-xs text-slate-400 flex-wrap">
                                @if($ann->isEvent() && $ann->event_date)
                                    <span class="text-purple-700 font-black">📅 {{ $ann->event_date->translatedFormat('d F Y') }}</span>
                                @else
                                    <span>📅 {{ $ann->created_at ? $ann->created_at->translatedFormat('d F Y') : 'مستمر' }}</span>
                                @endif

                                @if($ann->isEvent() && $ann->event_location)
                                    <span>•</span>
                                    <span class="text-slate-600 font-bold">📍 {{ $ann->event_location }}</span>
                                @endif

                                <span>•</span>
                                <span class="text-[#F58220] font-bold">مكتب ASOTS للترجمة والتدريب</span>
                            </div>

                            <h2 class="text-xl font-black text-slate-900 leading-snug {{ $ann->isEvent() ? 'group-hover:text-purple-700' : 'group-hover:text-[#0066A1]' }} transition-colors">
                                <a href="{{ route('announcements.show', $ann->id) }}">
                                    {{ $ann->title }}
                                </a>
                            </h2>

                            @if($ann->subtitle)
                                <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 leading-relaxed">
                                    {{ $ann->subtitle }}
                                </p>
                            @endif

                            @if($ann->content)
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3.5 text-xs text-slate-600 leading-relaxed line-clamp-3">
                                    {{ Str::limit(strip_tags($ann->content), 200) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- أزرار الإجراءات في أسفل البطاقة -->
                    <div class="p-6 pt-0 flex flex-wrap items-center gap-3 border-t border-slate-100 mt-4">
                        <a href="{{ route('announcements.show', $ann->id) }}" class="flex-1 {{ $ann->isEvent() ? 'bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800' : 'bg-gradient-to-r from-[#0066A1] to-[#004e7c] hover:from-[#005586]' }} text-white font-black text-xs py-3 px-4 rounded-xl text-center shadow-md transition flex items-center justify-center gap-2">
                            <span>{{ $ann->isEvent() ? 'استعراض ألبوم وتفاصيل الحفل 📸' : 'قراءة تفاصيل الإعلان 📄' }}</span>
                        </a>

                        @if($ann->link_url && $ann->link_url !== '#gallery')
                            <a href="{{ $ann->link_url }}" target="_blank" rel="noopener noreferrer" class="bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] hover:to-[#b85807] text-white font-black text-xs py-3 px-4 rounded-xl text-center shadow-md transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                                <span>{{ $ann->button_text ?: 'التسجيل والاستفسار' }}</span>
                            </a>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-sky-50 text-[#0066A1] mx-auto flex items-center justify-center text-3xl">
                📢
            </div>
            <h3 class="text-xl font-bold text-slate-800">لا توجد إعلانات نشطة حالياً</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto">
                يتم تحديث الإعلانات الرسمية والفرص التدريبية بشكل مستمر، يرجى معاودة الزيارة لاحقاً.
            </p>
        </div>
    @endif

    <!-- قسم الاستفسار السريع والتواصل المباشر الموحد -->
    <div class="bg-gradient-to-br from-[#003859] via-[#004e7c] to-[#0066A1] rounded-3xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden space-y-6">
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6 border-b border-white/15 pb-6">
            <div class="space-y-2 text-center md:text-right">
                <span class="text-xs font-black text-[#F58220] uppercase tracking-wider bg-white/10 px-3 py-1 rounded-full border border-white/20">
                    خدمة المستفيدين والطلاب 💬
                </span>
                <h3 class="text-2xl font-black">أرقام التواصل والتسجيل المعتمدة لدى ASOTS</h3>
                <p class="text-xs sm:text-sm text-sky-100 max-w-xl leading-relaxed">
                    فريق مكتب النجم الآسيوي جاهز للرد الفوري على استفسارات التسجيل والبرامج التدريبية ودبلومات اللغة الإنجليزية والترجمة المعتمدة.
                </p>
            </div>
            <div>
                <a href="https://whatsapp.com/channel/0029Vajnq1y7DAWxCf429S2T" target="_blank" rel="noopener noreferrer" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs px-6 py-3.5 rounded-2xl shadow-lg transition flex items-center gap-2 whitespace-nowrap">
                    <span>📢</span>
                    <span>انضم لقناة الطلاب على واتساب</span>
                </a>
            </div>
        </div>

        <!-- شبكة أرقام التواصل المتساوية والمعتمدة -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="https://wa.me/967735999101" target="_blank" rel="noopener noreferrer" class="bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl p-4 transition text-center sm:text-right group">
                <span class="text-[10px] text-[#F58220] font-black block">التسجيل والتواصل المباشر 💬</span>
                <strong class="text-sm font-black text-white font-mono block mt-1 group-hover:text-amber-300 transition" dir="ltr">+967 735 999 101</strong>
                <span class="text-[10px] text-sky-200 block mt-0.5">انقر للمحادثة الفورية عبر واتساب</span>
            </a>

            <a href="https://wa.me/967775342886" target="_blank" rel="noopener noreferrer" class="bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl p-4 transition text-center sm:text-right group">
                <span class="text-[10px] text-amber-300 font-black block">الإدارة الأكاديمية والقيادة 👨‍🏫</span>
                <strong class="text-sm font-black text-white font-mono block mt-1 group-hover:text-amber-300 transition" dir="ltr">+967 775 342 886</strong>
                <span class="text-[10px] text-sky-200 block mt-0.5">د. فخر الدين الصوفي - إدارة المكتب</span>
            </a>

            <a href="https://wa.me/967738837887" target="_blank" rel="noopener noreferrer" class="bg-white/10 hover:bg-white/20 border border-white/20 rounded-2xl p-4 transition text-center sm:text-right group">
                <span class="text-[10px] text-emerald-300 font-black block">الجودة والمتابعة وشؤون الطلاب 🛡️</span>
                <strong class="text-sm font-black text-white font-mono block mt-1 group-hover:text-amber-300 transition" dir="ltr">+967 738 837 887</strong>
                <span class="text-[10px] text-sky-200 block mt-0.5">متابعة الفصول والتقييم والشهائد</span>
            </a>
        </div>
    </div>

</div>

<script>
    function filterAnnouncements(category) {
        const items = document.querySelectorAll('.announcement-item');
        const buttons = document.querySelectorAll('.ann-tab-btn');

        buttons.forEach(btn => {
            btn.classList.remove('bg-[#0066A1]', 'bg-purple-700', 'bg-[#F58220]', 'text-white', 'shadow-sm');
            btn.classList.add('text-slate-600');
        });

        if (category === 'all') {
            document.getElementById('tab_all').classList.add('bg-[#0066A1]', 'text-white', 'shadow-sm');
            document.getElementById('tab_all').classList.remove('text-slate-600');
            items.forEach(el => el.classList.remove('hidden'));
        } else if (category === 'ad') {
            document.getElementById('tab_ad').classList.add('bg-[#F58220]', 'text-white', 'shadow-sm');
            document.getElementById('tab_ad').classList.remove('text-slate-600');
            items.forEach(el => {
                if (el.getAttribute('data-category') === 'event_gallery') {
                    el.classList.add('hidden');
                } else {
                    el.classList.remove('hidden');
                }
            });
        } else if (category === 'event_gallery') {
            document.getElementById('tab_event').classList.add('bg-purple-700', 'text-white', 'shadow-sm');
            document.getElementById('tab_event').classList.remove('text-slate-600');
            items.forEach(el => {
                if (el.getAttribute('data-category') === 'event_gallery') {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
        }
    }

    // دعم معلمة URL ?tab=events
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'events') {
            filterAnnouncements('event_gallery');
        }
    });
</script>
@endsection
