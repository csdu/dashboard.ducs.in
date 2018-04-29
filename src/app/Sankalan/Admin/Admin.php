<?php
namespace DUCS\Sankalan\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Component\HttpFoundation\JsonResponse;
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

        if (startsWith($uri, '/team')) {
            if ($uri === '/team/all') {
                $this->res = new JsonResponse(Team::listAll());
            }
            else if ($this->req->isMethod('GET')) {
                $args = explode("/", $uri);
                if (isset($args[2])) {
                    $this->res= new JsonResponse(Team::get($args[2]));
                }
            }
            if ($this->req->isMethod('POST')) {
                $team = json_decode($this->req->getContent(), true);
                $status = Team::create($team) ? 204 : 406;
                $this->res->setStatusCode($status);
            }
        }

        else if (startsWith($uri, '/event')) {
            if ($uri === '/event/all') {
                $this->res = new JsonResponse(Event::listAll());
            }
            else if ($this->req->isMethod('GET')) {
                $args = explode("/", $uri);
                if (isset($args[2])) {
                    $this->res= new JsonResponse(Event::get($args[2]));
                }
            }
            if ($this->req->isMethod('POST')) {
                $event = json_decode($this->req->getContent(), true);
                $status = Event::create($event) ? 204 : 406;
                $this->res->setStatusCode($status);
            }
        }

        else {
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

                default:
                    if($this->res->isNotFound()) {
                        return;
                    }
                    $this->res->setStatusCode(400);
                    break;
            }
        }
    }
}
