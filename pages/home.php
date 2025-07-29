<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskAssessmentModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/RiskCategoryModel.php');

$riskAssessmentModel    = new RiskAssessmentModel();
$riskCategoryModel    = new RiskCategoryModel();
$usersModel             = new UsersModel();

$totalRiskAssessments           = $riskAssessmentModel->getTotalRiskAssessments();
$totalRiskAssessmentsVerfied    = $riskAssessmentModel->getTotalRiskAssessmentsVerified(true);
$totalRiskAssessmentsRejected   = $riskAssessmentModel->getTotalRiskAssessmentsVerified(false);
$totalRiskAssessmentsWaited     = $riskAssessmentModel->getTotalRiskAssessmentsWaited();

$totalUsers     = number_format($usersModel->getTotalUsers(), 0, ".", ".");

$riskCategories     = $riskCategoryModel->getRiskCategories();
$totalByCategories       = array(
    'keuangan'      => $riskAssessmentModel->getTotalRiskAssessmentsByCategory(1),
    'kebijakan'     => $riskAssessmentModel->getTotalRiskAssessmentsByCategory(2),
    'reputasi'      => $riskAssessmentModel->getTotalRiskAssessmentsByCategory(3),
    'fraud'         => $riskAssessmentModel->getTotalRiskAssessmentsByCategory(4),
    'legal'         => $riskAssessmentModel->getTotalRiskAssessmentsByCategory(5),
    'kepatuhan'     => $riskAssessmentModel->getTotalRiskAssessmentsByCategory(6),
    'operasional'   => $riskAssessmentModel->getTotalRiskAssessmentsByCategory(7)
);

$riskCategoriesJSON     = json_encode($riskCategories);
$totalByCategoriesJSON  = json_encode($totalByCategories);
?>

<style>
    .col-2-4 {
        flex: 0 0 20%;
        max-width: 20%;
    }

    @media (max-width: 768px) {
        .col-2-4 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <li class="breadcrumb-item"><a href="<?= $base_url ?>"><?= PageNameEnum::DASHBOARD ?></a></li>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Penilaian Risiko -->
    <section class="content">
        <div class="container-fluid">
            <h4>Penilaian Risiko</h4>
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $totalRiskAssessments ?></h3>
                            <p>Total Penilaian Risiko</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-chart-bar"></i>
                        </div>
                        <a href="<?= URLEnum::getRiskAssasmentURL() ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $totalRiskAssessmentsVerfied ?></h3>
                            <p>Total Terverifikasi</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-sharp fa-solid fa-check"></i>
                        </div>
                        <a href="<?= URLEnum::getRiskAssasmentURL() ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $totalRiskAssessmentsRejected ?></h3>
                            <p>Total Ditolak</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-sharp fa-solid fa-times"></i>
                        </div>
                        <a href="<?= URLEnum::getRiskAssasmentURL() ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $totalRiskAssessmentsWaited ?></h3>
                            <p>Total Menunggu Verifikasi</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-sharp fa-solid fa-clock"></i>
                        </div>
                        <a href="<?= URLEnum::getRiskAssasmentURL() ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->
            <!-- Main row -->
            <div class="row">

            </div>
    </section>
    <!-- Penilaian Risiko -->
    <!-- Donut Chart  -->
    <section class="content">
        <div class="container-fluid">
            <!-- DONUT CHART -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Penilaian Risiko Chart</h3>
                </div>
                <div class="card-body">
                    <canvas id="donutChart" style="min-height: 250px; height: 350px; max-height: 350px; max-width: 100%;"></canvas>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </section>
    <!-- Donut Chart  -->
</div>
<!-- /.row (main row) -->
</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>

<script>
    $(function() {
        //-------------
        //- DONUT CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var donutChartCanvas = $('#donutChart').get(0).getContext('2d');
        var riskCategoriesJSON = <?= $riskCategoriesJSON; ?>;
        var riskCategoriesName = riskCategoriesJSON.map(item => item.name);
        var totalByCategoriesJSON = <?= $totalByCategoriesJSON; ?>;

        var donutData = {
            labels: riskCategoriesName,
            datasets: [{
                data: [
                    totalByCategoriesJSON['keuangan'],
                    totalByCategoriesJSON['kebijakan'],
                    totalByCategoriesJSON['reputasi'],
                    totalByCategoriesJSON['fraud'],
                    totalByCategoriesJSON['legal'],
                    totalByCategoriesJSON['kepatuhan'],
                    totalByCategoriesJSON['operasional']

                ],
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#FF0000', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }]
        }
        var donutOptions = {
            maintainAspectRatio: false,
            responsive: true,
        }
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        new Chart(donutChartCanvas, {
            type: 'doughnut',
            data: donutData,
            options: donutOptions
        })

        //-------------
        //- PIE CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
        var pieData = donutData;
        var pieOptions = {
            maintainAspectRatio: false,
            responsive: true,
        }
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        new Chart(pieChartCanvas, {
            type: 'pie',
            data: pieData,
            options: pieOptions
        })
    });
</script>