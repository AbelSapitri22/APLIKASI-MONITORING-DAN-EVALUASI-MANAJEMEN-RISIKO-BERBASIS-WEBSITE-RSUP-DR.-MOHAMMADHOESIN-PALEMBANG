<script type="text/javascript">
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    function showToastMessage(icon, message) {
        Toast.fire({
            icon: icon,
            title: message
        });
        <?php unset($_SESSION['toast_message']); ?>
    }
</script>