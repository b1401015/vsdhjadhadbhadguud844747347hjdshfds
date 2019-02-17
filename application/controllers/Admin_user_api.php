<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Admin_user_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('account_model');
        $this->load->model('account_info_model');
        $this->load->model('user_model');
        $this->load->model('admin_model');
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $res = RestSuccess('admin/user_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
    * function get user list
    * method: post
    * params: offset, limit
    */
    function get_user_list_post(){
        $keyword = !empty($this->post('search')['value']) ? $this->post('search')['value'] : '';
        $offset = !empty($this->post('start')) ? $this->post('start') : 0;
        $limit = !empty($this->post('length')) ? $this->post('length') : LIMIT_DEFAULT;
        $where = array(
            'account.is_delete' => false,
            'account.plain_name like' => '%'. skipVN($keyword, true) .'%',
        );
        $select = array(
            'account.*',
            'user.username',
            'user.email',
            'user.social_type',
            'user.last_login',
            'account_info.address',
            'account_info.phone',
            'account_info.gender',
            'account_info.birthday',
        );
        $account = $this->account_model->get_pagination($where, $select, $offset, $limit);
        $total_records = $this->account_model->count_total($where);
        $rs = [
            'status' => SUCCESS_CODE,
            'message' => OK_MSG,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => !empty($account) ? $account : [],
        ];
        $this->response($rs, SUCCESS_CODE);
    }

    /*
     * function add user
     * method: post
     * params: username, password, email, ...
     */
    function add_new_user_post()
    {

        /*user info*/
        $username = $this->post('username');
        $password = hash256($this->post('password'));
        $email = $this->post('email');
        $gender = !empty($this->post('gender')) ? strtolower($this->post('gender')) : GENDER_DEFAULT;
        $phone = $this->post('phone');
        $birthday = str_replace('/', '-', $this->post('birthday'));
        $birthday = !empty($this->post('birthday')) ? date('Y-m-d', strtotime($birthday)) : '';
        $last_name = $this->post('last_name');
        $first_name = $this->post('first_name');
        $fullname = $this->post('fullname');
        $address = $this->post('address');

        if(empty($email)){
            $this->response(RestBadRequest(EMAIL_IS_EMPTY_MSG), SUCCESS_CODE);
        }

        $check_verify_params = checkVerifyParams(array(
            $username,
            $password,
            $fullname,
        ));

        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        //check email exists
        $where = array(
            'email' => $email
        );
        $email_exists = $this->user_model->get_first_row($where);
        if(!empty($email_exists)){
            $this->response(RestBadRequest(EMAIL_IS_EXISTED_MSG), SUCCESS_CODE);
        }

        //check username exists
        $where = array(
            'username' => $username
        );
        $user_exists = $this->user_model->get_first_row($where);
        if(!empty($user_exists)){
            $this->response(RestBadRequest(USER_EXISTED_MSG), BAD_REQUEST_CODE);
        }

        //insert account tb
        $this->account_model->create(array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'fullname' => $fullname,
            'plain_name' => skipVN($fullname, true),
            'create_time_mi' => CURRENT_MILLISECONDS,
            'create_time' => CURRENT_TIME,
            'update_time' => CURRENT_TIME,
        ));

        $account_id = $this->db->insert_id();

        //insert account_info tb
        $this->account_info_model->create(array(
            'address' => $address,
            'phone' => $phone,
            'birthday' => !empty($birthday) ? date('Y-m-d', strtotime($birthday)) : '',
            'gender' => $gender,
            'account_id' => $account_id
        ));

        //insert user tb
        $this->user_model->create(array(
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'last_login' => CURRENT_TIME,
            'account_id' => $account_id,
        ));

        if(!empty($_FILES['img_src']['name']))
        {

            $upload_dir = FOLDER_IMG_UPLOAD .'/user/'. hash256($account_id). '/avatar';
            if (!is_dir($upload_dir)) {
                mkdir('./' . $upload_dir, 0777, TRUE);
            }

            //upload img_src
            $img_path = $_FILES['img_src']['name'];
            $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
            $img_name = cleanFileName(str_replace($img_path_ext, '', $img_path));
            $config['upload_path'] = './'. $upload_dir;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $file_name = strtolower(strtotime('now') .'_'. $img_name .'.'. $img_path_ext);
            $config['file_name'] = $file_name;

            //Load upload library and initialize configuration
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('img_src')) {
                //create crop_img_src
                $img_src = $upload_dir .'/'. $config['file_name'];
                $crop_img_src = $upload_dir .'/cr_'. $config['file_name'];
                resizeImage($img_src, null, '240', '300', false, $crop_img_src, false, false, 100);

                $this->account_model->update_by_condition(array(
                    '_id' => $account_id
                ), array(
                    'img_src' => $img_src,
                    'crop_img_src' => $crop_img_src,
                ));
            }
            else{
                //server err
                $this->response(RestServerError(), BAD_REQUEST_CODE);
            }
        } /* !empty($_FILES['img_src']['name'] */
        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    function edit_user_post()
    {

        /*user info*/
        $account_id = $this->post('account_id');
        $password = $this->post('password');
        $email = $this->post('email');
        $gender = !empty($this->post('gender')) ? strtolower($this->post('gender')) : GENDER_DEFAULT;
        $phone = $this->post('phone');
        $birthday = str_replace('/', '-', $this->post('birthday'));
        $birthday = !empty($this->post('birthday')) ? date('Y-m-d', strtotime($birthday)) : '';
        $last_name = $this->post('last_name');
        $first_name = $this->post('first_name');
        $fullname = $this->post('fullname');
        $address = $this->post('address');

        $check_verify_params = checkVerifyParams(array(
            $account_id,
        ));

        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        //get current user
        $where = array(
            'user.account_id' => $account_id
        );
        $current_user = $this->user_model->findOne($where, 'user.*');

        //check email exists
        $where = array(
            'email' => $email,
            'email <>' => $current_user->email,
        );
        $email_exists = $this->user_model->get_first_row($where);
        if(!empty($email_exists)){
            $this->response(RestBadRequest(EMAIL_IS_EXISTED_MSG), SUCCESS_CODE);
        }

        $user_exists = $this->user_model->get_first_row($where);
        if(!empty($user_exists)){
            $this->response(RestBadRequest(USER_EXISTED_MSG), BAD_REQUEST_CODE);
        }

        $where = array(
            'account.is_delete' => false
        );
        $account = $this->account_model->findOne($where, '*');
        if(empty($account)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }


        //update account tb
        $this->account_model->update_by_condition(array(
            '_id' => $account_id
        ), array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'fullname' => $fullname,
            'plain_name' => skipVN($fullname, true),
            'create_time_mi' => CURRENT_MILLISECONDS,
            'create_time' => CURRENT_TIME,
            'update_time' => CURRENT_TIME,
        ));


        //update account_info tb
        $data_update_user = array(
            'address' => $address,
            'phone' => $phone,
            'birthday' => !empty($birthday) ? date('Y-m-d', strtotime($birthday)) : '',
            'gender' => $gender,
        );

        $this->account_info_model->update_by_condition(array(
            '_id' => $account_id
        ), $data_update_user);

        //update user tb
        $data_update_user = array(
            'email' => $email,
            'last_login' => CURRENT_TIME,
        );

        if(!empty($password)){
            $data_update_user['password'] = hash256($password);
        }

        $this->user_model->update_by_condition(array(
            'account_id' => $account_id
        ), $data_update_user);

        $upload_dir = FOLDER_IMG_UPLOAD .'/user/'. hash256($account_id). '/avatar';
        if (!is_dir($upload_dir)) {
            mkdir('./' . $upload_dir, 0777, TRUE);
        }

        //upload img_src
        if(!empty($_FILES['img_src']['name']))
        {

            $upload_dir = FOLDER_IMG_UPLOAD .'/user/'. hash256($account_id). '/avatar';
            if (!is_dir($upload_dir)) {
                mkdir('./' . $upload_dir, 0777, TRUE);
            }

            //upload img_src
            $img_path = $_FILES['img_src']['name'];
            $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
            $img_name = cleanFileName(str_replace($img_path_ext, '', $img_path));
            $config['upload_path'] = './'. $upload_dir;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $file_name = strtolower(strtotime('now') .'_'. $img_name .'.'. $img_path_ext);
            $config['file_name'] = $file_name;

            //Load upload library and initialize configuration
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            //delete old original image file
            if(!empty($account->crop_img_src)) {
                if (file_exists(UPLOAD_PATH . $account->img_src)) {
                    unlink(UPLOAD_PATH . $account->img_src);
                }
            }

            //delete old crop image file
            if(!empty($account->crop_img_src)) {
                if (file_exists(UPLOAD_PATH . $account->crop_img_src)) {
                    unlink(UPLOAD_PATH . $account->crop_img_src);
                }
            }

            if ($this->upload->do_upload('img_src')) {
                //create crop_img_src
                $img_src = $upload_dir .'/'. $config['file_name'];
                $crop_img_src = $upload_dir .'/cr_'. $config['file_name'];
                resizeImage($img_src, null, '240', '300', false, $crop_img_src, false, false, 100);

                $this->account_model->update_by_condition(array(
                    '_id' => $account_id
                ), array(
                    'img_src' => $img_src,
                    'crop_img_src' => $crop_img_src,
                ));


            }
            else{
                //server err
                $this->response(RestServerError(), BAD_REQUEST_CODE);
            }
        } /* !empty($_FILES['img_src']['name'] */
        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    function delete_user_put()
    {
        $last = $this->uri->total_segments();
        $account_id = $this->uri->segment($last);
        $is_delete = !empty($this->put('is_delete')) ? 1 : 0;

        $check_verify_params = checkVerifyParams(array(
            $account_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $this->account_model->update_by_condition(array(
            '_id' => $account_id
        ), array(
            'is_delete' => $is_delete
        ));

        $where = array(
            'account._id' => $account_id
        );
        $select = array(
            'account.is_delete'
        );

        $account = $this->account_model->findOne($where, $select);

        $this->response(RestSuccess($account), SUCCESS_CODE);
    }

    function account_toggle_is_active_put(){
        $last = $this->uri->total_segments();
        $account_id = $this->uri->segment($last);
        $is_active = !empty($this->put('is_active')) ? 1 : 0;

        $check_verify_params = checkVerifyParams(array(
            $account_id,
        ));
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $this->account_model->update_by_condition(array(
            '_id' => $account_id
        ), array(
            'is_active' => $is_active
        ));

        $where = array(
            'account._id' => $account_id
        );
        $select = array(
            'account.is_active'
        );

        $account = $this->account_model->findOne($where, $select);

        $this->response(RestSuccess($account), SUCCESS_CODE);
    }

    function change_password_post()
    {
        $account_id = $this->session->userdata(SESSION_ACCOUNT_ID);

        $select = array(
            'admin._id',
            'admin.password'
        );
        $account = $this->admin_model->findOne(array(), $select);

        if(empty($account)){
            $this->response(RestNotFound(), NOT_FOUND_CODE);
        }

        $cur_password = hash256($this->post('old_pwd'));
        $password = hash256($this->post('new_pwd'));

        if($cur_password != $account->password)
        {
            $this->response(RestBadRequest(WRONG_PASSWORD_MSG), SUCCESS_CODE);
        }

        $this->admin_model->update_by_condition(array(
            '_id' => $account->_id,
        ),array(
                'password' => $password,
            )
        );
        $this->response(RestSuccess(), SUCCESS_CODE);
    }
}