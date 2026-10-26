<!-- =============================================================== -->
<!-- ودجت المساعد الذكي والشات المباشر (ASOTS Smart AI Chat Widget) -->
<!-- =============================================================== -->
@php
    $whatsappNumber = site_setting('whatsapp_platform_number') ?: site_setting('whatsapp_number', config('asots.contact.phone_whatsapp', '+967 735 999 101'));
    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsappNumber);
    $userName = auth()->check() ? auth()->user()->name : null;
@endphp

<!-- زر تشغيل المساعد العائم أسفل يسار الشاشة (فوق زر الواتساب مباشرة) -->
<div id="asotsChatLauncher" class="fixed bottom-24 left-6 z-50 flex items-center group">
    <!-- تلميح التوجيه عند التمرير -->
    <div class="hidden sm:block opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0 ml-3 bg-slate-900/95 backdrop-blur text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xl pointer-events-none whitespace-nowrap border border-slate-700/50">
        <span class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>المساعد الذكي 🌟 • استفسر عن أسعار الدورات والمستويات</span>
        </span>
        <span class="block text-[10px] text-amber-400 mt-0.5">رد فوري ولحظي من قاعدة البيانات ⚡</span>
    </div>

    <!-- الزر الدائري المتوهج -->
    <button type="button" onclick="toggleSmartChat()" aria-label="فتح المساعد الذكي"
        class="relative flex items-center justify-center w-14 h-14 bg-gradient-to-tr from-indigo-600 via-indigo-700 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-full shadow-2xl hover:shadow-indigo-500/50 transition-all duration-300 transform hover:scale-110 active:scale-95 focus:outline-none focus:ring-4 focus:ring-indigo-300">
        <!-- حلقة نبض ناعمة -->
        <span class="absolute inset-0 rounded-full bg-indigo-400 animate-ping opacity-25 pointer-events-none"></span>

        <!-- أيقونة الشعار للمساعد الذكي -->
        <span id="chatLauncherIcon" class="w-10 h-10 transition-transform duration-300 flex items-center justify-center">
            <img src="{{ asset('images/logo.png') }}" alt="شعار المساعد الذكي" class="w-full h-full object-contain rounded-full bg-white p-0.5 shadow-md">
        </span>
        <span id="chatCloseIcon" class="text-xl hidden font-bold">✕</span>

        <!-- بادج متصل أونلاين -->
        <span class="absolute top-0 right-0 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full shadow-xs"></span>
    </button>
</div>

