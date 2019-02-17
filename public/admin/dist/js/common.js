//author: SangDo 2015

var submitting = false;
var SERVER_URI = 'http://hoidapsuckhoeonline.com:3000/';

function formatdmY (input) {
    var datePart = input.match(/\d+/g),
        year = datePart[0].substring(0), // get only two digits
        month = datePart[1], day = datePart[2];

    return day + '-' + month + '-' + year;
}

function formatdmY_His (input) {
    var datePart = input.match(/\d+/g),
        year = datePart[0].substring(0), // get only two digits
        month = datePart[1], day = datePart[2];

    return day + '-' + month + '-' + year + ' ' + input.substring(10);
}

function dlog(mess){
	if (!isEmpty(mess) && console.log)		//avoid IE
		console.log(mess);
		
}
function isEmpty(a_var){
	return a_var === undefined || a_var == null || $.trim(a_var)=='';
}
function isset(a_var){
	return !isEmpty(a_var);
}
function parseBool(b) {
    return !(/^(false|0)$/i).test(b) && !!b;
}
function show_alert(mess){
	alert(mess);
}
function show_confirm(mess){
	return window.confirm(mess);
}
//=======Redirect
function redirect(url){
	window.location.href=url;
}

function isAlphanumeric( str ) {
	 return /^[0-9a-zA-Z]+$/.test(str);
}
//========== CLASS
function Common() { }

//====================
function ajaxPost(uri, params, callback, callback_err){
	uri = encodeURI(SERVER_URI + uri);

	$.ajax({
		url: uri,//url is a link request
		type: 'POST',
		data: params, //data send to server
		dataType: 'json',	//jsonp causes error in IE
		complete: function (response){ //response that server rely
//			slog('>>>> complete request: ' + uri);
//			slog(response);
			if (response.status == 500 && callback_err !== undefined)		//error
				callback_err(response.responseText);
			else if (response.status == 200 && callback !== undefined)
				callback(response.responseText);
		},
	});
}
//====================
function ajaxGet(uri, params, callback, callback_err){
	uri = encodeURI(SERVER_URI + uri);

	$.ajax({
		url: uri,
		type: 'GET',
		data: params,
		dataType: 'json',	//jsonp causes error in IE
		complete: function (response){
//			slog('>>>> complete request: ' + uri);
//			slog(response);
			if (response.status == 500 && callback_err !== undefined)		//error
				callback_err(response.responseText);
			else if (response.status == 200 && callback !== undefined)
				callback(response.responseText);
		}
	});
}

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

//uppercase first letter of string
function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function price_format(x){
    return isNaN(x)?"":x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

function number_format( number, decimals, dec_point, thousands_sep ) {
    // http://kevin.vanzonneveld.net
    // + original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + bugfix by: Michael White (http://crestidg.com)
    // + bugfix by: Benjamin Lupton
    // + bugfix by: Allan Jensen (http://www.winternet.no)
    // + revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // * example 1: number_format(1234.5678, 2, '.', '');
    // * returns 1: 1234.57

    var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
    var d = dec_point == undefined ? "," : dec_point;
    var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
    var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

$(function () {
    // Number format
    $('.format_number').number(true);

    //Trim value
    $(".trim_value").bind('keyup', function(e) {
        $(this).val($.trim($(this).val()));
    });
});



var common = new Common();		//global object

