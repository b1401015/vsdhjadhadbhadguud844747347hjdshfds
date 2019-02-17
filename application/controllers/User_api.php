<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class User_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('account_model');
        $this->load->model('account_info_model');
        $this->load->model('account_jwt_model');
        $this->load->model('push_token_model');
        $this->load->model('account_language_setting_model');// không cần
        $this->load->model('account_setting_model');// không cần
        $this->load->model('account_register_model');
        $this->load->model('user_get_type_model');
        $this->load->model('setting_model');
        $this->load->model('common_setting_model');
        $this->load->model('favorite_model');
        $this->load->model('project_model');
        $this->load->model('salary_model');
        $this->load->model('rank_model');
        $this->load->model('rating_model');
        $this->load->model('appointment_model');//
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $res = RestSuccess('user_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
     * function register
     * method: post
     * params: username, password, fullname, captcha
     */
    function register_post()
    {
        /*user info*/
        $username = trim($this->post('username'));

        $password = strtolower(trim($this->post('password')));
        // dump($password,true);
        $email = trim($this->post('email'));
        $fullname = $this->post('fullname');
        $phone_code = strtoupper(trim($this->post('phone_code')));
        $phone_number = trim($this->post('phone_number'));
        $code_introduction = trim($this->post('code_introduction'));

        /*device info*/
        $device_id = $this->post('device_id');
        $device_name = $this->post('device_name');
        $device_version = $this->post('device_version');
        $app_id = $this->post('app_id');
        $app_name = $this->post('app_name');
        $app_version = $this->post('app_version');

        /*push token*/
        $apple_token = $this->post('apple_token');
        $firebase_token = $this->post('firebase_token');
        $timezone = $this->post('timezone');

        $captcha = trim($this->post('captcha'));

        $type_client = USER_TYPE_CLIENT;

        $code_verify = generateReferenceCode(0, 6);
        $check_verify_params = checkVerifyParams([
            $username,
            $password,
            $fullname,
            $email,
            $app_id,
            $device_id,
            $captcha
        ]);
        if (!empty($check_verify_params)) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

//        $this->writeLog('CAPTCHA_EXPIRE_TIME', $this->session->userdata('CAPTCHA_EXPIRE_TIME'));
//        $this->writeLog('CAPTCHA_CODE', $this->session->userdata('CAPTCHA_CODE'));
//        $this->writeLog('CAPTCHA_CLIENT', $captcha);

        if (!$this->checkCaptcha($captcha)) {
//            $this->writeLog(current_url(), $captcha);
//            $this->writeLog(current_url(), $this->session->userdata('CAPTCHA_CODE'));
//            $this->writeLog(current_url(), $this->session->userdata('CAPTCHA_EXPIRE_TIME'));
//            $this->writeLog('register post wrong_captcha', json_encode($this->session->userdata(), JSON_UNESCAPED_UNICODE));
            $this->response(RestBadRequest(WRONG_CAPTCHA_MSG, $this->session->userdata()), BAD_REQUEST_CODE);
        }
        //delete captcha session
        $session_userdata = [
            'CAPTCHA_EXPIRE_TIME' => '',
            'CAPTCHA_CODE' => ''
        ];
        $this->session->set_userdata($session_userdata);


        //check user exists
        $where = "
            username like '$username'
            and type = '$type_client' 
        ";
        $user_exists = $this->user_model->findOne($where, array('*'));
        if (!empty($user_exists)) {
            $this->response(RestBadRequest(USER_EXISTED_MSG), BAD_REQUEST_CODE);
        }

        //check email exists
        $where = "
            email like '$email' and email is not null and email <> '' 
            and type = '$type_client' 
        ";
        $email_exists = $this->user_model->findOne($where, array('*'));
        if (!empty($email_exists)) {
            $this->response(RestBadRequest(EMAIL_IS_EXISTED_MSG), BAD_REQUEST_CODE);
        }

        //check phone exists
        if(!empty($phone_code) && !empty($phone_number)) {
            $where = "
            (account_info.phone like '$phone_number' and account_info.phone is not null and account_info.phone <> '')           
            and user_get_type.type = '$type_client'
            and account_info.short_name_phone = '$phone_code'
        ";
            $phone_exists = $this->user_model->findOne($where, array('*'));
            if (!empty($phone_exists)) {
                $this->response(RestBadRequest(PHONE_IS_EXISTED_MSG), BAD_REQUEST_CODE);
            }
        }

        //get code_intro max
        $where_intro = array(
            'user_get_type.type' => USER_TYPE_CLIENT,
        );
        $code_intro = $this->account_model->findCodeIntroClient($where_intro, array('account.code_intro'));
        //dump($code_intro,true);
        if (empty($code_intro)) {
            $new_code_intro = CODE_CLIENT . '0000001';
        } else {
            $code = substr($code_intro->code_intro, strlen(CODE_CLIENT)) + 1;
            $new_code_intro = CODE_CLIENT . str_pad($code, 7, "0", STR_PAD_LEFT);
        };
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well*/
        //insert account tb
        $this->account_model->create(array(
            'fullname' => $fullname,
            'is_verified' => false,
            'role' => ROLE_USER,
            'code_intro' => $new_code_intro,
            'code_verify' => $code_verify,
            'plain_name' => skipVN($fullname, true),
            'app_id' => $app_id,
            'app_name' => $app_name,
            'app_version' => $app_version,
            'device_id' => $device_id,

        ));

        $account_id = $this->db->insert_id();

        //insert account_info tb
        $this->account_info_model->create([
            'short_name_phone' => $phone_code,
            'phone' => $phone_number,
            'account_id' => $account_id
        ]);


        //insert user get type
        $this->user_get_type_model->create([
            'account_id' => $account_id,
            'type' => $type_client,

        ]);

        //insert user tb
        $this->user_model->create([
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'last_login' => CURRENT_TIME,
            'account_id' => $account_id,
        ]);

        //insert account_jwt
        $jwt_generator = new JWT();
        $jwt = $jwt_generator::encode([
            'account_id' => $account_id,
            'app_id' => $app_id,
            'device_id' => $device_id,
            'time_mi' => CURRENT_MILLISECONDS,
        ], SERVER_KEY);

        $this->account_jwt_model->create([
            'jwt' => $jwt,
            'expire_time' => JWT_EXPIRE_TIME, // 7 days
        ]);

        //upsert push_token
        $data_push_token = [
            'account_id' => $account_id,
            'apple_token' => $apple_token,
            'firebase_token' => $firebase_token,
            'device_id' => $device_id,
            'device_name' => $device_name,
            'device_version' => $device_version,
            'timezone' => $timezone,
            'app_version' => $app_version
        ];
        $this->upsertPushToken($data_push_token);
        //check code introduction
        //dump($code_introduction,true);
        if (!empty($code_introduction)) {
            $where_code = array(
                'type' => $type_client,
                'code_intro' => $code_introduction,
            );
            $select_code = array(
                'account._id',
                'account_info.point2rank',
                'account_info.point2gift',
            );
            $code_existed = $this->account_model->findOne($where_code, $select_code);
            // dump($code_existed,true);
            if (!empty($code_existed)) {
                $setting = $this->common_setting_model->get_first_row();
                $this->account_register_model->create(
                    array(
                        'account_id' => $account_id,
                        'code_introduction' => $code_introduction,
                        'account_introduction' => $code_existed->_id,
                        'time_register' => CURRENT_TIME,
                    )
                );
                $this->account_info_model->update_by_condition(
                    array(
                        'account_info.account_id' => $code_existed->_id
                    ),
                    array(
                        'account_info.point2rank' => $code_existed->point2rank + $setting->point2intro,
                        'account_info.point2gift' => $code_existed->point2gift + $setting->point2intro
                    )
                );

            } else {

                $this->db->trans_rollback();
                $this->response(RestBadRequest(CODE_INTRODUCTION_IS_NOT_EXISTED_MSG), BAD_REQUEST_CODE);
            }
        }
        $this->db->trans_commit();
        $res = $this->responseUserAccountDatas($account_id, $jwt);
        $res ['code_verify'] = $code_verify;
        //store user session
        $session_userdata = [
            'ACCOUNT_ID' => $account_id,
            'ACCOUNT_DATA' => json_encode($res, JSON_UNESCAPED_UNICODE)
        ];
        $this->session->set_userdata($session_userdata);


        $res = removeNullOfObject($res);
        $this->response(RestSuccess($res), SUCCESS_CODE);

    }

    /*
     * function login
     * method: post
     * params: username, password
     */
    function login_post()
    {

        $type_user = !empty($this->input->request_headers()['Type']) ? $this->input->request_headers()['Type'] : 'client';

        $username = trim($this->post('username'));
        $password = strtolower(trim($this->post('password')));
        $lat = trim($this->post('lat'));
        $long = trim($this->post('long'));

        /*device info*/
        $device_id = $this->post('device_id');
        $device_name = $this->post('device_name');
        $device_version = $this->post('device_version');
        $app_id = $this->post('app_id');
        $app_name = $this->post('app_name');
        $app_version = $this->post('app_version');

        /*push token*/
        $apple_token = $this->post('apple_token');
        $firebase_token = $this->post('firebase_token');
        $timezone = $this->post('timezone');

        $check_verify_params = checkVerifyParams([
            $username,
            $password,
            $app_id,
            $device_id,
            $type_user,
        ]);
        if (!empty($check_verify_params)) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $where = "
            username like '$username' 
            and (password like '$password' or (password_reset like '$password' and password_reset is not null))
        ";
        $select = array(
            'user._id',
            'user.account_id',
            'user.email',
            'user.social_type',
            'account.fullname',
            'account.crop_img_src',
            'account.img_src',
            'account_info.address',
            'account_info.phone',
            'account_info.birthday',
            'account_info.gender',
            'account.is_active',
            'account.is_delete',
            'account.is_verified',
            'user.password_reset',
            'user_get_type.type'
        );
        $user = $this->user_model->findOne($where, $select);

        if (empty($user)) {
            $this->response(RestBadRequest(INVALID_USERNAME_OR_PASSWORD_MSG), BAD_REQUEST_CODE);
        } else if ($user->is_verified == false) {

            /*
             * Nam.Pham
             * Date :2018/11/23
             */
            $code_verify = generateReferenceCode(0, 6);
            $data_update = array(
                'code_verify' => $code_verify,
            );
            $this->account_model->update_by_condition(array(
                '_id' => $user->account_id,
            ), $data_update);

            $rs = [
                'status' => BAD_REQUEST_CODE,
                'message' => USER_IS_NOT_VERIFY_MSG,
                'data' => !empty($data_update) ? $data_update : [],
            ];
            $this->response($rs, BAD_REQUEST_CODE);
        }
        if($type_user != $user->type){
            $this->response(RestBadRequest(WRONG_TYPE_USERNAME));
        }
        $data_update = array(
            'last_login' => CURRENT_TIME,
            'password' => $password,
            'password_reset' => null,
        );

        //update user
        $this->user_model->update_by_condition([
            '_id' => $user->_id
        ], $data_update);

        //update account
        $this->account_model->update_by_condition([
            '_id' => $user->account_id
        ], [
            'app_id' => $app_id,
            'app_name' => $app_name,
            'app_version' => $app_version,
            'device_id' => $device_id,/*
            'update_time' => CURRENT_TIME,*/
            'latitude' => $lat,
            'longitude' => $long,
        ]);

        //insert account_jwt
        $jwt_generator = new JWT();
        $jwt = $jwt_generator::encode([
            'account_id' => $user->account_id,
            'app_id' => $app_id,
            'device_id' => $device_id,
            'time_mi' => CURRENT_MILLISECONDS,
        ], SERVER_KEY);

        $this->account_jwt_model->create([
            'jwt' => $jwt,
            'expire_time' => JWT_EXPIRE_TIME, // 7 days
        ]);
        //upsert push_token
        $data_push_token = [
            'account_id' => $user->account_id,
            'apple_token' => $apple_token,
            'firebase_token' => $firebase_token,
            'device_id' => $device_id,
            'device_name' => $device_name,
            'device_version' => $device_version,
            'timezone' => $timezone,
            'app_version' => $app_version
        ];
        $this->upsertPushToken($data_push_token);

        $res = $this->responseUserAccountDatas($user->account_id, $jwt);

        //store user session
        $session_userdata = [
            'ACCOUNT_ID' => $user->account_id,
            'ACCOUNT_DATA' => json_encode($res, JSON_UNESCAPED_UNICODE)
        ];
        $this->session->set_userdata($session_userdata);

        $res = removeNullOfObject($res);
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function register & login via social (fb, gg, zalo, ...)
     * method: post
     * params: fb_id, gg_id
     */
    function login_social_post()
    {
        $social_type = strtolower($this->post('social_type'));
        $social_id = $this->post('social_id');
        $social_token = $this->post('social_token');
        $social_img_src = $this->post('social_img_src');
        $email = $this->post('email');
        //$gender = !empty($this->post('gender')) ? strtolower($this->post('gender')) : GENDER_DEFAULT;
        $phone_code = $this->post('phone_code');
        $phone_number = $this->post('phone_number');
        $birthday = !empty($this->post('birthday')) ? date('Y-m-d', strtotime($this->post('birthday'))) : null;
        $fullname = !empty($this->post('fullname')) ? $this->post('fullname') : $social_id;
        //$address = $this->post('address');

        /*device info*/
        $device_id = $this->post('device_id');
        $device_name = $this->post('device_name');
        $device_version = $this->post('device_version');
        $app_id = $this->post('app_id');
        $app_name = $this->post('app_name');
        $app_version = $this->post('app_version');

        /*push token*/
        $apple_token = $this->post('apple_token');
        $firebase_token = $this->post('firebase_token');
        $timezone = $this->post('timezone');

        $check_verify_params = checkVerifyParams([
            $social_type,
            $social_id,
            $app_id,
            $device_id,
        ]);
        if (!empty($check_verify_params)) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }
        if (!empty($email)) {
            //check email exists
            $where = "
            (email like '$email' and email is not null)
            and social_id not like '$social_id'
        ";
            $email_exists = $this->user_model->get_first_row($where);
            if (!empty($email_exists)) {
                $this->response(RestBadRequest(EMAIL_IS_EXISTED_MSG), BAD_REQUEST_CODE);
            }
        }

        //check user exists
        $where = [
            'social_id' => $social_id,
        ];
        $select = array(
            'user._id',
            'user.account_id',
            'user.username',
            'user.email',
            'account.fullname',
            'account.crop_img_src',
            'account.img_src',
            'account.social_img_src',
            'account_info.address',
            'account_info.phone',
            'account_info.short_name_phone',
            'account.is_active',
            'account.is_delete',
            'account_info.is_update_profile',
        );
        $user = $this->user_model->findOne($where, $select);
        if (empty($user)) { //insert new
            //insert account tb
            $this->account_model->create([
//                'first_name' => $first_name,
//                'last_name' => $last_name,
                'fullname' => $fullname,
                'plain_name' => skipVN($fullname, true),
                'app_id' => $app_id,
                'app_name' => $app_name,
                'app_version' => $app_version,
                'device_id' => $device_id,
                'social_img_src' => $social_img_src,
                'is_verified' => false,
                'role' => ROLE_USER,
                'code_intro' => CODE_INTRODUCTION_CLIENT . randomNumber(),
            ]);

            $account_id = $this->db->insert_id();

            //insert account_info tb
            $this->account_info_model->create([
//                'address' => $address,
                'phone' => $phone_number,
                'short_name_phone' => $phone_code,
//                'birthday' => $birthday,
//                'gender' => $gender,
                'social_token' => $social_token,
                'account_id' => $account_id
            ]);

            //insert user tb
            $this->user_model->create([
                'social_type' => $social_type,
                'social_id' => $social_id,
                'username' => $social_id,
                'email' => $email,
                'last_login' => CURRENT_TIME,
                'account_id' => $account_id,
            ]);
        } else { // user exists => update

            if ($user->is_update_profile) {

                $account_id = $user->account_id;
                $crop_img_src = $user->crop_img_src;
                $img_src = $user->img_src;

                /*if ($user->status == NOT_ACTIVATED) {
                    $this->response(RestBadRequest(USER_IS_NOT_ACTIVATED_MSG), BAD_REQUEST_CODE);
                } else if ($user->status == BANNED) {
                    $this->response(RestBadRequest(USER_IS_BANNED_MSG), BAD_REQUEST_CODE);
                }
                if ($user->is_delete == true) {
                    $this->response(RestBadRequest(USER_IS_NOT_EXISTED_MSG), BAD_REQUEST_CODE);
                }*/

                //update user
                $this->user_model->update_by_condition([
                    '_id' => $user->_id
                ], [
                    'last_login' => CURRENT_TIME
                ]);
                //update account
                $this->account_model->update_by_condition([
                    '_id' => $user->account_id
                ], [
                    /*'first_name' => $first_name,
                    'last_name' => $last_name,*/
                    'fullname' => $fullname,
                    'plain_name' => skipVN($fullname, true),
                    'app_id' => $app_id,
                    'app_name' => $app_name,
                    'app_version' => $app_version,
                    'device_id' => $device_id,
                    'social_img_src' => $social_img_src,
                ]);
                //update account_info
                $this->account_info_model->update_by_condition([
                    'account_id' => $user->account_id
                ], [
                    'phone' => $phone_number,
                    'short_name_phone' => $phone_code,
//                    'address' => $address,
//                    'birthday' => $birthday,
//                    'gender' => $gender,
                    'social_token' => $social_token,
                    'update_time' => CURRENT_TIME,
                ]);
            }/*$user->is_update_profile*/
        }


        //insert account_jwt
        $jwt_generator = new JWT();
        $jwt = $jwt_generator::encode([
            'account_id' => $account_id,
            'app_id' => $app_id,
            'device_id' => $device_id,
            'time_mi' => CURRENT_MILLISECONDS,
        ], SERVER_KEY);

        $this->account_jwt_model->create([
            'jwt' => $jwt,
            'expire_time' => JWT_EXPIRE_TIME, // 7 days
        ]);

//        //get account_language_setting
//        $account_language_setting = $this->account_language_setting_model->findOne([
//            'account_language_setting.account_id' => $account_id,
//        ], [
//            'language._id',
//            'language.code',
//            'language.name'
//        ]);

        //upsert push_token
        $data_push_token = [
            'account_id' => $account_id,
            'apple_token' => $apple_token,
            'firebase_token' => $firebase_token,
            'device_id' => $device_id,
            'device_name' => $device_name,
            'device_version' => $device_version,
            'timezone' => $timezone,
            'app_version' => $app_version
        ];
        $this->upsertPushToken($data_push_token);

        $res = $this->responseUserAccountDatas($account_id, $jwt);

        //store user session
        $session_userdata = [
            'ACCOUNT_ID' => $account_id,
            'ACCOUNT_DATA' => json_encode($res, JSON_UNESCAPED_UNICODE)
        ];
        $this->session->set_userdata($session_userdata);

        $res = removeNullOfObject($res);
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function verify_phone
     * method: post
     * params: account id
     */
    function verify_phone_post()
    {
        $code_verify = strtoupper(trim($this->post('code_verify')));
        $phone_code = strtoupper(trim($this->post('phone_code')));
        $phone_number = trim($this->post('phone_number'));

        $type_client = USER_TYPE_CLIENT;

        $check_verify_params = checkVerifyParams([
            $code_verify,
            $phone_code,
            $phone_number,
        ]);
        if (!empty($check_verify_params)) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        //check phone exists
        $where = "
            (account_info.phone like '$phone_number' and account_info.phone is not null and account_info.phone <> '')           
            and user_get_type.type = '$type_client'
            and account_info.short_name_phone = '$phone_code'
        ";
        $phone_exists = $this->user_model->findOne($where, array('*'));
        if (!empty($phone_exists)) {
            $this->response(RestBadRequest(PHONE_IS_EXISTED_MSG), BAD_REQUEST_CODE);
        }

        //check verify_code
        $where = array(
            'code_verify' => $code_verify
        );
        $account = $this->account_model->findOne($where, array('account.*'));
        if (empty($account)) {
            $this->response(RestBadRequest(WRONG_CODE_VERIFY_MSG), BAD_REQUEST_CODE);
        }

        $account_id = $account->_id;

        //update account_info tb
        $this->account_info_model->update_by_condition(array(
            'account_id' => $account_id
        ), array(
            'short_name_phone' => $phone_code,
            'phone' => $phone_number,
        ));


        $this->account_model->update_by_condition(
            array('account._id' => $account_id),
            array(
                'is_verified' => true,
                'code_verify' => NULL,
            )
        );
        //$res = removeNullOfObject($this->responseUserAccountDatas($account_id, $jwt));
        $res = removeNullOfObject($this->responseUserAccountDatas($account_id));

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function check login
     * method: post
     * params: jwt
     */
    function check_login_post()
    {
        $jwt = $this->input->request_headers()['Authorization'];
        /* use for CI */
        if (!(strpos($jwt, 'Bearer') !== false)) {
            $this->response(RestForbidden(INVALID_TOKEN_MSG), FORBIDDEN_CODE);
        }
        /* end use for CI */
        $jwt = str_replace('Bearer ', '', $jwt);
        /*device info*/
        //dump($account_id,true);
        $device_id = $this->post('device_id');
        $device_name = $this->post('device_name');
        $device_version = $this->post('device_version');
        $app_id = $this->post('app_id');
        $app_name = $this->post('app_name');
        $app_version = $this->post('app_version');
        /*push token*/
        $apple_token = $this->post('apple_token');
        $firebase_token = $this->post('firebase_token');
        $timezone = $this->post('timezone');
//dump($account_id,true);
        //check token
        $where = [
            'jwt' => $jwt
        ];
        $account_jwt = $this->account_jwt_model->findOne($where);

        if (empty($account_jwt)) {
            $this->response(RestForbidden(INVALID_TOKEN_MSG), FORBIDDEN_CODE);
        }


        if ($account_jwt->expire_time < time()) { //extension expire_time
            $this->account_jwt_model->update_by_condition([
                'jwt' => $jwt
            ], [
                'expire_time' => JWT_EXPIRE_TIME
            ]);
        }

        //dump($account_jwt,true);
        $jwt_generator = new JWT();
        $jwt_decode = $jwt_generator::decode($jwt, SERVER_KEY);
        //dump($jwt_decode,true);

        $where = [

            'user.account_id' => $jwt_decode->account_id,
            'account.is_delete' => false,
        ];
        //dump($where,true);
        $select = array(
            'user.account_id',
            'user.username',
            'user.email',
            'user.social_type',
            'account.fullname',
            'account.crop_img_src',
            'account.img_src',
            'account.social_img_src',
            'account_info.address',
            'account_info.phone',
            'account_info.birthday',
            'account_info.gender',
            'account.is_active',
            'account.is_delete',
        );
        $user = $this->user_model->findOne($where, $select);
        //  dump($user,true);
        if (empty($user)) {
            $this->response(RestBadRequest(USER_IS_NOT_EXISTED_MSG), BAD_REQUEST_CODE);
        }


        //upsert push_token
        $data_push_token = [
            'account_id' => $user->account_id,
            'apple_token' => $apple_token,
            'firebase_token' => $firebase_token,
            'device_id' => $device_id,
            'device_name' => $device_name,
            'device_version' => $device_version,
            'timezone' => $timezone,
            'app_version' => $app_version
        ];
        $this->upsertPushToken($data_push_token);

        $res = $this->responseUserAccountDatas($user->account_id, $jwt);

        //store user session
        $session_userdata = [
            'ACCOUNT_ID' => $user->account_id,
            'ACCOUNT_DATA' => json_encode($res, JSON_UNESCAPED_UNICODE)
        ];
        $this->session->set_userdata($session_userdata);

        $res = removeNullOfObject($res);
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function logout
     * method: post
     * params:
     */
    function logout_post()
    {
        /*check session & jwt*/
        $account_id = $this->session->userdata('ACCOUNT_ID');
        if (empty($account_id)) {
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        $jwt = $this->input->request_headers()['Authorization'];
        if (!$this->checkVerifyJWT($jwt, $account_id)) {
            $this->response(RestForbidden(INVALID_TOKEN_MSG), FORBIDDEN_CODE);
        }
        /*end check session & jwt*/

        /*push token*/
        $apple_token = $this->post('apple_token');
        $firebase_token = $this->post('firebase_token');

        //delete account_jwt
        $where = array(
            'jwt' => str_replace('Bearer ', '', $jwt)
        );
        $this->account_jwt_model->delete_by_condition($where);

        //delete push_token
        $where = "
            (apple_token like '$apple_token' and apple_token is not null)
            or (firebase_token like '$firebase_token' and firebase_token is not null)
        ";
        $this->push_token_model->delete_by_condition($where);


        //delete user session
        $session_userdata = [
            'ACCOUNT_ID' => '',
            'ACCOUNT_DATA' => ''
        ];
        $this->session->set_userdata($session_userdata);

        $this->response(RestSuccess(), SUCCESS_CODE);

    }

    /*
     * function get captcha base64 image
     * method: get
     * params:
     */
    function get_captcha_get()
    {
        $res = $this->generateCaptchaBase64();
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function update profile
     * method: put
     * params: fullname, phone, address, ...
     */
    function update_profile_put()
    {

        $account_id = $this->checkSessionAndTokenAuth();
        $type_user = !empty($this->input->request_headers()['Type']) ? $this->input->request_headers()['Type'] : '';
        $email = $this->put('email');
        $fullname = $this->put('fullname');
        $phone_code = strtoupper($this->put('phone_code'));
        $phone_number = $this->put('phone_number');


        //get account current
        $where_account_current = array(
            'account._id' => $account_id,
        );
        $select_account_current = array(
            'account_info.phone'
        );
        $account_current = $this->account_model->findOne($where_account_current, $select_account_current);
        //check email exists
//        $where = "
//            (user.email like '$email' and user.email is not null and user.email <> '')
//            and user.account_id = $account_id
//            and user_get_type.type = '$type_user'
//        ";
//        $email_exists = $this->user_model->findOne($where, array('*'));
//        if (!empty($email_exists)) {
//            $this->response(RestBadRequest(EMAIL_IS_EXISTED_MSG), BAD_REQUEST_CODE);
//        }
        //check phone exists
        $where = "
            (user.phone like '$phone_number' and user.phone is not null and user.phone <> '' )
            and user.account_id = $account_id
            and user_get_type.type = '$type_user'
            and user.short_name_phone = '$phone_code'
        ";
        $phone_exists = $this->user_model->findOne($where, array('*'));
        if (!empty($phone_exists)) {
            $this->response(RestBadRequest(PHONE_IS_EXISTED_MSG), BAD_REQUEST_CODE);
        }
        $this->db->trans_start(); # Starting Transaction
        //update account tb
        $data_update_account = array(
            'fullname' => $fullname,
            'plain_name' => skipVN($fullname),
        );
        //change verify when update new phone
        if ($phone_number != $account_current->phone) {
            $data_update_account ['is_verified'] = false;
        }
        $this->account_model->update_by_condition(array(
            '_id' => $account_id,
        ), $data_update_account);

        //update account_info tb
        $data_update = array(
            'short_name_phone' => $phone_code,
            'phone' => $phone_number,
        );

        $this->account_info_model->update_by_condition(array(
            'account_id' => $account_id,
        ), $data_update);

        //update user tb
        $this->user_model->update_by_condition(array(
            'account_id' => $account_id,
        ), array(
            'email' => $email,
        ));
        $res = $this->responseUserAccountDatas($account_id);
        $this->db->trans_complete();
        $res = removeNullOfObject($res);
        $this->response(RestSuccess($res), SUCCESS_CODE);
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
        }
        else
        {
            $this->db->trans_commit();
        }
    }

    /*
     * function get user profile
     * method: get
     * params:
     */
    function get_profile_user_get()
    {
        $account_id = $this->checkSessionAndTokenAuth();
        $res = $this->responseUserAccountDatas($account_id);

        $res = removeNullOfObject($res);
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function introduce_list
     * method: post
     * params: file
     */
    function introduce_list_get()
    {
        $type_app = !empty($this->input->request_headers()['Type']) ? $this->input->request_headers()['Type'] : '';
        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_DEFAULT;
        if(in_array($type_app,array('client','counselors')) == FALSE){
            $this->response(RestBadRequest(WRONG_TYPE_USERNAME),BAD_REQUEST_CODE);
        }
        $account_id = $this->checkApplicationTokenAuth();
        //dump($account_id,true);
        $where_list_account = array(
            'account_register.account_introduction' => $account_id
        );
        $select_list_account = array(
            '*',
        );
        $list_account = $this->account_register_model->findOne($where_list_account, $select_list_account);
        //dump($list_account,true);
        if(empty($list_account)){
            $rs = [];
            $this->response(RestSuccess($rs), SUCCESS_CODE);
        }
        $where_account = array(
            'account_register.account_introduction' => $account_id
        );
        $select_account = array(
            'account._id',
            'account.code_intro',
            'account_info.short_name_phone',
            'account_info.phone'

        );
        $code_account = $this->account_register_model->get_panigate($where_account, $select_account,$limit,$offset);

        $rs = array();
        $rs ['code_intro'] = $list_account->code_introduction;
        $rs ['total_account'] = !empty($code_account) ? count($code_account) : 0;
        $rs['list_account'] = !empty($code_account) ? removeNullElementOfArray($code_account) : [];
        $this->response(RestSuccess($rs), SUCCESS_CODE);
    }

    /*
     * function introduce_list
     * method: post
     * params: file
     */
    function introduce_yourself_get()
    {
        $account_id = $this->checkApplicationTokenAuth();
        $where_account = array(
            'account_info.account_id' => $account_id
        );
        $select_account = array(
            'account_info.introduction',
        );
        $introduction_account = $this->account_model->findOne($where_account, $select_account);
        $rs = !empty($introduction_account) ? removeNullOfObject($introduction_account) : [];
        $this->response(RestSuccess($rs), SUCCESS_CODE);
    }
    /*
     * function upload_tmp_avatar
     * method: post
     * params: file
     */
//    function upload_tmp_avatar_post()
//    {
//        /*check session & jwt*/
//        $account_id = $this->session->userdata('ACCOUNT_ID');
//        if (empty($account_id)) {
//            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
//        }
//        $jwt = $this->input->request_headers()['Authorization'];
//        if (!$this->checkVerifyJWT($jwt, $account_id)) {
//            $this->response(RestForbidden(INVALID_TOKEN_MSG), FORBIDDEN_CODE);
//        }
//        /*end check session & jwt*/
//
//        $check_verify_params = checkVerifyParams([
//            $_FILES['crop_file']['name'],
//            $_FILES['file']['name'],
//        ]);
//        if(!empty($check_verify_params)){
//            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
//        }
//
//        //upload crop_img_src
//        $img_path = $_FILES['crop_file']['name'];
//        $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
//        $img_name = cleanFileName(str_replace($img_path_ext, '', $img_path));
//        if (!is_dir(TMP_FOLDER_AVATAR)) {
//            mkdir('./' . TMP_FOLDER_AVATAR, 0777, TRUE);
//        }
//        $config['upload_path'] = './'. TMP_FOLDER_AVATAR;
//        $config['allowed_types'] = 'jpg|jpeg|png';
//        $file_name = strtolower('cr_'.strtotime('now') .'_'. $img_name .'.'. $img_path_ext);
//        $config['file_name'] = $file_name;
//
//        //Load upload library and initialize configuration
//        $this->load->library('upload', $config);
//        $this->upload->initialize($config);
//
//        $res = array();
//        if ($this->upload->do_upload('crop_file')) {
//            $res['crop_img_src'] = TMP_FOLDER_AVATAR .'/'. $config['file_name'];
//        }
//        else{
//            $this->response(RestServerError(), SERVER_ERROR_CODE);
//        }
//
//        //upload img_src
//        $img_path = $_FILES['file']['name'];
//        $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
//        $img_name = cleanFileName(str_replace($img_path_ext, '', $img_path));
//        if (!is_dir(TMP_FOLDER_AVATAR)) {
//            mkdir('./' . TMP_FOLDER_AVATAR, 0777, TRUE);
//        }
//        $config['upload_path'] = './'. TMP_FOLDER_AVATAR;
//        $config['allowed_types'] = 'jpg|jpeg|png';
//        $file_name = strtolower(strtotime('now') .'_'. $img_name .'.'. $img_path_ext);
//        $config['file_name'] = $file_name;
//
//        //Load upload library and initialize configuration
//        $this->load->library('upload', $config);
//        $this->upload->initialize($config);
//
//        if ($this->upload->do_upload('file')) {
//            $res['img_src'] = TMP_FOLDER_AVATAR .'/'. $config['file_name'];
//        }
//        else{
//            $this->response(RestServerError(), SERVER_ERROR_CODE);
//        }
//
//        $this->response(RestSuccess($res), SUCCESS_CODE);
//    }
//
//    /*
//     * function update avatar
//     * method: put
//     * params:
//     */
//    function upload_avatar_put()
//    {
//        /*check session & jwt*/
//        $account_id = $this->session->userdata('ACCOUNT_ID');
//        if (empty($account_id)) {
//            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
//        }
//        $jwt = $this->input->request_headers()['Authorization'];
//        if (!$this->checkVerifyJWT($jwt, $account_id)) {
//            $this->response(RestForbidden(INVALID_TOKEN_MSG), FORBIDDEN_CODE);
//        }
//        /*end check session & jwt*/
//
//        $crop_img_src = $this->put('crop_img_src');
//        $img_src = $this->put('img_src');
//
//        $check_verify_params = checkVerifyParams([
//            $crop_img_src,
//            $img_src,
//        ]);
//        if(!empty($check_verify_params)){
//            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
//        }
//
//        $where = array(
//            'account._id' => $account_id,
//        );
//        $account = $this->account_model->findOne($where, '*');
//
//        if(!file_exists(UPLOAD_PATH. $crop_img_src) || !file_exists(UPLOAD_PATH. $img_src)) {
//            $this->response(RestBadRequest(FILE_IS_NOT_EXISTED_MSG), BAD_REQUEST_CODE);
//        }
//
//        //delete old avatar
//        if(file_exists(UPLOAD_PATH. $account->crop_img_src)) {
//            unlink(UPLOAD_PATH . $account->crop_img_src);
//        }
//        if(file_exists(UPLOAD_PATH. $account->img_src)) {
//            unlink(UPLOAD_PATH . $account->img_src);
//        }
//
//        /*crop_img_src*/
//        $arr_img_src = explode('/', $crop_img_src);
//        $file_name = $arr_img_src[count($arr_img_src) - 1];
//
//        //move img_src from tmp to real folder
//        $new_dir = FOLDER_IMG_UPLOAD .'/user/'. hash256($account_id). '/avatar';
//        if (!is_dir($new_dir)) {
//            mkdir('./' . $new_dir, 0777, TRUE);
//        }
//        $new_crop_img_src = $new_dir. '/'. $file_name;
//        $new_crop_img_path = UPLOAD_PATH . $new_crop_img_src;
//        rename(UPLOAD_PATH. $img_src, $new_crop_img_path);
//        /*end crop_img_src*/
//
//        /*img_src*/
//        $arr_img_src = explode('/', $img_src);
//        $file_name = $arr_img_src[count($arr_img_src) - 1];
//
//        //move img_src from tmp to real folder
//        $new_dir = FOLDER_IMG_UPLOAD .'/user/'. hash256($account_id). '/avatar';
//        if (!is_dir($new_dir)) {
//            mkdir('./' . $new_dir, 0777, TRUE);
//        }
//        $new_img_src = $new_dir. '/'. $file_name;
//        $new_img_path = UPLOAD_PATH . $new_img_src;
//        rename(UPLOAD_PATH. $img_src, $new_img_path);
//        /*end img_src*/
//
//        //update account tb
//        $this->account_model->update_by_condition(array(
//            '_id' => $account_id,
//        ), array(
//            'crop_img_src' => $new_crop_img_src,
//            'img_src' => $new_img_src,
//        ));
//
//        $res = array(
//            'crop_img_src' => $new_crop_img_src,
//            'img_src' => $new_img_src,
//        );
//        $this->response(RestSuccess($res), SUCCESS_CODE);
//    }

    /*
     * function update avatar
     * method: post
     * params:
     */

    function upload_avatar_post()
    {
        //$this->post()
        $account_id = $this->checkSessionAndTokenAuth();


        if (empty($_FILES['file']['name']) || empty($_FILES['crop_file']['name'])) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $upload_dir = FOLDER_IMG_UPLOAD . '/user/' . hash256($account_id) . '/avatar';
        if (!is_dir($upload_dir)) {
            mkdir('./' . $upload_dir, 0777, TRUE);
        }

        $crop_img_src = '';
        $img_src = '';

        if (!empty($_FILES['crop_file']['name'])) {
            //upload crop_img_src
            $img_path = $_FILES['crop_file']['name'];
            $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
            $img_name = cleanFileName(str_replace($img_path_ext, '', $img_path));
            $config['upload_path'] = './' . $upload_dir;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $file_name = strtolower('cr_' . strtotime('now') . '_' . $img_name . '.' . $img_path_ext);
            $config['file_name'] = $file_name;

            //Load upload library and initialize configuration
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            $res = array();
            if ($this->upload->do_upload('crop_file')) {
                $crop_img_src = $upload_dir . '/' . $config['file_name'];
            } else {
                $this->response(RestServerError(), SERVER_ERROR_CODE);
            }
        }

        if (!empty($_FILES['file']['name'])) {
            //upload img_src
            $img_path = $_FILES['file']['name'];
            $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
            $img_name = cleanFileName(str_replace($img_path_ext, '', $img_path));
            $config['upload_path'] = './' . $upload_dir;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $file_name = strtolower(strtotime('now') . '_' . $img_name . '.' . $img_path_ext);
            $config['file_name'] = $file_name;

            //Load upload library and initialize configuration
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('file')) {
                $img_src = $upload_dir . '/' . $config['file_name'];
            } else {
                $this->response(RestServerError(), SERVER_ERROR_CODE);
            }
        }

        $where = array(
            'account._id' => $account_id,
        );
        $account = $this->account_model->findOne($where, '*');

        $data_update = array();
        //delete old avatar
        if (!empty($crop_img_src)) {
            if (!empty($account->crop_img_src)) {
                if (file_exists(UPLOAD_PATH . $account->crop_img_src)) {
                    unlink(UPLOAD_PATH . $account->crop_img_src);
                }
            }
            $data_update['crop_img_src'] = $crop_img_src;
        }
        if (!empty($img_src)) {
            if (!empty($account->img_src)) {
                if (file_exists(UPLOAD_PATH . $account->img_src)) {
                    unlink(UPLOAD_PATH . $account->img_src);
                }
            }
            $data_update['img_src'] = $img_src;
        }

        if (!empty($data_update) && count($data_update)) {
            //update account tb
            $this->account_model->update_by_condition(array(
                '_id' => $account_id,
            ), $data_update);
        }

        $res = array(
            'crop_img_src' => $crop_img_src,
            'img_src' => $img_src,
        );

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function change password
     * method: put
     * params:
     */
    function change_password_put()
    {
        /*check session & jwt*/
        $account_id = $this->checkSessionAndTokenAuth();
        $jwt = $this->input->request_headers()['Authorization'];

        $current_password = $this->put('current_password');
        $new_password = $this->put('new_password');

        $check_verify_params = checkVerifyParams([
            $current_password,
            $new_password,
        ]);
        if (!empty($check_verify_params)) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $where = "
            user.account_id like '$account_id'
            and (password like '$current_password' or (password_reset like '$current_password' and password_reset is not null))
        ";
        $user = $this->user_model->get_first_row($where, 'user.*');
        if (empty($user)) {
            $this->response(RestBadRequest(INVALID_CURRENT_PASSWORD_MSG), BAD_REQUEST_CODE);
        }

        $this->user_model->update_by_condition(array(
            '_id' => $user->_id
        ), array(
            'password' => $new_password,
            'password_reset' => ''
        ));
        //delete account_jwt
//        $where = array(
//            'jwt' => str_replace('Bearer ', '', $jwt)
//        );
        $jwt = str_replace('Bearer ', '', $jwt);
        $jwt_decode = new JWT();

        $account_jwt = $jwt_decode::decode($jwt,SERVER_KEY);
        $select_account_jwt = (
        '*'
        );
        $data_account_jwt = $this->account_jwt_model->get_total($select_account_jwt);
        if(!empty($data_account_jwt)) {
            foreach ($data_account_jwt as $data) {
                $jwt = str_replace('Bearer ', '', $data->jwt);
                $jwt_decode = new JWT();

                $data_jwt = $jwt_decode::decode($jwt, SERVER_KEY);

                if ($data_jwt->account_id == $account_jwt->account_id) {
                    $where = array(
                        '_id' => $data->_id,
                    );
                    $this->account_jwt_model->delete_by_condition($where);
                }
            }
        }else{
            $this->response(RestBadRequest(WRONG_EMPTY_DATA_TOKEN),BAD_REQUEST_CODE);
        }


        //$this->account_jwt_model->delete_by_condition($where);

        //delete push_token
        $where = "
            account_id = '$account_id'           
        ";
        $this->push_token_model->delete_by_condition($where);
//        if($user->password_reset == $password){ //remove password_reset
//            $data_update['password_reset'] = '';
//        }

        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    /*
     * function forgot password
     * method: post
     * params: email
     */
    function forgot_password_post()
    {
        $email = $this->post('email');

        $check_verify_params = checkVerifyParams([
            $email,
        ]);
        if (!empty($check_verify_params)) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $where = array(
            'user.email' => $email,
            'account.is_delete' => false,
        );
        $select = array(
            'user.*',
            'account.fullname'
        );
        $user = $this->user_model->findOne($where, $select);

        if (empty($user)) {
            $this->response(RestBadRequest(USER_IS_NOT_EXISTED_MSG), BAD_REQUEST_CODE);
        }

        //generate new_password
        $new_password = strtolower(generateReferenceCode(0, 6));
        $this->user_model->update_by_condition(array(
            'email' => $email
        ), array(
            'password_reset' => hash256($new_password),
        ));

        //send email
        $data = array(
            'user' => $user,
            'new_password' => $new_password
        );
        $subject = '[' . APP_TITLE_SHORT . '] Reset password';
        $message = $this->load->view('front/template/email/forgot_password', $data, TRUE);
//        $this->sendEmail(FROM_EMAIL, TO_EMAIL, $subject, $message);
        $this->sendEmail(FROM_EMAIL, $email, $subject, $message);
        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    function get_notification_post()
    {
        $keyword = !empty($this->post('search')['value']) ? $this->post('search')['value'] : '';
        $offset = !empty($this->post('start')) ? $this->post('start') : 0;
        $limit = !empty($this->post('length')) ? $this->post('length') : LIMIT_DEFAULT;
        $screen = $this->post('screen');
        $account_id = $this->post('account_id');
        $check_verify_params = checkVerifyParams([
            $screen,
            $account_id
        ]);
        if (!empty($check_verify_params)) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }


        $where = array(
            'notification.screen' => $screen,
            'notification.is_delete' => false,
            'notification._title  like' => '%' . skipVN(trim($keyword), true) . '%',

        );
        $select = array(
            'notification.*',
            'account.fullname',
        );
        $notifications = $this->notification_model->get_pagination($where, $select, $offset, $limit);
        foreach ($notifications as $value) {
//            if (!empty($value->department_ids)) {
//                $value->department_ids = str_replace('[', '', $value->department_ids);
//                $value->department_ids = str_replace(']', '', $value->department_ids);
//                // $value->department_ids=  str_replace( "|", '', $value->department_ids );
//                $value->department_ids = explode(',', $value->department_ids);
//
//                /* $arr_merge_de=array();
//                 if (!empty($value->department_ids)) {
//                     foreach ($value->department_ids as $value_department) {
//                         $where = "department._id =".$value_department;
//                         $mang = $this->department_model->find($where,"*");
//                         $arr_merge_de = array_merge($arr_merge_de, $mang);
//                     }
//                 }
//                 dump($arr_merge_de,true);*/
//                $value->department_ids = count($value->department_ids);
//
//            }
//            if (!empty($value->position_ids)) {
//                $value->position_ids = str_replace('[', '', $value->position_ids);
//                $value->position_ids = str_replace(']', '', $value->position_ids);
//                // $value->department_ids=  str_replace( "|", '', $value->department_ids );
//                $value->position_ids = explode(',', $value->position_ids);
//                $value->position_ids = count($value->position_ids);
//            }
            if (!empty($value->account_ids)) {
                $value->account_ids = str_replace('[', '', $value->account_ids);
                $value->account_ids = str_replace(']', '', $value->account_ids);
                // $value->department_ids=  str_replace( "|", '', $value->department_ids );
                $value->account_ids = explode(',', $value->account_ids);
                $value->account_ids = count($value->account_ids);
            }
            // $count=0;
            /* if (!empty($value->department_ids)){
                 foreach ($value->department_ids as $key){
                     //$count++;
                     $test_array[]=$key;
                 }
             }
             $value->department_ids=$test_array;*/

        }
        $where_count = array(
            'notification.is_delete' => false
        );

        $total_records = $this->notification_model->count_total($where_count);
        $rs = [
            'status' => SUCCESS_CODE,
            'message' => OK_MSG,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => !empty($notifications) ? $notifications : [],
        ];
        $this->response($rs, SUCCESS_CODE);


    }

    /*
   * function add favorite
   * method: post
   * params:
     * Modify : Nam.Pham
     * Date : 2018/12/11
   */
    function add_favorite_post()
    {
        $account_id = $this->checkApplicationTokenAuth();
        $type = $this->post('type');
        $project_id = $this->post('project_id');
        $sale_id = $this->post('sale_id');
        $check_verify_params = checkVerifyParams([
            $type,
        ]);
        if (!empty($check_verify_params)) {

            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }
        if ($type == TYPE_FAVORITE_PROJECT) {
            $check_verify_params = checkVerifyParams([
                $project_id,
            ]);
            if (!empty($check_verify_params)) {

                $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
            }
            $where_project = array(
                'account_id' => $account_id,
                'project_id' => $project_id
            );
            $project_favor = $this->favorite_model->get_first_row($where_project);
            $project_exist = $this->project_model->get_first_row(array('project._id' => $project_id));


            if (empty($project_favor)) {
                $this->favorite_model->create(
                    array(
                        'account_id' => $account_id,
                        'project_id' => $project_id,
                        'create_time' => CURRENT_DATE
                    ));

                //update total favorite
                $this->project_model->update_by_condition(
                    array('_id' => $project_id),
                    array(
                        'total_favorite' => $project_exist->total_favorite + 1
                    ));

            } else {
                $this->favorite_model->delete_by_condition($where_project);
                //update total favorite
                $this->project_model->update_by_condition(
                    array('_id' => $project_id),
                    array(
                        'total_favorite' => $project_exist->total_favorite - 1
                    ));
            }
        } else {
            $check_verify_params = checkVerifyParams([
                $sale_id,
            ]);
            if (!empty($check_verify_params)) {
                $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
            }
            $where_sale = array(
                'account_id' => $account_id,
                'seller_id' => $sale_id
            );
            $sale_favor = $this->favorite_model->get_first_row($where_sale);
            $sale_exist = $this->account_info_model->get_first_row(array('account_id' => $account_id));
            if (empty($sale_favor)) {
                $this->favorite_model->create(
                    array(
                        'account_id' => $account_id,
                        'seller_id' => $sale_id,
                        'create_time' => CURRENT_DATE
                    ));
                //update total favorite
                $this->account_info_model->update_by_condition(
                    array('account_id' => $account_id),
                    array(
                        'total_favorite' => $sale_exist->total_favorite + 1
                    ));
            } else {
                $this->favorite_model->delete_by_condition($where_sale);
                //update total favorite
                $this->account_info_model->update_by_condition(
                    array('account_id' => $account_id),
                    array(
                        'total_favorite' => $sale_exist->total_favorite - 1
                    ));
            }
        }
        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    /*
   * function favorite list
   * method: post
   * params:
   */
    function favorite_list_post()
    {
        $account_id = $this->checkApplicationTokenAuth();
        $type = $this->post('type');
        $offset = $this->post('offset');
        $limit = $this->post('limit');
        $check_verify_params = checkVerifyParams([
            $type,
        ]);
        if (!empty($check_verify_params)) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }
        if ($type == TYPE_FAVORITE_PROJECT) {

            $where_project = array(
                'account_id' => $account_id,
                'favorite.project_id <>' => null
            );
            $select_project = array(
                'project._id',
                'project.img_src',
                'project.name',
                'project.type',
                'project.address',
                'investor.name as investor',
                'project_type.name as project_type',
                'project_has_type.duration_price',
            );
            $favorite = $this->favorite_model->get_pagination_project($where_project, $select_project, $offset, $limit);
            if (!empty($favorite) && count($favorite)) {
                foreach ($favorite as $project) {
                    $duration_price = array();
                    if(strpos($project->duration_price,",") !==false){
                        $array_duration_price = explode(",",$project->duration_price);
                        foreach ($array_duration_price as $data){
                            $data_array= explode("-",$data);
                            $duration_price['from'] = $data_array[0];
                            $duration_price['to'] = $data_array[1];
                            $project->data_duration_price[] = $duration_price;
                        }
                    }else{
                        $array_duration_price = explode("-",$project->duration_price);
                        $duration_price['from'] = $array_duration_price[0];
                        $duration_price['to'] = $array_duration_price[1];
                        $project->data_duration_price[] = $duration_price;

                    }
                    //dump($project->img_src ,true);
                    if(!empty($project->img_src)) {
                        list($width, $height) = getimagesize($project->img_src);
                        $project->width = $width;
                        $project->height = $height;
                    }
                    $where_favorite = array(
                        'project_id' => $project->_id,
                        'account_id' =>$account_id
                    );
                    $favorite_account = $this->favorite_model->get_first_row($where_favorite);
                    if (!empty($favorite_account)) {
                        $project->is_favorite = 1;
                    }
                }
            }
            //dump($favorite,true);
        } else {
            //	dum
            $where_sale = array(
                'favorite.account_id' => $account_id,
                'favorite.seller_id <>' => null
            );
            $select_sale = array(
                'account._id',
                'account.fullname',
                'account.img_src',
                'account.social_img_src',
                'account_info.total_favorite',
                'account_info.avg_rating',
                'account_info.achievement_1',
                'account_info.achievement_2',
                'account_info.point2rank',
                'account_info.introduction'
            );
            $favorite = $this->favorite_model->get_pagination_sale($where_sale, $select_sale, $offset, $limit);

            if(!empty($favorite)){
                foreach ($favorite as $fa){
                    $where_rating = array(
                        "rating.seller_id"=>$fa->_id
                    );
                    $count_rating = $this->rating_model->count_total($where_rating);
                    //dump($rank_data1);
                    $fa->rating = $count_rating;
                    $select_rank =  array(
                        'rank.point',
                        'rank.name',
                        'rank._key'

                    );
                    $where_rank = [
                        'type' => TYPE_COUNSELORS
                    ];
                    $rank_data = $this->rank_model->find($where_rank,$select_rank);
                    foreach ($rank_data as $rank){
                        if(!empty($rank->point <= $fa->point2rank)){

                            $fa->rank_name = $rank->name;
                            $fa->rank_key = $rank->_key;
                            break;
                        }
                    }
                    $where_favorite = array(
                        'seller_id' => $fa->_id,
                        'account_id' => $account_id
                    );
                    $favorite_acccount = $this->favorite_model->get_first_row($where_favorite);
                    if (!empty($favorite_acccount)) {
                        $fa->is_favorite = 1;
                    }
                    $count_rating = $this->rating_model->count_total($where_rating);
                    //dump($rank_data1);
                    $fa->rating = $count_rating;
                }
            }

            //dump($favorite,true);
        }
        $rs = !empty($favorite) ? removeNullElementOfArray($favorite) : [];
        $this->response(RestSuccess($rs), SUCCESS_CODE);
    }

    /*
    * function get user list
    * method: post
    * params: offset, limit
    */
    function get_salary_get(){
        $account_id = $this->checkApplicationTokenAuth();
        $where = array('account_id' => $account_id);
        $select = array(
            'total_price',
            'not_received_price'
        );
        $salary = $this->salary_model->findOne($where,$select);
        $this->response(RestSuccess(!empty($salary)?removeNullOfObject($salary):'',SUCCESS_CODE));
    }

    /*
    * function get sale list
    * method: post
    * params: offset, limit
     * If account no login is_favorite = 0
     * Else account login if user favorite show is_favorite = 1 else is_favorite = 0
    */
    function get_sale_list_post(){
        $account_id = $this->checkApplicationTokenAuth_Nologin();
        $offset = $this->post('offset');
        $limit = $this->post('limit');

        $where_sale = array(
            'user_get_type.type' => USER_TYPE_SALE,
            'account.is_active' => true,
            'account.is_delete' => false,
        );
        $select_sale = array(
            'account._id',
            'account.img_src',
            'account.fullname',
            'account_info.point2rank',
            'account_info.total_favorite',
            'account_info.avg_rating',
            'account_info.achievement_1',
            'account_info.achievement_2',
        );
        $sale = $this->account_model->get_pagination($where_sale,$select_sale,$offset,$limit);

        $select_rank =  array(
            'rank.point',
            'rank.name',
            'rank._key'

        );
        $where_rank = [
            'type' => TYPE_COUNSELORS
        ];
        $rank_data = $this->rank_model->find($where_rank,$select_rank);

        foreach($sale as $sl){
            foreach ($rank_data as $rank){
                if(!empty($rank->point <= $sl->point2rank)){
                    $sl->rank_name = $rank->name;
                    $sl->key_rank = $rank->_key;
                    break;
                }
            }
            if(empty($account_id)){
                ///dump("dsadsaasd",true);
                $sl->is_favorite = 0;
            }else{
                $where_favorite = array(
                    'seller_id' => $sl->_id,
                    'account_id' =>$account_id
                );
                $favorite = $this->favorite_model->get_first_row($where_favorite);
                if (!empty($favorite)) {
                    $sl->is_favorite = 1;
                }
            }

//			$where_rating = array(
//				"rating.seller_id"=>$sl->_id
//			);
            //
            $where_rating = array(
                "rating.seller_id"=>$sl->_id
            );
            $count_rating = $this->rating_model->count_total($where_rating);
            //dump($rank_data1);
            $sl->rating = $count_rating;
        }

        $this->response(RestSuccess(!empty($sale)?removeNullElementOfArray($sale):[]));

    }

    /*
    * function get user list
    * method: post
    * params: offset, limit
    */
    function get_user_list_post()
    {
        $role = !empty($this->post('role')) ? $this->post('role') : '';
        $sess_token = !empty($this->post('token')) ? $this->post('token') : '';


        if ($this->checkAdminAPI($role, $sess_token) || CHECK_POSTMAN) { // admin
            $keyword = !empty($this->post('search')['value']) ? $this->post('search')['value'] : '';
            $offset = !empty($this->post('start')) ? $this->post('start') : 0;
            $current_page = !empty($this->post('current_page')) ? $this->post('current_page') : 1;
            //dump($current_page,true);
            $draw = !empty($this->post('draw')) ? $this->post('draw') : 1;
            if ($current_page != 1 && $offset == 0 && $draw == 1) {
                $offset = ($current_page - 1) * LIMIT_DEFAULT;
            }
            $limit = !empty($this->post('length')) ? $this->post('length') : LIMIT_DEFAULT;
            $where = array(
                'account.is_delete' => false,
                'account.plain_name like' => '%' . skipVN($keyword, true) . '%',
            );
            $select = array(
                'account.*',
                'user.username',
                'user.email',
                'user.social_type',
                'user.last_login',
                'account_info.address',
                'account_info.phone',
                'account_info.gender',
                'account_info.birthday',
                'account_info.purpose_stay',
            );
            $account = $this->account_model->get_pagination($where, $select, $offset, $limit);
            $total_records = $this->account_model->count_total($where);
            $rs = [
                'status' => SUCCESS_CODE,
                'message' => OK_MSG,
                'recordsTotal' => $total_records,
                'recordsFiltered' => $total_records,
                'data' => !empty($account) ? $account : [],
            ];
            $this->response($rs, SUCCESS_CODE);
        }
        else {

        }
    }

    /*
     * function add user
     * method: post
     * params: username, password, email, ...
     */
    function add_new_user_post()
    {
        $role = !empty($this->post('role')) ? $this->post('role') : '';
        $sess_token = !empty($this->post('token')) ? $this->post('token') : '';


        if ($this->checkAdminAPI($role, $sess_token) || CHECK_POSTMAN) { // admin
            /*user info*/
            $username = $this->post('username');
            $password = hash256($this->post('password'));
            $email = $this->post('email');
            $gender = !empty($this->post('gender')) ? strtolower($this->post('gender')) : GENDER_DEFAULT;
            $phone = $this->post('phone');
            $birthday = str_replace('/', '-', $this->post('birthday'));
            $birthday = !empty($this->post('birthday')) ? date('Y-m-d', strtotime($birthday)) : null;
            /*$last_name = $this->post('last_name');
            $first_name = $this->post('first_name');*/
            $fullname = $this->post('fullname');
            $address = $this->post('address');
            $province_id = !empty($this->post('province_id')) ? $this->post('province_id') : null;
            $purpose_stay = $this->post('purpose_stay');

            if (empty($email)) {
                $this->response(RestBadRequest(EMAIL_IS_EMPTY_MSG), SUCCESS_CODE);
            }

            $check_verify_params = checkVerifyParams(array(
                $username,
                $password,
                $fullname,
            ));

            if (!empty($check_verify_params)) {
                $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
            }

            //check email exists
            $where = array(
                'email' => $email
            );
            $email_exists = $this->user_model->get_first_row($where);
            if (!empty($email_exists)) {
                $this->response(RestBadRequest(EMAIL_IS_EXISTED_MSG), BAD_REQUEST_CODE);
            }
            //check birthday
            if (!empty($birthday)) {
                if (DateTime::createFromFormat('Y-m-d', $birthday) == FALSE) {
                    $this->response(RestBadRequest(DAY_FALSE_MSG), BAD_REQUEST_CODE);
                }
                if ($birthday > CURRENT_TIME) {
                    $this->response(RestBadRequest(DAY_FUTURE_MSG), BAD_REQUEST_CODE);
                }
            }

            //check username exists
            $where = array(
                'username' => $username
            );
            $user_exists = $this->user_model->get_first_row($where);
            if (!empty($user_exists)) {
                $this->response(RestBadRequest(USER_EXISTED_MSG), BAD_REQUEST_CODE);
            }

            //insert account tb
            $this->account_model->create(array(
                'fullname' => $fullname,
                'province_id' => $province_id,
                'plain_name' => skipVN($fullname, true),
                'create_time_mi' => CURRENT_MILLISECONDS,
                'create_time' => CURRENT_TIME,
                'update_time' => CURRENT_TIME,
            ));

            $account_id = $this->db->insert_id();

            //insert account_info tb
            $this->account_info_model->create(array(
                'address' => $address,
                'phone' => $phone,
                'purpose_stay' => $purpose_stay,
                'birthday' => !empty($birthday) ? date('Y-m-d', strtotime($birthday)) : '',
                'gender' => $gender,
                'account_id' => $account_id
            ));

            //insert user tb
            $this->user_model->create(array(
                'username' => $username,
                'password' => $password,
                'email' => $email,
                'last_login' => CURRENT_TIME,
                'account_id' => $account_id,
            ));

            if (!empty($_FILES['img_src']['name'])) {

                $upload_dir = FOLDER_IMG_UPLOAD . '/user/' . hash256($account_id) . '/avatar';
                if (!is_dir($upload_dir)) {
                    mkdir('./' . $upload_dir, 0777, TRUE);
                }

                //upload img_src
                $img_path = $_FILES['img_src']['name'];
                $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
                $img_name = cleanFileName(str_replace($img_path_ext, '', $img_path));
                $config['upload_path'] = './' . $upload_dir;
                $config['allowed_types'] = 'jpg|jpeg|png';
                $file_name = strtolower(strtotime('now') . '_' . $img_name . '.' . $img_path_ext);
                $config['file_name'] = $file_name;

                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('img_src')) {
                    //create crop_img_src
                    $img_src = $upload_dir . '/' . $config['file_name'];
                    $crop_img_src = $upload_dir . '/cr_' . $config['file_name'];
                    resizeImage($img_src, null, '240', '300', false, $crop_img_src, false, false, 100);

                    $this->account_model->update_by_condition(array(
                        '_id' => $account_id
                    ), array(
                        'img_src' => $img_src,
                        'crop_img_src' => $crop_img_src,
                    ));
                } else {
                    //server err
                    $this->response(RestServerError(), BAD_REQUEST_CODE);
                }
            } /* !empty($_FILES['img_src']['name'] */
            $this->response(RestSuccess(), SUCCESS_CODE);
        } else {
            //API
        }
    }

    function edit_user_post()
    {

        $role = !empty($this->post('role')) ? $this->post('role') : '';
        $sess_token = !empty($this->post('token')) ? $this->post('token') : '';


        if ($this->checkAdminAPI($role, $sess_token) || CHECK_POSTMAN) { // admin
            //dump('ád',true);
            /*user info*/
            $account_id = $this->post('account_id');
            $password = $this->post('password');
            $email = $this->post('email');
            $gender = !empty($this->post('gender')) ? strtolower($this->post('gender')) : GENDER_DEFAULT;
            //$phone = $this->post('phone');
            $birthday = str_replace('/', '-', $this->post('birthday'));
            $birthday = !empty($this->post('birthday')) ? date('Y-m-d', strtotime($birthday)) : '';
            /*$last_name = $this->post('last_name');
            $first_name = $this->post('first_name');*/
            $fullname = $this->post('fullname');
            $address = $this->post('address');
            $province_id = $this->post('province_id');
            $purpose_stay = $this->post('purpose_stay');
            $check_verify_params = checkVerifyParams(array(
                $account_id,
            ));

            if (!empty($check_verify_params)) {
                $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
            }

            //get current user
            $where = array(
                'user.account_id' => $account_id
            );
            $current_user = $this->user_model->findOne($where, 'user.*');
            //check birthday
            if (!empty($birthday)) {
                if (DateTime::createFromFormat('Y-m-d', $birthday) == FALSE) {
                    $this->response(RestBadRequest(DAY_FALSE_MSG), BAD_REQUEST_CODE);
                }
                if ($birthday > CURRENT_TIME) {
                    $this->response(RestBadRequest(DAY_FUTURE_MSG), BAD_REQUEST_CODE);
                }
            }

            //check email exists
            $where = array(
                'email' => $email,
                'email <>' => $current_user->email,
            );
            $email_exists = $this->user_model->get_first_row($where);
            if (!empty($email_exists)) {
                $this->response(RestBadRequest(EMAIL_IS_EXISTED_MSG), SUCCESS_CODE);
            }

            $user_exists = $this->user_model->get_first_row($where);
            if (!empty($user_exists)) {
                $this->response(RestBadRequest(USER_EXISTED_MSG), BAD_REQUEST_CODE);
            }

            $where = array(
                'account.is_delete' => false
            );
            $account = $this->account_model->findOne($where, '*');
            if (empty($account)) {
                $this->response(RestNotFound(), NOT_FOUND_CODE);
            }


            //update account tb
            $this->account_model->update_by_condition(array(
                '_id' => $account_id
            ), array(
                'fullname' => $fullname,
                'province_id' => $province_id,
                'plain_name' => skipVN($fullname, true),
                'create_time_mi' => CURRENT_MILLISECONDS,
                'create_time' => CURRENT_TIME,
                'update_time' => CURRENT_TIME,
            ));


            //update account_info tb
            $data_update_user = array(
                'address' => $address,
                //'phone' => $phone,
                'purpose_stay' => $purpose_stay,
                'birthday' => !empty($birthday) ? date('Y-m-d', strtotime($birthday)) : '',
                'gender' => $gender,
            );

            $this->account_info_model->update_by_condition(array(
                '_id' => $account_id
            ), $data_update_user);

            //update user tb
            $data_update_user = array(
                'email' => $email,
                'last_login' => CURRENT_TIME,
            );

            if (!empty($password)) {
                $data_update_user['password'] = hash256($password);
            }

            $this->user_model->update_by_condition(array(
                'account_id' => $account_id
            ), $data_update_user);

            $upload_dir = FOLDER_IMG_UPLOAD . '/user/' . hash256($account_id) . '/avatar';
            if (!is_dir($upload_dir)) {
                mkdir('./' . $upload_dir, 0777, TRUE);
            }

            //upload img_src
            if (!empty($_FILES['img_src']['name'])) {

                $upload_dir = FOLDER_IMG_UPLOAD . '/user/' . hash256($account_id) . '/avatar';
                if (!is_dir($upload_dir)) {
                    mkdir('./' . $upload_dir, 0777, TRUE);
                }

                //upload img_src
                $img_path = $_FILES['img_src']['name'];
                $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
                $img_name = cleanFileName(str_replace($img_path_ext, '', $img_path));
                $config['upload_path'] = './' . $upload_dir;
                $config['allowed_types'] = 'jpg|jpeg|png';
                $file_name = strtolower(strtotime('now') . '_' . $img_name . '.' . $img_path_ext);
                $config['file_name'] = $file_name;

                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                //delete old original image file
                if (!empty($account->crop_img_src)) {
                    if (file_exists(UPLOAD_PATH . $account->img_src)) {
                        unlink(UPLOAD_PATH . $account->img_src);
                    }
                }

                //delete old crop image file
                if (!empty($account->crop_img_src)) {
                    if (file_exists(UPLOAD_PATH . $account->crop_img_src)) {
                        unlink(UPLOAD_PATH . $account->crop_img_src);
                    }
                }

                if ($this->upload->do_upload('img_src')) {
                    //create crop_img_src
                    $img_src = $upload_dir . '/' . $config['file_name'];
                    $crop_img_src = $upload_dir . '/cr_' . $config['file_name'];
                    resizeImage($img_src, null, '240', '300', false, $crop_img_src, false, false, 100);

                    $this->account_model->update_by_condition(array(
                        '_id' => $account_id
                    ), array(
                        'img_src' => $img_src,
                        'crop_img_src' => $crop_img_src,
                    ));


                } else {
                    //server err
                    $this->response(RestServerError(), BAD_REQUEST_CODE);
                }
            } /* !empty($_FILES['img_src']['name'] */
            $this->response(RestSuccess(), SUCCESS_CODE);
        } else {
            //API
        }
    }

    function delete_user_put()
    {
        $role = !empty($this->put('role')) ? $this->put('role') : '';
        $sess_token = !empty($this->put('token')) ? $this->put('token') : '';


        if ($this->checkAdminAPI($role, $sess_token) || CHECK_POSTMAN) { // admin
            $last = $this->uri->total_segments();
            $account_id = $this->uri->segment($last);
            $is_delete = !empty($this->put('is_delete')) ? 1 : 0;

            $check_verify_params = checkVerifyParams(array(
                $account_id,
            ));
            if (!empty($check_verify_params)) {
                $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
            }

            $this->account_model->update_by_condition(array(
                '_id' => $account_id
            ), array(
                'is_delete' => $is_delete
            ));

            $where = array(
                'account._id' => $account_id
            );
            $select = array(
                'account.is_delete'
            );

            $account = $this->account_model->findOne($where, $select);

            $this->response(RestSuccess($account), SUCCESS_CODE);
        } else {
            //API
        }
    }

    /*
     * Nam.Pham
     * Date : 2018/11/21
     * param :  account_id
     */
    function sale_detail_post(){

        $account_id = !empty($this->post('account_id')) ? $this->post('account_id') : '';
        //$type_user = !empty($this->input->request_headers()['Type']) ? $this->input->request_headers()['Type'] : '';
        $account_login = $this->checkApplicationTokenAuth();

        ///if($type_user == 'client'){
        $check_verify_params = checkVerifyParams(array(
            $account_id,
        ));
        if (!empty($check_verify_params)) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }


        $where = array(
            'account.is_delete' => false,
            'account._id' => $account_id,
        );

        $select =  array(
            'account._id',
            'account.fullname',
            'account.img_src',
            'account_info.achievement_1',
            'account_info.achievement_2',
            'account_info.introduction',
            'account_info.point2rank',
            'account_info.total_favorite',
            'account_info.avg_rating'

        );
        $account_data = $this->account_model->findOne($where,$select); // Get info sale
        if(!empty($account_data)){
            $where_rating = array(
                "rating.seller_id"=>$account_data->_id
            );
            $count_rating = $this->rating_model->count_total($where_rating);
            //dump($rank_data1);
            $account_data->rating = $count_rating;
        }
        $where_favorite = "seller_id = $account_data->_id AND account_id = $account_login";
        $favorite = $this->favorite_model->get_first_row($where_favorite);
        if (!empty($favorite)) {
            $account_data->is_favorite = 1;
        }
        $select_rank =  array(
            'rank.point'
        );
        $rank_data = $this->rank_model->find($select_rank);

        $select_rank =  array(
            'rank.point',
            'rank.name',
            'rank._key'

        );
        $where_rank = [
            'type' => TYPE_COUNSELORS
        ];
        $rank_data = $this->rank_model->find($where_rank,$select_rank);
        foreach($rank_data as $data){
            if(!empty($data->point <= $account_data->point2rank)){

                $account_data->rank_name = $data->name;
                $account_data->_key = $data->_key;
                $account_data->project_array = [];
                break;
            }

        }
        //Get project
        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_DEFAULT;
        $where_project = array(
            'project.is_delete' => false,
            'account._id' => $account_id,
        );

        $project_data = $this->project_model->get_project_pagination($where_project,$offset,$limit);
        if (!empty($project_data) && count($project_data)) {
            foreach ($project_data as $project) {
                $duration_price = array();
                $where_favorite = array(
                    'project_id' => $project->_id,
                );
                $favorite = $this->favorite_model->get_first_row($where_favorite);
                if (!empty($favorite)) {
                    $project->is_favorite = 1;
                }
                if(strpos($project->duration_price,",") !==false){
                    $array_duration_price = explode(",",$project->duration_price);
                    foreach ($array_duration_price as $data){
                        $data_array= explode("-",$data);
                        $duration_price['from'] = $data_array[0];
                        $duration_price['to'] = $data_array[1];
                        $project->data_duration_price[] = $duration_price;
                    }
                }else{
                    $array_duration_price = explode("-",$project->duration_price);
                    $duration_price['from'] = $array_duration_price[0];
                    $duration_price['to'] = $array_duration_price[1];
                    $project->data_duration_price[] = $duration_price;

                }
                //dump($project->img_src ,true);
                if(!empty($project->img_src)) {
                    list($width, $height) = getimagesize($project->img_src);
                    $project->width = $width;
                    $project->height = $height;
                }
            }
        }
        $account_data->project_array = !empty($project_data) ? removeNullElementOfArray($project_data) : [];

        $rs = !empty($account_data) ? removeNullOfObject($account_data) : [];

        $this->response(RestSuccess($rs), SUCCESS_CODE);

    }

    /*
     * Nam.Pham
     * Date : 2019/11/29
     * @param : jwt login with user TVV
     */
    function saler_summary_get(){
        try{
            /*
         * Check user
         */
            $account_id = $this->checkSessionAndTokenAuth();
            $where = array(
                'account_id'=>$account_id
            );
            $data_type = $this->user_get_type_model->findOne($where);

            $type_user = !empty($this->input->request_headers()['Type']) ? $this->input->request_headers()['Type'] : '';

            //check is sale get info
            if($type_user == $data_type->type && $data_type->type == 'counselors'){
                /*
                 * Get point of account login
                 */
                $where_account = array(
                    "account_id" => $account_id
                );
                $select_account = array(
                    "point2rank"
                );
                $data_account = $this->account_info_model->findOne($where_account,$select_account);
                $data_account = !empty($data_account) ? $data_account : [];
                /*
                 * Get list Rank
                 */

                $select_rank = array(
                    "*"
                );
                $data_rank = $this->rank_model->find($select_rank);
                $data_rank = !empty($data_rank) ? $data_rank : [];
                /*
                 * Get list salary
                 */
                $where_salary =  array(
                    "account_id" =>$account_id,
                );
                $select_salary = array(
                    'total_price',
                    'received_price',
                    'not_received_price'
                );
                /*
                 * Get appointment nearly show on app
                 */
                $where_appointment = "seller_id = $account_id AND status = 'confirmed'  ORDER BY time_booking DESC";
                $select_appointment = array(
                    'project_id',
                    'time_booking',
                    'address'
                );
                $data_appointment = $this->appointment_model->findOne($where_appointment,$select_appointment);

                /*
                 * Get info project
                 */
                if(!empty($data_appointment)){
                    $where_project = array(
                        "project._id" => $data_appointment->project_id
                    );
                    $select_project = array(
                        'project.name',
                        'project.address'
                    );
                    $data_project = $this->project_model->findOne($where_project,$select_project);
                }else{
                    $data_project = [];
                }

                //		dump($data_appointment,true);
                $data_salary = $this->salary_model->findOne($where_salary,$select_salary);
                $data_salary = !empty($data_salary) ? $data_salary : [];
                $rs = array(
                    'data_account' => $data_account,
                    'data_rank' => $data_rank,
                    'data_salary' => $data_salary,
                    'data_project' => $data_project
                );
                $rs = !empty($rs) ? removeNullOfObject($rs) : [];

                $this->response(RestSuccess($rs), SUCCESS_CODE);
            }else{
                $this->response(RestBadRequest(WRONG_TYPE_USERNAME), BAD_REQUEST_CODE);
            }
        }catch (Exception $e){
            $this->response(RestBadRequest(ERROR), BAD_REQUEST_CODE);
        }

    }

}
