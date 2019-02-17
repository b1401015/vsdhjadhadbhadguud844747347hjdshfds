<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cập nhật danh mục
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Cập nhật danh mục</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form class="form-validate form-horizontal" action="" id="frm_edit" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="category_id" value="<?= $category->_id ?>">
                <!-- main form -->
                <div class="col-md-12">
                    <!-- general form elements disabled -->
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Thông tin danh mục</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <div id="info" class="tab-pane">
                                        <div class="form-group ">
                                            <label for="category" class="control-label col-lg-2">Tên danh mục</label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="name" name="name" type="text" value="<?= $category->name ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="img_src" class="control-label col-lg-2">Hình ảnh</label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="img_src" name="img_src" type="file" accept="image/*" />
                                            </div>
                                            <span id="img_preview">
                                                <img width="100" src="<?= base_url() . $category->img_src ?>"/>
                                            </span>
                                        </div>
                                        <div class="form-group ">
                                            <label for="ordinal" class="control-label col-lg-2">Thứ tự hiển thị</label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="ordinal" name="ordinal" onkeypress="return isNumberKey(event)" type="text" value="<?= $category->ordinal ?>" min="1" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="show_product" class="control-label col-lg-2">Danh mục nổi bậc</label>
                                            <div class="col-lg-2">
                                                <input type="checkbox" name="is_featured" id="is_feature" <?= $category->is_featured == "1" ? "checked" : "" ?> >
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="show_caegory" class="control-label col-lg-2">Hiện</label>
                                            <div class="col-lg-2">
                                                <input class="is-active-item" type="checkbox" name="is_active" id="show_category" <?= $category->is_active == "1" ? "checked" : "" ?> >
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group text-right">
                                        <button id="btn_edit" name="btn_edit" type="button" class="btn btn-primary"><i class="fa fa-fw fa-pencil-square-o"></i> Cập nhật</button> &nbsp;&nbsp;
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

<!-- Add new product JS -->
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/category/edit.js"></script>