<?php

class Database {

    private $host = "localhost";
    private $dbname = "library_db";
    private $username = "root";
    private $password = "";
    private $port = "3307";

    public $conn;

    public function connect() {

        $this->conn = null;

        try {

            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->dbname}",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die('Database connection failed.');
        }

        return $this->conn;
    }
}