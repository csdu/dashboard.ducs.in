<?php

namespace DUCS\Sankalan\Quiz;

use \DUCS\Sankalan\Database;

/**
 * returns problems json based on event
 * @param  string $event event id
 * @return array        list of problems
 */

class Problems {
    static function listByEventID($event_id) {
        $db = new Database();
        $event = $db->query('SELECT name FROM events WHERE event_id = :event_id', ['event_id' => $event_id], true);

        if (!$event) return false;

        $problems = $db->query('SELECT qid, statement, choice1, choice2, choice3, choice4, multi_sol 
                                FROM questions WHERE event_id = :event_id', ['event_id' => $event_id], false);

        return [
            'event' => $event,
            'problems' => $problems,
        ];
    }
    
    static private function add($question) {
        if (isset($params['statement']) and isset($params['choice1']) and isset($params['choice2']) and isset($params['choice3']) and
                     isset($params['choice4']) and isset($params['multi_sol']) and isset($params['answer']) and isset($params['event_id'])) {
            $db = new Database();
            $response = $db->modify('INSERT INTO questions (statement, choice1, choice2, choice3, choice4, multi_sol, answer, event_id)
                                     VALUES (:statement, :choice1, :choice2, :choice3, :choice4, :multi_sol, :answer, :event_id)',
                                    ['statement' => $params['statement'], 'choice1' => $params['choice1'], 'choice2' => $params['choice2'],
                                     'choice3' => $params['choice3'], 'choice4' => $params['choice4'], 'multi_sol' => $params['multi_sol'],
                                      'answer' => $params['answer'], "event_id" => $params['event_id']]);
    
    
            if (!$response) return false;
            return true;
        }
        else {
            return false;
        }
    }
    
    static function addByEventID($content, $event_id) {
        $question_list = json_decode($content, true);
        $failed = [];
        foreach ($questions as $question) {
            $question['event_id'] = $event_id;
            if (add($problems) === false) array_push($response, $question);
        }
    
        return $response;
    }

    static function makeAttempt($content)
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

    static function getAttempts($tid, $eid)
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
}

