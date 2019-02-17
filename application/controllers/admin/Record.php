<?php
Class Record extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('record_model');
        $this->load->model('record_file_model');
        $this->load->model('category_has_record_model');
        $this->load->model('category_model');
        $this->load->library('form_validation', 'upload');
        $this->load->helper(array("form", "url"));
    }
    //display list of users
    function listing()
    {
        $where=array(
            'is_delete'=>false,
            'is_active'=>true
        );
        $category=$this->category_model->find($where,"*");
        $this->data['temp'] = 'admin/page/record/listing' ;
        $this->data['category'] = $category ;
        $this->load->view('admin/block/main', $this->data);
    }

    function add_record()
    {
        $where = array(
            'category.is_delete' => false,
            'category.is_active'=>true
        );
        $select = array(
            'category.*'
        );
        $categories = $this->category_model->find($where, $select);

        $this->data['temp'] = 'admin/page/record/add' ;
        $this->data['categories'] = $categories;
        $this->load->view('admin/block/main', $this->data);
    }

    function edit_record($record_id)
    {
        if(empty($record_id)){
            redirect(base_url('_admin/record/listing'));
        }

        $where = array(
            'record._id' => $record_id,
            'record.is_delete' => false
        );
        $select = array(
            'record.*',
            'category._id as category_id'
        );

        $record = $this->record_model->findOne($where, $select);
        if(empty($record)){
            redirect(base_url('_admin/record/listing'));
        }

        $where = array(
            'category.is_delete' => false
        );
        $select = array(
            'category.*',
        );

        $categories = $this->category_model->find($where, $select);

        if (!empty($record)) {
            $where = array(
                'category.is_delete' => false,
                'category_has_record.record_id' =>$record->_id
        );
        }
        $select = array(
            'category_has_record.category_id',
        );

        $cate_has = $this->category_has_record_model->find_cate($where, $select);

        $this->data['temp'] = 'admin/page/record/edit' ;
        $this->data['categories'] = $categories;
        $this->data['record'] = $record;
        $this->data['cate_has'] = $cate_has;
        $this->load->view('admin/block/main', $this->data);
    }

    function gallery($record_id)
    {
        if(empty($record_id)){
            redirect(base_url('_admin/record/listing'));
        }

        $where = array(
            'record._id' => $record_id,
            'record.is_delete' => false
        );
        $select = array(
            'record.*'
        );

        $record = $this->record_model->findOne($where, $select);
        if(empty($record)){
            redirect(base_url('_admin/record/listing'));
        }

        $where = array(
            'record_id' => $record_id
        );
        $record_files = $this->record_file_model->find($where, 'record_file.*');

        $this->data['record'] = $record;
        $this->data['record_files'] = $record_files;
        $this->data['temp'] = 'admin/page/record/gallery' ;
        $this->load->view('admin/block/main', $this->data);
    }
}