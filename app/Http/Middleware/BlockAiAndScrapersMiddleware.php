<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockAiAndScrapersMiddleware
{
    /**
     * قائمة بتوقيعات وتسميات روبوتات الذكاء الاصطناعي ومجمعات البيانات الآلية
     */
    protected array $aiBots = [
        'gptbot',
        'chatgpt-user',
        'chatgpt',
        'claudebot',
        'claude-web',
        'anthropic-ai',
        'perplexitybot',
        'perplexity',
        'bytespider',
        'bytedance',
        'ccbot',
        'diffbot',
        'cohere-ai',
        'google-extended',
        'facebookbot',
        'meta-externalagent',
        'omgilibot',
        'amazonbot',
        'applebot-extended',
        'deepseek',
        'youbot',
        'seekr',
    ];

    /**
     * قائمة بمكتبات وبرمجيات التجريف واستخراج البيانات الآلية (Scrapers & Headless Tools)
     */
    protected array $scrapingTools = [
        'scrapy',
        'python-requests',
        'python-urllib',
        'aiohttp',
        'httpx',
        'beautifulsoup',
        'headlesschrome',
        'phantomjs',
        'puppeteer',
        'playwright',
        'selenium',
        'curl/',
        'wget/',
        'go-http-client',
        'httpclient',
        'java/',
        'postmanruntime',
    ];

    /**
     * مسارات النظام والتحليل المحمية بحظر مشدد حتى ضد محركات البحث العادية
     */
    protected array $protectedPaths = [
        'admin*',
        'help*',
        'accounting*',
        'student*',
        'teacher*',
        'parent*',
        'classroom*',
        'licenses*',
        'api*',
    ];

    /**
     * اعتراض ومعالجة الطلبات الواردة لحماية المنصة من الاستنساخ والذكاء الاصطناعي
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = strtolower($request->header('User-Agent', ''));

        // 1. حظر الطلبات التي لا تحتوي على User-Agent نهائياً (نموذج روبوتات التسلل)
        if (empty(trim($userAgent))) {
            return $this->blockResponse('403 - User-Agent Required', 'الوصول محظور لعدم توفر ترويسة تعريف المتصفح.');
        }

        // 2. التحقق من روبوتات الذكاء الاصطناعي وحظرها فوراً على مستوى المنصة بالكامل
        foreach ($this->aiBots as $botSignature) {
            if (str_contains($userAgent, $botSignature)) {
                return $this->blockResponse('403 - AI Scraping Prohibited', 'عذراً، الزحف البرمجي وتغذية أنظمة الذكاء الاصطناعي بالمحتوى محظور بموجب سياسة حماية الملكية الفكرية لمنصة ASOTS.');
            }
        }

        // 3. التحقق من برمجيات التجريف (Scrapers) ومحاكيات المتصفحات
        foreach ($this->scrapingTools as $tool) {
            if (str_contains($userAgent, $tool)) {
                // استثناء واجهات برمجة التطبيقات الرسمية إن وجدت في المستقبل
                return $this->blockResponse('403 - Automated Access Prohibited', 'الوصول الآلي ومحاولات استخراج البيانات محظورة أمنياً.');
            }
        }

        // 4. استكمال معالجة الطلب الطبيعي
        $response = $next($request);

        // 5. حقن ترويسات الأمان والحماية ضد الفهرسة والتخزين المؤقت للذكاء الاصطناعي
        $isProtectedPath = false;
        foreach ($this->protectedPaths as $pattern) {
            if ($request->is($pattern)) {
                $isProtectedPath = true;
                break;
            }
        }

        if ($isProtectedPath) {
            // منع الفهرسة والتلخيص واستخراج الأكواد من قِبل أي محرك بحث أو أداة ذكاء اصطناعي
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet, noimageindex, notranslate');
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        // ترويسات الأمان العامة (Hardening Headers)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-AI-Content-Declaration', 'no-ai-train, no-scrape');

        return $response;
    }

    /**
     * إرجاع استجابة حظر أمنية وقانونية واضحة
     */
    protected function blockResponse(string $title, string $message): Response
    {
        $html = <<<HTML
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - ASOTS Security Shield</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #001c2e 0%, #003859 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            text-align: center;
        }
        .box {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(245, 130, 32, 0.4);
            border-radius: 24px;
            padding: 40px 30px;
            max-width: 580px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
        }
        h1 {
            color: #F58220;
            font-size: 26px;
            margin-bottom: 15px;
        }
        p {
            color: #d1e7f5;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .badge {
            display: inline-block;
            background: rgba(245, 130, 32, 0.15);
            border: 1px solid #F58220;
            color: #F58220;
            font-size: 11px;
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 20px;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 25px;
            font-size: 11px;
            color: #8daec4;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="badge">🛡️ ASOTS CYBER & INTELLECTUAL PROPERTY SHIELD</div>
        <h1>⛔ تم حظر الوصول البرمجي والآلي</h1>
        <p>{$message}</p>
        <p>
            تخضع منصة ومكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS) لحماية متقدمة لحقوق الملكية الفكرية وبراءات التحليل المؤسسي والتصميم. 
            يمنع منعاً باتاً استنساخ البنية الهيكلية أو التغذية لخوارزميات الذكاء الاصطناعي.
        </p>
        <div class="footer">
            ASOTS Security Enforcement • Status: 403 Forbidden
        </div>
    </div>
</body>
</html>
HTML;

        return response($html, 403, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
        ]);
    }
}
