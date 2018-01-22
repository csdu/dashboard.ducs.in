<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DUCS\Template;

require_once 'app/utils/general.php';

$request = Request::createFromGlobals();
$uri = rtrim($request->getPathInfo(), '/');

if ('' === $uri) {
    $html = Template::render('sn18/layout', array(
        'title' => 'DUCS',
        'content' => 'Home page',
    ));
    $response = new Response($html);
} elseif (startsWith($uri, '/sankalan')) {
    $sankalan = new DUCS\Sankalan\_2018\Sankalan($uri);
    $response = $sankalan->getResponse();
} else {
    $response = new Response();
    $response->setStatusCode(Response::HTTP_NOT_FOUND);
}

$get404Response = function() use($uri)
{
    $html = '<html><body><h1>Page Not Found</h1>'. $uri .'</body></html>';
    $response = new Response($html, Response::HTTP_NOT_FOUND);
    return $response;
};
