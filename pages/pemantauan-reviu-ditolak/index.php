<?php

use function PHPSTORM_META\map;

require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/PageNameEnum.php");
$pageName = PageNameEnum::DATA_PEMANTAUAN_REVIU_DITOLAK;

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/sidebar.php';

require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/MonitoringReviewModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskAssessmentModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskCategoryModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskLevelModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskPriorityModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskImpactModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskProbabilityModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskConclusionLevelModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/UsersModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/UnitModel.php');

$userModel              = new UsersModel();
$unitModel              = new UnitModel();
$monitoringReviewModel  = new MonitoringReviewModel();
$riskAssessmentModel    = new RiskAssessmentModel();
$riskCategoryModel      = new RiskCategoryModel();
$riskLevelModel         = new RiskLevelModel();
$riskPriorityModel      = new RiskPriorityModel();
$riskProbabilityModel   = new RiskProbabilityModel();
$riskConclusionLevelModel   = new RiskConclusionLevelModel();
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
$availableYears = $monitoringReviewModel->getAvailableYears();
$years = ['Semua' => 'Semua Tahun'] + array_combine($availableYears, $availableYears);

$allReviewRejected = $monitoringReviewModel->getMonitoringReviewByVerifiedAndUnitAndCategoryMonthAndYear(
    false,
    $unit_id,
    $riskCategorySelected,
    $monthSelected,
    $yearSelected
);

