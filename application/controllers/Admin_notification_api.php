<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Admin_notification_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('push_token_model');
        $this->load->model('notification_model');
        $this->load->model('account_get_notification_model');
//        $this->load->model('role_model');
    }

    /*
     * function index
     * method:
     * params:
     */
    public function index_get()
    {
        $res = RestSuccess('admin/notification_api');
        $this->response($res, SUCCESS_CODE);
    }

    /*
    * function get notification list
    * method: post
    * params: offset, limit
    */
    function get_notification_list_post(){
        $keyword = !empty($this->post('search')['value']) ? $this->post('search')['value'] : '';
        $offset = !empty($this->post('start')) ? $this->post('start') : 0;
        $limit = !empty($this->post('length')) ? $this->post('length') : LIMIT_DEFAULT;
        $where = "
            notification.is_delete = 0
            and (
                notification._title like '%". $keyword ."%'
                or notification._content like '%". $keyword ."%'
            )
        ";
        $select = array(
            'notification.*',
            'account.fullname',
        );
        $notifications = $this->notification_model->get_pagination($where, $select, $offset, $limit);
        $total_notifications = $this->notification_model->count_total($where);
        $rs = array(
            'status' => SUCCESS_CODE,
            'message' => OK_MSG,
            'recordsTotal' => $total_notifications,
            'recordsFiltered' => $total_notifications,
            'data' => !empty($notifications) ? $notifications : array(),
        );
        $this->response($rs, SUCCESS_CODE);
    }

    /*
     * function add record
     * method: post
     * params: code, barcode, title, ...
     */
    function send_push_post()
    {
        $is_save_notification = true;
        $is_created_notification = false;
        $count_send_push = 0;

        $is_push_time = 0;
        $push_time = $this->post('time');
        $title = $this->post('title');
        $content = $this->post('content');
        $type = strtoupper($this->post('type'));
        $account_id = $this->post('account_id');

        $params = array(
            $title,
            $content,
            $type
        );

        if(!empty($push_time)){
            if(strlen($push_time) < 19) {
                $push_time .= ':00';
            }
            if(strtotime($push_time) <= time()){
                $this->response(RestBadRequest(TIME_GREATER_THAN_CURRENT_TIME_MSG), SUCCESS_CODE);
            }
            $is_push_time = 1;
        }

        $check_verify_params = checkVerifyParams($params);
        if(!empty($check_verify_params) || empty($account_id) || !count($account_id)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), SUCCESS_CODE);
        }

        $screen = $type;
        $action_key = PUSH_ACTION_KEY .'_'. $type;
        $is_send_all = true;
        $notif_account_id = null;

        //list users not allow push
//        if($type == EVENT_TYPE){
//            $where = array(
//                'user_notification_setting.is_notif_event' => false
//            );
//        }
//        else if($type == BLOG_TYPE){
//            $where = array(
//                'user_notification_setting.is_notif_blog' => false
//            );
//        }
//
//        $select = array(
//            'user_notification_setting.account_id'
//        );
//        $user_notification_settings = $this->user_notification_setting_model->find($where, $select);
        $unpush_user_list = '(0)';
//        if(!empty($user_notification_settings) && count($user_notification_settings)) {
//            $unpush_user_list = '(';
//            $arr_ids = json_encode($user_notification_settings);
//            $unpush_user_list .= str_replace(array('[', ']', '{', '}', '"account_id":', '"'), '', $arr_ids);
////            $unpush_user_list = explode(',', $arr_ids);
//            $unpush_user_list .= ')';
//        }


