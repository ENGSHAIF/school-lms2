<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->must_change_password) {
                // السماح بالوصول فقط لصفحة تغيير كلمة المرور وتسجيل الخروج
                $allowedRoutes = [
                    'password.force_change',
                    'password.force_change.update',
                    'logout',
                ];

                if (!$request->routeIs($allowedRoutes)) {
                    return redirect()->route('password.force_change')
                        ->with('warning', 'عزيزي المستخدم: تم تغيير كلمة المرور لحسابك من قبل إدارة النظام. للحفاظ على أمان حسابك، يجب عليك تعيين كلمة مرور جديدة خاصة بك الآن للمتابعة.');
                }
            }
        }

        return $next($request);
    }
}
