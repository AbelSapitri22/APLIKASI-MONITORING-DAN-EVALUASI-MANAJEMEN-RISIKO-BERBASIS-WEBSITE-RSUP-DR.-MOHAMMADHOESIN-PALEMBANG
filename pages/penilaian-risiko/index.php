<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/PageNameEnum.php");
$pageName = PageNameEnum::DATA_PENILAIAN_RISIKO;

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/sidebar.php';

require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/MonitoringReviewModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskAssessmentModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskCategoryModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskLevelModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskPriorityModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskImpactModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskProbabilityModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/UsersModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/UnitModel.php');

$userModel              = new UsersModel();
$unitModel              = new UnitModel();
$monitoringReviewModel  = new MonitoringReviewModel();
$riskAssasementModel    = new RiskAssessmentModel();
$riskCategoryModel      = new RiskCategoryModel();
$riskLevelModel         = new RiskLevelModel();
$riskPriorityModel      = new RiskPriorityModel();
$riskProbabilityModel   = new RiskProbabilityModel();
$riskImpactModel        = new RiskImpactModel();

$units      = $unitModel->getUnits();
$user       = $userModel->getUserDetail($_SESSION['username']);

$unit_id = 0;
$riskCategorySelected = "Semua";
$monthSelected = "Semua";
$yearSelected = "Semua";

if ($user['role'] === "unit") {
    $unit_id = $user['unit_id'];
}

