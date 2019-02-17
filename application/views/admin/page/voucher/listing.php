<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Danh sách khuyến mãi
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
            <li class="active">Danh sách khuyến mãi</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <a href="<?= base_url('_admin/voucher/add_voucher') ?>">
                            <button type="button" class="btn btn-info pull-left">
                                Thêm mới
                            </button>
                        </a>
                    </div>
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã giảm giá</th>
                                <th>Tên</th>
<!--                                <th>Loại</th>-->
<!--                                <th>Hình ảnh</th>-->
                                <th>Thời gian áp dụng</th>
                                <th>Giá trị giảm</th>
                                <th>Giá giảm tối đa</th>
                                <th>Số lượng</th>
                                <th>Hiện</th>
<!--                                <th>Được cộng dồn</th>-->
<!--                                <th>Không giới hạn số lượng</th>-->
                                <th>Thứ tự hiển thị</th>
                                <th>Ngày chỉnh sửa</th>
                                <th style="width:10%">Công cụ</th>
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

<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/voucher/list.js"></script>