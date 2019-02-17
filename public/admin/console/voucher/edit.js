//BootstrapValidator

$(function () {

    $('.daterange').daterangepicker(
        {
            format: 'DD/MM/YYYY',
            ranges: {
//                'Today': [moment(), moment()],
//                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
//                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
//                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
//                'This Month': [moment().startOf('month'), moment().endOf('month')],
//                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            "locale": {
                "format": "YYYY/MM/DD",
                "separator": " - ",
                "applyLabel": "Thiết lập",
                "cancelLabel": "Hủy",
                "fromLabel": "Từ",
                "toLabel": "Đến",
                "customRangeLabel": "Tùy chỉnh",
                "daysOfWeek": [
                    "CN",
                    "T2",
                    "T3",
                    "T4",
                    "T5",
                    "T6",
                    "T7"
                ],
                "monthNames": [
                    "Tháng 1",
                    "Tháng 2",
                    "Tháng 3",
                    "Tháng 4",
                    "Tháng 5",
                    "Tháng 6",
                    "Tháng 7",
                    "Tháng 8",
                    "Tháng 9",
                    "Tháng 10",
                    "Tháng 11",
                    "Tháng 12",
                ],
                "firstDay": 1
            }
        },
        function (start, end) {
            // alert("You chose: " + start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

            $("#time_start").val(start.format('DD/MM/YYYY'));
            $("#time_end").val(end.format('DD/MM/YYYY'));
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
            name: {
                validators: {
                    notEmpty: {
                        message: 'Không được để trống!'
                    },
                }
            },
            _value: {
                validators: {
                    notEmpty: {
                        message: 'Không được để trống!'
                    }
                }
            },
            percent_value: {
                validators: {
                    notEmpty: {
                        message: 'Không được để trống!'
                    },
                }
            },
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
        var url = '/_api/admin/voucher/update_voucher';
        // formData.append('content_product', CKEDITOR.instances['write_content'].getData());
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (res) {
                if(res.message == CONSTANT.OK_CODE){
                    alert("Cập nhật thành công!");
                    window.location = '/_admin/voucher/listing';
                }
                else{
                    alert(res.message);
                }
                $('#btn_edit').button('reset');
            },
            error: function () {
                alert('Vui lòng thử lại. Hãy chắc rằng bạn đã nhập đầy đủ trường có * !');
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

    // Add event listener for sale record
    $('#voucher_type').change( function () {
        var $this = $(this);
        var $val = $this.val();
        if($val == CONSTANT.PERCENT_ITEM){

            //Hide record select box
            $('.sale-record').css('display', 'none').toggle(500);

            //Hide sale price and show percent price
            $('#_value').hide(500);
            $('.percent_value').show(500);

            //Show max sale price
            $('.max-value').show(500);

        }
        else if($val == CONSTANT.PRICE_ITEM){
            $('#_value').removeAttr('max');
            $('.sale-record').css('display', 'none').toggle(500);

            //Show sale price and hide percent price
            $('#_value').show(500);
            $('.percent_value').hide(500);

            //Hide max sale price
            $('.max-value').hide(500);
        }
        else if($val == CONSTANT.PRICE_TOTAL){
            $('#_value').removeAttr('max');
            $('.sale-record').css('display', 'block').toggle(500);

            //Show sale price and hide percent price
            $('#_value').show(500);
            $('.percent_value').hide(500);

            //Hide max sale price
            $('.max-value').hide(500);
        }
        else if($val == CONSTANT.PERCENT_TOTAL){

            //Hide record select box
            $('.sale-record').css('display', 'block').toggle(500);

            //Hide sale price and show percent price
            $('#_value').hide(500);
            $('.percent_value').show(500);

            //Show max sale price
            $('.max-value').show(500);
        }
    });

});