<?php
require_once '../vendor/autoload.php';

// modify the following hosts before deployment
if ($_SERVER['SERVER_ADDR'] === '127.0.0.1') {
    // for local development
    define('HOST_NAME', 'http://dash.ducs.in/');
    define('ASSETS_HOST_NAME', 'http://localhost:8000/');
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    define('HOST_NAME', 'https://dashboard.ducs.in/');
    define('ASSETS_HOST_NAME', 'https://cdn.ducs.in/');
}

require_once '../src/routes.php';

// echo the headers and send the response
$response->headers->set('X-Powered-By', 'Sid Vishnoi');

if($response->isNotFound()) {
    $response = $get404Response();
}

$response->send();
