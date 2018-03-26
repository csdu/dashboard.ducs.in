<?php

use \DUCS\Sankalan\Database;

/**
 * returns problems json based on event
 * @param  string $event event id
 * @return array        list of problems
 */
function saveResponse($content)
{
    $params =  json_decode($content, true);
    if (isset($params['ques_id']) and isset($params['event_id']) and isset($params['team_id']) and isset($params['attempt'])) {
        $db = new Database();
        $response = $db->modify('INSERT INTO submissions (tid, eid, qid, attempt)
                                    VALUES (:tid, :eid, :qid, :attempt)
                                    ON DUPLICATE KEY UPDATE
                                    tid = :tid,
                                    eid = :eid,
                                    qid = :qid,
                                    attempt = :attempt', 
                    ['tid' => $params['team_id'], 'eid' => $params['event_id'], 'qid' => $params['ques_id'], 'attempt' => $params['attempt']]);
        
        if (!$response) return false;
        return true;
    }
    else {
        return false;
    }
}

function getResponse($tid, $eid)
{
    $db = new Database();
    $attempts = $db->query('SELECT qid, attempt FROM submissions WHERE (tid = :tid) AND (eid = :eid)', ['tid' => $tid, 'eid' => $eid], false);
    $event = $db->query('SELECT name FROM events WHERE event_id = :event_id', ['event_id' => $eid], true);
    $team = $db->query('SELECT tname FROM teams WHERE tid = :tid', ['tid' => $tid], true);

    return [
        'team' => $team,
        'event' => $event,
        'attempts' => $attempts,
    ];
}