@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 text-slate-800">

    <!-- رسائل التنبيهات والنجاح -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold">✕</button>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-lg">ℹ️</span>
                <span>{{ session('info') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-blue-600 hover:text-blue-800 font-bold">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="text-lg">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 font-bold">✕</button>
        </div>
    @endif

    <!-- 1. الهيدر العام للواتساب -->
    <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-emerald-500/20 relative overflow-hidden">
        <div class="space-y-2 z-10">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>مركز إدارة الواتساب الذكي والآلي</span>
                </span>
                @if($settings['auto_send_enabled'] && $settings['provider'] !== 'web_only' && !empty($settings['api_token']))
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white flex items-center gap-1 shadow-sm">
                        <span>⚡</span>
                        <span>البوابة السحابية مفعلة: {{ strtoupper($settings['provider']) }}</span>
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/30 text-blue-200 border border-blue-400/30 flex items-center gap-1">
                        <span>💬</span>
                        <span>نمط الإرسال الفوري بنقرة واحدة (One-Click)</span>
                    </span>
                @endif
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-white flex items-center gap-2.5">
                <span>💬</span>
                <span>لوحة تحكم ورسائل الواتساب للمنصة</span>
            </h1>

            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                تحكم كامل في رسائل الواتساب الرسمية: ربط رقم المنصة، إرسال رسائل ترحيبية أو مغادرة أو تهنئة أو إعلانات، وإرسال حملات جماعية موجهة للطلاب، المجموعات، المعلمين، أو الكل.
            </p>
        </div>

        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 text-xs space-y-2 shrink-0 z-10 w-full sm:w-auto">
            <div class="text-slate-300 font-bold">الرقم المعتمد للمنصة:</div>
            <div class="font-mono text-lg font-black text-emerald-300 flex items-center gap-2" dir="ltr">
                <span>📱</span>
                <span>{{ $settings['platform_number'] }}</span>
            </div>
            <button type="button" onclick="switchWhatsAppTab('settings')" class="w-full text-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold transition text-[11px] shadow-sm">
                ⚙️ تعديل وربط الرقم والبوابة
            </button>
        </div>
    </div>

    <!-- 1.5 بطاقة المفتاح العام للواتساب (Master Switch Banner & One-Click Toggle) -->
    <div class="p-5 rounded-3xl border shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 transition {{ $settings['master_switch'] ? 'bg-emerald-50/80 border-emerald-300' : 'bg-rose-50 border-rose-300' }}">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 {{ $settings['master_switch'] ? 'bg-emerald-600 text-white shadow-emerald-200 shadow-md animate-pulse' : 'bg-rose-600 text-white shadow-rose-200 shadow-md' }}">
                {{ $settings['master_switch'] ? '🟢' : '🛑' }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-black {{ $settings['master_switch'] ? 'text-emerald-950' : 'text-rose-950' }}">
                        {{ $settings['master_switch'] ? 'نظام الواتساب التلقائي مُفعل وشغّال كلياً (Master Switch ON)' : 'نظام الواتساب التلقائي مُعطل ومتوقف بالكامل (Master Switch OFF)' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $settings['master_switch'] ? 'bg-emerald-200 text-emerald-800' : 'bg-rose-200 text-rose-800' }}">
                        {{ $settings['master_switch'] ? 'شغال ونشط ⚡' : 'إيقاف شامل ✕' }}
                    </span>
                </div>
                <p class="text-xs mt-1 leading-relaxed {{ $settings['master_switch'] ? 'text-emerald-800' : 'text-rose-800' }}">
                    @if($settings['master_switch'])
                        الرسائل التلقائية (سندات القبض، الصرف، المستحقات، الترحيب) ترسل تلقائياً وفق مصفوفة العمليات واستثناءات المستخدمين.
                    @else
                        تم إيقاف كافة الرسائل التلقائية بنقرة واحدة. لن يتم إرسال أي إشعار واتساب تلقائي من النظام حتى تقوم بإعادة تشغيله.
                    @endif
                </p>
            </div>
        </div>

        <form action="{{ route('admin.whatsapp.master.toggle') }}" method="POST" onsubmit="return confirm('{{ $settings['master_switch'] ? 'هل أنت متأكد من إيقاف نظام الواتساب كلياً؟ سيتوقف إرسال كل الرسائل التلقائية.' : 'هل أنت متأكد من تفعيل وتشغيل نظام الواتساب كلياً؟' }}');" class="shrink-0 w-full sm:w-auto">
            @csrf
            @if($settings['master_switch'])
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2">
                    <span>🛑</span>
                    <span>إيقاف الواتساب بالكامل</span>
                </button>
            @else
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2">
                    <span>🟢</span>
                    <span>تشغيل وتفعيل الواتساب الآن</span>
                </button>
            @endif
        </form>
    </div>

    <!-- 2. بطاقات الإحصاءات العامة -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                📨
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">إجمالي الرسائل بالسجل</span>
                <span class="text-xl font-black text-slate-800 font-mono">{{ number_format($stats['total']) }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg shrink-0">
                🚀
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">مرسلة آلياً بالبوابة</span>
                <span class="text-xl font-black text-teal-600 font-mono">{{ number_format($stats['sent']) }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0">
                ⚡
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">موثقة بنقرة واحدة</span>
                <span class="text-xl font-black text-indigo-600 font-mono">{{ number_format($stats['logged']) }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
                🚫
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block" title="مستبعدة وفق شروط الصلاحية أو استثناء المستخدم أو تعطيل العملية">مستبعدة آلياً</span>
                <span class="text-xl font-black text-rose-600 font-mono">{{ number_format($stats['suppressed']) }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3 col-span-2 lg:col-span-1">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                📅
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">رسائل اليوم</span>
                <span class="text-xl font-black text-amber-600 font-mono">{{ number_format($stats['today']) }}</span>
            </div>
        </div>
    </div>

    <!-- 3. حاوية التبويبات الرئيسية -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">

        <!-- أزرار التبويبات -->
        <div class="flex items-center justify-between border-b border-slate-200 pb-4 flex-wrap gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="switchWhatsAppTab('campaign')" id="tabBtnCampaign" class="px-4 sm:px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-emerald-600 text-white shadow-sm">
                    <span>🚀</span>
                    <span>إرسال وحملات الواتساب الموجهة</span>
                </button>
                <button type="button" onclick="switchWhatsAppTab('settings')" id="tabBtnSettings" class="px-4 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <span>⚙️</span>
                    <span>ربط الرقم ومصفوفة العمليات</span>
                </button>
                <button type="button" onclick="switchWhatsAppTab('users')" id="tabBtnUsers" class="px-4 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <span>👥</span>
                    <span>التحكم بالمستخدمين والاستثناءات</span>
                    @if($userNotificationStats['excluded'] > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-500 text-white font-black" title="{{ $userNotificationStats['excluded'] }} مستخدم مستثنى">{{ $userNotificationStats['excluded'] }}</span>
                    @endif
                </button>
                <button type="button" onclick="switchWhatsAppTab('templates')" id="tabBtnTemplates" class="px-4 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <span>📝</span>
                    <span>قوالب الرسائل التلقائية الذكية</span>
                </button>
                <button type="button" onclick="switchWhatsAppTab('logs')" id="tabBtnLogs" class="px-4 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <span>📜</span>
                    <span>سجل وأرشيف الرسائل الصادرة</span>
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- التبويب 1: إرسال وحملات الواتساب الموجهة -->
        <!-- ========================================== -->
        <div id="tabContentCampaign" class="space-y-6">
            <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-200 text-xs text-emerald-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <span class="font-black text-sm block mb-1">🎯 مركز إرسال الرسائل المخصصة والحملات الجماعية:</span>
                    يمكنك إرسال إعلانات، تهاني بالنجاح، ترحيب، أو تنبيهات هامة لكافة المسجلين، أو تحديد طلاب مجموعة أو دورة أو معلمين بدقة.
                </div>
            </div>

            <form action="{{ route('admin.whatsapp.campaign.send') }}" method="POST" class="space-y-5" onsubmit="return confirm('هل أنت متأكد من بدء إرسال هذه الرسالة للمستهدفين المحددين؟');">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                    
                    <!-- 1. تحديد الفئة المستهدفة -->
                    <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <label class="block font-black text-slate-800 text-sm flex items-center gap-1.5">
                            <span>👥</span>
                            <span>الفئة المستهدفة بالرسالة <span class="text-rose-500">*</span></span>
                        </label>

                        <select name="target" id="campaignTargetSelect" onchange="toggleCampaignTargetOptions()" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                            <option value="all_users">👥 جميع المسجلين بالمنصة (الكل: {{ $audienceCounts['all_users'] }} مستخدم لديه هاتف)</option>
                            <option value="all_students">🎓 جميع الطلاب المسجلين بالمنصة ({{ $audienceCounts['all_students'] }} طالب)</option>
                            <option value="teachers_and_supervisors">👨‍🏫 جميع المعلمين والمشرفين الأكاديميين ({{ $audienceCounts['teachers_and_supervisors'] }} كادر)</option>
                            <option value="parents">👨‍👧‍👦 أولياء الأمور المسجلين ({{ $audienceCounts['parents'] }} ولي أمر)</option>
                            <option value="specific_group">📚 طلاب مجموعة دراسية محددة</option>
                            <option value="specific_course">🎯 طلاب مسجلين في دورة أو دبلوم محدد</option>
                            <option value="custom_user">👤 مستخدم أو طالب محدد بالاسم</option>
                            <option value="custom_phone">📱 إرسال لرقم هاتف مخصص</option>
                        </select>

                        <!-- خيار: اختيار المجموعة المحددة -->
                        <div id="targetGroupWrapper" class="hidden space-y-1 pt-2">
                            <label class="block font-bold text-slate-700">اختر المجموعة الدراسية:</label>
                            <select name="study_group_id" id="campaignGroupId" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 font-bold bg-white outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">-- اختر المجموعة --</option>
                                @foreach($studyGroups as $grp)
                                    <option value="{{ $grp->id }}">
                                        {{ $grp->name }} (دورة: {{ $grp->course?->title ?? 'عام' }}) - {{ $grp->students->count() }} طالب
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- خيار: اختيار الدورة المحددة -->
                        <div id="targetCourseWrapper" class="hidden space-y-1 pt-2">
                            <label class="block font-bold text-slate-700">اختر الدورة أو المنهج:</label>
                            <select name="course_id" id="campaignCourseId" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 font-bold bg-white outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">-- اختر الدورة التعليمية --</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->title }} ({{ $c->level?->title ?? 'منهج' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- خيار: اختيار مستخدم بالاسم -->
                        <div id="targetUserWrapper" class="hidden space-y-1 pt-2">
                            <label class="block font-bold text-slate-700">اختر المستخدم / المستفيد:</label>
                            <select name="custom_user_id" id="campaignUserId" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 font-bold bg-white outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">-- ابحث واختر المستخدم --</option>
                                @foreach($usersList as $u)
                                    <option value="{{ $u->id }}">
                                        {{ $u->name }} [{{ $u->role }}] - ({{ $u->phone_whatsapp }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- خيار: إدخال رقم هاتف مخصص -->
                        <div id="targetPhoneWrapper" class="hidden space-y-1 pt-2">
                            <label class="block font-bold text-slate-700">أدخل رقم الهاتف:</label>
                            <input type="text" name="custom_phone" id="campaignCustomPhone" placeholder="مثال: 777823720 أو 967777823720" class="w-full border border-slate-300 rounded-xl px-3.5 py-2 font-mono outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        </div>
                    </div>

                    <!-- 2. نوع الرسالة واختيار قالب جاهز -->
                    <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <label class="block font-black text-slate-800 text-sm flex items-center gap-1.5">
                            <span>🏷️</span>
                            <span>تصنيف الرسالة وقالب التعبئة السريعة <span class="text-rose-500">*</span></span>
                        </label>

                        <div>
                            <select name="message_type" id="campaignMessageType" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                                <option value="announcement">📢 إعلان عام وتنبيه دراسي</option>
                                <option value="congratulations">🎉 تهنئة بالنجاح والتميز والشهادات</option>
                                <option value="welcome">👋 رسالة ترحيبية بالمنصة والدورة</option>
                                <option value="completion">🎓 استكمال الدورة واجتياز البرنامج</option>
                                <option value="reminder">⏰ تذكير بسداد الأقساط أو المواعيد</option>
                                <option value="alert">⚠️ تنبيه إداري هام</option>
                            </select>
                        </div>

                        <div class="pt-2">
                            <label class="block font-bold text-slate-600 mb-1">⚡ نسخ من قالب جاهز بنقرة واحدة:</label>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @foreach($templates as $key => $tpl)
                                    <button type="button" onclick="loadTemplateToCampaign('{{ $key }}')" class="px-2.5 py-1 bg-white hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 rounded-lg text-[11px] font-bold transition">
                                        {{ $tpl['title'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-200 text-[11px] text-emerald-900 leading-relaxed">
                            💡 <strong>المتغيرات الديناميكية المدعومة:</strong><br>
                            اكتب <code class="font-bold text-emerald-700">{name}</code> وسيتم استبدالها باسم كل طالب آلياً، و <code class="font-bold text-emerald-700">{school_name}</code> لاسم المنصة، و <code class="font-bold text-emerald-700">{link}</code> لرابط الموقع.
                        </div>
                    </div>

                </div>

                <!-- 3. نص الرسالة ومعاينتها -->
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <label class="block font-black text-slate-800 text-sm">نص رسالة الواتساب <span class="text-rose-500">*</span></label>
                        <span id="charCountLabel" class="text-slate-400 font-mono text-[11px]">0 حرف</span>
                    </div>

                    <textarea name="message" id="campaignMessageText" rows="6" required oninput="updateCharCount()" placeholder="اكتب نص رسالة الواتساب هنا... أو اختر أحد القوالب الجاهزة أعلاه" class="w-full border border-slate-300 rounded-2xl p-4 text-xs font-sans leading-relaxed focus:ring-2 focus:ring-emerald-500 outline-none bg-white shadow-inner"></textarea>
                </div>

                <!-- زر الإرسال -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-3 border-t border-slate-100">
                    <div class="text-xs text-slate-500 flex items-center gap-2">
                        <span>⚡</span>
                        <span>سيتم إرسال الرسالة آلياً في حال ربط البوابة، أو توليد روابط إرسال فورية وتوثيقها في سجل الرسائل.</span>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-lg hover:shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                        <span>🚀</span>
                        <span>إطلاق وإرسال حملة الواتساب الآن</span>
                    </button>
                </div>

            </form>
        </div>

        <!-- ========================================== -->
        <!-- ========================================== -->
        <!-- التبويب 2: ربط رقم المنصة ومصفوفة العمليات -->
        <!-- ========================================== -->
        <div id="tabContentSettings" class="space-y-6 hidden">
            <div class="bg-blue-50/70 p-4 rounded-2xl border border-blue-200 text-xs text-blue-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <span class="font-black text-sm block mb-1">⚙️ ضبط وتخصيص إعدادات ومصفوفة عمليات الواتساب:</span>
                    تحكم شامل في تشغيل/إيقاف الواتس، فترة صلاحية العمليات لمنع إرسال العمليات السابقة بعد التوقف، وتحديد أي عمليات ترسل إشعارات فورية عبر الواتساب.
                </div>
            </div>

            <form action="{{ route('admin.whatsapp.settings.update') }}" method="POST" class="space-y-6 text-xs">
                @csrf

                <!-- 1. المفتاح العام وفترة الصلاحية (Anti-Flood & Master Switch) -->
                <div class="p-5 bg-gradient-to-r from-slate-50 to-emerald-50/40 rounded-2xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="font-black text-slate-900 text-sm flex items-center gap-2">
                            <span>🛡️</span>
                            <span>التحكم الشامل وصلاحية العمليات (منع التكرار بعد التوقف):</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- تفعيل أو إيقاف الواتساب كلياً -->
                        <label class="flex items-start gap-3 p-3.5 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-400 transition shadow-xs">
                            <input type="checkbox" name="whatsapp_master_switch" value="1" {{ $settings['master_switch'] ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 rounded mt-0.5">
                            <div>
                                <span class="font-black text-slate-900 block text-xs">المفتاح الرئيسي للواتساب (Master Switch)</span>
                                <span class="text-[11px] text-slate-500 block mt-0.5">عند تعطيل هذا الخيار، يتوقف النظام فوراً عن إرسال أي رسائل تلقائية لكافة العمليات والمستخدمين.</span>
                            </div>
                        </label>

                        <!-- أقصى مدة مسموحة للعمليات (ساعات) -->
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 space-y-1 shadow-xs">
                            <label class="block font-black text-slate-900 text-xs flex items-center justify-between">
                                <span>⏱️ أقصى فترة صلاحية للعملية:</span>
                                <span class="text-[10px] text-emerald-600 font-bold">حماية ضد الإغراق (Anti-Flood)</span>
                            </label>
                            <select name="whatsapp_max_age_hours" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 font-bold text-xs bg-slate-50 outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="0" {{ $settings['max_age_hours'] == 0 ? 'selected' : '' }}>بلا حد زمني (إرسال كل العمليات مهما كان تاريخها)</option>
                                <option value="1" {{ $settings['max_age_hours'] == 1 ? 'selected' : '' }}>ساعة واحدة فقط (أقصى صرامة)</option>
                                <option value="6" {{ $settings['max_age_hours'] == 6 ? 'selected' : '' }}>6 ساعات</option>
                                <option value="12" {{ $settings['max_age_hours'] == 12 ? 'selected' : '' }}>12 ساعة</option>
                                <option value="24" {{ $settings['max_age_hours'] == 24 ? 'selected' : '' }}>24 ساعة (يوم واحد - افتراضي وموصى به)</option>
                                <option value="48" {{ $settings['max_age_hours'] == 48 ? 'selected' : '' }}>48 ساعة (يومان)</option>
                                <option value="72" {{ $settings['max_age_hours'] == 72 ? 'selected' : '' }}>72 ساعة (ثلاثة أيام)</option>
                                <option value="168" {{ $settings['max_age_hours'] == 168 ? 'selected' : '' }}>أسبوع كامل (7 أيام)</option>
                            </select>
                            <span class="text-[10px] text-slate-400 block">إذا توقف الواتس عدة أيام ثم أعدت تشغيله، لن يرسل النظام العمليات القديمة التي تجاوزت هذه المدة لمنع إزعاج المستخدمين.</span>
                        </div>
                    </div>
                </div>

                <!-- 2. مصفوفة العمليات التشغيلية التلقائية المستقلة -->
                <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="font-black text-slate-900 text-sm flex items-center gap-2">
                            <span>📋</span>
                            <span>مصفوفة العمليات التلقائية (تفعيل أو إلغاء كل عملية على حدة):</span>
                        </span>
                        <span class="text-[11px] text-slate-500 font-bold">يمكنك تحديد أي نوع من العمليات المالية والأكاديمية يرسل إشعار واتساب</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- سندات القبض -->
                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-300 transition shadow-xs">
                            <input type="checkbox" name="whatsapp_notify_receipt" value="1" {{ $settings['notify_receipt'] ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded mt-0.5">
                            <div>
                                <span class="font-black text-slate-800 block text-xs flex items-center gap-1">
                                    <span>💳</span>
                                    <span>سندات القبض والرسوم</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">إشعار ورابط سند القبض للطلاب عند استلام رسوم</span>
                            </div>
                        </label>

                        <!-- سندات الصرف -->
                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-300 transition shadow-xs">
                            <input type="checkbox" name="whatsapp_notify_payment" value="1" {{ $settings['notify_payment'] ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded mt-0.5">
                            <div>
                                <span class="font-black text-slate-800 block text-xs flex items-center gap-1">
                                    <span>💼</span>
                                    <span>سندات الصرف المالي</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">إشعار وسند الصرف للمستفيدين والمعلمين</span>
                            </div>
                        </label>

                        <!-- قيود ومستحقات المعلمين -->
                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-300 transition shadow-xs">
                            <input type="checkbox" name="whatsapp_notify_entitlement" value="1" {{ $settings['notify_entitlement'] ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded mt-0.5">
                            <div>
                                <span class="font-black text-slate-800 block text-xs flex items-center gap-1">
                                    <span>📊</span>
                                    <span>القيود وإثبات المستحقات</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">إشعار المعلم بقيد مستحقات تدريس أو دورات بحسابه</span>
                            </div>
                        </label>

                        <!-- نتائج الاختبارات والشهائد -->
                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-300 transition shadow-xs">
                            <input type="checkbox" name="whatsapp_notify_exam_result" value="1" {{ $settings['notify_exam_result'] ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded mt-0.5">
                            <div>
                                <span class="font-black text-slate-800 block text-xs flex items-center gap-1">
                                    <span>🎯</span>
                                    <span>نتائج الاختبارات والشهائد</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">إرسال النتيجة ورابط الشهادة فور اعتماد الاختبار</span>
                            </div>
                        </label>

                        <!-- تقييم الحصص والدروس -->
                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-300 transition shadow-xs">
                            <input type="checkbox" name="whatsapp_notify_evaluation" value="1" {{ $settings['notify_evaluation'] ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded mt-0.5">
                            <div>
                                <span class="font-black text-slate-800 block text-xs flex items-center gap-1">
                                    <span>⭐</span>
                                    <span>تقييمات الحصص والدروس</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">إشعار المعلم والطالب بملخص تقييم الحصة التعليمية</span>
                            </div>
                        </label>

                        <!-- إتمام الدورة والتخرج -->
                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-300 transition shadow-xs">
                            <input type="checkbox" name="whatsapp_notify_completion" value="1" {{ $settings['notify_completion'] ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded mt-0.5">
                            <div>
                                <span class="font-black text-slate-800 block text-xs flex items-center gap-1">
                                    <span>🎓</span>
                                    <span>إتمام الدورة والتخرج</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">إشعار تهنئة بإكمال المستوى أو الدبلوم بالكامل</span>
                            </div>
                        </label>

                        <!-- الترحيب بالمسجلين الجدد -->
                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-300 transition shadow-xs">
                            <input type="checkbox" name="whatsapp_notify_welcome" value="1" {{ $settings['notify_welcome'] ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded mt-0.5">
                            <div>
                                <span class="font-black text-slate-800 block text-xs flex items-center gap-1">
                                    <span>👋</span>
                                    <span>الترحيب بالمسجلين الجدد</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">إرسال رسالة ترحيبية فور تسجيل أي طالب جديد</span>
                            </div>
                        </label>

                        <!-- تذكير بالأقساط والمستحقات -->
                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-300 transition shadow-xs">
                            <input type="checkbox" name="whatsapp_notify_reminder" value="1" {{ $settings['notify_reminder'] ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded mt-0.5">
                            <div>
                                <span class="font-black text-slate-800 block text-xs flex items-center gap-1">
                                    <span>⏰</span>
                                    <span>تذكير بالأقساط والمواعيد</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">تذكير الطلاب بالأقساط المستحقة بموعدها</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 3. بيانات الربط ورقم المنصة والمزود السحابي -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- رقم الواتساب الرسمي -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">رقم هاتف المنصة الرسمي للواتساب <span class="text-rose-500">*</span></label>
                        <input type="text" name="whatsapp_platform_number" value="{{ old('whatsapp_platform_number', $settings['platform_number']) }}" required placeholder="مثال: +967 735 999 101" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-mono font-bold focus:ring-2 focus:ring-emerald-500 outline-none" dir="ltr">
                        <span class="text-[10px] text-slate-400 block">هذا الرقم سيظهر للمستخدمين والطلاب كقناة التواصل والدعم الرسمية للمنصة.</span>
                    </div>

                    <!-- مزود البوابة السحابية -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">مزود بوابة الواتساب السحابية <span class="text-rose-500">*</span></label>
                        <select name="whatsapp_gateway_provider" id="gatewayProviderSelect" onchange="toggleGatewayFields()" required class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                            <option value="web_only" {{ $settings['provider'] === 'web_only' ? 'selected' : '' }}>💬 وضع الإرسال الفوري المجاني الذكي بالمتصفح (One-Click Free - بدون أي اشتراك أو خوادم)</option>
                            <option value="greenapi" {{ $settings['provider'] === 'greenapi' ? 'selected' : '' }}>🟢 GreenAPI Gateway (سحابي مجاني ودائم - Free Developer Tier بدون تثبيت أي برامج)</option>
                            <option value="local_gateway" {{ $settings['provider'] === 'local_gateway' ? 'selected' : '' }}>💻 خادم البوابة المحلي المجاني 100% (Node.js Baileys Bridge - دائم ومجاني مدى الحياة)</option>
                            <option value="ultramsg" {{ $settings['provider'] === 'ultramsg' ? 'selected' : '' }}>⚡ UltraMsg Gateway (الأسرع والأسهل - برمز QR)</option>
                            <option value="wasender" {{ $settings['provider'] === 'wasender' ? 'selected' : '' }}>🚀 Wasender API</option>
                            <option value="twilio" {{ $settings['provider'] === 'twilio' ? 'selected' : '' }}>🏢 Twilio WhatsApp Business API</option>
                            <option value="custom" {{ $settings['provider'] === 'custom' ? 'selected' : '' }}>⚙️ مخصص (Custom Webhook / REST API)</option>
                        </select>
                    </div>

                    <!-- معرف الجلسة / الجهاز (Instance ID) -->
                    <div id="instanceIdWrapper" class="space-y-1">
                        <label class="block font-bold text-slate-700">معرف الجهاز أو الجلسة (Instance ID)</label>
                        <input type="text" name="whatsapp_instance_id" value="{{ old('whatsapp_instance_id', $settings['instance_id']) }}" placeholder="مثال: instance99482" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-mono focus:ring-2 focus:ring-emerald-500 outline-none" dir="ltr">
                        <span class="text-[10px] text-slate-400 block">يُمنح لك في حسابك لدى مزود البوابة بعد مسح رمز الـ QR بهاتفك.</span>
                    </div>

                    <!-- رمز الـ API Token -->
                    <div id="apiTokenWrapper" class="space-y-1">
                        <label class="block font-bold text-slate-700">رمز المرور والتوثيق (API Token / Secret Key)</label>
                        <input type="password" name="whatsapp_api_token" value="{{ old('whatsapp_api_token', $settings['api_token']) }}" placeholder="••••••••••••••••" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-mono focus:ring-2 focus:ring-emerald-500 outline-none" dir="ltr">
                        <span class="text-[10px] text-slate-400 block">الرمز السري المعتمد في حساب البوابة لتأمين الإرسال.</span>
                    </div>

                    <!-- رابط الـ API المخصص (Custom API URL) -->
                    <div id="apiUrlWrapper" class="space-y-1 md:col-span-2">
                        <label class="block font-bold text-slate-700">رابط البوابة API Endpoint (اختياري - في حال استخدام مزود خاص)</label>
                        <input type="url" name="whatsapp_api_url" value="{{ old('whatsapp_api_url', $settings['api_url']) }}" placeholder="https://api.ultramsg.com/instanceXXXXX/messages/chat" class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 font-mono focus:ring-2 focus:ring-emerald-500 outline-none" dir="ltr">
                    </div>

                    <!-- بطاقة ربط الباركود QR للخادم المحلي المجاني -->
                    <div id="localGatewayQrCard" class="md:col-span-2 p-5 bg-gradient-to-br from-slate-900 to-teal-950 text-white rounded-2xl border-2 border-emerald-500/50 shadow-lg space-y-4">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-emerald-800/60 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="text-2xl">📲</span>
                                <div>
                                    <h4 class="font-black text-sm text-emerald-300">ربط الواتساب بهاتفك عبر الباركود (QR Code Live Link)</h4>
                                    <p class="text-[11px] text-slate-300">امسح الباركود بهاتفك من تطبيق واتساب (الأجهزة المرتبطة) لتفعيل الإرسال الآلي التلقائي مدى الحياة مجاناً.</p>
                                </div>
                            </div>
                            <div id="gatewayConnectionBadge" class="px-3 py-1.5 rounded-full text-xs font-black bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                                <span id="gatewayConnectionText">جاري الاتصال بالخادم المحلي...</span>
                            </div>
                        </div>

                        <!-- حالة بانتظار المسح -->
                        <div id="gatewayQrPendingSection" class="flex flex-col md:flex-row items-center justify-between gap-6 py-2">
                            <div class="space-y-3 max-w-sm text-xs">
                                <div class="font-bold text-slate-200">📌 خطوات الربط بثوانٍ:</div>
                                <ol class="list-decimal list-inside space-y-1.5 text-slate-300 text-[11px] leading-relaxed">
                                    <li>افتح تطبيق <strong>WhatsApp</strong> على هاتفك المحمول.</li>
                                    <li>اضغط على <strong>القائمة (⋮)</strong> بالأعلى أو <strong>الإعدادات</strong> بالأسفل.</li>
                                    <li>اختر <strong>الأجهزة المرتبطة (Linked Devices)</strong>.</li>
                                    <li>اضغط على <strong>ربط جهاز (Link a Device)</strong> ووجّه الكاميرا نحو الباركود المقابل.</li>
                                </ol>
                                <div class="pt-2 flex items-center gap-2 flex-wrap">
                                    <button type="button" onclick="refreshQrCode()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg transition text-[11px] border border-slate-700 flex items-center gap-1">
                                        <span>🔄</span>
                                        <span>تحديث الباركود</span>
                                    </button>
                                    <a href="http://localhost:3000" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition text-[11px] flex items-center gap-1">
                                        <span>🔗</span>
                                        <span>فتح نافذة الباركود المستقلة (3000)</span>
                                    </a>
                                </div>
                            </div>

                            <div class="p-3 bg-white rounded-2xl shadow-xl border-4 border-emerald-400 flex flex-col items-center justify-center min-w-[220px] min-h-[220px]">
                                <div id="gatewayQrImgWrapper">
                                    <div class="text-slate-500 text-[11px] font-bold text-center py-16 animate-pulse">
                                        جاري توليد الباركود...<br>ثوانٍ معدودة
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-500 mt-2 font-bold">يتجدد الباركود تلقائياً لحمايتك</span>
                            </div>
                        </div>

                        <!-- حالة متصل بالفعل -->
                        <div id="gatewayQrConnectedSection" class="hidden text-center py-6 space-y-3">
                            <div class="text-5xl">🎉</div>
                            <h4 class="text-base font-black text-emerald-400">واتساب متصل وجاهز للإرسال الآلي التلقائي!</h4>
                            <p class="text-xs text-slate-300 max-w-md mx-auto">
                                رقم الهاتف المرتبط: <strong id="gatewayConnectedPhoneDisplay" class="font-mono text-emerald-300 text-sm dir-ltr"></strong>
                                <br>
                                يتم الآن إرسال جميع السندات والإشعارات فورياً وبشكل تلقائي في الخلفية بدون الحاجة لأي نقر.
                            </p>
                            <button type="button" onclick="disconnectLocalGateway()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                                🛑 قطع الارتباط / ربط رقم هاتف آخر
                            </button>
                        </div>
                    </div>

                </div>

                <!-- 4. خيار الإرسال الآلي الصامت بالخلفية -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <label class="flex items-start gap-2.5 p-3 rounded-xl bg-white border border-slate-200 cursor-pointer hover:border-emerald-300 transition">
                        <input type="checkbox" name="whatsapp_auto_send_enabled" value="1" {{ $settings['auto_send_enabled'] ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded mt-0.5">
                        <div>
                            <span class="font-bold text-slate-900 block">إرسال آلي صامت بالخلفية عبر البوابة السحابية</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">إرسال الرسائل تلقائياً عبر مزود البوابة دون الحاجة للنقر على زر الواتساب أو فتح نافذة ويب</span>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <span>💾</span>
                        <span>حفظ إعدادات ومصفوفة الواتساب</span>
                    </button>
                </div>
            </form>

            <!-- فحص الاتصال وإرسال رسالة تجريبية -->
            <div class="border-t border-slate-200 pt-6">
                <div class="bg-amber-50/70 p-5 rounded-2xl border border-amber-200 text-xs space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">🔔</span>
                        <span class="font-black text-amber-950 text-sm">اختبار ربط الواتساب وإرسال رسالة تجريبية فورية:</span>
                    </div>
                    <p class="text-amber-900 text-xs leading-relaxed">
                        أدخل رقم هاتفك لتجربة إرسال رسالة فحص الاتصال للتأكد من وصول الرسائل لهاتفك بالصيغة الدولية المعتمدة.
                    </p>

                    <form action="{{ route('admin.whatsapp.test') }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                        @csrf
                        <input type="text" name="test_phone" required placeholder="مثال: 777823720 أو 967777823720" class="w-full sm:w-72 border border-amber-300 rounded-xl px-3.5 py-2.5 font-mono text-xs focus:ring-2 focus:ring-amber-500 outline-none bg-white">
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5 shrink-0">
                            <span>📱</span>
                            <span>إرسال رسالة تجريبية الآن</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- دليل الربط بخطوات مبسطة -->
            <!-- دليل التفعيل المجاني والدائم بالكامل -->
            <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-slate-50 p-6 rounded-2xl border border-emerald-200 text-xs space-y-4">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🆓</span>
                    <span class="font-black text-emerald-950 text-sm">كيفية تفعيل إرسال الواتساب الذكي التلقائي مجاناً وبشكل دائم:</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- الخيار الأول: Green-API المجاني الدائم -->
                    <div class="bg-white p-4 rounded-xl border border-emerald-200/80 shadow-xs space-y-2">
                        <span class="font-black text-emerald-800 text-xs flex items-center gap-1.5">
                            <span>🟢</span>
                            <span>الخيار 1: Green-API المجاني الدائم (Free Developer Tier)</span>
                        </span>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            أسهل خيار سحابي يعمل 24/7 دون الحاجة لتشغيل أي برامج على جهازك:
                        </p>
                        <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-600">
                            <li>سجّل حساباً مجانياً على <a href="https://green-api.com" target="_blank" class="text-emerald-700 font-bold underline">Green-API.com</a> (اختر Developer Free Plan).</li>
                            <li>امسح رمز الـ <strong>QR Code</strong> بهاتفك من لوحة Green-API (الأجهزة المرتبطة).</li>
                            <li>انسخ <strong>idInstance</strong> و <strong>apiTokenInstance</strong> وضعهما في الحقول أعلاه.</li>
                            <li>اختر المزود <strong>GreenAPI</strong> وفعّل <strong>(إرسال آلي صامت بالخلفية)</strong> ثم اضغط حفظ.</li>
                        </ol>
                    </div>

                    <!-- الخيار الثاني: البوابة المحلية المجانية بالكامل -->
                    <div class="bg-white p-4 rounded-xl border border-teal-200/80 shadow-xs space-y-2">
                        <span class="font-black text-teal-800 text-xs flex items-center gap-1.5">
                            <span>💻</span>
                            <span>الخيار 2: خادم البوابة المحلي (مجاني 100% مدى الحياة)</span>
                        </span>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            خادم مفتوح المصدر يعمل على جهازك مباشرة عبر Node.js بدون أي حدود أو اشتراكات:
                        </p>
                        <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-600">
                            <li>افتح المجلد <code>whatsapp-gateway</code> وشغّل ملف <code>start.bat</code>.</li>
                            <li>امسح رمز الـ <strong>QR</strong> الذي سيظهر لك في المتصفح أو الشاشة.</li>
                            <li>في الحقول أعلاه اختر <strong>(خادم البوابة المحلي المجاني)</strong>.</li>
                            <li>فعّل <strong>(إرسال آلي صامت بالخلفية)</strong> وسيرسل النظام تلقائياً عبر جهازك مباشرة.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- التبويب 3: التحكم بالمستخدمين والاستثناءات -->
        <!-- ========================================== -->
        <div id="tabContentUsers" class="space-y-6 hidden">
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-5 rounded-2xl border border-indigo-500/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="font-black text-sm text-indigo-300 block flex items-center gap-2">
                        <span>👥</span>
                        <span>مصفوفة التحكم بالمستخدمين واستثناءات الإرسال التلقائي:</span>
                    </span>
                    <p class="text-xs text-slate-300 leading-relaxed max-w-2xl">
                        تحكم بدقة في المستخدمين الذين ترتسل لهم العمليات التلقائية (سندات، قيود، نتائج، ترحيب) ومن يتم استثناؤه وحظره. يمكنك تفعيل أو استثناء أي مستخدم بنقرة واحدة أو تطبيق إجراء جماعي.
                    </p>
                </div>

                <!-- أزرار التبديل الجماعي -->
                <div class="flex items-center gap-2 flex-wrap">
                    <form action="{{ route('admin.whatsapp.users.bulk_toggle') }}" method="POST" onsubmit="return confirm('تفعيل إشعارات الواتساب التلقائية لجميع المستخدمين؟');">
                        @csrf
                        <input type="hidden" name="bulk_action" value="enable_all">
                        <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold rounded-xl transition shadow-xs flex items-center gap-1">
                            <span>✓</span>
                            <span>تفعيل للكل</span>
                        </button>
                    </form>

                    <form action="{{ route('admin.whatsapp.users.bulk_toggle') }}" method="POST" onsubmit="return confirm('استثناء وحظر إشعارات الواتساب عن جميع المستخدمين؟');">
                        @csrf
                        <input type="hidden" name="bulk_action" value="exclude_all">
                        <button type="submit" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-[11px] font-bold rounded-xl transition shadow-xs flex items-center gap-1">
                            <span>✕</span>
                            <span>استثناء وحظر الكل</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- إحصاءات المستخدمين السريعة -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-slate-500 block text-[11px]">إجمالي المسجلين</span>
                        <span class="font-black text-slate-800 text-base font-mono">{{ number_format($userNotificationStats['total']) }}</span>
                    </div>
                    <span class="text-xl">👥</span>
                </div>

                <div class="p-3.5 bg-blue-50/60 border border-blue-200 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-blue-700 block text-[11px]">مسجل بهاتف واتساب</span>
                        <span class="font-black text-blue-900 text-base font-mono">{{ number_format($userNotificationStats['with_phone']) }}</span>
                    </div>
                    <span class="text-xl">📱</span>
                </div>

                <div class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-emerald-700 block text-[11px]">مفعل للإشعارات التلقائية</span>
                        <span class="font-black text-emerald-800 text-base font-mono">{{ number_format($userNotificationStats['enabled']) }}</span>
                    </div>
                    <span class="text-xl">🟢</span>
                </div>

                <div class="p-3.5 bg-rose-50/70 border border-rose-200 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-rose-700 block text-[11px]">مستثنى ومحظور</span>
                        <span class="font-black text-rose-800 text-base font-mono">{{ number_format($userNotificationStats['excluded']) }}</span>
                    </div>
                    <span class="text-xl">🔴</span>
                </div>
            </div>

            <!-- فلترة وبحث المستخدمين -->
            <form action="{{ route('admin.whatsapp.index') }}" method="GET" class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                <input type="hidden" name="tab" value="users">

                <div class="w-full md:w-auto flex-1 flex flex-col sm:flex-row items-center gap-2">
                    <input type="text" name="user_search" value="{{ request('user_search') }}" placeholder="ابحث بالاسم، الهاتف، أو البريد الإلكتروني..." class="w-full sm:w-72 border border-slate-300 rounded-xl px-3.5 py-2 font-bold focus:ring-2 focus:ring-emerald-500 outline-none bg-white">

                    <select name="user_role" class="w-full sm:w-auto border border-slate-300 rounded-xl px-3 py-2 font-bold focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="">-- جميع الأدوار --</option>
                        <option value="student" {{ request('user_role') === 'student' ? 'selected' : '' }}>طلاب (Students)</option>
                        <option value="teacher" {{ request('user_role') === 'teacher' ? 'selected' : '' }}>معلمين (Teachers)</option>
                        <option value="supervisor" {{ request('user_role') === 'supervisor' ? 'selected' : '' }}>مشرفين (Supervisors)</option>
                        <option value="parent" {{ request('user_role') === 'parent' ? 'selected' : '' }}>أولياء أمور (Parents)</option>
                        <option value="admin" {{ request('user_role') === 'admin' ? 'selected' : '' }}>إدارة (Admins)</option>
                    </select>

                    <select name="user_status" class="w-full sm:w-auto border border-slate-300 rounded-xl px-3 py-2 font-bold focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="">-- حالة الإشعارات: الكل --</option>
                        <option value="enabled" {{ request('user_status') === 'enabled' ? 'selected' : '' }}>🟢 المفعلين فقط</option>
                        <option value="excluded" {{ request('user_status') === 'excluded' ? 'selected' : '' }}>🔴 المستثنين فقط</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold transition shadow-xs flex items-center gap-1.5">
                        <span>🔍</span>
                        <span>تصفية</span>
                    </button>
                    @if(request()->hasAny(['user_search', 'user_role', 'user_status']))
                        <a href="{{ route('admin.whatsapp.index', ['tab' => 'users']) }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-bold transition">
                            إلغاء التصفية ✕
                        </a>
                    @endif
                </div>
            </form>

            <!-- جدول المستخدمين وحالة إشعاراتهم التلقائية -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3.5">#</th>
                            <th class="p-3.5">المستخدم</th>
                            <th class="p-3.5">الدور</th>
                            <th class="p-3.5">رقم الواتساب</th>
                            <th class="p-3.5 text-center">حالة الإرسال التلقائي</th>
                            <th class="p-3.5 text-center">إجراء التبديل الفوري</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($managedUsers as $mIdx => $u)
                        @php
                            $roleName = match($u->role) {
                                'student' => 'طالب',
                                'teacher' => 'معلم',
                                'supervisor' => 'مشرف أكاديمي',
                                'parent' => 'ولي أمر',
                                'admin' => 'مسؤول نظام',
                                default => $u->role
                            };
                            $isExcluded = isset($u->whatsapp_notifications_enabled) && !$u->whatsapp_notifications_enabled;
                        @endphp
                        <tr class="hover:bg-slate-50 transition {{ $isExcluded ? 'bg-rose-50/30' : '' }}">
                            <td class="p-3.5 font-mono text-slate-400">
                                {{ ($managedUsers->currentPage() - 1) * $managedUsers->perPage() + $mIdx + 1 }}
                            </td>
                            <td class="p-3.5">
                                <span class="font-black text-slate-900 block text-xs">{{ $u->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $u->email }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-lg border text-[10px] font-bold bg-slate-100 text-slate-700 border-slate-200">
                                    {{ $roleName }}
                                </span>
                            </td>
                            <td class="p-3.5 font-mono font-bold text-slate-700" dir="ltr">
                                @if(!empty($u->phone_whatsapp))
                                    <span class="text-emerald-700">📱 {{ $u->phone_whatsapp }}</span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">غير مسجل</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                @if(!$isExcluded)
                                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 text-[11px] font-black inline-flex items-center gap-1 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>مُفعل للإشعارات التلقائية</span>
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 border border-rose-200 text-[11px] font-black inline-flex items-center gap-1 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        <span>مُستثنى ومحظور من الإرسال</span>
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <form action="{{ route('admin.whatsapp.users.toggle', $u->id) }}" method="POST" class="inline">
                                    @csrf
                                    @if(!$isExcluded)
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl font-bold text-xs transition shadow-2xs flex items-center gap-1 mx-auto" title="حظر استلام الإشعارات التلقائية لهذا المستخدم">
                                            <span>✕</span>
                                            <span>استثناء وحظر</span>
                                        </button>
                                    @else
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl font-bold text-xs transition shadow-2xs flex items-center gap-1 mx-auto" title="إعادة تفعيل استلام الإشعارات التلقائية">
                                            <span>✓</span>
                                            <span>تفعيل الإرسال</span>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                لا يوجد مستخدمين مطابقين لشروط البحث والتصفية.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- روابط ترقيم الصفحات -->
            <div class="pt-2">
                {{ $managedUsers->links() }}
            </div>
        </div>

        <!-- ========================================== -->
        <!-- التبويب 3: قوالب الرسائل التلقائية الذكية -->
        <!-- ========================================== -->
        <div id="tabContentTemplates" class="space-y-6 hidden">
            <div class="bg-indigo-50/70 p-4 rounded-2xl border border-indigo-200 text-xs text-indigo-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <span class="font-black text-sm block mb-1">📝 قوالب الرسائل التلقائية الجاهزة:</span>
                    يمكنك تعديل نصوص ورسائل الترحيب، المغادرة، التهنئة بالشهادات، الإعلانات، وسندات القبض والصرف، وتفعيل أو تعطيل أي قالب في أي وقت.
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($templates as $key => $tpl)
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4 flex flex-col justify-between hover:border-emerald-300 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <h3 class="font-black text-slate-900 text-sm">{{ $tpl['title'] }}</h3>
                                @if($tpl['enabled'])
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[10px]">مفعل</span>
                                @else
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full font-bold text-[10px]">معطل</span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400">{{ $tpl['description'] }}</p>
                        </div>

                        <form action="{{ route('admin.whatsapp.template.update', $key) }}" method="POST" class="space-y-3">
                            @csrf
                            <textarea name="body" rows="5" required class="w-full border border-slate-200 rounded-xl p-3 text-xs font-sans leading-relaxed focus:ring-2 focus:ring-emerald-500 outline-none bg-slate-50/50">{{ $tpl['body'] }}</textarea>

                            <div class="flex items-center justify-between pt-1">
                                <label class="flex items-center gap-2 cursor-pointer text-xs">
                                    <input type="checkbox" name="is_enabled" value="1" {{ $tpl['enabled'] ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded">
                                    <span class="font-bold text-slate-700">تفعيل القالب</span>
                                </label>

                                <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 font-bold rounded-lg text-xs transition shadow-xs">
                                    حفظ القالب 💾
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================== -->
        <!-- التبويب 4: سجل وأرشيف الرسائل الصادرة -->
        <!-- ========================================== -->
        <div id="tabContentLogs" class="space-y-5 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="text-xs text-slate-600">
                    <span class="font-black text-slate-900 block">أرشيف رسائل الواتساب الصادرة:</span>
                    توثيق كامل لكافة الرسائل المرسلة مع حالة الإرسال ورابط إعادتها الفوري.
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <form action="{{ route('admin.whatsapp.index') }}" method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="logs">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 بحث برقم أو اسم..." class="text-xs px-3.5 py-2 rounded-xl border border-slate-300 w-44 outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                        <select name="type" onchange="this.form.submit()" class="text-xs px-3 py-2 rounded-xl border border-slate-300 font-bold bg-white outline-none">
                            <option value="">كافة الأنواع</option>
                            <option value="announcement" {{ request('type') === 'announcement' ? 'selected' : '' }}>إعلانات</option>
                            <option value="congratulations" {{ request('type') === 'congratulations' ? 'selected' : '' }}>تهاني</option>
                            <option value="welcome" {{ request('type') === 'welcome' ? 'selected' : '' }}>ترحيب</option>
                            <option value="receipt_voucher" {{ request('type') === 'receipt_voucher' ? 'selected' : '' }}>سندات قبض</option>
                            <option value="payment_voucher" {{ request('type') === 'payment_voucher' ? 'selected' : '' }}>سندات صرف</option>
                        </select>
                    </form>

                    <form action="{{ route('admin.whatsapp.logs.clear') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من تفريغ كافة سجلات رسائل الواتساب؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl border border-rose-200 transition">
                            🗑️ تفريغ السجل
                        </button>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3.5">#</th>
                            <th class="p-3.5">المستلم</th>
                            <th class="p-3.5">رقم الهاتف</th>
                            <th class="p-3.5">نوع الرسالة</th>
                            <th class="p-3.5">نص الرسالة</th>
                            <th class="p-3.5 text-center">الحالة</th>
                            <th class="p-3.5 text-slate-500">التاريخ</th>
                            <th class="p-3.5 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $idx => $log)
                        @php
                            $waUrl = \App\Helpers\WhatsAppHelper::buildUrl($log->phone_number, $log->message_body);
                            $typeBadge = match($log->message_type) {
                                'welcome' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'label' => '👋 ترحيب'],
                                'congratulations' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => '🎉 تهنئة'],
                                'completion' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => '🎓 تخرج'],
                                'receipt_voucher' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => '💳 سند قبض'],
                                'payment_voucher' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => '💼 سند صرف'],
                                'entitlement' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'label' => '📊 مستحق'],
                                default => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => '📢 إعلان']
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3.5 font-mono text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-3.5">
                                @if($log->user)
                                    <span class="font-black text-slate-900 block">{{ $log->user->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $log->user->role }}</span>
                                @else
                                    <span class="text-slate-500 italic">رقم مباشر</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-bold text-slate-700" dir="ltr">
                                {{ $log->phone_number }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-lg border text-[10px] font-bold {{ $typeBadge['bg'] }}">
                                    {{ $typeBadge['label'] }}
                                </span>
                            </td>
                            <td class="p-3.5 max-w-xs truncate text-slate-600" title="{{ $log->message_body }}">
                                {{ mb_substr($log->message_body, 0, 60) }}...
                            </td>
                            <td class="p-3.5 text-center">
                                @if($log->status === 'sent')
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[10px]">
                                        ✓ أُرسلت آلياً
                                    </span>
                                @elseif($log->status === 'suppressed')
                                    @php
                                        $suppressedReason = '';
                                        if ($log->api_response) {
                                            $parsed = json_decode($log->api_response, true);
                                            $suppressedReason = $parsed['suppressed_reason'] ?? $parsed['reason'] ?? $log->api_response;
                                        }
                                    @endphp
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded-full font-bold text-[10px] cursor-help" title="{{ $suppressedReason }}">
                                        🚫 مستبعدة آلياً
                                    </span>
                                @elseif($log->status === 'failed')
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full font-bold text-[10px]" title="{{ $log->api_response }}">
                                        ✕ فشل الإرسال
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full font-bold text-[10px]">
                                        💬 موثقة بنقرة واحدة
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono text-[11px] text-slate-500">
                                {{ $log->created_at?->format('Y-m-d H:i') }}
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ $waUrl }}" target="_blank" class="px-2.5 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-bold text-[11px] transition shadow-xs flex items-center gap-1" title="فتح المحادثة بالواتساب بنقرة واحدة">
                                        <span>💬</span>
                                        <span>إرسال</span>
                                    </a>
                                    <form action="{{ route('admin.whatsapp.logs.destroy', $log->id) }}" method="POST" onsubmit="return confirm('حذف هذا السجل؟');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-600 rounded-lg text-[11px] transition">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                                لا توجد رسائل واتساب مسجلة في الأرشيف حتى الآن.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- روابط التصفح -->
            <div class="pt-2">
                {{ $logs->links() }}
            </div>
        </div>

    </div>

</div>

<!-- قوالب الرسائل الجاهزة للتحميل عبر JavaScript -->
<script>
    const templatesData = @json($templates);

    function switchWhatsAppTab(tab) {
        const cCampaign = document.getElementById('tabContentCampaign');
        const cSettings = document.getElementById('tabContentSettings');
        const cUsers = document.getElementById('tabContentUsers');
        const cTemplates = document.getElementById('tabContentTemplates');
        const cLogs = document.getElementById('tabContentLogs');

        const btnCamp = document.getElementById('tabBtnCampaign');
        const btnSet = document.getElementById('tabBtnSettings');
        const btnUsr = document.getElementById('tabBtnUsers');
        const btnTpl = document.getElementById('tabBtnTemplates');
        const btnLog = document.getElementById('tabBtnLogs');

        cCampaign.classList.add('hidden');
        cSettings.classList.add('hidden');
        if (cUsers) cUsers.classList.add('hidden');
        cTemplates.classList.add('hidden');
        cLogs.classList.add('hidden');

        const inactiveClass = "px-4 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200";
        const activeClass = "px-4 sm:px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-emerald-600 text-white shadow-sm";

        btnCamp.className = inactiveClass;
        btnSet.className = inactiveClass;
        if (btnUsr) btnUsr.className = inactiveClass;
        btnTpl.className = inactiveClass;
        btnLog.className = inactiveClass;

        if (tab === 'campaign') {
            cCampaign.classList.remove('hidden');
            btnCamp.className = activeClass;
        } else if (tab === 'settings') {
            cSettings.classList.remove('hidden');
            btnSet.className = activeClass;
        } else if (tab === 'users') {
            if (cUsers) cUsers.classList.remove('hidden');
            if (btnUsr) btnUsr.className = activeClass;
        } else if (tab === 'templates') {
            cTemplates.classList.remove('hidden');
            btnTpl.className = activeClass;
        } else if (tab === 'logs') {
            cLogs.classList.remove('hidden');
            btnLog.className = activeClass;
        }
    }

    function toggleCampaignTargetOptions() {
        const target = document.getElementById('campaignTargetSelect').value;
        const gWrap = document.getElementById('targetGroupWrapper');
        const cWrap = document.getElementById('targetCourseWrapper');
        const uWrap = document.getElementById('targetUserWrapper');
        const pWrap = document.getElementById('targetPhoneWrapper');

        gWrap.classList.add('hidden');
        cWrap.classList.add('hidden');
        uWrap.classList.add('hidden');
        pWrap.classList.add('hidden');

        if (target === 'specific_group') {
            gWrap.classList.remove('hidden');
        } else if (target === 'specific_course') {
            cWrap.classList.remove('hidden');
        } else if (target === 'custom_user') {
            uWrap.classList.remove('hidden');
        } else if (target === 'custom_phone') {
            pWrap.classList.remove('hidden');
        }
    }

    function toggleGatewayFields() {
        const prov = document.getElementById('gatewayProviderSelect').value;
        const inst = document.getElementById('instanceIdWrapper');
        const tok = document.getElementById('apiTokenWrapper');
        const url = document.getElementById('apiUrlWrapper');
        const qrCard = document.getElementById('localGatewayQrCard');

        if (prov === 'web_only') {
            inst.style.opacity = '0.5';
            tok.style.opacity = '0.5';
            url.style.opacity = '0.5';
            if (qrCard) qrCard.classList.add('hidden');
        } else if (prov === 'local_gateway') {
            inst.style.opacity = '0.3';
            tok.style.opacity = '0.3';
            url.style.opacity = '0.3';
            if (qrCard) qrCard.classList.remove('hidden');
            pollLocalGatewayStatus();
        } else {
            inst.style.opacity = '1';
            tok.style.opacity = '1';
            url.style.opacity = '1';
            if (qrCard) qrCard.classList.add('hidden');
        }
    }

    async function pollLocalGatewayStatus() {
        const prov = document.getElementById('gatewayProviderSelect')?.value;
        const qrCard = document.getElementById('localGatewayQrCard');
        if (prov !== 'local_gateway') {
            if (qrCard) qrCard.classList.add('hidden');
            return;
        } else {
            if (qrCard) qrCard.classList.remove('hidden');
        }

        try {
            const res = await fetch('http://127.0.0.1:3000/status');
            const data = await res.json();

            const badge = document.getElementById('gatewayConnectionBadge');
            const bText = document.getElementById('gatewayConnectionText');
            const pendSec = document.getElementById('gatewayQrPendingSection');
            const connSec = document.getElementById('gatewayQrConnectedSection');
            const imgWrap = document.getElementById('gatewayQrImgWrapper');
            const phoneDisp = document.getElementById('gatewayConnectedPhoneDisplay');

            if (data.connected) {
                if (badge) badge.className = 'px-3 py-1.5 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1.5';
                if (bText) bText.innerText = '🟢 متصل بنجاح: +' + (data.phone || '');
                if (pendSec) pendSec.classList.add('hidden');
                if (connSec) connSec.classList.remove('hidden');
                if (phoneDisp) phoneDisp.innerText = '+' + (data.phone || '');

                const plNum = document.querySelector('input[name="whatsapp_platform_number"]');
                if (plNum && (!plNum.value || plNum.value.includes('735 999 101')) && data.phone) {
                    plNum.value = '+' + data.phone;
                }
            } else {
                if (badge) badge.className = 'px-3 py-1.5 rounded-full text-xs font-black bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-1.5';
                if (bText) bText.innerText = '⏳ بانتظار مسح رمز الـ QR...';
                if (pendSec) pendSec.classList.remove('hidden');
                if (connSec) connSec.classList.add('hidden');

                if (data.qr && imgWrap) {
                    imgWrap.innerHTML = '<img src="' + data.qr + '" class="w-52 h-52 rounded-xl object-contain shadow" alt="QR Code">';
                }
            }
        } catch(e) {
            const bText = document.getElementById('gatewayConnectionText');
            if (bText) bText.innerText = '⚠️ خادم البوابة المحلي متوقف (شغّل start.bat)';
        }
    }

    async function disconnectLocalGateway() {
        if (!confirm('هل أنت متأكد من رغبتك في قطع الاتصال برقم الواتساب الحالي؟')) return;
        try {
            await fetch('http://127.0.0.1:3000/disconnect', { method: 'POST' });
            alert('تم قطع الاتصال بنجاح. سيتم توليد باركود جديد الآن.');
            pollLocalGatewayStatus();
        } catch(e) {
            alert('تعذر قطع الاتصال: ' + e);
        }
    }

    function refreshQrCode() {
        pollLocalGatewayStatus();
    }

    setInterval(pollLocalGatewayStatus, 2500);
    setTimeout(pollLocalGatewayStatus, 500);

    function loadTemplateToCampaign(key) {
        if (templatesData[key] && templatesData[key].body) {
            document.getElementById('campaignMessageText').value = templatesData[key].body;
            updateCharCount();
            
            // ضبط نوع الرسالة المتطابق
            const typeSelect = document.getElementById('campaignMessageType');
            if (key === 'welcome') typeSelect.value = 'welcome';
            else if (key === 'congratulations') typeSelect.value = 'congratulations';
            else if (key === 'completion') typeSelect.value = 'completion';
            else if (key === 'reminder') typeSelect.value = 'reminder';
            else typeSelect.value = 'announcement';
        }
    }

    function updateCharCount() {
        const text = document.getElementById('campaignMessageText').value || '';
        document.getElementById('charCountLabel').innerText = text.length + ' حرف';
    }

    // Hash or URL param navigation
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'logs' || window.location.hash === '#logs') {
        switchWhatsAppTab('logs');
    } else if (urlParams.get('tab') === 'users' || urlParams.get('users_page') || window.location.hash === '#users') {
        switchWhatsAppTab('users');
    } else if (urlParams.get('tab') === 'settings' || window.location.hash === '#settings') {
        switchWhatsAppTab('settings');
    } else if (urlParams.get('tab') === 'templates' || window.location.hash === '#templates') {
        switchWhatsAppTab('templates');
    }
</script>
@endsection
