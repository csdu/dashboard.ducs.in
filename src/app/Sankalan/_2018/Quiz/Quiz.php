<?php

namespace DUCS\Sankalan\_2018\Quiz;

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

        switch ($uri) {
            case '':
                $this->res->setContent('quiz home');
                break;

            case '/get-problems':
                include 'get-problems.php';
                $event = 'algoholics'; // figure out event name
                $this->res = new JsonResponse(getProblems($event));
                break;

            default:
                $event;
                if (in_array($uri, $this->events)) {
                    $event = $uri;
                }
                if (!isset($event)) {
                    $this->res->setStatusCode(404);
                    return;
                }
                $this->res->setContent('quiz page for : [' . $uri . ']');
                break;
        }

    }
}
