<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class UsersModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function getUserDetail($username)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT 
            users.*, 
            units.unit as unit,
            direktorat.name as directorate_name
        FROM users
        LEFT JOIN units ON users.unit_id = units.id
        LEFT JOIN direktorat ON users.direktorat_id = direktorat.id
        WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getUsers()
    {
        $stmt = $this->dbConnect->connection->prepare("
        SELECT 
            users.*,
            units.unit AS unit_name,
            direktorat.name AS direktorat_name
        FROM 
            users
        LEFT JOIN 
            units ON users.unit_id = units.id
        LEFT JOIN 
            direktorat ON users.direktorat_id = direktorat.id
    ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function getUsername()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT id, username, name FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalUsers()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function createUser($username, $name, $address, $role, $unit_id = null, $direktorat_id = null)
    {
        $isActive = true;
        $password = md5($username);

        $stmt = $this->dbConnect->connection->prepare(
            "INSERT INTO users (username, name, address, role, unit_id, direktorat_id, password, is_active) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("ssssiisi", $username, $name, $address, $role, $unit_id, $direktorat_id, $password, $isActive);
        return $stmt->execute();
    }

    public function updateUser($id, $name, $address, $isActive, $unit_id = null, $direktorat_id = null)
    {
        $fields = "name = ?, address = ?, is_active = ?";
        $params = [$name, $address, $isActive];
        $types = "ssi";

        if (!empty($unit_id)) {
            $fields .= ", unit_id = ?";
            $params[] = (int)$unit_id;
            $types .= "i";
        }

        if (!empty($direktorat_id)) {
            $fields .= ", direktorat_id = ?";
            $params[] = (int)$direktorat_id;
            $types .= "i";
        }

        $fields .= " WHERE id = ?";
        $params[] = (int)$id;
        $types .= "i";

        $query = "UPDATE users SET $fields";
        $stmt = $this->dbConnect->connection->prepare($query);

        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    public function deleteUser($id)
    {
        $stmt = $this->dbConnect->connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function isUsernameAlready($username)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function isUserActive($username)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function isValidPassword($username, $password)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $this->verifyPassword($row['password'], $password);
        } else {
            return false;
        }
    }

    public function changePassword($username, $password)
    {
        $stmt = $this->dbConnect->connection->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $password, $username);
        return $stmt->execute();
    }

    public function login($username, $password)
    {
        $user = $this->getUserDetail($username);
        if ($user && $this->verifyPassword($user['password'], $password)) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['name']       = $user['name'];
            $_SESSION['username']   = $user['username'];
            $_SESSION['role']       = $user['role'];
            $_SESSION['unit_id']    = $user['unit_id'];

            return $user;
        } else {
            return false;
        }
    }

    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
            return true;
        } else {
            return false;
        }
    }

    public function verifyPassword($password, $hashedPassword)
    {
        // Verify if the password matches the hashed password
        return $password === $hashedPassword;
    }

    public function isUserAlreadyLogin()
    {
        return isset($_SESSION['user_id']);
    }

    private function password_validation($username, $old_password)
    {
        return $this->isValidPassword($username, $old_password);
    }
}
