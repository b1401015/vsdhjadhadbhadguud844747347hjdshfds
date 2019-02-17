<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Order_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
        $this->load->model('record_model');
        $this->load->model('record_file_model');
        $this->load->model('order_model');
        $this->load->model('order_item_model');
        $this->load->model('voucher_model');
        $this->load->model('account_use_voucher_model');
        $this->load->model('common_setting_model');
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $res = RestSuccess('order_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
     * function check vouher code
     * method: post
     * params: fee_ship, name, phone, ...
     */
    public function check_voucher_post() {
        /*check session*/
        $account_id = $this->session->userdata('ACCOUNT_ID');
        if(empty($account_id)){
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        /*end check session*/

        $code = strtoupper($this->post('code'));

        $check_verify_params = checkVerifyParams([
            $code,
        ]);
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $where = [
            'voucher.code' => $code,
            'voucher.time_start <=' => CURRENT_TIME,
            'voucher.time_end >=' => CURRENT_TIME,
            'voucher.is_active' => true,
            'voucher.is_delete' => false,
        ];
        $select = 'voucher.*';
        $voucher = $this->voucher_model->findOne($where, $select);
        if(empty($voucher)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }

        $where = [
            'account_use_voucher.account_id' => $account_id,
            'voucher_id' => $voucher->_id
        ];
        $check_is_use_voucher = $this->account_use_voucher_model->findOne($where, '*');
        if(!empty($check_is_use_voucher)){
            $this->response(RestBadRequest(VOUCHER_IS_ALREADY_USED_MSG), BAD_REQUEST_CODE);
        }

        $res = $voucher->_value;

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function check vouher code
     * method: post
     * params: fee_ship, name, phone, ...
     */
    public function get_voucher_info_post() {
        /*check session*/
        $account_id = $this->checkApplicationTokenAuth();
//        $account_id = 9;
        /*end check session*/

        $code = strtoupper($this->post('code'));

        $check_verify_params = checkVerifyParams([
            $code,
        ]);
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $where = [
            'voucher.code' => $code,
            'voucher.time_start <=' => CURRENT_TIME,
            'voucher.time_end >=' => CURRENT_TIME,
            'voucher.stock >' => 0,
            'voucher.is_active' => true,
            'voucher.is_delete' => false,
        ];
        $select = array(
            'voucher._id',
            'voucher.code',
            'voucher.type',
            'voucher._value as value',
            'voucher.max_value',
            'voucher.time_end',
            'voucher.record_id',
            'voucher.create_time_mi',
        );

        $voucher = $this->voucher_model->findOne($where, $select);
        if(empty($voucher)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }

        $where = [
            'account_use_voucher.account_id' => $account_id,
            'voucher_id' => $voucher->_id
        ];
        //mỏi người chỉ được sử dụng 1 lần
        $check_is_use_voucher = $this->account_use_voucher_model->findOne($where, '*');
        if(!empty($check_is_use_voucher)){
            $this->response(RestBadRequest(VOUCHER_IS_ALREADY_USED_MSG), BAD_REQUEST_CODE);
        }

        if(!empty($voucher->record_id)){
            $select = array(
                'record._id',
                'record.code',
                'record.barcode',
                'record.title',
                'record.price',
                'record.sale_price',
//                'record.price_vacation',
            );

            $where = [
                'record._id' => $voucher->record_id
            ];
            $record = $this->record_model->findOne($where, $select);
            if(!empty($record)) {
                $voucher->record = removeNullOfObject($record);
            }
        }

        $res = removeNullOfObject($voucher);

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function booking order
     * method: post
     * params: fee_ship, name, phone, ...
     */
    public function booking_order_post() {
        /*check session & jwt*/
        $account_id = $this->session->userdata('ACCOUNT_ID');
        if(empty($account_id)){
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        $jwt = $this->input->request_headers()['Authorization'];
        if(!$this->checkVerifyJWT($jwt, $account_id)){
            $this->response(RestForbidden(INVALID_TOKEN_MSG), FORBIDDEN_CODE);
        }
        /*end check session & jwt*/

        $fee_ship =  !empty($this->post('fee_ship')) ? $this->post('fee_ship') : 0;
        $name = trim($this->post('name'));
        $phone = $this->post('phone');
        $time_delivery = !empty($this->post('time_delivery')) ? date('Y-m-d H:i:s', strtotime($this->post('time_delivery'))) : null;
        $email = $this->post('email');
        $note = $this->post('note');
        $data_record = $this->post('data_record');
        $number_item = count($data_record);
        $voucher_code = strtoupper($this->post('voucher_code'));
        $order_location = trim($this->post('order_location'));
        $order_geolocation = $this->post('order_geolocation');
        $current_location = trim($this->post('current_location'));
        $current_geolocation = $this->post('current_geolocation');

        $check_verify_params = checkVerifyParams([
            $name,
            $phone,
            $time_delivery,
            $data_record,
            $order_location,
        ]);
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        if(!is_array($data_record) || !$number_item){
            $this->response(RestBadRequest(ERROR_FORMAT_DATA_RECORD_MSG), BAD_REQUEST_CODE);
        }

        $total_price = 0;

        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(FALSE); # See Note 01. If you wish can remove as well

        $this->order_model->create([
            'ref' => generateReferenceCode(),
            'number_item' => $number_item,
            'name' => $name,
            'phone' => $phone,
            'time_delivery' => $time_delivery,
            'email' => $email,
            'note' => $note,
            'order_location' => $order_location,
            'order_geolocation' => !empty($order_geolocation) ? json_encode($order_geolocation, JSON_UNESCAPED_UNICODE) : '',
            'current_location' => $current_location,
            'current_geolocation' => !empty($current_geolocation) ? json_encode($current_geolocation, JSON_UNESCAPED_UNICODE) : '',
            'account_id' => $account_id,
            'create_time' => CURRENT_TIME,
            'update_time' => CURRENT_TIME,
            'create_time_mi' => CURRENT_MILLISECONDS,
        ]);

        $order_id = $this->db->insert_id();

        foreach($data_record as $record_item){
            $where = [
                'record._id' => $record_item['_id'],
                'record.is_active' => true,
                'record.is_delete' => false,
            ];
            $record = $this->record_model->findOne($where, 'record.*');
            if(empty($record)){
                continue;
            }

            //insert order_item
            $this->order_item_model->create([
                'order_id' => $order_id,
                'record_id' => $record_item['_id'],
                'price' => !empty($record_item['price']) ? $record_item['price'] : $record_item['sale_price'],
                'sale_price' => $record_item['sale_price'],
                'quantity' => !empty($record_item['quantity']) ? $record_item['quantity'] : 1,
                'create_time' => CURRENT_TIME,
                'update_time' => CURRENT_TIME,
            ]);

            $total_price += (float) ($record_item['sale_price'] * $record_item['quantity']);
        } /* foreach $data_record */


        $is_allow_use_voucher = true;
        //get common_setting
        $common_setting = $this->common_setting_model->findOne(array(), '*');
        if(!empty($common_setting)) {
            if ($total_price < (float) $common_setting->limit_price_use_voucher) {
                $is_allow_use_voucher = false;
            }
            if($total_price >= (float) $common_setting->large_fee_price){
                $fee_ship = (float) $common_setting->small_fee_price;
                $total_price += (float) $fee_ship;
            }
        } /* !empty common_setting */

        //check voucher
        if(!empty($voucher_code) && $is_allow_use_voucher){
            $where = [
                'voucher.code' => $voucher_code,
                'voucher.time_start <=' => CURRENT_TIME,
                'voucher.time_end >=' => CURRENT_TIME,
                'voucher.stock >' => 0,
                'voucher.is_active' => true,
                'voucher.is_delete' => false,
            ];
            $select = 'voucher.*';
            $voucher = $this->voucher_model->findOne($where, $select);
            if(!empty($voucher)) {
                $where = [
                    'account_use_voucher.account_id' => $account_id,
                    'voucher_id' => $voucher->_id,
                ];
                $check_is_use_voucher = $this->account_use_voucher_model->findOne($where, '*');
                if(empty($check_is_use_voucher) ||  true){
                    //insert account_use_voucher
                    $this->account_use_voucher_model->create(array(
                        'account_id' => $account_id,
                        'voucher_id' => $voucher->_id,
                        'create_time' => CURRENT_TIME,
                        'update_time' => CURRENT_TIME,
                    ));

                    //update order with voucher
                    $this->order_model->update_by_condition([
                        '_id' => $order_id,
                    ], [
                        'voucher_id' => $voucher->_id,
                        'voucher_code' => $voucher->code,
                        'voucher_discount_type' => $voucher->type,
                        'voucher_discount_value' => $voucher->_value,
                        'voucher_discount_max_value' => !empty($voucher->max_value) ? $voucher->max_value : 0,
                    ]);

                    //update stock
                    $this->voucher_model->update_by_condition([
                        '_id' => $voucher->_id,
                    ], [
                        'stock' => $voucher->stock - 1 ,
                    ]);
                    $discount = 0;
//                    $this->writeLog("switch", $total_price);
                    switch ($voucher->type){
                        case VOUCHER_PERCENT_TOTAL:
                            $discount = $total_price * ((float)$voucher->_value / 100.0);

                            if(!empty($voucher->max_value) && $voucher->max_value > 0 && $discount > $voucher->max_value){
                                $discount = $voucher->max_value;
                            }

                            break;
                        case VOUCHER_PRICE_ITEM:
                            foreach($data_record as $record_item){
                                if($record_item['_id'] == $voucher->record_id){
                                    $discount = (float) $voucher->_value;
                                }
                            }
                            break;
                        case VOUCHER_PERCENT_ITEM:
                            foreach($data_record as $record_item){
                                if($record_item['_id'] == $voucher->record_id){
                                    $where = [
                                        'record._id' => $record_item['_id'],
                                        'record.is_active' => true,
                                        'record.is_delete' => false,
                                    ];
                                    $record = $this->record_model->findOne($where, 'record.*');
                                    if(empty($record)){
                                        continue;
                                    }
                                    $discount = $record->sale_price * ((float) $voucher->_value / 100.0);
                                    if(!empty($voucher->max_value) && $voucher->max_value > 0 && $discount > $voucher->max_value){
                                        $discount = $voucher->max_value;
                                    }
                                }
                            }

                            break;
                        default:
                            $discount = (float) $voucher->_value;
                            break;
                    }
//                    $this->writeLog("discount", $discount);
                    $total_price -= $discount;
//                    $this->writeLog("total_price", $total_price);
                }else{
                    $this->db->trans_rollback();
                    $this->response(RestBadRequest(VOUCHER_IS_ALREADY_USED_MSG), BAD_REQUEST_CODE);
                }

            }
        } /* !empty $voucher_code */

        if($total_price < 0){
            $total_price = 0;
        }

        $this->order_model->update_by_condition([
            '_id' => $order_id,
        ], [
            'fee_ship' => $fee_ship,
            'total_price' => $total_price,
        ]);

        $select = array(
            'order._id',
            'order.ref',
            'order.status',
            'order.fee_ship',
            'order.total_price',
            'order.number_item',
            'order.name',
            'order.phone',
            'order.time_delivery',
            'order.email',
            'order.note',
            'order.order_location',
            'order.order_geolocation',
            'order.voucher_id',
            'order.voucher_code',
            'order.voucher_discount_type',
            'order.voucher_discount_value',
            'order.create_time_mi',
        );
        $where = array(
            'order._id' => $order_id
        );
        $order = $this->order_model->findOne($where, $select);



        if ($this->db->trans_status() === FALSE) {
            # Something went wrong.
            $this->db->trans_rollback();
            $this->response(RestServerError(), SERVER_ERROR_CODE);
        }
        else {
            # Everything is Perfect.
            # Committing data to the database.
            $this->db->trans_commit();
            $res = !empty($order) ? removeNullOfObject($order) : '';
            $this->response(RestSuccess($res), SUCCESS_CODE);
        }
    }

    /*
     * function get history orders
     * method: post
     * params: offset, limit, last_id
     */
    //lịch sử đơn hàng oganban
    function get_history_orders_post()
    {
        /*check session*/
        $account_id = $this->session->userdata('ACCOUNT_ID');
        //dump($account_id,true);
        if(empty($account_id)){
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        /*end check session*/

        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';
        $status = $this->post('status');


        $where = [
            'order.account_id' => $account_id,
            'order.is_delete' => false,
        ];
        //chọn đơn hàng theo trạng thái //nếu status rỗng thì tất cả
        if (!empty($status)){
            $where["order.status"] = $status;
        }

        $select = [
            'order._id',
           // 'order.ref',
            'order.status',
            //'order.fee_ship',
            'order.total_price',
            //'order.number_item',
        //    'order.name',
        //    'order.phone',
           // 'order.time_delivery',
          //  'order.email',
           // 'order.note',
            //'order.order_location',
            //'order.order_geolocation',
            //'order.voucher_id',
          //  'order.voucher_code',
            //'order.voucher_discount_type',
           // 'order.voucher_discount_value',
            'order.create_time_mi',
            'record.title',
            'record.img_src',
            'account.fullname',
        ];
        $orders = $this->order_model->get_pagination($where, $select, $offset, $limit, $last_id);
        //dump($orders,true);

        //dump($orders,true);

        $res = !empty($orders) ? removeNullElementOfArray($orders) : [];
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
    * function get history orders
    * method: post
    * params: offset, limit, last_id
    */
    //lịch sử đơn hàng oganban
    function get_transaction_seller_post()
    {
        /*check session*/
        $seller_id = $this->session->userdata('ACCOUNT_ID');
        if(empty($account_id)){
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        /*end check session*/

        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';
        $status = $this->post('status');


        $where = [
            'order.seller_id' => $seller_id,
            'order.is_delete' => false,
        ];
        //chọn đơn hàng theo trạng thái //nếu status rỗng thì tất cả
        if (!empty($status)){
            $where["status"] = $status;
        }

        $select = [
            'order._id',
            // 'order.ref',
            'order.status',
            //'order.fee_ship',
            'order.total_price',
            //'order.number_item',
            //    'order.name',
            //    'order.phone',
            // 'order.time_delivery',
            //  'order.email',
            // 'order.note',
            //'order.order_location',
            //'order.order_geolocation',
            //'order.voucher_id',
            //  'order.voucher_code',
            //'order.voucher_discount_type',
            // 'order.voucher_discount_value',
            'order.create_time_mi',
            'record.title',
            'record.img_src',
            'account.fullname',
        ];
        $orders = $this->order_model->get_pagination($where, $select, $offset, $limit, $last_id);

        $res = !empty($orders) ? removeNullElementOfArray($orders) : [];
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }
    /*
     * function get history order detail
     * method: get
     * params: order_id
     */
    function get_history_order_detail_get()
    {
        /*check session*/
        $account_id = $this->session->userdata('ACCOUNT_ID');
        if(empty($account_id)){
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        /*end check session*/

        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $last = $this->uri->total_segments();
        $order_id = $this->uri->segment($last);
        $check_verify_params = checkVerifyParams([$order_id]);
        if(!empty($check_verify_params)){
            $this->response($check_verify_params, BAD_REQUEST_CODE);
        }

        $where = [
            'order._id' => $order_id,
            'order.is_delete' => false,
        ];
        $select = [
            'order._id',
            'order.ref',
            'order.status',
            'order.fee_ship',
            'order.total_price',
            'order.number_item',
            'order.name',
            'order.phone',
            'order.time_delivery',
            'order.email',
            'order.note',
            'order.order_location',
            'order.order_geolocation',
            'order.voucher_id',
            'order.voucher_code',
            'order.voucher_discount_type',
            'order.voucher_discount_value',
            'order.create_time_mi',
        ];
        $order = $this->order_model->findOne($where, $select);
        if(empty($order)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }

        $where = [
            'order_item.order_id' => $order_id,
        ];
        $select = [
            'record._id',
            'record.code',
            'record.barcode',
            'record.title',
            'record.img_src',
            'order_item.price',
            'order_item.sale_price',
            'order_item.quantity',
        ];
        $order_items = $this->order_item_model->find($where, $select);
        if(empty($order_items) || !count($order_items)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }

        $order->data_record = $order_items;

        $res = !empty($order) ? removeNullOfObject($order) : '';
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function get search order
     * method: post
     * params: keyword, offset, limit, last_id
     */
    function search_order_post()
    {
        /*check session*/
        $account_id = $this->session->userdata('ACCOUNT_ID');
        if(empty($account_id)){
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        /*end check session*/

        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $keyword = trim(skipVN($this->post('keyword'), true, STR_UPPERCASE));
        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';

        $where = [
            'order.account_id' => $account_id,
            'order.ref like' => '%'.$keyword.'%',
            'order.is_delete' => false,
        ];
        $select = [
            'order._id',
            'order.ref',
            'order.status',
            'order.fee_ship',
            'order.total_price',
            'order.number_item',
            'order.name',
            'order.phone',
            'order.time_delivery',
            'order.email',
            'order.note',
            'order.order_location',
            'order.order_geolocation',
            'order.voucher_id',
            'order.voucher_code',
            'order.voucher_discount_type',
            'order.voucher_discount_value',
            'order.create_time_mi',
        ];
        $orders = $this->order_model->get_pagination($where, $select, $offset, $limit, $last_id);

        $res = !empty($orders) ? removeNullElementOfArray($orders) : [];
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function get latest order
     * method: get
     * params:
     */
    function get_latest_order_get()
    {
        /*check session*/
        $account_id = $this->session->userdata('ACCOUNT_ID');
        if(empty($account_id)){
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        /*end check session*/

        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $where = [
            'order.account_id' => $account_id,
            'order.is_delete' => false,
        ];
        $select = [
            'order._id',
            'order.ref',
            'order.status',
            'order.fee_ship',
            'order.total_price',
            'order.number_item',
            'order.name',
            'order.phone',
            'order.time_delivery',
            'order.email',
            'order.note',
            'order.order_location',
            'order.order_geolocation',
            'order.voucher_id',
            'order.voucher_code',
            'order.voucher_discount_type',
            'order.voucher_discount_value',
            'order.create_time_mi',
        ];
        $order = $this->order_model->findOne($where, $select);

        $res = !empty($order) ? removeNullOfObject($order) : '';
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function cancel order
     * method: put
     * params: order_id
     */
    public function cancel_order_put()
    {
        /*check session & jwt*/
        $account_id = $this->session->userdata('ACCOUNT_ID');
        if (empty($account_id)) {
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        $jwt = $this->input->request_headers()['Authorization'];
        if (!$this->checkVerifyJWT($jwt, $account_id)) {
            $this->response(RestForbidden(INVALID_TOKEN_MSG), FORBIDDEN_CODE);
        }
        /*end check session & jwt*/

        $last = $this->uri->total_segments();
        $order_id = $this->uri->segment($last);

        $check_verify_params = checkVerifyParams(array(
            $order_id,
        ));
        if (!empty($check_verify_params)) {
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $this->order_model->update_by_condition(array(
            '_id' => $order_id,
            'is_delete' => false,
        ), array(
            'status' => ORDER_STATUS_CANCEL
        ));

        $select = array(
            'order._id',
            'order.status',
        );
        $where = array(
            '_id' => $order_id
        );
        $order = $this->order_model->findOne($where, $select);

        $res = !empty($order) ? removeNullOfObject($order) : '';
        $this->response(RestSuccess($res), SUCCESS_CODE);

    }

}