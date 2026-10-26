<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use App\Models\SiteSetting;

if (!function_exists('site_setting')) {
    function site_setting($key, $default = null) {
        return SiteSetting::get($key, $default);
    }
}

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('siteSettings', SiteSetting::allCached());
        });

        // مدير النظام أو أي مستخدم ينتمي لمجموعة الإدارة العليا (Admin) يملك كافة الصلاحيات تلقائياً ودائماً بدون استثناء
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin() || (optional($user->permissionGroup)->isAdminGroup())) {
                return true;
            }
        });

        // ربط فحص الصلاحيات ببقية مجموعات الصلاحيات والمستخدمين
        Gate::after(function ($user, $ability, $result) {
            if ($result === true) {
                return true;
            }
            return $user->hasPermission($ability);
        });
    }
}
