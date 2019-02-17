<!-- CKEDITOR & CKFINDER -->
<script type="text/javascript" src="<?php echo public_url('admin');?>/plugins/ckeditor/ckeditor.js "></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/dist/js/config_ckeditor.js "></script>
<!-- END CKEDITOR & CKFINDER -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cập nhật banner
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Cập nhật banner</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form class="form-validate form-horizontal" action="" id="frm_edit" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="banner_id" value="<?= $banner->_id ?>">
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
                                            <label for="category" class="control-label col-lg-2">Danh mục</label>
                                            <div class="col-lg-5">
                                                <select class="form-control select2" name="category_id" id="category_id"  _autocheck="true" >
                                                    <option value="" <?= $banner->category_id == "" ? "selected" : "" ?> >Chọn</option>
                                                    <?php if(!empty($categories)): ?>
                                                        <?php foreach ($categories as $category): ?>
                                                            <option value="<?= $category->_id ?>" <?= $category->_id == $banner->category_id ? "selected" : "" ?>
                                                            ><?= $category->name ?></option>

                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="name" class="control-label col-lg-2">Tiêu đề<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="name" name="name" value="<?= $banner->name ?>" type="text" required />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="img_src" class="control-label col-lg-2">Hình ảnh<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="img_src" name="img_src" type="file" accept="image/*" />
                                            </div>
                                            <span id="img_preview">
                                                <img width="100" src="<?= base_url() . $banner->img_src ?>"/>
                                            </span>
                                        </div>
                                        <div class="form-group ">
                                            <label for="content_item" class="control-label col-lg-2">Nội dung<span class="required">*</span></label>
                                            <div class="col-lg-10">
                                                <textarea class="form-control animated" id="content_item" name="content_item"><?= $banner->_description ?></textarea>
                                                <script type="text/javascript">ckeditor("content_item")</script>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="ordinal" class="control-label col-lg-2">Thứ tự hiển thị</label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="ordinal" name="ordinal" value="<?= $banner->ordinal ?>" type="text"/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="show_banner" class="control-label col-lg-2">Hiện</label>
                                            <div class="col-lg-2">
                                                <input type="checkbox" name="is_active" id="show_banner" <?= $banner->is_active == 1 ? "checked" : "" ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group text-right">
                                        <button id="btn_edit" name="btn_edit" type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i> Cập nhật</button> &nbsp;&nbsp;
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

<!-- Add new news JS -->
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/banner/edit.js"></script>
