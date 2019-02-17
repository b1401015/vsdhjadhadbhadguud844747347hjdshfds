<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Danh sách user
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
            <li class="active">Danh sách user</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <a href="<?= base_url('_admin/user/add_user') ?>">
                            <button type="button" class="btn btn-info pull-left">
                                Thêm mới
                            </button>
                        </a>
                        <div style="font-size: 16px; float: right;">
                            <span>Tổng số user:</span>
                            <span style="font-weight: bold; color: red;" id="total_user"></span>
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th></th>
                                <th>#</th>
                                <th>Loại tài khoản</th>
                                <th>Họ tên</th>
                                <th>Ảnh đại diện</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Giới tính</th>
                                <th>Trạng thái</th>
                                <th>Hiện</th>
                                <th>Ngày tạo</th>
                                <th>Ngày chỉnh sửa</th>
                                <th>Hành động</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.section -->
</div><!-- /.content -->

<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/user/list.js"></script>