@echo off
title ASOTS Local WhatsApp Gateway (بوابة الواتساب المحلي المجاني)
color 0a
echo ====================================================================
echo         تشغيل خادم بوابة الواتساب المحلي المجاني - منصة ASOTS
echo ====================================================================
echo.
cd /d %~dp0

if not exist node_modules (
    echo [1/2] تثبيت الحزم الاساسية...
    call npm install express cors @whiskeysockets/baileys qrcode pino --no-audit --no-fund
)

echo [2/2] بدء تشغيل الخادم على بورت 3000...
echo.
node server.js
pause
