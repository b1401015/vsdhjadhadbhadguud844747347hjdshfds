<?php
Class Home extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('account_model');
        $this->load->model('record_model');
        $this->load->library('form_validation', 'upload');
        $this->load->helper(array("form", "url", "captcha"));
    }

    function index()
    {
        $this->data['temp'] = 'front/page/home/home' ;
        $this->load->view('front/block/main', $this->data);
    }

    function login()
    {

        $this->data['temp'] = 'front/page/home/login';

        $this->load->view('front/block/main',  $this->data);
    }

    function register()
    {

       /* $this->data['temp'] = 'front/page/event/event_list';*/
        $this->load->view('front/block/main', '');
    }
}