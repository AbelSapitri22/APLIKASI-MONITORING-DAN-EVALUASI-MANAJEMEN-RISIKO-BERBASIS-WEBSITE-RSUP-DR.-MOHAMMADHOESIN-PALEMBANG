<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class RiskImpactModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function getRiskImpact($id)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM dampak_risiko WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getRiskImpacts()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM dampak_risiko ORDER BY value ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalRiskImpacts()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM dampak_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function createRiskImpact($name, $value)
    {
        $stmt = $this->dbConnect->connection->prepare("INSERT INTO dampak_risiko (name, value) VALUES (?, ?)");
        $stmt->bind_param('si', $name, $value);
        return $stmt->execute();
    }

    public function updateRiskImpact($id, $name, $value)
    {
        $stmt = $this->dbConnect->connection->prepare("UPDATE dampak_risiko SET name = ?, value = ? WHERE id = ?");
        $stmt->bind_param("sii", $name, $value, $id);
        return $stmt->execute();
    }

    public function deleteRiskImpact($id)
    {
        $stmt = $this->dbConnect->connection->prepare("DELETE FROM dampak_risiko WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
