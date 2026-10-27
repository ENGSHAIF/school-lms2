@echo off
chcp 65001 >nul
title ASOTS School LMS Server
color 0a

echo ===================================================================
echo               تشغيل نظام ASOTS للتعليم والتدريب
echo ===================================================================
echo.

cd /d "%~dp0"

REM 1. التحقق من تشغيل خدمة MySQL
echo [1/3] فحص قاعدة البيانات MySQL...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe" >nul
if "%ERRORLEVEL%"=="0" (
    echo   [✓] قاعدة البيانات MySQL قيد التشغيل بالفعل.
) else (
    echo   [+] جارٍ تشغيل خدمة MySQL من Laragon...
    if exist "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe" (
        start /B "" "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe" --datadir="C:\laragon\data\mysql-8.4"
        timeout /t 3 /nobreak >nul
    ) else (
        echo   [!] تنبيه: يرجى فتح Laragon والضغط على Start All لتشغيل MySQL.
    )
)

REM 2. تشغيل خدمة الواتساب المحلية (Port 3000)
echo [2/3] فحص خدمة الواتساب المحلية...
tasklist /FI "WINDOWTITLE eq ASOTS WhatsApp Gateway*" 2>NUL | find /I /N "cmd.exe" >nul
if "%ERRORLEVEL%" NEQ "0" (
    echo   [+] جارٍ تشغيل بوابة الواتساب على المنفذ 3000...
    start "ASOTS WhatsApp Gateway" /D "%~dp0whatsapp-gateway" /min node server.js
) else (
    echo   [✓] بوابة الواتساب قيد التشغيل بالفعل.
)

REM 3. فتح المتصفح بوضع الحماية من الشاشة الزرقاء (Disable GPU)
echo [3/3] جارٍ فتح النظام في المتصفح بأمان...
if exist "C:\Program Files\Google\Chrome\Application\chrome.exe" (
    start "" "C:\Program Files\Google\Chrome\Application\chrome.exe" --disable-gpu http://127.0.0.1:8000
) else (
    start http://127.0.0.1:8000
)

echo.
echo ===================================================================
echo   رابط النظام:  http://127.0.0.1:8000
echo   رابط الواتساب: http://127.0.0.1:3000
echo   ملاحظة: لإيقاف السيرفر اضغط Ctrl + C أو أغلق هذه النافذة
echo ===================================================================
echo.

REM 4. تشغيل خادم Laravel
php artisan serve --host=127.0.0.1 --port=8000
if %ERRORLEVEL% NEQ 0 (
    if exist "C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" (
        "C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" artisan serve --host=127.0.0.1 --port=8000
    )
)

pause
