<?php

namespace DUCS\Sankalan\Quiz;

use \DUCS\Sankalan\Database;

/**
 * returns problems json based on event
 * @param  string $event event id
 * @return array        list of problems
 */

class Events {
    static function list() {
        $db = new Database();
        $event = $db->query('SELECT * FROM events', [], true);
    
        return $event;
    }

    static function score($tid, $eid) {
        $db = new Database();
        $score = $db->query('SELECT score FROM scores WHERE (tid = :tid) AND (eid = :eid)', ['tid' => $tid, 'eid' => $eid], true);
        $event = $db->query('SELECT name FROM events WHERE event_id = :event_id', ['event_id' => $eid], true);
        $team = $db->query('SELECT tname FROM teams WHERE tid = :tid', ['tid' => $tid], true);
    
        return [
            'team' => $team,
            'event' => $event,
            'score' => $score,
        ];
    }

    static function makeSubmission($tid, $eid) {
        $db = new Database();
        $score = $db->query('SELECT COUNT(qid) as score from 
                    (SELECT questions.qid as qid FROM questions INNER JOIN submissions ON questions.qid=submissions.qid AND questions.answer=submissions.attempt) 
                    AS Score;', [], true);

        if (!$score) return false;

        $response = $db->modify('INSERT INTO scores (tid, eid, score)
                                    VALUES (:tid, :eid, :score)
                                    ON DUPLICATE KEY UPDATE
                                    tid = :tid,
                                    eid = :eid,
                                    score = :score',
                    ['tid' => $tid, 'eid' => $eid, 'score' => $score['score']]);

        if (!$response) return false;
        return true;
    }
}