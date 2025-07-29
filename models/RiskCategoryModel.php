<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class RiskCategoryModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function getRiskCategory($id)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM kategori_risiko WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getRiskCategories()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM kategori_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRiskCategoriesName()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT name FROM kategori_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalRiskCategories()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM kategori_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function createRiskCategory($category)
    {
        $stmt = $this->dbConnect->connection->prepare("INSERT INTO kategori_risiko (category) VALUES (?)");
        $stmt->bind_param('s', $category);
        return $stmt->execute();
    }

    public function updateRiskCategory($id, $category)
    {
        $stmt = $this->dbConnect->connection->prepare("UPDATE kategori_risiko SET category = ? WHERE id = ?");
        $stmt->bind_param("si", $category, $id);
        return $stmt->execute();
    }

    public function deleteRiskCategory($id)
    {
        $stmt = $this->dbConnect->connection->prepare("DELETE FROM kategori_risiko WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
