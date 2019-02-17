<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Danh sách sản phẩm
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('admin/home') ?>"><i class="fa fa-dashboard"></i>Trang chủ</a></li>
            <li class="active">Sản phẩm</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <?php  $val_category=isset($_GET['filter']) ? $_GET['filter'] : ''; ?>
                    <div class="hide" id="upload_container">
                        <div>
                            File mẫu <a download href="<?=public_url().'/file/product_sample.xlsx'?>"><?php echo "product_sample.xlsx"?></a><!--strtolower(RECORD_ALIAS_EN) -->
                        </div>
                        <div>
                            <strong>Tải lên file danh sách <?=strtolower(RECORD_ALIAS) ?> (Excel):</strong>
                        </div>
                        <form id="frm_upload" method="post" action="" enctype="multipart/form-data">
                            <input type="file" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/>
                        </form><br/>
                        <button type="button" class="btn btn-primary" onclick="upload_excel_file();">Tải lên</button>
                        <span class="hide" id="txt_upload_status"></span>
                        <span id="import_err" style="color:red;"></span>
                        <hr/>
                    </div> <!-- .upload-container -->

                    <div class="box-header">
                        <a href="<?= base_url('_admin/record/add_record') ?>">
                            <button type="button" class="btn btn-info pull-left">
                                Thêm mới
                            </button>
                        </a>
                        <button onclick="toggle_frm_upload();" style="margin-left: 10px" type="button" class="btn btn-info pull-left">
                            Tải lên file excel
                        </button>
                        <div class="col-xs-8 col-sm-8 col-md-6 col-lg-6 " style="margin-left: 10px;">
                            <input type="hidden" value="<?= $val_category ?>" id="category_val">
                            <label class="control-label col-xs-12 col-sm-4 col-lg-2">Loại danh mục:</label>
                            <div class="col-xs-3 col-sm-6 col-lg-3">
                                <!-- <a href="http://ion.me:8080/_admin/order/listing">dddddddddddd</a>-->
                                <select id="filter_category"  onchange="javascript:handleSelect(this)" >
                                    <option  <?php if (empty($val_category)){ echo  'selected';}else{ echo '';} ?> value="<?= base_url('_admin/record/listing') ?>" >Tất cả danh mục</option>
                                    <?php if (!empty($category)){
                                        foreach ($category as $value){
                                            ?>
                                            <option  <?= $val_category== $value->_id ? 'selected' : '' ?> value="<?= base_url('_admin/record/listing?filter='.$value->_id) ?>"><?= $value->name ?></option>
                                        <?php } } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th></th>
                                <th>#</th>
                                <th>Mã</th>
                                <th>Tên</th>
                                <th>Ảnh</th>
                                <th>Thứ thự hiển thị</th>
                                <th>Danh mục</th>
                                <th>Giá bán</th>
                                <th>Bán chạy</th>
                                <th>Hiện</th>
<!--                                <th>Ngày tạo</th>-->
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



<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div id="modal_content_account" class="modal-content">
            <div class="table-responsive">
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/record/list.js"></script>