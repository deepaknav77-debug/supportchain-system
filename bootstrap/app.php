<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: '*',
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PREFIX
        );
        
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'activity_log' => \App\Http\Middleware\ActivityLogMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

if (getenv('VERCEL') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $app->useBootstrapPath('/tmp/laravel-bootstrap-cache');
    $app->useStoragePath('/tmp/laravel-storage');
}

return $app;