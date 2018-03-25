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

        if (startsWith($uri, '/get-problems')) {
            include 'get-problems.php';
            $args = explode("/", $uri); // figure out event name
            if (isset($args[2])) {
                $this->res = new JsonResponse(getProblems($args[2]));
            }
            // else {
            //     // return 400 status
            // }
        }
        else if ($uri === '/submit' and $this->req->isMethod('POST')) {
            $this->res->setContent('quiz home');
        }
        else if ($uri === '/respond' and $this->req->isMethod('POST')) {
            include 'save-response.php';
            
            $this->res->setContent('quiz home');
        }
        else if ($uri === '') {
            $this->res->setContent('quiz home');
        } 
         else {
            $event;
            if (in_array($uri, $this->events)) {
                $event = $uri;
            }
            if (!isset($event)) {
                $this->res->setStatusCode(404);
                return;
            }
            $this->res->setContent('quiz page for : [' . $uri . ']');
        }
    }
}
