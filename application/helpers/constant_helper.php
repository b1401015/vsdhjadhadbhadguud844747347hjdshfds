<?php
//========== Define common variables of whole system
define('ADMIN_CONTROLLER_NAME', '_admin');		//controller name in URL of Admin
define('USER_MANAGER_CONTROLLER_NAME', 'user_manager');		//controller name in URL of Admin
define('API_CONTROLLER_NAME', 'api');		    //controller name in URL of API which need login to process
define('PUBLIC_CONTROLLER_NAME', 'public');	//controller name in URL of API which don't need login to process

//========== Session keys
define('SESS_KEY_USER_ID', 'sess_user_id');     //used in Admin or front-end user
define('SESS_KEY_CAPTCHA', 'sess_captcha');     //save captcha string

//========== captcha
define('CAPTCHA_FOLDER', 'captcha/');
define('CAPTCHA_W', 200);
define('CAPTCHA_H', 80);
define('CAPTCHA_EXP_DURATION', 300);    //5 minutes
define('CAPTCHA_FONT_SIZE', 80);

define('UNKNOWN_ERROR', 'unknown_error');