// Daftar bulan untuk filter
$months = [
    'Semua' => 'Semua Bulan',
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

// Daftar tahun yang tersedia (ambil dari data yang ada di database)
$availableYears = $riskAssasementModel->getAvailableYears();
$years = ['Semua' => 'Semua Tahun'] + array_combine($availableYears, $availableYears);

$allRiskAssasement  = $riskAssasementModel->getRiskAssessmentsByUnitCategoryMonthAndYear(
    $unit_id,
    $riskCategorySelected,
    $monthSelected,
    $yearSelected
);

$riskProbabilities  = $riskProbabilityModel->getRiskProbabilities();
$riskCategories     = $riskCategoryModel->getRiskCategories();
$riskPriorities     = $riskPriorityModel->getRiskPriorities();
$riskImpacts        = $riskImpactModel->getRiskImpacts();
$riskLevels         = $riskLevelModel->getRiskLevels();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    // Identifikasi Risiko
    $risiko = isset($_POST['risiko']) ? trim($_POST['risiko']) : '';
    $kategori_risiko_id = isset($_POST['kategori_risiko_id']) ? (int)$_POST['kategori_risiko_id'] : 0;
    $sebab = isset($_POST['sebab']) ? trim($_POST['sebab']) : '';
    $sumber_risiko = isset($_POST['sumber_risiko']) ? $_POST['sumber_risiko'] : 'internal';
    $cuc = isset($_POST['cuc']) ? $_POST['cuc'] : 'c';
    $dampak = isset($_POST['dampak']) ? trim($_POST['dampak']) : '';

    // Analisis Risiko
    $uraian_pengendalian = isset($_POST['uraian_pengendalian']) ? trim($_POST['uraian_pengendalian']) : '';
    $efektif_pengendalian = isset($_POST['efektif_pengendalian']) ? $_POST['efektif_pengendalian'] : 'efektif';
    $p_analisis = isset($_POST['p_analisis']) ? (int)$_POST['p_analisis'] : 0;
    $d_analisis = isset($_POST['d_analisis']) ? (int)$_POST['d_analisis'] : 0;
    $bobot_analisis = isset($_POST['bobot_analisis']) ? (float)$_POST['bobot_analisis'] : 0.0;
    $nilai_analisis = isset($_POST['nilai_analisis']) ? (float)$_POST['nilai_analisis'] : 0;
    $tingkat_risiko_analisis_id = isset($_POST['tingkat_risiko_analisis_id']) ? (int)$_POST['tingkat_risiko_analisis_id'] : 0;

    // Evaluasi Risiko
    $prioritas_risiko_id = isset($_POST['prioritas_risiko_id']) ? (int)$_POST['prioritas_risiko_id'] : 0;
    $selera_risiko = isset($_POST['selera_risiko']) ? trim($_POST['selera_risiko']) : '';
    $pilihan_penanganan = isset($_POST['pilihan_penanganan']) ? $_POST['pilihan_penanganan'] : 'Mitigasi risiko';

    // Rencana Penanganan Risiko
    $uraian_penanganan = isset($_POST['uraian_penanganan']) ? trim($_POST['uraian_penanganan']) : '';
    $jadwal_pelaksanaan = isset($_POST['jadwal_pelaksanaan']) ? trim($_POST['jadwal_pelaksanaan']) : '';

    // Target Penurunan Risiko
    $p_target = isset($_POST['p_target']) ? (int)$_POST['p_target'] : 0;
    $d_target = isset($_POST['d_target']) ? (int)$_POST['d_target'] : 0;
    $bobot_target = isset($_POST['bobot_target']) ? (float)$_POST['bobot_target'] : 0.0;
    $nilai_target = isset($_POST['nilai_target']) ? (int)$_POST['nilai_target'] : 0;
    $tingkat_risiko_target_id = isset($_POST['tingkat_risiko_target_id']) ? (int)$_POST['tingkat_risiko_target_id'] : 0;

    // Handle file upload
    $documentPath = null;
    $uploadError = null;

    if (isset($_FILES['document']) && $_FILES['document']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/smr/uploads/documents/';

        // Create directory if not exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // File validation
        $allowedTypes = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'image/jpeg' => 'jpg',
            'image/png' => 'png'
        ];

        $maxSize = 150 * 1024 * 1024; // 150MB
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $_FILES['document']['tmp_name']);
        finfo_close($fileInfo);

        // Validate file type and size
        if (array_key_exists($mimeType, $allowedTypes) && $_FILES['document']['size'] <= $maxSize) {
            $extension = $allowedTypes[$mimeType];
            $filename = 'doc_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['document']['tmp_name'], $destination)) {
                $documentPath = '/smr/uploads/documents/' . $filename;
            } else {
                $uploadError = 'Gagal mengunggah dokumen.';
            }
        } else {
            $uploadError = 'Format file tidak didukung atau ukuran melebihi 5MB.';
        }
    } elseif (isset($_FILES['document']) && $_FILES['document']['error'] != UPLOAD_ERR_NO_FILE) {
        $uploadError = 'Error upload: ' . $_FILES['document']['error'];
    }

    // Prepare data array
    $data = [
        'risiko' => $risiko,
        'kategori_risiko_id' => $kategori_risiko_id,
        'sebab' => $sebab,
        'sumber_risiko' => $sumber_risiko,
        'cuc' => $cuc,
        'dampak' => $dampak,
        'uraian_pengendalian' => $uraian_pengendalian,
        'efektif_pengendalian' => $efektif_pengendalian,
        'p_analisis' => $p_analisis,
        'd_analisis' => $d_analisis,
        'bobot_analisis' => $bobot_analisis,
        'nilai_analisis' => $nilai_analisis,
        'tingkat_risiko_analisis_id' => $tingkat_risiko_analisis_id,
        'prioritas_risiko_id' => $prioritas_risiko_id,
        'selera_risiko' => $selera_risiko,
        'pilihan_penanganan' => $pilihan_penanganan,
        'uraian_penanganan' => $uraian_penanganan,
        'jadwal_pelaksanaan' => $jadwal_pelaksanaan,
        'p_target' => $p_target,
        'd_target' => $d_target,
        'bobot_target' => $bobot_target,
        'nilai_target' => $nilai_target,
        'tingkat_risiko_target_id' => $tingkat_risiko_target_id,
        'document_path' => $documentPath
    ];

    // Validate required fields
    if (empty($risiko) || empty($kategori_risiko_id) || empty($sebab) || empty($dampak)) {
        $toastMessage = [
            "icon" => "error",
            "message" => "Harap isi semua field yang wajib diisi!"
        ];
    } elseif ($uploadError) {
        $toastMessage = [
            "icon" => "error",
            "message" => $uploadError
        ];
    } else {
        if ($riskAssasementModel->createRiskAssessment($data)) {
            if ($data['nilai_analisis'] > 9) {
                $id = $riskAssasementModel->getLastRiskAssessmentId();
                if ($monitoringReviewModel->createMonitoringReview($id)) {
                    $toastMessage = [
                        "icon" => "success",
                        "message" => "Tambah $pageName berhasil!"
                    ];
                } else {
                    $toastMessage = [
                        "icon" => "error",
                        "message" => "Kesalahan sistem! Silakan coba lagi!"
                    ];
                }
            } else {
                $toastMessage = [
                    "icon" => "success",
                    "message" => "Tambah $pageName berhasil!"
                ];
            }
        } else {
            // Hapus file yang sudah diupload jika gagal menyimpan data
            if ($documentPath && file_exists($_SERVER['DOCUMENT_ROOT'] . $documentPath)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $documentPath);
            }

            $toastMessage = [
                "icon" => "error",
                "message" => "Kesalahan sistem! Silakan coba lagi!"
            ];
        }
    }

    $_SESSION['toast_message'] = $toastMessage;
    echo '<script>window.location.href = "' . URLEnum::getRiskAssasmentURL() . '";</script>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Identifikasi Risiko
    $risiko = isset($_POST['risiko']) ? $_POST['risiko'] : '';
    $kategori_risiko_id = isset($_POST['kategori_risiko_id']) ? $_POST['kategori_risiko_id'] : '';
    $sebab = isset($_POST['sebab']) ? $_POST['sebab'] : '';
    $sumber_risiko = isset($_POST['sumber_risiko']) ? $_POST['sumber_risiko'] : '';
    $cuc = isset($_POST['cuc']) ? $_POST['cuc'] : '';
    $dampak = isset($_POST['dampak']) ? $_POST['dampak'] : '';

    // Analisis Risiko
    $uraian_pengendalian = isset($_POST['uraian_pengendalian']) ? $_POST['uraian_pengendalian'] : '';
    $efektif_pengendalian = isset($_POST['efektif_pengendalian']) ? $_POST['efektif_pengendalian'] : '';
    $p_analisis = isset($_POST['p_analisis']) ? $_POST['p_analisis'] : 0;
    $d_analisis = isset($_POST['d_analisis']) ? $_POST['d_analisis'] : 0;
    $bobot_analisis = isset($_POST['bobot_analisis']) ? $_POST['bobot_analisis'] : 0.0;
    $nilai_analisis = isset($_POST['nilai_analisis']) ? $_POST['nilai_analisis'] : 0;
    $tingkat_risiko_analisis_id = isset($_POST['tingkat_risiko_analisis_id']) ? $_POST['tingkat_risiko_analisis_id'] : '';

    // Evaluasi Risiko
    $prioritas_risiko_id = isset($_POST['prioritas_risiko_id']) ? $_POST['prioritas_risiko_id'] : '';
    $selera_risiko = isset($_POST['selera_risiko']) ? $_POST['selera_risiko'] : '';
    $pilihan_penanganan = isset($_POST['pilihan_penanganan']) ? $_POST['pilihan_penanganan'] : '';

    // Rencana Penanganan Risiko
    $uraian_penanganan = isset($_POST['uraian_penanganan']) ? $_POST['uraian_penanganan'] : '';
    $jadwal_pelaksanaan = isset($_POST['jadwal_pelaksanaan']) ? $_POST['jadwal_pelaksanaan'] : '';

    // Target Penurunan Risiko
    $p_target = isset($_POST['p_target']) ? $_POST['p_target'] : 0;
    $d_target = isset($_POST['d_target']) ? $_POST['d_target'] : 0;
    $bobot_target = isset($_POST['bobot_target']) ? $_POST['bobot_target'] : 0.0;
    $nilai_target = isset($_POST['nilai_target']) ? $_POST['nilai_target'] : 0;
    $tingkat_risiko_target_id = isset($_POST['tingkat_risiko_target_id']) ? $_POST['tingkat_risiko_target_id'] : '';

    // ID untuk update
    $riskId = isset($_POST['risk_id']) ? $_POST['risk_id'] : '';

    // Prepare data array for update
    $data = [
        'risiko' => $risiko,
        'kategori_risiko_id' => $kategori_risiko_id,
        'sebab' => $sebab,
        'sumber_risiko' => $sumber_risiko,
        'cuc' => $cuc,
        'dampak' => $dampak,
        'uraian_pengendalian' => $uraian_pengendalian,
        'efektif_pengendalian' => $efektif_pengendalian,
        'p_analisis' => $p_analisis,
        'd_analisis' => $d_analisis,
        'bobot_analisis' => $bobot_analisis,
        'nilai_analisis' => $nilai_analisis,
        'tingkat_risiko_analisis_id' => $tingkat_risiko_analisis_id,
        'prioritas_risiko_id' => $prioritas_risiko_id,
        'selera_risiko' => $selera_risiko,
        'pilihan_penanganan' => $pilihan_penanganan,
        'uraian_penanganan' => $uraian_penanganan,
        'jadwal_pelaksanaan' => $jadwal_pelaksanaan,
        'p_target' => $p_target,
        'd_target' => $d_target,
        'bobot_target' => $bobot_target,
        'nilai_target' => $nilai_target,
        'tingkat_risiko_target_id' => $tingkat_risiko_target_id
    ];

    if ($riskAssasementModel->updateRiskAssessment($riskId, $data)) {
        if ($data['nilai_analisis'] > 9 && !($monitoringReviewModel->isMonitoringExist($riskId))) {
            $id = $riskAssasementModel->getLastRiskAssessmentId();
            if ($monitoringReviewModel->createMonitoringReview($id)) {
                $toastMessage = [
                    "icon" => "success",
                    "message" => "Tambah $pageName berhasil!"
                ];
            } else {
                $toastMessage = [
                    "icon" => "error",
                    "message" => "Kesalahan sistem! Silakan coba lagi!"
                ];
            }
        } else {
            $toastMessage = [
                "icon" => "success",
                "message" => "Tambah $pageName berhasil!"
            ];
        }

        $_SESSION['toast_message'] = $toastMessage;
    } else {
        $toastMessage = [
            "icon" => "error",
            "message" => "Kesalahan sistem! Silakan coba lagi!"
        ];
        $_SESSION['toast_message'] = $toastMessage;
    }

    echo '<script>window.location.href = "' . URLEnum::getRiskAssasmentURL() . '";</script>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    $id         = isset($_POST['risk_id']) ? $_POST['risk_id'] : '';
    $is_verifed = isset($_POST['is_verified_' . $id]) ? $_POST['is_verified_' . $id] : '';
    $notes      = isset($_POST['notes_' . $id]) ? $_POST['notes_' . $id] : '';

    if ($riskAssasementModel->verifyRiskAssessment($id, $is_verifed, $notes)) {
        $toastMessage = array(
            "icon" => "success",
            "message" => "Verifikasi $pageName Behasil.!"
        );
        $_SESSION['toast_message'] = $toastMessage;
    } else {
        $toastMessage = array(
            "icon" => "error",
            "message" => "Kesalahan sistem! Silakan coba lagi.!"
        );
        $_SESSION['toast_message'] = $toastMessage;
    }

    $url = URLEnum::getRiskAssasmentURL();
    if (isset($_GET['act'])) {
        $unit_id = $_GET['unit_id'];
        $url .= "?act=show&unit_id=" . $unit_id;
    }

    echo '<script>window.location.href = "' . $url . '";</script>';
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['act'])) {
        $action = $_GET['act'];

        if ($action == "delete") {
            $id     = $_GET['id'];
            if ($riskAssasementModel->deleteRiskAssessment($id)) {
                $toastMessage = array(
                    "icon" => "success",
                    "message" => "Hapus $pageName Behasil.!"
                );
                $_SESSION['toast_message'] = $toastMessage;
            } else {
                $toastMessage = array(
                    "icon" => "error",
                    "message" => "Kesalahan sistem! Silakan coba lagi.!"
                );
                $_SESSION['toast_message'] = $toastMessage;
            }

            $unit_id = $_GET['unit_id'] ?? $unit_id;
            $riskCategorySelected =  $_GET['category'] ?? $riskCategorySelected;
            echo '<script>window.location.href = "' .  URLEnum::getRiskAssasmentURL() . '?act=show' . '&unit_id=' . $unit_id . '&category=' . $riskCategorySelected . '";</script>';
        }

        if ($action == "show") {
            // Ambil parameter dari URL atau gunakan default
            $unit_id = $_GET['unit_id'] ?? $unit_id;
            $riskCategorySelected = $_GET['category'] ?? $riskCategorySelected;
            $monthSelected = $_GET['month'] ?? $monthSelected;
            $yearSelected = $_GET['year'] ?? $yearSelected;

            // Panggil method dengan semua parameter filter
            $allRiskAssasement = $riskAssasementModel->getRiskAssessmentsByUnitCategoryMonthAndYear(
                $unit_id,
                $riskCategorySelected,
                $monthSelected,
                $yearSelected
            );
        }
    }
}
?>

