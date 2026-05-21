<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$appDir = __DIR__ . '/vending-machine-api';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if (str_starts_with($uri, '/api')) {
    if (file_exists($maintenance = "$appDir/storage/framework/maintenance.php")) {
        require $maintenance;
    }
    require "$appDir/vendor/autoload.php";
    $app = require_once "$appDir/bootstrap/app.php";
    $app->handleRequest(Request::capture());
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
if (file_exists(__DIR__ . '/spa.html')) {
    readfile(__DIR__ . '/spa.html');
} else {
    echo '<p>Frontend not deployed yet.</p>';
}
