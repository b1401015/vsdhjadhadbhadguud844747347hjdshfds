<?php defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH.'/libraries/REST_Controller.php');

Class Common_api extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('common_setting_model');
        $this->load->model('about_model');
    }

    /*
     * function get setting
     * method: get
     * params:
     */
    function get_setting_get(){
        $setting = $this->common_setting_model->get_first_row();
//        $res = [
//            'large_total_price' => $setting->large_total_price,
//            'small_total_price' => $setting->small_total_price,
//            'large_fee_price' => $setting->large_fee_price,
//            'small_fee_price' => $setting->small_fee_price,
//            'limit_time_delivery' => $setting->limit_time_delivery,
//            'limit_price_use_voucher' => $setting->limit_price_use_voucher,
//        ];
        $res = removeNullOfObject($setting);
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function get app info
     * method: get
     * params:
     */
    function get_app_info_get(){
        $about = $this->about_model->get_first_row();
        $res = [
            'hotline' => $about->hotline,
            'email' => $about->email,
            'address' => $about->address,
        ];
        $res = removeNullOfObject($res);
        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function upload tmp img
     * method: post
     * params: file, crop_file
     */
    function upload_tmp_img_post()
    {
        /*check session & jwt*/
        $account_id = $this->checkSessionAndTokenAuth();

        $check_verify_params = checkVerifyParams([
            $_FILES['img']['name'],
        ]);
        if(!empty($check_verify_params)){
            $this->response(RestBadRequest(MISMATCH_PARAMS_MSG), BAD_REQUEST_CODE);
        }

        $tmp_folder = TMP_USER_FOLDER .'/'. hash256($account_id). '/img';
        if (!is_dir($tmp_folder)) {
            mkdir('./' . $tmp_folder, 0777, TRUE);
        }

//        dump($tmp_folder);

        if(!empty($_FILES['crop_img']['name'])) {
            //upload crop_img_src
            $img_path = $_FILES['crop_img']['name'];
            $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
            $img_name = cleanFileName(str_replace($img_path_ext, '', $img_path));

            $config['upload_path'] = './' . $tmp_folder;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $file_name = strtolower('cr_' . strtotime('now') . '_' . $img_name . '.' . $img_path_ext);
            $config['file_name'] = $file_name;

            //Load upload library and initialize configuration
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            $res = array();
            if ($this->upload->do_upload('crop_img')) {
                $res['crop_img_src'] = $tmp_folder . '/' . $config['file_name'];
            } else {
                $this->response(RestServerError(), SERVER_ERROR_CODE);
            }
        }

        //upload img_src
        $img_path = $_FILES['img']['name'];
        $img_path_ext = pathinfo($img_path, PATHINFO_EXTENSION);
        $img_name = cleanFileName(str_replace($img_path_ext, '', $img_path));
        $config['upload_path'] = './'. $tmp_folder;
        $config['allowed_types'] = 'jpg|jpeg|png';
        $file_name = strtolower(strtotime('now') .'_'. $img_name .'.'. $img_path_ext);
        $config['file_name'] = $file_name;

        //Load upload library and initialize configuration
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload('img')) {
            $res['img_src'] = $tmp_folder .'/'. $config['file_name'];
        }
        else{
            $this->response(RestServerError(), SERVER_ERROR_CODE);
        }

        $this->response(RestSuccess($res), SUCCESS_CODE);
    }

    /*
     * function upload tmp img
     * method: post
     * params: file, crop_file
     */
    function report_log_post()
    {
        $message = $this->post('message');
        $this->writeErrorLog(date('Ymd'), $message);

        $this->response(RestSuccess(), SUCCESS_CODE);
    }
}