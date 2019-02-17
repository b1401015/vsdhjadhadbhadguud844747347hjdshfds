
var submitting = false;

//========== CLASS
function Common() { }

var common = new Common();		//global object

Common.prototype.dlog = function(mess){
    if (!this.isEmpty(mess) && console.log) {		//avoid IE
        console.log(mess);
    }
};
//
Common.prototype.isEmpty = function(a_var){
    return a_var === undefined || a_var == null || $.trim(a_var)=='';
}
//
Common.prototype.isset = function(a_var){
    return !this.isEmpty(a_var);
};
//
Common.prototype.ajaxPost = function(uri, params, callback, callback_err){
    uri = encodeURI(SERVER_URI + uri);

    $.ajax({
        url: uri,//url is a link request
        type: 'POST',
        data: params, //data send to server
        dataType: 'json',	//jsonp causes error in IE
        success: function (msg) {
            if (callback !== undefined){
                callback(msg.data);
            }
        },
        error: function (errormessage) {
            if (callback_err !== undefined) {
                callback_err(errormessage.responseText);
            }
        }
    });
};
//
Common.prototype.redirect = function(url){
    window.location.href = url;
};