<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class DirectorateModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function getDirectorate($id)
    {
        $stmt = $this->dbConnect->connection->prepare("
            SELECT * FROM direktorat 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getDirectorates()
    {
        $stmt = $this->dbConnect->connection->prepare("
            SELECT * FROM direktorat
            ORDER BY id ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalDirectorates()
    {
        $stmt = $this->dbConnect->connection->prepare("
            SELECT COUNT(*) as total FROM direktorat
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function createDirectorate($name)
    {
        $stmt = $this->dbConnect->connection->prepare("
            INSERT INTO direktorat (name) 
            VALUES (?)
        ");
        $stmt->bind_param('s', $name);
        return $stmt->execute();
    }

    public function updateDirectorate($id, $name)
    {
        $stmt = $this->dbConnect->connection->prepare("
            UPDATE direktorat 
            SET name = ? 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }

    public function deleteDirectorate($id)
    {
        $stmt = $this->dbConnect->connection->prepare("
            DELETE FROM direktorat 
            WHERE id = ?
        ");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function getUnitsByDirectorate($directorateId)
    {
        $stmt = $this->dbConnect->connection->prepare("
            SELECT units.* 
            FROM units
            WHERE units.direktorat_id = ?
        ");
        $stmt->bind_param("i", $directorateId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
