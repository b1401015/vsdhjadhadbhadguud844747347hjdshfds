<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- PAGE CONTENT -->
    <section class="content-header">
        <h1>
            <i class="fa fa-dashboard"></i>
            Home
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- <div class="col-lg-3 col-xs-6"> -->
            <!-- small box -->
            <!-- <div class="small-box bg-aqua">
              <div class="inner">
                <h3><?php //echo (!empty($news[0]['num']) ? $news[0]['num'] : 0) ?></h3>
                <p><?php //echo $translate->__('Tin tức</p> ?>
              </div>
              <div class="icon">
                <i class="ion ion-ios-paper"></i>
              </div>
              <a href="<?php //echo ADMIN_URL.'?page=tin-tuc' ?>" class="small-box-footer"><?php //echo $translate->__('Xem chi tiết <i class="fa fa-arrow-circle-right"></i></a> ?>
            </div>
          </div> -->
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6 hide">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>0</h3>
                        <p>Tin tức</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-book-outline"></i>
                    </div>
                    <a href="#" class="small-box-footer">Xem chi tiết <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->

            <h3 class="text-center">
                Chào mừng đến với <?=APP_TITLE_SHORT ?> !
            </h3>
            <!-- Main row -->

    </section><!-- /.content -->
</div>


<!-- Profile Modal -->
<div class="example-modal">
    <div id="profile" role="dialog" class="modal fade modal-primary">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Thay đổi mật khẩu</h4>
                </div>
                <div class="modal-body">
                    <form id="frm_edit_profile" enctype="multipart/form-data" method="post">
                        <div class="alert alert-danger">
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Mật khẩu cũ</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-lock"></i>
                                        </div>
                                        <input name="old_pwd" value="" type="password" class="form-control">
                                    </div><!-- /.input group -->
                                </div>
                                <div class="form-group">
                                    <label>Mật khẩu mới</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-lock"></i>
                                        </div>
                                        <input name="new_pwd" value="" type="password" class="form-control">
                                    </div><!-- /.input group -->
                                </div>
                                <div class="form-group">
                                    <label>Nhập lại mật khẩu mới</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-lock"></i>
                                        </div>
                                        <input name="re_pwd" value="" type="password" class="form-control">
                                    </div><!-- /.input group -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Đóng</button>
                    <button id="change_pass" type="button" class="btn btn-outline">Lưu</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div><!-- /.example-modal -->

<!-- Edit user info JS -->
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/js/change_password.js"></script>