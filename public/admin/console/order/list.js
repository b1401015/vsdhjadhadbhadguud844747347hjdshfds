


if ($("#example1").length) {
    var table = $('#example1').DataTable({
        "language": {
            "sEmptyTable": "Không tìm thấy dữ liệu",
            "sSearch": "Tìm kiếm:",
            "sZeroRecords": "Không tìm thấy dữ liệu này",
            "sLengthMenu": "Xem _MENU_ dòng / trang",
            "sInfo": "Đang xem từ _START_ đến _END_ trong tổng số _TOTAL_ dòng",
            "sInfoEmpty": "Hiển thị từ 0 đến 0 của 0 dòng",
            "sInfoFiltered": "(Lọc từ tổng _MAX_ dòng)",
            "oPaginate": {
                "sFirst": "Trang đầu",
                "sPrevious": "Trang trước",
                "sNext": "Trang tiếp",
                "sLast": "Trang cuối"
            }
        },
        "processing": true,
        "serverSide": true,
        "searching": true,
        "lengthChange": false,
        "ordering": false,
        "ajax": {
            url : CONSTANT.API.GET_ORDER_LIST,
            type : "post",
            data: {}
        },
        "columns": [
            {
                "class":          "details-control",
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { "data": null},
            { "data": "ref"},
            {
                "data": "status",
                "render": function (data, type, row, meta) {
                    var $status_wish_selected = '',
                        $status_ordered_selected = '',
                        $status_confirmation_selected = '',
                        $status_checkout_selected = '',
                        $status_delivery_selected = '',
                        $status_validation_selected = '',
                        $status_return_selected = '',
                        $status_delay_selected = '',
                        $status_cancel_selected = '',
                        $disabled = '';

                    var data = data.toUpperCase();

                    // if(data == CONSTANT.ORDER_STATUS_WISH){
                    //     $status_wish_selected = 'selected';
                    // }
                    if(data == CONSTANT.ORDER_STATUS_ORDERED){
                        $status_ordered_selected = 'selected';
                    }else if(data == CONSTANT.ORDER_STATUS_CONFIRMATION){
                        $status_confirmation_selected = 'selected';
                    }
                    else if(data == CONSTANT.ORDER_STATUS_CHECKOUT){
                        $status_checkout_selected = 'selected';
                    }
                    else if(data == CONSTANT.ORDER_STATUS_DELIVERY){
                        $status_delivery_selected = 'selected';
                    }
                    else if(data == CONSTANT.ORDER_STATUS_VALIDATION){
                        $status_validation_selected = 'selected';
                    }
                    else if(data == CONSTANT.ORDER_STATUS_RETURN){
                        $status_return_selected = 'selected';
                    }
                    else if(data == CONSTANT.ORDER_STATUS_DELAY){
                        $status_delay_selected = 'selected';
                    }else if(data == CONSTANT.ORDER_STATUS_CANCEL){
                        $status_cancel_selected = 'selected';
                    }

                    return '<div class="btn-group open">' +
                        '<select ' + $disabled + ' class="select2 status-item">' +
                        // '<option ' + $status_wish_selected + ' value="' + CONSTANT.ORDER_STATUS_WISH + '">Bỏ giỏ hàng</option>' +
                        '<option ' + $status_ordered_selected + ' value="' + CONSTANT.ORDER_STATUS_ORDERED + '">Vừa đặt</option>' +
                        '<option ' + $status_confirmation_selected + ' value="' + CONSTANT.ORDER_STATUS_CONFIRMATION + '">Đã xác nhận</option>' +
                        '<option ' + $status_checkout_selected + ' value="' + CONSTANT.ORDER_STATUS_CHECKOUT + '">Đã thanh toán</option>' +
                        '<option ' + $status_delivery_selected + ' value="' + CONSTANT.ORDER_STATUS_DELIVERY + '">Đã giao hàng</option>' +
                        '<option ' + $status_validation_selected + ' value="' + CONSTANT.ORDER_STATUS_VALIDATION + '">Đã hoàn tất</option>' +
                        '<option ' + $status_return_selected + ' value="' + CONSTANT.ORDER_STATUS_RETURN + '">Trả lại</option>' +
                        '<option ' + $status_delay_selected + ' value="' + CONSTANT.ORDER_STATUS_DELAY + '">Bị hoãn</option>' +
                        '<option ' + $status_cancel_selected + ' value="' + CONSTANT.ORDER_STATUS_CANCEL + '">Hủy</option>' +
                        '</select>' +
                        '</div>';
                }
            },
            { "data": "name" },
            { "data": "phone" },
            { "data": "email" },
            { "data": "number_item" },
            {
                "data": null,
                "render": function ( data, type, row, meta ) {
                    return number_format(row.total_price, 0, ',', '.');
                }
            },
            { "data": "fee_ship" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var discount = '';
                    if(!isEmpty(row.voucher_id)) {
                        discount = number_format(row.voucher_discount_value, 0, '.', ',');
                        var type = row.voucher_discount_type;
                        if (type.indexOf(CONSTANT.PERCENT_TYPE) >= 0) {
                            discount += ' %';
                        }
                    }
                    return discount;
                }
            },
            { "data": "create_time" },
            { "data": "update_time" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return '<div class="btn-group"><a href="'+ baseURL +'_admin/order/export_order_detail/' + row._id + '" class="btn btn-primary"><i class="fa fa-file-excel-o"></i></a><a href="'+ baseURL +'_admin/order/edit_order/' + row._id + '" class="btn btn-success"><i class="fa fa-fw fa-pencil-square-o"></i></a><a href="javascript:void(0)" class="btn btn-danger delete-item"><i class="fa fa-fw fa-trash-o"></i></a></div>';
                }
            },
        ], //end columns
        "createdRow": function ( row, data, index ) {
            $('td', row).eq(1).html(index + 1);
            for(var i = 0; i <= index; i++) {
                $('td', row).eq(i).parent().addClass('tr_data').attr('data-id', data._id).attr('data-src', JSON.stringify(data));
            }
        }
    });

    // Add event listener for change is_active
    $('#example1 tbody').on('change', '.is-active-item', function () {
        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');
        var $is_active = 0;
        if ($this.is(':checked')) {
            $is_active = 1;
        }

        var $url = '/_api/admin/order/order_toggle_is_active/' + $id;

        $.ajax({
            url: $url,
            type: 'PUT',
            cache: false,
            dataType: "json",
            data: {id: $id, is_active: $is_active},
            success: function (res) {
                if (res.message == CONSTANT.OK_CODE) {
                }
                else {
                    alert(res.message);
                }
            },
            error: function () {
                alert('Vui lòng thử lại !');
            }
        });
    });

    // Add event listener for opening and closing details
    $('#example1 tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        // console.log(tr.attr('data-src'));
        var $data = $.parseJSON(tr.attr('data-src'));

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child(format($data)).show();
            tr.addClass('shown');
        }
    });

    // Add event listener for change is_active
    $('#example1 tbody').on('click', '.delete-item', function () {

        if (!confirm('Bạn có chắc muốn xóa?')) {
            return false;
        }

        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');

        var $url = '/_api/admin/order/delete_order/' + $id;

        $.ajax({
            url: $url,
            type: 'PUT',
            cache: false,
            dataType: "json",
            data: {id: $id, is_delete: 1},
            success: function (res) {
                if (res.message == CONSTANT.OK_CODE) {
                    $tr.remove();
                    table.row($tr)
                        .remove()
                        .draw();
                }
                else {
                    alert(res.message);
                }
            },
            error: function () {
                alert('Vui lòng thử lại !');
            }
        });
    });

    // Add event listener for changing order status
    $('#example1 tbody').on('change', '.status-item', function () {
        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');
        var $status = $this.val();

        // console.log($id);
        // console.log($deliver_id);

        var $html = '';
        var cond = {
            _id: $id
        };

        var $url = CONSTANT.API.ORDER_CHANGE_STATUS + '/' + $id;

        $.ajax({
            url: $url,
            type: 'PUT',
            cache: false,
            dataType: "json",
            data: {status: $status},
            success: function (res) {
                if (res.message == CONSTANT.OK_CODE) {
                    if($status == CONSTANT.ORDER_STATUS_PAYMENT){
                        location.reload();
                    }
                }
                else {
                    alert(res.message);
                }
            },
            error: function () {
                console.log('error');
            }
        });
    });

}


