<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/PageNameEnum.php");
$pageName = PageNameEnum::DATA_UNIT;

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/sidebar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/smr/models/DirectorateModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/smr/models/UnitModel.php';

if ($_SESSION['role'] != RoleEnum::ADMIN) {
    echo '<script>window.location.href = "' .  URLEnum::getDashboardURL() . '";</script>';
}

$unitModel = new UnitModel();
$directorateModel = new DirectorateModel();

$units = $unitModel->getUnits();
$directorates = $directorateModel->getDirectorates();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $unit           = isset($_POST['unit'])          ? $_POST['unit'] : '';
    $direktorat_id  = isset($_POST['direktorat_id']) ? $_POST['direktorat_id'] : '';

    if ($unitModel->createUnit($unit, $direktorat_id)) {
        $toastMessage = array(
            "icon" => "success",
            "message" => "Tambah $pageName Behasil.!"
        );
        $_SESSION['toast_message'] = $toastMessage;
        echo '<script>window.location.href = "' .  URLEnum::getUnitURL() . '";</script>';
    } else {
        $toastMessage = array(
            "icon" => "error",
            "message" => "Kesalahan sistem! Silakan coba lagi.!"
        );
        $_SESSION['toast_message'] = $toastMessage;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id             = isset($_POST['id'])       ? $_POST['id'] : '';
    $unit           = isset($_POST['unit'])     ? $_POST['unit'] : '';
    $direktorat_id  = isset($_POST['direktorat_id']) ? $_POST['direktorat_id'] : '';

    if ($unitModel->updateUnit($id, $unit, $direktorat_id)) {
        $toastMessage = array(
            "icon" => "success",
            "message" => "Update $pageName Behasil.!"
        );
        $_SESSION['toast_message'] = $toastMessage;
        $units = $unitModel->getUnits();
    } else {
        $toastMessage = array(
            "icon" => "error",
            "message" => "Kesalahan sistem! Silakan coba lagi.!"
        );
        $_SESSION['toast_message'] = $toastMessage;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['act']) && isset($_GET['id'])) {
        $action = $_GET['act'];
        $id = $_GET['id'];

        if ($action == "delete") {
            if ($unitModel->deleteUnit($id)) {
                $toastMessage = array(
                    "icon" => "success",
                    "message" => "Tambah $pageName Behasil.!"
                );
                $_SESSION['toast_message'] = $toastMessage;
            } else {
                $toastMessage = array(
                    "icon" => "error",
                    "message" => "Kesalahan sistem! Silakan coba lagi.!"
                );
                $_SESSION['toast_message'] = $toastMessage;
            }
            echo '<script>window.location.href = "' .  URLEnum::getUnitURL() . '";</script>';
        }
    }
}

// Panggil fungsi showToastMessage di sini
if (isset($toastMessage)) {
    echo '
    <script>
        $(function() {
            showToastMessage("' . $toastMessage['icon'] . '", "' . $toastMessage['message'] . '");
        });
    </script>';
    // Hapus pesan toast dari session
    unset($_SESSION['toast_message']);
}
?>

<style>
    table.dataTable {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 40px !important;
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
                            <div class="col-md-9">
                                <h3 class="card-title">Daftar <?= $pageName ?></h3>
                            </div>
                            <div class="col-md-3">
                                <?php if ($_SESSION['role'] == RoleEnum::ADMIN || $_SESSION['role'] == RoleEnum::DIREKSI) { ?>
                                    <button type="button" class="btn btn-block btn-outline-primary" data-toggle="modal" data-target="#modal-add-user">Tambah <?= $pageName ?></button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class=" card-body">
                        <table id="example1" class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th width="5%" class="text-center">No</th>
                                    <th>Unit</th>
                                    <th>Direktorat</th>
                                    <?php if ($_SESSION['role'] == RoleEnum::ADMIN || $_SESSION['role'] == RoleEnum::DIREKSI) { ?>
                                        <th width="5%">Aksi</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($units) && count($units) > 0) {
                                    $i = 0;
                                    foreach ($units as $unit) {
                                        if ($unit['unit'] != RoleEnum::ADMIN):
                                            $i++;
                                            echo '
                                        <tr>
                                            <td class="text-center">' . $i . '</td>
                                            <td>' . $unit['unit'] . '</td>
                                            <td>' . $unit['direktorat'] . '</td>';

                                            if ($_SESSION['role'] == RoleEnum::ADMIN) {
                                                echo '<td class="text-center">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-update-user-' . $unit['id'] . '"><i class="fas fa-edit"></i></button>
                                                        <a href="' . URLEnum::getUnitURL() . '?act=delete&id=' . $unit['id'] . '" class="btn btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>';
                                            }; ?>
                                            </tr>

                                            <!-- /. modal-update-user -->
                                            <div class="modal fade" id="modal-update-user-<?= $unit['id'] ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="">
                                                            <input type="text" class="form-control" name="id" value="<?= $unit['id'] ?>" hidden>

                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Update <?= $pageName ?></h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="inputUnit">Unit</label>
                                                                    <input type="text" id="inputUnit" class="form-control" name="unit" value="<?= $unit['unit'] ?>">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="inputUnit">Direktorat</label>
                                                                    <select class="form-control select2" style="width: 100%;" name="direktorat_id">
                                                                        <?php foreach ($directorates as $directorate) {
                                                                            echo '<option value="' . $directorate['id'] . '"' . ($directorate['id'] == $unit['direktorat_id'] ? ' selected' : '') . '>' . htmlspecialchars($directorate['name']) . '</option>';
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="modal-footer justify-content-between">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                                                    <button type="submit" class="btn btn-primary" name="update">Update</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>
                                            <!-- /. modal-update-visitor -->

                                <?php
                                        endif;
                                    }
                                } else {
                                    echo '
                                        <tr>
                                            <td colspan="7" class="text-center">' . $pageName . ' Kosong</td>
                                        </tr>
                                    ';
                                }

                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th class="text-center">Unit</th>
                                    <?php if ($_SESSION['role'] == RoleEnum::ADMIN || $_SESSION['role'] == RoleEnum::DIREKSI) { ?>
                                        <th class="text-center">Aksi</th>
                                    <?php } ?>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /. modal-add-visitor -->
<div class="modal fade" id="modal-add-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah <?= $pageName ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="inputUnit">Unit</label>
                        <input type="text" id="inputUnit" class="form-control" name="unit" required>
                    </div>
                    <div class="form-group">
                        <label for="inputUnit">Direktorat</label>
                        <select class="form-control select2" style="width: 100%;" name="direktorat_id">
                            <?php foreach ($directorates as $directorate) {
                                echo '<option value="' . $directorate['id'] . '">' . htmlspecialchars($directorate['name']) . '</option>';
                            } ?>
                        </select>
                    </div>
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
<!-- /. modal-add-visitor -->

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/footer.php'; ?>
<script>
    $(document).ready(function() {
        // Initialize bootstrap switch
        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });

        $("input[data-bootstrap-switch]").bootstrapSwitch();

        // Log the value of status on switch change
        $("input[data-bootstrap-switch]").on('switchChange.bootstrapSwitch', function(event, state) {
            // Ensure the hidden input is properly selected
            var hiddenInput = $(this).closest('.form-group').find('input[name="is_active"]');
            hiddenInput.val(state ? 1 : 0);
        });

        // Log the initial value of status
        $("input[data-bootstrap-switch]").each(function() {
            // Ensure the hidden input is properly selected
            var hiddenInput = $(this).closest('.form-group').find('input[name="is_active"]');
            hiddenInput.val($(this).prop('checked') ? 1 : 0);
        });
    });
</script>