<?php
require_once '../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../src/routes.php';

// echo the headers and send the response
$response->headers->set('X-Powered-By', 'Sid Vishnoi');
$response->send();