<!-- نافذة الشات الذكية المنبثقة -->
<div id="asotsChatWindow" 
    class="fixed bottom-4 sm:bottom-6 left-3 right-3 sm:right-auto sm:left-24 z-50 w-auto sm:w-[410px] max-w-[420px] h-[580px] max-h-[85vh] bg-white rounded-3xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden hidden transition-all duration-300 transform origin-bottom-left"
    style="box-shadow: 0 25px 60px -15px rgba(30, 27, 75, 0.35);">
    
    <!-- 1. رأس نافذة الشات -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-4 flex items-center justify-between border-b border-indigo-900/50 shrink-0">
        <div class="flex items-center gap-3">
            <div class="relative w-10 h-10 rounded-2xl bg-white flex items-center justify-center p-1 shadow-md ring-2 ring-white/30">
                <img src="{{ asset('images/logo.png') }}" alt="المساعد الذكي" class="w-full h-full object-contain rounded-xl">
                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <h3 class="font-black text-sm text-white">المساعد الذكي لمنصة ASOTS</h3>
                    <span class="text-[9px] font-bold bg-amber-500/20 text-amber-300 px-1.5 py-0.5 rounded border border-amber-400/30">AI Bot</span>
                </div>
                <p class="text-[10px] text-slate-300 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>متصل الآن • يجيب فوراً من قاعدة البيانات</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-1">
            <button type="button" onclick="restartChat()" title="بدء محادثة جديدة" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-white/10 transition text-xs">
                🔄
            </button>
            <button type="button" onclick="toggleSmartChat()" title="إغلاق الشات" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-white/10 transition text-sm font-bold">
                ✕
            </button>
        </div>
    </div>

    <!-- 2. شريط تحويل للواتساب المباشر (عند الحاجة للتحدث مع إنسان) -->
    <a href="https://wa.me/{{ $cleanWhatsapp }}?text={{ urlencode('مرحباً إدارة ASOTS، أود الاستفسار من خدمة العملاء') }}" target="_blank"
        class="bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-bold px-3.5 py-2 border-b border-emerald-200/80 flex items-center justify-between transition shrink-0">
        <span class="flex items-center gap-1.5">
            <span class="text-sm">💬</span>
            <span>تريد التحدث مع موظف خدمة العملاء مباشرة؟</span>
        </span>
        <span class="text-[10px] text-emerald-700 font-black flex items-center gap-0.5">
            <span>واتساب</span>
            <span class="font-mono" dir="ltr">↗</span>
        </span>
    </a>

    <!-- 3. مساحة الرسائل التفاعلية -->
    <div id="chatMessagesArea" class="flex-1 p-4 overflow-y-auto space-y-3.5 bg-slate-50/70 text-xs">
        <!-- رسالة الترحيب المبدئية -->
        <div class="flex gap-2.5 items-start">
            <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 p-0.5 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                <img src="{{ asset('images/logo.png') }}" alt="ASOTS Bot" class="w-full h-full object-contain rounded-lg">
            </div>
            <div class="bg-white border border-slate-200 p-3.5 rounded-2xl rounded-tr-none shadow-xs max-w-[85%] text-slate-800 space-y-2">
                <p class="font-bold text-slate-900">
                    @if($userName)
                        أهلاً بك يا <strong>{{ $userName }}</strong> 🎓!
                    @else
                        أهلاً بك في منصة ASOTS التعليمية 🌟!
                    @endif
                </p>
                <p class="text-slate-600 leading-relaxed text-[11px]">
                    أنا المساعد الآلي الذكي للمنصة. يمكنك سؤالي عن:
                </p>
                <ul class="text-[11px] text-slate-700 space-y-1 list-disc list-inside">
                    <li>💰 <strong>أسعار الدورات</strong> (مثال: <em>كم سعر دورة Access A؟</em>)</li>
                    <li>🏛️ <strong>المستويات الأكاديمية والدبلوم</strong></li>
                    <li>🚀 <strong>اختبار تحديد المستوى المجاني</strong></li>
                    <li>📊 <strong>الاستعلام المالي وسندات القبض</strong></li>
                </ul>
                <span class="text-[9px] text-slate-400 block text-left font-mono mt-1" id="initialBotTime">{{ now()->format('H:i') }}</span>
            </div>
        </div>

        <!-- مؤشر جاري الكتابة / جاري البحث (مخفي افتراضياً) -->
        <div id="botTypingIndicator" class="hidden flex gap-2.5 items-start">
            <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 p-0.5 flex items-center justify-center shrink-0 shadow-2xs">
                <img src="{{ asset('images/logo.png') }}" alt="ASOTS Bot" class="w-full h-full object-contain rounded-lg">
            </div>
            <div class="bg-white border border-indigo-100 p-3 rounded-2xl rounded-tr-none shadow-xs text-indigo-600 flex items-center gap-1.5 text-[11px] font-bold">
                <span>جاري البحث في قاعدة البيانات</span>
                <span class="flex gap-1 items-center">
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0s;"></span>
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.15s;"></span>
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.3s;"></span>
                </span>
            </div>
        </div>
    </div>

    <!-- 4. شريط الأزرار والاقتراحات السريعة (Quick Suggestions) -->
    <div id="chatSuggestionsBox" class="p-2.5 bg-white border-t border-slate-100 overflow-x-auto whitespace-nowrap space-x-1.5 space-x-reverse flex items-center shrink-0">
        <button type="button" onclick="sendQuickMessage('كم سعر دورة Access A؟')" class="px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-200 rounded-xl text-[10px] font-bold transition shrink-0 flex items-center gap-1">
            <span>💰</span>
            <span>كم سعر دورة Access A؟</span>
        </button>
        <button type="button" onclick="sendQuickMessage('ما هي اسعار الدورات المتاحة؟')" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200 rounded-xl text-[10px] font-bold transition shrink-0 flex items-center gap-1">
            <span>📋</span>
            <span>قائمة أسعار الدورات</span>
        </button>
        <button type="button" onclick="sendQuickMessage('ما هي المستويات الأكاديمية؟')" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl text-[10px] font-bold transition shrink-0 flex items-center gap-1">
            <span>🏛️</span>
            <span>المستويات الأكاديمية</span>
        </button>
        <button type="button" onclick="sendQuickMessage('كيف اسوي اختبار تحديد المستوى؟')" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-[10px] font-bold transition shrink-0 flex items-center gap-1">
            <span>🚀</span>
            <span>تحديد المستوى المجاني</span>
        </button>
        @auth
            <button type="button" onclick="sendQuickMessage('كم رصيدي المالي المتبقي؟')" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 rounded-xl text-[10px] font-bold transition shrink-0 flex items-center gap-1">
                <span>🎓</span>
                <span>رصيدي المالي وسنداتي</span>
            </button>
        @endauth
        <button type="button" onclick="sendQuickMessage('اريد رقم الواتساب والتواصل مع الإدارة')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl text-[10px] font-bold transition shrink-0 flex items-center gap-1">
            <span>📞</span>
            <span>بيانات التواصل</span>
        </button>
    </div>

    <!-- 5. حقل إدخال الرسالة وزر الإرسال -->
    <div class="p-3 bg-white border-t border-slate-200 shrink-0">
        <form id="smartChatForm" onsubmit="handleChatSubmit(event)" class="flex items-center gap-2">
            <input type="text" id="chatMessageInput" required maxlength="500" autocomplete="off"
                placeholder="اكتب سؤالك هنا... (مثال: كم سعر دورة Access A؟)"
                class="flex-1 bg-slate-50 border border-slate-300 rounded-2xl px-3.5 py-2.5 text-xs text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition placeholder:text-slate-400">
            <button type="submit" id="chatSendBtn"
                class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-black px-4 py-2.5 rounded-2xl shadow-md transition flex items-center justify-center gap-1 shrink-0">
                <span class="text-xs">إرسال</span>
                <span class="text-sm">🚀</span>
            </button>
        </form>
        <span class="block text-[9px] text-slate-400 text-center mt-1.5 font-medium">
            ⚡ رد آلي ذكي لحظي متصل بقاعدة بيانات الدورات والأسعار
        </span>
    </div>
