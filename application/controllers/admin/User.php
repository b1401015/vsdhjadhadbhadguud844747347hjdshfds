<?php
Class User extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('account_model');
        $this->load->library('form_validation', 'upload');
        $this->load->helper(array("form", "url", "captcha"));
    }
    //display list of users
    function listing()
    {
        $offset = !empty($this->input->post('offset')) ? $this->input->post('offset') : 0;
        $limit = !empty($this->input->post('limit')) ? $this->input->post('limit') : LIMIT_DEFAULT;
        $where = ['is_delete' => 0];
        $list = $this->user_model->get_pagination($where, $offset, $limit);
        $this->data['list'] = $list;
        $this->data['temp'] = 'admin/page/user/listing' ;
        $this->load->view('admin/block/main', $this->data);
    }

    function add_user()
    {
        $where = array(
            'account.is_delete' => false
        );
        $select = array(
            'account.*'
        );
        $parents = $this->account_model->find($where, $select);

        $this->data['temp'] = 'admin/page/user/add' ;
        $this->data['parents'] = $parents;
        $this->load->view('admin/block/main', $this->data);
    }

    function edit_user($account_id)
    {

        if(empty($account_id)){
            redirect(base_url('_admin/user/listing'));
        }

        $where = array(
            'account._id' => $account_id,
            'account.is_delete' => false
        );
        $select = array(
            'account.*',
            'user.username',
            'user.email',
            'account_info.address',
            'account_info.gender',
            'account_info.phone',
            'account_info.birthday',
        );

        $account = $this->user_model->findOne($where, $select);
        $this->data['temp'] = 'admin/page/user/edit' ;
        $this->data['account'] = $account;
        $this->load->view('admin/block/main', $this->data);
    }
}