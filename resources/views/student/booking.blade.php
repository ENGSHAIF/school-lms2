@extends('layouts.app', ['title' => 'حجز الحصص التفاعلية 1-on-1'])

@section('content')
<div class="space-y-8">

    <!-- شريط الرصيد والمحفظة (ASOTS Wallet) -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
        <div>
            <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-bold border border-indigo-400/20 inline-block mb-2">
                نظام حجز المعلمين وجلسات الـ 25 دقيقة
            </span>
            <h1 class="text-2xl font-black">احجز حصتك الحية المباشرة (1-on-1 Air Class)</h1>
            <p class="text-xs text-slate-300 mt-1">اختر معلمك المفضل والموعد المناسب لتبدأ حصتك التفاعلية الخاصة بك وحدك.</p>
        </div>

        <div class="flex items-center gap-4 bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl">
            <div class="text-right">
                <span class="text-[11px] text-indigo-200 block font-bold">رصيد الحصص المتبقي</span>
                <span class="text-3xl font-black text-amber-400 font-mono">{{ $wallet->remaining_lessons }}</span>
                <span class="text-[10px] text-slate-300 mr-1">حصة (25 دقيقة)</span>
            </div>
            <a href="{{ route('packages.index') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition">
                + شراء باقة حصص
            </a>
        </div>
    </div>

    <!-- الحصص المحجوزة الحالية والقادمة -->
    @if($myBookings->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-base font-black text-slate-900 mb-4 flex items-center gap-2">
                <span>📅</span>
                <span>جدول حصصي المحجوزة الحالية</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($myBookings as $b)
                    <div class="p-5 rounded-2xl border {{ $b->status === 'completed' ? 'border-slate-200 bg-slate-50' : 'border-indigo-200 bg-indigo-50/30' }} flex flex-col justify-between gap-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-bold text-indigo-700 block">المعلم: {{ $b->teacher?->name }}</span>
                                <h3 class="font-black text-sm text-slate-900 mt-0.5">{{ $b->topic }}</h3>
                                <span class="text-xs text-slate-500 block font-mono mt-1">
                                    التاريخ: {{ $b->schedule?->session_date->format('Y-m-d') }} | الوقت: {{ $b->schedule?->start_time }} - {{ $b->schedule?->end_time }}
                                </span>
                            </div>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $b->status === 'completed' ? 'bg-slate-200 text-slate-700' : 'bg-emerald-100 text-emerald-800 animate-pulse' }}">
                                {{ $b->status === 'completed' ? 'مكتملة' : 'جاهزة للبدء' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-200/60">
                            @if($b->status === 'completed')
                                @if($b->report)
                                    <div class="text-xs text-emerald-700 font-bold">
                                        ⭐ نلت {{ $b->report->stars_awarded }} نجوم • النطق: {{ $b->report->pronunciation_score }}/5
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">الحصة منتهية</span>
                                @endif
                            @else
                                <a href="{{ route('classroom.show', $b->room_id) }}" target="_blank"
                                    class="w-full text-center bg-red-600 hover:bg-red-700 text-white font-black text-xs py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                                    <span class="w-2.5 h-2.5 bg-white rounded-full animate-ping"></span>
                                    <span>دخول الفصل التفاعلي المباشر (Air Class) 🔴</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- جدول المعلمين وخانات المواعيد المتاحة للحجز (25 دقيقة) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span>👨‍🏫</span>
                <span>المواعيد المتاحة للمعلمين اليوم (جلسات 25 دقيقة)</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">اختر الموعد الأنسب لك واضغط على زر الحجز ليتم تأكيد موعدك فوراً وخصم حصة واحدة.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($schedules as $s)
                <div class="p-5 rounded-2xl border border-slate-200 hover:border-indigo-500 hover:shadow-md transition bg-white flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 bg-indigo-100 text-indigo-700 rounded-2xl flex items-center justify-center text-xl font-bold">
                                🎓
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">{{ $s->teacher?->name }}</h3>
                                <div class="flex items-center gap-1 text-amber-500 text-xs font-bold">
                                    <span>⭐⭐⭐⭐⭐</span>
                                    <span class="text-slate-500 text-[11px]">(4.9/5) • خبير توفل ومحادثة</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs space-y-1 mb-4 font-mono">
                            <div class="flex justify-between text-slate-600">
                                <span>التاريخ:</span>
                                <strong>{{ $s->session_date->format('Y-m-d') }}</strong>
                            </div>
                            <div class="flex justify-between text-indigo-700 font-bold">
                                <span>الوقت:</span>
                                <strong>{{ $s->start_time }} - {{ $s->end_time }} (25 دقيقة)</strong>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('student.book.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="schedule_id" value="{{ $s->id }}">
                        <button type="submit" 
                            class="w-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs py-2.5 rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
                            <span>⚡</span>
                            <span>احجز هذه الحصة الآن (25 دقيقة)</span>
                        </button>
                    </form>
                </div>
            @empty
                <div class="col-span-3 text-center py-10 text-slate-400 text-xs">
                    لا توجد خانات مواعيد متاحة حالياً. سيقوم المدرسون بإضافة أوقات تفرغ جديدة قريباً.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
