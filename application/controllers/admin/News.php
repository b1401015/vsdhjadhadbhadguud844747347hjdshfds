<?php
Class News extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('news_model');
        $this->load->library('form_validation', 'upload');
        $this->load->helper(array("form", "url", "captcha"));
    }
    //display list of users
    function listing()
    {
        $this->data['temp'] = 'admin/page/news/listing' ;
        $this->load->view('admin/block/main', $this->data);
    }

    function add_news()
    {
        $this->data['temp'] = 'admin/page/news/add' ;
        $this->load->view('admin/block/main', $this->data);
    }

    function edit_news($news_id)
    {

        if(empty($news_id)){
            redirect(base_url('_admin/news/listing'));
        }

        $where = array(
            'news._id' => $news_id,
            'news.is_delete' => false
        );
        $select = array(
            'news._id',
            'news.title',
            'news._description',
            'news.img_src'
        );

        $news = $this->news_model->findOne($where, $select);
        $this->data['temp'] = 'admin/page/news/edit' ;
        $this->data['news'] = $news;
        $this->load->view('admin/block/main', $this->data);
    }
}