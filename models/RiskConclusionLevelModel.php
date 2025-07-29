<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class RiskConclusionLevelModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function get($id)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM simpulan_tingkat_risiko WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAll()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM simpulan_tingkat_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function count()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM simpulan_tingkat_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function create($name)
    {
        $stmt = $this->dbConnect->connection->prepare("INSERT INTO simpulan_tingkat_risiko (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }

    public function update($id, $name)
    {
        $stmt = $this->dbConnect->connection->prepare("UPDATE simpulan_tingkat_risiko SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->dbConnect->connection->prepare("DELETE FROM simpulan_tingkat_risiko WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
