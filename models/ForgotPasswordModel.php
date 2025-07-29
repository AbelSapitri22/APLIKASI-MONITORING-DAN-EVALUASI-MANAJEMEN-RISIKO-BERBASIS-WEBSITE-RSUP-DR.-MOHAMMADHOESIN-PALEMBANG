<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class ForgotPasswordModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function isAlreadyResetPassword($username)
    {
        $query = mysqli_query($this->dbConnect->connection, "SELECT * FROM forgot_password WHERE username='$username' && status='request'");
        return mysqli_num_rows($query) > 0 ? true : false;
    }

    public function createResetPasswordData($username)
    {
        $date = date('Y-m-d H:i:s');
        $query = mysqli_query($this->dbConnect->connection, "INSERT INTO forgot_password (username, date, status) VALUES ('$username', '$date', 'request')");
        return $query;
    }
}
