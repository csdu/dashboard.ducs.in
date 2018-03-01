<?php
namespace DUCS\Sankalan\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DUCS\Router;

class Admin extends Router
{
    public function __construct($_uri, $_base_uri = '/admin')
    {
        parent::__construct($_uri, $_base_uri);
    }

    protected function handleRoute()
    {
        $uri = $this->uri;
        switch ($uri) {
            case '':
                $dash = new Dashboard();
                $this->html = $dash->view();
                break;

            case '/login':
                $auth = new Auth();
                if ($this->req->isMethod('GET')) {
                    $this->res = $auth->view();
                } elseif ($this->req->isMethod('POST')) {
                    $this->res = $auth->login();
                }
                break;

            case '/logout':
                $auth = new Auth();
                $this->res = $auth->logout();
                break;

            // TODO
            case '/create-team':
                $team = new CreateTeam();
                $this->res = $team->view();
                break;

            // TODO
            case '/event-register':
                $reg = new EventRegistration();
                $this->res = $reg->view();
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
