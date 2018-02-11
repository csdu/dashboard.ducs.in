<?php

namespace DUCS\Sankalan\_2018;

use PDO;
use PDOException;

class Database
{
    private $conn;
    public function __construct()
    {
        include 'db.config.php'; // $config
        $db_path = 'mysql:' . 'host=' . $config['db_host'] . ';port=' . $config['db_port'] . ';dbname=' . $config['db_name'];
        try {
            $this->conn = new PDO(
                $db_path,
                $config['db_user_name'],
                $config['db_password']
            );
        } catch (PDOException $e) {
            die('Failed to connect with MySQL: ' . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->conn = null;
    }

    public function get_connection()
    {
        return $this->conn;
    }

    /**
     * prepares and executes a SELECT sql query using the $args
     * @param  string $statement SELECT sql query
     * @param  array $args      associative array of named (:name) parameters
     * @return array             results of query (FALSE if error)
     */
    public function query($statement, $args, $find_one)
    {
        $result;
        try {
            $stmt = $this->conn->prepare($statement);
            if (!$stmt->execute($args)) {
                throw new PDOException("Error Processing Request", 1);
            };
            $result = ($find_one)
                ? $stmt->fetch(PDO::FETCH_ASSOC)
                : $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $stml = null;
        } catch (PDOException $e) {
            return 'Wrong SQL: ' . $statement . ' Error: ' . $e->getMessage();
        }
        return $result;
    }

    /**
     * prepares and executes an INSERT/UPDATE/DELETE sql query using the $args
     * @param  string $statement INSERT/UPDATE/DELETE sql query
     * @param  array $args      associative array of named (:name) parameters
     * @return int             count of documents affected, FALSE if error
     */
    public function modify($statement, $args)
    {
        $stmt = $this->conn->prepare($statement);
        if (!$stmt->execute($args)) {
            return false;
        };
        $result = $stmt->rowCount();
        $stmt->closeCursor();
        $stml = null;
        return $result;
    }
}
