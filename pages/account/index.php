<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/PageNameEnum.php");
$pageName = PageNameEnum::AKUN;

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/sidebar.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/UsersModel.php');

$usersModel = new UsersModel();
$userData = $usersModel->getUserDetail($_SESSION['username']);
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
                        <li class="breadcrumb-item active"><?= $pageName ?></li>
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
                    <h3 class="profile-username text-center "><?= $userData['name'] ?></h3>

                    <p class="text-muted text-center text-capitalize"><?= $userData['role'] ?></p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Username</b> <a class="float-right"><?= $userData['username'] ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Alamat</b> <a class="float-right"><?= $userData['address'] ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b> <a class="float-right"><?= $userData['is_active'] ? "Aktif" : "Tidak Aktif" ?></a>
                        </li>
                    </ul>

                    <a href="<?= URLEnum::getEditAccountURL() ?>" class="btn btn-primary btn-block"><b>Edit Akun</b></a>
                    <a href="<?= URLEnum::getChangePasswordURL() ?>" class="btn btn-light btn-block"><b>Ganti Password</b></a>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/footer.php' ?>