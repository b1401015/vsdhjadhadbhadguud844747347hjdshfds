<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Notification extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('push_token_model');
        $this->load->model('notification_model');
        $this->load->model('account_get_notification_model');
//        $this->load->model('role_model');
//        $this->load->model('account_notification_setting_model');
    }

    //send push by time
    function send_push_by_time_get()
    {
        $res = array();
        $where = array(

        );
        $select = array(
            'push_token.account_id',
            'push_token.timezone'
        );
        $push_tokens = $this->push_token_model->find($where, $select);

        if(!empty($push_tokens) && count($push_tokens)){
            foreach($push_tokens as $push_token){
                $tz = $push_token->timezone;
                $tz_obj = new \DateTimeZone($tz);
                $today = new \DateTime("now", $tz_obj);
                $current_time = $today->format('H:i');
//                dump($current_time);

                $from_date = new \DateTime("now", $tz_obj);
                $from_date = $from_date->modify('-5 minutes');
                $from_time = $from_date->format('Y-m-d H:i:s');

                $to_date = new \DateTime("now", $tz_obj);
                $to_date = $to_date->modify('+5 minutes');
                $to_time = $to_date->format('Y-m-d H:i:s');

                $where = "
                    notification.is_done = 0
                    and notification.push_time <= '". CURRENT_TIME ."'
                ";
                $select = array(
                    'notification.*'
                );
                $notifications = $this->notification_model->find($where, $select);
//                dump(CURRENT_TIME);
//                dump($notifications);

                if(!empty($notifications) && count($notifications)) {
                    foreach ($notifications as $notification) {
                        $type = $notification->screen;
                        //list users not allow push
//                        if($type == EVENT_TYPE){
//                            $where = array(
//                                'account_notification_setting.is_notif_event' => false
//                            );
//                        }
//                        else if($type == BLOG_TYPE){
//                            $where = array(
//                                'account_notification_setting.is_notif_blog' => false
//                            );
//                        }
//
//                        $select = array(
//                            'account_notification_setting.account_id'
//                        );
//                        $account_notification_settings = $this->account_notification_setting_model->find($where, $select);
                        $unpush_user_list = '(0)';
//                        if(!empty($account_notification_settings) && count($account_notification_settings)) {
//                            $unpush_user_list = '(';
//                            $arr_ids = json_encode($account_notification_settings);
//                            $unpush_user_list .= str_replace(array('[', ']', '{', '}', '"account_id":', '"'), '', $arr_ids);
////            $unpush_user_list = explode(',', $arr_ids);
//                            $unpush_user_list .= ')';
//                        }

                        $where = "
                            push_token.device_version like '%" . FLATFORM_ANDROID . "%'
                            and push_token.account_id not in $unpush_user_list
                        ";
                        if(!empty($notification->account_id)) {
                            $where .= " and account._id = " . $notification->account_id;
                        }

                        $select = array(
                            'push_token.*',
                        );
                        $push_token_androids = $this->push_token_model->findByAccount($where, $select);
                        $where = "
                            push_token.device_version like '%". FLATFORM_IOS ."%'
                            and push_token.account_id not in $unpush_user_list
                        ";
                        if(!empty($notification->account_id)) {
                            $where .= " and account._id = " . $notification->account_id;
                        }
                        $push_token_ios = $this->push_token_model->findByAccount($where, $select);

//                        $this->sendPushToDevice($notification, $push_token_androids, $push_token_ios);
                        $this->sendPushToDevice_v2($notification, $push_token_androids, $push_token_ios);

                        $res[] = $notification->_id;
                    } //end foreach $notifications
                }

            } //end foreach $push_tokens
        }

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    function sendPushToDevice($notification, $push_token_androids, $push_token_ios){
        $screen = $notification->screen;
        $action_key = $notification->action_key;
        $title = $notification->_title;
        $content = $notification->_content;

        //begin send_push
        $where = array(
            'notification.is_delete' => false,
        );
        $total_notifications = $this->notification_model->count_total($where);
        $badge = !empty($total_notifications) ? $total_notifications : 1;
        $sound = trim("ap.mp3");

        if(!empty($push_token_androids)) {
            foreach ($push_token_androids as $push_token_android) {
                $this->account_get_notification_model->create(array(
                    'account_id' => $push_token_android->account_id,
                    'notification_id' => $notification->_id,
                    '_title' => $title,
                    '_content' => $content,
                ));
            }
        }

        if(!empty($push_token_ios)) {
            foreach ($push_token_ios as $push_token_io) {
                $this->account_get_notification_model->create(array(
                    'account_id' => $push_token_io->account_id,
                    'notification_id' => $notification->_id,
                    '_title' => $title,
                    '_content' => $content,
                ));
            }
        }

        $android_devices = [];
        $ios_devices = [];
        $account_ids = [];
        if (!empty($push_token_androids) && count($push_token_androids)) {
            foreach ($push_token_androids as $push_token_android) {
                $android_devices[] = $push_token_android->firebase_token;
                $account_ids[] = $push_token_android->account_id;
            }
        }

        if (!empty($push_token_ios) && count($push_token_ios)) {
            foreach ($push_token_ios as $push_token_io) {
                $ios_devices[] = $push_token_io->apple_token;
                $account_ids[] = $push_token_io->account_id;
            }
        }

        if (!empty($android_devices) && count($android_devices)) {

            $data = array(
                'nid' => $notification->_id,
                'click_action' => $action_key,
                'title' => $title,
                'body' => $content,
                'badge' => $badge,
                'sound' => $sound,
                //add extra data here
                //                        'notification_id' => $notification->id //notification_channel->id
                'screen' => $screen,
            );

            if (!empty($obj_id)) {
                $data['oid'] = $obj_id;
            }
            if (!empty($webview_url)) {
                $data['webview'] = !empty($webview_url) ? true : false;
            }

            $send_push = $this->sendPushNotificationFirebase($android_devices, $data, $notification);
//            $send_push = $this->sendPush($ios_devices, $notification->_id, $title, $content, $action_key, $data['badge']);
            if ($send_push) {
                //update is_push
                $data_update = array(
                    'is_push' => 1,
                    'create_time' => CURRENT_TIME,
                    'update_time' => CURRENT_TIME,
                    'create_time_mi' => CURRENT_MILLISECONDS,
                );
                $this->db->where_in('account_id', $account_ids);
                $this->db->update('account_get_notification', $data_update);

                //update is_done
                $this->notification_model->update_by_condition(array(
                    '_id' => $notification->_id,
                ), array(
                    'is_done' => 1,
                    'update_time' => CURRENT_TIME,
                ));
            }
        } /*end empty android_devices*/
        if (!empty($ios_devices) && count($ios_devices)) {
            $data = array(
                'nid' => $notification->_id,
                'click_action' => $action_key,
                'title' => $title,
                'body' => $content,
                'badge' => $badge,
                'sound' => $sound,
                //add extra data here
                'screen' => $screen,
            );
            if (!empty($notification)) {
                $data['nid'] = $notification->_id;
            }
            if (!empty($obj_id)) {
                $data['oid'] = $obj_id;
            }
            if (!empty($webview_url)) {
                $data['webview'] = !empty($webview_url) ? true : false;
            }

            $ios_msg = array(
                'body' => $notification->_content,
                'title' => $notification->_title,
                'vibrate' => 1,
                'sound' => 1,
            );

            $send_push = $this->sendPushNotificationFirebase($ios_devices, $data, $notification, $ios_msg);
//            $send_push = $this->sendPush($ios_devices, $notification->_id, $title, $content, $action_key, $data['badge']);
            if ($send_push) {
                //update is_push
                $data_update = array(
                    'is_push' => 1,
                    'create_time' => CURRENT_TIME,
                    'update_time' => CURRENT_TIME,
                    'create_time_mi' => CURRENT_MILLISECONDS,
                );
                $this->db->where_in('account_id', $account_ids);
                $this->db->update('account_get_notification', $data_update);

                //update is_done
                $this->notification_model->update_by_condition(array(
                    '_id' => $notification->_id,
                ), array(
                    'is_done' => 1,
                    'update_time' => CURRENT_TIME,
                ));
            }
        } /*end empty ios_devices*/
    }

    /* send push with badge of user*/
    function sendPushToDevice_v2($notification, $push_token_androids, $push_token_ios){
        $screen = $notification->screen;
        $action_key = $notification->action_key;
        $title = $notification->_title;
        $content = $notification->_content;

        //begin send_push
        $sound = DEFAULT_SOUND_NOTIFF;

        if(!empty($push_token_androids)) {
            foreach ($push_token_androids as $push_token_android) {
                $where = array(
                    'account_get_notification.account_id' => $push_token_android->account_id,
                    'account_get_notification.notification_id' => $notification->_id,
                );
                $check_agn = $this->account_get_notification_model->findOne($where);
                if(empty($check_agn)) {
                    $this->account_get_notification_model->create(array(
                        'account_id' => $push_token_android->account_id,
                        'notification_id' => $notification->_id,
                        '_title' => $title,
                        '_content' => $content,
                    ));
                }
            }
        }

        if(!empty($push_token_ios)) {
            foreach ($push_token_ios as $push_token_io) {
                $where = array(
                    'account_get_notification.account_id' => $push_token_io->account_id,
                    'account_get_notification.notification_id' => $notification->_id,
                );
                $check_agn = $this->account_get_notification_model->findOne($where);
                if(empty($check_agn)) {
                    $this->account_get_notification_model->create(array(
                        'account_id' => $push_token_io->account_id,
                        'notification_id' => $notification->_id,
                        '_title' => $title,
                        '_content' => $content,
                    ));
                }
            }
        }

        if (!empty($push_token_androids) && count($push_token_androids)) {

            $data = array(
                'nid' => $notification->_id,
                'click_action' => $action_key,
                'title' => $title,
                'body' => $content,
                'sound' => $sound,
                //add extra data here
                //                        'notification_id' => $notification->id //notification_channel->id
                'screen' => $screen,
            );

            if (!empty($obj_id)) {
                $data['oid'] = $obj_id;
            }
            if (!empty($webview_url)) {
                $data['webview'] = !empty($webview_url) ? true : false;
            }

            foreach ($push_token_androids as $push_token_android) {
                $where = array(
                    'account_get_notification.account_id' => $push_token_android->account_id,
                    'account_get_notification.is_push' => true,
                    'account_get_notification.is_delete' => false,
                    'account_get_notification.is_read' => false,
                );
                $total_notifications = $this->account_get_notification_model->count_total($where);
                $data['badge'] = !empty($total_notifications) ? $total_notifications + 1 : 1;

                $send_push = $this->sendPushNotificationFirebase($push_token_android->firebase_token, $data, $notification);
//                $send_push = $this->sendPush(array($push_token_android->firebase_token), $notification->_id, $title, $content, $action_key, $data['badge']);
                if ($send_push) {
                    //update is_push
                    $data_update = array(
                        'is_push' => 1,
                        'create_time' => CURRENT_TIME,
                        'update_time' => CURRENT_TIME,
                        'create_time_mi' => CURRENT_MILLISECONDS,
                    );
                    $this->db->where('account_id', $push_token_android->account_id);
                    $this->db->where('notification_id', $notification->_id);
                    $this->db->update('account_get_notification', $data_update);

                    //update is_done
                    $this->notification_model->update_by_condition(array(
                        '_id' => $notification->_id,
                    ), array(
                        'is_done' => 1,
                        'update_time' => CURRENT_TIME,
                    ));
                }
            }
        } /*end push token androids*/

        if (!empty($push_token_ios) && count($push_token_ios)) {

            $data = array(
                'nid' => $notification->_id,
                'click_action' => $action_key,
                'title' => $title,
                'body' => $content,
                'sound' => $sound,
                //add extra data here
                'screen' => $screen,
            );
            if (!empty($notification)) {
                $data['nid'] = $notification->_id;
            }
            if (!empty($obj_id)) {
                $data['oid'] = $obj_id;
            }
            if (!empty($webview_url)) {
                $data['webview'] = !empty($webview_url) ? true : false;
            }

            $ios_msg = array(
                'body' => $notification->_content,
                'title' => $notification->_title,
                'vibrate' => 1,
                'sound' => 1,
            );

            foreach ($push_token_ios as $push_token_io) {
                $where = array(
                    'account_get_notification.account_id' => $push_token_io->account_id,
                    'account_get_notification.is_push' => true,
                    'account_get_notification.is_delete' => false,
                    'account_get_notification.is_read' => false,
                );
                $total_notifications = $this->account_get_notification_model->count_total($where);
                $data['badge'] = !empty($total_notifications) ? $total_notifications + 1 : 1;

                $send_push = $this->sendPushNotificationFirebase($push_token_io->apple_token, $data, $notification, true);
//                $send_push = $this->sendPush(array($push_token_io->firebase_token), $notification->_id, $title, $content, $action_key, $data['badge']);
                if ($send_push) {
                    //update is_push
                    $data_update = array(
                        'is_push' => 1,
                        'create_time' => CURRENT_TIME,
                        'update_time' => CURRENT_TIME,
                        'create_time_mi' => CURRENT_MILLISECONDS,
                    );
                    $this->db->where('account_id', $push_token_io->account_id);
                    $this->db->where('notification_id', $notification->_id);
                    $this->db->update('account_get_notification', $data_update);

                    //update is_done
                    $this->notification_model->update_by_condition(array(
                        '_id' => $notification->_id,
                    ), array(
                        'is_done' => 1,
                        'update_time' => CURRENT_TIME,
                    ));
                }
            }
        } /*end push token ios*/
    }
}