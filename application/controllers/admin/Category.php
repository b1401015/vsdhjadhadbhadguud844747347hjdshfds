<?php
Class Category extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
        $this->load->library('form_validation', 'upload');
        $this->load->helper(array("form", "url", "captcha"));
    }
    //display list of users
    function listing()
    {
        $offset = !empty($this->input->post('offset')) ? $this->input->post('offset') : 0;
        $limit = !empty($this->input->post('limit')) ? $this->input->post('limit') : LIMIT_DEFAULT;
        $where = array(
            'category.is_delete' => false
        );
        $list = $this->category_model->get_pagination($where, '*',$offset, $limit);
        $this->data['list'] = $list;
        $this->data['temp'] = 'admin/page/category/listing' ;
        $this->load->view('admin/block/main', $this->data);
    }

    function add_category()
    {
        $this->data['temp'] = 'admin/page/category/add' ;
        $this->load->view('admin/block/main', $this->data);
    }

    function edit_category($category_id)
    {
        if(empty($category_id)){
            redirect(base_url('_admin/category/listing'));
        }

        $where = array(
            'category._id' => $category_id,
            'category.is_delete' => false
        );
        $select = array(
            'category.*'
        );

        $category = $this->category_model->findOne($where, $select);

        $this->data['temp'] = 'admin/page/category/edit' ;
        $this->data['category'] = $category;
        $this->load->view('admin/block/main', $this->data);
    }
}