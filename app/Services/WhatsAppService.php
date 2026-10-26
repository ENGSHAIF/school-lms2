<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Models\WhatsAppLog;
use App\Models\User;
use App\Models\StudyGroup;
use App\Models\Course;
use App\Helpers\WhatsAppHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * جلب إعدادات الواتساب الحالية والمصفوفة التشغيلية
     */
    public static function getSettings(): array
    {
        return [
            // المفتاح الرئيسي لتعطيل أو تشغيل الواتساب كلياً
            'master_switch' => (bool) SiteSetting::get('whatsapp_master_switch', true),

            // أقصى مدة مسموحة للعمليات بالساعات (تجاهل العمليات السابقة بعد توقف الواتس)
            'max_age_hours' => (int) SiteSetting::get('whatsapp_max_age_hours', 24),

            'platform_number' => SiteSetting::get('whatsapp_platform_number', '+967 735 999 101'),
            'provider' => SiteSetting::get('whatsapp_gateway_provider', 'web_only'), // ultramsg, greenapi, wasender, twilio, custom, local_gateway, web_only
            'instance_id' => SiteSetting::get('whatsapp_instance_id', ''),
            'api_token' => SiteSetting::get('whatsapp_api_token', ''),
            'api_url' => SiteSetting::get('whatsapp_api_url', ''),
            'auto_send_enabled' => (bool) SiteSetting::get('whatsapp_auto_send_enabled', false),

            // مصفوفة العمليات التشغيلية (تفعيل أو إلغاء كل عملية على حدة)
            'notify_receipt' => (bool) SiteSetting::get('whatsapp_notify_receipt', true),
            'notify_payment' => (bool) SiteSetting::get('whatsapp_notify_payment', true),
            'notify_entitlement' => (bool) SiteSetting::get('whatsapp_notify_entitlement', true),
            'notify_exam_result' => (bool) SiteSetting::get('whatsapp_notify_exam_result', true),
            'notify_evaluation' => (bool) SiteSetting::get('whatsapp_notify_evaluation', true),
            'notify_completion' => (bool) SiteSetting::get('whatsapp_notify_completion', true),
            'notify_welcome' => (bool) SiteSetting::get('whatsapp_notify_welcome', true),
            'notify_reminder' => (bool) SiteSetting::get('whatsapp_notify_reminder', true),
        ];
    }

    /**
     * التحقق مما إذا كانت العملية مسموح بإرسالها آلياً بناءً على:
     * 1. المفتاح العام (Master Switch)
     * 2. مدة صلاحية العملية (تجاهل العمليات السابقة وقت توقف الواتس)
     * 3. مصفوفة العمليات المفعلة والمعطلة (سندات، قيود، نتائج، تقييمات، ترحيب)
     * 4. إعداد المستخدم نفسه (مفعل أم مستثنى من الاستلام)
     */
    public static function canSendAutomatedOperation(
        string $operationType,
        $user = null,
        $operationTimestamp = null
    ): array {
        $settings = self::getSettings();

        // 1. فحص المفتاح العام (Master Switch)
        if (!$settings['master_switch']) {
            return [
                'allowed' => false,
                'reason' => 'نظام الواتساب معطل بالكامل حالياً من إعدادات الإدارة (Master Switch is OFF).',
            ];
        }

        // 2. فحص مدة صلاحية العملية (تجاهل العمليات السابقة)
        if ($operationTimestamp) {
            $timestamp = is_numeric($operationTimestamp) 
                ? \Carbon\Carbon::createFromTimestamp($operationTimestamp) 
                : \Carbon\Carbon::parse($operationTimestamp);

            $maxHours = $settings['max_age_hours'];
            if ($maxHours > 0 && $timestamp->lt(now()->subHours($maxHours))) {
                return [
                    'allowed' => false,
                    'reason' => "تم تجاوز الحد الأقصى لصلاحية إرسال العملية ({$maxHours} ساعة)؛ أُجريت سابقاً وقت توقف الواتس وتم استبعادها لمنع إزعاج المستخدمين.",
                ];
            }
        }

        // 3. فحص مصفوفة نوع العملية
        $operationKeyMap = [
            'receipt_voucher' => 'notify_receipt',
            'payment_voucher' => 'notify_payment',
            'entitlement' => 'notify_entitlement',
            'exam_result' => 'notify_exam_result',
            'evaluation' => 'notify_evaluation',
            'completion' => 'notify_completion',
            'welcome' => 'notify_welcome',
            'reminder' => 'notify_reminder',
        ];

        $settingKey = $operationKeyMap[$operationType] ?? null;
        if ($settingKey && empty($settings[$settingKey])) {
            return [
                'allowed' => false,
                'reason' => "تم تعطيل إرسال إشعارات هذا النوع من العمليات ({$operationType}) من لوحة الإدارة.",
            ];
        }

        // 4. فحص المستخدم المستهدف (هل هو مستثنى أم لا؟)
        if ($user) {
            if (is_numeric($user)) {
                $user = User::find($user);
            }
            if ($user && isset($user->whatsapp_notifications_enabled) && !$user->whatsapp_notifications_enabled) {
                return [
                    'allowed' => false,
                    'reason' => "تم استثناء المستخدم ({$user->name}) من استلام الرسائل التلقائية بقرار الإدارة.",
                ];
            }
        }

        return [
            'allowed' => true,
            'reason' => 'مسموح بالإرسال',
        ];
    }

    /**
     * تبديل حالة استلام المستخدم للإشعارات التلقائية (تفعيل أو استثناء)
     */
    public static function toggleUserNotifications(int $userId, ?bool $status = null): bool
    {
        $user = User::findOrFail($userId);
        $user->whatsapp_notifications_enabled = $status !== null ? $status : !$user->whatsapp_notifications_enabled;
        $user->save();

        return (bool) $user->whatsapp_notifications_enabled;
    }

    /**
     * القوالب الافتراضية للرسائل التلقائية
     */
    public static function defaultTemplates(): array
    {
        $schoolName = SiteSetting::get('school_name', 'منصة ASOTS التعليمية للغات والترجمة');

        return [
            'welcome' => [
                'title' => '👋 رسالة الترحيب بالمسجلين الجدد',
                'description' => 'تُرسل تلقائياً عند تسجيل أو إضافة طالب أو مستخدم جديد في المنصة',
                'enabled' => (bool) SiteSetting::get('wa_template_welcome_enabled', true),
                'body' => SiteSetting::get('wa_template_welcome_body', 
                    "أهلاً وسهلاً بك عزيزنا {name} في {school_name}! 🎓✨\n\nيسرنا انضمامك إلينا لمواصلة رحلتك التعليمية المتميزة. يمكنك الآن تسجيل الدخول لمنصتك التعليمية ومتابعة دروسك واختباراتك بكل سهولة.\n\n🔗 رابط المنصة: {link}\n📱 هاتف الدعم والاستفسار: {platform_phone}\n\nنتمنى لك تجربة ممتعة وتفوقاً مستمراً!"
                ),
            ],
            'completion' => [
                'title' => '🎓 رسالة استكمال الدورة والشهادة',
                'description' => 'تُرسل عند استكمال الطالب للدورة أو البرنامج بنجاح',
                'enabled' => (bool) SiteSetting::get('wa_template_completion_enabled', true),
                'body' => SiteSetting::get('wa_template_completion_body',
                    "ألف مبروك يا {name}! 🌟🎉\n\nنهنئك في {school_name} على إتمام واجتياز {course} بكل جدارة واستحقاق.\nتم إصدار شهادتك الرسمية المعتمدة ويمكنك استعراضها وتحميلها الآن من حسابك.\n\n🔗 رابط الشهادة: {link}\n\nفخورون بإنجازك ونتمنى لك دوام التقدم والنجاح!"
                ),
            ],
            'congratulations' => [
                'title' => '🎉 رسالة تهنئة بالتميز والنجاح',
                'description' => 'تُرسل عند تفوق الطالب في الاختبارات والأنشطة',
                'enabled' => (bool) SiteSetting::get('wa_template_congratulations_enabled', true),
                'body' => SiteSetting::get('wa_template_congratulations_body',
                    "تهانينا الحارة لك يا {name}! 🏆✨\n\nتتقدم إدارة {school_name} بأسمى آيات التهاني والتقدير لجهودك المتميزة وحصولك على نتائج مشرفة في {course}.\n\nاستمر في التألق والريادة!"
                ),
            ],
            'announcement' => [
                'title' => '📢 إعلان عام وتنبيه دراسي',
                'description' => 'قالب الرسائل الإعلانية الدورية للطلاب والمجموعات',
                'enabled' => (bool) SiteSetting::get('wa_template_announcement_enabled', true),
                'body' => SiteSetting::get('wa_template_announcement_body',
                    "إشعار هام من {school_name} 📢\n\nأهلاً {name}، نود إحاطتكم علماً بالتالي:\n{announcement_text}\n\nشاكرين اهتمامكم والتزامكم الدائم."
                ),
            ],
            'receipt_voucher' => [
                'title' => '💳 سند قبض مالي للطالب',
                'description' => 'تُرسل عند تسديد الطالب لرسوم أو أقساط دراسية',
                'enabled' => (bool) SiteSetting::get('wa_template_receipt_voucher_enabled', true),
                'body' => SiteSetting::get('wa_template_receipt_voucher_body',
                    "مرحباً {name}، نشكركم على سداد الرسوم في {school_name}. تم إصدار سند القبض رقم ({voucher_no}) بمبلغ {amount} بنجاح.\n\n📄 رابط استعراض وطباعة سند القبض المالي:\n{link}"
                ),
            ],
            'payment_voucher' => [
                'title' => '💼 سند صرف مالي للمعلم والمشرف',
                'description' => 'تُرسل عند صرف أتعاب أو رواتب للمعلمين والمشرفين',
                'enabled' => (bool) SiteSetting::get('wa_template_payment_voucher_enabled', true),
                'body' => SiteSetting::get('wa_template_payment_voucher_body',
                    "أستاذنا الفاضل {name}، تم صرف مستحقاتكم المالية بمبلغ {amount} بموجب سند الصرف رقم ({voucher_no}) في {school_name}.\n\n📄 رابط استعراض وطباعة سند الصرف:\n{link}\n\nشكراً لجهودكم وعطائكم المستمر."
                ),
            ],
            'entitlement' => [
                'title' => '📊 إثبات مستحق مالي دائن',
                'description' => 'تُرسل عند قيد مستحقات تدريس أو إشراف أو خدمات بحساب المعلم',
                'enabled' => (bool) SiteSetting::get('wa_template_entitlement_enabled', true),
                'body' => SiteSetting::get('wa_template_entitlement_body',
                    "أستاذنا {name}، تم قيد مستحقات مالية بحسابكم برقم ({voucher_no}) بمبلغ {amount} عن: {statement} في {school_name}.\n\n📄 رابط كشف الحساب والمستند:\n{link}"
                ),
            ],
            'reminder' => [
                'title' => '⏰ تذكير بسداد الأقساط والمستحقات',
                'description' => 'تُرسل لتذكير الطلاب بالأقساط المتبقية',
                'enabled' => (bool) SiteSetting::get('wa_template_reminder_enabled', true),
                'body' => SiteSetting::get('wa_template_reminder_body',
                    "عزيزنا {name}، نود تذكيركم بموعد سداد القسط الدراسي المستحق لدورة {course} بمبلغ {amount}. يرجى التكرم بتسوية القسط لضمان استمرار خدماتكم التعليمية.\n\nشاكرين تعاونكم مع {school_name}."
                ),
            ],
            'exam_result' => [
                'title' => '🎯 نتيجة اختبار وتحديد مستوى',
                'description' => 'تُرسل عند تسليم ورصد نتيجة اختبار الطالب أو اختبار تحديد المستوى',
                'enabled' => (bool) SiteSetting::get('wa_template_exam_result_enabled', true),
                'body' => SiteSetting::get('wa_template_exam_result_body',
                    "عزيزنا الطالب {name} 🎯\n\nتم رصد واحتساب نتيجتك في {course} بنجاح:\n📊 النتيجة المحققة: {score}%\n🏷️ المستوى المقترح: {level}\n\nيرجى مراجعة منصتك لمتابعة تفاصيل دراستك في {school_name}."
                ),
            ],
            'evaluation' => [
                'title' => '⭐ تقرير تقييم الحصة الدراسية',
                'description' => 'تُرسل عند حفظ المعلم لتقرير وحضور وتقييم الحصة التعليمية',
                'enabled' => (bool) SiteSetting::get('wa_template_evaluation_enabled', true),
                'body' => SiteSetting::get('wa_template_evaluation_body',
                    "عزيزنا {name} 📋\n\nتم رصد تقرير الحصة رقم ({session_no}) لمجموعة ({group_name}):\n- حالة الحضور: {status}\n- موضوع الدرس: {topic}\n- النجوم المكتسبة: {stars} ⭐\n\nنتمنى لك دوام التقدم والاجتهاد في {school_name}!"
                ),
            ],
        ];
    }

    /**
     * دمج المتغيرات الديناميكية في نص القالب
     */
    public static function renderTemplate(string $text, array $data = []): string
    {
        $schoolName = SiteSetting::get('school_name', 'منصة ASOTS التعليمية');
        $platformPhone = SiteSetting::get('whatsapp_platform_number', '+967 735 999 101');
        $platformLink = url('/');

        $defaultReplacements = [
            '{school_name}' => $schoolName,
            '{platform_phone}' => $platformPhone,
            '{link}' => $platformLink,
            '{date}' => date('Y/m/d'),
            '{time}' => date('h:i A'),
        ];

        $merged = array_merge($defaultReplacements, $data);

        return str_replace(array_keys($merged), array_values($merged), $text);
    }

    /**
     * إرسال رسالة واتساب (آلياً عبر البوابة أو توليد رابط فوري مع التوثيق في السجل)
     */
    public static function send(
        string $phone,
        string $message,
        ?string $attachmentUrl = null,
        $user = null,
        string $messageType = 'announcement',
        bool $isAutomated = true,
        $operationTimestamp = null
    ): array {
        $settings = self::getSettings();
        $formattedPhone = WhatsAppHelper::formatNumber($phone);
        $userId = ($user instanceof User) ? $user->id : (is_numeric($user) ? (int)$user : null);

        // 1. إذا كانت الرسالة ناجمة عن عملية آلية، نتحقق من مصفوفة الإرسال وصلاحية العملية واستثناء المستخدم والمفتاح العام
        if ($isAutomated) {
            $check = self::canSendAutomatedOperation($messageType, $user, $operationTimestamp);
            if (!$check['allowed']) {
                // توثيق الرسالة كمستبعدة (Suppressed) في سجل الواتس لإحاطة الإدارة علماً بسبب الاستبعاد
                $log = WhatsAppLog::create([
                    'user_id' => $userId,
                    'phone_number' => $formattedPhone ?: $phone,
                    'message_type' => $messageType,
                    'message_body' => $message,
                    'attachment_url' => $attachmentUrl,
                    'status' => 'suppressed',
                    'api_response' => json_encode(['suppressed_reason' => $check['reason']], JSON_UNESCAPED_UNICODE),
                ]);

                return [
                    'success' => false,
                    'skipped' => true,
                    'status' => 'suppressed',
                    'log_id' => $log->id,
                    'reason' => $check['reason'],
                    'message' => $check['reason'],
                    'wa_url' => '',
                    'formatted_phone' => $formattedPhone ?: $phone,
                ];
            }
        }

        if (empty($formattedPhone)) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'رقم الهاتف غير صالح أو فارغ.',
                'wa_url' => '',
            ];
        }

        $waUrl = WhatsAppHelper::buildUrl($formattedPhone, $message);
        $status = 'logged';
        $apiResponse = null;

        // التحقق من تفعيل الإرسال الآلي ووجود بيانات الربط
        if ($settings['auto_send_enabled'] && !empty($settings['api_token']) && $settings['provider'] !== 'web_only') {
            try {
                $dispatchResult = self::dispatchToGateway($settings, $formattedPhone, $message, $attachmentUrl);
                if ($dispatchResult['success']) {
                    $status = 'sent';
                    $apiResponse = json_encode($dispatchResult['response'], JSON_UNESCAPED_UNICODE);
                } else {
                    $status = 'failed';
                    $apiResponse = json_encode($dispatchResult['error'], JSON_UNESCAPED_UNICODE);
                }
            } catch (\Exception $e) {
                Log::error("WhatsApp Gateway Exception: " . $e->getMessage());
                $status = 'failed';
                $apiResponse = $e->getMessage();
            }
        }

        // توثيق الرسالة في سجل الرسائل
        $log = WhatsAppLog::create([
            'user_id' => $userId,
            'phone_number' => $formattedPhone,
            'message_type' => $messageType,
            'message_body' => $message,
            'attachment_url' => $attachmentUrl,
            'status' => $status,
            'api_response' => $apiResponse,
        ]);

        return [
            'success' => in_array($status, ['sent', 'logged']),
            'status' => $status,
            'log_id' => $log->id,
            'wa_url' => $waUrl,
            'formatted_phone' => $formattedPhone,
            'api_response' => $apiResponse,
        ];
    }

    /**
     * الاتصال الفعلي ببوابة مزود خدمة الواتساب السحابية
     */
    protected static function dispatchToGateway(array $settings, string $phone, string $message, ?string $attachmentUrl = null): array
    {
        $provider = $settings['provider'];
        $token = $settings['api_token'];
        $instanceId = $settings['instance_id'];
        $customUrl = $settings['api_url'];

        // 1. UltraMsg Gateway
        if ($provider === 'ultramsg') {
            $url = $customUrl ?: "https://api.ultramsg.com/{$instanceId}/messages/chat";
            $response = Http::timeout(12)->asForm()->post($url, [
                'token' => $token,
                'to' => $phone,
                'body' => $message,
            ]);

            if ($response->successful() && !empty($response->json('sent'))) {
                return ['success' => true, 'response' => $response->json()];
            }

            return ['success' => false, 'error' => $response->body()];
        }

        // 2. GreenAPI Gateway
        if ($provider === 'greenapi') {
            $url = $customUrl ?: "https://api.green-api.com/waInstance{$instanceId}/sendMessage/{$token}";
            $chatId = $phone . '@c.us';
            $response = Http::timeout(12)->post($url, [
                'chatId' => $chatId,
                'message' => $message,
            ]);

            if ($response->successful() && !empty($response->json('idMessage'))) {
                return ['success' => true, 'response' => $response->json()];
            }

            return ['success' => false, 'error' => $response->body()];
        }

        // 3. Wasender / Generic Custom Gateway
        if ($provider === 'wasender' || $provider === 'custom') {
            $url = $customUrl ?: "https://api.wasender.com/api/send-message";
            $response = Http::timeout(12)->withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
            ])->post($url, [
                'phone' => $phone,
                'message' => $message,
                'instance_id' => $instanceId,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'response' => $response->json()];
            }

            return ['success' => false, 'error' => $response->body()];
        }

        // 4. Local Self-Hosted Gateway (Node.js Baileys / WPPConnect on localhost)
        if ($provider === 'local_gateway') {
            $url = $customUrl ?: "http://127.0.0.1:3000/send-message";
            $response = Http::timeout(10)->post($url, [
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'response' => $response->json() ?? ['status' => 'sent']];
            }

            return ['success' => false, 'error' => $response->body() ?: 'تعذر الاتصال بخادم الواتساب المحلي (تأكد من تشغيل خادم المنصة على بورت 3000)'];
        }

        return ['success' => false, 'error' => 'مزود الخدمة غير مدعوم'];
    }

    /**
     * اختبار الاتصال بالبوابة ورقم المنصة
     */
    public static function testConnection(string $testPhone): array
    {
        $settings = self::getSettings();
        $formattedPhone = WhatsAppHelper::formatNumber($testPhone);

        if (empty($formattedPhone)) {
            return [
                'success' => false,
                'message' => 'يرجى إدخال رقم هاتف صالح لاختبار الاتصال.',
            ];
        }

        $testMessage = "🔔 تجربة ربط واتساب منصة ASOTS التعليمية بنجاح!\n\nهذه الرسالة تؤكد نجاح ربط الرقم الرسمي للمنصة ومزود الخدمة ({$settings['provider']}).\nالتاريخ: " . date('Y/m/d h:i A');

        return self::send($formattedPhone, $testMessage, null, null, 'announcement', false);
    }

    /**
     * إرسال حملة جماعية أو مخصصة لمجموعة مستهدفة
     */
    public static function sendCampaign(array $params): array
    {
        $target = $params['target'] ?? 'all_users';
        $messageRaw = $params['message'] ?? '';
        $messageType = $params['message_type'] ?? 'announcement';
        $groupId = $params['study_group_id'] ?? null;
        $courseId = $params['course_id'] ?? null;
        $customPhone = $params['custom_phone'] ?? null;
        $customUserId = $params['custom_user_id'] ?? null;

        $recipients = collect();

        // 1. تحديد قائمة المستلمين بناءً على الفئة المستهدفة
        switch ($target) {
            case 'all_users':
                $recipients = User::whereNotNull('phone_whatsapp')
                    ->where('phone_whatsapp', '!=', '')
                    ->get();
                break;

            case 'all_students':
                $recipients = User::where('role', 'student')
                    ->whereNotNull('phone_whatsapp')
                    ->where('phone_whatsapp', '!=', '')
                    ->get();
                break;

            case 'teachers_and_supervisors':
                $recipients = User::whereIn('role', ['teacher', 'supervisor', 'admin'])
                    ->whereNotNull('phone_whatsapp')
                    ->where('phone_whatsapp', '!=', '')
                    ->get();
                break;

            case 'parents':
                $recipients = User::where('role', 'parent')
                    ->whereNotNull('phone_whatsapp')
                    ->where('phone_whatsapp', '!=', '')
                    ->get();
                break;

            case 'specific_group':
                if ($groupId) {
                    $group = StudyGroup::with('students')->find($groupId);
                    if ($group) {
                        $recipients = $group->students;
                    }
                }
                break;

            case 'specific_course':
                if ($courseId) {
                    // الطلاب المسجلين في مجموعات هذه الدورة
                    $groupStudentIds = \App\Models\StudyGroupStudent::whereHas('studyGroup', fn($q) => $q->where('course_id', $courseId))
                        ->pluck('student_id');
                    $recipients = User::whereIn('id', $groupStudentIds)->get();
                }
                break;

            case 'custom_user':
                if ($customUserId) {
                    $u = User::find($customUserId);
                    if ($u) {
                        $recipients = collect([$u]);
                    }
                }
                break;

            case 'custom_phone':
                if ($customPhone) {
                    $fakeUser = new User([
                        'id' => null,
                        'name' => 'مشترك',
                        'phone_whatsapp' => $customPhone,
                    ]);
                    $recipients = collect([$fakeUser]);
                }
                break;
        }

        $sentCount = 0;
        $failedCount = 0;
        $loggedCount = 0;
        $dispatchedList = [];

        foreach ($recipients as $recipient) {
            $phone = $recipient->phone_whatsapp;
            if (empty($phone)) {
                continue;
            }

            // تخصيص الرسالة للمستلم
            $personalizedMessage = self::renderTemplate($messageRaw, [
                '{name}' => $recipient->name,
                '{phone}' => $recipient->phone_whatsapp,
                '{email}' => $recipient->email ?? '',
            ]);

            $result = self::send(
                $phone,
                $personalizedMessage,
                null,
                $recipient->id,
                $messageType,
                false
            );

            if ($result['status'] === 'sent') {
                $sentCount++;
            } elseif ($result['status'] === 'failed') {
                $failedCount++;
            } else {
                $loggedCount++;
            }

            $dispatchedList[] = [
                'recipient_name' => $recipient->name,
                'phone' => $phone,
                'formatted_phone' => $result['formatted_phone'] ?? '',
                'wa_url' => $result['wa_url'] ?? '',
                'status' => $result['status'],
            ];
        }

        return [
            'total_target' => $recipients->count(),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'logged_count' => $loggedCount,
            'dispatched_list' => $dispatchedList,
        ];
    }
}
