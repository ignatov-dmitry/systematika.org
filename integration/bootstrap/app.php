<?php

use App\Http\Middleware\AuthByKey;
use App\Http\Middleware\isAdminUser;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.key'  => AuthByKey::class,
            'auth'      => Authenticate::class,
            'admin'     => isAdminUser::class
        ]);
        $middleware->priority([
            AuthByKey::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'user-notification/get-updates'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
