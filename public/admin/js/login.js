/**
 * Process login page
 */
//========== CLASS
function LoginPage() { }

var loginPage = new LoginPage();		//global object
//get new captcha image
LoginPage.prototype.refresh_captcha = function(){
    var uri = ADMIN_API_URI.READ_NEW_CAPTCHA;
    common.ajaxPost(uri, {}, function(resp){
       if (common.isset(resp)){
           $('#img_captcha').html(resp);
       }
    });
};
//begin login progress
LoginPage.prototype.process_login = function(){
    if (submitting){
        return;
    }
    //verify input
    var txt_account = $.trim($('#txt_account').val());
    var txt_password = $.trim($('#txt_password').val());
    var txt_captcha = $.trim($('#txt_captcha').val());
    if (common.isEmpty(txt_account) || common.isEmpty(txt_password) || common.isEmpty(txt_captcha)){
        $('#label_message').text(STR_MESS.MISSING_INPUT);
        return;
    }
    var encrypted_pass = sha256(txt_password);
    var params = {
        username: txt_account,
        password: encrypted_pass,
        captcha: txt_captcha
    };
    $('#label_message').text('');
    submitting = true;
    common.ajaxPost(ADMIN_API_URI.LOGIN, params, function(resp){
        //correct authentication
        submitting = false;
        common.redirect('/_admin/index');
    }, function(err){
        err = $.parseJSON(err);
        if (err.message == 'WRONG_CAPTCHA'){
            $('#label_message').text(STR_MESS.WRONG_CAPTCHA);
            loginPage.refresh_captcha();
        } else if (err.message == 'NOT_FOUND'){
            $('#label_message').text(STR_MESS.INVALID_LOGIN_ADMIN);
        }
        $.trim($('#txt_password').val(''));
        submitting = false;
    });
}

//
window.onload = function(){
    $('#txt_captcha').unbind();
    $('#txt_captcha').keypress(function(e){
        if (e.which == 13){
            //pressed ENTER
            loginPage.process_login();
        }
    });
}
