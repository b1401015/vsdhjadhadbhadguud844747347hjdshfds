<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Admin extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model');
    }
    //
    function create_admin_account() {
//        $this->admin_model->create(array(
//        'username'    => 'admin',
//        'password'    => $this->hash256('8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92'),    //123456
//        'role'        => 'superadmin'
//        ));
    }
    //display index page
    function index_get() {
        $this->data['temp'] = 'admin/page/index' ;
        $this->load->view('admin/block/main', $this->data);
    }
    //display login page
    function login_get() {
        if(!empty($this->get_login_user_id())){
            redirect(base_url('_admin/index'));
        }
        $captcha = $this->generateCaptchaImageTag();
        $this->data['captcha'] = $captcha;
        $this->load->view('admin/auth/login', $this->data);
    }
    //get new tag <image/> of captcha
    function read_new_captcha_post(){
        $captcha = $this->generateCaptchaImageTag();
        $this->response(RestSuccess($captcha), SUCCESS_CODE);
    }
    //process login
    function login_post() {
        $username = trim($this->post('username'));
        $password = trim($this->post('password'));
        $captcha = trim($this->post('captcha'));
        if(!$this->isValidCaptchaSession($captcha)){
            $this->response(RestBadRequest(WRONG_CAPTCHA_MSG), BAD_REQUEST_CODE);
        }
        //check user exists
        $where = array(
            'username'  => $username,
            'password'  => $password
        );
        $user_info = $this->admin_model->read_row($where);
        if (empty($user_info->_id)){
            $this->response(RestBadRequest(NOT_FOUND_MSG), BAD_REQUEST_CODE);
        }
        $this->set_login_user_id($user_info->_id);
        $this->response(RestSuccess(array()), SUCCESS_CODE);
    }

    /**
     * logout Admin pages
     */
    function logout_get() {
        $this->set_login_user_id('');
        redirect(base_url('_admin/index'));
    }
}