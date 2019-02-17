var $lang = 1;

$(function(){
    // $('div.alert').delay(5000).slideUp();
    $('div.alert').css('display', 'none');

    // Change Password
    $('#change_pass').click(function(){
        $error = '';
        $OldPass = $('#frm_edit_profile').find('input[name="old_pwd"]').val();

        $NewPass = $('#frm_edit_profile').find('input[name="new_pwd"]').val();
        $ReNewPass = $('#frm_edit_profile').find('input[name="re_pwd"]').val();
        if($NewPass != $ReNewPass){
            if($lang == 1){
                $error = 'Mật khẩu nhập lại không trùng với mật khẩu mới!';
            }
            else{
                $error = 'Mot de passe retapez votre nouveau mot de passe ne correspond pas !';
            }
            $html = '<ul><li>' + $error + '</li></ul>';
            $('#frm_edit_profile').find('.alert-danger').css('display', 'block').html($html);
            $('div.alert').delay(5000).slideUp();
        }
        else{
            $.ajax({
                // url: $.getHost() + 'fea-admin/view/pages/auth/change_pass.php',
                url: '/_api/admin/user/change_password',
                type: 'POST',
                dataType: 'json',
                async: true,
                cache: false,
                data:{old_pwd: $OldPass, new_pwd: $NewPass},
                success: function(res){
                    console.log(res.message);
                    if(res.message == CONSTANT.WRONG_PASSWORD){
                        if($lang == 1){
                            $error = 'Mật khẩu cũ không đúng !';
                        }
                        else{
                            $error = 'Ancien mot de passe incorrect !';
                        }
                        $html = '<ul><li>' + $error + '</li></ul>';
                        $('#frm_edit_profile').find('.alert-danger').css('display', 'block').html($html);
                        $('div.alert').delay(5000).slideUp();
                    }
                    else if(res.message == CONSTANT.OK_CODE){
                        if($lang == 1){
                            $error = 'Đổi mật khẩu thành công. Hệ thống sẽ tự động quay về trang đăng nhập !';
                        }
                        else{
                            $error = 'Avec succès changé votre mot de passe. Le système va automatiquement revenir à la page de connexion !';
                        }
                        $html = '<ul><li>' + $error + '</li></ul>';
                        $('#frm_edit_profile').find('.alert-danger').removeClass('alert-danger').addClass('alert-success').css('display', 'block').html($html);
                        $('div.alert').delay(5000).slideUp();
                        setTimeout(function(){
                            // window.location.href = $.getHost() + 'fea-admin/?page=dang-xuat';
                            window.location.href = '/_admin/logout';
                        }, 5000);

                    }
                    else{
                        alert(res.message);
                    }
                },
                error: function(){
                    alert('Vui lòng thử lại');
                }
            });
        }
    });
});

function confirmDelete(msg){
    if(window.confirm(msg)){
        return true;
    }
    else return false;
}

function edit_Profile(){
    $('#profile-Modal').modal('hide');
    $('#profile-Edit').modal('show');
}