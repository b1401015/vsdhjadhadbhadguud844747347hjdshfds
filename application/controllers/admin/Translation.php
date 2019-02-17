<?php
Class Translation extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('language_model');
        $this->load->model('language_translation_model');
        $this->load->library('form_validation', 'upload');
        $this->load->helper(array("form", "url"));
    }
    //display list of users
    function listing()
    {
        $this->data['temp'] = 'admin/page/translation/listing' ;
        $this->load->view('admin/block/main', $this->data);
    }

    function add()
    {
        $where = array();
        $languages = $this->language_model->find($where, '*');

        $this->data['temp'] = 'admin/page/translation/add' ;
        $this->data['languages'] = $languages;
        $this->load->view('admin/block/main', $this->data);
    }

    function edit($translation_id)
    {
        if(empty($translation_id)){
            redirect(base_url('_admin/translation/listing'));
        }

        $where = array(
            'language_translation._id' => $translation_id,
        );

        $translation = $this->language_translation_model->findOne($where);
        if(empty($translation)){
            redirect(base_url('_admin/translation/listing'));
        }

        $where = array();
        $languages = $this->language_model->find($where, '*');

        $this->data['temp'] = 'admin/page/translation/edit' ;
        $this->data['languages'] = $languages;
        $this->data['translation'] = $translation;
        $this->load->view('admin/block/main', $this->data);
    }
}