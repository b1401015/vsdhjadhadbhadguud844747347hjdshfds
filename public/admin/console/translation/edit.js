//BootstrapValidator

$(function () {

    var $is_submitting = false;

    //form validation
    $("#frm_edit").bootstrapValidator({
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
                        //     message: 'Vui lòng không để trống!'
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
                        // message: 'Vui lòng không để trống!'
                    },
                }
            },
        }
    }).on('success.form.bv', function(e) {
        // Prevent form submission
        e.preventDefault();

        $is_submitting = true;

        $('#btn_edit').html('loading');
        // Get the form instance
        var $form = $(e.target);
        var formData = new FormData($("#frm_edit")[0]);
        formData = new FormData($(this)[0]);
        // Get the BootstrapValidator instance
        var bv = $form.data('bootstrapValidator');
        // Use Ajax to submit form data
        var url = CONSTANT.URI.UPDATE_TRANSLATION;
        // formData.append('content_product', CKEDITOR.instances['write_content'].getData());
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (res) {
                if(res.message == CONSTANT.OK_CODE){
                    alert("Edit successfully!");
                    window.location = CONSTANT.URI.TRANSLATION;
                }
                else{
                    alert(res.message);
                    $('#btn_edit').html('Cập nhật');
                }
                $('#btn_edit').button('reset');
            },
            error: function () {
                alert('Vui lòng thử lại!');
                $('#btn_edit').html('Cập nhật');
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }); //end bootstrapValidator

    $('#btn_edit').click(function() {
        $('#frm_edit').bootstrapValidator('validate');
    });
});