<?php
Class Order extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('order_item_model');
        $this->load->model('record_model');
        $this->load->library('form_validation', 'upload');
        $this->load->helper(array("form", "url", "captcha"));
    }
    //display list of users
    function listing()
    {
        $this->data['temp'] = 'admin/page/order/listing' ;
        $this->load->view('admin/block/main', $this->data);
    }

    function add_order()
    {
        $this->data['temp'] = 'admin/page/order/add' ;
        $this->load->view('admin/block/main', $this->data);
    }

    function edit_order($order_id)
    {

        if(empty($order_id)){
            redirect(base_url('_admin/order/listing'));
        }

        $where = array(
            'order._id' => $order_id,
            'order.is_delete' => false
        );
        $select = array(
            'order.*',
            'account.fullname',
        );
        $order = $this->order_model->findOne($where, $select);

        $where = array(
            'order_item.order_id' => $order_id
        );
        $select = array(
            'order_item.*',
            'record._id as record_id',
            'record.title',
            'record.code',
        );
        $order_items = $this->order_item_model->find($where, $select);

        $where = array(
            'record.is_delete' => false
        );
        $select = array(
            'record._id',
            'record.code',
            'record.title',
            'record.price',
            'record.sale_price'
        );
        $records = $this->record_model->find($where, $select);

        $this->data['temp'] = 'admin/page/order/edit' ;
        $this->data['order'] = $order;
        $this->data['order_items'] = $order_items;
        $this->data['records'] = $records;
        $this->load->view('admin/block/main', $this->data);
    }

    function export_order_detail($order_id)
    {
        if(empty($order_id)){
            redirect(base_url('_admin/order/listing'));
        }

        $where = array(
            'order._id' => $order_id,
            'order.is_delete' => false
        );
        $select = array(
            'order.*',
            'account.fullname',
            'account_info.address',
            'account_info.phone as account_phone',
        );
        $order = $this->order_model->findOne($where, $select);

        $where = array(
            'order_item.order_id' => $order_id
        );
        $select = array(
            'order_item.*',
            'record._id as record_id',
            'record.title',
            'record.code',
        );
        $order_items = $this->order_item_model->find($where, $select);

        $where = array(
            'record.is_delete' => false
        );
        $select = array(
            'record._id',
            'record.code',
            'record.title',
            'record.price',
            'record.sale_price'
        );
        $records = $this->record_model->find($where, $select);

        $this->data['order'] = $order;
        $this->data['order_items'] = $order_items;
        $this->data['records'] = $records;
        $this->load->view('admin/template/excel/detail_order_user', $this->data);
    }
}