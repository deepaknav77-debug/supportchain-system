
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode.
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader.
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request.
 /** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Handle the request and log any runtime exception.
try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    error_log('Laravel request error: ' . $e->getMessage());
    error_log($e->getTraceAsString());

    http_response_code(500);

    echo 'Internal Server Error. Please check Vercel Runtime Logs.';
}