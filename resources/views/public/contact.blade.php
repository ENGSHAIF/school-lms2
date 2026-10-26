@extends('layouts.app', ['title' => 'اتصل بنا - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-12 max-w-4xl mx-auto my-6">

    <!-- رأس الصفحة -->
    <div class="text-center space-y-2">
        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
            تواصل معنا • Contact Us
        </span>
        <h1 class="text-3xl font-black text-slate-900">تواصل مع مكتب النجم الآسيوي (ASOTS)</h1>
        <p class="text-xs sm:text-sm text-slate-500">نحن هنا للإجابة على استفساراتك حول الدورات، الترجمة، والاستشارات</p>
    </div>

    <!-- شبكة معلومات التواصل ونموذج المراسلة -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- بطاقة معلومات التواصل -->
        <div class="bg-slate-900 text-white rounded-3xl p-8 space-y-6">
            <div>
                <h3 class="text-lg font-black text-white mb-1">بيانات التواصل المباشر</h3>
                <span class="text-xs text-slate-400">يسعدنا تواصلكم عبر القنوات التالية:</span>
            </div>

            <div class="space-y-4 text-xs">
                <div class="flex items-start gap-3">
                    <span class="text-xl">💬</span>
                    <div>
                        <strong class="text-amber-400 block">واتساب / هاتف الإدارة:</strong>
                        <span class="font-mono text-slate-300 dir-ltr block mt-0.5" dir="ltr">{{ site_setting('whatsapp_platform_number') ?: site_setting('whatsapp_number', $info['contact']['phone_whatsapp']) }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="text-xl">✉️</span>
                    <div>
                        <strong class="text-amber-400 block">البريد الإلكتروني:</strong>
                        <span class="font-mono text-slate-300 block mt-0.5">{{ site_setting('contact_email') ?: $info['contact']['email'] }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="text-xl">📍</span>
                    <div>
                        <strong class="text-amber-400 block">المقر والخدمات:</strong>
                        <span class="text-slate-300 leading-relaxed block mt-0.5">{{ $info['contact']['address'] }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <span class="text-xl">⏰</span>
                    <div>
                        <strong class="text-amber-400 block">ساعات العمل:</strong>
                        <span class="text-slate-300 block mt-0.5">{{ $info['contact']['work_hours'] }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', site_setting('whatsapp_platform_number') ?: site_setting('whatsapp_number', $info['contact']['phone_whatsapp'])) }}" target="_blank"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-md">
                    <span>مراسلة فورية عبر الواتساب 💬</span>
                </a>
            </div>
        </div>

        <!-- نموذج إرسال استفسار أو طلب خدمة -->
        <div class="md:col-span-2 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
            <h3 class="text-lg font-black text-slate-900 mb-1">أرسل لنا رسالة أو استفساراً</h3>
            <p class="text-xs text-slate-500 mb-6">املأ البيانات وسيقوم فريق ASOTS بالرد عليك خلال ساعات العمل الرسمية.</p>

            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('شكراً لتواصلك! تم استلام رسالتك وسيتواصل معك فريق النجم الآسيوي قريباً.');" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الاسم الكامل</label>
                        <input type="text" required placeholder="مثال: أحمد محمد" 
                            class="w-full border border-slate-300 rounded-xl p-3 focus:ring-1 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">رقم هاتف الواتساب</label>
                        <input type="text" required placeholder="+967 ..." 
                            class="w-full border border-slate-300 rounded-xl p-3 focus:ring-1 focus:ring-indigo-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">نوع الخدمة المطلوبة</label>
                    <select class="w-full border border-slate-300 rounded-xl p-3 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option>التدريب اللغوي واختبارات التوفل (1-on-1)</option>
                        <option>ترجمة وثائق ومستندات معتمدة</option>
                        <option>استشارات أكاديمية ومنح جامعية</option>
                        <option>أخرى / استفسار عام</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">نص الرسالة أو تفاصيل الطلب</label>
                    <textarea rows="4" required placeholder="اكتب تفاصيل استفسارك هنا..." 
                        class="w-full border border-slate-300 rounded-xl p-3 focus:ring-1 focus:ring-indigo-500 outline-none"></textarea>
                </div>

                <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white font-black px-8 py-3 rounded-xl shadow-md transition">
                    إرسال الرسالة الآن 🚀
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
