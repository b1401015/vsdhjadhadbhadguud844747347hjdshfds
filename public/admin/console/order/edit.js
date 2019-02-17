
$(function () {
    $('.select2').select2();
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    $('#time_delivery').datetimepicker({
        // mask:'39/19/9999 29:00',
        step: 60, /*hour step by hour*/
        lang: 'vi',
        minDate: date.getDate() + '/' + date.getMonth() + '/' + date.getFullYear(),
        allowTimes: ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00',
            '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
            '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
            '08:00', '09:00', '20:00', '21:00', '22:00', '23:00'],
        format: 'd/m/Y H:i:s'
    });


    if ($("#example1").length) {
        var table = $('#example1').DataTable({
            "language": {
                "sEmptyTable": "Không tìm thấy dữ liệu",
                "sSearch": "Tìm kiếm:",
                "sZeroRecords": "Không tìm thấy dữ liệu này",
                "sLengthMenu": "Xem _MENU_ dòng / trang",
                "sInfo": "Đang xem từ _START_ đến _END_ trong tổng số _TOTAL_ dòng",
                "sInfoEmpty": "Hiển thị từ 0 đến 0 của 0 dòng",
                "sInfoFiltered": "(Lọc từ tổng _MAX_ dòng)",
                "oPaginate": {
                    "sFirst": "Trang đầu",
                    "sPrevious": "Trang trước",
                    "sNext": "Trang tiếp",
                    "sLast": "Trang cuối"
                }
            },
            "fnDrawCallback": function (oSettings) {
                // initCheckbox();

            }
        });

        //add event change value in .product_price
        $('#example1 tbody').on('keyup', 'input.qty', function () {
            var tr = $(this).closest('tr');

            $price = tr.find('input.product_price').val();
            $price = $price.split(',').join('');
            $qty = $(this).val();

            $total_price = parseInt($price) * parseInt($qty);
            console.log($total_price);
            tr.find('.total-price-item').text(number_format($total_price, 0, '.', ','));
        });

        // Add event listener for delete item
        $('#example1 tbody').on('click', '.delete-item', function () {
            if (!confirm('Bạn chắc chắn muốn xóa ?')) {
                return false;
            }
            var $this = $(this);
            var $tr = $this.closest('tr');

            $tr.remove();
            table.row($tr)
                .remove()
                .draw();
        });


        var counter = 1;

        if($('#order_item_length').val() > 0) {
            counter = parseInt($('#order_item_length').val()) + 1;
        }

        // event add product in list
        $('#add_product').click(function(){

            $id = $('#product_id').val();

            if(!isset($id)){
                alert('Vui lòng chọn sản phẩm !');
                return false;
            }
            if($('#product-' + $id).length){
                alert('Sản phẩm này đã tồn tại trong danh sách !');
                return false;
            }

            $code = $('option:selected', $('#product_id')).attr('data-code');
            $name = $('option:selected', $('#product_id')).attr('data-name');
            $price = $('option:selected', $('#product_id')).attr('data-price');
            $sale_price = $('option:selected', $('#product_id')).attr('data-sale-price');
            $qty = $('#qty').val();

            var $input_id = '<input type="hidden" name="product_id[]" value="' + $id + '" readonly >';
            var $input_price = '<div class="form-group ">' +
                '<div class="col-lg-9">' +
                number_format($sale_price, 0, '.', ',') +
                '</div>' +
                '</div>';
            var $input_qty = '<div class="form-group ">' +
                '<div class="col-lg-9">' +
                '<input class="form-control format_number qty" name="product_qty[]" onKeyPress="return isNumberKey(event)" value="' + $qty + '" min="1" type="text"/>' +
                '</div>' +
                '</div>';
            var $input_total_price = '<span class="total-price-item">' + number_format($sale_price * $qty, 0, '.', ',') + '</span>';
            var $input_delete = '<a class="delete-item" href="javascript:void(0)"><i class="fa fa-trash"></i></a>';

            table.row.add([
                counter + '<br>' + $input_id +
                '<input type="hidden" name="product_code[]" value="' + $code + '">' +
                '<input type="hidden" name="product_name[]" value="' + $name + '">' +
                '<input type="hidden" class="product_price" name="product_price[]" value="' + $price + '">' +
                '<input type="hidden" class="product_sale_price" name="product_sale_price[]" value="' + $sale_price + '">' +
                '<input type="hidden" id="product-' + $id + '">',
                $code,
                $name,
                $input_price,
                $input_qty,
                $input_total_price,
                $input_delete

            ]).draw(false);

            counter++;


        });

    }

    $("#frm_edit").bootstrapValidator({
        message: 'This value is not valid',
        live: 'enabled',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields : {
            code: {
                message: 'Mã địa lý không chính xác',
                validators: {
                    notEmpty: {
                        message: 'Mã địa lý không được để trống'
                    },
                    // stringLength: {
                    //     min: 5,
                    //     message: 'Mã địa lý phải lớn hơn 5 ký tự'
                    // },
                    // remote: {
                    //     type: 'POST',
                    //     url: '/EsmAdmin/geolocation/checkExistsCode',
                    //     //Send { username: 'its value', email: 'its value' }
                    //     message: 'Mã địa lý đã tồn tại',
                    //     data: function(validator) {
                    //         return {
                    //             'code': validator.getFieldElements('code').val()
                    //             //
                    //         };
                    //     },
                    // }
                }
            },
            name: {
                validators: {
                    notEmpty: {
                        message: 'Không được để trống!'
                    }
                }
            },
            qty: {
                validators: {
                    notEmpty: {
                        message: 'Vui lòng nhập số lượng sản phẩm!'
                    }
                }
            },
            // img_src: {
            //     validators: {
            //         notEmpty: {
            //             message: 'Vui lòng chọn hình'
            //         }
            //
            //     }
            // },
            ordinal: {
                validators: {
                    notEmpty: {
                        message: 'Thứ tự hiển thị không được để trống'
                    }

                }
            },
            // content: {
            //     validators: {
            //         notEmpty: {
            //             message: 'Nội dung không được để trống'
            //         }
            //
            //     }
            // },
        }
    }).on('success.form.bv', function(e) {
        // Prevent form submission
        e.preventDefault();

        $('#btn_edit').button('loading');
        // Get the form instance
        var $form = $(e.target);
        // Get the BootstrapValidator instance
        var bv = $form.data('bootstrapValidator');
        // Use Ajax to submit form data
        var url = CONSTANT.API.UPDATE_ORDER + '/' + $('#order_id').val();
        $.ajax({
            url: url,
            type: 'PUT',
            data: $("#frm_edit").serialize(),
            success: function (res) {
                if(res.message == CONSTANT.OK_CODE) {
                    alert('Chỉnh sửa thành công !');
                    window.location.href = CONSTANT.URI.GET_ORDER_LIST;
                }
                else{
                    alert(res.message);
                }
            },
            error: function (err) {
                alert('Chỉnh sửa thất bại. Vui lòng nhập đầy đủ thông tin đơn hàng và DS sản phẩm !');
                $('#btn_edit').button('reset');
            },
        });
    });


    // Number format
    $('.format_number').number(true);

    // $('#check_hide').iCheck('check');

    //Trim value
    $(".trim_value").bind('keyup', function(e) {
        $(this).val($.trim($(this).val()));
    });

    // Validate the form manually
    $('#btn_edit').click(function() {
        $('#frm_edit').bootstrapValidator('validate');
    });

    // event click change type
    $('.action-type-item a').click(function () {
        var $this = $(this);
        var $type = $this.attr('data-value');
        var $txt = $this.text();
        $('#input_type').val($type);
        $('#btn_type').html($txt + ' <span class="caret"></span>');
    });


}); // end $(function()

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

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
