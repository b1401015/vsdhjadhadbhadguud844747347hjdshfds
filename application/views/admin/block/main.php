<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view('admin/block/head') ?>
</head>
<body class="skin-blue sidebar-mini sidebar-collapse">
    <div class="wrapper">

    <!-- Sidebar -->
    <?php $this->load->view('admin/block/sidebar'); ?>

    <!-- Main header -->
    <?php $this->load->view('admin/block/header'); ?>

    <!-- Main content -->
    <?php $this->load->view($temp, $this->data); ?>

    <!-- Main footer -->
    <?php $this->load->view('admin/block/footer'); ?>

    </div>

</body>
</html>