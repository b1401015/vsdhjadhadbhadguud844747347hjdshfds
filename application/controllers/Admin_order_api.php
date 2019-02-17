<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Admin_order_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('order_item_model');
        $this->load->model('record_model');
        $this->load->model('common_setting_model');
        $this->load->model('voucher_model');
//        $this->load->model('user_use_voucher_model');
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $res = RestSuccess('admin/order_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
     * function get order list
     * method: post
     * params: offset, limit
     */
    function get_order_list_post(){
        $keyword = !empty($this->post('search')['value']) ? $this->post('search')['value'] : '';
        $offset = !empty($this->post('start')) ? $this->post('start') : 0;
        $limit = !empty($this->post('length')) ? $this->post('length') : LIMIT_DEFAULT;
        $where = array(
            'order.is_delete' => false,
            'order.ref like' => '%'. skipVN($keyword, true, STR_UPPERCASE) .'%',
        );
        $select = array(
            'order.*',
        );
        $order = $this->order_model->get_pagination($where, $select, $offset, $limit);
        $where = array(
            'order.is_delete' => false,
        );
        $total_records = $this->order_model->count_total($where);
        $rs = [
            'status' => SUCCESS_CODE,
            'message' => OK_MSG,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => !empty($order) ? $order : [],
        ];
        $this->response($rs, SUCCESS_CODE);
    }

    /*
     * function add news
     * method: post
     * params: title, img_src, _description
     */
    function add_new_order_post()
    {

        //order info
        $name = $this->post('name');
        $description = $this->post('_description');
        $ordinal = $this->post('ordinal');
        $is_active = !empty($this->post('is_active')) ? 1 : 0 ;

        $check_verify_params = checkVerifyParams(array(
            $name,
            $_FILES['img_src']['name'],
        ));

        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        //upload img_src
        $img_path = $_FILES['img_src']['name'];
        $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
        $config['upload_path'] = './public/upload/img/order';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name'] = strtotime('now') .'.'. $img_path_ext;

        //Load upload library and initialize configuration
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload('img_src')) {

            //insert order tb
            $this->order_model->create(array(
                'name' => $name,
                'plain_name' => skipVN($name, true),
                '_description' => $description,
                'plain_description' => skipVN(removeHTMLTagsAndSpecialChar($description), true),
                '_key' => generateKeyTranslation(),
                'img_src' => 'public/upload/img/order/'. $config['file_name'],
                'ordinal' => $ordinal,
                'is_active' => $is_active,
                'create_time' => CURRENT_TIME,
                'update_time' => CURRENT_TIME,
                'create_time_mi' => CURRENT_MILLISECONDS
            ));


            $this->response(RestSuccess(), SUCCESS_CODE);
        }
        else{
            $this->response(RestServerError(), SERVER_ERROR_CODE);
        }
    }

    function order_change_status_put(){
        $last = $this->uri->total_segments();
        $order_id = $this->uri->segment($last);
        $status = strtoupper($this->put('status'));

        $check_verify_params = checkVerifyParams(array(
            $order_id,
            $status,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $money = 0;
        $is_add_plus = true;


//        $this->order_model->update_by_condition(array(
//            '_id' => $order_id
//        ), array(
//            'status' => $status
//        ));

        //update order
        if($status == ORDER_STATUS_NOT_DEPOSIT
            || $status == ORDER_STATUS_DEPOSIT
            || $status == ORDER_STATUS_PAYMENT) {
            $this->order_model->update_by_condition(array(
                '_id' => $order_id,
            ),
                array(
                    'status' => $status,
                ));
        }


        if($status == ORDER_STATUS_PAYMENT){
            if(!$this->calculateComissionByOrder($order_id, $money, $is_add_plus)){
//            $this->response(RestServerError(), SERVER_ERROR_CODE);
            }
        }

        $where = array(
            'order._id' => $order_id
        );
        $select = array(
            'order.status'
        );

        $order = $this->order_model->findOne($where, $select);

        $this->response(RestSuccess($order), SUCCESS_CODE);
    }

    function update_order_put()
    {
//        dump($this->put());
        $order_id =  $this->put('order_id');
        $fee_ship =  !empty($this->put('fee_ship')) ? $this->put('fee_ship') : 0;
        $account_id = $this->put('account_id');
        $name = trim($this->put('name'));
        $phone = $this->put('phone');
        $time_delivery = str_replace('/', '-', $this->put('time_delivery'));
        $time_delivery = !empty($this->put('time_delivery')) ? date('Y-m-d H:i:s', strtotime($time_delivery)) : null;
        $email = $this->put('email');
        $note = $this->put('note');
        $product_ids = $this->put('product_id');
        $product_codes = $this->put('product_code');
        $product_names = $this->put('product_name');
        $product_quantities = $this->put('product_qty');
        $product_prices = $this->put('product_price');
        $product_sale_prices = $this->put('product_sale_price');
        $number_item = count($product_ids);
        $voucher_code = strtoupper($this->put('voucher_code'));
        $check_verify_params = checkVerifyParams(array(
            $order_id,
            $name,
            $phone,
            $time_delivery,
        ));
        if(!empty($check_verify_params)
            || empty($product_ids) || !count($product_ids)
            || empty($product_codes) || !count($product_codes)
            || empty($product_names) || !count($product_names)
            || empty($product_quantities) || !count($product_quantities)
            || empty($product_prices) || !count($product_prices)
            || empty($product_sale_prices) || !count($product_sale_prices)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), SUCCESS_CODE);
        }

        if(!is_array($product_ids) || !$number_item){
            $this->response(RestBadRequest(ERROR_FORMAT_DATA_RECORD_MSG), SUCCESS_CODE);
        }

        $total_price = 0;

        $this->order_model->update_by_condition(array(
            '_id' => $order_id
        ),array(
            'number_item' => $number_item,
            'name' => $name,
            'phone' => $phone,
            'time_delivery' => $time_delivery,
            'email' => $email,
            'note' => $note,
            'update_time' => CURRENT_TIME,
        ));

        //delete record_items
        $this->db->where_not_in('record_id', $product_ids);
        $this->db->where('order_id', $order_id);
        $this->db->delete('order_item');
        $this->order_item_model->delete_by_condition(array(
            'order_id' => $order_id
        ));

        for($i = 0; $i < count($product_ids); $i++){
            $where = array(
                'record._id' => $product_ids[$i],
                'record.is_active' => true,
                'record.is_delete' => false,
            );
            $record = $this->record_model->findOne($where, 'record.*');
            if(empty($record)){
                continue;
            }

            $price = !empty($product_prices[$i]) ? str_replace(',', '', $product_prices[$i]) : 0;
            $sale_price = !empty($product_sale_prices[$i]) ? str_replace(',', '', $product_sale_prices[$i]) : 0;
//            dump($price, false);
//            dump($sale_price, false);

            //add order_item
            $this->order_item_model->create(array(
                'order_id' => $order_id,
                'record_id' => $product_ids[$i],
                'price' => !empty($price) ? $price : $sale_price,
                'sale_price' => $sale_price,
                'quantity' => !empty($product_quantities[$i]) ? $product_quantities[$i] : 1,
                'create_time' => CURRENT_TIME,
                'update_time' => CURRENT_TIME,
            ));

            $total_price += (float) ($sale_price * $product_quantities[$i]);
        } /* foreach $data_record */
//        dump($total_price);


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
            $where = array(
                'voucher.code' => $voucher_code,
                'voucher.time_start <=' => CURRENT_TIME,
                'voucher.time_end >=' => CURRENT_TIME,
                'voucher.is_active' => true,
                'voucher.is_delete' => false,
            );
            $select = 'voucher.*';
            $voucher = $this->voucher_model->findOne($where, $select);
//            if(!empty($voucher)) {
//                $where = array(
//                    'account_use_voucher.account_id' => $account_id,
//                    'voucher_id' => $voucher->_id,
//                );
//                $check_is_use_voucher = $this->account_use_voucher_model->findOne($where, '*');
//                if(empty($check_is_use_voucher)){
//                    //insert account_use_voucher
//                    $this->account_use_voucher_model->create(array(
//                        'account_id' => $account_id,
//                        'voucher_id' => $voucher->_id,
//                        'create_time' => CURRENT_TIME,
//                        'update_time' => CURRENT_TIME,
//                    ));

                    //update order with voucher
                    $this->order_model->update_by_condition(array(
                        '_id' => $order_id,
                    ), array(
                        'voucher_id' => $voucher->_id,
                        'voucher_code' => $voucher->code,
                        'voucher_discount_type' => $voucher->type,
                        'voucher_discount_value' => $voucher->_value,
                        'voucher_discount_max_value' => !empty($voucher->max_value) ? $voucher->max_value : 0,
                    ));

                    //update stock
//                    $this->voucher_model->update_by_condition([
//                        '_id' => $voucher->_id,
//                    ], [
//                        'stock' => $voucher->stock - 1 ,
//                    ]);
                    $discount = 0;
//                    $this->writeLog("switch", $total_price);
//                    dump($voucher->type, false);
//                    dump($voucher->_value, false);
                    switch ($voucher->type){
                        case VOUCHER_PERCENT_TOTAL:
                            $discount = $total_price * ((float)$voucher->_value / 100.0);

                            if(!empty($voucher->max_value) && $voucher->max_value > 0 && $discount > $voucher->max_value){
                                $discount = $voucher->max_value;
                            }

                            break;
//                        case VOUCHER_PRICE_ITEM:
//                            foreach($data_record as $record_item){
//                                if($record_item['_id'] == $voucher->record_id){
//                                    $discount = (float) $voucher->_value;
//                                }
//                            }
//                            break;
//                        case VOUCHER_PERCENT_ITEM:
//                            foreach($data_record as $record_item){
//                                if($record_item['_id'] == $voucher->record_id){
//                                    $where = [
//                                        'record._id' => $record_item['_id'],
//                                        'record.is_active' => true,
//                                        'record.is_delete' => false,
//                                    ];
//                                    $record = $this->record_model->findOne($where, 'record.*');
//                                    if(empty($record)){
//                                        continue;
//                                    }
//                                    $discount = $record->sale_price * ((float) $voucher->_value / 100.0);
//                                    if(!empty($voucher->max_value) && $voucher->max_value > 0 && $discount > $voucher->max_value){
//                                        $discount = $voucher->max_value;
//                                    }
//                                }
//                            }
//
//                            break;
                        default:
                            $discount = (float) $voucher->_value;
                            break;
                    }

//                    dump($discount);
                    $total_price -= $discount;
                }

