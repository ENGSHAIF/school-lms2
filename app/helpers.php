<?php

use App\Models\SiteSetting;

if (!function_exists('site_setting')) {
    function site_setting(string $key, $default = null)
    {
        return SiteSetting::get($key, $default);
    }
}
