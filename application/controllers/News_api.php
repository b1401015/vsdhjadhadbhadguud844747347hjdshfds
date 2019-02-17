<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class News_api extends REST_Controller
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
        $res = RestSuccess('news_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
     * function get news list
     * method: post
     * params:
     */
    function get_news_list_post()
    {
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';

        $where = [
            'is_active' => true,
            'is_delete' => false,
        ];
        $select = [
            '_id',
            'title',
            'img_src',
            'link_url',
            'count_view',
            'create_time_mi'
        ];
        $news = $this->news_model->get_pagination($where, $select, $offset, $limit, $last_id);
        $res = !empty($news) ? removeNullElementOfArray($news) : [];
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function get news detail
     * method: get
     * params: news_id
     */
    function get_news_detail_get()
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

        $res = !empty($news) ? removeNullElementOfArray($news) : '';
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }
}