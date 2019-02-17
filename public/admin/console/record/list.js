

if ($("#example1").length) {
    var category_val=$("#category_val").val();
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
        "ajax": {
            url : CONSTANT.API.GET_RECORD_LIST,
            type : "post",
            data: {category_val:category_val}
        },
        "columns": [
            {
                "class":          "details-control",
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { "data": null },
            { "data": "code" },
            { "data": "title" },
            {
                "data": "img_src",
                "render": function ( data, type, row, meta ) {
                    return '<a href="' + baseURL + data + '" data-lightbox="image-1" data-title="' + row.title + '"><img width="50" src="' + baseURL + data + '" /></a>';
                }
            },
            {
                "data": "ordinal",
                "render": function ( data, type, row, meta ) {
                    return '<input class="ordinal-item" style="width:50px;" type="number" value="'+ row.ordinal +'" min="1" />';
                }
            },
            { "data": "count_cate",
                "render": function (data, type, row, meta) {
                var a='';
                if (data == 0){
                        a='<a href="#"    >' + data + '</a>';
                    } else {
                    a='<a href="#" class="detail_cate" data-toggle="modal" data-target="#myModal"  >' + data + '</a>';
                }
                    return a;/**/
                }
                },
            {
                "data": null,
                "render": function ( data, type, row, meta ) {
                    var html = number_format(row.sale_price, 0, ',', '.');
                    if(parseFloat(row.sale_price) < parseFloat(row.price)){
                        html = '<del>' + number_format(row.price, 0, ',', '.') + '</del><br/>' + ' ' + number_format(row.sale_price, 0, ',', '.');
                    }
                    return html;
                }
            },
            {
                "data": "is_featured",
                "render": function ( data, type, row, meta ) {
                    var checked = '';
                    if(data == 1){
                        checked = 'checked';
                    }
                    return '<input ' + checked + ' type="checkbox" class="is-featured-item" />';
                }
            },
            {
                "data": "is_active",
                "render": function ( data, type, row, meta ) {
                    var checked = '';
                    if(data == 1){
                        checked = 'checked';
                    }
                    return '<input ' + checked + ' type="checkbox" class="is-active-item" />';
                }
            },
            // { "data": "create_time" },
            { "data": "update_time" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return '<div class="btn-group"><a href="'+ baseURL +'_admin/record/gallery/' + row._id + '" class="btn btn-warning"><i class="fa fa-fw fa-picture-o"></i></a><a href="'+ baseURL +'_admin/record/edit_record/' + row._id + '" class="btn btn-success"><i class="fa fa-fw fa-pencil-square-o"></i></a>' +
                        // '<a href="javascript:void(0)" class="btn btn-danger delete-item"><i class="fa fa-fw fa-trash-o"></i></a>' +
                        '</div>';
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

    // var table = $('#example1').DataTable({
    //     "language": {
    //         "sEmptyTable": "Không tìm thấy dữ liệu",
    //         "sSearch": "Tìm kiếm:",
    //         "sZeroRecords": "Không tìm thấy dữ liệu này",
    //         "sLengthMenu": "Xem _MENU_ dòng / trang",
    //         "sInfo": "Đang xem từ _START_ đến _END_ trong tổng số _TOTAL_ dòng",
    //         "sInfoEmpty": "Hiển thị từ 0 đến 0 của 0 dòng",
    //         "sInfoFiltered": "(Lọc từ tổng _MAX_ dòng)",
    //         "oPaginate": {
    //             "sFirst": "Trang đầu",
    //             "sPrevious": "Trang trước",
    //             "sNext": "Trang tiếp",
    //             "sLast": "Trang cuối"
    //         }
    //     },
    //     "columnDefs": [{
    //         "targets": 0,
    //         "data": null,
    //         "orderable": false,
    //         "className": 'details-control',
    //         "defaultContent": ''
    //     }],
    //     "fnDrawCallback": function (oSettings) {
    //         // common.initCheckbox();
    //
    //
    //     }
    // });

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
    $('#example1 tbody').on('change', '.is-active-item', function () {
        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');
        var $is_active = 0;
        if ($this.is(':checked')) {
            $is_active = 1;
        }

        var $url = '/_api/admin/record/record_toggle_is_active/' + $id;

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

    // Add event listener for click is_feaured
    $('#example1 tbody').on('change', '.is-featured-item', function () {
        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');
        var $is_featured = 0;
        if ($this.is(':checked')) {
            $is_featured = 1;
        }

        var $url = '/_api/admin/record/record_toggle_is_featured/' + $id;
        $.ajax({
            url: $url,
            type: 'PUT',
            cache: false,
            dataType: "json",
            data: {id: $id, is_featured: $is_featured},
            success: function (res) {
                console.log(res);
            },
            error: function () {
                console.log('error');
            }
        });
    });

    // Add event listener for change is_active
    $('#example1 tbody').on('click', '.delete-item', function () {

        if(submitting){
            return;
        }
        submitting = true;

        if (!confirm('Bạn có chắc muốn xóa?')) {
            return false;
        }

        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');

        var $url = '/_api/admin/record/delete_record/' + $id;

        $.ajax({
            url: $url,
            type: 'PUT',
            cache: false,
            dataType: "json",
            data: {id: $id, is_delete: 1},
            success: function (res) {
                if (res.message == CONSTANT.OK_CODE) {
                    $tr.remove();
                    // table.row($tr)
                    //     .remove()
                    //     .draw();
                }
                else {
                    alert(res.message);
                }
                submitting = false;
            },
            error: function () {
                alert('Vui lòng thử lại !');
                submitting = false;
            }
        });
    });

    // Add event listener for change ordinal item
    $('#example1 tbody').on('keyup mouseup', 'input.ordinal-item', function () {
        // $('input.ordinal-item').bind('keyup mouseup', function () {
        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');
        var $ordinal = $this.val();

        var $url = '/_api/admin/record/update_ordinal';

        $.ajax({
            url: $url,
            type: 'POST',
            cache: false,
            dataType: "json",
            data: {record_id: $id, ordinal: $ordinal},
            success: function (res) {
                if(res.message == CONSTANT.OK_CODE){
                }
                else{
                    alert(res.message);
                }
            },
            error: function () {
                alert('Vui lòng thử lại !');
            }
        });
    });

    //detail account
    $('#example1 tbody').on('click', '.detail_cate', function () {
        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');
        $.ajax({
            url: CONSTANT.API.DETAIL_CATEGORY_RECORD_LIST + $id,
            type: 'PUT',
            cache: false,
            dataType: "json",
            data: {id: $id},
            success: function (res) {
                if (res.message == CONSTANT.OK_CODE) {
                    var position = res.data;
                    // alert(position);
                    // alert(position);
                    //alert(position);
                    var html = '<table class="table table-bordered table-striped">' +
                        '                        <thead>' +
                        '                        <tr>' +
                        '                        <th class="text-center">#</th>' +
                        '                        <th class="text-center">Tên danh mục</th>' +
                        '                    </tr>' +
                        '                    </thead><tbody>';

                    var $loop = 1;

                    $.each(position, function (k, v) {
                        html += '<tr>' +
                            '                    <td class="text-center">' + $loop++ + '</td>' +
                            '                    <td class="text-center">' + v + '</td>' +
                            '                    </tr>';
                    });
                    html += '</tbody></table>';
                    $('#modal_content_account').html(html);


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

}

function format(d) {
// `d` is the original data object for the row

    var barcode_ = '';
    if (!isEmpty(d.barcode)) {
        barcode_ = d.barcode;
    }

    var number_item_ = '';
    if (!isEmpty(d.number_item)) {
        number_item_ = d.number_item;
    }

    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
        '<tr>' +
        '<td><strong>Thông tin khác</strong></td>' +
        '</tr>' +
        '<tr>' +
        '<td>Từ khóa dịch thuật:</td>' +
        '<td>' + d._key + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td>Barcode:</td>' +
        '<td>' + barcode_ + '</td>' +
        '</tr>' +
        '<tr>' +
        '<td>Mô tả:</td>' +
        '<td>' + d._description + '</td>' +
        '</tr>' +
        '</table>';
}

/*
 *  upload excel file
 */
function upload_excel_file() {
    if (submitting) {
        return;
    }
    var formData = new FormData($("#frm_upload")[0]);
    // Use Ajax to submit form data
    var url = '/_api/admin/record/import_excel';
    submitting = true;
    $('#txt_upload_status').text('Vui lòng chờ...').removeClass('hide');
    $('#import_err').html('');
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        async: false,
        success: function (res) {
            submitting = false;
            if (res.status == CONSTANT.HTTP.SUCCESS) {
                $('#txt_upload_status').text('Tải lên thành công...').removeClass('hide');
                //refresh the page
                alert('Tải lên thành công. Bạn đã thêm ' + res.data.count_insert + ' và cập nhật ' + res.data.count_update + ' sản phẩm')
                location.reload();
            } else {
                $('#txt_upload_status').text(res.message).removeClass('hide');
            }
        },
        error: function (e) {
            $('#txt_upload_status').text('').removeClass('hide');
            // console.log('err', e.responseJSON);
            if(e.responseJSON){
                var err_msg = JSON.parse(e.responseJSON.message);
                var err_data = "<br/>";
                if(!isEmpty(err_msg)) {
                    for(var propertyName in err_msg) {
                        // propertyName is what you want
                        // you can get the value like this: myObject[propertyName]
                        err_data += "Dòng " + propertyName +": " + err_msg[propertyName]+ "<br/>";
                    }
                }
            }else{
                dlog(e);
            }


            $('#import_err').html(err_data);
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
function toggle_frm_upload() {
    var $frm = $('#upload_container');
    if ($frm.hasClass('hide')) {
        $frm.removeClass('hide');
    }
    else {
        $frm.addClass('hide');
    }
}

// funtion onchage option
function handleSelect(elm)
{
    window.location = elm.value;
}