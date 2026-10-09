<?php

error_log('DIAGNOSTIC: api/index.php executed');

try {
    $isVercel = getenv('VERCEL') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']);

    if ($isVercel) {
        $tmpStorage = '/tmp/laravel-storage';
        $tmpBootstrapCache = '/tmp/laravel-bootstrap-cache';

        $directories = [
            $tmpStorage . '/app',
            $tmpStorage . '/framework/cache/data',
            $tmpStorage . '/framework/sessions',
            $tmpStorage . '/framework/views',
            $tmpStorage . '/logs',
            $tmpBootstrapCache,
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new RuntimeException('Unable to create writable directory: ' . $directory);
            }
        }

        $environment = [
            'LARAVEL_STORAGE_PATH' => $tmpStorage,
            'APP_CONFIG_CACHE' => $tmpBootstrapCache . '/config.php',
            'APP_EVENTS_CACHE' => $tmpBootstrapCache . '/events.php',
            'APP_PACKAGES_CACHE' => $tmpBootstrapCache . '/packages.php',
            'APP_ROUTES_CACHE' => $tmpBootstrapCache . '/routes.php',
            'APP_SERVICES_CACHE' => $tmpBootstrapCache . '/services.php',
        ];

        foreach ($environment as $key => $value) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    error_log('DIAGNOSTIC ERROR: ' . $e->getMessage());
    error_log($e->getTraceAsString());

    http_response_code(500);
    echo 'Application startup failed. Check Vercel Runtime Logs.';
}
