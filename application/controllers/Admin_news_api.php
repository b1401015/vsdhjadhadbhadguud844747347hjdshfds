<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Admin_news_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('news_model');
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $res = RestSuccess('admin/news_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
     * function get news list
     * method: post
     * params: offset, limit
     */
    function get_news_list_post(){
        $keyword = !empty($this->post('search')['value']) ? $this->post('search')['value'] : '';
        $offset = !empty($this->post('start')) ? $this->post('start') : 0;
        $limit = !empty($this->post('length')) ? $this->post('length') : LIMIT_DEFAULT;
        $where = array(
            'news.is_delete' => false,
            'news.plain_title like' => '%'. skipVN($keyword, true) .'%',
        );
        $select = array(
            'news.*',
        );
        $news = $this->news_model->get_pagination($where, $select, $offset, $limit);
        $total_records = $this->news_model->count_total($where);
        $rs = [
            'status' => SUCCESS_CODE,
            'message' => OK_MSG,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => !empty($news) ? $news : [],
        ];
        $this->response($rs, SUCCESS_CODE);
    }

    /*
     * function add news
     * method: post
     * params: title, img_src, _description
     */
    function add_news_post()
    {

        //News info
        $title = $this->post('title');
        $description = $this->post('_description');
        $is_active = !empty($this->post('is_active')) ? 1 : 0 ;

        $check_verify_params = checkVerifyParams(array(
            $title,
            $_FILES['img_src']['name'],
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $upload_path = './public/upload/img/news';
        if(!file_exists($upload_path)){
            mkdir($upload_path);
        }

        //upload img_src
        $img_path = $_FILES['img_src']['name'];
        $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name'] = strtotime('now') .'.'. $img_path_ext;

        //Create upload path if isn't exist
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        //Load upload library and initialize configuration
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload('img_src')) {

            //insert news tb
            $this->news_model->create(array(
                'title' => $title,
                'plain_title' => skipVN($title, true),
                '_description' => $description,
                'plain_description' => skipVN(removeHTMLTagsAndSpecialChar($description), true),
                '_key' => generateKeyTranslation(),
                'img_src' => 'public/upload/img/news/'. $config['file_name'],
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

    function update_news_post()
    {

        //Product info
        $news_id = $this->post('news_id');
        $title = $this->post('title');
        $description = $this->post('_description');
        $is_active = !empty($this->post('is_active')) ? 1 : 0 ;

        $check_verify_params = checkVerifyParams(array(
            $news_id,
            $title,
        ));

        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $where = array (
            '_id' => $news_id,
            'is_delete' => false,
        );

        $news = $this->news_model->findOne($where, '*');
        if(empty($news)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }

        $data_upload = array(
            'title' => $title,
            'plain_title' => skipVN($title, true),
            '_description' => $description,
            'plain_description' => skipVN(removeHTMLTagsAndSpecialChar($description), true),
            'is_active' => $is_active,
            'update_time' => CURRENT_TIME,
        );

        //upload img_src
        if(!empty($_FILES['img_src']['name']))
        {
            $img_path = $_FILES['img_src']['name'];
            $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
            $config['upload_path'] = './public/upload/img/news';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = strtotime('now') .'.'. $img_path_ext;

            //Create upload path if isn't exist
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }

            //Load upload library and initialize configuration
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            //delete old file
            if(file_exists(UPLOAD_PATH. $news->img_src)) {
                unlink(UPLOAD_PATH . $news->img_src);
            }

            if ($this->upload->do_upload('img_src')) {
                $data_upload['img_src'] = 'public/upload/img/news/'. $config['file_name'];
            }
            else{
                $this->response(RestServerError(), SERVER_ERROR_CODE);
            }
        }


        //update account tb
        $this->news_model->update_by_condition(array(
                '_id' => $news_id
            )
            ,$data_upload);

        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    function delete_news_put()
    {
        $last = $this->uri->total_segments();
        $news_id = $this->uri->segment($last);
        $is_delete = !empty($this->put('is_delete')) ? 1 : 0;

        $check_verify_params = checkVerifyParams(array(
            $news_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $this->news_model->update_by_condition(array(
            '_id' => $news_id
        ), array(
            'is_delete' => $is_delete
        ));

        $where = array(
            '_id' => $news_id
        );
        $select = array(
            'news.is_delete'
        );

        $news = $this->news_model->findOne($where, $select);

        $this->response(RestSuccess($news), SUCCESS_CODE);
    }

    function news_toggle_is_active_put(){
        $last = $this->uri->total_segments();
        $news_id = $this->uri->segment($last);
        $is_active = !empty($this->put('is_active')) ? 1 : 0;

        $check_verify_params = checkVerifyParams(array(
            $news_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $this->news_model->update_by_condition(array(
            '_id' => $news_id
        ), array(
            'is_active' => $is_active
        ));

        $where = array(
            'news._id' => $news_id
        );
        $select = array(
            'news.is_active'
        );

        $news = $this->news_model->findOne($where, $select);

        $this->response(RestSuccess($news), SUCCESS_CODE);
    }
}