//            }
//        } /* !empty $voucher_code */

        $this->order_model->update_by_condition(array(
            'order._id' => $order_id,
        ), array(
            'order.fee_ship' => $fee_ship,
            'order.total_price' => $total_price,
        ));

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

        $res = !empty($order) ? removeNullOfObject($order) : '';
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    function delete_order_put()
    {
        $last = $this->uri->total_segments();
        $order_id = $this->uri->segment($last);
        $is_delete = !empty($this->put('is_delete')) ? 1 : 0;

        $check_verify_params = checkVerifyParams(array(
            $order_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $this->order_model->update_by_condition(array(
            'order._id' => $order_id
        ), array(
            'order.is_delete' => $is_delete
        ));

        $where = array(
            'order._id' => $order_id
        );
        $select = array(
            'order.is_delete'
        );

        $order = $this->order_model->findOne($where, $select);

        $this->response(RestSuccess($order), SUCCESS_CODE);
    }

    function order_toggle_status_put(){
        $last = $this->uri->total_segments();
        $order_id = $this->uri->segment($last);
        $status = $this->put('status');

        $check_verify_params = checkVerifyParams(array(
            $order_id,
            $status,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        //update order_item
        if($status == ORDER_STATUS_WISH
            || $status == ORDER_STATUS_ORDERED
            || $status == ORDER_STATUS_CONFIRMATION
            || $status == ORDER_STATUS_CHECKOUT
            || $status == ORDER_STATUS_DELIVERY
            || $status == ORDER_STATUS_VALIDATION
            || $status == ORDER_STATUS_RETURN
            || $status == ORDER_STATUS_DELAY
            || $status == ORDER_STATUS_CANCEL) {
            $this->order_model->update_by_condition(array(
                '_id' => $order_id,
            ),
                array(
                    'status' => strtolower($status),
                ));
        }

        $where = array(
            'order._id' => $order_id
        );
        $select = array(
            'order.status'
        );

        $order = $this->order_model->findOne($where, $select);

        $this->response(RestSuccess($order), SUCCESS_CODE);
    }
}

