<?php

namespace Lib\database;

use mysqli;

class MySQLAccess
{
    private mysqli $conn;

    public function __construct()
    {
        $this->conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

        if ($this->conn->connect_error) {
            die("Connection to mysql failed: " . $this->conn->connect_error);
        }
    }

    public function execPreparedQuery(string $query, array $args)
    {
//        echo '<br>'. $query . '<br>';
        $stmt = $this->conn->prepare($query);
        if ( false===$stmt ) {
            die('prepare() failed: ' . htmlspecialchars($this->conn->error));
        }
        if (count($args) != 0)
            $stmt->bind_param(str_repeat('s', count($args)), ...$args);
        $stmt->execute();
//        var_dump($stmt);
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : false;
    }
}