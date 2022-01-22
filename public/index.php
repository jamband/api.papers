<?php

declare(strict_types=1);

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));
const MAINTENANCE_PATH = __DIR__ . '/../storage/framework/maintenance.php';

if (file_exists(MAINTENANCE_PATH)) {
    require MAINTENANCE_PATH;
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$request = Request::capture();
$kernel->terminate($request, $kernel->handle($request)->send());
