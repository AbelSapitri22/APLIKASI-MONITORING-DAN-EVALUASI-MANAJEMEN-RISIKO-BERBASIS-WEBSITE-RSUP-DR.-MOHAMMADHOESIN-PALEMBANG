<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/config/DBConnect.php';

class MonitoringReviewModel
{
    private $dbConnect;

    public function __construct()
    {
        $this->dbConnect = new DBConnect();
    }

    public function isMonitoringExist($penilaian_risiko_id)
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM pemantauan_reviu WHERE penilaian_risiko_id = ?");
        $stmt->bind_param("i", $penilaian_risiko_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function getMonitoringReviews()
    {
        $query = "SELECT 
                mr.*,
                pr.id AS penilaian_id,
                pr.risiko,
                pr.unit_id,
                pr.kategori_risiko_id,
                pr.uraian_pengendalian,
                pr.efektif_pengendalian,
                pr.p_analisis,
                pr.d_analisis,
                pr.bobot_analisis,
                pr.nilai_analisis,
                pr.tingkat_risiko_analisis_id,
                u.name AS unit_name,
                kr.name AS kategori_risiko,
                tra.name AS tingkat_risiko_analisis,
                trs.name AS tingkat_risiko_simpulan
            FROM pemantauan_reviu mr
            INNER JOIN penilaian_risiko pr ON mr.penilaian_risiko_id = pr.id
            INNER JOIN units u ON pr.unit_id = u.id
            INNER JOIN kategori_risiko kr ON pr.kategori_risiko_id = kr.id
            INNER JOIN tingkat_risiko tra ON pr.tingkat_risiko_analisis_id = tra.id
            LEFT JOIN tingkat_risiko trs ON mr.simpulan_tingkat_risiko_id = trs.id
            ORDER BY mr.id DESC";

        $stmt = $this->dbConnect->connection->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMonitoringReviewsByUnitCategoryMonthAndYear($unit_id, $riskCategorySelected, $monthSelected, $yearSelected)
    {
        $query = "SELECT 
        mr.*,
        pr.id AS penilaian_id,
        pr.risiko,
        pr.unit_id,
        pr.kategori_risiko_id,
        pr.uraian_pengendalian,
        pr.efektif_pengendalian,
        pr.p_analisis,
        pr.d_analisis,
        pr.bobot_analisis,
        pr.nilai_analisis,
        pr.selera_risiko,
        pr.prioritas_risiko_id,
        pr.created_at AS created_at,
        pr.jadwal_pelaksanaan,
        kr.name AS kategori_risiko,
        tra.id AS tingkat_risiko_analisis_id,
        tra.name AS tingkat_risiko_analisis,
        trp.id AS tingkat_risiko_pemantauan_id,
        trp.name AS tingkat_risiko_pemantauan,
        trs.id AS tingkat_risiko_simpulan_id,
        trs.name AS tingkat_risiko_simpulan,
        prr.code AS prioritas_risiko,
        prr.code AS kode_prioritas
        FROM pemantauan_reviu mr
        INNER JOIN penilaian_risiko pr ON mr.penilaian_risiko_id = pr.id
        INNER JOIN units u ON pr.unit_id = u.id
        INNER JOIN kategori_risiko kr ON pr.kategori_risiko_id = kr.id
        INNER JOIN tingkat_risiko tra ON pr.tingkat_risiko_analisis_id = tra.id
        LEFT JOIN tingkat_risiko trp ON mr.tingkat_risiko_id = trp.id
        LEFT JOIN simpulan_tingkat_risiko trs ON mr.simpulan_tingkat_risiko_id = trs.id
        LEFT JOIN prioritas_risiko prr ON pr.prioritas_risiko_id = prr.id
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

        $query .= " ORDER BY mr.id DESC";

        $stmt = $this->dbConnect->connection->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMonitoringReviewsByRiskAssessment($penilaian_risiko_id, $unit_id)
    {
        $query = "SELECT 
                mr.*,
                pr.id AS penilaian_id,
                pr.risiko,
                pr.unit_id,
                pr.kategori_risiko_id,
                pr.uraian_pengendalian,
                pr.efektif_pengendalian,
                pr.p_analisis,
                pr.d_analisis,
                pr.bobot_analisis,
                pr.nilai_analisis,
                pr.tingkat_risiko_analisis_id,
                pr.created_at,
                u.name AS unit_name,
                kr.name AS kategori_risiko,
                tra.name AS tingkat_risiko_analisis,
                trs.name AS tingkat_risiko_simpulan
            FROM pemantauan_reviu mr
            INNER JOIN penilaian_risiko pr ON mr.penilaian_risiko_id = pr.id
            INNER JOIN units u ON pr.unit_id = u.id
            INNER JOIN kategori_risiko kr ON pr.kategori_risiko_id = kr.id
            INNER JOIN tingkat_risiko tra ON pr.tingkat_risiko_analisis_id = tra.id
            LEFT JOIN tingkat_risiko trs ON mr.simpulan_tingkat_risiko_id = trs.id
            WHERE mr.penilaian_risiko_id = ? AND pr.unit_id = ?
            ORDER BY mr.id DESC";

        $stmt = $this->dbConnect->connection->prepare($query);
        $stmt->bind_param("ii", $penilaian_risiko_id, $unit_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMonitoringReviewByVerifiedAndUnitAndCategoryMonthAndYear($is_verified, $unit_id, $categorySelected, $monthSelected = "Semua", $yearSelected = "Semua")
    {
        $query = "SELECT 
        mr.*,
        pr.id AS penilaian_id,
        pr.risiko,
        pr.unit_id,
        pr.kategori_risiko_id,
        pr.uraian_pengendalian,
        pr.efektif_pengendalian,
        pr.p_analisis,
        pr.d_analisis,
        pr.bobot_analisis,
        pr.nilai_analisis,
        pr.selera_risiko,
        pr.prioritas_risiko_id,
        pr.created_at AS created_at,
        pr.jadwal_pelaksanaan,
        kr.name AS kategori_risiko,
        tra.id AS tingkat_risiko_analisis_id,
        tra.name AS tingkat_risiko_analisis,
        trp.id AS tingkat_risiko_pemantauan_id,
        trp.name AS tingkat_risiko_pemantauan,
        trs.id AS tingkat_risiko_simpulan_id,
        trs.name AS tingkat_risiko_simpulan,
        prr.code AS prioritas_risiko,
        prr.code AS kode_prioritas
        FROM pemantauan_reviu mr
        INNER JOIN penilaian_risiko pr ON mr.penilaian_risiko_id = pr.id
        INNER JOIN units u ON pr.unit_id = u.id
        INNER JOIN kategori_risiko kr ON pr.kategori_risiko_id = kr.id
        INNER JOIN tingkat_risiko tra ON pr.tingkat_risiko_analisis_id = tra.id
        LEFT JOIN tingkat_risiko trp ON mr.tingkat_risiko_id = trp.id
        LEFT JOIN simpulan_tingkat_risiko trs ON mr.simpulan_tingkat_risiko_id = trs.id
        LEFT JOIN prioritas_risiko prr ON pr.prioritas_risiko_id = prr.id
        WHERE mr.is_verified = ?";

        $types = "i"; // is_verified
        $params = [$is_verified];

        if ($unit_id != 0) {
            $query .= " AND pr.unit_id = ?";
            $types .= "i";
            $params[] = $unit_id;
        }

        if ($categorySelected !== "Semua") {
            $query .= " AND kr.name = ?";
            $types .= "s";
            $params[] = $categorySelected;
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

        $query .= " ORDER BY mr.id DESC";

        $stmt = $this->dbConnect->connection->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getMonitoringReview($id)
    {
        $query = "SELECT 
                mr.*,
                pr.id AS penilaian_id,
                pr.risiko,
                pr.unit_id,
                pr.kategori_risiko_id,
                pr.uraian_pengendalian,
                pr.efektif_pengendalian,
                pr.p_analisis,
                pr.d_analisis,
                pr.bobot_analisis,
                pr.nilai_analisis,
                pr.tingkat_risiko_analisis_id,
                u.name AS unit_name,
                kr.name AS kategori_risiko,
                tra.name AS tingkat_risiko_analisis,
                trs.name AS tingkat_risiko_simpulan,
                trm.name AS tingkat_risiko_monitoring
            FROM pemantauan_reviu mr
            INNER JOIN penilaian_risiko pr ON mr.penilaian_risiko_id = pr.id
            INNER JOIN units u ON pr.unit_id = u.id
            INNER JOIN kategori_risiko kr ON pr.kategori_risiko_id = kr.id
            INNER JOIN tingkat_risiko tra ON pr.tingkat_risiko_analisis_id = tra.id
            LEFT JOIN tingkat_risiko trs ON mr.simpulan_tingkat_risiko_id = trs.id
            LEFT JOIN tingkat_risiko trm ON mr.tingkat_risiko_id = trm.id
            WHERE mr.id = ?";

        $stmt = $this->dbConnect->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getMonitoringReviewsByEffectiveness($efektif)
    {
        $query = "SELECT 
                mr.*,
                pr.id AS penilaian_id,
                pr.risiko,
                pr.unit_id,
                pr.kategori_risiko_id,
                pr.uraian_pengendalian,
                pr.efektif_pengendalian,
                pr.p_analisis,
                pr.d_analisis,
                pr.bobot_analisis,
                pr.nilai_analisis,
                pr.tingkat_risiko_analisis_id,
                u.name AS unit_name,
                kr.name AS kategori_risiko,
                tra.name AS tingkat_risiko_analisis,
                trs.name AS tingkat_risiko_simpulan
            FROM pemantauan_reviu mr
            INNER JOIN penilaian_risiko pr ON mr.penilaian_risiko_id = pr.id
            INNER JOIN units u ON pr.unit_id = u.id
            INNER JOIN kategori_risiko kr ON pr.kategori_risiko_id = kr.id
            INNER JOIN tingkat_risiko tra ON pr.tingkat_risiko_analisis_id = tra.id
            LEFT JOIN tingkat_risiko trs ON mr.simpulan_tingkat_risiko_id = trs.id
            WHERE mr.efektif = ?
            ORDER BY mr.id DESC";

        $stmt = $this->dbConnect->connection->prepare($query);
        $stmt->bind_param("s", $efektif);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function createMonitoringReview($penilaian_risiko_id)
    {
        $query = "INSERT INTO pemantauan_reviu (
            penilaian_risiko_id
        ) VALUES (?)";

        $stmt = $this->dbConnect->connection->prepare($query);
        $stmt->bind_param(
            "i",
            $penilaian_risiko_id
        );
        return $stmt->execute();
    }

    public function updateMonitoringReview($id, $data)
    {
        // Validasi enum efektif
        $efektif = strtolower(trim($data['efektif_pengendalian']));
        if (!in_array($efektif, ['efektif', 'tidak efektif'])) {
            throw new InvalidArgumentException("Nilai 'efektif' harus 'efektif' atau 'tidak efektif'");
        }

        // Base query
        $query = "UPDATE pemantauan_reviu SET
        p = ?, 
        d = ?, 
        bobot = ?, 
        nilai = ?, 
        tingkat_risiko_id = ?, 
        simpulan_tingkat_risiko_id = ?, 
        efektif = ?, 
        updated_at = NOW(),
        is_verified = NULL";

        $params = [
            (int)$data['p'],
            (int)$data['d'],
            (float)$data['bobot'],
            (int)$data['nilai'],
            (int)$data['tingkat_risiko_id'],
            (int)$data['simpulan_tingkat_risiko_id'],
            $efektif
        ];

        $types = "iidiiis"; // untuk parameter di atas

        // Tambahkan document_path jika ada
        if (isset($data['document_path']) && !empty($data['document_path'])) {
            $query .= ", document = ?";
            $params[] = $data['document_path'];
            $types .= "s";
        }

        $query .= " WHERE id = ?";
        $params[] = (int)$id;
        $types .= "i";

        $stmt = $this->dbConnect->connection->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->dbConnect->connection->error);
        }

        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
    }


    public function verifyMonitoringReview($id, $is_verified, $notes)
    {
        $verified_at = date('Y-m-d H:i:s');

        if ($is_verified == 0) {
            $query = "UPDATE pemantauan_reviu SET
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
            $query = "UPDATE pemantauan_reviu SET
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

    public function deleteMonitoringReview($id)
    {
        $stmt = $this->dbConnect->connection->prepare("DELETE FROM pemantauan_reviu WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTotalMonitoringReviews()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT COUNT(*) as total FROM pemantauan_reviu");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getRiskLevelConclusion()
    {
        $stmt = $this->dbConnect->connection->prepare("SELECT * FROM simpulan_tingkat_risiko");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAvailableYears()
    {
        $query = "SELECT DISTINCT YEAR(updated_at) as year 
              FROM pemantauan_reviu 
              ORDER BY year DESC";

        $result = $this->dbConnect->connection->query($query);
        $years = [];

        while ($row = $result->fetch_assoc()) {
            $years[] = $row['year'];
        }

        return $years;
    }

    public function getAvailableYearsRejected()
    {
        $query = "SELECT DISTINCT YEAR(created_at) as year 
              FROM pemantauan_reviu 
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
