//BootstrapValidator
var SERVER_URI = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + '';

$(function () {
    //form validation
    $("#frm_add").bootstrapValidator({
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
                        message: 'Không được để trống!'
                    },
                }
            },
            img_src: {
                validators: {
                    file: {
                        extension: 'gif,jpeg,jpg,png,svg,svgz,webp',
                        type: 'image/gif,image/jpeg,image/png,image/svg+xml,image/webp',
                        maxSize: 2*1024*1024,
                        message: 'Vui lòng chọn file là hình ảnh và có kích thước nhỏ hơn 2MB!'
                    },
                    notEmpty: {
                        message: 'Vui lòng thêm file hình ảnh!'
                    }

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

        if(submitting){
            return;
        }

        submitting = true;

        $('#btn_add').html('Đang xử lý');
        // Get the form instance
        var $form = $(e.target);
        var formData = new FormData($("#frm_add")[0]);
        formData = new FormData($(this)[0]);
        // Get the BootstrapValidator instance
        var bv = $form.data('bootstrapValidator');
        // Use Ajax to submit form data
        var url = CONSTANT.API.ADD_NEW_RECORD;
        formData.append('description', CKEDITOR.instances['description'].getData());
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (res) {
                $('#btn_add').html('Thêm');
                submitting = false;
                if(res.message == CONSTANT.OK_CODE){
                    alert("Thêm mới thành công!");
                    window.location.href = CONSTANT.URI.GET_RECORD_LIST;
                }
                else{
                    alert(res.message);
                }
            },
            error: function (err) {
                if (err.responseJSON.message == CONSTANT.POINT_CHECK) {
                    alert('Đánh giá phải lớn hơn hoặc bằng 1 hoặc nhỏ hơn 5!');
                }else {
                    alert('Vui lòng thử lại !');
                }

                $('#btn_add').html('Thêm');
                submitting = false;
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }); //end bootstrapValidator

    $('#btn_add').click(function() {
        $('#frm_add').bootstrapValidator('validate');
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
