<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = '/home/u850419603/sarh/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader (absolute path — Hostinger bridge)
require '/home/u850419603/sarh/vendor/autoload.php';

// Bootstrap Laravel and handle the request (absolute path — Hostinger bridge)
(require_once '/home/u850419603/sarh/bootstrap/app.php')
    ->handleRequest(Request::capture());
