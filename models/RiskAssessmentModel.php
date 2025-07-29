<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class RiskAssessmentModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function getRiskAssessments()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM penilaian_risiko ORDER BY id DESC");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getRiskAssessmentsByUnitCategoryMonthAndYear($unit_id, $riskCategorySelected, $monthSelected, $yearSelected)
    {
        $query = "SELECT 
        pr.*,
        u.id AS unit_id,
        kr.id as kategori_risiko_id,
        kr.name AS kategori_risiko,
        tra.id AS tingkat_risiko_analisis_id,
        tra.name AS tingkat_risiko_analisis,
        prio.id AS prioritas_risiko_id,
        prio.name AS prioritas_risiko,
        trt.id AS tingkat_risiko_target_id,
        trt.name AS tingkat_risiko_target
        FROM penilaian_risiko pr
        INNER JOIN units u ON pr.unit_id = u.id
        INNER JOIN kategori_risiko kr ON pr.kategori_risiko_id = kr.id
        INNER JOIN tingkat_risiko tra ON pr.tingkat_risiko_analisis_id = tra.id
        INNER JOIN prioritas_risiko prio ON pr.prioritas_risiko_id = prio.id
        INNER JOIN tingkat_risiko trt ON pr.tingkat_risiko_target_id = trt.id
        WHERE 1=1";

        $params = [];
        $types = "";

        if ($unit_id != 0) {
            $query .= " AND pr.unit_id = ?";
            $types .= "i";
            $params[] = $unit_id;
        }

        if ($riskCategorySelected !== "Semua") {
            $query .= " AND kr.name = ?";
            $types .= "s";
            $params[] = $riskCategorySelected;
        }

        if ($monthSelected !== "Semua") {
            $query .= " AND MONTH(pr.created_at) = ?";
            $types .= "s";
            $params[] = $monthSelected;
        }

        if ($yearSelected !== "Semua") {
            $query .= " AND YEAR(pr.created_at) = ?";
            $types .= "s";
            $params[] = $yearSelected;
        }

        $query .= " ORDER BY pr.created_at DESC";

        $stmt = $this->dbConnect->connection->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getRiskAssessmentsByVerifiedUnitCategoryMonthYear($is_verified, $unit_id, $riskCategorySelected, $monthSelected = 'Semua', $yearSelected = 'Semua')
    {
        $query = "SELECT 
        pr.*,
        u.id AS unit_id,
        kr.id AS kategori_risiko_id,
        kr.name AS kategori_risiko,
        tra.id AS tingkat_risiko_analisis_id,
        tra.name AS tingkat_risiko_analisis,
        prio.id AS prioritas_risiko_id,
        prio.name AS prioritas_risiko,
        trt.id AS tingkat_risiko_target_id,
        trt.name AS tingkat_risiko_target
        FROM penilaian_risiko pr
        INNER JOIN units u ON pr.unit_id = u.id
        INNER JOIN kategori_risiko kr ON pr.kategori_risiko_id = kr.id
        INNER JOIN tingkat_risiko tra ON pr.tingkat_risiko_analisis_id = tra.id
        INNER JOIN prioritas_risiko prio ON pr.prioritas_risiko_id = prio.id
        INNER JOIN tingkat_risiko trt ON pr.tingkat_risiko_target_id = trt.id
        WHERE pr.is_verified = ?";

        $params = [$is_verified];
        $types = "i"; // untuk is_verified

        if ($unit_id != 0) {
            $query .= " AND pr.unit_id = ?";
            $types .= "i";
            $params[] = $unit_id;
        }

        if ($riskCategorySelected !== "Semua") {
            $query .= " AND kr.name = ?";
            $types .= "s";
            $params[] = $riskCategorySelected;
        }

        if ($monthSelected !== "Semua") {
            $query .= " AND MONTH(pr.created_at) = ?";
            $types .= "i"; // sebelumnya "s"
            $params[] = (int)$monthSelected; // casting ke integer
        }

        if ($yearSelected !== "Semua") {
            $query .= " AND YEAR(pr.created_at) = ?";
            $types .= "i"; // sebelumnya "s"
            $params[] = (int)$yearSelected; // casting ke integer
        }

        $query .= " ORDER BY pr.created_at DESC";

        $stmt = $this->dbConnect->connection->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getRiskAssessment($id)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM penilaian_risiko WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getLastRiskAssessmentId()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT id FROM penilaian_risiko ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['id'] : null;
    }

    public function createRiskAssessment($data)
    {
        $query = "INSERT INTO penilaian_risiko (
        unit_id, risiko, kategori_risiko_id, sebab, sumber_risiko, cuc, dampak,
        uraian_pengendalian, efektif_pengendalian,
        p_analisis, d_analisis, bobot_analisis, nilai_analisis, tingkat_risiko_analisis_id,
        prioritas_risiko_id, selera_risiko,
        pilihan_penanganan, uraian_penanganan, jadwal_pelaksanaan,
        p_target, d_target, bobot_target, nilai_target, tingkat_risiko_target_id, document,
        created_at
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?,
        ?, ?, ?,
        ?, ?, ?, ?, ?, ?,
        ?
    )";

        $data['tingkat_risiko_target_id'] = (int)$data['tingkat_risiko_target_id'];
        $createdAt = date('Y-m-d H:i:s');

        $stmt = $this->dbConnect->connection->prepare($query);
        $stmt->bind_param(
            "isissssssiiddiissssiiddiss",
            $_SESSION['unit_id'],
            $data['risiko'],
            $data['kategori_risiko_id'],
            $data['sebab'],
            $data['sumber_risiko'],
            $data['cuc'],
            $data['dampak'],
            $data['uraian_pengendalian'],
            $data['efektif_pengendalian'],
            $data['p_analisis'],
            $data['d_analisis'],
            $data['bobot_analisis'],
            $data['nilai_analisis'],
            $data['tingkat_risiko_analisis_id'],
            $data['prioritas_risiko_id'],
            $data['selera_risiko'],
            $data['pilihan_penanganan'],
            $data['uraian_penanganan'],
            $data['jadwal_pelaksanaan'],
            $data['p_target'],
            $data['d_target'],
            $data['bobot_target'],
            $data['nilai_target'],
            $data['tingkat_risiko_target_id'],
            $data['document_path'],
            $createdAt
        );
        return $stmt->execute();
    }


    public function updateRiskAssessment($id, $data)
    {
        $query = "UPDATE penilaian_risiko SET
        is_verified = __IS_VERIFIED__,
        risiko = ?, kategori_risiko_id = ?, sebab = ?, sumber_risiko = ?, cuc = ?, dampak = ?,
        uraian_pengendalian = ?, efektif_pengendalian = ?,
        p_analisis = ?, d_analisis = ?, bobot_analisis = ?, nilai_analisis = ?, tingkat_risiko_analisis_id = ?,
        prioritas_risiko_id = ?, selera_risiko = ?,
        pilihan_penanganan = ?, uraian_penanganan = ?, jadwal_pelaksanaan = ?, 
        p_target = ?, d_target = ?, bobot_target = ?, nilai_target = ?, tingkat_risiko_target_id = ?, document = ?,
        notes = NULL
    WHERE id = ?";

        $isNull = is_null($data['is_verified']);

        if ($isNull) {
            $query = str_replace("is_verified = __IS_VERIFIED__", "is_verified = NULL", $query);
        } else {
            $query = str_replace("is_verified = __IS_VERIFIED__", "is_verified = ?", $query);
        }

        $stmt = $this->dbConnect->connection->prepare($query);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->dbConnect->connection->error);
        }

        $types = ($isNull ? "" : "i") . "sissssssiidiiissssiidiisi";
        $params = [];

        if (!$isNull) {
            $params[] = $data['is_verified'];
        }

        $params = array_merge($params, [
            $data['risiko'],
            $data['kategori_risiko_id'],
            $data['sebab'],
            $data['sumber_risiko'],
            $data['cuc'],
            $data['dampak'],
            $data['uraian_pengendalian'],
            $data['efektif_pengendalian'],
            $data['p_analisis'],
            $data['d_analisis'],
            $data['bobot_analisis'],
            $data['nilai_analisis'],
            $data['tingkat_risiko_analisis_id'],
            $data['prioritas_risiko_id'],
            $data['selera_risiko'],
            $data['pilihan_penanganan'],
            $data['uraian_penanganan'],
            $data['jadwal_pelaksanaan'],
            $data['p_target'],
            $data['d_target'],
            $data['bobot_target'],
            $data['nilai_target'],
            $data['tingkat_risiko_target_id'],
            $data['document_path'],
            $id
        ]);

        // Gunakan call_user_func_array untuk bind_param secara dinamis
        $bindNames[] = $types;
        foreach ($params as $key => $value) {
            $bindNames[] = &$params[$key];
        }

        call_user_func_array([$stmt, 'bind_param'], $bindNames);

        return $stmt->execute();
    }

    public function verifyRiskAssessment($id, $is_verified, $notes)
    {
        $verified_at = date('Y-m-d H:i:s');

        if ($is_verified == 0) {
            $query = "UPDATE penilaian_risiko SET
            is_verified = ?, verified_at = ?, notes = ?
        WHERE id = ?";

            $stmt = $this->dbConnect->connection->prepare($query);
            $stmt->bind_param(
                "issi",  // i = integer, s = string
                $is_verified,
                $verified_at,
                $notes,
                $id
            );
        } else {
            $query = "UPDATE penilaian_risiko SET
            is_verified = ?, verified_at = ?, notes = NULL
        WHERE id = ?";

            $stmt = $this->dbConnect->connection->prepare($query);
            $stmt->bind_param(
                "isi",
                $is_verified,
                $verified_at,
                $id
            );
        }

        return $stmt->execute();
    }

    public function deleteRiskAssessment($id)
    {
        $stmt = $this->dbConnect->connection->prepare("DELETE FROM penilaian_risiko WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTotalRiskAssessments()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM penilaian_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTotalRiskAssessmentsVerified($is_verified)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM penilaian_risiko where is_verified = ?");
        $stmt->bind_param("i", $is_verified);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTotalRiskAssessmentsWaited()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM penilaian_risiko where is_verified IS NULL");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTotalRiskAssessmentsByCategory($kategori_risiko_id)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM penilaian_risiko where kategori_risiko_id = ?");
        $stmt->bind_param("i", $kategori_risiko_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getAvailableYears()
    {
        $query = "SELECT DISTINCT YEAR(created_at) as year 
              FROM penilaian_risiko 
              ORDER BY year DESC";

        $result = $this->dbConnect->connection->query($query);
        $years = [];

        while ($row = $result->fetch_assoc()) {
            $years[] = $row['year'];
        }

        return $years;
    }

    public function getAvailableYearsForRejected()
    {
        $query = "SELECT DISTINCT YEAR(created_at) as year 
              FROM penilaian_risiko 
              WHERE is_verified = 0
              ORDER BY year DESC";

        $result = $this->dbConnect->connection->query($query);
        $years = [];

        while ($row = $result->fetch_assoc()) {
            $years[] = $row['year'];
        }

        return $years;
    }
}
