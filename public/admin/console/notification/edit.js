$(function () {

    var submitting = false;

    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    // $('input[name="datetime"]').datetimepicker('destroy');
    $('input[name="time"]').datetimepicker({
        // mask:'39/19/9999 29:00',
        step: 60, /*hour step by hour*/
        lang:'en',
        minDate: date.getDate() + '/' + date.getMonth() + '/' + date.getFullYear(),
        allowTimes:['00:00','01:00','02:00','03:00','04:00','05:00',
            '06:00','07:00','08:00','09:00','10:00','11:00',
            '12:00','13:00','14:00','15:00','16:00','17:00',
            '18:00','19:00','20:00','21:00','22:00','23:00'],
        format: 'Y/m/d H:i'
    });

    $('.select2').select2();

    $("#frm_edit").bootstrapValidator({
        message: 'Giá trị không hợp lệ',
        live: 'enabled',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields : {
            time: {
                validators: {
                    notEmpty: {
                        // message: 'Vui lòng không để trống'
                    }

                }
            },
            title: {
                validators: {
                    notEmpty: {
                        // message: 'Vui lòng không để trống'
                    }

                }
            },
            content: {
                validators: {
                    notEmpty: {
                        // message: 'Vui lòng không để trống'
                    }

                }
            },
            // 'country_id[]': {
            //     validators: {
            //         notEmpty: {
            //             // message: 'Vui lòng không để trống'
            //         }
            //
            //     }
            // }
        }
    }).on('success.form.bv', function(e) {
        // Prevent form submission
        e.preventDefault();

        if (submitting){
            return;
        }

        submitting = true;
        $('#btn_edit').button('loading');
        // Get the form instance
        var $form = $(e.target);
        var formData = new FormData($("#frm_edit")[0]);
        formData = new FormData($(this)[0]);
        // Get the BootstrapValidator instance
        var bv = $form.data('bootstrapValidator');
        // Use Ajax to submit form data
        var url = CONSTANT.API.UPDATE_NOTIFICATION;
        // formData.append('content_product', CKEDITOR.instances['write_content'].getData());
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (res) {
                if(res.message == CONSTANT.OK_CODE){
                    // $("#frm_edit")[0].reset();
                    alert("Update successfully!");
                    window.location = CONSTANT.URI.NOTIFICATION_LIST;
                }
                else{
                    alert(res.message);
                    submitting = false;
                }
                $('#btn_edit').button('reset');
            },
            error: function () {
                alert('Please try again!');
                $('#btn_edit').button('reset');
                submitting = false;
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }); //end bootstrapValidator

    // Validate the form manually
    $('#btn_edit').click(function() {
        $('#frm_edit').bootstrapValidator('validate');
    });

    // Add event listener for change is_active
    $('#is_push_time').change(function() {
        var $this = $(this);
        if ($this.is(':checked')) {
            $('#input_time').removeClass('hide');
        }
        else{
            $('#input_time').addClass('hide');
        }
    });

}); // end $(function)