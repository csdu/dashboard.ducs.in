<?php

namespace DUCS\Sankalan\Admin;
use \DUCS\Sankalan\Database;

class Team {
    static function create($team) {
        $db = new Database();
        if (isset($team['tname']) && isset($team['user1']) && isset($team['user2']) && isset($team['user3']) && isset($team['user4'])) {
            $response = $db->modify('INSERT INTO teams (tname, user1, user2, user3, user4) 
                                     VALUES (:tname, :user1, :user2, :user3, :user4)', $team);
                        
            if (!$response) return false;
                return true;
        }

        return false;
    }

    static function listAll() {
        $db = new Database();
        $teams = $db->query('SELECT * FROM teams', [], false);
        return $teams;
    }

    static function get($id) {
        $db = new Database();
        $team = $db->query('SELECT * FROM teams where tid = :tid', ['tid' => $id], true);
        return $team;
    }
}

?>