<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Admin_translation_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('language_model');
        $this->load->model('language_translation_model');
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $res = RestSuccess('admin/translation_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
    * function get translation list
    * method: post
    * params: offset, limit
    */
    function get_translation_list_post(){
        $keyword = !empty($this->post('search')['value']) ? $this->post('search')['value'] : '';
        $offset = !empty($this->post('start')) ? $this->post('start') : 0;
        $limit = !empty($this->post('length')) ? $this->post('length') : LIMIT_DEFAULT;
        $where = array(
            'language_translation.is_delete' => false,
            'language_translation.keyword like' => '%'. $keyword .'%',
        );
        $select = array(
            'language_translation.*',
            'language.name'
        );
        $translations = $this->language_translation_model->get_pagination($where, $select, $offset, $limit);
        $total_translations = $this->language_translation_model->count_total($where);
        $rs = [
            'status' => SUCCESS_CODE,
            'message' => OK_MSG,
            'recordsTotal' => $total_translations,
            'recordsFiltered' => $total_translations,
            'data' => !empty($translations) ? $translations : array(),
        ];
        $this->response($rs, SUCCESS_CODE);
    }

    /*
     * function add
     * method: post
     * params: username, password, email, ...
     */
    function add_new_post()
    {
        $keyword = $this->post('keyword');
        $language_id = $this->post('language_id');
        $_content = $this->post('_content');

        $check_verify_params = checkVerifyParams(array(
            $keyword,
            $language_id,
            $_content
        ));

        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        //check language
        $where = array(
            '_id' => $language_id
        );
        $language = $this->language_model->findOne($where);
        if(empty($language)){
            $this->response(RestBadRequest(LANGUAGE_NOT_FOUND_MSG), SUCCESS_CODE);
        }

        //check translation exists
        $where = array(
            'keyword' => $keyword,
            'language_id' => $language_id,
        );
        $translation_exists = $this->language_translation_model->findOne($where);
        if(!empty($translation_exists)){
            $this->response(RestBadRequest(TRANSLATION_IS_EXISTED_MSG), SUCCESS_CODE);
        }

        //insert translation tb
        $this->language_translation_model->create(array(
            'keyword' => $keyword,
            'language_id' => $language_id,
            '_content' => $_content,
            'is_app' => true,
            'create_time' => CURRENT_TIME,
            'update_time' => CURRENT_TIME,
        ));

        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    /*
     * function update
     * method: post
     * params: username, password, email, ...
     */
    function update_post()
    {
        $translation_id = $this->post('translation_id');
        $keyword = $this->post('keyword');
        $language_id = $this->post('language_id');
        $_content = $this->post('_content');

        $check_verify_params = checkVerifyParams(array(
            $translation_id,
            $keyword,
            $language_id,
            $_content
        ));

        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        //check language
        $where = array(
            '_id' => $language_id
        );
        $language = $this->language_model->findOne($where);
        if(empty($language)){
            $this->response(RestBadRequest(LANGUAGE_NOT_FOUND_MSG), SUCCESS_CODE);
        }

        //check translation exists
        $where = array(
            'keyword' => $keyword,
            'language_id' => $language_id,
            '_content <>' => $_content,
            'is_delete' => false,
        );
        $translation_exists = $this->language_translation_model->findOne($where);
        if(!empty($translation_exists)){
            $this->response(RestBadRequest(TRANSLATION_IS_EXISTED_MSG), SUCCESS_CODE);
        }

        //insert translation tb
        $this->language_translation_model->update_by_condition(array(
            '_id' => $translation_id,
        ),array(
            'keyword' => $keyword,
            'language_id' => $language_id,
            '_content' => $_content,
            'is_app' => true,
            'create_time' => CURRENT_TIME,
            'update_time' => CURRENT_TIME,
        ));

        $this->response(RestSuccess(), SUCCESS_CODE);
    }

//    function delete_put()
//    {
//        $last = $this->uri->total_segments();
//        $translation_id = $this->uri->segment($last);
//
//        $is_delete = !empty($this->put('is_delete')) ? 1 : 0;
//
//        $check_verify_params = checkVerifyParams(array(
//            $translation_id,
//        ));
//        if(!empty($check_verify_params)){
//            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
//        }
//
//        $this->language_translation_model->update_by_condition(array(
//            '_id' => $translation_id
//        ), array(
//            'is_delete' => $is_delete
//        ));
//
//        $where = array(
//            '_id' => $translation_id
//        );
//        $select = array(
//            'is_delete'
//        );
//
//        $translation = $this->language_translation_model->findOne($where, $select);
//        $res = array(
//            'is_delete' => $translation->is_delete
//        );
//
//        $this->response(RestSuccess($res), SUCCESS_CODE);
//    }

    /*
     * function import
     * method: post
     * params:
     */
    function import_post()
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
        $upload_path = TRANSLATION_UPLOAD_PATH;
        if(!file_exists($upload_path)){
            mkdir($upload_path);
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
        $last_key = key( array_slice( $data_arr, -1, 1, TRUE ) );
        //exception value import
        for ($i = 3; $i < $last_key; $i++)
        {
            array_push($ar, $data_arr[$i]);
//            var_dump($i);
            $err = '';
            $value = $data_arr[$i];
            $messerr = array();
            //check null
            if(empty($value['A']))//check code user
            {
                array_push($messerr,"Keyword is empty");
            }

            if(empty($value['B']))//check code user
            {
                array_push($messerr,"Language is empty");
            }

            //check translation exists
            $where = array(
                'keyword' => $value['A'],
                'language_id' => $value['B'],
            );
            $translation_exists = $this->language_translation_model->findOne($where);
            if(!empty($translation_exists)){
                array_push($messerr,"Translation is existed");
            }

            if(count($messerr) > 0){
                $err['line'] = $i;
                array_push($list_err, $err);
                $mess = $comma_separated = implode(", ", $messerr);
                $arr_err[$i] = $mess;
            }
        }

        if(count($arr_err) > 0)
        {
            $this->returnErrorImport($data_arr, $arr_err, $filename,$upload_data['file_ext']);
            $this->response(RestBadRequest(UPLOAD_ERROR), BAD_REQUEST_CODE);
//            $this->exoport_file_error($list_err);
        }else{
            if($this->insertDatas($data_arr)){
                $this->response(RestSuccess(UPLOAD_SUCCESS), SUCCESS_CODE);
            }
            else{
                $this->response(RestBadRequest(UPLOAD_WARNING), BAD_REQUEST_CODE);

            }
        }

    }

    /*
     * function insert records
     */
    function insertDatas($data_arr){
        //transaction:  add new record if isn't fail
        $this->db->trans_start();
        $last_key = key( array_slice( $data_arr, -1, 1, TRUE ) );
        for ($j = 2; $j <= $last_key; $j++) //$j is row number to get data of excel file
        {
            $array = $data_arr[$j];
            //get language
            $where = array(
                'name' => $array['B'],
            );
            $language = $this->language_model->findOne($where);

            if(empty($language)) {
                continue;
            }

            $keyword = trim($array['A']);
            $language_id = $language->_id;
            $_content = trim($array['C']);

            //insert translation tb
            $this->language_translation_model->create(array(
                'keyword' => $keyword,
                'language_id' => $language_id,
                '_content' => $_content,
                'is_app' => true,
                'create_time' => CURRENT_TIME,
                'update_time' => CURRENT_TIME,
            ));

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }else{
            return true;
        }
    }
}