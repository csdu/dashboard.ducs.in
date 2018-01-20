<?php

/**
 * returns problems json based on event
 * @param  string $event event id
 * @return array        list of problems
 */
function getProblems($event)
{
    return [
        'event' => $event,
        'problems' => [],
    ];
}
