<?php

use \DUCS\Sankalan\Database;

/**
 * returns problems json based on event
 * @param  string $event event id
 * @return array        list of problems
 */
function getProblems($event_id)
{
    $db = new Database();
    $event = $db->query('SELECT name FROM events WHERE event_id = :event_id', ['event_id' => $event_id], true);

    if (!$event) die("Invalid ID");

    $problems = $db->query('SELECT qid, statement, choice1, choice2, choice3, choice4, multi_sol FROM questions WHERE event_id = :event_id', ['event_id' => $event_id], false);

    return [
        'event' => $event,
        'problems' => $problems,
    ];
}

function getEvents() {
    $db = new Database();
    $event = $db->query('SELECT * FROM events', [], true);

    return $event;
}
