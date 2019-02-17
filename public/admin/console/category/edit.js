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
            name: {
                validators: {
                    notEmpty: {
                        message: 'Tên danh mục không được để trống!'
                    },
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
        var url = '/_api/admin/category/edit_category';
        // formData.append('content_product', CKEDITOR.instances['write_content'].getData());
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (res) {
                if(res.message == 'OK'){
                    alert("Cập nhật danh mục thành công!");
                    window.location = '/_admin/category/listing';
                }
                else{
                    alert(res.message);
                }
                $('#btn_edit').button('reset');
            },
            error: function () {
                alert('Vui lòng thử lại !');
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
});