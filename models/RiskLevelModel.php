<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class RiskLevelModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function getRiskLevel($id)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM tingkat_risiko WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getRiskLevels()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM tingkat_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalRiskLevels()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM tingkat_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function createRiskLevel($code, $name)
    {
        $stmt = $this->dbConnect->connection->prepare("INSERT INTO tingkat_risiko (code, name) VALUES (?, ?)");
        $stmt->bind_param('ss', $code, $name);
        return $stmt->execute();
    }

    public function updateRiskLevel($id, $code, $name)
    {
        $stmt = $this->dbConnect->connection->prepare("UPDATE tingkat_risiko SET code = ?, name = ? WHERE id = ?");
        $stmt->bind_param("ssi", $code, $name, $id);
        return $stmt->execute();
    }

    public function deleteRiskLevel($id)
    {
        $stmt = $this->dbConnect->connection->prepare("DELETE FROM tingkat_risiko WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
