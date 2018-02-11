<?php
require_once '../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// modify the following hosts before deployment
define('HOST_NAME', 'http://dash.ducs.in/');
define('ASSETS_HOST_NAME', 'http://localhost:8000/');

require_once '../src/routes.php';

// echo the headers and send the response
$response->headers->set('X-Powered-By', 'Sid Vishnoi');

if($response->isNotFound()) {
    $response = $get404Response();
}

$response->send();