<style>
    .risk-assessment-table th,
    td {
        padding: 4px 8px;
        /* lebih kecil dari default */
        font-size: 12px;
        /* ukuran font lebih kecil */
        line-height: 1.2;
        /* jarak antar baris lebih rapat */
        font-size: 10px;
    }

    table.dataTable {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $pageName ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= $base_url ?>"><?= PageNameEnum::DASHBOARD ?></a></li>
                        <li class="breadcrumb-item active"><?= $pageName ?></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <?php if ($user['role'] == RoleEnum::UNIT): ?>
                            <div class="row">
                                <div class="col-md-9"></div> <!-- Empty column to push the button to the right -->
                                <div class="col-md-3">
                                    <?php if ($_SESSION['role'] == RoleEnum::UNIT) { ?>
                                        <button type="button" class="btn btn-block btn-outline-primary float-md-right" data-toggle="modal" data-target="#modal-add-penilaian-risiko">Tambah <?= $pageName ?></button>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php endif ?>
                        <div class="row">
                            <div class="col-md-2">
                                <span class="text-muted">Pilih Unit</span>
                                <select class="form-control select2" onchange="onChangeFilter(this.value, '<?= $riskCategorySelected ?>', '<?= $monthSelected ?>', '<?= $yearSelected ?>')">
                                    <option value="0" <?= $unit_id == 0 ? 'selected' : '' ?>>Semua</option>
                                    <?php foreach ($units as $unit):
                                        if ($unit['unit'] != RoleEnum::ADMIN):
                                    ?>
                                            <option value="<?= $unit['id'] ?>" <?= $unit['id'] == $unit_id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($unit['unit']) ?>
                                            </option>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <span class="text-muted">Pilih Kategori</span>
                                <select class="form-control select2" onchange="onChangeFilter(<?= $unit_id ?>, this.value, '<?= $monthSelected ?>', '<?= $yearSelected ?>')">
                                    <?php foreach (['Semua' => 'Semua'] + array_column($riskCategories, 'name', 'name') as $val => $name): ?>
                                        <option value="<?= htmlspecialchars($val) ?>" <?= $riskCategorySelected === $val ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <span class="text-muted">Pilih Bulan</span>
                                <select class="form-control select2" onchange="onChangeFilter(<?= $unit_id ?>, '<?= $riskCategorySelected ?>', this.value, '<?= $yearSelected ?>')">
                                    <?php foreach (['Semua' => 'Semua Bulan'] + $months as $val => $name): ?>
                                        <option value="<?= $val ?>" <?= $monthSelected === $val ? 'selected' : '' ?>>
                                            <?= $name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <span class="text-muted">Pilih Tahun</span>
                                <select class="form-control select2" onchange="onChangeFilter(<?= $unit_id ?>, '<?= $riskCategorySelected ?>', '<?= $monthSelected ?>', this.value)">
                                    <?php foreach ($years as $val => $name): ?>
                                        <option value="<?= $val ?>" <?= ($yearSelected == $val) ? 'selected' : '' ?>>
                                            <?= $name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="risk-assassment-table" class="table table-bordered table-striped risk-assessment-table">
                                <thead>
                                    <tr>
                                        <th colspan="7" class="text-center" style="vertical-align: middle;">IDENTIFIKASI RISIKO</th>
                                        <th colspan="8" class="text-center" style="vertical-align: middle;">ANALISIS RISIKO</th>
                                        <th colspan="2" class="text-center" style="vertical-align: middle;">EVALUASI RISIKO</th>
                                        <th colspan="3" class="text-center" style="vertical-align: middle;">RENCANA PENANGANAN RISIKO (RPR)</th>
                                        <th colspan="5" class="text-center" style="vertical-align: middle;">TARGET PENURUNAN TINGKAT RISIKO</th>
                                        <th rowspan="3" class="text-center" style="vertical-align: middle;">Tanggal Dibuat</th>
                                        <!-- <th rowspan="3" class="text-center" style="vertical-align: middle;">Dokumen</th> -->
                                        <th rowspan="3" class="text-center" style="vertical-align: middle;">Status</th>
                                        <th rowspan="3" class="text-center" style="vertical-align: middle;">Catatan</th>
                                        <?php if ($_SESSION['role'] == RoleEnum::ADMIN || $_SESSION['role'] == RoleEnum::UNIT) { ?>
                                            <th rowspan="3" class="text-center" style="vertical-align: middle;">Aksi</th>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <!-- Identifikasi Risiko (Kolom 1-7) -->
                                        <th rowspan="2" class="text-center" style="width: 3%; vertical-align: middle;">No</th>
                                        <th rowspan="2" class="text-center" style="min-width: 100px; vertical-align: middle;">RISIKO</th>
                                        <th rowspan="2" class="text-center" style="min-width: 30px; vertical-align: middle;">KATEGORI RISIKO</th>
                                        <th rowspan="2" class="text-center" style="min-width: 150px; vertical-align: middle;">SEBAB</th>
                                        <th rowspan="2" class="text-center" style="min-width: 30px; vertical-align: middle;">SUMBER RISIKO</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">C/UC</th>
                                        <th rowspan="2" class="text-center" style="min-width: 100px; vertical-align: middle;">DAMPAK</th>

                                        <!-- Pengendalian Yang Ada (Kolom 8-10) -->
                                        <th colspan="3" class="text-center" style="vertical-align: middle;">PENGENDALIAN YANG ADA</th>

                                        <!-- Analisis Risiko (Kolom 11-15) -->
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">P</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">D</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">BOBOT</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">NILAI</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">TINGKAT RISIKO</th>

                                        <!-- Evaluasi Risiko (Kolom 16-17) -->
                                        <th rowspan="2" class="text-center" style="min-width: 100px; vertical-align: middle;">PRIORITAS RISIKO</th>
                                        <th rowspan="2" class="text-center" style="min-width: 120px; vertical-align: middle;">SELERA RISIKO</th>

                                        <!-- Rencana Penanganan Risiko (Kolom 18-20) -->
                                        <th rowspan="2" class="text-center" style="min-width: 80px; vertical-align: middle;">PENANGANAN</th>
                                        <th rowspan="2" class="text-center" style="min-width: 200px; vertical-align: middle;">URAIAN</th>
                                        <th rowspan="2" class="text-center" style="min-width: 50px; vertical-align: middle;">JADWAL</th>

                                        <!-- Target Penurunan (Kolom 21-25) -->
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">P</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">D</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">BOBOT</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">NILAI</th>
                                        <th rowspan="2" class="text-center" style="min-width: 50px; vertical-align: middle;">RISIKO</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="min-width: 200px; vertical-align: middle;">URAIAN</th>
                                        <th class="text-center" style="min-width: 20px; vertical-align: middle;">EFEKTIF</th>
                                        <th class="text-center" style="min-width: 20px; vertical-align: middle;">TIDAK EFEKTIF</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    if (isset($allRiskAssasement) && count($allRiskAssasement) > 0) {
                                        $i = 0;
                                        foreach ($allRiskAssasement as $riskAssasement) {
                                            $i++;
                                            $date = new DateTime($riskAssasement['created_at']);
                                            $created_at = $date->format('d/m/Y H:i');
                                            echo '
                                        <tr>
                                            <td class="text-center">' . $i . '</td>
                                            <td>' . $riskAssasement['risiko'] . '</td>
                                            <td>' . $riskAssasement['kategori_risiko'] . '</td>
                                            <td>' . $riskAssasement['sebab'] . '</td>
                                            <td>' . $riskAssasement['sumber_risiko'] . '</td>
                                            <td>' . $riskAssasement['cuc'] . '</td>
                                            <td>' . $riskAssasement['dampak'] . '</td>
                                            <td>' . $riskAssasement['uraian_pengendalian'] . '</td>
                                            <td>' . ($riskAssasement['efektif_pengendalian'] == "efektif" ? "&#10004;" : "") . '</td>
                                            <td class="text-center">' . ($riskAssasement['efektif_pengendalian'] == "tidak efektif" ? "&#10004;" : "") . '</td>
                                            <td>' . $riskAssasement['p_analisis'] . '</td>
                                            <td>' . $riskAssasement['d_analisis'] . '</td>
                                            <td>' . $riskAssasement['bobot_analisis'] . '</td>
                                            <td>' . $riskAssasement['nilai_analisis'] . '</td>
                                            <td>' . $riskAssasement['tingkat_risiko_analisis'] . '</td>
                                            <td>' . $riskAssasement['prioritas_risiko'] . '</td>
                                            <td>' . $riskAssasement['selera_risiko'] . '</td>
                                            <td>' . $riskAssasement['pilihan_penanganan'] . '</td>
                                            <td>' . $riskAssasement['uraian_penanganan'] . '</td>
                                            <td>' . $riskAssasement['jadwal_pelaksanaan'] . '</td>
                                            <td>' . $riskAssasement['p_target'] . '</td>
                                            <td>' . $riskAssasement['d_target'] . '</td>
                                            <td>' . $riskAssasement['bobot_target'] . '</td>
                                            <td>' . $riskAssasement['nilai_target'] . '</td>
                                            <td>' . $riskAssasement['tingkat_risiko_target'] . '</td>
                                            <td class="text-center">' . $created_at . '</td>';
                                    ?>
                                            <!-- Dokumen Row -->
                                            <!-- <td class="text-center"> -->
                                            <!-- // Ambil nama file dokumen -->
                                            <?php
                                            // if ($riskAssasement['document'] != null) {
                                            //     $document = $riskAssasement['document'];
                                            //     $ext = pathinfo($document, PATHINFO_EXTENSION);

                                            //     // Tentukan ikon berdasarkan ekstensi file menggunakan Font Awesome
                                            //     $icon = '';

                                            //     // Cek jenis file dan pilih ikon yang sesuai
                                            //     if (strpos($ext, 'pdf') !== false) {
                                            //         $icon = 'fas fa-file-pdf'; // Ikon PDF
                                            //     } elseif (strpos($ext, 'jpg') !== false || strpos($ext, 'jpeg') !== false || strpos($ext, 'png') !== false) {
                                            //         $icon = 'fas fa-image'; // Ikon Gambar
                                            //     } elseif (strpos($ext, 'doc') !== false || strpos($ext, 'docx') !== false) {
                                            //         $icon = 'fas fa-file-word'; // Ikon Word
                                            //     } elseif (strpos($ext, 'xls') !== false || strpos($ext, 'xlsx') !== false) {
                                            //         $icon = 'fas fa-file-excel'; // Ikon Excel
                                            //     } else {
                                            //         $icon = 'fas fa-file'; // Ikon default untuk file yang tidak dikenali
                                            //     }
                                            ?>
                                            <!-- Tampilkan ikon yang sesuai dengan jenis file -->
                                            <!-- <a href="<?= $document; ?>" target="_blank">
                                                        <i class="<?php echo $icon; ?>" style="font-size: 24px;"></i>
                                                    </a> -->
                                            <?php
                                            // } 
                                            ?>
                                            <!-- </td> -->
                                            <td class="text-center">
                                                <?php
                                                $date = new DateTime($riskAssasement['verified_at']);
                                                $verified_at = $date->format('d/m/Y H:i');
                                                if ($riskAssasement['is_verified'] === 1 && $riskAssasement['is_verified'] !== null) {
                                                    echo '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Terverifikasi</span>';
                                                    echo '<span class="d-block mt-2 text-muted">' . $verified_at . '</span>';
                                                } elseif ($riskAssasement['is_verified'] === 0 && $riskAssasement['is_verified'] !== null) {
                                                    echo '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Ditolak</span>';
                                                    echo '<span class="d-block mt-2 text-muted">' . $verified_at . '</span>';
                                                } else {
                                                    echo '<span class="badge badge-secondary"><i class="fas fa-clock"></i> Belum Diverifikasi</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center"><?= $riskAssasement['notes'] ?></td>
                                            <?php
                                            if ($_SESSION['role'] === RoleEnum::ADMIN && (!$riskAssasement['is_verified'] && $riskAssasement['is_verified'] === null)) {
                                                echo '
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-verif-risk-' . $riskAssasement['id'] . '"><i class="fas fa-edit"></i></button>
                                                        <a href="' . URLEnum::getRiskAssasmentURL() . '?act=delete' . '&id=' . $riskAssasement['id'] . (isset($unit_id) ? '&unit_id=' . $unit_id : '') . (isset($riskCategorySelected) ? '&category=' . $riskCategorySelected : '') . '" class="btn btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>';
                                            } elseif ($_SESSION['role'] === RoleEnum::ADMIN && (($riskAssasement['is_verified'] || !$riskAssasement['is_verified']) && $riskAssasement['is_verified'] !== null)) { ?>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a class="btn btn-danger" href="<?= URLEnum::getRiskAssasmentURL() ?>?act=delete&id=<?= $riskAssasement['id'] ?><?= (isset($unit_id) ? '&unit_id=' . $unit_id : '') . (isset($riskCategorySelected) ? '&category=' . $riskCategorySelected : '') ?>" class="btn btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>

                                            <?php
                                            } elseif ($_SESSION['role'] == RoleEnum::UNIT && $user['unit_id'] == $riskAssasement['unit_id']) {
                                                $isDisabled = $riskAssasement['is_verified'] == 1 ? 'disabled' : '';

                                                echo '
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <button type="button" 
                                                            class="btn btn-warning btn-edit-risiko" 
                                                            data-toggle="modal" 
                                                            data-target="#modalUpdateRisiko"
                                                            data-id="' . $riskAssasement['id'] . '"
                                                            data-risiko="' . htmlspecialchars($riskAssasement['risiko']) . '"
                                                            data-kategori="' . $riskAssasement['kategori_risiko_id'] . '"
                                                            data-sebab="' . htmlspecialchars($riskAssasement['sebab']) . '"
                                                            data-sumber="' . $riskAssasement['sumber_risiko'] . '"
                                                            data-cuc="' . $riskAssasement['cuc'] . '"
                                                            data-dampak="' . htmlspecialchars($riskAssasement['dampak']) . '"
                                                            data-pengendalian="' . htmlspecialchars($riskAssasement['uraian_pengendalian']) . '"
                                                            data-efektif="' . $riskAssasement['efektif_pengendalian'] . '"
                                                            data-p="' . $riskAssasement['p_analisis'] . '"
                                                            data-d="' . $riskAssasement['d_analisis'] . '"
                                                            data-bobot="' . $riskAssasement['bobot_analisis'] . '"
                                                            data-nilai="' . $riskAssasement['nilai_analisis'] . '"
                                                            data-tingkat="' . $riskAssasement['tingkat_risiko_analisis_id'] . '"
                                                            data-prioritas="' . $riskAssasement['prioritas_risiko_id'] . '"
                                                            data-selera="' . $riskAssasement['selera_risiko'] . '"
                                                            data-pilihan="' . $riskAssasement['pilihan_penanganan'] . '"
                                                            data-uraian="' . htmlspecialchars($riskAssasement['uraian_penanganan']) . '"
                                                            data-jadwal="' . htmlspecialchars($riskAssasement['jadwal_pelaksanaan']) . '"
                                                            data-p-target="' . $riskAssasement['p_target'] . '"
                                                            data-d-target="' . $riskAssasement['d_target'] . '"
                                                            data-bobot-target="' . $riskAssasement['bobot_target'] . '"
                                                            data-nilai-target="' . $riskAssasement['nilai_target'] . '"
                                                            data-tingkat-target="' . $riskAssasement['tingkat_risiko_target_id'] . '"' .
                                                    $isDisabled . '
                                                        >
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                ';
                                            } else {
                                                echo '<td></td>';
                                            }
                                            echo '</tr>'; ?>

                                            <!-- /. modal-verif-risk -->
                                            <div class="modal fade" id="modal-verif-risk-<?= $riskAssasement['id'] ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="">
                                                            <input type="text" class="form-control" name="risk_id" value="<?= $riskAssasement['id'] ?>" hidden>

                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Verifikasi <?= $pageName ?></h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- radio -->
                                                                <?php if ($riskAssasement['is_verified'] == 1 && $riskAssasement['verified_at'] !== '0000-00-00 00:00:00' && $riskAssasement['verified_at'] != null): ?>
                                                                    <div class="text-center mt-3">
                                                                        <i class="fas fa-check-circle fa-4x text-success"></i><br />
                                                                        <span class="badge badge-success">Terverifikasi</span>
                                                                        <p class="text-muted mt-2">Tanggal: <?= date('d-m-Y H:i', strtotime($riskAssasement['verified_at'])) ?></p>
                                                                    </div>
                                                                <?php elseif ($riskAssasement['is_verified'] == 0 && $riskAssasement['verified_at'] !== '0000-00-00 00:00:00' && $riskAssasement['verified_at'] != null): ?>
                                                                    <div class="text-center mt-3">
                                                                        <i class="fas fa-times-circle fa-4x text-danger"></i><br />
                                                                        <span class="badge badge-danger">Ditolak</span>
                                                                        <p class="text-muted mt-2">Tanggal: <?= date('d-m-Y H:i', strtotime($riskAssasement['verified_at'])) ?></p>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="text-center mt-3">
                                                                        <i class="fas fa-clock fa-4x text-secondary"></i><br />
                                                                        <span class="badge badge-secondary">Belum Diverifikasi</span>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div class="form-group mt-4">
                                                                    <label class="d-block text-center">Verifikasi Penilaian Risiko<span class="text-danger">*</span></label>
                                                                    <div class="row justify-content-center">
                                                                        <div class="icheck-success col-md-2">
                                                                            <input class="form-check-input" type="radio" onchange="onShowNotes(false, <?= $riskAssasement['id'] ?>)"
                                                                                name="is_verified_<?= $riskAssasement['id'] ?>"
                                                                                value="1" id="verified-yes-<?= $riskAssasement['id'] ?>"
                                                                                <?= (($riskAssasement['is_verified'] == true && $riskAssasement['verified_at'] != null) || ($riskAssasement['is_verified'] == false && $riskAssasement['verified_at'] == '0000-00-00 00:00:00')) ? 'checked' : '' ?>>
                                                                            <label class="form-check-label" for="verified-yes-<?= $riskAssasement['id'] ?>">Ya<span class="text-danger">*</span></label>
                                                                        </div>
                                                                        <div class="icheck-danger col-md-2">
                                                                            <input class="form-check-input" type="radio" onchange="onShowNotes(true, <?= $riskAssasement['id'] ?>)"
                                                                                name="is_verified_<?= $riskAssasement['id'] ?>"
                                                                                value="0" id="verified-no-<?= $riskAssasement['id'] ?>"
                                                                                <?= ($riskAssasement['is_verified'] == false && $riskAssasement['verified_at'] != null) ? 'checked' : '' ?>>
                                                                            <label class="form-check-label" for="verified-no-<?= $riskAssasement['id'] ?>">Tidak<span class="text-danger">*</span></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- radio -->
                                                                <!-- Notes -->
                                                                <div class="form-group mt-2" id="notes-wrapper-<?= $riskAssasement['id'] ?>" style="display: <?= ($riskAssasement['is_verified'] == false && $riskAssasement['verified_at'] != null) ? 'block' : 'none' ?>;">
                                                                    <h6 for="inputNotes" class="d-block">Catatan<span class="text-danger">*</span></h6>
                                                                    <textarea type="text" id="inputNotes" class="form-control" name="notes_<?= $riskAssasement['id'] ?>"><?= $riskAssasement['notes'] ?></textarea>
                                                                </div>
                                                                <!-- close notes -->
                                                            </div>
                                                            <div class="modal-footer justify-content-between">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary" name="verify">Submit</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>
                                            <!-- /. modal-update-visitor -->
                                    <?php
                                        }
                                    } else {
                                        echo '
                                        <tr>
                                            <td colspan="30" class="text-center">Tidak ada ' . $pageName . '</td>
                                        </tr>
                                    ';
                                    }
                                    ?>
                                </tbody>

                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<!-- Modal Update Risk -->
<div class="modal fade" id="modalUpdateRisiko" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Update <?= $pageName ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" id="inputRiskIdUpdate" class="form-control" name="risk_id" hidden required>
                    <label class="modal-title">Identifikasi Risiko</label>
                    <hr>
                    <div class="form-group">
                        <h6 for="inputRisikoUpdate">Risiko <span class="text-danger">*</span></h6>
                        <textarea type="text" id="inputRisikoUpdate" class="form-control" name="risiko" required></textarea>
                    </div>
                    <div class="form-group">
                        <h6 for="inputKategoriRisikoUpdate">Kategori Risiko<span class="text-danger">*</span></h6>
                        <select class="form-control" id="inputKategoriRisikoUpdate" name="kategori_risiko_id">
                            <option disabled selected>Pilih Kategori Risiko</option>
                            <?php foreach ($riskCategories as $riskCategorie): ?>
                                <option value="<?= $riskCategorie['id'] ?>"><?= $riskCategorie['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputSebabUpdate">Sebab <span class="text-danger">*</span></h6>
                        <textarea type="text" id="inputSebabUpdate" class="form-control" name="sebab" required></textarea>
                    </div>
                    <div class="form-group">
                        <h6 for="inputSumberRisikoUpdate">Sumber Risiko <span class="text-danger">*</span></h6>
                        <select class="form-control" id="inputSumberRisikoUpdate" name="sumber_risiko" required>
                            <option disabled selected>Pilih Sumber Risiko</option>
                            <option value="internal">Internal</option>
                            <option value="eksternal">eksternal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputCUCUpdate">C/UC <span class="text-danger">*</span></h6>
                        <select class="form-control" id="inputCUCUpdate" name="cuc" required>
                            <option disabled selected>Pilih C/UC</option>
                            <option value="c">C</option>
                            <option value="uc">UC</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputDampakUpdate">Dampak <span class="text-danger">*</span></h6>
                        <textarea type="text" id="inputDampakUpdate" class="form-control" name="dampak" required></textarea>
                    </div>

                    <br />
                    <hr>
                    <label class="modal-title">Analisis Risiko</label>
                    <hr>
                    <p class="modal-title">Pengendalian Yang Ada</p>
                    <div class="form-group">
                        <h6 for="inputUraianPengendalianUpdate">Uraian <span class="text-danger">*</span></h6>
                        <textarea type="text" id="inputUraianPengendalianUpdate" class="form-control" name="uraian_pengendalian" required></textarea>
                    </div>
                    <div class="form-group">
                        <h6 for="inputEfektifUpdate">Efektif/Tidak Efektif <span class="text-danger">*</span></h6>
                        <select class="form-control" id="inputEfektifUpdate" name="efektif_pengendalian" required>
                            <option disabled selected>Pilih Efektif/Tidak Efektif</option>
                            <option value="efektif">Efektif</option>
                            <option value="tidak efektif">Tidak Efektif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputPAnalisisUpdate">P<span class="text-danger">*</span></h6>
                        <select class="form-control" name="p_analisis" id="inputPAnalisisUpdate">
                            <option disabled selected>Pilih Nilai P Analisis</option>
                            <?php foreach ($riskProbabilities as $riskProbability): ?>
                                <option value="<?= $riskProbability['id'] ?>" data-value="<?= $riskProbability['value'] ?>"><?= $riskProbability['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputDAnalisisUpdate">D<span class="text-danger">*</span></h6>
                        <select class="form-control" name="d_analisis" id="inputDAnalisisUpdate">
                            <option disabled selected>Pilih Nilai D Analisis</option>
                            <?php foreach ($riskImpacts as $riskImpact): ?>
                                <option value="<?= $riskImpact['id'] ?>" data-value="<?= $riskImpact['value'] ?>"><?= $riskImpact['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputBobotAnalisisUpdate">Bobot<span class="text-danger">*</span></h6>
                        <input type="number" id="inputBobotAnalisisUpdate" class="form-control" name="bobot_analisis" step="0.01" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputNilaiAnalisisUpdate">Nilai<span class="text-danger">*</span></h6>
                        <input type="number" id="inputNilaiAnalisisUpdate" class="form-control" name="nilai_analisis" placeholder="0" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputTingkatRisikoAnalisisUpdate">Tingkat Risiko<span class="text-danger">*</span></h6>
                        <input type="text" id="outputTingkatRisikoAnalisisUpdate" class="form-control" readonly required>
                        <select class="form-control" id="inputTingkatRisikoAnalisisUpdate" name="tingkat_risiko_analisis_id" hidden>
                            <option disabled selected>Pilih Tingkat Risiko</option>
                            <?php foreach ($riskLevels as $riskLevel): ?>
                                <option value="<?= $riskLevel['id'] ?>"><?= $riskLevel['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <br />
                    <hr>
                    <label class="modal-title">Evaluasi Risiko</label>
                    <hr>
                    <div class="form-group">
                        <h6 for="inputPrioritasRisikoUpdate">Prioritas Risiko<span class="text-danger">*</span></h6>
                        <input type="text" id="outputPrioritasRisikoUpdate" class="form-control" readonly required>
                        <select class="form-control" name="prioritas_risiko_id" id="inputPrioritasRisikoUpdate" hidden>
                            <option disabled selected>Pilih Prioritas Risiko</option>
                            <?php foreach ($riskPriorities as $riskPriority): ?>
                                <option value="<?= $riskPriority['id'] ?>"><?= $riskPriority['code'] . ' - ' . $riskPriority['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputSeleraRisikoUpdate">Selera Risiko<span class="text-danger">*</span></h6>
                        <input type="text" id="inputSeleraRisikoUpdate" class="form-control" name="selera_risiko" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputPilihanPenangananRisikoUpdate">Pilihan Penanganan Risiko<span class="text-danger">*</span></h6>
                        <input type="text" id="inputPilihanPenangananRisikoUpdate" class="form-control" name="pilihan_penanganan" readonly required>
                    </div>

                    <br />
                    <hr>
                    <label class="modal-title">Rencana Penanganan Risiko (RPR)</label>
                    <hr>
                    <div class="form-group">
                        <h6 for="inputUraian">Uraian<span class="text-danger">*</span></h6>
                        <textarea type="text" id="inputUraian" class="form-control" name="uraian_penanganan" required></textarea>
                    </div>
                    <div class="form-group">
                        <h6 for="inputJadwalPelaksanaan">Jadwal Pelaksanaan<span class="text-danger">*</span></h6>
                        <input type="text" id="inputJadwalPelaksanaan" class="form-control" name="jadwal_pelaksanaan" placeholder="Setiap Bulan" required>
                    </div>

                    <br />
                    <hr>
                    <label class="modal-title">Target Penurunan Tingkat Risiko</label>
                    <hr>
                    <div class="form-group">
                        <h6 for="inputPTargetUpdate">P<span class="text-danger">*</span></h6>
                        <select class="form-control" name="p_target" id="inputPTargetUpdate">
                            <option disabled selected>Pilih Nilai P Target</option>
                            <?php foreach ($riskProbabilities as $riskProbability): ?>
                                <option value="<?= $riskProbability['id'] ?>" data-value="<?= $riskProbability['value'] ?>"><?= $riskProbability['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputDTargetUpdate">D<span class="text-danger">*</span></h6>
                        <select class="form-control" name="d_target" id="inputDTargetUpdate">
                            <option disabled selected>Pilih Tingkat D Target</option>
                            <?php foreach ($riskImpacts as $riskImpact): ?>
                                <option value="<?= $riskImpact['id'] ?>" data-value="<?= $riskImpact['value'] ?>"><?= $riskImpact['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputBobotTargetUpdate">Bobot<span class="text-danger">*</span></h6>
                        <input type="text" id="inputBobotTargetUpdate" class="form-control" name="bobot_target" placeholder="0.0" step="0.01" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputNilaiTargetUpdate">Nilai<span class="text-danger">*</span></h6>
                        <input type="text" id="inputNilaiTargetUpdate" class="form-control" name="nilai_target" placeholder="0" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputTingkatRisikoTargetUpdate">Tingkat Risiko Target<span class="text-danger">*</span></h6>
                        <input type="text" id="outputTingkatRisikoTargetUpdate" class="form-control" step="0.01" readonly required>
                        <select class="form-control" id="inputTingkatRisikoTargetUpdate" name="tingkat_risiko_target_id" hidden>
                            <option disabled selected>Pilih Tingkat Risiko Target</option>
                            <?php foreach ($riskLevels as $riskLevel): ?>
                                <option value=" <?= $riskLevel['id'] ?>"><?= $riskLevel['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- <div class="form-group">
                        <h6 for="exampleInputFile">Upload Dokumen Bukti Penilaian</h6>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="exampleInputFile" name="document">
                                <label class="custom-file-label" for="exampleInputFile">Pilih file</label>
                            </div>
                            <div class="input-group-append">
                                <span class="input-group-text">Upload</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Format: PDF, DOC, DOCX, JPG, PNG</small>
                    </div> -->
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="update">Simpan</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal-update-risk -->

<!-- /. modal-add-penilaian-risiko -->
<div class="modal fade" id="modal-add-penilaian-risiko">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah <?= $pageName ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label class="modal-title">Identifikasi Risiko</label>
                    <hr>
                    <div class="form-group">
                        <h6 for="inputRisiko">Risiko <span class="text-danger">*</span></h6>
                        <textarea type="text" id="inputRisiko" class="form-control" name="risiko" required></textarea>
                    </div>
                    <div class="form-group">
                        <h6 for="inputKategoriRisiko">Kategori Risiko<span class="text-danger">*</span></h6>
                        <select class="form-control" name="kategori_risiko_id">
                            <option disabled selected>Pilih Kategori Risiko</option>
                            <?php foreach ($riskCategories as $riskCategorie): ?>
                                <option value="<?= $riskCategorie['id'] ?>"><?= $riskCategorie['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputSebab">Sebab <span class="text-danger">*</span></h6>
                        <textarea type="text" id="inputSebab" class="form-control" name="sebab" required></textarea>
                    </div>
                    <div class="form-group">
                        <h6 for="inputSumberRisiko">Sumber Risiko <span class="text-danger">*</span></h6>
                        <select class="form-control" name="sumber_risiko" required>
                            <option disabled selected>Pilih Sumber Risiko</option>
                            <option value="internal">Internal</option>
                            <option value="eksternal">eksternal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputCUC">C/UC <span class="text-danger">*</span></h6>
                        <select class="form-control" name="cuc" required>
                            <option disabled selected>Pilih C/UC</option>
                            <option value="c">C</option>
                            <option value="uc">UC</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputDampak">Dampak <span class="text-danger">*</span></h6>
                        <textarea type="text" id="inputDampak" class="form-control" name="dampak" required></textarea>
                    </div>

                    <br />
                    <hr>
                    <label class="modal-title">Analisis Risiko</label>
                    <hr>
                    <p class="modal-title">Pengendalian Yang Ada</p>
                    <div class="form-group">
                        <h6 for="inputUraianPengendalian">Uraian <span class="text-danger">*</span></h6>
                        <textarea type="text" id="inputUraianPengendalian" class="form-control" name="uraian_pengendalian" required></textarea>
                    </div>
                    <div class="form-group">
                        <h6 for="inputEfektif">Efektif/Tidak Efektif <span class="text-danger">*</span></h6>
                        <select class="form-control" name="efektif_pengendalian" required>
                            <option disabled selected>Pilih Efektif/Tidak Efektif</option>
                            <option value="efektif">Efektif</option>
                            <option value="tidak efektif">Tidak Efektif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputPAnalisisCreate">P<span class="text-danger">*</span></h6>
                        <select class="form-control" name="p_analisis" id="inputPAnalisisCreate">
                            <option disabled selected>Pilih Nilai P Analisis</option>
                            <?php foreach ($riskProbabilities as $riskProbability): ?>
                                <option value="<?= $riskProbability['id'] ?>" data-value="<?= $riskProbability['value'] ?>"><?= $riskProbability['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputDAnalisisCreate">D<span class="text-danger">*</span></h6>
                        <select class="form-control" name="d_analisis" id="inputDAnalisisCreate">
                            <option disabled selected>Pilih Nilai D Analisis</option>
                            <?php foreach ($riskImpacts as $riskImpact): ?>
                                <option value="<?= $riskImpact['id'] ?>" data-value="<?= $riskImpact['value'] ?>"><?= $riskImpact['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputBobotAnalisisCreate">Bobot<span class="text-danger">*</span></h6>
                        <input type="number" id="inputBobotAnalisisCreate" class="form-control" name="bobot_analisis" step="0.01" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputNilaiAnalisisCreate">Nilai<span class="text-danger">*</span></h6>
                        <input type="number" id="inputNilaiAnalisisCreate" class="form-control" name="nilai_analisis" placeholder="0" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputTingkatRisikoAnalisisCreate">Tingkat Risiko<span class="text-danger">*</span></h6>
                        <input type="text" id="outputTingkatRisikoAnalisisCreate" class="form-control" readonly required>
                        <select class="form-control" id="inputTingkatRisikoAnalisisCreate" name="tingkat_risiko_analisis_id" hidden>
                            <option disabled selected>Pilih Tingkat Risiko</option>
                            <?php foreach ($riskLevels as $riskLevel): ?>
                                <option value="<?= $riskLevel['id'] ?>"><?= $riskLevel['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <br />
                    <hr>
                    <label class="modal-title">Evaluasi Risiko</label>
                    <hr>
                    <div class="form-group">
                        <h6 for="inputPrioritasRisikoCreate">Prioritas Risiko<span class="text-danger">*</span></h6>
                        <input type="text" id="outputPrioritasRisikoCreate" class="form-control" readonly required>
                        <select class="form-control" name="prioritas_risiko_id" id="inputPrioritasRisikoCreate" hidden>
                            <option disabled selected>Pilih Prioritas Risiko</option>
                            <?php foreach ($riskPriorities as $riskPriority): ?>
                                <option value="<?= $riskPriority['id'] ?>"><?= $riskPriority['code'] . ' - ' . $riskPriority['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputSeleraRisikoCreate">Selera Risiko<span class="text-danger">*</span></h6>
                        <input type="text" id="inputSeleraRisikoCreate" class="form-control" name="selera_risiko" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputPilihanPenangananRisikoCreate">Pilihan Penanganan Risiko<span class="text-danger">*</span></h6>
                        <input type="text" id="inputPilihanPenangananRisikoCreate" class="form-control" name="pilihan_penanganan" readonly required>
                    </div>

                    <br />
                    <hr>
                    <label class="modal-title">Rencana Penanganan Risiko (RPR)</label>
                    <hr>
                    <div class="form-group">
                        <h6 for="inputUraian">Uraian<span class="text-danger">*</span></h6>
                        <textarea type="text" id="inputUraian" class="form-control" name="uraian_penanganan" required></textarea>
                    </div>
                    <div class="form-group">
                        <h6 for="inputJadwalPelaksanaan">Jadwal Pelaksanaan<span class="text-danger">*</span></h6>
                        <input type="text" id="inputJadwalPelaksanaan" class="form-control" name="jadwal_pelaksanaan" placeholder="Setiap Bulan" required>
                    </div>

                    <br />
                    <hr>
                    <label class="modal-title">Target Penurunan Tingkat Risiko</label>
                    <hr>
                    <div class="form-group">
                        <h6 for="inputPTargetCreate">P<span class="text-danger">*</span></h6>
                        <select class="form-control" name="p_target" id="inputPTargetCreate">
                            <option disabled selected>Pilih Nilai P Target</option>
                            <?php foreach ($riskProbabilities as $riskProbability): ?>
                                <option value="<?= $riskProbability['id'] ?>" data-value="<?= $riskProbability['value'] ?>"><?= $riskProbability['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputDTargetCreate">D<span class="text-danger">*</span></h6>
                        <select class="form-control" name="d_target" id="inputDTargetCreate">
                            <option disabled selected>Pilih Tingkat D Target</option>
                            <?php foreach ($riskImpacts as $riskImpact): ?>
                                <option value="<?= $riskImpact['id'] ?>" data-value="<?= $riskImpact['value'] ?>"><?= $riskImpact['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputBobotTargetCreate">Bobot<span class="text-danger">*</span></h6>
                        <input type="text" id="inputBobotTargetCreate" class="form-control" name="bobot_target" placeholder="0.0" step="0.01" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputNilaiTargetCreate">Nilai<span class="text-danger">*</span></h6>
                        <input type="text" id="inputNilaiTargetCreate" class="form-control" name="nilai_target" placeholder="0" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputTingkatRisikoTargetCreate">Tingkat Risiko Target<span class="text-danger">*</span></h6>
                        <input type="text" id="outputTingkatRisikoTargetCreate" class="form-control" step="0.01" readonly required>
                        <select class="form-control" id="inputTingkatRisikoTargetCreate" name="tingkat_risiko_target_id" hidden>
                            <option disabled selected>Pilih Tingkat Risiko Target</option>
                            <?php foreach ($riskLevels as $riskLevel): ?>
                                <option value=" <?= $riskLevel['id'] ?>"><?= $riskLevel['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- <div class="form-group">
                        <h6 for="exampleInputFile">Upload Dokumen Bukti Penilaian</h6>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="exampleInputFile" name="document">
                                <label class="custom-file-label" for="exampleInputFile">Pilih file</label>
                            </div>
                            <div class="input-group-append">
                                <span class="input-group-text">Upload</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">Format: PDF, DOC, DOCX, JPG, PNG</small>
                    </div> -->
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="save">Simpan</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /. modal-add-penilaian-risiko -->


<script>
    function validateInput(input) {
        input.value = input.value.replace(/^0+/, '');
    }

    $(document).ready(function() {
        $('#risk-assassment-table').DataTable({
            scrollX: true, // Aktifkan horizontal scroll
            responsive: false, // Nonaktifkan mode responsive
            fixedColumns: false, // Pastikan tidak ada kolom fixed,
            autoWidth: false, // Ini akan membuat kolom menyesuaikan isi
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Handle edit button clicks
        document.querySelectorAll('.btn-edit-risiko').forEach(button => {
            button.addEventListener('click', function() {
                const data = this.dataset;

                // Set form values
                document.getElementById('inputRiskIdUpdate').value = data.id;
                document.getElementById('inputRisikoUpdate').value = data.risiko;
                document.getElementById('inputKategoriRisikoUpdate').value = data.kategori;
                document.getElementById('inputSebabUpdate').value = data.sebab;
                document.getElementById('inputSumberRisikoUpdate').value = data.sumber;
                document.getElementById('inputCUCUpdate').value = data.cuc;
                document.getElementById('inputDampakUpdate').value = data.dampak;
                document.getElementById('inputUraianPengendalianUpdate').value = data.pengendalian;
                document.getElementById('inputEfektifUpdate').value = data.efektif;

                // Analysis section
                setSelectValueByOptionValue('inputPAnalisisUpdate', data.p);
                setSelectValueByOptionValue('inputDAnalisisUpdate', data.d);

                // Action plan
                document.getElementById('inputUraian').value = data.uraian;
                document.getElementById('inputJadwalPelaksanaan').value = data.jadwal;

                // Target section
                setSelectValueByOptionValue('inputPTargetUpdate', data.pTarget);
                setSelectValueByOptionValue('inputDTargetUpdate', data.dTarget);

                // Trigger calculations if needed
                if (typeof calculateAll === 'function') {
                    calculateAll();
                }
            });
        });

        // Your existing calculation functions
        function calculateAll() {
            // Your existing calculation logic
        }

        // Initialize any other needed functionality
    });

    function setSelectValueByOptionValue(selectElementId, targetValue) {
        const select = document.getElementById(selectElementId);
        if (!select) {
            console.error(`❌ Element with ID "${selectElementId}" not found.`);
            return;
        }

        const option = Array.from(select.options).find(opt => opt.value === targetValue);
        if (option) {
            select.value = targetValue;
            select.dispatchEvent(new Event('change'));
        } else {
            console.error(`❌ Value "${targetValue}" not found in options for select ID "${selectElementId}".`);
        }
    }

    function onChangeFilter(unitId, categorySelected, monthSelected, yearSelected) {
        var params = {
            act: 'show'
        };

        params.unit_id = unitId;
        if (categorySelected != "Semua") params.category = categorySelected;
        if (monthSelected != "Semua") params.month = monthSelected;
        if (yearSelected != "Semua") params.year = yearSelected;

        var queryString = Object.keys(params)
            .map(key => key + '=' + encodeURIComponent(params[key]))
            .join('&');

        window.location.href = "<?= URLEnum::getRiskAssasmentURL() ?>?" + queryString;
    }

    function onShowNotes(show, id) {
        const notesWrapper = document.getElementById(`notes-wrapper-${id}`);
        const notesInput = notesWrapper.querySelector('textarea');

        if (show) {
            notesWrapper.style.display = 'block';
            notesInput.required = true;
        } else {
            notesWrapper.style.display = 'none';
            notesInput.required = false;
        }
    }

    // PERHITUNGAN AUTOFILL P & D
    // document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    //     var fileName = document.getElementById("exampleInputFile").files[0].name;
    //     var nextSibling = e.target.nextElementSibling;
    //     nextSibling.innerText = fileName;
    // });

    document.addEventListener("DOMContentLoaded", function() {
        function setupAutoBobot(pId, dId, bobotId) {

            const bobotMatrix = [
                [1.5, 1.4, 1.13, 1.15, 1], // P = 5
                [1.2, 1.19, 1.3, 1.16, 1.2], // P = 4
                [1.17, 1.42, 1.43, 1.46, 1.47], // P = 3
                [1, 1.8, 1.83, 1.9, 2.1], // P = 2
                [1, 1.5, 2, 3, 4] // P = 1
            ];

            const inputP = document.getElementById(pId);
            const inputD = document.getElementById(dId);
            const inputBobot = document.getElementById(bobotId);

            if (!inputP || !inputD || !inputBobot) return;

            function updateBobot() {
                const selectedP = inputP.options[inputP.selectedIndex];
                const selectedD = inputD.options[inputD.selectedIndex];

                const p = parseInt(selectedP?.dataset.value);
                const d = parseInt(selectedD?.dataset.value);

                if (!isNaN(p) && !isNaN(d) && p >= 1 && p <= 5 && d >= 1 && d <= 5) {
                    const bobot = bobotMatrix[5 - p][d - 1];
                    inputBobot.value = bobot.toFixed(2);
                } else {
                    inputBobot.value = 0;
                }
            }

            inputP.addEventListener('change', updateBobot);
            inputD.addEventListener('change', updateBobot);

            // Jalankan saat pertama kali untuk set default (jika ada nilai terpilih)
            updateBobot();
        }

        function setupAutoNilai(pId, dId, bobotId, nilaiId) {
            const inputP = document.getElementById(pId);
            const inputD = document.getElementById(dId);
            const inputBobot = document.getElementById(bobotId);
            const inputNilai = document.getElementById(nilaiId);

            if (!inputP || !inputD || !inputBobot) return;

            function updateNilai() {
                const p = parseInt(inputP.value);
                const d = parseInt(inputD.value);
                const bobot = inputBobot.value;

                if (!isNaN(p) && !isNaN(d) && !isNaN(bobot)) {
                    const nilai = p * d * bobot;
                    inputNilai.value = Math.round(nilai);
                } else {
                    inputNilai.value = 0;
                }
            }

            inputP.addEventListener('change', updateNilai);
            inputD.addEventListener('change', updateNilai);
            updateNilai();
        }

        function setupTingkatRisiko(pId, dId, nilaiId, tingkatRisikoId, outputId) {
            const inputP = document.getElementById(pId);
            const inputD = document.getElementById(dId);
            const inputNilai = document.getElementById(nilaiId);
            const tingkatRisikoSelect = document.getElementById(tingkatRisikoId);
            const output = document.getElementById(outputId);

            function hitungTingkatRisiko(nilai) {
                if (nilai <= 4) return "Sangat Rendah";
                if (nilai <= 9) return "Rendah";
                if (nilai <= 14) return "Sedang";
                if (nilai <= 19) return "Tinggi";
                return "Sangat Tinggi";
            }

            function updateTingkatRisiko() {
                const nilaiValue = parseFloat(inputNilai.value);

                if (!isNaN(nilaiValue)) {
                    const tingkatRisiko = hitungTingkatRisiko(nilaiValue).trim().toLowerCase();

                    for (let i = 0; i < tingkatRisikoSelect.options.length; i++) {
                        const optionText = tingkatRisikoSelect.options[i].text.trim().toLowerCase();

                        if (optionText === tingkatRisiko) {
                            tingkatRisikoSelect.selectedIndex = i;
                            output.value = tingkatRisikoSelect.options[i].text; // tampil ke user
                            break;
                        }
                    }
                } else {
                    tingkatRisikoSelect.selectedIndex = 0;
                    output.value = ''; // kosongkan tampilan jika nilai invalid
                }
            }

            inputP.addEventListener('change', updateTingkatRisiko);
            inputD.addEventListener('change', updateTingkatRisiko);
            updateTingkatRisiko(); // langsung panggil sekali
        }

        function setupPrioritasRisiko(pId, dId, nilaiInputId, prioritasOutputId, outputId) {
            const inputP = document.getElementById(pId);
            const inputD = document.getElementById(dId);
            const inputNilai = document.getElementById(nilaiInputId);
            const prioritasSelect = document.getElementById(prioritasOutputId);
            const output = document.getElementById(outputId);

            function hitungPrioritas(nilai) {
                if (nilai <= 4) return "5";
                if (nilai <= 9) return "4";
                if (nilai <= 14) return "3";
                if (nilai <= 19) return "2";
                return "1";
            }

            function updatePrioritas() {
                const nilai = parseFloat(inputNilai.value);
                if (!isNaN(nilai)) {
                    const prioritas = hitungPrioritas(nilai);
                    prioritasSelect.value = prioritas;
                    output.value = prioritas;
                } else {
                    prioritasSelect.value = "";
                    output.value = "";
                }
            }

            inputP.addEventListener('change', updatePrioritas);
            inputD.addEventListener('change', updatePrioritas);
            updatePrioritas(); // inisialisasi awal
        }

        function setupSeleraRisiko(pId, dId, nilaiInputId, seleraRisikoId) {
            const inputP = document.getElementById(pId);
            const inputD = document.getElementById(dId);
            const inputNilai = document.getElementById(nilaiInputId);
            const inputSeleraRisiko = document.getElementById(seleraRisikoId);

            function hitungSeleraRisiko(nilai) {
                if (nilai <= 9) return "dalam batas selera risiko";
                return "diatas batas selera risiko";
            }

            function updateSeleraRisiko() {
                const nilai = parseFloat(inputNilai.value);
                if (!isNaN(nilai)) {
                    const prioritas = hitungSeleraRisiko(nilai);
                    inputSeleraRisiko.value = prioritas;
                } else {
                    inputSeleraRisiko.value = "";
                }
            }

            inputP.addEventListener('change', updateSeleraRisiko);
            inputD.addEventListener('change', updateSeleraRisiko);
            updateSeleraRisiko(); // inisialisasi awal
        }

        function setupPilihanPenanganan(pId, dId, seleraRisikoId, pilihanOutputId) {
            const inputP = document.getElementById(pId);
            const inputD = document.getElementById(dId);
            const inputSeleraRisiko = document.getElementById(seleraRisikoId);
            const outputPilihan = document.getElementById(pilihanOutputId);

            function hitungPilihan(seleraRisiko) {
                if (seleraRisiko.toLowerCase() === "dalam batas selera risiko") {
                    return "Menerima risiko";
                } else if (seleraRisiko.toLowerCase() === "diatas batas selera risiko") {
                    return "Mitigasi Risiko";
                }
                return "";
            }

            function updatePilihan() {
                const seleraRisiko = inputSeleraRisiko.value.trim();
                const pilihan = hitungPilihan(seleraRisiko);
                outputPilihan.value = pilihan;
            }

            inputP.addEventListener('change', updatePilihan);
            inputD.addEventListener('change', updatePilihan);
            updatePilihan(); // Jalankan saat inisialisasi
        }


        // Form Create
        // Bobbot
        setupAutoBobot("inputPAnalisisCreate", "inputDAnalisisCreate", "inputBobotAnalisisCreate");
        setupAutoBobot("inputPTargetCreate", "inputDTargetCreate", "inputBobotTargetCreate");

        // Nilai
        setupAutoNilai("inputPAnalisisCreate", "inputDAnalisisCreate", "inputBobotAnalisisCreate", "inputNilaiAnalisisCreate");
        setupAutoNilai("inputPTargetCreate", "inputDTargetCreate", "inputBobotTargetCreate", "inputNilaiTargetCreate");

        // Tingkat Risiko
        setupTingkatRisiko("inputPAnalisisCreate", "inputDAnalisisCreate", "inputNilaiAnalisisCreate", "inputTingkatRisikoAnalisisCreate", "outputTingkatRisikoAnalisisCreate");
        setupTingkatRisiko("inputPTargetCreate", "inputDTargetCreate", "inputNilaiTargetCreate", "inputTingkatRisikoTargetCreate", "outputTingkatRisikoTargetCreate");

        // Prioritas Risiko
        setupPrioritasRisiko("inputPAnalisisCreate", "inputDAnalisisCreate", 'inputNilaiAnalisisCreate', 'inputPrioritasRisikoCreate', "outputPrioritasRisikoCreate");

        // Selera Risiko
        setupSeleraRisiko("inputPAnalisisCreate", "inputDAnalisisCreate", "inputNilaiAnalisisCreate", "inputSeleraRisikoCreate");

        // Pilihan Penanganan
        setupPilihanPenanganan("inputPAnalisisCreate", "inputDAnalisisCreate", 'inputSeleraRisikoCreate', 'inputPilihanPenangananRisikoCreate');

        // Form Update
        // Auto Bobot
        setupAutoBobot("inputPAnalisisUpdate", "inputDAnalisisUpdate", "inputBobotAnalisisUpdate");
        setupAutoBobot("inputPTargetUpdate", "inputDTargetUpdate", "inputBobotTargetUpdate");

        // Nilai
        setupAutoNilai("inputPAnalisisUpdate", "inputDAnalisisUpdate", "inputBobotAnalisisUpdate", "inputNilaiAnalisisUpdate");
        setupAutoNilai("inputPTargetUpdate", "inputDTargetUpdate", "inputBobotTargetUpdate", "inputNilaiTargetUpdate");

        // Tingkat Risiko
        setupTingkatRisiko("inputPAnalisisUpdate", "inputDAnalisisUpdate", "inputNilaiAnalisisUpdate", "inputTingkatRisikoAnalisisUpdate", "outputTingkatRisikoAnalisisUpdate");
        setupTingkatRisiko("inputPTargetUpdate", "inputDTargetUpdate", "inputNilaiTargetUpdate", "inputTingkatRisikoTargetUpdate", "outputTingkatRisikoTargetUpdate");

        // Prioritas Risiko
        setupPrioritasRisiko("inputPAnalisisUpdate", "inputDAnalisisUpdate", 'inputNilaiAnalisisUpdate', 'inputPrioritasRisikoUpdate', "outputPrioritasRisikoUpdate");

        // Selera Risiko
        setupSeleraRisiko("inputPAnalisisUpdate", "inputDAnalisisUpdate", "inputNilaiAnalisisUpdate", "inputSeleraRisikoUpdate");

        // Pilihan Penanganan
        setupPilihanPenanganan("inputPAnalisisUpdate", "inputDAnalisisUpdate", 'inputSeleraRisikoUpdate', 'inputPilihanPenangananRisikoUpdate');
    });
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/footer.php'; ?>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/toast-message.php'; ?>