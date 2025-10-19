<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\EnsureZohoConnected;
use App\Http\Middleware\VerifyZohoToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        /*
        |--------------------------------------------------------------------------
        | Global & API Middleware
        |--------------------------------------------------------------------------
        |
        | Here we define our application-wide HTTP middleware configuration
        | for both web and API requests, using Laravel 12's new fluent API.
        |
        */

        // ✅ Enable CORS for API requests
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);



        // ✅ Custom middleware aliases (short names for routes)
        $middleware->alias([
            'zoho.connected' => EnsureZohoConnected::class,
            'zoho.token'     => VerifyZohoToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /*
        |--------------------------------------------------------------------------
        | Exception Handling
        |--------------------------------------------------------------------------
        |
        | Register global exception handling or custom renderers here if needed.
        |
        */
    })
    ->create();
