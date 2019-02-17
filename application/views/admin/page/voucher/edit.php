<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cập nhật thông tin mã khuyến mãi
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('_admin/user/listing') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Cập nhật thông tin mã khuyến mãi</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form class="form-validate form-horizontal" action="" id="frm_edit" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="voucher_id" value="<?= $voucher->_id ?>">
                <!-- main form -->
                <div class="col-md-12">
                    <!-- general form elements disabled -->
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Thông tin mã khuyến mãi</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <h3></h3>
                                    <div class="form-group ">
                                        <label for="name" class="control-label col-lg-2">Tên khuyến mãi</label>
                                        <div class="col-lg-5">
                                            <input class="form-control" id="name" name="name" type="text" value="<?= $voucher->name ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="_value" class="control-label col-lg-2">Giá trị giảm</label>
                                        <div class="col-lg-3">
                                            <input class="form-control percent_value" <?php if(strpos($voucher->type, PRICE_TYPE) !== false ): ?> style="display: none" <?php endif; ?> name="percent_value" min="0" max="100" value="<?= !empty($voucher->_value) ? number_format($voucher->_value, 0) : '' ?>" type="number"/>
                                            <input class="form-control format_number" <?php if(strpos($voucher->type, PERCENT_TYPE) !== false ): ?> style="display: none" <?php endif; ?> id="_value" onKeyPress="return isNumberKey(event)" name="_value" value="<?= !empty($voucher->_value) ? number_format($voucher->_value,0, '.', ',') : '' ?>" type="text"/>
                                        </div>
                                    </div>

                                    <div class="form-group max-value" <?php if(strpos($voucher->type, PRICE_TYPE) !== false ): ?> style="display: none" <?php endif; ?>>
                                        <label for="max_value" class="control-label col-lg-2">Giá giảm tối đa</label>
                                        <div class="col-lg-3">
                                            <input class="form-control format_number" id="max_value" onKeyPress="return isNumberKey(event)" name="max_value" value="<?= !empty($voucher->max_value) ? $voucher->max_value : '' ?>" type="text"/>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="type_user" class="control-label col-lg-2">Loại giảm</label>
                                        <div class="col-lg-2">

                                            <!-- Version 1 -->
                                            <select class="form-control select2" name="voucher_type" id="voucher_type" _autocheck="true" >
                                                <option <?= $voucher->type == strtolower(PERCENT_TOTAL_TYPE) ? "selected" : "" ?> value="<?= PERCENT_TOTAL_TYPE ?>">Giảm % tổng đơn hàng</option>
                                                <option <?= $voucher->type == strtolower(PRICE_TOTAL_TYPE) ? "selected" : "" ?> value="<?= PRICE_TOTAL_TYPE ?>">Giảm giá tổng đơn hàng</option>
                                            </select>
                                        </div>

                                        <div class="sale-record" <?php if(strpos($voucher->type, ITEM_TYPE) == false ): ?> style="display: none" <?php endif; ?>>
                                            <label for="type_user" class="control-label col-lg-1">Sản phẩm</label>
                                            <div class="col-lg-5">
                                                <select class="form-control select2" name="record_id" id="record_id" _autocheck="true" style="width: 50% !important;">
                                                    <?php if(!empty($records)): ?>
                                                        <?php foreach ($records as $record): ?>
                                                            <option <?= $voucher->record_id == $record->_id ? "selected" : "" ?> value="<?= $record->_id ?>"><?= '[ID: '. $record->_id. '] ' . $record->title ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group hide">
                                        <label for="img_src" class="control-label col-lg-2">Hình ảnh</label>
                                        <div class="col-lg-5">
                                            <input class="form-control" id="img_src" name="img_src" minlength="5" type="file" />
                                            <img width="100" src="<?= base_url() . $voucher->img_src ?>"/>
                                        </div>
                                        <span id="img_preview"></span>
                                    </div>

                                    <div class="form-group ">
                                        <label for="description" class="control-label col-lg-2">Mô tả</label>
                                        <div class="col-lg-5">
                                            <textarea style="resize: none" rows="3" class="form-control animated" id="description" name="description" ><?= $voucher->_description ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="daterange" class="control-label col-lg-2">Thời gian áp dụng<span class="required">*</span></label>
                                        <div class="col-lg-3">
                                            <div class="input-group">
                                                <input type="text" class="form-control pull-left daterange" id="daterange" value="<?= date('d/m/Y', strtotime($voucher->time_start)) . ' - ' . date('d/m/Y', strtotime($voucher->time_end))  ?>"/>
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div><!-- /.input group -->
                                            <input class="form-control" id="time_start" name="time_start" value="<?= date('Y-m-d', strtotime($voucher->time_start)) ?>" type="hidden" />
                                            <input class="form-control" id="time_end" name="time_end" value="<?= date('Y-m-d', strtotime($voucher->time_end)) ?>" type="hidden" />
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="stock" class="control-label col-lg-2">Số lượng</label>
                                        <div class="col-lg-2">
                                            <input class="form-control" id="stock" type="text" name="stock" onKeyPress="return isNumberKey(event)" value="<?= $voucher->stock ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="ordinal" class="control-label col-lg-2">Thứ tự hiển thị</label>
                                        <div class="col-lg-1">
                                            <input class="form-control" id="ordinal" type="text" name="ordinal" onKeyPress="return isNumberKey(event)" value="<?= $voucher->ordinal ?>" min="1" />
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="is_active" class="control-label col-lg-2"></label>
                                        <div class="col-lg-5">
                                            <div class="checkbox">
                                                <input tabindex="1" style="margin-left: 0" type="checkbox" name="is_active" id="is_active" checked>
                                                <label for="is_active">Hiển thị</label>
                                            </div>
                                            <!--                                            <div class="checkbox">-->
                                            <!--                                                <input tabindex="1" style="margin-left: 0" type="checkbox" name="is_unlimited" id="is_unlimited">-->
                                            <!--                                                <label for="is_unlimited">Không giới hạn số lượng</label>-->
                                            <!--                                            </div>-->
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="form-group text-right">
                                        <button id="btn_edit" name="btn_edit" type="button" class="btn btn-primary"><i class="fa fa-pencil-o"></i> Cập nhật</button> &nbsp;&nbsp;
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

<!-- Edit user info JS -->
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/voucher/edit.js"></script>