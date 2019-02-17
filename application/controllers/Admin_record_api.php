<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Admin_record_api extends REST_Controller
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
        $res = RestSuccess('admin/record_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
     * function get record list
     * method: post
     * params: offset, limit
     */
    function get_record_list_post(){
        $keyword = !empty($this->post('search')['value']) ? $this->post('search')['value'] : '';
        $offset = !empty($this->post('start')) ? $this->post('start') : 0;
        $limit = !empty($this->post('length')) ? $this->post('length') : LIMIT_DEFAULT;
        $category_val=$this->post("category_val");
        $where = array(
            'record.is_delete' => false,
            /*'category.is_delete' => false,*/
            'record.plain_title like' => '%'. skipVN($keyword, true) .'%',
        );
        if (!empty($category_val)){

            $where["category_has_record.category_id"]=$category_val;
        }
        $select = array(
            'record.*',
            '(SELECT count(*) FROM category_has_record WHERE record_id = record._id and is_delete = 0 ) AS count_cate',
            /*'category.name as category_name',*/
        );
        $records = $this->record_model->get_pagination_chage($where, $select, $offset, $limit);
        if (!empty($category_val)) {
            $total_records = $this->record_model->count_total($where);
        }else{
            $where = array(
                'record.is_delete' => false,
                /*'category.is_delete' => false,*/
                'record.plain_title like' => '%'. skipVN($keyword, true) .'%',
            );
            $total_records = $this->record_model->count_total_all($where);
        }
        $rs = [
            'status' => SUCCESS_CODE,
            'message' => OK_MSG,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => !empty($records) ? $records : [],
        ];
        $this->response($rs, SUCCESS_CODE);
    }

    /*
     * function import excel
     * method: post
     * params:
     */
    function import_excel_post()
    {
        if(empty($_FILES['file']['name'])){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }
        $path = $_FILES['file']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if (!in_array($ext, getExtenstions(EXCEL_FILE_TYPE))) {
            $this->response(RestBadRequest(INVALID_EXCEL_FILE_MSG), BAD_REQUEST_CODE);
        }
        $this->load->library('excel');
        $this->load->library("upload_library");
        //save Excel input file
        $upload_path = RECORD_UPLOAD_PATH;
        if(!file_exists($upload_path)){
            mkdir($upload_path, 0777, TRUE);
        }
        $upload_data = $this->upload_library->upload_excel('file', $upload_path);		//excel_file: key in upload form
        $filename = $upload_path.'/'.$upload_data['file_name'];
        $objPHPExcel = PHPExcel_IOFactory::load($filename);

        $data_arr = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        foreach($data_arr as $key => &$row) {
            $row = array_filter($row,
                function($cell) {
                    return !is_null($cell);
                }
            );
            if (count($row) == 0) {
                unset($data_arr[$key]);
            }
        }
        unset ($row);
        $list_err = array();
        $arr_err = array();
        $ar = array();
        $import_Data = array();
        $last_key = key( array_slice( $data_arr, -1, 1, TRUE ) );
        //exception value import
        for ($i = 3; $i <= $last_key; $i++)
        {

            $value = $data_arr[$i];
            $messerr = array();

            if(empty($value['B']) &&
                empty($value['D']) &&
                empty($value['F']) &&
                empty($value['H']) &&
                empty($value['I'])
            ){
                continue;
            }

            array_push($ar, $data_arr[$i]);
//            var_dump($i);
            $err = '';



            //check null
            if(empty($value['C']))//check code user
            {
                array_push($messerr,"Category rỗng");
            }

            if(empty($value['E']))//check code
            {
                array_push($messerr,"Product Code rỗng");
            }
//            else{
//                $code_exists = $this->record_model->findOne([
//                    'record.code' => $value['D'],
//                    'record.is_delete' => false,
//                ]);
//                if(!empty($code_exists))
//                {
//                    array_push($messerr,"Product Code đã tồn tại");
//                }
//            }

            if(empty($value['G']))//check title
            {
                array_push($messerr,"Title rỗng");
            }

            if(empty($value['I']))//check price
            {
                array_push($messerr,"Price rỗng");
            }
            else{
                if($value['I'] <= 0){
                    array_push($messerr,"Price phải > 0");
                }
            }

            if(empty($value['J']))//check sale_price
            {
                array_push($messerr,"Sale Price rỗng");
            }
            else{
                $check = preg_match('/^[a-zA-Z]+$/', $value['J']);
                if ($check) {
                    array_push($messerr, "Trường sale price vui lòng nhập số");
                }
            }

           /* if(empty($value['K']))//check price
            {
                array_push($messerr,"Đánh giá rỗng");
            }
            else{*/
           /*if (!empty($value['K'])){*/

            if (!empty($value['K'])) {
                $check = preg_match('/^[a-zA-Z]+$/', $value['K']);
                if ($check) {
                    array_push($messerr, "Trường thêm vui lòng nhập số");
                }
            }

            if (isset($value['K'])) {
                if ($value['K'] < 1 || $value['K'] > 5) {
                    array_push($messerr, "Đánh giá phải lớn hơn hoặc bằng 1 hoặc nhỏ hơn 5 ");
                }
            }

            /*}*/
//            else{
//                if($value['I'] <= 0 || $value['I'] < $value['H']){
//                    array_push($messerr,"Sale Price phải > 0 và <= Price");
//                }
//            }

            if(count($messerr) > 0){
                //$err['line'] = $i;
                $err = array($i);
                array_push($list_err, $err);
                $mess = $comma_separated = implode(", ", $messerr);
                $arr_err[$i] = $mess;
            }else{
                array_push($import_Data, $value);
            }
        }

        if(count($arr_err) > 0)
        {
            $this->returnErrorImport($data_arr, $arr_err, $filename,$upload_data['file_ext']);
            $err = json_encode($arr_err, JSON_UNESCAPED_UNICODE);
            $this->response(RestBadRequest($err), BAD_REQUEST_CODE);
//            $this->exoport_file_error($list_err);
        }else{
//            $this->response(RestSuccess($import_Data), SUCCESS_CODE);
            $data_insertRecords = $this->insertRecords($import_Data);
            if(!empty($data_insertRecords)){
                $res = array(
                    'count_update' => $data_insertRecords['count_update'],
                    'count_insert' => $data_insertRecords['count_insert'],
                );
                $this->response(RestSuccess($res), SUCCESS_CODE);
            }
            else{
                $this->response(RestBadRequest(UPLOAD_WARNING), BAD_REQUEST_CODE);

            }
        }

    }

    /*
     * function insert records
     */
    function insertRecords($data_arr){

        $count_update = 0;
        $count_insert = 0;

        //transaction:  add new record if isn't fail
        $this->db->trans_start();
        $last_key = key( array_slice( $data_arr, -1, 1, TRUE ) );

        for ($j = 0; $j <= $last_key; $j++){ //$j is row number to get data of excel file
            $array = $data_arr[$j];
             $arr_cate= explode(';', $array['C']);
            $cate_id=array();
             if (!empty($arr_cate)){
                 foreach ($arr_cate as $item){
                     //get category_id
                     $where = array(
                         'category.plain_name like' => skipVN(trim($item), true),
                         'category.is_delete' => false,
                     );
                     $select = array(
                         'category.*'
                     );

                     $category_exists = $this->category_model->findOne($where, $select);
                     if(empty($category_exists)){
                         //insert new category
                         $this->category_model->create([
                             'name' => trim($item),
                             'plain_name' => skipVN(trim($item), true),
                             '_key' => generateKeyTranslation(),
                             'is_active' => true,
                             'is_featured'=>false,
                             'is_fixed' => false,
                             'create_time' => CURRENT_TIME,
                             'update_time' => CURRENT_TIME,
                             'create_time_mi' => CURRENT_MILLISECONDS
                         ]);

                         $cate_id[] = $this->db->insert_id();
                     }
                     else{
                         $cate_id[] = $category_exists->_id;
                     }

                 }
             }
          //  $testtest[$j]=$cate_id;

            $ordinal = !empty($array['B']) ? (int) $array['B'] : 1;
            $barcode = trim($array['D']);
            $code = trim($array['E']);
            $title = $array['G'];
            $description = $array['H'];
            $price = $array['I'];
            $sale_price = $price;
            if(!empty($array['J'])){
                if($array['J'] < $array['I']){
                    $sale_price = $array['J'];
                }
            }
            $point_evaluation = !empty($array['K']) ? $array['K']: 0;
            $is_featured = !empty($array['L']) ? $array['L'] : 0 ;


            $where = array(
                'record.code' => $code,
                'record.is_delete' => false,
            );
            $select = array(
                'record.*',
                'category.name as category_name',
                'category.plain_name as category_plain_name',
            );
            $record_exists = $this->record_model->findOne($where, $select);
           // $testexists[]=$record_exists;

            if(!empty($record_exists)) {
                $data_update = array(
//                    'barcode' => $barcode,
//                    'title' => $title,
//                    'plain_title' => skipVN($title, true),
//                    '_description' => $description,
//                    'plain_description' => skipVN(removeHTMLTagsAndSpecialChar($description), true),
                    'ordinal' => $ordinal,
                    'price' => $price,
                    'sale_price' => $sale_price,
                    'is_featured' => $is_featured,
                    'point_evaluation' => $point_evaluation,
                    'update_time' => CURRENT_TIME,
                );

                $this->record_model->update_by_condition($where, $data_update);
                $count_update++;

                $record_id = $record_exists->_id;
            }
            else{
                $data_insert = array(
                    'code' => $code,
                    'barcode' => $barcode,
                    'title' => $title,
                    'plain_title' => skipVN($title, true),
                    '_description' => $description,
                    'plain_description' => skipVN(removeHTMLTagsAndSpecialChar($description), true),
                    '_key' => generateKeyTranslation(),
                    'ordinal' => $ordinal,
                    'price' => $price,
                    'sale_price' => $sale_price,
                    'is_featured' => $is_featured,
                    'point_evaluation' => $point_evaluation,
                    'create_time' => CURRENT_TIME,
                    'update_time' => CURRENT_TIME,
                    'create_time_mi' => CURRENT_MILLISECONDS
                );

                $this->record_model->create($data_insert);
                $count_insert++;

                $record_id = $this->db->insert_id();
            }
            /*dump($record_id,true);*/
            //record exists & category exists before
            /*if(!empty($record_exists)){*/
                //delete to add new
              if (!empty($record_id)) {
                  $this->category_has_record_model->delete_by_condition(array(
                      'record_id' => $record_id,
                  ));
              }
            /*}*/
//            else{
                //insert category_has_record
               // dump($record_id,true);
                if (!empty($cate_id)) {
                foreach ($cate_id as $item_cate) {
                    $this->category_has_record_model->create(array(
                        'category_id' => $item_cate,
                        'record_id' => $record_id,
                    ));
                    //$category_id_test[] = $this->db->insert_id();
                    //$test[]=[$item_cate,$record_id];
                }
                }
               // dump('dung',true);
            //dump($category_id_test,true);
//            }


            //get image list
            if(!empty($array['F'])){
                $images = explode(',', $array['F']);
                if(!empty($images) && count($images)){
                    $loop_image = 1;
                    foreach($images as $image){
                        if($loop_image > MAX_FILE_UPLOAD){
                            break;
                        }

                        //replace every instance of the " " character to the character "%20"
                        $image = str_replace(' ', '%20', $image);

                        $img_src = RECORD_PATH . '/' . $image;
                        if($loop_image == 1){ //insert img_src of record
                            $this->record_model->update_by_condition([
                                '_id' => $record_id
                            ], [
                                'img_src' => $img_src
                            ]);


                        }
                        else { //insert record_file
                            $this->record_file_model->create([
                                'file_src' => $img_src,
                                'record_id' => $record_id,
                                'ordinal' => $loop_image,
                                'create_time' => CURRENT_TIME,
                                'update_time' => CURRENT_TIME,
                            ]);
                        }

                        $loop_image++;
                    } //end foreach images
                }
            }

        } //end for products

       // dump($testtest,true);
        //dump($testexists,true);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }else{
            return array(
                'count_update' => $count_update,
                'count_insert' => $count_insert,
            );
        }
    }

    /*
     * function add record
     * method: post
     * params: code, barcode, title, ...
     */
    function add_new_record_post()
    {

        //Product info
        $code = $this->post('code');
        $barcode = $this->post('barcode');
        $title = $this->post('title');
        $short_description = $this->post('short_description');
        $description = $this->post('description');
        $price = str_replace(',', '', $this->post('price'));
        $sale_price = str_replace(',', '', $this->post('sale_price'));
        $is_featured = !empty($this->post('is_featured')) ? 1 : 0 ;
        $is_active = !empty($this->post('is_active')) ? 1 : 0 ;
        $category_id = $this->post('category_id');
        $rating = str_replace(',', '', $this->post('rating'));
        $point_elv= !empty($this->post('point_elv')) ? $this->post('point_elv') : 0 ;

        $check_verify_params = checkVerifyParams(array(
            $title,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }
       /* $value_isset=$this->post('point_elv');*/
        /*if (isset($value_isset)) {
            if ($point_elv < 1 || $point_elv > 5) {
                $this->response(RestBadRequest(POINT_CHECK_MSG), BAD_REQUEST_CODE);
            }
        }else{
            dump('test',true);
        }*/
        //Check sale price if larger than price
        if($sale_price > $price){
            $sale_price = $price;
        }
        if($rating > $sale_price){
            $rating = $sale_price;
        }

        //Check sale price is empty
        if(empty($sale_price)){
            $sale_price = $price;
        }

        $data_upload = array(
            'code' => $code,
            'barcode' => $barcode,
            'title' => $title,
            'plain_title' => skipVN($title, true),
            '_description' => $description,
            'short_description' => $short_description,
            'plain_description' => skipVN(removeHTMLTagsAndSpecialChar($description), true),
            '_key' => generateKeyTranslation(),
            'price' => $price,
            'sale_price' => $sale_price,
            'wholesale_price' => $rating,
            'is_featured' => $is_featured,
             'point_evaluation'=>$point_elv,
            'is_active' => $is_active,
            'create_time' => CURRENT_TIME,
            'update_time' => CURRENT_TIME,
            'create_time_mi' => CURRENT_MILLISECONDS
        );

        if(!empty($_FILES['img_src']['name'])){
            $upload_dir = FOLDER_FILE_UPLOAD_RECORD;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, TRUE);
            }
            //upload img_src
            $img_path = $_FILES['img_src']['name'];
            $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
            $config['upload_path'] = './public/upload/img/record';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = strtotime('now') .'.'. $img_path_ext;

            //Load upload library and initialize configuration
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('img_src')) {
                $data_upload['img_src'] = 'public/upload/img/record/'. $config['file_name'];
            }
            else{
                $this->response(RestServerError(), SERVER_ERROR_CODE);
            }
        }

        //insert account tb
        $this->record_model->create($data_upload);

        $record_id = $this->db->insert_id();
        //dump($category_id,true);
        //insert category_has_record tb

        if (!empty($category_id)) {
            foreach ($category_id as $value) {
                $this->category_has_record_model->create(array(
                    'category_id' => $value,
                    'record_id' => $record_id,
                ));
            }
        }
        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    function update_record_post()
    {

        //Product info
        $record_id = $this->post('record_id');
        $title = $this->post('title');
        $description = $this->post('description');
        $short_description = $this->post('short_description');
        $price = str_replace(',', '', $this->post('price'));
        $sale_price = str_replace(',', '', $this->post('sale_price'));
        $is_featured = !empty($this->post('is_featured')) ? 1 : 0 ;
        $is_active = !empty($this->post('is_active')) ? 1 : 0 ;
        $category_id = $this->post('category_id');
        $rating = str_replace(',', '', $this->post('rating'));
        $point_elv= !empty($this->post('point_elv')) ? $this->post('point_elv') : 0 ;

        $check_verify_params = checkVerifyParams(array(
            $record_id,
            $title,
        ));

        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        /*if($point_elv <= 0 || $point_elv > 5 ){
            $this->response(RestBadRequest(POINT_CHECK_MSG), BAD_REQUEST_CODE);
        }*/
        //Check sale price if larger than price
        if($sale_price > $price){
            $sale_price = $price;
        }
        if($rating > $price){
            $rating = $price;
        }

        //Check sale price is empty
        if(empty($sale_price)){
            $sale_price = $price;
        }

        $where = array (
            'record._id' => $record_id,
            'record.is_delete' => false,
        );
        $record = $this->record_model->findOne($where, '*');
        if(empty($record)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }

       /* $where = array(
            'category._id' => $category_id
        );
        $select = array(
            'category.*'
        );

        $category = $this->category_model->findOne($where, $select);*/

        $data_upload = array(
            'title' => $title,
            'plain_title' => skipVN($title, true),
            '_description' => $description,
            'short_description' => $short_description,
            'plain_description' => skipVN(removeHTMLTagsAndSpecialChar($description), true),
            'price' => $price,
            'sale_price' => $sale_price,
            'wholesale_price' => $rating,
            'point_evaluation'=>$point_elv,
            'is_featured' => $is_featured,
            'is_active' => $is_active,
            'update_time' => CURRENT_TIME,
        );

        //upload img_src
        if(!empty($_FILES['img_src']['name'])) {
            $upload_dir = FOLDER_FILE_UPLOAD_RECORD;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, TRUE);
            }
            $img_path = $_FILES['img_src']['name'];
            $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
            $config['upload_path'] = './public/upload/img/record';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = strtotime('now') . '.' . $img_path_ext;

            //Load upload library and initialize configuration
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            //delete old file
            if (!empty($record->img_src)){
                if (file_exists(UPLOAD_PATH . $record->img_src)) {
                    unlink(UPLOAD_PATH . $record->img_src);
                }
            }

            if ($this->upload->do_upload('img_src')) {
                $data_upload['img_src'] = 'public/upload/img/record/'. $config['file_name'];
            }
            else{
                $this->response(RestServerError(), SERVER_ERROR_CODE);
            }
        }

        //update record tb
        $this->record_model->update_by_condition(array(
            '_id' => $record_id
        ),$data_upload);

        $this->category_has_record_model->delete_by_condition(array(
            'record_id' => $record_id,
        ));
        //update category_has_record tb
        if (!empty($category_id)) {
            foreach ($category_id as $value) {
                $this->category_has_record_model->create(array(
                    'category_id' => $value,
                    'record_id' => $record_id,
                ));
            }
        }
