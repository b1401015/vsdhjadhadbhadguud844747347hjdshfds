<style>
    .select2-container{
        width: 100% !important;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cập nhật thông tin đơn hàng
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('_admin/order/listing') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Cập nhật thông tin đơn hàng</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <section class="panel">
                    <header class="panel-heading">

                    </header>
                    <div class="panel-body">
                        <div class="form">
                            <form class="form-validate form-horizontal" id="frm_edit">
                                <input type="hidden" id="order_id" name="order_id" value="<?= $order->_id ?>">
                                <input type="hidden" id="voucher_code" name="voucher_code" value="<?= $order->voucher_code ?>">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#general">Thông tin chung</a></li>
                                    <li><a data-toggle="tab" href="#description">Danh sách sản phẩm</a></li>
                                    <li class="pull-right"><button class="btn btn-primary edit_submit"  id="btn_edit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing Order" type="button">Lưu</button></li>
                                    <li class="pull-right" style="margin-right: 10px;"><button type="button" class="btn btn-default" onclick="return window.history.back();">Hủy bỏ</button></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="general" class="tab-pane fade in active">
                                        <h3></h3>
                                        <div class="form-group ">
                                            <label for="token" class="control-label col-lg-2">Mã đơn hàng<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input readonly class="form-control trim_value" id="token" name="token" type="text" value="<?= $order->ref ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="account_id" class="control-label col-lg-2">Mã khách hàng<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input readonly class="form-control trim_value" type="text" value="<?= '[ID: ' . $order->account_id . ']' . ' ' .$order->fullname ?>" />
                                                <input type="hidden" id="account_id" name="account_id" value="<?=$order->account_id ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="customer_id" class="control-label col-lg-2">Tên người nhận<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="customer_id" name="name" type="text" value="<?= $order->name ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="customer_id" class="control-label col-lg-2">Số điện thoại<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="phone_" name="phone" onkeypress="return numberPressed(event);" placeholder="Số điện thoại" type="text" value="<?= $order->phone ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="customer_id" class="control-label col-lg-2">Thời gian giao hàng<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <div class="input-group">
                                                    <input id="time_delivery" class="form-control appointment-control datetime thoi-gian-giao" type="text" name="time_delivery" placeholder="Thời gian giao" value="<?= date('d/m/Y H:i:s', strtotime($order->time_delivery)) ?>" autocomplete="off" required >
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                </div><!-- /.input group -->
                                            </div>
                                        </div>
                                    </div>
                                    <div id="description" class="tab-pane fade">
                                        <h3></h3>
                                        <div class="col-xs-12">
                                            <div id="item_template">
                                                <fieldset>
                                                    <div class="col-xs-4">
                                                        <div class="form-group ">
                                                            <label for="list_products" class="control-label col-lg-3">Sản phẩm <span class="required">*</span></label>
                                                            <div class="col-lg-9">
                                                                <select class="form-control select2 list_products" id="product_id" _autocheck="true" >
                                                                    <?php if(!empty($records)): ?>
                                                                        <option selected disabled>Lựa chọn sản phẩm </option>
                                                                        <?php foreach ($records as $record): ?>
                                                                            <option data-code="<?=$record->code ?>" data-name="<?=$record->title ?>" data-price="<?=$record->price ?>" data-sale-price="<?=!empty($record->sale_price) ? $record->sale_price : $record->price ?>" value="<?= $record->_id ?>"><?= $record->title. ' ' . '(' . $record->code . ')'   ?></option>
                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-3">
                                                        <div class="form-group ">
                                                            <label for="qty" class="control-label col-lg-3">Số lượng</label>
                                                            <div class="col-lg-9">
                                                                <input class="form-control format_number" id="qty" name="qty" onKeyPress="return isNumberKey(event)" value="1" min="1" type="text"/>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-xs-2">
                                                        <div class="form-group text-center">
                                                            <button id="add_product" name="add_product" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Thêm sản phẩm</button> &nbsp;&nbsp;
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <div style="margin-top: 30px" class="col-xs-12">
                                            <table id="example1" class="table table-bordered table-striped">
                                                <caption style="text-align: center; font-size: 16px; border-top: 5px dotted #ccc;">Danh sách sản phẩm</caption>
                                                <thead>
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Mã sản phẩm</th>
                                                    <th>Tên sản phẩm</th>
                                                    <th>Giá (VNĐ)</th>
                                                    <th>Số lượng</th>
                                                    <th>Thành tiền (VNĐ)</th>
                                                    <th style="width:10%">Công cụ</th>
                                                </tr>
                                                </thead>
                                                <tbody class="list-items">
                                                <?php $stt = 1 ?>
                                                <?php
                                                    foreach ($order_items as $order_item):
                                                        $price = $order_item->price;
                                                        $sale_price = !empty($order_item->sale_price) ? $order_item->sale_price : $order_item->price;
                                                        $total_price = $sale_price * $order_item->quantity;
                                                        $price = number_format($price, 0, '.', ',');
                                                        $sale_price = number_format($sale_price, 0, '.', ',');
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?= $stt ?>
                                                        <br/>
                                                        <input type="hidden" name="product_id[]" value="<?= $order_item->record_id ?>">
                                                        <input type="hidden" name="product_code[]" value="<?= $order_item->code ?>">
                                                        <input type="hidden" name="product_name[]" value="<?= $order_item->title ?>">
                                                        <input type="hidden" class="product_price" name="product_price[]" value="<?=$price ?>">
                                                        <input type="hidden" class="product_sale_price" name="product_sale_price[]" value="<?=$sale_price ?>">
                                                        <input type="hidden" id="product-<?= $order_item->record_id ?>">
                                                    </td>
                                                    <td>
                                                        <?= $order_item->code ?>

                                                    </td>
                                                    <td>
                                                        <?= $order_item->title ?>
                                                    </td>
                                                    <td>
                                                        <?=$price ?>
                                                    </td>
                                                    <td>
                                                        <input class="form-control format_number qty" name="product_qty[]" onKeyPress="return isNumberKey(event)" value="<?= $order_item->quantity ?>" min="1" type="text"/>
                                                    </td>
                                                    <td >
                                                        <span class="total-price-item"><?=number_format($total_price, 0, '.', ','); ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="delete-item" href="javascript:void(0)"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                                <?php $stt++ ?>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section>
    <!-- / Main content -->
</div><!-- /.row -->

<!-- Edit user info JS -->
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/order/edit.js"></script>