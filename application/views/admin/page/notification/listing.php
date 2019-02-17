<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Notification list
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?=base_url('/_admin/index')?>"><i class="fa fa-dashboard"></i>Trang chủ</a></li>
            <li class="active">Notification</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <a href="<?= base_url('_admin/notification/send_push') ?>">
                            <button type="button" class="btn btn-info pull-left">
                                Send push
                            </button>
                        </a>
                    </div>
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Type</th>
                                <th>User</th>
                                <th>Push time</th>
                                <th>Done</th>
                                <th>Create time</th>
                                <th>Update time</th>
                                <th>Action</th>
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

<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/notification/list.js"></script>