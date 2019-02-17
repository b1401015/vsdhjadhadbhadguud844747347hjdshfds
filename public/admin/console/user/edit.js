//BootstrapValidator

$(function () {

    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    $('#birthday').datetimepicker({
        // mask:'39/19/9999 29:00',
        step: 60, /*hour step by hour*/
        lang: 'vi',
        // minDate: date.getDate() + '/' + date.getMonth() + '/' + date.getFullYear(),
        maxDate: 'now',
        // allowTimes: ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00',
        //     '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
        //     '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
        //     '08:00', '09:00', '20:00', '21:00', '22:00', '23:00'],
        format: 'd/m/Y'
    });


    //form validation
    $("#frm_edit").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            email: {
                validators: {
                    regexp: {
                        regexp: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
                        message: 'Email hợp lệ chỉ chứa ký tự, chữ số và dấu gạch dưới!'
                    }
                }
            },
            password: {
                // validators: {
                //     stringLength: {
                //         min: 6,
                //         message: 'Mật khẩu phải nhiều hơn 6 ký tự!'
                //     }
                // }
            },
            re_password: {
                validators: {
                    identical: {
                        field: 'password',
                            message: 'Xác nhận mật khẩu không trùng khớp!'
                    },
                    callback: {
                        message: 'Vui lòng nhập xác nhận mật khẩu',
                        callback: function (value, validator, $field) {
                            var pass = $('#password').val();
                            var re_pass = value;
                            var vali = true;
                            if(pass.length > 0 && re_pass.length == 0){
                                vali = false;
                            }
                            return vali;
                        }
                    }
                }
            },
            fullname: {
                validators: {
                    notEmpty: {
                        message: 'Không được để trống!'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9._-ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s-]+$/,
                        message: 'Không được nhập ký tự đặc biệt!'
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
        var url = CONSTANT.API.UPDATE_USER;
        // formData.append('content_product', CKEDITOR.instances['write_content'].getData());
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (res) {
                if(res.message == CONSTANT.OK_CODE){
                    alert("Cập nhật thành công!");
                    window.location = CONSTANT.URI.GET_USER_LIST;
                }
                else{
                    if(res.message == CONSTANT.USER_IS_EXISTED) {
                        alert('Username đã tồn tại!');
                    }
                    else if (res.message == CONSTANT.EMAIL_IS_EXISTED) {
                        alert('Email đã tồn tại!');
                    }
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

    // Format the phone number as the user types it
    document.getElementById('phone').addEventListener('keyup',function(evt){
        var phoneNumber = document.getElementById('phone');
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        phoneNumber.value = phoneFormat(phoneNumber.value);
    });

    // We need to manually format the phone number on page load
    document.getElementById('phone').value = phoneFormat(document.getElementById('phone').value);

    // A function to determine if the pressed key is an integer
    function numberPressed(evt){
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if(charCode > 31 && (charCode < 48 || charCode > 57) && (charCode < 36 || charCode > 40)){
            return false;
        }
        return true;
    }

    // A function to format text to look like a phone number
    function phoneFormat(input){
        // Strip all characters from the input except digits
        input = input.replace(/\D/g,'');

        // Trim the remaining input to ten characters, to preserve phone number format
        input = input.substring(0,10);

        // Based upon the length of the string, we add formatting as necessary
        var size = input.length;
        if(size == 0){
            input = input;
        }else if(size < 4){
            input = '('+input;
        }else if(size < 7){
            input = '('+input.substring(0,3)+') '+input.substring(3,6);
        }else{
            input = '('+input.substring(0,3)+') '+input.substring(3,6)+' - '+input.substring(6,10);
        }
        return input;
    }
});