//BootstrapValidator
var SERVER_URI = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + '';

$(function () {
    //form validation
    $("#frm_edit").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            title: {
                validators: {
                    notEmpty: {
                        message: 'Tên sản phẩm không được để trống!'
                    },
                }
            },
            'category_id[]': {
                validators: {
                    notEmpty: {
                        message: 'Vui chọn danh mục!'
                    }

                }
            }
        }
    }).on('success.form.bv', function(e) {
        // Prevent form submission
        e.preventDefault();

        $('#btn_edit').button('loading');
        // Get the form instance
        var $form = $(e.target);
        var formData = new FormData($("#frm_edit")[0]);
        formData = new FormData($(this)[0]);
        // Get the BootstrapValidator instance
        var bv = $form.data('bootstrapValidator');
        // Use Ajax to submit form data
        var url = '/_api/admin/record/update_record';
        // formData.append('content_product', CKEDITOR.instances['write_content'].getData());
        formData.append('description', CKEDITOR.instances['description'].getData());
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (res) {
                if(res.message == 'OK'){
                    alert("Cập nhật thành công!");
                    window.location = '/_admin/record/listing';
                }
                else{
                    alert(res.message);
                }
                $('#btn_edit').button('reset');
            },
            error: function (err) {
                if (err.responseJSON.message == CONSTANT.POINT_CHECK) {
                    alert('Đánh giá phải lớn hơn hoặc bằng 0 hoặc nhỏ hơn 5!');
                }else {
                    alert('Vui lòng thử lại !');
                }
                $('#btn_edit').button('reset');
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }); //end bootstrapValidator

    $('#btn_edit').click(function() {
        $('#frm_edit').bootstrapValidator('validate');
    });

    function loadImage(input) {
        /*
         * Todo: check file size when upload
         */
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                var img = '<img width="100px" src="' + e.target.result + '" />';
                $('#img_preview').html(img);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#img_src").change(function() {
        loadImage(this);
    });
});
// In your Javascript (external .js resource or <script> tag)
$(document).ready(function() {
    $('.select2').select2();
});