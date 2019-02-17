<?php
Class Banner extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('banner_model');
        $this->load->model('category_model');
        $this->load->library('form_validation', 'upload');
        $this->load->helper(array("form", "url", "captcha"));
    }
    //display list of users
    function listing()
    {
        $this->data['temp'] = 'admin/page/banner/listing' ;
        $this->load->view('admin/block/main', $this->data);
    }

    function add_banner()
    {

        $where = array(
            'category.is_delete' => false,
            'category.is_active'=>true
        );
        $select = array(
            'category.*'
        );
        $categories = $this->category_model->find($where, $select);


        $this->data['temp'] = 'admin/page/banner/add' ;
        $this->data['categories'] = $categories;
        $this->load->view('admin/block/main', $this->data);
    }

    function edit_banner($banner_id)
    {

        if(empty($banner_id)){
            redirect(base_url('_admin/banner/listing'));
        }

        $where = array(
            'banner._id' => $banner_id,
            'banner.is_delete' => false
        );
        $select = array(
            '*'
        );

        $banner = $this->banner_model->findOne($where, $select);
        $where = array(
            'category.is_delete' => false,
        );
        $select = array(
            'category.*'
        );
        $categories = $this->category_model->find($where, $select);

        $this->data['temp'] = 'admin/page/banner/edit' ;
        $this->data['categories'] = $categories;
        $this->data['banner'] = $banner;
        $this->load->view('admin/block/main', $this->data);
    }
}