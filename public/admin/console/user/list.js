

if ($("#example1").length) {
    var table = $('#example1').DataTable({
        "language": {
            "sEmptyTable": "Không tìm thấy dữ liệu",
            "sSearch": "Tìm kiếm:",
            "sZeroRecords": "Không tìm thấy dữ liệu này",
            "sLengthMenu": "Xem _MENU_ user / trang",
            "sInfo": "Đang xem từ _START_ đến _END_ trong tổng số _TOTAL_ user",
            "sInfoEmpty": "Hiển thị từ 0 đến 0 của 0 user",
            "sInfoFiltered": "(Lọc từ tổng _MAX_ user)",
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
            url : CONSTANT.API.GET_USER_LIST,
            type : "post",
            data: {}
        },
        "drawCallback": function(res) {
            var data_ = res.json;
            $('#total_user').html(data_.recordsTotal);
        },
        "columns": [
            {
                "class":          "details-control",
                "orderable":      false,
                "data":           null,
                "defaultContent": ""
            },
            { "data": null },
            {
                "data": "social_type",
                "render": function ( data, type, row, meta ) {
                    var register_type = 'Truyền thống';
                    if(!isEmpty(row.social_type)){
                        register_type = row.social_type;
                    }
                    return ucfirst(register_type);
                }
            },
            { "data": "fullname" },
            {
                "data": null,
                "render": function ( data, type, row, meta ) {
                    var avatar = row.img_src;
                    if(isEmpty(avatar)){
                        avatar = row.social_img_src;
                    }
                    return '<a href="' + baseURL + avatar + '" data-lightbox="image-1" data-title="' + row.title + '"><img width="50" src="' + baseURL + avatar + '" /></a>';

                }
            },
            { "data": "username" },
            { "data": "email" },
            { "data": "gender" },
            { "data": "status" },
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
            { "data": "create_time" },
            { "data": "update_time" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return '<div class="btn-group"><a href="'+ baseURL +'_admin/user/edit_user/' + row._id + '" class="btn btn-success"><i class="fa fa-fw fa-pencil-square-o"></i></a><a href="javascript:void(0)" class="btn btn-danger delete-item"><i class="fa fa-fw fa-trash-o"></i></a></div>';
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

        var $url = '/_api/admin/user/account_toggle_is_active/' + $id;

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

        var $url = '/_api/admin/user/delete_user/' + $id;

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

function format ( d ) {
    // `d` is the original data object for the row
    console.log(d);

    var last_login_ = '';
    if(!isEmpty(d.last_login)){
        last_login_ = d.last_login;
    }

    var fullname_ = '';
    if(!isEmpty(d.fullname)){
        fullname_ = d.fullname;
    }

    var phone_ = '';
    if(!isEmpty(d.phone)){
        phone_ = d.phone;
    }

    var last_login_ = '';
    if(!isEmpty(d.last_login)){
        last_login_ = d.last_login;
    }

    var birthday_ = '';
    if(!isEmpty(d.birthday)){
        birthday_ = d.birthday;
    }

    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
        '<td><strong>Thông tin khác</strong></td>'+
        '</tr>'+
        '<tr>'+
        '<td>Lần đăng nhập cuối: </td>'+
        '<td>'+ last_login_ +'</td>'+
        '</tr>'+
        '<tr>'+
        '<td>SĐT: </td>'+
        '<td>'+ phone_ +'</td>'+
        '</tr>'+
        '<td>Ngày sinh: </td>'+
        '<td>'+ birthday_ +'</td>'+
        '</tr>'+
        '</table>';
}

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