<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/PageNameEnum.php");
$pageName = PageNameEnum::DATA_PENGGUNA;

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/sidebar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/smr/models/UsersModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/smr/models/DirectorateModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/smr/models/UnitModel.php';

if ($_SESSION['role'] != RoleEnum::ADMIN) {
    echo '<script>window.location.href = "' .  URLEnum::getDashboardURL() . '";</script>';
}

$unitModel = new UnitModel();
$usersModel = new UsersModel();
$directorateModel = new DirectorateModel();

$users = $usersModel->getUsers();
$units = $unitModel->getUnits();
$directorates = $directorateModel->getDirectorates();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $username   = isset($_POST['username']) ? $_POST['username'] : '';
    $name       = isset($_POST['name'])     ? $_POST['name'] : '';
    $address    = isset($_POST['address'])  ? $_POST['address'] : '';
    $role       = isset($_POST['role'])  ? $_POST['role'] : '';
    $unit_id    = isset($_POST['unit_id'])  ? $_POST['unit_id'] : null;
    $direktorat_id    = isset($_POST['direktorat_id'])  ? $_POST['direktorat_id'] : null;

    if (!$usersModel->isUsernameAlready($username)) {
        if ($usersModel->createUser($username, $name, $address, $role, $unit_id, $direktorat_id)) {
            $toastMessage = array(
                "icon" => "success",
                "message" => "Tambah Data Pengunjung Behasil.!"
            );
            $_SESSION['toast_message'] = $toastMessage;
            echo '<script>window.location.href = "' .  URLEnum::getUsersURL() . '";</script>';
        } else {
            $toastMessage = array(
                "icon" => "error",
                "message" => "Kesalahan sistem! Silakan coba lagi.!"
            );
            $_SESSION['toast_message'] = $toastMessage;
        }
    } else {
        $toastMessage = array(
            "icon" => "error",
            "message" => "Tambah data pengunjung gagal, Username sudah dipakai.!"
        );
        $_SESSION['toast_message'] = $toastMessage;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id         = isset($_POST['id'])       ? $_POST['id'] : '';
    $name       = isset($_POST['name'])     ? $_POST['name'] : '';
    $address    = isset($_POST['address'])  ? $_POST['address'] : '';
    $role       = isset($_POST['role'])  ? $_POST['role'] : '';
    $unit_id    = isset($_POST['unit_id'])  ? $_POST['unit_id'] : '';
    $direktorat_id    = isset($_POST['direktorat_id'])  ? $_POST['direktorat_id'] : '';
    $isActive   = isset($_POST['is_active'])   ? $_POST['is_active'] : '';

    if ($role == RoleEnum::UNIT) {
        $direktorat_id = null;
    } else {
        $unit_id = null;
    }
    if ($usersModel->updateUser($id, $name, $address, $isActive, $unit_id, $direktorat_id)) {
        $toastMessage = array(
            "icon" => "success",
            "message" => "Update $pageName Behasil.!"
        );
        $_SESSION['toast_message'] = $toastMessage;
        $users = $usersModel->getUsers();
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
            if ($usersModel->deleteUser($id)) {
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
            echo '<script>window.location.href = "' .  URLEnum::getUsersURL() . '";</script>';
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
                                <h3 class="card-title">Daftar <?= PageNameEnum::DATA_PENGGUNA ?></h3>
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
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Direktorat/Unit</th>
                                    <th>Status</th>
                                    <?php if ($_SESSION['role'] == RoleEnum::ADMIN || $_SESSION['role'] == RoleEnum::DIREKSI) { ?>
                                        <th width="5%">Aksi</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($users) && count($users) > 0) {
                                    $i = 0;
                                    foreach ($users as $user) {
                                        $i++;
                                        echo '
                                        <tr>
                                            <td class="text-center">' . $i . '</td>
                                            <td>' . $user['username'] . '</td>
                                            <td class="text-capitalize">' . $user['name'] . '</td>
                                            <td class="text-capitalize">' . $user['address'] . '</td>
                                            <td class="text-capitalize">' . ($user['role'] == RoleEnum::DIREKSI ? $user['direktorat_name'] : $user['unit_name']) . '</td>
                                            <td>' . ($user['is_active'] ? "Aktif" : "Tidak Aktif") . '</td>';
                                        if ($_SESSION['role'] == RoleEnum::ADMIN) {
                                            echo '<td class="text-center">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-update-user-' . $user['id'] . '"><i class="fas fa-edit"></i></button>
                                                        <a href="' . URLEnum::getUsersURL() . '?act=delete&id=' . $user['id'] . '" class="btn btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>';
                                        } else if ($_SESSION['role'] == RoleEnum::ADMIN) {
                                            echo '<td class="text-center">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-update-user-' . $user['id'] . '"><i class="fas fa-edit"></i></button>
                                                    </div>
                                                </td>';
                                        }; ?>
                                        </tr>

                                        <!-- /. modal-update-user -->
                                        <div class="modal fade" id="modal-update-user-<?= $user['id'] ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="">
                                                        <input type="text" class="form-control" name="id" value="<?= $user['id'] ?>" hidden>

                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Update <?= $pageName ?></h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="inputUsername">Username</label>
                                                                <input type="text" id="inputUsername" class="form-control" name="username" value="<?= $user['username'] ?>" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="inputName">Nama</label>
                                                                <input type="text" id="inputName" class="form-control" name="name" value="<?= $user['name'] ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="inputAddress">Alamat</label>
                                                                <input type="text" id="inputAddress" class="form-control" name="address" value="<?= $user['address'] ?>" required>
                                                            </div>
                                                            <input type="text" id="inputRoleUpdate" class="form-control" name="role" value="<?= $user['role'] ?>" hidden>
                                                            <?php if ($user['role'] == RoleEnum::UNIT): ?>
                                                                <div class="form-group">
                                                                    <label for="inputUnit">Unit</label>
                                                                    <select class="form-control select2" style="width: 100%;" name="unit_id">
                                                                        <?php foreach ($units as $unit) {
                                                                            echo '<option value="' . $unit['id'] . '"' . ($unit['id'] == $user['unit_id'] ? ' selected' : '') . '>' . htmlspecialchars($unit['unit']) . '</option>';
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="form-group">
                                                                    <label for="inputUnit">Direktorat</label>
                                                                    <select class="form-control select2" style="width: 100%;" name="direktorat_id">
                                                                        <?php foreach ($directorates as $directorate) {
                                                                            echo '<option value="' . $directorate['id'] . '"' . ($directorate['id'] == $user['direktorat_id'] ? ' selected' : '') . '>' . htmlspecialchars($directorate['name']) . '</option>';
                                                                        } ?>
                                                                    </select>
                                                                </div>
                                                            <?php endif ?>
                                                            <div class="form-group">
                                                                <label for="inputStatus">Status</label>
                                                                <input type="hidden" name="is_active" value="<?= (isset($user['is_active']) ? $user['is_active'] : 0) ?>">
                                                                <input type="checkbox" value="1" <?= ($user['is_active'] ? "checked" : "") ?> data-bootstrap-switch>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer justify-content-between">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                                                            <button type="submit" class="btn btn-primary" name="update">Update</button>
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
                                            <td colspan="7" class="text-center">Data Pengunjung Kosong</td>
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
                        <label for="inputUsername">Username</label>
                        <input type="text" id="inputUsername" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="inputName">Nama</label>
                        <input type="text" id="inputName" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="inputAddress">Alamat</label>
                        <input type="text" id="inputAddress" class="form-control" name="address" required>
                    </div>
                    <!-- select -->
                    <div class="form-group">
                        <label for="inputRole">Direktorat/Unit</label>
                        <select class="form-control" id="inputRole" name="role">
                            <option disabled>Pilih Unit</option>
                            <option value="direksi">Direktorat</option>
                            <option value="unit">Unit</option>
                        </select>
                    </div>
                    <!-- select -->
                    <div class="form-group">
                        <label for="inputDirectorate">Direktorat</label>
                        <select class="form-control" id="inputDirectorate" name="direktorat_id">
                            <option disabled>Pilih Direktorat</option>
                            <?php foreach ($directorates as $directorate): ?>
                                <option value="<?= $directorate['id'] ?>"><?= $directorate['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- select -->
                    <div class="form-group">
                        <label for="inputUnit">Unit</label>
                        <select class="form-control" id="inputUnit" name="unit_id">
                            <option disabled>Pilih Unit</option>
                            <?php foreach ($units as $unit): ?>
                                <option value="<?= $unit['id'] ?>"><?= $unit['unit'] ?> </option>
                            <?php endforeach; ?>
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

    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('inputRole');
        const directorateGroup = document.getElementById('inputDirectorate').closest('.form-group');
        const unitGroup = document.getElementById('inputUnit').closest('.form-group');

        // Initially hide both (or you can set one as default)
        directorateGroup.style.display = 'none';
        unitGroup.style.display = 'none';

        // Handle role selection change
        function handleRoleChange() {
            const selectedValue = roleSelect.value;

            // Hide both first
            directorateGroup.style.display = 'none';
            unitGroup.style.display = 'none';

            // Show the appropriate one
            if (selectedValue === 'direksi') {
                directorateGroup.style.display = 'block';
                unitGroup.querySelector('select').selectedIndex = 0;
            } else if (selectedValue === 'unit') {
                unitGroup.style.display = 'block';
                directorateGroup.querySelector('select').selectedIndex = 0;
            }
        }

        // Add event listener
        roleSelect.addEventListener('change', handleRoleChange);

        // Initialize on page load
        handleRoleChange();
    });
</script>