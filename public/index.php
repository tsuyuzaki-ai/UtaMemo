<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// サブディレクトリ対応: REQUEST_URIから/utamemoを削除
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/utamemo') === 0) {
    $_SERVER['REQUEST_URI'] = str_replace('/utamemo', '', $_SERVER['REQUEST_URI']);
    if ($_SERVER['REQUEST_URI'] === '') {
        $_SERVER['REQUEST_URI'] = '/';
    }
    // SCRIPT_NAMEも調整
    if (isset($_SERVER['SCRIPT_NAME']) && strpos($_SERVER['SCRIPT_NAME'], '/utamemo') === 0) {
        $_SERVER['SCRIPT_NAME'] = str_replace('/utamemo', '', $_SERVER['SCRIPT_NAME']);
    }
    // PHP_SELFも調整（セッションやルーティングに影響）
    if (isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], '/utamemo') === 0) {
        $_SERVER['PHP_SELF'] = str_replace('/utamemo', '', $_SERVER['PHP_SELF']);
    }
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
