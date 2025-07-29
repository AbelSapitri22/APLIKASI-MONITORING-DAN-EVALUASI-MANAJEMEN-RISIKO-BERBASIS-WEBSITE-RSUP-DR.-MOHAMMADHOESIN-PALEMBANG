<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class UnitModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function getUnit($id)
    {
        $stmt = $this->dbConnect->connection->prepare("
        SELECT units.*, 
        direktorat.id AS direktorat_id,
        direktorat.name AS direktorat, 
        FROM units
        LEFT JOIN direktorat ON units.direktorat_id = direktorat.id
        WHERE units.id = ?
    ");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getUnits()
    {
        $stmt = $this->dbConnect->connection->prepare("
        SELECT units.*, direktorat.name AS direktorat
        FROM units
        LEFT JOIN direktorat ON units.direktorat_id = direktorat.id
    ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalUnits()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM units");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function createUnit($unit, $direktorat_id)
    {
        $stmt = $this->dbConnect->connection->prepare("INSERT INTO units (unit, direktorat_id) VALUES (?, ?)");
        $stmt->bind_param('si', $unit, $direktorat_id);
        return $stmt->execute();
    }

    public function updateUnit($id, $unit, $direktorat_id)
    {
        $stmt = $this->dbConnect->connection->prepare("UPDATE units SET unit = ?, direktorat_id = ? WHERE id = ?");
        $stmt->bind_param("sii", $unit, $direktorat_id, $id);
        return $stmt->execute();
    }

    public function deleteUnit($id)
    {
        $stmt = $this->dbConnect->connection->prepare("DELETE FROM units WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
