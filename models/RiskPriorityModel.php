<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class RiskPriorityModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function getRiskPriority($id)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM prioritas_risiko WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getRiskPriorities()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM prioritas_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalRiskPriorities()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM prioritas_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function createRiskPriority($priority_level, $priority_name)
    {
        $stmt = $this->dbConnect->connection->prepare("INSERT INTO prioritas_risiko (priority_level, priority_name) VALUES (?, ?)");
        $stmt->bind_param('is', $priority_level, $priority_name);
        return $stmt->execute();
    }

    public function updateRiskPriority($id, $priority_level, $priority_name)
    {
        $stmt = $this->dbConnect->connection->prepare("UPDATE prioritas_risiko SET priority_level = ?, priority_name = ? WHERE id = ?");
        $stmt->bind_param("isi", $priority_level, $priority_name, $id);
        return $stmt->execute();
    }

    public function deleteRiskPriority($id)
    {
        $stmt = $this->dbConnect->connection->prepare("DELETE FROM prioritas_risiko WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