//        dump($data_upload);

        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    function delete_record_put()
    {
        $last = $this->uri->total_segments();
        $record_id = $this->uri->segment($last);
        $is_delete = !empty($this->put('is_delete')) ? 1 : 0;

        $check_verify_params = checkVerifyParams(array(
            $record_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $this->record_model->update_by_condition(array(
            '_id' => $record_id
        ), array(
            'is_delete' => $is_delete
        ));

        $where = array(
            'record._id' => $record_id
        );
        $select = array(
            'record.is_delete'
        );

        $record = $this->record_model->findOne($where, $select);

        $this->response(RestSuccess($record), SUCCESS_CODE);
    }

    function record_toggle_is_active_put(){
        $last = $this->uri->total_segments();
        $record_id = $this->uri->segment($last);
        $is_active = !empty($this->put('is_active')) ? 1 : 0;

        $check_verify_params = checkVerifyParams(array(
            $record_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }


        $this->record_model->update_by_condition(array(
            '_id' => $record_id
        ), array(
            'is_active' => $is_active
        ));

        $where = array(
            'record._id' => $record_id
        );
        $select = array(
            'record.is_active'
        );

        $record = $this->record_model->findOne($where, $select);

        $this->response(RestSuccess($record), SUCCESS_CODE);
    }

    function record_toggle_is_featured_put(){
        $last = $this->uri->total_segments();
        $record_id = $this->uri->segment($last);
        $is_featured = !empty($this->put('is_featured')) ? 1 : 0;

        $check_verify_params = checkVerifyParams(array(
            $record_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }


        $this->record_model->update_by_condition(array(
            '_id' => $record_id
        ), array(
            'is_featured' => $is_featured
        ));

        $where = array(
            'record._id' => $record_id
        );
        $select = array(
            'record.is_featured'
        );

        $record = $this->record_model->findOne($where, $select);

        $this->response(RestSuccess($record), SUCCESS_CODE);
    }

    /*
     * function add record files
     * method: post
     * params: files
     */
    function add_record_files_post(){
        $record_id = $this->post('record_id');
        $files = $this->post('files');

        $check_verify_params = checkVerifyParams(array(
            $record_id,
            $files,
        ));
        if(!empty($check_verify_params) || !count($files)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $file_src = [];
        $thumb_file_src = [];
        foreach($files as $file){
            $this->record_file_model->create(array(
                'record_id' => $record_id,
                'file_name' => cleanFileName($file['name']),
                'file_type' => $file['type'],
                'file_size' => $file['size'],
                'file_src' => $file['url'],
                'thumb_file_src' => $file['thumbnailUrl'],
            ));
            $file_src[] = $file['url'];
            $thumb_file_src[] = $file['thumbnailUrl'];
        }

        $res = array(
            'file_src' => $file_src,
            'thumb_file_src' => $thumb_file_src
        );

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    function update_ordinal_post(){
        $record_id = $this->post('record_id');
        $ordinal = !empty($this->post('ordinal')) ? $this->post('ordinal') : 1;

        $check_verify_params = checkVerifyParams(array(
            $record_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), SUCCESS_CODE);
        }

        $where = array (
            'record._id' => $record_id,
            'record.is_delete' => false,
        );
        $record = $this->record_model->findOne($where, '*');
        if(empty($record)){
            $this->response(RestBadRequest(NOT_FOUND_MSG), SUCCESS_CODE);
        }

        $this->record_model->update_by_condition(array(
            'record._id' => $record_id,
        ), array(
            'record.ordinal' => $ordinal
        ));

        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    /*
     * function add record files
     * method: post
     * params: files
     */
    function delete_record_file_delete()
    {
        $record_file_id = $this->delete('record_file_id');
        $check_verify_params = checkVerifyParams(array(
            $record_file_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), SUCCESS_CODE);
        }

        $where = array (
            'record_file._id' => $record_file_id,
        );
        $record_file = $this->record_file_model->findOne($where, '*');
        if(empty($record_file)){
            $this->response(RestBadRequest(NOT_FOUND_MSG), SUCCESS_CODE);
        }

        //delete file
        if(!empty($record_file->thumb_file_src)){
            $item_explode = explode('public/', $record_file->thumb_file_src);
            $file_path = 'public/'. $item_explode[1];
            if (file_exists(UPLOAD_PATH . $file_path)) {
                unlink(UPLOAD_PATH . $file_path);
            }
        }
        if(!empty($record_file->file_src)){
            $item_explode = explode('public/', $record_file->file_src);
            $file_path = 'public/'. $item_explode[1];
            if (file_exists(UPLOAD_PATH . $file_path)) {
                unlink(UPLOAD_PATH . $file_path);
            }
        }

        $this->record_file_model->delete_by_condition(array(
            '_id' => $record_file_id,
        ));


        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    //detail department
    function detail_category_put()
    {
            $last = $this->uri->total_segments();
            $record_id = $this->uri->segment($last);
            $check_verify_params = checkVerifyParams(array(
                $record_id,
            ));
            if (!empty($check_verify_params)) {
                $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
            }

                $select = array(
                    'category_has_record.category_id',
                    'category.name'

                );
                $where = array(
                    'category.is_delete' => false,
                    'category_has_record.record_id' => $record_id

                );
                $category = $this->category_has_record_model->find_cate($where, $select);
                foreach ($category as $value){
                    $data[]=$value->name;
                }

            $this->response(RestSuccess($data), SUCCESS_CODE);

    }

}

