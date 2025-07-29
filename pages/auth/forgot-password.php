<?php

session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/UsersModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/ForgotPasswordModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/URLEnum.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/PageNameEnum.php");

$usersModel             = new UsersModel();
$forgotPasswordModel    = new ForgotPasswordModel();

$pageName = PageNameEnum::FORGOT_PASSWORD;
$companyName = "Al-Quran Al-Akbar";
$base_url = URLEnum::BASE_URL;

if ($usersModel->isUserAlreadyLogin()) {
    header('Location: ' . $base_url);
    exit;
}

$status = false;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $username = isset($_POST['username']) ? $_POST['username'] : '';

    if ($username) {
        if ($usersModel->isUsernameAlready($username)) {
            if (!$forgotPasswordModel->isAlreadyResetPassword($username)) {
                if ($forgotPasswordModel->createResetPasswordData($username)) {
                    $status = true;
                    $message = "Request reset password berhasil, silahkan hubungi admin untuk melakukan validasi";
                } else {
                    $status = false;
                    $message = "Kesalahan sistem! Silakan coba lagi.";
                }
            } else {
                $status = false;
                $message = "Anda sudah request reset password.!";
            }
        } else {
            $status = false;
            $message = "Username anda tidak ditemukan.!";
        }
    } else {
        $status = false;
        $message = "Tolong, isi username anda.!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $companyName . " | " . $pageName ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $base_url ?>/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= $base_url ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= $base_url ?>/dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <b><?= $companyName ?></b>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg"><?= $pageName ?></p>

                <form method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="username" value="<?= $_POST['username'] ?? '' ?>" placeholder="Username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <p class="mb-3 text-center <?= $status ?? false ? "text-success" : "text-red" ?>" <?= $message == null || $message == "" ? "hidden" : "" ?>>
                        <?= $message ?>
                    </p>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block" name="reset">Request reset password</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <p class="mt-3 mb-1">
                    <a href="login.php">Saya sudah mempunyai akun</a>
                </p>
                <p class="mb-0">
                    <a href="register.php" class="text-center">Buat Akun Baru</a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="<?= $base_url ?>/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= $base_url ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= $base_url ?>/dist/js/adminlte.min.js"></script>

</body>

</html>