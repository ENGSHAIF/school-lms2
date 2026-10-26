const express = require('express');
const cors = require('cors');
const fs = require('fs');
const path = require('path');
const qrcode = require('qrcode');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

let isConnected = false;
let connectedPhone = null;
let rawQrCode = null;
let qrDataURL = null;
let sentMessagesLog = [];
let sock = null;
let isInitializing = false;

const AUTH_DIR = path.join(__dirname, 'auth_info_baileys');

async function initBaileys() {
    if (isInitializing) return;
    isInitializing = true;

    try {
        const { default: makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } = await import('@whiskeysockets/baileys');
        const pino = (await import('pino')).default;

        if (!fs.existsSync(AUTH_DIR)) {
            fs.mkdirSync(AUTH_DIR, { recursive: true });
        }

        const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
        const { version } = await fetchLatestBaileysVersion().catch(() => ({ version: [2, 3000, 1015901307] }));

        sock = makeWASocket({
            version,
            auth: state,
            printQRInTerminal: true,
            logger: pino({ level: 'silent' }),
            browser: ['ASOTS School System', 'Chrome', '1.0.0'],
            connectTimeoutMs: 60000,
            defaultQueryTimeoutMs: 60000,
            keepAliveIntervalMs: 10000,
            emitOwnEvents: false,
            fireInitQueries: true,
            generateHighQualityLinkPreview: true,
            syncFullHistory: false,
        });

        sock.ev.on('creds.update', saveCreds);

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                rawQrCode = qr;
                try {
                    qrDataURL = await qrcode.toDataURL(qr, { width: 300, margin: 2 });
                    console.log('⚡ [WhatsApp Gateway] تم توليد رمز QR جديد جاهز للمسح من الهاتف.');
                } catch (e) {
                    console.error('QR Generate Error:', e);
                }
            }

            if (connection === 'close') {
                const statusCode = (lastDisconnect?.error)?.output?.statusCode;
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;
                isConnected = false;
                connectedPhone = null;
                console.log(`🔴 [WhatsApp Gateway] انقطع الاتصال (رمز: ${statusCode}). إعادة المحاولة:`, shouldReconnect);

                if (statusCode === DisconnectReason.loggedOut) {
                    // إذا سجل المستخدم الخروج من الهاتف، ننظف بيانات الجلسة لنسمح بربط جديد
                    console.log('🧹 تنظيف بيانات الجلسة المنتهية...');
                    try {
                        fs.rmSync(AUTH_DIR, { recursive: true, force: true });
                    } catch (_) {}
                    rawQrCode = null;
                    qrDataURL = null;
                }

                isInitializing = false;
                if (shouldReconnect) {
                    setTimeout(() => initBaileys(), 3000);
                }
            } else if (connection === 'open') {
                isConnected = true;
                rawQrCode = null;
                qrDataURL = null;
                isInitializing = false;
                connectedPhone = sock?.user?.id ? sock.user.id.split(':')[0] : 'متصل';
                console.log('====================================================');
                console.log('🟢 [WhatsApp Gateway] تم ربط الواتساب بنجاح برقم:', connectedPhone);
                console.log('====================================================');
            }
        });

    } catch (e) {
        isInitializing = false;
        console.error('Baileys Init Error:', e);
        setTimeout(() => initBaileys(), 5000);
    }
}

// بدء التشغيل والربط
initBaileys();

