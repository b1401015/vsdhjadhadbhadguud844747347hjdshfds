<?php
$thead = '
<tr>
        <th style="text-align: center; font-weight: bold;">
            STT
        </th>
    
        <th style="text-align: center; ; font-weight: bold;">
            Sản phẩm
        </th>
        <th style="text-align: center; ; font-weight: bold;">
            Giá
        </th>
        <th style="text-align: center; ; font-weight: bold;">
            Giá bán
        </th>
        <th style="text-align: center; ; font-weight: bold;">
            Số lượng
        </th>
        <th style="text-align: center; ; font-weight: bold;">
            Thành tiền
        </th>
    ';



?>

    <!DOCTYPE html>
    <html xmlns='http://www.w3.org/1999/xhtml'>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            *{font-size:14px!important;}
            .report_table tr td{min-height: 20px;}
            .video{width: 130px!important;}
            .report_table{
                border-collapse:collapse;
                border-width: thin;
            }
            .report_table th{
                font-weight:bold;
                /*padding:5px 0px 3px 5px;*/
                border-collapse:collapse;
                border:1px solid #8d8d8d;
            }
            .report_table td{
                /*padding:5px;*/
                border-collapse:collapse;
                border:1px solid #8d8d8d;
            }
            .report_table tr{
                border-collapse:collapse;
                border:1px solid #8d8d8d;
            }
            .footer-info span{
                font-weight: bold;
                width: 100%;
            }
            caption{background:#F7F7F7;font-weight:bold;height:30px;line-height:30px;font-size:24px;}
            .center{text-align:center;}
        </style>
        <title></title>
    </head>
<body>

<table width="100%" class="" >
    <thead>
    <tr>
        <th style="text-align: center; font-weight: bold; height: 50px" colspan="6">
            THÔNG TIN ĐƠN HÀNG
        </th>
    </tr>
    <tr></tr>

    </thead>
    <tbody>
        <tr>
            <td colspan="3">Họ tên: <?= $order->fullname ?></td>
            <td colspan="3">Mã đơn hàng: <?= $order->ref ?></td>
        </tr>
        <tr>
            <td colspan="3">Điện thoại: <?= $order->phone ?></td>
            <td colspan="3">Ngày đặt: <?= date('d/m/Y H:i:s', strtotime($order->create_time)) ?></td>
        </tr>
        <tr>
            <td colspan="3">Địa chỉ: <?= $order->order_location ?></td>
            <td colspan="3">Tình trạng:
                <?php $status = $order->status ?>
                <?php
                    if($status == strtolower(ORDER_STATUS_ORDERED)){
                        $status = "Vừa đặt";
                    }
                    elseif($status == strtolower(ORDER_STATUS_CONFIRMATION)){
                        $status = "Đã xác nhận";
                    }
                    elseif($status == strtolower(ORDER_STATUS_CHECKOUT)){
                        $status = "Đã thanh toán";
                    }
                    elseif($status == strtolower(ORDER_STATUS_DELIVERY)){
                        $status = "Đã giao hàng";
                    }
                    elseif($status == strtolower(ORDER_STATUS_VALIDATION)){
                        $status = "Đã hoàn tất";
                    }
                    elseif($status == strtolower(ORDER_STATUS_RETURN)){
                        $status = "Trả lại";
                    }
                    elseif($status == strtolower(ORDER_STATUS_DELAY)){
                        $status = "Bị hoãn";
                    }
                    elseif($status == strtolower(ORDER_STATUS_CANCEL)){
                        $status = "Hủy";
                    }
                ?>
                <?= $status ?>
            </td>
        </tr>
    </tbody>
</table>
<table width="100%" class="report_table">
    <thead>
        <tr>
            <?= $thead ?>
        </tr>
    </thead>
    <tbody>
    <?php $stt = 1; ?>
    <?php $total_price = 0; ?>
    <?php $total_price_item = 0; ?>

    <?php if(!empty($order_items)): ?>
    <?php foreach ($order_items as $order_item): ?>

        <tr>

            <td align="center" valign="middle" style="text-align: center; vertical-align: middle;">
                <?= $stt++ ?>
            </td>
            <td align="left" valign="middle" style="text-align: left; vertical-align: middle;">
                <?= $order_item->title ?>
            </td>
            <td align="center" valign="middle" style="text-align: right; vertical-align: middle;">
                <?=  number_format($order_item->price, 0,'.', ',')  ?>
            </td>
            <td align="center" valign="middle" style="text-align: right; vertical-align: middle;">
                <?= number_format($order_item->sale_price, 0,'.', ',') ?>
            </td>
            <td align="center" valign="middle" style="text-align: center; vertical-align: middle;">
                <?= $order_item->quantity ?>
            </td>
            <td align="center" valign="middle" style="text-align: right; vertical-align: middle;">
                <?php
                    $total_amount = $order_item->sale_price * $order_item->quantity;
                ?>
                <?= number_format($total_amount, 0,'.', ',')  ?>
            </td>

            <?php $total_price_item += $total_amount; ?>

        </tr>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php
    $discount = 0;
    if(!empty($order->voucher_id)) { //voucher
        if (strpos($order->voucher_discount_type, PERCENT_TYPE) !== false) { //percent
            $discount = $total_price_item * ((float)$order->voucher_discount_value / 100.0);

            if (!empty($order->voucher_discount_max_value) && $order->voucher_discount_max_value > 0 && $discount > $order->voucher_discount_max_value) {
                $discount = $order->voucher_discount_max_value;
            }
        } else { //price
            $discount = $order->voucher_discount_value;
        }
    }
    ?>

    <tr>
        <td align="center" valign="middle" style="text-align: left; vertical-align: middle; border-right: 0; border-bottom: 0" colspan="5">
            Tổng tiền
        </td>
        <td style="text-align: right; vertical-align: middle; border-left: 0; border-bottom: 0">
            <?= number_format($total_price_item, 0,'.', ',')  ?>
        </td>
        <!-- end check voucher type -->
    </tr>
    <tr>
        <td align="center" valign="middle" style="text-align: left; vertical-align: middle; border-right: 0; border-bottom: 0; border-top: 0" colspan="5">
            Giảm giá
        </td>
        <td style="text-align: right; vertical-align: middle; border-left: 0; border-bottom: 0; border-top: 0">

            <?= !empty(intval($discount)) ? number_format($discount, 0,'.', ',') : '' ?>
        </td>
    </tr>
    <tr>
        <td align="center" valign="middle" style="text-align: left; vertical-align: middle; border-right: 0; border-bottom: 0; border-top: 0" colspan="5">
            Giá ship
        </td>
        <td style="text-align: right; vertical-align: middle; border-left: 0; border-bottom: 0; border-top: 0">
            <?= !empty(intval($order->fee_ship)) ? number_format($order->fee_ship, 0,'.', ',') : '' ?>
        </td>
    </tr>
    <tr>
        <td align="center" valign="middle" style="text-align: left; vertical-align: middle; border-right: 0; border-top: 0" colspan="5">
            Tổng thanh toán
        </td>
        <td align="center" valign="middle" style="text-align: right; vertical-align: middle; border-left: 0; border-top: 0">
            <?= number_format($order->total_price, 0,'.', ',') ?>
        </td>
    </tr>

    </tbody>
</table>
    <br>
<table border="0">
    <tr>
        <td colspan="2" style="text-align: center; vertical-align: middle; font-weight: bold">Xác nhận thanh toán</td>
        <td colspan="4" style="text-align: center; vertical-align: middle; font-weight: bold">Người giao hàng</td>
    </tr>
</table>
<?php
$file = date('Ymd') .'_' . "chi_tiet_hoa_don.xls" ;
header("Content-type: application/x-www-form-urlencoded\r\n" );
header('Content-Disposition: attachment; filename=' . $file );
?>