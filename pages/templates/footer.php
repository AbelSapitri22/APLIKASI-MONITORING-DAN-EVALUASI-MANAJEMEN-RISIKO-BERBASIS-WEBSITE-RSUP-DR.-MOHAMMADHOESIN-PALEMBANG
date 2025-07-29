<!-- /.content-wrapper -->
<footer class="main-footer">
    <strong>Copyright &copy; 2024 <a href="#"><?= $companyName ?></a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0
    </div>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?= $base_url ?>/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= $base_url ?>/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?= $base_url ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<?php
if (
    $pageName == PageNameEnum::DASHBOARD
) { ?>
    <!-- ChartJS -->
    <script src="<?= $base_url ?>/plugins/chart.js/Chart.min.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?= $base_url ?>/plugins/jquery-knob/jquery.knob.min.js"></script>
<?php } ?>
<!-- Sparkline -->
<script src="<?= $base_url ?>/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?= $base_url ?>/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="<?= $base_url ?>/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- daterangepicker -->
<script src="<?= $base_url ?>/plugins/moment/moment.min.js"></script>
<script src="<?= $base_url ?>/plugins/daterangepicker/daterangepicker.js"></script>

<?php
if (
    $pageName == PageNameEnum::DATA_UNIT
    || $pageName == PageNameEnum::DATA_PENGGUNA
    || $pageName == PageNameEnum::DATA_PENILAIAN_RISIKO
    || $pageName == PageNameEnum::DATA_PEMANTAUAN_REVIU
    || $pageName == PageNameEnum::DATA_PEMANTAUAN_REVIU_DITOLAK
    || $pageName == PageNameEnum::DATA_PENILAIAN_RISIKO_DITOLAK
    || $pageName == PageNameEnum::DATA_PENGGUNA
) { ?>
    <!-- DataTables -->
    <script src="<?= $base_url ?>/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?= $base_url ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?= $base_url ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?= $base_url ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?= $base_url ?>/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?= $base_url ?>/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="<?= $base_url ?>/plugins/jszip/jszip.min.js"></script>
    <script src="<?= $base_url ?>/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="<?= $base_url ?>/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="<?= $base_url ?>/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?= $base_url ?>/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="<?= $base_url ?>/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?= $base_url ?>/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="<?= $base_url ?>/plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="<?= $base_url ?>/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= $base_url ?>/dist/js/adminlte.js"></script>
    <!-- Bootstrap Switch -->
    <script src="<?= $base_url ?>/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>

    <!-- Select2 -->
    <script src="../../plugins/select2/js/select2.full.min.js"></script>

    <script>
        <?php if ($_SESSION['role'] == RoleEnum::DIREKSI) { ?>
            $(document).ready(function() {
                $("#example1").DataTable({
                    "responsive": true,
                    "lengthChange": true,
                    "autoWidth": true,
                    "buttons": ["csv", "excel", "pdf"]
                }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            });
        <?php } else { ?>
            $(document).ready(function() {
                $("#example1").DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false
                }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            });
        <?php } ?>

        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        });
    </script>
<?php } ?>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<?php if ($pageName == PageNameEnum::DASHBOARD) {
    echo `<script src="$base_url/dist/js/pages/dashboard.js"></script>`;
}
?>

<!-- AdminLTE for demo purposes -->
<script src="<?= $base_url ?>/dist/js/demo.js"></script>
<!-- SweetAlert2 -->
<script src="<?= $base_url ?>/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?= $base_url ?>/plugins/toastr/toastr.min.js"></script>
</body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/smr/pages/templates/toast-message.php'; ?>

</html>