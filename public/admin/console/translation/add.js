//BootstrapValidator

$(function () {

    var $is_submitting = false;

    //form validation
    $("#frm_add").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            keyword: {
                validators: {
                    notEmpty: {
                        message: 'Vui lòng không để trống!'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                        message: 'Valid keywords contain only characters, numbers, and underscores!'
                    }
                }
            },
            language_id: {
                validators: {
                    notEmpty: {
                        // message: 'Vui lòng không để trống!'
                    },
                }
            },
            _content: {
                validators: {
                    notEmpty: {
                        message: 'Vui lòng không để trống!'
                    },
                }
            },
        }
    }).on('success.form.bv', function(e) {
        // Prevent form submission
        e.preventDefault();

        $is_submitting = true;

        $('#btn_add').html('loading');

        // Get the form instance
        var $form = $(e.target);
        var formData = new FormData($("#frm_add")[0]);
        formData = new FormData($(this)[0]);
        // Get the BootstrapValidator instance
        var bv = $form.data('bootstrapValidator');
        // Use Ajax to submit form data
        var url = CONSTANT.URI.ADD_NEW_TRANSLATION;
        // formData.append('content_product', CKEDITOR.instances['write_content'].getData());
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (res) {
                if(res.message == CONSTANT.OK_CODE){
                    alert("Thêm mới thành công!");
                    window.location = CONSTANT.URI.TRANSLATION;
                }
                else{
                    alert(res.message);
                    $('#btn_add').html('Thêm');
                }

                $is_submitting = false;
            },
            error: function () {
                alert('Vui lòng thử lại!');
                $('#btn_add').html('Reset');
                $is_submitting = false;
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