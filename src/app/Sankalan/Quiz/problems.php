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

function saveProblem($question) {
    $params = json_decode($question, true);
    if (isset($params['statement']) and isset($params['choice1']) and isset($params['choice2']) and isset($params['choice3']) and
             isset($params['choice4']) and isset($params['multi_sol']) and isset($params['answer']) and isset($params['event_id'])) {
        $db = new Database();
        $response = $db->modify('INSERT INTO questions (statement, choice1, choice2, choice3, choice4, multi_sol, answer, event_id) VALUES (:stmt, :ch1, :ch2, :ch3, :ch4, :m_sol, :ans, :eid)',
                                ['stmt' => $params['statement'], 'ch1' => $params['choice1'], 'ch2' => $params['choice2'], 'ch3' => $params['choice3'], 'ch4' => $params['choice4'],
                                 'm_sol' => $params['multi_sol'], 'ans' => $params['answer'], "eid" => $params['event_id']]);


        if (!$response) return false;
        return true;
    }
    else {
        return false;
    }
}

function setMultipleProblems($questions) {
    $success = true;
    $response = [];
    foreach ($questions as $question) {
        if (!setProblem($problems)) array_push($response, $question);
    }

    return $response;
}