</div>

<script>
    const asotsBotLogoUrl = "{{ asset('images/logo.png') }}";
    let isSmartChatOpen = false;

    function toggleSmartChat() {
        const win = document.getElementById('asotsChatWindow');
        const iconBot = document.getElementById('chatLauncherIcon');
        const iconClose = document.getElementById('chatCloseIcon');

        isSmartChatOpen = !isSmartChatOpen;

        if (isSmartChatOpen) {
            win.classList.remove('hidden');
            iconBot.classList.add('hidden');
            iconClose.classList.remove('hidden');
            document.getElementById('chatMessageInput').focus();
            scrollChatToBottom();
        } else {
            win.classList.add('hidden');
            iconBot.classList.remove('hidden');
            iconClose.classList.add('hidden');
        }
    }

    function scrollChatToBottom() {
        const area = document.getElementById('chatMessagesArea');
        if (area) {
            setTimeout(() => {
                area.scrollTop = area.scrollHeight;
            }, 50);
        }
    }

    function restartChat() {
        const area = document.getElementById('chatMessagesArea');
        const indicator = document.getElementById('botTypingIndicator');
        const firstMessage = area.firstElementChild;
        area.innerHTML = '';
        if (firstMessage) area.appendChild(firstMessage);
        if (indicator) area.appendChild(indicator);
        indicator.classList.add('hidden');
        scrollChatToBottom();
    }

    function sendQuickMessage(text) {
        document.getElementById('chatMessageInput').value = text;
        const form = document.getElementById('smartChatForm');
        form.dispatchEvent(new Event('submit', { cancelable: true }));
    }

    async function handleChatSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('chatMessageInput');
        const text = input.value.trim();
        if (!text) return;

        // 1. عرض رسالة المستخدم فوراً في المحادثة
        appendUserMessage(text);
        input.value = '';

        // 2. إظهار مؤشر الكتابة
        const indicator = document.getElementById('botTypingIndicator');
        indicator.classList.remove('hidden');
        scrollChatToBottom();

        // 3. إرسال الطلب للسيرفر
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch('/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ message: text })
            });

            const data = await res.json();
            indicator.classList.add('hidden');

            if (data.success && data.reply) {
                appendBotMessage(data.reply, data.suggestions, data.whatsapp_url, data.timestamp);
            } else {
                appendBotMessage('عذراً، حدث خطأ أثناء معالجة السؤال. يمكنك المحاولة مجدداً أو التواصل مع الإدارة مباشرة.');
            }
        } catch (err) {
            indicator.classList.add('hidden');
            appendBotMessage('تعذر الاتصال بالخادم مؤقتاً. يرجى التحقق من اتصال الإنترنت أو التواصل عبر الواتساب.');
        }
    }

    function appendUserMessage(text) {
        const area = document.getElementById('chatMessagesArea');
        const indicator = document.getElementById('botTypingIndicator');

        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

        const userMsg = document.createElement('div');
        userMsg.className = 'flex gap-2.5 items-start justify-end';
        userMsg.innerHTML = `
            <div class="bg-indigo-600 text-white p-3 rounded-2xl rounded-tl-none shadow-xs max-w-[85%] text-slate-100">
                <p class="leading-relaxed text-[11px] font-medium whitespace-pre-wrap">${escapeHtmlBot(text)}</p>
                <span class="text-[9px] text-indigo-200 block text-right font-mono mt-1">${timeStr}</span>
            </div>
            <div class="w-7 h-7 rounded-xl bg-slate-800 text-amber-400 flex items-center justify-center text-xs shrink-0 shadow-2xs font-bold mt-0.5">
                👤
            </div>
        `;

        area.insertBefore(userMsg, indicator);
        scrollChatToBottom();
    }

    function appendBotMessage(text, suggestions = [], whatsappUrl = null, timestamp = null) {
        const area = document.getElementById('chatMessagesArea');
        const indicator = document.getElementById('botTypingIndicator');

        const timeStr = timestamp || (new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));

        const formattedHtml = formatBotMarkdown(text);

        const botMsg = document.createElement('div');
        botMsg.className = 'flex gap-2.5 items-start';

        let extraButtonsHtml = '';
        if (whatsappUrl) {
            extraButtonsHtml += `
                <div class="mt-2.5 pt-2 border-t border-slate-100">
                    <a href="${whatsappUrl}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-bold shadow-xs transition">
                        <span>💬</span>
                        <span>محادثة مباشرة عبر الواتساب مع الإدارة</span>
                    </a>
                </div>
            `;
        }

        botMsg.innerHTML = `
            <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 p-0.5 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                <img src="${asotsBotLogoUrl}" alt="ASOTS Bot" class="w-full h-full object-contain rounded-lg">
            </div>
            <div class="bg-white border border-slate-200 p-3.5 rounded-2xl rounded-tr-none shadow-xs max-w-[88%] text-slate-800 space-y-1.5 leading-relaxed text-[11px]">
                <div>${formattedHtml}</div>
                ${extraButtonsHtml}
                <span class="text-[9px] text-slate-400 block text-left font-mono mt-1">${timeStr}</span>
            </div>
        `;

        area.insertBefore(botMsg, indicator);

        // تحديث أزرار الاقتراحات السريعة
        if (suggestions && suggestions.length > 0) {
            updateChatSuggestions(suggestions);
        }

        scrollChatToBottom();
    }

    function updateChatSuggestions(suggestions) {
        const box = document.getElementById('chatSuggestionsBox');
        if (!box) return;
        box.innerHTML = '';
        suggestions.forEach(s => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200 rounded-xl text-[10px] font-bold transition shrink-0 flex items-center gap-1';
            btn.onclick = () => sendQuickMessage(s);
            btn.innerHTML = `<span>💡</span><span>${escapeHtmlBot(s)}</span>`;
            box.appendChild(btn);
        });
    }

    function formatBotMarkdown(text) {
        if (!text) return '';
        let escaped = escapeHtmlBot(text);

        // استبدال الروابط [text](url)
        escaped = escaped.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" class="text-indigo-600 hover:text-indigo-800 font-bold underline inline-flex items-center gap-0.5">$1 🔗</a>');

        // استبدال النص العريض **text**
        escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong class="font-black text-slate-900">$1</strong>');

        // استبدال الكود أو الأرقام `code`
        escaped = escaped.replace(/`(.*?)`/g, '<code class="bg-amber-50 text-amber-900 border border-amber-200 px-1.5 py-0.5 rounded font-mono font-bold">$1</code>');

        // الأسطر الجديدة
        escaped = escaped.replace(/\n/g, '<br>');

        return escaped;
    }

    function escapeHtmlBot(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
