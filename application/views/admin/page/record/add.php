<!-- CKEDITOR & CKFINDER -->
<script type="text/javascript" src="<?php echo public_url('admin');?>/plugins/ckeditor/ckeditor.js "></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/dist/js/config_ckeditor.js "></script>
<!-- END CKEDITOR & CKFINDER -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Add a new product
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Forms</a></li>
            <li class="active">General Elements</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form class="form-validate form-horizontal" action="" id="frm_add" method="POST" enctype="multipart/form-data">
                <!-- main form -->
                <div class="col-md-12">
                    <!-- general form elements disabled -->
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Product Information</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <div id="info" class="tab-pane">
                                        <br/>
                                        <div class="form-group ">
                                            <label for="code" class="control-label col-lg-2">Code</label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="code" name="code" type="text" value="" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="barcode" class="control-label col-lg-2">Barcode</label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="barcode" name="barcode" type="text" value="" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="title" class="control-label col-lg-2">Tên sản phẩm<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="title" name="title" type="text" required />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="description" class="control-label col-lg-2">Mô tả</label>
                                            <div class="col-lg-5">
                                                <textarea style="resize: none; overflow: hidden; word-wrap: break-word; height: 54px;" rows="3" class="form-control animated" id="description" name="description"></textarea>
                                                <script type="text/javascript">ckeditor("description")</script>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="description" class="control-label col-lg-2">Mô tả ngắn</label>
                                            <div class="col-lg-5">
                                                <textarea style="resize: none; overflow: hidden; word-wrap: break-word; height: 54px;" rows="3" class="form-control animated" id="short_description" name="short_description"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="product_img" class="control-label col-lg-2">Ảnh sản phẩm<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="img_src" name="img_src" type="file" required accept="image/*" />
                                            </div>
                                            <span id="img_preview"></span>
                                        </div>
                                        <div class="form-group ">
                                            <label for="price" class="control-label col-lg-2">Giá bán</label>
                                            <div class="col-lg-5">
                                                <input class="form-control format_number" id="price" name="price" onKeyPress="return isNumberKey(event)" type="text" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="sale_price" class="control-label col-lg-2">Giá giảm</label>
                                            <div class="col-lg-5">
                                                <input class="form-control format_number" id="sale_price" name="sale_price" onKeyPress="return isNumberKey(event)" type="text" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="sale_price" class="control-label col-lg-2">Giá sỉ</label>
                                            <div class="col-lg-5">
                                                <input class="form-control format_number" id="rating" name="rating" onKeyPress="return isNumberKey(event)" type="text" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="category" class="control-label col-lg-2">Danh mục *</label>
                                            <div class="col-lg-5">
                                                <select class="form-control select2" name="category_id[]" id="category_id" multiple="true" _autocheck="true" >
                                                    <?php if(!empty($categories)): ?>
                                                        <?php foreach ($categories as $category): ?>
                                                            <option value="<?= $category->_id ?>"><?= $category->name ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="price" class="control-label col-lg-2">Đánh giá</label>
                                            <div class="col-lg-5">
                                                <input class="form-control " min="1" max="5" id="point_elv" name="point_elv" onKeyPress="return isNumberKey(event)" type="number" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="featured" class="control-label col-lg-2">Bán chạy</label>
                                            <div class="col-lg-2">
                                                <input type="checkbox" name="is_featured" id="stragery_product">
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="show_product" class="control-label col-lg-2">Hiện</label>
                                            <div class="col-lg-2">
                                                <input type="checkbox" name="is_active" id="show_product" checked>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-xs-12">
                                    <div class="form-group text-right">
                                        <button id="btn_add" name="btn_add" type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Thêm</button> &nbsp;&nbsp;
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
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/record/add.js"></script>