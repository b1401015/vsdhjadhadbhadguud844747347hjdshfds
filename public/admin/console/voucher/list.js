

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
        "ajax": {
            url : CONSTANT.API.GET_VOUCHER_LIST,
            type : "post",
            data: {}
        },
        "columns": [
            { "data": null },
            { "data": "code" },
            { "data": "name" },
            // { "data": "type" },
            // {
            //     "data": "img_src",
            //     "render": function ( data, type, row, meta ) {

            //         return '<a href="' + baseURL + data + '" data-lightbox="image-1" data-title="' + row.title + '"><img width="50" src="' + baseURL + data + '" /></a>';
            //     }
            // },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return formatdmY(row.time_start) + ' - ' + formatdmY(row.time_end);
                }
            },
            {
                "data": "_value",
                "render": function ( data, type, row, meta ) {
                    if(row.type.indexOf(CONSTANT.PERCENT_TYPE) != -1){
                        return number_format(data, 0, ',', '.') + '%';
                    }else if(row.type.indexOf(CONSTANT.PRICE_TYPE) != -1){
                        return number_format(data, 0, ',', '.') + ' VND';
                    }
                }
            },
            {
                "data": "max_value",
                "render": function ( data, type, row, meta ) {
                    if(!isEmpty(row.max_value)){
                        return number_format(data, 0, ',', '.');
                    }
                }
            },
            {"data": "stock"},
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
            // {
            //     "data": "is_combined",
            //     "render": function ( data, type, row, meta ) {
            //         var checked = '';
            //         if(data == 1){
            //             checked = 'checked';
            //         }
            //         return '<input ' + checked + ' type="checkbox" class="is-combined-item" />';
            //     }
            // },
            // {
            //     "data": "is_unlimited",
            //     "render": function ( data, type, row, meta ) {
            //         var checked = '';
            //         if(data == 1){
            //             checked = 'checked';
            //         }
            //         return '<input ' + checked + ' type="checkbox" class="is-unlimited-item" />';
            //     }
            // },
            { "data": "ordinal" },
            { "data": "update_time" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return '<div class="btn-group"><a href="'+ baseURL +'_admin/voucher/edit_voucher/' + row._id + '" class="btn btn-success"><i class="fa fa-fw fa-pencil-square-o"></i></a><a href="javascript:void(0)" class="btn btn-danger delete-item"><i class="fa fa-fw fa-trash-o"></i></a></div>';
                }
            },
        ], //end columns
        "createdRow": function ( row, data, index ) {
            $('td', row).eq(0).html(index + 1);
            for(var i = 0; i <= index; i++) {
                $('td', row).eq(i).parent().addClass('tr_data').attr('data-id', data._id).attr('data-src', JSON.stringify(data));
            }
        }
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
    $('#example1 tbody').on('change', '.is-active-item', function () {
        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');
        var $is_active = 0;
        if ($this.is(':checked')) {
            $is_active = 1;
        }

        var $url = '/_api/admin/voucher/voucher_toggle_is_active/' + $id;

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

    // Add event listener for delete item
    $('#example1 tbody').on('click', '.delete-item', function () {
        if (!confirm('Bạn chắc chắn muốn xóa ?')) {
            return false;
        }
        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');

        var $url = '/_api/admin/voucher/delete_voucher/' + $id;

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
}

// function format ( d ) {
//     // `d` is the original data object for the row
//     console.log(d);
//
//     // var last_login_ = '';
//     // if(!isEmpty(d.last_login)){
//     //     last_login_ = d.last_login;
//     // }
//     //
//     // var fullname_ = '';
//     // if(!isEmpty(d.fullname)){
//     //     fullname_ = d.fullname;
//     // }
//     //
//     // var phone_ = '';
//     // if(!isEmpty(d.phone)){
//     //     phone_ = d.phone;
//     // }
//     //
//     // var last_login_ = '';
//     // if(!isEmpty(d.last_login)){
//     //     last_login_ = d.last_login;
//     // }
//     //
//     // var birthday_ = '';
//     // if(!isEmpty(d.birthday)){
//     //     birthday_ = d.birthday;
//     // }
//
//     return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
//         '<tr>'+
//         '<td><strong>Thông tin khác</strong></td>'+
//         '</tr>'+
//         '<tr>'+
//         '<td>Lần đăng nhập cuối: </td>'+
//         '<td>'+ last_login_ +'</td>'+
//         '</tr>'+
//         '<tr>'+
//         '<td>SĐT: </td>'+
//         '<td>'+ phone_ +'</td>'+
//         '</tr>'+
//         '<td>Ngày sinh: </td>'+
//         '<td>'+ birthday_ +'</td>'+
//         '</tr>'+
//         '</table>';
// }

// $('#example1 tbody').on('click', '.status-check', function () {
//     var status = ($(this).hasClass("btn-success")) ? '0' : '1';
//     var msg = (status=='0')? 'Hide' : 'Show';
//     if(confirm("Are you sure to "+ msg)) {
//         var current_element = $(this);
//         var id = $(current_element).attr('data');
//         $.ajax({
//             type: "POST",
//             url: "/user_manager/update_status",
//             data: {"id": id, "status": status},
//             success: function (data) {
//                 location.reload();
//             }
//         });
//     }
// });