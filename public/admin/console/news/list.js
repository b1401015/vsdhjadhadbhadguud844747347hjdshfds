
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
            url : CONSTANT.API.GET_NEWS_LIST,
            type : "post",
            data: {}
        },
        "columns": [
            { "data": null},
            { "data": "title"},
            { "data": "_key" },
            {
                "data": "img_src",
                "render": function ( data, type, row, meta ) {
                    return '<a href="' + baseURL + data + '" data-lightbox="image-1" data-title="' + row.title + '"><img width="50" src="' + baseURL + data + '" /></a>';
                }
            },
            // { "data": "link_url" },
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
                    return '<div class="btn-group"><a href="'+ baseURL +'_admin/news/edit_news/' + row._id + '" class="btn btn-success"><i class="fa fa-fw fa-pencil-square-o"></i></a><a href="javascript:void(0)" class="btn btn-danger delete-item"><i class="fa fa-fw fa-trash-o"></i></a></div>';
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

    // Add event listener for change is_active
    $('#example1 tbody').on('change', '.is-active-item', function () {
        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');
        var $is_active = 0;
        if ($this.is(':checked')) {
            $is_active = 1;
        }

        var $url = '/_api/admin/news/news_toggle_is_active/' + $id;

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

    // Add event listener for load list delivers
    $('#example1 tbody').on('click', '.select-deliver:not(.ajax)', function () {
        var $this = $(this);

        var $html = '';
        var cond = {
            get_all: CONSTANT.GET_ALL_KEY,
            role: 'DELIVER'
        };

        var uri = "EsmAdmin/user/get_all_users_by_role";
        common.ajaxPost(uri, cond, function (resp) {
            var resps = $.parseJSON(resp);
            if (resps.result == CONSTANT.OK_CODE) {
                // console.log(resps.data);
                for (var i = 0; i < resps.data.length; i++) {
                    var loop = resps.data[i];
                    $html += '<option value="' + loop._id + '">' + loop.last_name + ' ' + loop.first_name + '</option>';
                }

                $this.addClass('select2 ajax').append($html);
                $('.select2').select2();
            }
        });

    });

    // Add event listener for change is_active
    $('#example1 tbody').on('click', '.delete-item', function () {

        if (!confirm('Bạn có chắc muốn xóa?')) {
            return false;
        }

        var $this = $(this);
        var $tr = $this.closest('tr');
        var $id = $tr.attr('data-id');

        var $url = '/_api/admin/news/delete_news/' + $id;

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