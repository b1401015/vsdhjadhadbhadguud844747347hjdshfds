//BootstrapValidator

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
                        message: 'Tên tiêu đề không được để trống!'
                    },
                    stringLength: {
                        max: 191,
                        message: 'Tên tiêu đề quá dài!'
                    }
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
            }
        }
    }).on('success.form.bv', function(e) {
        // Prevent form submission
        e.preventDefault();

        $('#btn_add').button('loading');
        // Get the form instance
        var $form = $(e.target);
        var formData = new FormData($("#frm_add")[0]);
        formData = new FormData($(this)[0]);
        // Get the BootstrapValidator instance
        var bv = $form.data('bootstrapValidator');
        // Use Ajax to submit form data
        var url = '/_api/admin/news/add_news';
        formData.append('_description', CKEDITOR.instances['content_item'].getData());
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (res) {
                if(res.message == 'OK'){
                    alert("Thêm mới thành công!");
                    window.location = '/_admin/news/listing';
                }
                else{
                    alert(res.message);
                }
                $('#btn_add').button('reset');
            },
            error: function () {
                alert('Vui lòng thử lại !');
                $('#btn_add').button('reset');
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