<!-- CKEDITOR & CKFINDER -->
<script type="text/javascript" src="<?php echo public_url('admin');?>/plugins/ckeditor/ckeditor.js "></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/dist/js/config_ckeditor.js "></script>
<!-- END CKEDITOR & CKFINDER -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cập nhật tin tức khuyến mãi
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Cập nhật tin tức khuyến mãi</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form class="form-validate form-horizontal" action="" id="frm_edit" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="news_id" value="<?= $news->_id ?>">
                <!-- main form -->
                <div class="col-md-12">
                    <!-- general form elements disabled -->
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Thông tin</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <div id="info" class="tab-pane">
                                        <div class="form-group ">
                                            <label for="title" class="control-label col-lg-2">Tiêu đề</label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="title" name="title" type="text" value="<?= $news->title ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="img_src" class="control-label col-lg-2">Hình ảnh</label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="img_src" name="img_src" type="file" accept="image/*" />
                                            </div>
                                            <span id="img_preview">
                                                <img width="100" src="<?= base_url() . $news->img_src ?>"/>
                                            </span>
                                        </div>
                                        <div class="form-group ">
                                            <label for="content_item" class="control-label col-lg-2">Nội dung</label>
                                            <div class="col-lg-10">
                                                <textarea class="form-control animated" id="content_item" name="content_item"><?= $news->_description ?></textarea>
                                                <script type="text/javascript">ckeditor("content_item")</script>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="show_news" class="control-label col-lg-2">Hiện</label>
                                            <div class="col-lg-2">
                                                <input type="checkbox" name="is_active" id="show_news" checked>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group text-right">
                                        <button id="btn_edit" name="btn_edit" type="button" class="btn btn-primary"><i class="fa fa-pencil-square-o"></i> Cập nhật</button> &nbsp;&nbsp;
                                        <button type="button" class="btn btn-danger" onclick="return window.history.back();"><i class="fa fa-rotate-left"></i> Quay lại DS</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!--/.col (right) -->
            </form>
        </div>   <!-- /.row -->
    </section><!-- /.content -->
    <!-- / Main content -->
</div><!-- /.row -->

<!-- Edit news JS -->
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/news/edit.js"></script>
