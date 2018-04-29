<?php
namespace DUCS\Sankalan;

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
        else if (startsWith($uri, '/admin')) {
            $uri = '/admin';
        }
        switch ($uri) {
            case '':
                $this->html = Me::view();
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

            case '/quiz':
                $quiz = new Quiz\Quiz($this->uri);
                $this->res = $quiz->getResponse();
                break;

            case '/admin':
                $quiz = new Admin\Admin($this->uri);
                $this->res = $quiz->getResponse();
                break;

            default:
                if($this->res->isNotFound()) {
                    return;
                }
                $this->res->setStatusCode(400);
                break;
        }
    }
}
