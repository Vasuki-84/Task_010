<?php

class Database
{
    public $conn;

    public function connect()
    {
        $host = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        $db   = $_ENV['DB_NAME'];

        $port = 3308;

        $this->conn = mysqli_connect(
            $host,
            $user,
            $pass,
            $db,
            $port
        );

        if (!$this->conn) {
            die("DB Connection Failed: " . mysqli_connect_error());
        }

        return $this->conn;
    }
}

?>