<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffOrAdmin
{
    /**
     * حماية شاشات الإدارة والنظام: منع الطلاب وأولياء الأمور من فتح شاشات الإدارة
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'يرجى تسجيل الدخول للوصول إلى هذه الشاشة.');
        }

        $user = Auth::user();

        // 1. منع الطلاب وأولياء الأمور قطعياً من شاشات إدارة النظام
        if ($user->isStudent()) {
            return redirect()->route('student.portal')->with('error', '⛔ عذراً، هذه الشاشة مخصصة لكادر إدارة المنصة والمعلمين فقط.');
        }

        if ($user->isParent()) {
            return redirect()->route('parent.dashboard')->with('error', '⛔ عذراً، هذه الشاشة مخصصة لكادر إدارة المنصة والمعلمين فقط.');
        }

        // 2. إذا حُددت صلاحية معينة للشاشة، نتحقق منها (إلا إذا كان مديراً عاماً)
        if ($permission && !$user->isAdmin() && !$user->hasPermission($permission)) {
            abort(403, 'عذراً، ليس لديك الصلاحية الكافية لفتح هذه الشاشة (' . $permission . ').');
        }

        return $next($request);
    }
}
