
<?php

error_log('DIAGNOSTIC: api/index.php executed');

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    error_log('DIAGNOSTIC ERROR: ' . $e->getMessage());
    error_log($e->getTraceAsString());

    http_response_code(500);

    echo 'Application startup failed. Check Vercel Runtime Logs.';
}