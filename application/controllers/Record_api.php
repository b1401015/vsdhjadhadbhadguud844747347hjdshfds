<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Record_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
        $this->load->model('record_model');
        $this->load->model('record_file_model');
        $this->load->model('category_has_record_model');
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $res = RestSuccess('record_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
     * function get records per category
     * function get records per category
     * method: post
     * params: limit_record, offset, limit, last_id
     */
    function get_records_per_category_post()
    {
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $limit_record = !empty($this->post('limit_record')) ? $this->post('limit_record') : LIMIT_RECORD_DEFAULT;
        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';
        $is_featured = !empty($this->post('is_featured')) ? true : false;

        $where = array(
            'category.is_active' => true,
            'category.is_delete' => false,
            ' (select count(*) from category_has_record, record where category_id = category._id AND record._id = category_has_record.record_id AND record.is_featured  = '.($is_featured ? "1" : "0").') > ' => 0,
        );

        $select = array(
            'category._id',
            'category.name'
        );
        $categories = $this->category_model->get_pagination($where, $select, $offset, $limit, $last_id);

        if(empty($categories) || !count($categories)){
            $this->response(RestSuccess([]), SUCCESS_CODE);
        }

        $res = [];
        foreach($categories as $category){
            $where = [
                'category._id' => $category->_id,
                'record.is_active' => true,
                'record.is_delete' => false,
            ];
//            if(!empty($is_featured)){ //get featured records
                $where['record.is_featured'] = $is_featured;
//            }
            $select = [
                'record._id',
                'record.code',
                'record.img_src',
                'record.title',
                'record.price',
                'record.sale_price',
            ];
            $records = $this->record_model->get_pagination($where, $select, 0, $limit_record);
            if(!empty($records) && count($records)){
                $data = [
                    '_id' => $category->_id,
                    'name' => !empty($category->_value) ? $category->_value : $category->name,
                ];

                $data['data_record'] = $records;

                $res[] = $data;
            } /* empty records */

        } /* foreach $categories */
        $this->response(RestSuccess(!empty($res) ? removeNullElementOfArray($res) : []), SUCCESS_CODE);
    }

    /*
     * function get_all_featured_records
     * method: post
     * params: category_id, offset, limit,
     */
    function get_all_featured_records_post()
    {
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_FEATURED_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';
        $is_featured = !empty($this->post('is_featured')) ? $this->post('is_featured') : true;

        $check_verify_params = checkVerifyParams([$is_featured]);
        if(!empty($check_verify_params)){
            $this->response($check_verify_params, BAD_REQUEST_CODE);
        }

        $where = [
            'category.is_active' => true,
            'category.is_delete' => false,
        ];

        $where['record.is_featured'] = $is_featured;
        $select = [
            'record._id',
            'record.code',
            'record.img_src',
            'record.title',
            'record.price',
            'record.sale_price',
        ];
        $records = $this->record_model->get_pagination($where, $select, $offset, $limit, $last_id);
        $res = !empty($records) ? removeNullElementOfArray($records) : [];

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function get_all_records_by_category
     * method: post
     * params: category_id, offset, limit,
     */
    //get sản phẩm theo category
    function get_all_records_by_category_post()
    {
       // $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $_key = $this->post('_key');
        $mismath=$_key;
        if (empty($_key)){
            $category_id = $this->post('category_id');
           // dump($category_id,true);
            $mismath=$category_id;
        }

        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_RECORD_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';

            $check_verify_params = checkVerifyParams([$mismath]);
            if(!empty($check_verify_params)){
                $this->response($check_verify_params, BAD_REQUEST_CODE);
            }

        $where = [
            'category.is_active' => true,
            'category.is_delete' => false,
        ];

        //truy vấn tìm category_id list
        $cate_id_key = array();
        if (!empty($_key)){
          //  $where['category._key']=$_key;
            if (!empty($_key)){
                foreach ($_key as $value_key){
                    $where['category._key']= $value_key;
                    $select=array(
                        'category.*'
                    );
                    $category = $this->category_model->findOne($where, $select);
                    if (!empty($category->_id)) {
                        $cate_id_key[] = $category->_id;
                    }
                }
            }
        }else{
            //nếu $_key rỗng thì chuyển đúng định dạng như trên
            if (!empty($category_id) && count($category_id)) {
                foreach ($category_id as $value_key) {
                    $cate_id_key[] = $value_key ;
                }
            }
        }

       // dump($cate_id_key,true);
        // xử lý điều kiện hoặc
        $where = "
        record.is_active= 1 and  record.is_delete = 0 and 
        (   ";

        if (!empty($cate_id_key) && count($cate_id_key)){
            for ($i=0; $i<count($cate_id_key) ;$i++){
                if ($i == 0) {
                    $where .= " category_has_record.category_id = $cate_id_key[$i] ";
                }else{
                    $where .= " or category_has_record.category_id =  $cate_id_key[$i]";
                }
            }
        }
        $where .= " ) ";

        $select = [
            'record._id',
            'record.code',
            'record.img_src',
            'record.title',
           // 'record.price',
            'record.sale_price',
           // 'record.wholesale_price',
            'record.coin',
         //   'record.point_evaluation',
            'record.point_rating',
            'record.longitude',
            'record.latitude',
            'account.fullname'
        ];
        $records = $this->record_model->get_pagination($where, $select, $offset, $limit, $last_id);
        //dump(count($records,true));
        $res = !empty($records) ? removeNullElementOfArray($records) : [];

        $this->response(RestSuccess($res), SUCCESS_CODE);

    }

    /*
     * function get record detail
     * method: get
     * params: record_id
     */
    //oganban
    public function get_record_detail_get()
    {
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $last = $this->uri->total_segments();
        $record_id = $this->uri->segment($last);
        $check_verify_params = checkVerifyParams([$record_id]);
        if(!empty($check_verify_params)){
            $this->response($check_verify_params, BAD_REQUEST_CODE);
        }

        $where = [
            'record._id' => $record_id,
            'record.is_active' => true,
            'record.is_delete' => false,
        ];
        $select = [
            'record._id',
            //'record.code',
            //'record.barcode',
            'record.img_src',
            'record.title',
            'record.price',
            'record.sale_price',
            'record.coin',
            'record._description',
            'account.fullname',
            'record.address',
            'record.address_2',
            'record.about',
            'record.quantity',
            'account_info.phone',
            'record.deadline_post',
        ];
        $record = $this->record_model->findOne($where, $select);
        if(empty($record)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }
        $record->deadline_post = date("d/m/Y", strtotime( $record->deadline_post));
        $record->img_src = [$record->img_src];
        /*$where = [
            'record_file.record_id' => $record_id,
            'record_file.is_active' => true,
            'record_file.is_delete' => false,
            'record_file.is_tmp' => false,
        ];
        $select = [
            'record_file._id',
            'record_file.file_type',
            'record_file.thumb_file_src',
            'record_file.file_src',
            'record_file.file_size',
        ];
        $record_files = $this->record_file_model->find($where, $select);
        $record->data_file = !empty($record_files) ? $record_files : [];*/

        $res = !empty($record) ? removeNullOfObject($record) : '';

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function search record
     * method: get
     * params: keyword, offset, limit, last_id
     */
    public function search_record_post()
    {
        //$this->writeLog('search', json_encode($this->post(), JSON_UNESCAPED_UNICODE));
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        $keyword = trim(skipVN($this->post('keyword'), true));
        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';

        $check_verify_params = checkVerifyParams([$keyword]);
        if(!empty($check_verify_params)){
            $this->response($check_verify_params, BAD_REQUEST_CODE);
        }

        $where = [
            'record.plain_title like' => '%'. $keyword .'%',
            'record.is_active' => true,
            'record.is_delete' => false,
            'category.is_active' => true,
            'category.is_delete' => false,
        ];
        $select = [
            'record._id',
            'record.code',
            'record.img_src',
            'record.title',
            'record.price',
            'record.sale_price',
        ];
        $records = $this->record_model->get_pagination($where, $select, $offset, $limit, $last_id);
        $res = !empty($records) ? removeNullElementOfArray($records) : [];

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    //
    /*
     * function search record
     * method: get
     * params: keyword, offset, limit, last_id
     */
    //thêm mới một sản phẩm oganban
    public function add_record_post()
    {
        //lấy id người bán
        $account_id = $this->session->userdata('ACCOUNT_ID');
        if(empty($account_id)){
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        //$this->writeLog('search', json_encode($this->post(), JSON_UNESCAPED_UNICODE));
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;

        //
        $category_id = $this->post('category_id');
        $file_attach = $this->post('img_src');
        $title = $this->post('title');
        $quantity = $this->post('quantity');
        $deadline_post = $this->post('deadline_post');
        $abount_record = $this->post('abount_record');

        $address = $this->post('address');
        $address_2 = $this->post('address_2');
        $position_gps = $this->post('position_gps');
        $cash = $this->post('cash');
        $exchange_coin = $this->post('exchange_coin');
        //cần phân biệt sản phẩm và dịch vụ


        $check_verify_params = checkVerifyParams(array(
            $category_id,
            $file_attach,
            $title
        ));
        if(!empty($check_verify_params)){
            $this->response($check_verify_params, BAD_REQUEST_CODE);
        }
        //
        $data_upload = array(
            'title' => $title,
            'plain_title' => skipVN($title, true),
            'quantity' => $quantity,
            'deadline_post' => $deadline_post,
            'abount_record' => $abount_record,
            'address' => $address,
            'address_2' => $address_2,
            'position_gps'=>$position_gps,
            'cash' => $cash,
            'exchange_coin' => $exchange_coin,
            'create_time' => CURRENT_TIME,
            'update_time' => CURRENT_TIME,
            'create_time_mi' => CURRENT_MILLISECONDS,
            'update_time_mi' => CURRENT_MILLISECONDS,
        );

        //insert account tb
        $this->record_model->create($data_upload);

        $record_id = $this->db->insert_id();

        //thêm hình vào
        if (!empty($file_attach) && count($file_attach)) {
            $data_file_attach = array();
            foreach ($file_attach as $file_src_u) {
                //dump(basename($file_src_u),true);
                if (file_exists($file_src_u)) {
                    $file_name = basename($file_src_u);
                    $path = 'public/upload/img/record/';
                    if (!is_dir($path)) {
                        mkdir('./' . $path, 0777, TRUE);
                    }

                    rename('./' . $file_src_u, './' . $path . $file_name);
                    $file_src = $path . $file_name;

                    $this->post_file_model->create(array(
                        'file_name' => $file_name,
                        'file_src' => $file_src,
                        'post_id' => $record_id,
                        'create_time' => CURRENT_TIME,
                        'update_time' => CURRENT_TIME,
                        'create_time_mi' => CURRENT_MILLISECONDS,
                    ));

                    $data_file_attach[] = $file_src;
                }
            }

        }

        //thêm trong bảng category_has_rocord
        $data_cate_has_record = array(
            'category_id' => $category_id,
            'record_id' => $record_id,
        );

        //insert account tb
        $this->category_has_record_model->create($data_cate_has_record);


        $res = !empty($records) ? removeNullElementOfArray($records) : [];

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function search record
     * method: get
     * params: keyword, offset, limit, last_id
     */
    public function get_record_seller_post_post()
    {
        //lấy id người bán
        $account_id = $this->session->userdata('ACCOUNT_ID');
        if(empty($account_id)){
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        //$this->writeLog('search', json_encode($this->post(), JSON_UNESCAPED_UNICODE));
        $language_code = !empty($this->input->request_headers()['Language']) ? $this->input->request_headers()['Language'] : LANGUAGE_DEFAULT;
        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_RECORD_POST_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';
        //tin hết hạn ẩn chung lun
        $status = !empty($this->post("status")) ? $this->post("status") : SHOW_RECORD ;

        if ($status == HIDE_RECORD){
            /*$where = array(

                "record.is_delete" => false,
                "record.is_active" => false,
                "record.account_id" => $account_id // set account_id trước chứ chưa cón
            );*/
            $where = "
        record.is_delete = 0 and   record.account_id = $account_id and
        (    record.is_active = 0 or record.deadline_post <  '".CURRENT_DATE."' )";



        }else{
            $where = array(
                "record.is_delete" => false,
                "record.is_active" => true,
                "record.deadline_post >= " => CURRENT_DATE,
                "record.account_id" => $account_id // set account_id trước chứ chưa cón
            );
        }

        $select = [
            'record._id',
          //  'record.code',
            'record.img_src',
            'record.title',
            //'record.price',
            'record.sale_price',
            //'record.wholesale_price',
            'record.coin',
            //'record.point_evaluation',
            'record.point_rating',
            'record.quantity',
            'record.deadline_post',
            'record.is_active',
            'record.create_time_mi',

            //trả về thêm tin hết hạn hoặc ẩn đọc mockup

        ];
        $records = $this->record_model->get_pagination($where, $select, $offset, $limit, $last_id);

        if ($status == HIDE_RECORD){
            $count_hide = 0;
            $count_expired = 0;
        if (!empty($records) && count($records)){
            foreach ($records as $value_item){
                if ($value_item->is_active == false && $value_item->deadline_post < CURRENT_DATE){
                    $value_item->status = HIDE_RECORD;
                    $count_hide++;
                }elseif ($value_item->is_active == false){
                    $value_item->status = HIDE_RECORD;
                    $count_hide++;
                }
                elseif ($value_item->deadline_post < CURRENT_DATE){
                    //hết hạn
                    $value_item->status = EXPIRED;
                    $count_expired++;
                }
            }
        }
        }


        //dump(count($records,true));

            $rs = [
                'status' => SUCCESS_CODE,
                'message' => OK_MSG,
                'data' => !empty($records) ? removeNullElementOfArray($records) : [],
            ];
        if ($status == HIDE_RECORD){
            $rs[ 'count_expired'] = $count_expired;
            $rs["count_hide"] = $count_hide;
        }else{
            if (empty($records)){
                $rs["count_show"] = 0;
            }else{
                $rs["count_show"] = count($records);
            }

        }
        $this->response($rs, SUCCESS_CODE);

       // $this->response($rs, SUCCESS_CODE);
    }


    function get_featured_records_post()
    {


        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_FEATURED_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';
        //$is_featured = !empty($this->post('is_featured')) ? $this->post('is_featured') : true;

       /* $check_verify_params = checkVerifyParams([$is_featured]);
        if(!empty($check_verify_params)){
            $this->response($check_verify_params, BAD_REQUEST_CODE);
        }*/

        $where = [
            'record.is_active' => true,
            'record.is_delete' => false,
            'record.is_featured' => true
        ];

        $select = [
            'record._id',
            'record.code',
            'record.img_src',
            'record.title',
            'record.price',
            'record.sale_price',
            'record.wholesale_price',
            'record.is_featured',
            'record.point_evaluation',
        ];
        $records = $this->record_model->get_pagination_chage($where, $select, $offset, $limit, $last_id);
        $res = !empty($records) ? removeNullElementOfArray($records) : [];

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    function get_sale_records_post()
    {
        $offset = !empty($this->post('offset')) ? $this->post('offset') : 0;
        $limit = !empty($this->post('limit')) ? $this->post('limit') : LIMIT_FEATURED_DEFAULT;
        $last_id = !empty($this->post('last_id')) ? $this->post('last_id') : '';


        $where = " record.is_active = 1 and record.is_delete = 0 and record.price <> record.sale_price ";

        $select = [
            'record._id',
            'record.code',
            'record.img_src',
            'record.title',
            'record.price',
            'record.sale_price',
            'record.wholesale_price',
            'record.point_evaluation',
        ];
        $records = $this->record_model->get_pagination_chage($where, $select, $offset, $limit, $last_id);
        $res = !empty($records) ? removeNullElementOfArray($records) : [];

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

}