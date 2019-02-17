$('#fileupload').fileupload({
    maxNumberOfFiles: 10
});

$(function() {
    $('.photo-delete').click(function () {
        var confrm = confirm("Bạn có chắc chắn muốn xóa?");
        if (confrm) {
            var $this = $(this);
            var $id = $this.attr('data-id');

            $.ajax({
                url: CONSTANT.API.DELETE_RECORD_FILE,
                type: 'DELETE',
                dataType: 'json',
                cache: false,
                data: {'record_file_id': $id},
                success: function (res) {
                    if (res.message == CONSTANT.OK_CODE) {
                        $this.parent().parent().remove();
                    }
                    else {
                        alert(res.message);
                    }
                },
                error: function (err) {
                  alert('Vui lòng thử lại!');
                }
            });
        }
    });
}); /* $(function(){ */