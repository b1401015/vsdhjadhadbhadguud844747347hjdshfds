<?php
Class Notification extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('push_token_model');
        $this->load->model('notification_model');
        $this->load->library('form_validation', 'upload');
        $this->load->helper(array("form", "url"));
    }

    //display list
    function listing()
    {
        $this->data['temp'] = 'admin/page/notification/listing' ;
        $this->load->view('admin/block/main', $this->data);
    }

    //display add
    function send_push()
    {
        $where = array(
            'account.is_delete' => false,
        );
        $accounts = $this->account_model->find($where, 'account.*');
        $this->data['accounts'] = $accounts;
        $this->data['temp'] = 'admin/page/notification/send_push' ;
        $this->load->view('admin/block/main', $this->data);
    }

    //display edit
    function edit($notification_id)
    {
        if(empty($notification_id)){
            redirect(base_url('_admin/notification/listing'));
        }

        $where = array(
            'notification._id' => $notification_id,
            'notification.is_done' => false,
            'notification.is_delete' => false,
        );

        $notification = $this->notification_model->findOne($where);
        if(empty($notification)){
            redirect(base_url('_admin/notification/listing'));
        }

        $where = array(
            'account.is_delete' => false,
        );
        $accounts = $this->account_model->find($where, 'account.*');
        $this->data['accounts'] = $accounts;
        $this->data['notification'] = $notification;
        $this->data['temp'] = 'admin/page/notification/edit' ;
        $this->load->view('admin/block/main', $this->data);
    }
}