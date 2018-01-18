<?php
namespace DUCS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Router
{
    protected $uri;
    protected $html;
    protected $res;
    protected $req;
    public function __construct($_uri, $_base_uri)
    {
        $this->req = Request::createFromGlobals();
        $this->uri = rtrim(
            substr(
                $_uri,
                strpos($_uri, $_base_uri) + strlen($_base_uri)
            ),
            '/'
        );
        $this->res = new Response('response not set');
    }

    abstract protected function handleRoute();

    public function getResponse()
    {
        $this->handleRoute();
        if (isset($this->html)) {
            $this->res->setContent($this->html);
        }
        return $this->res;
    }
}
