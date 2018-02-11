<?php
namespace DUCS\Sankalan\_2018;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DUCS\Router;

class Sankalan extends Router
{
    public function __construct($_uri, $_base_uri = '/sankalan')
    {
        parent::__construct($_uri, $_base_uri);
    }

    protected function handleRoute()
    {
        $uri = $this->uri;
        if (startsWith($uri, '/quiz')) {
            $uri = '/quiz';
        }
        switch ($uri) {
            case '':
                $this->html = 'hey you!';
                break;

            case '/ticket':
                $ticket = new Ticket();
                $this->res = $ticket->view();
                break;

            case '/register':
                $register = new Auth\RegisterUser();
                if ($this->req->isMethod('GET')) {
                    $this->res = $register->view();
                } elseif ($this->req->isMethod('POST')) {
                    $this->res = $register->registerUser();
                }
                break;

            case '/auth':
                $google_auth = new Auth\GoogleAuth();
                $this->res = $google_auth->authorize();
                break;

            case '/logout':
                $google_auth = new Auth\GoogleAuth();
                $this->res = $google_auth->logout();
                break;

            case '/login':
                $login = new Auth\LoginUser();
                if ($this->req->isMethod('GET')) {
                    $this->res = $login->view();
                } elseif ($this->req->isMethod('POST')) {
                    $this->res = $login->login();
                }
                break;

            case '/me':
                $this->html = Me::view();
                break;

            case '/quiz':
                $quiz = new Quiz\Quiz($this->uri);
                $this->res = $quiz->getResponse();
                break;

            default:
                if($this->res->isNotFound()) {
                    return;
                }
                $this->res->setStatusCode(200);
                $this->res->setContent(json_encode(array(
                    'data' => 123,
                    'nested' => array('el' => 10),
                )));
                $this->res->headers->set('Content-Type', 'application/json');
                break;
        }
    }
}
