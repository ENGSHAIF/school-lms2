<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceReadOnlyReviewer
{
    /**
     * حماية الدورة المستندية: تقييد حساب الاستعراض ليكون للقراءة والمشاهدة فقط (Read-Only)
     * يمنع أي عمليات إضافة أو تعديل أو حذف، ويسمح بالاطلاع الكامل والطباعة.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isReviewer()) {
            if (! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
                // السماح بتسجيل الخروج
                if ($request->routeIs('logout')) {
                    return $next($request);
                }

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'عذراً، هذا الحساب في وضع الاستعراض والمشاهدة الشاملة فقط (Read-Only). لا يمكن حفظ أو تعديل أي بيانات.',
                    ], 403);
                }

                return back()->with('warning', '⚠️ تنبيه: هذا الحساب في وضع الاستعراض والمشاهدة الشاملة فقط (Read-Only). يمكنك تصفح كافة أقسام النظام والتقارير والدورة المستندية، ولكن لا يُسمح بعمليات التعديل أو الحفظ أو الحذف.');
            }

            // 2. منع استخراج أو تصدير البيانات الخام مجمعاً (Bulk Data Dumps & CSV Exports)
            if ($request->is('*export*') || $request->is('*backup*') || $request->is('*dump*')) {
                return back()->with('warning', '⚠️ تنبيه أمني: تصدير أو سحب البيانات مجمعاً محظور لحساب الاستعراض لحماية سرية وأمان المنصة.');
            }
        }

        return $next($request);
    }
}
