<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <p class="brand-link text-center">
        <span class="brand-text font-weight-light"><?= $companyName ?></span>
    </p>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <div class="row">
                    <div class="col-auto">
                        <span class="text-grey text-white">Selamat Datang,</span>
                    </div>
                    <div class="col-auto" style="margin-left: -8px;">
                        <a href="<?= URLEnum::getAccountURL() ?>" class="d-block text-white"><span><?= $_SESSION['name'] ?? "" ?></span></a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="<?= URLEnum::getDashboardURL() ?>" class="nav-link <?= $pageName == PageNameEnum::DASHBOARD ? "active" : "" ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            <?= PageNameEnum::DASHBOARD ?>
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= URLEnum::getRiskAssasmentURL() ?>" class="nav-link <?= $pageName == PageNameEnum::DATA_PENILAIAN_RISIKO ? "active" : "" ?>">
                        <i class="nav-icon fas fa-list"></i>
                        <p>
                            <?= PageNameEnum::DATA_PENILAIAN_RISIKO ?>
                        </p>
                    </a>
                </li>
                <?php if ($_SESSION['role'] != RoleEnum::DIREKSI) { ?>
                    <li class="nav-item">
                        <a href="<?= URLEnum::getRiskAssasmentRejectedURL() ?>" class="nav-link <?= $pageName == PageNameEnum::DATA_PENILAIAN_RISIKO_DITOLAK ? "active" : "" ?>">
                            <i class="nav-icon fas fa-times"></i>
                            <p>
                                Data Penilaian Ditolak
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a href="<?= URLEnum::getMonitoringReviewURL() ?>" class="nav-link <?= $pageName == PageNameEnum::DATA_PEMANTAUAN_REVIU ? "active" : "" ?>">
                        <i class="nav-icon fas fa-solid fa-search"></i>
                        <p>
                            <?= PageNameEnum::DATA_PEMANTAUAN_REVIU ?>
                        </p>
                    </a>
                </li>
                <?php if ($_SESSION['role'] != RoleEnum::DIREKSI) { ?>
                    <li class="nav-item">
                        <a href="<?= URLEnum::getMonitoringReviewRejectedURL() ?>" class="nav-link <?= $pageName == PageNameEnum::DATA_PEMANTAUAN_REVIU_DITOLAK ? "active" : "" ?>">
                            <i class="nav-icon fas fa-times"></i>
                            <p>
                                Data Pemantauan Ditolak
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['role'] == RoleEnum::ADMIN) { ?>
                    <li class="nav-item">
                        <a href="<?= URLEnum::getUsersURL() ?>" class="nav-link <?= $pageName == PageNameEnum::DATA_PENGGUNA ? "active" : "" ?>">
                            <i class="nav-icon fas fa-address-card"></i>
                            <p>
                                <?= PageNameEnum::DATA_PENGGUNA ?>
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <?php if ($_SESSION['role'] == RoleEnum::ADMIN) { ?>
                    <li class="nav-item">
                        <a href="<?= URLEnum::getUnitURL() ?>" class="nav-link <?= $pageName == PageNameEnum::DATA_UNIT ? "active" : "" ?>">
                            <i class="nav-icon fas fa-address-card"></i>
                            <p>
                                <?= PageNameEnum::DATA_UNIT ?>
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a href="<?= URLEnum::getAccountURL() ?>" class="nav-link <?= $pageName == PageNameEnum::AKUN ? "active" : "" ?>">
                        <i class="nav-icon fas fa-user-alt"></i>
                        <p>
                            <?= PageNameEnum::AKUN ?>
                        </p>
                    </a>
                </li>
                <li class="nav-item hover">
                    <a href="<?= URLEnum::getLogoutURL() ?>" class="nav-link text-danger">
                        <i class="nav-icon fas fa-chevron-right"></i>
                        <!-- <i class="fa-solid fa-right-from-bracket"></i> -->
                        <p>
                            <?= PageNameEnum::LOGOUT ?>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>