//        dump($unpush_user_list);

        // all countries
        if (in_array(VALUE_ALL, $account_id) || count($account_id) == 0) {
            $where = array(
                'account.is_delete' => false,
            );
            $select = array(
                'account._id',
            );
            $accounts = $this->account_model->find($where, $select);
            $loop_accounts = 1;
            foreach ($accounts as $account) {
                $where = "
                    push_token.account_id = ". $account->_id ."
                    and push_token.device_version like '%". FLATFORM_ANDROID ."%'
                    and push_token.account_id not in $unpush_user_list
                ";
                $select = array(
                    'push_token.account_id',
                    'push_token.firebase_token',
                );
                $push_token_androids = $this->push_token_model->findByAccount($where, $select);
                $where = "
                    push_token.account_id = ". $account->_id ."
                    and push_token.device_version like '%". FLATFORM_IOS ."%'
                    and push_token.account_id not in $unpush_user_list
                ";
                $select = array(
                    'push_token.account_id',
                    'push_token.apple_token',
                );
                $push_token_ios = $this->push_token_model->findByAccount($where, $select);

                if((!empty($push_token_androids) && count($push_token_androids)) || (!empty($push_token_ios) && count($push_token_ios))){
                    $count_send_push++;
                }

                if($loop_accounts == 1) {
                    $notification_id = $this->pushToDevices($is_push_time, $push_time, $title, $content, $screen, $action_key, $is_send_all, $notif_account_id, $push_token_androids, $push_token_ios);

                    if(!empty($notification_id)){
                        $loop_accounts = 2;
                    }
                }
                else{
                    $this->pushToDevices($is_push_time, $push_time, $title, $content, $screen, $action_key, $is_send_all, $notif_account_id, $push_token_androids, $push_token_ios, false, $notification_id);
                }
            }
        } else {
            foreach ($account_id as $id) {

                $is_send_all = false;
                $notif_account_id = $id;

                $where = "
                    push_token.account_id = ". $id ."
                    and push_token.device_version like '%". FLATFORM_ANDROID ."%'
                    and push_token.account_id not in $unpush_user_list
                ";
                $select = array(
                    'push_token.*',
                );
                $push_token_androids = $this->push_token_model->findByAccount($where, $select);
                $where = "
                    push_token.account_id = ". $id ."
                    and push_token.device_version like '%". FLATFORM_IOS ."%'
                    and push_token.account_id not in $unpush_user_list
                ";
                $push_token_ios = $this->push_token_model->findByAccount($where, $select);

                if((!empty($push_token_androids) && count($push_token_androids)) || (!empty($push_token_ios) && count($push_token_ios))){
                    $count_send_push++;
                }

                $this->pushToDevices($is_push_time, $push_time, $title, $content, $screen, $action_key, $is_send_all, $notif_account_id, $push_token_androids, $push_token_ios);
            }
        } //end country_id

        if($count_send_push == 0){
            $this->response(RestBadRequest(EMPTY_DEVICE_TO_PUSH_MSG), SUCCESS_CODE);
        }

        $this->response(RestSuccess(), SUCCESS_CODE);
    }

    public function update_notification_post(){
        $is_push_time = 1;
        $push_time = $this->post('time');
        $title = $this->post('title');
        $content = $this->post('content');
        $type = strtoupper($this->post('type'));
        $notification_id = $this->post('notification_id');

        $params = array(
            $title,
            $content,
            $type,
            $push_time,
            $notification_id,
        );

        $check_verify_params = checkVerifyParams($params);
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), SUCCESS_CODE);
        }

        if(strlen($push_time) < 19) {
            $push_time .= ':00';
        }
        if(strtotime($push_time) <= time()){
            $this->response(RestBadRequest(TIME_GREATER_THAN_CURRENT_TIME_MSG), SUCCESS_CODE);
        }

        $screen = $type;
        $action_key = PUSH_ACTION_KEY .'_'. $type;

        $this->notification_model->update_by_condition(array(
            '_id' => $notification_id
        ), array(
            '_title' => $title,
            '_content' => $content,
            'screen' => $screen,
            'action_key' => $action_key,
            'push_time' => $push_time,
            'update_time' => CURRENT_TIME,
        ));

        $this->response(RestSuccess(), SUCCESS_CODE);

    }
}

