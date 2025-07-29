<?php
class DBConnect
{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "smr";
    public $connection;

    function __construct()
    {
        $this->connection = mysqli_connect($this->host, $this->username, $this->password, $this->database);
    }
}
