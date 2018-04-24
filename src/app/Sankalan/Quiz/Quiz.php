<?php

namespace DUCS\Sankalan\Quiz;

use DUCS\Router;
use \Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Manages quiz events at Sankalan
 */
class Quiz extends Router
{
    private $events;
    function __construct($_uri, $_base_uri = '/quiz')
    {
        parent::__construct($_uri, $_base_uri);
        $this->events = ['/algoholics', ];
    }

    protected function handleRoute()
    {
        $uri = $this->uri;

        if (startsWith($uri, '/problems')) {
            $args = explode("/", $uri);
            if (!isset($args[2])) {
                $this->res->setStatusCode(404);
            }
            else {
                $event_id = $args[2];

                if ($this->req->isMethod('POST')) {
                    $result = Problems::addByEventID($args[2]);
                    if (empty($result)) $this->res->setStatusCode(204);
                    else $this->res = new JsonResponse($result);
                }
                else if ($this->req->isMethod('GET')) {
                    $result = new JsonResponse(Problems::listByEventID($args[2]));
                    if ($result === false) $this->res->setStatusCode(400);
                    else $this->res = $result;
                }
            }
        }

        else if (startsWith($uri, '/response')) {
            $args = explode("/", $uri);

            if ($this->req->isMethod('POST')) {
                $status = (Problems::makeAttempt($this->req->getContent())) ? 204 : 406;
                $this->res->setStatusCode($status);
            }
            else if ($this->req->isMethod('GET') && isset($args[2]) && isset($args[3])) {
                $tid = $args[2]; $eid = $args[3];
                $this->res = new JsonResponse(Problems::getAttempts($tid, $eid));
            }
            else {
                $this->res->setStatusCode(404);
            }
        }

        else if ($uri === '/submit') {
            if ($this->req->isMethod('POST')) {
                $params = json_decode($this->req->getContent(), true);
                if (isset($params['tid']) && isset($params['eid'])) {
                    $status = (Events::makeSubmission($params['tid'], $params['eid'])) ? 204 : 406;
                    $this->res->setStatusCode($status);
                }
                else {
                    $this->res->setStatusCode(406);
                }
            }
            else {
                $this->res->setStatusCode(404);
            }
        }

        else if (startsWith($uri, '/score')) {
            $args = explode("/", $uri);

            if ($this->req->isMethod('GET') && isset($args[2]) && isset($args[3])) {
                $tid = $args[2]; $eid = $args[3];
                $this->res = new JsonResponse(Events::score($tid, $eid));
            }
            else {
                $this->res->setStatusCode(404);
            }
        } 

        else {
            $event;
            if (in_array($uri, $this->events)) {
                $event = $uri;
            }
            if (!isset($event)) {
                $this->res->setStatusCode(400);
                return;
            }
            $this->res->setContent('quiz page for : [' . $uri . ']');
        }
    }
}