// 1. الصفحة الرئيسية التفاعلية
app.get('/', (req, res) => {
    res.send(`
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>خادم ربط الواتساب الآلي - منصة ASOTS</title>
        <style>
            * { box-sizing: border-box; }
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0b1120; color: #f8fafc; padding: 25px; margin: 0; line-height: 1.6; }
            .card { max-width: 680px; margin: 0 auto; background: #1e293b; border-radius: 20px; padding: 30px; box-shadow: 0 15px 35px rgba(0,0,0,0.4); border: 1px solid #334155; }
            h1 { font-size: 22px; color: #38bdf8; margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px; }
            p { font-size: 13px; color: #94a3b8; margin: 0 0 20px 0; }
            .status-box { padding: 16px; border-radius: 12px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
            .status-connected { background: #064e3b; border: 1px solid #059669; color: #6ee7b7; }
            .status-waiting { background: #451a03; border: 1px solid #d97706; color: #fde68a; }
            .qr-container { text-align: center; background: #0f172a; padding: 25px; border-radius: 16px; border: 1px dashed #475569; margin-bottom: 25px; }
            .qr-image { width: 280px; height: 280px; border-radius: 12px; background: white; padding: 10px; box-shadow: 0 8px 20px rgba(0,0,0,0.5); }
            .instructions { background: #172554; border: 1px solid #1e40af; border-radius: 12px; padding: 16px; font-size: 12px; color: #bfdbfe; margin-bottom: 25px; }
            .instructions ol { margin: 8px 0 0 0; padding-right: 20px; }
            .instructions li { margin-bottom: 5px; }
            .btn { background: #0284c7; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; }
            .btn:hover { background: #0369a1; }
            .btn-danger { background: #dc2626; }
            .btn-danger:hover { background: #b91c1c; }
            .log-box { background: #090d16; border-radius: 10px; padding: 15px; font-family: monospace; font-size: 11px; max-height: 200px; overflow-y: auto; color: #a5f3fc; border: 1px solid #1e293b; }
            .pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
            @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
        </style>
    </head>
    <body>
        <div class="card">
            <h1><span>📱</span> خادم ربط الواتساب الآلي الذكي (ASOTS WhatsApp Gateway)</h1>
            <p>يستقبل هذا الخادم إشعارات وسندات منصة المدرسة ويرسلها فورياً وتلقائياً عبر حساب الواتساب الخاص بك.</p>
            
            <div id="statusBox" class="status-box ${isConnected ? 'status-connected' : 'status-waiting'}">
                <div>
                    <strong style="font-size: 15px;" id="statusText">
                        ${isConnected ? '✅ متصل وجاهز للإرسال الآلي' : '⏳ بانتظار مسح رمز الاستجابة السريعة (QR Code)...'}
                    </strong>
                    <div style="font-size: 12px; opacity: 0.9; margin-top: 4px;" id="phoneText">
                        ${isConnected ? 'رقم الهاتف المرتبط: +' + connectedPhone : 'يرجى توجيه كاميرا الهاتف لمسح الباركود أدناه'}
                    </div>
                </div>
                ${isConnected ? '<button onclick="disconnect()" class="btn btn-danger">قطع الاتصال</button>' : ''}
            </div>

            <div id="qrSection" style="${isConnected ? 'display: none;' : 'display: block;'}">
                <div class="instructions">
                    <strong>📌 خطوات الربط بخطوة واحدة:</strong>
                    <ol>
                        <li>افتح تطبيق <strong>WhatsApp</strong> على هاتفك المحمول.</li>
                        <li>اضغط على <strong>القائمة (ثلاث نقاط)</strong> أو <strong>الإعدادات</strong> في أسفل الشاشة.</li>
                        <li>اختر <strong>الأجهزة المرتبطة (Linked Devices)</strong> ثم اضغط على <strong>ربط جهاز (Link a Device)</strong>.</li>
                        <li>وجّه كاميرا الهاتف نحو الباركود التالي:</li>
                    </ol>
                </div>

                <div class="qr-container">
                    <div id="qrWrapper">
                        ${qrDataURL 
                            ? '<img class="qr-image" src="' + qrDataURL + '" alt="WhatsApp QR Code">' 
                            : '<div class="pulse" style="padding: 60px 20px; color: #94a3b8; font-size: 14px;">جاري تجهيز رمز الباركود... ثوانٍ معدودة...</div>'}
                    </div>
                    <div style="margin-top: 15px; font-size: 12px; color: #64748b;">
                        يتجدد الباركود تلقائياً لحمايتك. بمجرد المسح سيتصل النظام مباشرة بدون الحاجة للتحديث.
                    </div>
                </div>
            </div>

            <div id="connectedSection" style="${isConnected ? 'display: block;' : 'display: none;'}; text-align: center; padding: 25px 0;">
                <div style="font-size: 55px; margin-bottom: 10px;">🎉</div>
                <h3 style="color: #34d399; margin: 0 0 10px 0;">تم ربط الواتساب بنجاح وبشكل دائم!</h3>
                <p style="color: #94a3b8; font-size: 13px; max-width: 480px; margin: 0 auto 20px auto;">
                    أي سند صرف، سند قبض، إشعار مستحقات، أو نتيجة اختبار يتم اعتمادها في المنصة ستُرسل تلقائياً إلى واتساب المستلم في أجزاء من الثانية.
                </p>
                <a href="http://localhost:8000/accounting" class="btn" style="background: #10b981;">← العودة للنظام المحاسبي في المنصة</a>
            </div>

            <div style="margin-top: 25px;">
                <h3 style="font-size: 14px; color: #cbd5e1; margin-bottom: 10px;">📋 سجل الرسائل الصادرة اللحظي:</h3>
                <div class="log-box" id="logBox">
                    ${sentMessagesLog.length === 0 ? 'لا توجد رسائل صادرة في هذه الجلسة بعد.' : sentMessagesLog.map(l => '[' + l.time + '] إلى: ' + l.phone + ' - الحالة: ' + l.status).join('<br>')}
                </div>
            </div>
        </div>

        <script>
            let currentStatus = ${isConnected};

            async function checkStatus() {
                try {
                    const res = await fetch('/status');
                    const data = await res.json();
                    
                    if (data.connected && !currentStatus) {
                        currentStatus = true;
                        window.location.reload();
                    } else if (!data.connected && currentStatus) {
                        currentStatus = false;
                        window.location.reload();
                    } else if (!data.connected && data.qr) {
                        const imgWrapper = document.getElementById('qrWrapper');
                        if (imgWrapper) {
                            imgWrapper.innerHTML = '<img class="qr-image" src="' + data.qr + '" alt="WhatsApp QR Code">';
                        }
                    }
                } catch(e) {}
            }

            async function disconnect() {
                if (!confirm('هل أنت متأكد من رغبتك في تسجيل الخروج وقطع ربط الواتساب؟')) return;
                try {
                    await fetch('/disconnect', { method: 'POST' });
                    alert('تم قطع الاتصال. جاري إعادة تحميل الصفحة لإظهار باركود جديد...');
                    window.location.reload();
                } catch(e) {
                    alert('حدث خطأ: ' + e);
                }
            }

            // فحص الحالة كل ثانيتين ونصف
            setInterval(checkStatus, 2500);
        </script>
    </body>
    </html>
    `);
});

