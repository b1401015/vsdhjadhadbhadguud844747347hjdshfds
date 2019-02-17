<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Category_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
        $this->load->model('record_model');
        $this->load->model('record_file_model');
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $res = RestSuccess('category_api');
        $this->response($res, SUCCESS_CODE);
    }



    /*
     * function get records per category
     * function get records per category
     * method: post
     * params: limit_record, offset, limit, last_id
     */
    function get_featured_category_post()
    {
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;
        //$is_featured = !empty($this->post('is_featured')) ? $this->post('is_featured') : false;
        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_FEATURED_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';
        //dump($is_featured,true);
        $where = array(
            'category.is_active' => true,
            'category.is_delete' => false,
            'category.is_featured' => true
        );

        $select = array(
            'category._id',
            'category.name',
            'category.img_src',
            'category._key',
        );
        //$categories = $this->category_model->find($where, $select);
        $categories = $this->category_model->get_pagination($where, $select, $offset, $limit, $last_id);
        //dump($categories,true);
        $this->response(RestSuccess(!empty($categories) ? removeNullElementOfArray($categories) : []), SUCCESS_CODE);
    }

    function get_category_post()
    {
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;
        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';

        $where = array(
            'category.is_active' => true,
            'category.is_delete' => false,
            'category.parent_id' => null
        );

        $select = array(
            'category._id',
            'category.name',
            'category._key',
        );
        $categories = $this->category_model->get_pagination($where, $select, $offset, $limit, $last_id);



        $this->response(RestSuccess(!empty($categories) ? removeNullElementOfArray($categories) : []), SUCCESS_CODE);
    }

    function get_category_child_post()
    {
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;
        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';
        $category_id = $this->post('category_id');

        $check_verify_params = checkVerifyParams([$category_id]);
        if(!empty($check_verify_params)){
            $this->response($check_verify_params, BAD_REQUEST_CODE);
        }

            $where_check['category._id']=$category_id;

        $select_check=array(
            'category.*'
        );
        $category = $this->category_model->findOne($where_check, $select_check);
        if(empty($category)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }

                $where = array(
                    'category.is_active' => true,
                    'category.is_delete' => false,
                    'category.parent_id' => $category_id
                );
                $select = array(
                    'category._id',
                    'category.name',
                    'category._key',
                );
        $categories = $this->category_model->get_pagination($where, $select, $offset, $limit, $last_id);
        $this->response(RestSuccess(!empty($categories) ? removeNullElementOfArray($categories) : []), SUCCESS_CODE);
    }

}