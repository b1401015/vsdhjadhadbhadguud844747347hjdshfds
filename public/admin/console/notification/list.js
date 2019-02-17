

if ($("#example1").length) {
    var table = $('#example1').DataTable({
        // "language": {
        //     "sEmptyTable": "Không tìm thấy dữ liệu",
        //     "sSearch": "Tìm kiếm:",
        //     "sZeroRecords": "Không tìm thấy dữ liệu này",
        //     "sLengthMenu": "Xem _MENU_ dòng / trang",
        //     "sInfo": "Đang xem từ _START_ đến _END_ trong tổng số _TOTAL_ dòng",
        //     "sInfoEmpty": "Hiển thị từ 0 đến 0 của 0 dòng",
        //     "sInfoFiltered": "(Lọc từ tổng _MAX_ dòng)",
        //     "oPaginate": {
        //         "sFirst": "Trang đầu",
        //         "sPrevious": "Trang trước",
        //         "sNext": "Trang tiếp",
        //         "sLast": "Trang cuối"
        //     }
        // },
        "ordering": false,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "lengthChange": false,
        "ajax": {
            url : CONSTANT.API.GET_NOTIFICATION_LIST,
            type : "post",
            data: {}
        },
        "columns": [
            { "data": null },
            { "data": "_title" },
            { "data": "_content" },
            { "data": "screen" },
            {
                "data": "account_id",
                "render": function (data, type, row, meta) {
                    var $html = '';
                    if(row.is_send_all == 1){
                        $html = 'All users';
                    }
                    else{
                        $html = '[ID: ' + row.account_id + '] ' + row.fullname;
                    }
                    return $html;
                }
            },
            {
                "data": "push_time",
                "render": function (data, type, row, meta) {
                    var $html = data;
                    if(data == "0000-00-00 00:00:00" || isEmpty(data)){
                        $html = '';
                    }
                    return $html;
                }

            },
            {
                "data": "is_done",
                "render": function (data, type, row, meta) {
                    var $html = '';
                    if(data == 1){
                        $html = '<img width="30" src="' + baseURL + '/public/img/checked.png" />';
                    }
                    return $html;
                    // <a href="javascript:void(0)" class="btn btn-danger delete-item"><i class="fa fa-fw fa-trash-o"></i></a></div>';
                }
            },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return formatdmY_His(row.create_time);
                }
            },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return formatdmY_His(row.update_time);
                }
            },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    var $html = '';
                    if(row.is_done == 0) {
                        $html = '<div class="btn-group"><a href="' + baseURL + '_admin/notification/edit/' + row._id + '" class="btn btn-success"><i class="fa fa-fw fa-pencil-square-o"></i></a>';
                        // <a href="javascript:void(0)" class="btn btn-danger delete-item"><i class="fa fa-fw fa-trash-o"></i></a></div>';
                    }
                    return $html;
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

    // Add event listener for delete item
    $('#example1 tbody').on('click', '.delete-item', function () {
        if (!confirm('Are you sure to delete ?')) {
            return false;
        }
        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');

        var $url = CONSTANT.URI.DELETE_TRANSLATION + '/' + $id;

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
                alert('Please try again !');
            }
        });
    });
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

/*
 *  upload excel file
 */

var submitting = false;

function upload_excel_file() {
    if (submitting) {
        return;
    }
    var formData = new FormData($("#frm_upload")[0]);
    // Use Ajax to submit form data
    submitting = true;
    $('#txt_upload_status').text('Vui lòng chờ...').removeClass('hide');
    $.ajax({
        url: CONSTANT.URI.IMPORT_TRANSLATION,
        type: 'POST',
        data: formData,
        async: false,
        success: function (res) {
            submitting = false;
            if (res.status == CONSTANT.HTTP.SUCCESS) {
                $('#txt_upload_status').text('Upload successfully...').removeClass('hide');
                //refresh the page
                location.reload();
            } else {
                $('#txt_upload_status').text(res.message).removeClass('hide');
            }
        },
        error: function (e) {
            $('#txt_upload_status').text('Please try again !').removeClass('hide');
            console.log('err', e);
            submitting = false;
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

function toggle_frm_upload() {
    var $frm = $('#upload_container');
    if ($frm.hasClass('hide')) {
        $frm.removeClass('hide');
    }
    else {
        $frm.addClass('hide');
    }
}