<?php
namespace DUCS\Sankalan\Admin;
use \DUCS\Sankalan\Database;

class Event {
    static function create($event) {
        $db = new Database();

        if (isset($event['name'])) {
            $response = $db->modify('INSERT INTO events (name) VALUES (:name)',  $event);
                        
            if (!$response) return false;
                return true;
        }

        return false;
    }

    static function listAll() {
        $db = new Database();
        $teams = $db->query('SELECT * FROM events', [], false);
        return $teams;
    }

    static function get($id) {
        $db = new Database();
        $team = $db->query('SELECT * FROM events where event_id = :eid', ['eid' => $id], true);
        return $team;
    }
}
?>