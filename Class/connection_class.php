<?php
class DatabaseConnection
{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "rentrover";

    public $conn;


    public function __construct()
    {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }

    public function closeConnection()
    {
        $this->conn->close();
    }

    public function setKeyValue($key, $value)
    {
        $this->$key = $this->conn->real_escape_string($value);
    }

    public function getKeyValue($key)
    {
        return $key;
    }
}