// 2. فحص حالة الاتصال والحصول على الباركود (Status API)
app.get('/status', (req, res) => {
    res.json({
        success: true,
        connected: isConnected,
        phone: connectedPhone,
        qr: isConnected ? null : qrDataURL,
        port: PORT,
        uptime: process.uptime()
    });
});

// 3. مسار الصورة المباشرة للباركود (Direct QR Image)
app.get('/qr-image', (req, res) => {
    if (isConnected) {
        return res.status(200).send('<p style="color:green;font-family:sans-serif;font-weight:bold;">✅ متصل بالفعل</p>');
    }
    if (!qrDataURL) {
        return res.status(200).send('<p style="color:#666;font-family:sans-serif;">⏳ جاري تجهيز الباركود...</p>');
    }
    const im = qrDataURL.split(",")[1];
    const img = Buffer.from(im, 'base64');
    res.writeHead(200, {
        'Content-Type': 'image/png',
        'Content-Length': img.length
    });
    res.end(img);
});

// 4. واجهة إرسال الرسائل عبر الواتساب (Send Message API)
app.post('/send-message', async (req, res) => {
    const { phone, message } = req.body;

    if (!phone || !message) {
        return res.status(400).json({ success: false, error: 'الرقم ونص الرسالة مطلوبان' });
    }

    let cleanPhone = phone.replace(/[^0-9]/g, '');
    
    // تصحيح الأرقام اليمنية إذا كان الرقم محلياً (مثال: 777823720 -> 967777823720)
    if (cleanPhone.length === 9 && (cleanPhone.startsWith('7') || cleanPhone.startsWith('1'))) {
        cleanPhone = '967' + cleanPhone;
    } else if (cleanPhone.length === 10 && cleanPhone.startsWith('07')) {
        cleanPhone = '967' + cleanPhone.substring(1);
    }

    const logEntry = {
        time: new Date().toLocaleTimeString('ar-EG'),
        phone: cleanPhone,
        message: message.substring(0, 60) + '...',
        status: 'pending'
    };

    if (isConnected && sock) {
        try {
            const jid = `${cleanPhone}@s.whatsapp.net`;
            await sock.sendMessage(jid, { text: message });
            logEntry.status = 'sent_successfully';
            sentMessagesLog.unshift(logEntry);
            if (sentMessagesLog.length > 50) sentMessagesLog.pop();

            console.log(`[WhatsApp Gateway] ✅ Message delivered to ${cleanPhone}`);
            return res.json({
                success: true,
                status: 'delivered',
                provider: 'baileys_gateway',
                phone: cleanPhone,
                timestamp: Date.now()
            });
        } catch (err) {
            console.error('Error sending via Baileys:', err);
            logEntry.status = 'failed: ' + err.message;
            sentMessagesLog.unshift(logEntry);
            return res.status(500).json({ success: false, error: err.message });
        }
    } else {
        logEntry.status = 'not_connected';
        sentMessagesLog.unshift(logEntry);
        console.warn(`[WhatsApp Gateway] ⚠️ Cannot send: WhatsApp is not linked yet. Queueing simulated receipt.`);

        return res.status(400).json({
            success: false,
            error: 'خادم الواتساب غير مرتبط بأي هاتف حالياً. يرجى مسح رمز الباركود QR أولاً.',
            connected: false
        });
    }
});

// 5. مسار قطع الاتصال وتسجيل الخروج (Disconnect / Logout API)
app.all('/disconnect', async (req, res) => {
    try {
        if (sock) {
            await sock.logout().catch(() => {});
        }
        isConnected = false;
        connectedPhone = null;
        rawQrCode = null;
        qrDataURL = null;

        try {
            if (fs.existsSync(AUTH_DIR)) {
                fs.rmSync(AUTH_DIR, { recursive: true, force: true });
            }
        } catch (_) {}

        setTimeout(() => initBaileys(), 1500);

        res.json({ success: true, message: 'تم قطع الاتصال وحذف بيانات الجلسة بنجاح.' });
    } catch (e) {
        res.status(500).json({ success: false, error: e.message });
    }
});

app.listen(PORT, '0.0.0.0', () => {
    console.log(`====================================================`);
    console.log(`🚀 ASOTS Local WhatsApp Gateway is running!`);
    console.log(`📡 URL: http://127.0.0.1:${PORT}`);
    console.log(`📱 QR Code Web Page: http://127.0.0.1:${PORT}`);
    console.log(`====================================================`);
});
