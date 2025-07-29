<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class RiskProbabilityModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function getRiskProbability($id)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM kemungkinan_risiko WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getRiskProbabilities()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM kemungkinan_risiko ORDER BY value ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalRiskProbabilities()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM kemungkinan_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function createRiskProbability($name, $value)
    {
        $stmt = $this->dbConnect->connection->prepare("INSERT INTO kemungkinan_risiko (name, value) VALUES (?, ?)");
        $stmt->bind_param('si', $name, $value);
        return $stmt->execute();
    }

    public function updateRiskProbability($id, $name, $value)
    {
        $stmt = $this->dbConnect->connection->prepare("UPDATE kemungkinan_risiko SET name = ?, value = ? WHERE id = ?");
        $stmt->bind_param("sii", $name, $value, $id);
        return $stmt->execute();
    }

    public function deleteRiskProbability($id)
    {
        $stmt = $this->dbConnect->connection->prepare("DELETE FROM kemungkinan_risiko WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
