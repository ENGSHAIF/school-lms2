<?php

require_once __DIR__.'/../app/helpers.php';

use App\Http\Middleware\BlockAiAndScrapersMiddleware;
use App\Http\Middleware\EnforceReadOnlyReviewer;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\EnsureStaffOrAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup('web', BlockAiAndScrapersMiddleware::class);
        $middleware->appendToGroup('web', EnsurePasswordChanged::class);
        $middleware->appendToGroup('web', EnforceReadOnlyReviewer::class);
        $middleware->alias([
            'staff' => EnsureStaffOrAdmin::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'webhook/*',
            'chatbot/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