$riskProbabilities  = $riskProbabilityModel->getRiskProbabilities();
$riskCategories     = $riskCategoryModel->getRiskCategories();
$riskPriorities     = $riskPriorityModel->getRiskPriorities();
$riskConclusions    = $riskConclusionLevelModel->getAll();
$riskLevelConclusions = $monitoringReviewModel->getRiskLevelConclusion();
$riskImpacts        = $riskImpactModel->getRiskImpacts();
$riskLevels         = $riskLevelModel->getRiskLevels();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Data dari form
    $riskId = $_POST['monitoring_id'] ?? '';
    $p = $_POST['p'] ?? 0;
    $d = $_POST['d'] ?? 0;
    $bobot = $_POST['bobot'] ?? 0.0;
    $nilai = $_POST['nilai'] ?? 0;
    $tingkat_risiko_id = $_POST['tingkat_risiko_id'] ?? '';
    $simpulan_tingkat_risiko_id = $_POST['simpulan_tingkat_risiko_id'] ?? '';
    $efektif_pengendalian = $_POST['efektif_pengendalian'] ?? '';

    // Menyiapkan array data untuk update
    $data = [
        'p' => $p,
        'd' => $d,
        'bobot' => $bobot,
        'nilai' => $nilai,
        'tingkat_risiko_id' => $tingkat_risiko_id,
        'simpulan_tingkat_risiko_id' => $simpulan_tingkat_risiko_id,
        'efektif_pengendalian' => $efektif_pengendalian
    ];

    // Proses upload dokumen jika ada
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/smr/uploads/documents/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

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

        if (array_key_exists($mimeType, $allowedTypes) && $_FILES['document']['size'] <= $maxSize) {
            $extension = $allowedTypes[$mimeType];
            $filename = 'doc_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['document']['tmp_name'], $destination)) {
                $documentPath = '/smr/uploads/documents/' . $filename;
                $data['document_path'] = $documentPath;
            } else {
                $_SESSION['toast_message'] = [
                    "icon" => "error",
                    "message" => "Gagal mengunggah dokumen!"
                ];
                echo '<script>window.history.back();</script>';
                exit;
            }
        } else {
            $_SESSION['toast_message'] = [
                "icon" => "error",
                "message" => "Format file tidak didukung atau ukuran melebihi batas!"
            ];
            echo '<script>window.history.back();</script>';
            exit;
        }
    } elseif (isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
        $_SESSION['toast_message'] = [
            "icon" => "error",
            "message" => "Terjadi kesalahan saat mengunggah dokumen!"
        ];
        echo '<script>window.history.back();</script>';
        exit;
    }

    // Update ke database
    if ($monitoringReviewModel->updateMonitoringReview($riskId, $data)) {
        $_SESSION['toast_message'] = [
            "icon" => "success",
            "message" => "Update $pageName berhasil!"
        ];
    } else {
        $_SESSION['toast_message'] = [
            "icon" => "error",
            "message" => "Kesalahan sistem! Silakan coba lagi!"
        ];
    }

    echo '<script>window.location.href = "' . URLEnum::getMonitoringReviewRejectedURL() . '";</script>';
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['act'])) {
        $action = $_GET['act'];

        if ($action == "delete") {
            $id     = $_GET['id'];
            if ($monitoringReviewModel->deleteMonitoringReview($id)) {
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
            echo '<script>window.location.href = "' .  URLEnum::getMonitoringReviewRejectedURL() . '";</script>';
        }

        if ($action == "show") {
            $unit_id = $_GET['unit_id'] ?? $unit_id;
            $riskCategorySelected = $_GET['category'] ?? $riskCategorySelected;
            $monthSelected = $_GET['month'] ?? "Semua";
            $yearSelected = $_GET['year'] ?? "Semua";

            $allMonitoringReviews = $monitoringReviewModel->getMonitoringReviewsByUnitCategoryMonthAndYear(
                false,
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
                        <div class="row">
                            <?php if ($user['role'] !== RoleEnum::UNIT): ?>
                                <div class="col-md-2">
                                    <span class="text-muted">Pilih Unit</span>
                                    <select class="form-control select2" style="width: 100%;" onchange="onChangeFilter(this.value, '<?= $riskCategorySelected ?>', '<?= $monthSelected ?>', '<?= $yearSelected ?>')">
                                        <?php
                                        echo '<option value="0"' . ($unit_id == ' 0' ? ' selected' : '') . '>Semua</option>';

                                        foreach ($units as $unit) {
                                            if ($unit['unit'] != RoleEnum::ADMIN) {
                                                echo '<option value="' . $unit['id'] . '"' .
                                                    ($unit['id'] == $unit_id ? ' selected' : '') .
                                                    '>' . htmlspecialchars($unit['unit']) . '</option>';
                                            }
                                        } ?>
                                    </select>
                                </div>
                            <?php endif ?>
                            <div class="col-md-2">
                                <span class="text-muted">Pilih Kategori</span>
                                <select class="form-control select2" style="width: 100%;" onchange="onChangeFilter(<?= $unit_id ?>, this.value, '<?= $monthSelected ?>', '<?= $yearSelected ?>')">
                                    <?php
                                    $riskCategoriesWithAll = ['Semua'] + array_column($riskCategories, 'name');
                                    foreach ($riskCategoriesWithAll as $name) {
                                        echo '<option value="' . htmlspecialchars($name) . '"' .
                                            ($riskCategorySelected === $name ? ' selected' : '') .
                                            '>' . htmlspecialchars($name) . '</option>';
                                    } ?>
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
                            <table id="monitoring_reviu-table" class="table table-bordered table-striped risk-assessment-table">
                                <thead>
                                    <tr>
                                    </tr>
                                    <tr>
                                        <!-- Identifikasi Risiko (Kolom 1-7) -->
                                        <th rowspan="2" class="text-center" style="width: 3%; vertical-align: middle;">No</th>
                                        <th rowspan="2" class="text-center" style="min-width: 150px; vertical-align: middle;">RISIKO</th>
                                        <th rowspan="2" class="text-center" style="min-width: 30px; vertical-align: middle;">KATEGORI RISIKO</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">P</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">D</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">BOBOT</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">NILAI</th>
                                        <th rowspan="2" class="text-center" style="min-width: 20px; vertical-align: middle;">TINGKAT RISIKO</th>
                                        <th rowspan="2" class="text-center" style="min-width: 100px; vertical-align: middle;">PRIORITAS RISIKO</th>
                                        <th rowspan="2" class="text-center" style="min-width: 200px; vertical-align: middle;">URAIAN PENGENDALIAN</th>
                                        <th rowspan="2" class="text-center" style="min-width: 50px; vertical-align: middle;">JADWAL PELAKSANAAN</th>

                                        <th colspan="5" class="text-center" style="vertical-align: middle;">HASIL PEMANTAUAN</th>
                                        <th colspan="2" class="text-center" style="vertical-align: middle;">SIMPULAN</th>


                                        <th rowspan="2" class="text-center" style="vertical-align: middle;">Tanggal Diperbaharui</th>
                                        <th rowspan="2" class="text-center" style="vertical-align: middle;">Status</th>
                                        <th rowspan="2" class="text-center" style="vertical-align: middle;">Dokumen</th>
                                        <th rowspan="2" class="text-center" style="vertical-align: middle;">Catatan</th>
                                        <?php if ($_SESSION['role'] == RoleEnum::UNIT) { ?>
                                            <th rowspan="2" class="text-center" style="vertical-align: middle;">Aksi</th>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="min-width: 20px; vertical-align: middle;">P</th>
                                        <th class="text-center" style="min-width: 20px; vertical-align: middle;">D</th>
                                        <th class="text-center" style="min-width: 20px; vertical-align: middle;">BOBOT</th>
                                        <th class="text-center" style="min-width: 20px; vertical-align: middle;">NILAI</th>
                                        <th class="text-center" style="min-width: 20px; vertical-align: middle;">TINGKAT RISIKO</th>

                                        <th class="text-center" style="min-width: 20px; vertical-align: middle;">TINGKAT RISIKO</th>
                                        <th class="text-center" style="min-width: 20px; vertical-align: middle;">EFEKTIFITAS </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    if (isset($allReviewRejected) && count($allReviewRejected) > 0) {
                                        $i = 0;
                                        foreach ($allReviewRejected as $rejected) {
                                            $i++;
                                            $date = new DateTime($rejected['updated_at']);
                                            $updated_at = $date->format('d M Y H:i'); ?>

                                            <tr>
                                                <td class="text-center"><?= $i ?> <?= $rejected['id'] ?></td>
                                                <td><?= $rejected['risiko'] ?></td>
                                                <td><?= $rejected['kategori_risiko'] ?></td>
                                                <td><?= $rejected['p_analisis'] ?></td>
                                                <td><?= $rejected['d_analisis'] ?></td>
                                                <td><?= $rejected['bobot_analisis'] ?></td>
                                                <td><?= $rejected['nilai_analisis'] ?></td>
                                                <td><?= $rejected['tingkat_risiko_analisis'] ?></td>
                                                <td><?= $rejected['prioritas_risiko'] ?></td>
                                                <td><?= $rejected['uraian_pengendalian'] ?></td>
                                                <td><?= $rejected['jadwal_pelaksanaan'] ?></td>
                                                <td><?= $rejected['p'] ?></td>
                                                <td><?= $rejected['d'] ?></td>
                                                <td><?= $rejected['bobot'] ?></td>
                                                <td><?= $rejected['nilai'] ?></td>
                                                <td><?= $rejected['tingkat_risiko_pemantauan'] ?></td>
                                                <td><?= $rejected['tingkat_risiko_simpulan'] ?></td>
                                                <td><?= $rejected['efektif'] ?></td>
                                                <td class="text-center"><?= $updated_at ?></td>
                                                <td class="text-center">
                                                    <?php
                                                    if ($rejected['is_verified'] === 1 && $rejected['is_verified'] !== null) {
                                                        $date = new DateTime($rejected['verified_at']);
                                                        $verified_at = $date->format('d/m/Y H:i');
                                                        echo '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Terverifikasi</span>';
                                                        echo '<span class="d-block mt-2 text-muted">' . $verified_at . '</span>';
                                                    } elseif ($rejected['is_verified'] === 0 && $rejected['is_verified'] !== null) {
                                                        $date = new DateTime($rejected['verified_at']);
                                                        $verified_at = $date->format('d/m/Y H:i');
                                                        echo '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Ditolak</span>';
                                                        echo '<span class="d-block mt-2 text-muted">' . $verified_at . '</span>';
                                                    } else {
                                                        echo '<span class="badge badge-secondary"><i class="fas fa-clock"></i> Belum Diverifikasi</span>';
                                                    } ?>
                                                </td>
                                                <!-- Dokumen Row -->
                                                <td class="text-center">
                                                    <!-- // Ambil nama file dokumen -->
                                                    <?php
                                                    if ($rejected['document'] != null):
                                                        $document = $rejected['document'];
                                                        $ext = pathinfo($document, PATHINFO_EXTENSION);

                                                        // Tentukan ikon berdasarkan ekstensi file menggunakan Font Awesome
                                                        $icon = '';

                                                        // Cek jenis file dan pilih ikon yang sesuai
                                                        if (strpos($ext, 'pdf') !== false) {
                                                            $icon = 'fas fa-file-pdf'; // Ikon PDF
                                                        } elseif (strpos($ext, 'jpg') !== false || strpos($ext, 'jpeg') !== false || strpos($ext, 'png') !== false) {
                                                            $icon = 'fas fa-image'; // Ikon Gambar
                                                        } elseif (strpos($ext, 'doc') !== false || strpos($ext, 'docx') !== false) {
                                                            $icon = 'fas fa-file-word'; // Ikon Word
                                                        } elseif (strpos($ext, 'xls') !== false || strpos($ext, 'xlsx') !== false) {
                                                            $icon = 'fas fa-file-excel'; // Ikon Excel
                                                        } else {
                                                            $icon = 'fas fa-file'; // Ikon default untuk file yang tidak dikenali
                                                        }
                                                    ?>
                                                        <!-- Tampilkan ikon yang sesuai dengan jenis file -->
                                                        <a href="<?= $document; ?>" target="_blank">
                                                            <i class="<?php echo $icon; ?>" style="font-size: 24px;"></i>
                                                        </a>
                                                    <?php
                                                    endif
                                                    ?>
                                                </td>
                                                <td class="text-center"><?= $rejected['notes'] ?></td>
                                                <?php
                                                if ($_SESSION['role'] == RoleEnum::UNIT): ?>
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-warning btn-edit-pemantauan"
                                                                data-toggle="modal"
                                                                data-target="#modalUpdateMonitoring"
                                                                data-id="<?= $rejected['id'] ?>"
                                                                data-nilai-analisis="<?= $rejected['nilai'] ?>"
                                                                data-p="<?= $rejected['p'] ?>"
                                                                data-d="<?= $rejected['d'] ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                <?php endif ?>
                                            </tr>
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
<div class="modal fade" id="modalUpdateMonitoring" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
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
                    <input type="hidden" id="inputMonitoringId" class="form-control" name="monitoring_id" required>
                    <input type="text" id="inputNilaiAnalisis" class="form-control" hidden>
                    <label class="modal-title">Hasil Pemantauan</label>
                    <hr>
                    <div class="form-group">
                        <h6 for="inputP">P<span class="text-danger">*</span></h6>
                        <select class="form-control" name="p" id="inputP" required>
                            <option disabled selected>Pilih Nilai P Pemantauan</option>
                            <?php foreach ($riskProbabilities as $riskProbability): ?>
                                <option value="<?= $riskProbability['id'] ?>" data-value="<?= $riskProbability['value'] ?>"><?= $riskProbability['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputD">D<span class="text-danger">*</span></h6>
                        <select class="form-control" name="d" id="inputD" required>
                            <option disabled selected>Pilih Nilai D Pemantauan</option>
                            <?php foreach ($riskImpacts as $riskImpact): ?>
                                <option value="<?= $riskImpact['id'] ?>" data-value="<?= $riskImpact['value'] ?>"><?= $riskImpact['value'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputBobot">Bobot<span class="text-danger">*</span></h6>
                        <input type="number" id="inputBobot" class="form-control" name="bobot" step="0.01" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputNilaiPemantauan">Nilai<span class="text-danger">*</span></h6>
                        <input type="number" id="inputNilaiPemantauan" class="form-control" name="nilai" placeholder="0" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="inputTingkatRisiko">Tingkat Risiko<span class="text-danger">*</span></h6>
                        <input type="text" id="outputTingkatRisiko" class="form-control" readonly required>
                        <select class="form-control" id="inputTingkatRisiko" name="tingkat_risiko_id" hidden>
                            <option disabled selected>Pilih Tingkat Risiko</option>
                            <?php foreach ($riskLevels as $riskLevel): ?>
                                <option value="<?= $riskLevel['id'] ?>"><?= $riskLevel['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <br />
                    <hr>
                    <label class="modal-title">Simpulan Pemantauan</label>
                    <hr>
                    <div class="form-group">
                        <h6 for="inputTingkatRisikoSimpulan">Tingkat Risiko<span class="text-danger">*</span></h6>
                        <input type="text" id="outputTingkatRisikoSimpulan" class="form-control" readonly required>
                        <select class="form-control" id="inputTingkatRisikoSimpulan" name="simpulan_tingkat_risiko_id" hidden>
                            <?php foreach ($riskConclusions as $riskConclusion): ?>
                                <option value="<?= $riskConclusion['id'] ?>"><?= $riskConclusion['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <h6 for="inputEfektifitas">Efektifitas<span class="text-danger">*</span></h6>
                        <input type="text" id="inputEfektifitas" class="form-control" name="efektif_pengendalian" readonly required>
                    </div>
                    <div class="form-group">
                        <h6 for="exampleInputFile">Upload Dokumen Bukti Pemantauan</h6>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="exampleInputFile" name="document">
                                <label class="custom-file-label" for="exampleInputFile">Pilih file</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">Format: PDF, DOC, DOCX, JPG, PNG</small>
                    </div>
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
<!-- /.modal-update-pemantauan -->

<script>
    function validateInput(input) {
        input.value = input.value.replace(/^0+/, '');
    }

    $(document).ready(function() {
        $('#monitoring_reviu-table').DataTable({
            scrollX: true, // Aktifkan horizontal scroll
            responsive: false, // Nonaktifkan mode responsive
            fixedColumns: false, // Pastikan tidak ada kolom fixed,
            autoWidth: false, // Ini akan membuat kolom menyesuaikan isi
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Handle edit button clicks
        document.querySelectorAll('.btn-edit-pemantauan').forEach(button => {
            button.addEventListener('click', function() {
                const data = this.dataset;

                document.getElementById('inputMonitoringId').value = data.id;
                document.getElementById('inputNilaiAnalisis').value = data.nilaiAnalisis;

                if (data.p != "" && data.d != "") {
                    setSelectValueByOptionValue('inputP', data.p);
                    setSelectValueByOptionValue('inputD', data.d);
                }

            });
        });

        document.querySelectorAll('.modal-verif-pemantauan').forEach(button => {
            button.addEventListener('click', function() {
                const data = this.dataset;
                const riskId = data.id;
                const notes = data.notes || '';
                const verified = data.verified;
                const verifiedAt = data.verifiedAt;

                // Set nilai hidden input
                document.getElementById('modal-pemantauan-id').value = riskId;

                // Set catatan
                document.getElementById('modal-notes').value = notes;

                // Tampilkan atau sembunyikan notes berdasarkan status
                const notesWrapper = document.getElementById('modal-notes-wrapper');
                const radioYes = document.getElementById('modal-verified-yes');
                const radioNo = document.getElementById('modal-verified-no');

                // Reset radio
                radioYes.checked = false;
                radioNo.checked = false;

                if (verified === "1") {
                    radioYes.checked = true;
                    notesWrapper.style.display = 'none';
                } else if (verified === "0") {
                    radioNo.checked = true;
                    notesWrapper.style.display = 'block';
                } else {
                    // Belum diverifikasi
                    radioYes.checked = true;
                    notesWrapper.style.display = 'none';
                }

                // Tampilkan status verifikasi di header modal
                const statusHtmlContainer = document.getElementById('modal-verif-status');
                let statusHtml = '';
                if (verified === "1" && verifiedAt && verifiedAt !== "0000-00-00 00:00:00") {
                    statusHtml = `
                <i class="fas fa-check-circle fa-4x text-success"></i><br/>
                <span class="badge badge-success">Terverifikasi</span>
                <p class="text-muted mt-2">Tanggal: ${formatTanggal(verifiedAt)}</p>
            `;
                } else if (verified === "0" && verifiedAt && verifiedAt !== "0000-00-00 00:00:00") {
                    statusHtml = `
                <i class="fas fa-times-circle fa-4x text-danger"></i><br/>
                <span class="badge badge-danger">Ditolak</span>
                <p class="text-muted mt-2">Tanggal: ${formatTanggal(verifiedAt)}</p>
            `;
                } else {
                    statusHtml = `
                <i class="fas fa-clock fa-4x text-secondary"></i><br/>
                <span class="badge badge-secondary">Belum Diverifikasi</span>
            `;
                }
                statusHtmlContainer.innerHTML = statusHtml;
            });
        });
    });

    function onShowNotes(show) {
        const notesWrapper = document.getElementById('modal-notes-wrapper');
        if (notesWrapper) {
            notesWrapper.style.display = show ? 'block' : 'none';
        }
    }

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

        window.location.href = "<?= URLEnum::getMonitoringReviewRejectedURL() ?>?" + queryString;
    }

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

        function setupTingkatRisikoSimpulan(pId, dId, nilaiAnalisisId, nilaiPemantauanId, inputTingkatRisikoId, outputTingkatRisikoId) {
            const inputP = document.getElementById(pId);
            const inputD = document.getElementById(dId);
            const inputNilaiAnalisis = document.getElementById(nilaiAnalisisId);
            const inputNilaiPemantauan = document.getElementById(nilaiPemantauanId);
            const inputTingkatRisiko = document.getElementById(inputTingkatRisikoId); // <select>
            const outputTingkatRisiko = document.getElementById(outputTingkatRisikoId); // <input>

            function hitungTingkatRisikoSimpulan(nilaiAnalaisis, nilaiPemantauan) {
                if (nilaiAnalaisis === nilaiPemantauan) return "Tidak ada penurunan tingkat risiko";
                if (nilaiAnalaisis < nilaiPemantauan) return "Tingkat risiko mengalami peningkatan";
                return "Tingkat risiko mengalami penurunan";
            }

            function updateTingkatRisikoSimpulan() {
                const nilaiAnalaisis = parseFloat(inputNilaiAnalisis.value) || 0;
                const nilaiPemantauan = parseFloat(inputNilaiPemantauan.value) || 0;

                if (!isNaN(nilaiAnalaisis) && !isNaN(nilaiPemantauan) && nilaiPemantauan !== 0) {
                    const simpulan = hitungTingkatRisikoSimpulan(nilaiAnalaisis, nilaiPemantauan);

                    outputTingkatRisiko.value = simpulan;

                    // Otomatis pilih <option> berdasarkan teks
                    for (let option of inputTingkatRisiko.options) {
                        console.log(simpulan);
                        if (option.text.trim().toLowerCase() === simpulan.toLowerCase()) {
                            option.selected = true;
                            break;
                        }
                    }
                } else {
                    outputTingkatRisiko.value = "";
                    inputTingkatRisiko.selectedIndex = 0; // reset ke "Pilih Tingkat Risiko"
                }
            }

            inputP.addEventListener('change', updateTingkatRisikoSimpulan);
            inputD.addEventListener('change', updateTingkatRisikoSimpulan);
            inputNilaiAnalisis.addEventListener('input', updateTingkatRisikoSimpulan);
            inputNilaiPemantauan.addEventListener('input', updateTingkatRisikoSimpulan);

            updateTingkatRisikoSimpulan();
        }

        function setupEfektifitas(pId, dId, nilaiAnalisisId, nilaiPemantauanId, efektifitasId) {
            const inputP = document.getElementById(pId);
            const inputD = document.getElementById(dId);
            const inputNilaiAnalisis = document.getElementById(nilaiAnalisisId);
            const inputNilaiPemantauan = document.getElementById(nilaiPemantauanId);
            const inputEfektifitas = document.getElementById(efektifitasId);

            function hitungEfektifitas(nilaiAnalaisis, nilaiPemantauan) {
                if (nilaiAnalaisis <= nilaiPemantauan) return "tidak efektif";
                return "efektif";
            }

            function updateEfektifitas() {
                const nilaiAnalaisis = parseFloat(inputNilaiAnalisis.value);
                const nilaiPemantauan = parseFloat(inputNilaiPemantauan.value);

                if (!isNaN(nilaiAnalaisis) && nilaiAnalaisis != 0, !isNaN(nilaiPemantauan) && nilaiPemantauan != 0) {
                    const tingkatRisiko = hitungEfektifitas(nilaiAnalaisis, nilaiPemantauan);
                    inputEfektifitas.value = tingkatRisiko;
                } else {
                    inputEfektifitas.value = "";
                }
            }

            inputP.addEventListener('change', updateEfektifitas);
            inputD.addEventListener('change', updateEfektifitas);
            updateEfektifitas(); // langsung panggil sekali
        }

        // Form
        // Bobbot
        setupAutoBobot("inputP", "inputD", "inputBobot");

        // Nilai
        setupAutoNilai("inputP", "inputD", "inputBobot", "inputNilaiPemantauan");

        // Tingkat Risiko 
        setupTingkatRisiko("inputP", "inputD", "inputNilaiPemantauan", "inputTingkatRisiko", "outputTingkatRisiko");

        // Tingkat Risiko Simpulan
        setupTingkatRisikoSimpulan("inputP", "inputD", "inputNilaiAnalisis", "inputNilaiPemantauan", "inputTingkatRisikoSimpulan", "outputTingkatRisikoSimpulan");

        // Efektifitas simpulan
        setupEfektifitas("inputP", "inputD", "inputNilaiAnalisis", "inputNilaiPemantauan", "inputEfektifitas");
    });
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/footer.php'; ?>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/toast-message.php'; ?>