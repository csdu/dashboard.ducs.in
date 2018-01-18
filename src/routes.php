<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DUCS\Template;

require_once 'app/utils.php';

$request = Request::createFromGlobals();
$uri = rtrim($request->getPathInfo(), '/');

if ('' === $uri) {
    $html = Template::render('layout', array(
        'title' => 'DUCS',
        'content' => 'Home page',
    ));
    $response = new Response($html);
} elseif (startsWith($uri, '/sankalan')) {
    $sankalan = new DUCS\Sankalan\_2018\Sankalan($uri);
    $response = $sankalan->getResponse();
} else {
    $html = '<html><body><h1>Page Not Found</h1>'.$uri.'</body></html>';
    $response = new Response($html, Response::HTTP_NOT_FOUND);
}