/*
 *  upload excel file
 */
function upload_excel_file(){
    if (submitting){
        return;
    }
    var formData = new FormData($("#frm_upload")[0]);
    // Use Ajax to submit form data
    var url ='/_api/admin/record/import_excel';
    submitting = true;
    $('#txt_upload_status').text('Vui lòng chờ...').removeClass('hide');
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        async: false,
        success: function (res) {
            submitting = false;
            if (res.status == CONSTANT.HTTP.SUCCESS){
                $('#txt_upload_status').text('Tải lên thành công...').removeClass('hide');
                //refresh the page
                location.reload();
            } else {
                $('#txt_upload_status').text(res.message).removeClass('hide');
            }
        },
        error: function (e) {
            $('#txt_upload_status').text('Vui lòng thử lại').removeClass('hide');
            console.log('err', e);
            submitting = false;
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

/*
 *  display frm upload show/hide
 */
function toggle_frm_upload(){
    var $frm = $('#upload_container');
    if($frm.hasClass('hide')){
        $frm.removeClass('hide');
    }
    else{
        $frm.addClass('hide');
    }
}

function format ( d ) {
    // `d` is the original data object for the row
    console.log(d);


    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
        '<td><strong>Thông tin khác</strong></td>'+
        '</tr>'+
        '<tr>'+
        '<td>Ghi chú: </td>'+
        '<td>'+ d.note +'</td>'+
        '</tr>'+
        '<tr>'+
        '<td>Nơi giao hàng: </td>'+
        '<td>'+ d.order_location +'</td>'+
        '</tr>'+
        '</table>';
}