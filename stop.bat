@echo off
chcp 65001 >nul
title ASOTS - Stop Server
color 0c

echo ===================================================================
echo               إيقاف خدمات نظام ASOTS
echo ===================================================================
echo.

taskkill /F /FI "WINDOWTITLE eq ASOTS WhatsApp Gateway*" 2>nul
taskkill /F /IM php.exe /FI "WINDOWTITLE eq ASOTS School LMS Server*" 2>nul

echo [✓] تم إيقاف خدمات النظام بنجاح.
timeout /t 2 >nul
