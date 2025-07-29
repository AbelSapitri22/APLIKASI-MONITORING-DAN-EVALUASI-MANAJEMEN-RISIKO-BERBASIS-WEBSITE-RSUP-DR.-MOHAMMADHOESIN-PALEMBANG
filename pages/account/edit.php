<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/PageNameEnum.php");
$pageName = PageNameEnum::AKUN;

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/sidebar.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/UsersModel.php');

$usersModel = new UsersModel();
$userData = $usersModel->getUserDetail($_SESSION['username']);

$editFailed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $id         = $userData['id'];
    $name       = $_POST['name'];
    $address    = isset($_POST['address']) ? $_POST['address'] : '';
    $is_active     = $userData['is_active'];

    if ($usersModel->updateUser($id, $name, $address, $is_active)) {
        echo '<script>window.location.href = "' . URLEnum::getAccountURL() . '";</script>';
        exit;
    } else {
        $editFailed = true;
    }
}
?>

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
                        <li class="breadcrumb-item"><a href="<?= URLEnum::getDashboardURL() ?>"><?= PageNameEnum::DASHBOARD ?></a></li>
                        <li class="breadcrumb-item"><a href="<?= URLEnum::getAccountURL() ?>"><?= PageNameEnum::AKUN ?></a></li>
                        <li class="breadcrumb-item active"><?= PageNameEnum::EDIT_AKUN ?></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid d-flex justify-content-center">
            <div class="card card-primary card-outline w-50">
                <div class="card-body box-profile">
                    <h3 class="text-center mb-3"><?= PageNameEnum::EDIT_AKUN ?></h3>

                    <?php if ($editFailed) {
                        echo '<div class="alert alert-danger" role="alert">
                            Maaf, Terjadi Kesalahan. Silahkan Coba Lagi.
                        </div>';
                    } ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="inputName">Nama Lengkap</label>
                            <input type="text" id="inputName" class="form-control" name="name" value="<?= $userData['name'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="inputAddress">Alamat</label>
                            <input type="text" id="inputAddress" class="form-control" name="address" value="<?= $userData['address'] ?>">
                        </div>
                        <div class="form-group">
                            <label for="inputUsername">Username</label>
                            <input type="text" id="inputUsername" class="form-control" name="username" value="<?= $userData['username'] ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="inputRole">Role</label>
                            <input type="text" id="inputRole" class="form-control" name="role" value="<?= $userData['role'] ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="inputStatus">Status</label>
                            <input type="text" id="inputStatus" class="form-control" name="status" value="<?= $userData['is_active'] ? 'Aktif' : 'Tidak Aktif' ?>" readonly>
                        </div>

                        <button type="submit" name="save" class="btn btn-primary btn-block"><b>Simpan</b></button>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/footer.php' ?>