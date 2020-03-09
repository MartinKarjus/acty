<?php

class Db
{
    private $host = 'db';
    private $user = 'user';
    private $password = 'pass';
    private $db = 'acty_db';
    private $conn;

    public function connect()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->db);
        if ($this->conn->connect_error) {
            echo 'connection failed' . $this->conn->connect_error;
        }

        return $this->conn;
    }

}

?>