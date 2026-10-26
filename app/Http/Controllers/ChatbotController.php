<?php

namespace App\Http\Controllers;

use App\Services\SmartBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * استقبال ومعالجة رسائل الشات من واجهة المنصة (Web Chat Widget)
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        $response = SmartBotService::handleMessage($request->input('message'), $user);

        return response()->json([
            'success' => true,
            'reply' => $response['reply'],
            'suggestions' => $response['suggestions'] ?? [],
            'intent' => $response['matched_intent'] ?? 'general',
            'whatsapp_url' => $response['whatsapp_url'] ?? null,
            'timestamp' => now()->format('H:i'),
        ]);
    }

    /**
     * جلب رسالة الترحيب والخيارات السريعة عند فتح نافذة الشات لأول مرة
     */
    public function quickInfo()
    {
        $user = auth()->user();
        $welcome = SmartBotService::handleMessage('مرحبا', $user);

        return response()->json([
            'success' => true,
            'welcome_message' => $welcome['reply'],
            'suggestions' => $welcome['suggestions'] ?? [],
            'timestamp' => now()->format('H:i'),
        ]);
    }

    /**
     * نقطة استقبال وتوجيه الردود التلقائية لرسائل الواتساب (WhatsApp Webhook)
     * متوافقة مع بوابات الواتساب (UltraMsg, Wasapi, Meta Cloud API, Baileys)
     */
    public function whatsappWebhook(Request $request)
    {
        // 1. تأكيد الـ Webhook الخاص بـ Meta Cloud API (GET verification challenge)
        if ($request->isMethod('get')) {
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');

            $expectedToken = config('services.whatsapp.verify_token', 'asots_wa_secret_2026');
            if ($mode === 'subscribe' && $token === $expectedToken) {
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }
            return response('Unauthorized', 403);
        }

        // 2. معالجة الرسالة الواردة (POST Webhook Payload)
        $incomingText = '';
        $senderPhone = '';

        // أ) تنسيق UltraMsg
        if ($request->has('data.body')) {
            $incomingText = (string)$request->input('data.body');
            $senderPhone = (string)$request->input('data.from');
        }
        // ب) تنسيق Meta WhatsApp Cloud API
        elseif ($request->has('entry.0.changes.0.value.messages.0')) {
            $msgObj = $request->input('entry.0.changes.0.value.messages.0');
            $incomingText = $msgObj['text']['body'] ?? '';
            $senderPhone = $msgObj['from'] ?? '';
        }
        // ج) تنسيق عام مبسط (Generic/Wasapi/Custom)
        else {
            $incomingText = (string)($request->input('message') ?? $request->input('text') ?? $request->input('body') ?? '');
            $senderPhone = (string)($request->input('phone') ?? $request->input('from') ?? $request->input('sender') ?? '');
        }

        if (trim($incomingText) === '') {
            return response()->json(['status' => 'ignored', 'reason' => 'empty_body']);
        }

        // البحث عن الطالب برقم الهاتف إن وجد لتخصيص الرد
        $cleanPhone = preg_replace('/[^0-9]/', '', $senderPhone);
        $user = null;
        if ($cleanPhone !== '') {
            $user = \App\Models\User::where('phone_whatsapp', 'like', "%{$cleanPhone}%")->first();
        }

        // توليد الرد الذكي المعتمد على قاعدة البيانات
        $botResponse = SmartBotService::handleMessage($incomingText, $user);
        $replyText = $botResponse['reply'];

        // تسجيل العملية في السجلات للتدقيق والمتابعة
        Log::info("WhatsApp Auto-Reply sent to {$senderPhone}: Intent [{$botResponse['matched_intent']}]");

        return response()->json([
            'status' => 'success',
            'to' => $senderPhone,
            'reply' => $replyText,
            'intent' => $botResponse['matched_intent'] ?? 'general',
            'matched_course' => $botResponse['course'] ?? null,
        ]);
    }
}
