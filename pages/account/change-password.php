<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/PageNameEnum.php");
$pageName = PageNameEnum::AKUN;

include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/sidebar.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/UsersModel.php');

$usersModel = new UsersModel();
$userData = $usersModel->getUserDetail($_SESSION['username']);

$isRequestFaied = false;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $username    = $_SESSION['username'];
    $oldPassword    = md5(isset($_POST['oldPassword']) ? $_POST['oldPassword'] : '');
    $newPassword    = md5(isset($_POST['newPassword']) ? $_POST['newPassword'] : '');

    if ($usersModel->isValidPassword($username, $oldPassword)) {
        if ($usersModel->changePassword($username, $newPassword)) {
            echo '
            <script>window.location.href = "' . URLEnum::getAccountURL() . '"</script>
            ';
        } else {
            $message = "Kesalahan sistem! Silakan coba lagi.";
            $isRequestFaied = true;
        }
    } else {
        $message = "Password anda tidak valid! Silakan coba lagi.";
        $isRequestFaied = true;
    }

    $_SESSION['system_status'] = array(
        'isSuccess' => !$isRequestFaied,
        'message' => $message
    );
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
                        <li class="breadcrumb-item active"><?= PageNameEnum::CHANGE_PASSWORD ?></li>
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
                    <h3 class="text-center mb-3"><?= PageNameEnum::CHANGE_PASSWORD ?></h3>

                    <?php if ($isRequestFaied) {
                        echo '<div class="alert alert-danger" role="alert">
                            ' . $message . '
                        </div>';
                    } ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="inputOldPassword">Password Lama</label>
                            <input type="password" id="inputOldPassword" class="form-control" name="oldPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="inputNewPassword">Password Baru</label>
                            <input type="password" id="inputNewPassword" class="form-control" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="inputConfirmPassword">Konfirmasi Password</label>
                            <input type="password" id="inputConfirmPassword" class="form-control" name="confirmPassword" oninput="validateForm()" required>
                            <div id="passwordError" style="color: red;"></div>
                        </div>
                        <button type="submit" name="save" id="saveButton" class="btn btn-primary btn-block"><b>Simpan</b></button>
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

<script>
    function validateForm() {
        var newPassword = document.getElementById("inputNewPassword").value;
        var confirmPassword = document.getElementById("inputConfirmPassword").value;
        var passwordError = document.getElementById("passwordError");
        var saveButton = document.getElementById("saveButton");

        if (newPassword !== confirmPassword || newPassword === "" || confirmPassword === "") {
            passwordError.textContent = "Password baru dan konfirmasi password harus sama.";
            saveButton.disabled = true;
            event.preventDefault(); // Prevent form submission
        } else {
            passwordError.textContent = "";
            saveButton.disabled = false;
        }
    }
</script>