<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Webview_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('news_model');
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $last = $this->uri->total_segments();
        $page = $this->uri->segment($last);

        $this->load->view("front/webview/$page", null);
    }

    /*
     * function terms of use
     * method: get
     * params:
     */
    function news_detail_get()
    {
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $last = $this->uri->total_segments();
        $news_id = $this->uri->segment($last);

        $check_verify_params = checkVerifyParams(array(
            $news_id
        ));
        if(!empty($check_verify_params)){
            $this->response($check_verify_params, BAD_REQUEST_CODE);
        }

        $select = array(
            '_id',
            'title',
            '_description',
            'img_src',
            'link_url',
            'count_view',
            'create_time_mi'
        );
        $where = ['_id' => $news_id];
        $news = $this->news_model->findOne($where, $select);
        if(empty($news)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }

        $this->data['news'] = $news;
        $this->load->view('front/webview/news_detail', $this->data);
    }
}