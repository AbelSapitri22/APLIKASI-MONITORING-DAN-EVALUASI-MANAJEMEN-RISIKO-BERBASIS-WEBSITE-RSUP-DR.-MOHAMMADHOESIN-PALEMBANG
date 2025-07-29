<?php

session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/UsersModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/URLEnum.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/PageNameEnum.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pageName = PageNameEnum::LOGIN;
$companyName = "RSUP Dr. Mohammad Hoesin Palembang";
$base_url = URLEnum::BASE_URL;


$usersModel = new UsersModel();

if ($usersModel->isUserAlreadyLogin()) {
    header('Location: ' . URLEnum::getDashboardURL());
    exit;
}

$status = true;
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username && $password) {
        if ($usersModel->isUserActive($username)) {
            $user = $usersModel->login($username, md5($password)); // Assuming you use MD5 for password hashing
            if ($user) {
                $_POST['status'] = true;
                echo '<div class="alert alert-success" role="alert">Login berhasil...</div>';
                header('Location: ' . URLEnum::getDashboardURL());
                exit;
            } else {
                $status = false;
                $message = "Maaf, kata sandi Anda salah. Harap periksa kembali kata sandi Anda.";
            }
        } else {
            $status = false;
            $message = "Maaf, akun anda tidak aktif. Mohon hubungi admin.";
        }
    } else {
        $status = false;
        $message = "Maaf, kata sandi Anda salah. Harap periksa kembali kata sandi Anda.";
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

    <style>
        .background-image {
            position: relative;
            background-image: url('../../uploads/images/rsmh.webp');
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: cover;
            height: 100vh;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Tambahkan overlay hitam semi-transparan */
        .background-image::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Atur tingkat kegelapan di sini */
            z-index: 1;
        }

        /* Supaya isi di atas overlay tetap terlihat */
        .login-box {
            position: relative;
            z-index: 2;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="background-image">
        <div class="overlay"></div> <!-- Optional, bisa pakai ::before juga -->
        <div class="login-box" style="width: 40%;">
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                    <div class="text-center mb-3">
                        <img class="mb-2 mt-2" src="../../uploads/images/kemenkes.png" alt="Logo Kemenkes" style="height: 100px;">
                    </div>
                    <h3 class="text-center" style="padding-bottom: 1rem">Sistem Monitoring dan Evaluasi Manajemen Risiko <?= $companyName ?></h3>
                    <p class="login-box-msg">Masuk untuk memulai sesi anda</p>

                    <form method="post" action="">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="username" placeholder="Username" value="<?= $_POST['username'] ?? "" ?>" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Kata Sandi" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- /.col -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block" name="login">Masuk</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>

                    <p class="mb-3 text-center text-red" <?= $status ?? false ? "hidden" : "" ?>>
                        <?= $message ?>
                    </p>
                    <!-- <p class="mb-1">
                    <a href="<?= URLEnum::getForgotPasswordURL() ?>">Lupa Password</a>
                </p>
                <p class="mb-0">
                    <a href="<?= URLEnum::getRegisterURL() ?>" class="text-center">Buat Akun Baru</a>
                </p> -->
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
        <!-- /.login-box -->
    </div>

    <!-- jQuery -->
    <script src="<?= $base_url ?>/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= $base_url ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= $base_url ?>/dist/js/adminlte.min.js"></script>

</body>

</html>