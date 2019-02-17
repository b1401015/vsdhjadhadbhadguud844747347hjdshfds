<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Admin_banner_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('banner_model');
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $res = RestSuccess('admin/banner_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
     * function get banner list
     * method: post
     * params: offset, limit
     */
    function get_banner_list_post(){
        $keyword = !empty($this->post('search')['value']) ? $this->post('search')['value'] : '';
        $offset = !empty($this->post('start')) ? $this->post('start') : 0;
        $limit = !empty($this->post('length')) ? $this->post('length') : LIMIT_DEFAULT;
        $where = array(
            'banner.is_delete' => false,
            'banner.plain_name like' => '%'. skipVN($keyword, true) .'%',
        );
        $select = array(
            'banner.*',
            '(category.name) as category_name'
        );
        $banner = $this->banner_model->get_pagination($where, $select, $offset, $limit);
        $where = array(
            'banner.is_delete' => false,
        );
        $total_records = $this->banner_model->count_total($where);
        $rs = [
            'status' => SUCCESS_CODE,
            'message' => OK_MSG,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => !empty($banner) ? $banner : [],
        ];
        $this->response($rs, SUCCESS_CODE);
    }

    /*
     * function add news
     * method: post
     * params: title, img_src, _description
     */
    function add_new_banner_post()
    {

        //Banner info
        $name = $this->post('name');
        $description = $this->post('_description');
        $ordinal = $this->post('ordinal');
        $is_active = !empty($this->post('is_active')) ? 1 : 0 ;
        $category_id = !empty($this->post('category_id')) ? $this->post('category_id') : null;
        $check_verify_params = checkVerifyParams(array(
            $name,
            $_FILES['img_src']['name'],
        ));

        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }
        $upload_dir = FOLDER_FILE_UPLOAD_BANNNER;
        if (!is_dir($upload_dir)) {
            mkdir('./' . $upload_dir, 0777, TRUE);
        }
        //upload img_src
        $img_path = $_FILES['img_src']['name'];
        $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
        $config['upload_path'] = './public/upload/img/banner';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name'] = strtotime('now') .'.'. $img_path_ext;

        //Load upload library and initialize configuration
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload('img_src')) {

            //insert banner tb
            $this->banner_model->create(array(
                'name' => $name,
                'plain_name' => skipVN($name, true),
                '_description' => $description,
                'plain_description' => skipVN(removeHTMLTagsAndSpecialChar($description), true),
                '_key' => generateKeyTranslation(),
                'img_src' => 'public/upload/img/banner/'. $config['file_name'],
                'ordinal' => $ordinal,
                'is_active' => $is_active,
                'category_id' => $category_id,
                'create_time' => CURRENT_TIME,
                'update_time' => CURRENT_TIME,
                'create_time_mi' => CURRENT_MILLISECONDS
            ));


            $this->response(RestSuccess(), SUCCESS_CODE);
        }
        else{
            //$error = array('error' => $this->upload->display_errors());
            //$this->response(RestServerError($error), SERVER_ERROR_CODE); kiểm tra lỗi
            $this->response(RestServerError(), SERVER_ERROR_CODE);
        }
    }

    function update_banner_post()
    {

        //Product info
        $banner_id = $this->post('banner_id');
        $name = $this->post('name');
        $description = $this->post('_description');
        $ordinal = $this->post('ordinal');
        $is_active = !empty($this->post('is_active')) ? 1 : 0 ;
        //$category_id = $this->post('category_id');
        $category_id = !empty($this->post('category_id')) ? $this->post('category_id') : null;

        $check_verify_params = checkVerifyParams(array(
            $banner_id,
            $name,
        ));

        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $where = array (
            '_id' => $banner_id,
            'is_delete' => false,
        );

        $banner = $this->banner_model->findOne($where, '*');
        if(empty($banner)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }

        $data_upload = array(
            'name' => $name,
            'plain_name' => skipVN($name, true),
            '_description' => $description,
            'category_id' => $category_id,
            'ordinal' => $ordinal,
            'plain_description' => skipVN(removeHTMLTagsAndSpecialChar($description), true),
            'is_active' => $is_active,
            'update_time' => CURRENT_TIME,
        );

        //upload img_src
        if(!empty($_FILES['img_src']['name']))
        {
            $upload_dir = FOLDER_FILE_UPLOAD_BANNNER;
            if (!is_dir($upload_dir)) {
                mkdir('./' . $upload_dir, 0777, TRUE);
            }
            $img_path = $_FILES['img_src']['name'];
            $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
            $config['upload_path'] = './public/upload/img/banner';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = strtotime('now') .'.'. $img_path_ext;

            //Load upload library and initialize configuration
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            //delete old file
            if(file_exists(UPLOAD_PATH. $banner->img_src)) {
                unlink(UPLOAD_PATH . $banner->img_src);
            }

            if ($this->upload->do_upload('img_src')) {
                $data_upload['img_src'] = 'public/upload/img/banner/'. $config['file_name'];
            }
            else{
                $this->response(RestServerError(), SERVER_ERROR_CODE);
            }
        }


        //update account tb
        $this->banner_model->update_by_condition(array(
                '_id' => $banner_id
            )
            ,$data_upload);

        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    function delete_banner_put()
    {
        $last = $this->uri->total_segments();
        $banner_id = $this->uri->segment($last);
        $is_delete = !empty($this->put('is_delete')) ? 1 : 0;

        $check_verify_params = checkVerifyParams(array(
            $banner_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $this->banner_model->update_by_condition(array(
            '_id' => $banner_id
        ), array(
            'is_delete' => $is_delete
        ));

        $where = array(
            '_id' => $banner_id
        );
        $select = array(
            'is_delete'
        );

        $banner = $this->banner_model->findOne($where, $select);

        $this->response(RestSuccess($banner), SUCCESS_CODE);
    }

    function banner_toggle_is_active_put(){
        $last = $this->uri->total_segments();
        $banner_id = $this->uri->segment($last);
        $is_active = !empty($this->put('is_active')) ? 1 : 0;

        $check_verify_params = checkVerifyParams(array(
            $banner_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $this->banner_model->update_by_condition(array(
            '_id' => $banner_id
        ), array(
            'is_active' => $is_active
        ));

        $where = array(
            '_id' => $banner_id
        );
        $select = array(
            'is_active'
        );

        $banner = $this->banner_model->findOne($where, $select);

        $this->response(RestSuccess($banner), SUCCESS_CODE);
    }
}

