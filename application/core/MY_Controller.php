<?php
/*
 * Parent controller
 */
date_default_timezone_set('Asia/Ho_Chi_Minh');

Class MY_Controller extends CI_Controller
{
    //send data to view
    public $data = array();

    function __construct()
    {
        //inherit from IC_Controller
        parent::__construct();
        $controller = $this->uri->segment(1);
        //check permission to access files/API (logined or not)
        switch ($controller){
            case USER_MANAGER_CONTROLLER_NAME:
            case ADMIN_CONTROLLER_NAME:
            {
                //someone using Admin pages
                $this->load->model('admin_model');
                $admin_id = $this->get_login_user_id();
                if (empty($admin_id) && $this->uri->segment(2) != 'login' && $this->uri->segment(2) != 'read_new_captcha'){
                    redirect(base_url('_admin/login'));
                } else {
                    $data_admin = $this->admin_model->read_row(array('_id' => $admin_id));
                    if (!$data_admin){
                        //failed to search Admin account, kick out
//                        $this->redirect_admin_login();
                    } else {
                        //found 1 account
                        //continue to process
                    }
                }
                break;
            }

            case API_CONTROLLER_NAME:
            {
                //someone accesses API
                if (empty($this->get_login_user_id())){
                    //not logined yet
                    return FALSE;
                }

                break;
            }
            default:
            {
                //
            }
        }

        //models
        $this->load->model('user_model');
        $this->load->model('account_model');
        $this->load->model('account_info_model');
        $this->load->model('account_jwt_model');
        $this->load->model('push_token_model');
        $this->load->model('account_language_setting_model');
        $this->load->model('notification_model');
        $this->load->model('account_get_notification_model');

        $this->load->helper('url');
        $this->load->helper('file');
    }
    //force user back to admin login page
    protected function redirect_admin_login(){
        $func = $this->uri->segment(2);
        if ($func != 'login'){
            redirect(base_url(ADMIN_CONTROLLER_NAME.'/login'));
        }
    }
    //get logined user id from session
    protected function get_login_user_id(){
        return $this->session->userdata(SESS_KEY_USER_ID);
    }

    //get logined user id from session
    protected function set_login_user_id($user_id){
        $this->session->set_userdata(SESS_KEY_USER_ID, $user_id);
    }

    protected function get_captcha(){
        return $this->session->userdata(SESS_KEY_CAPTCHA);
    }

    protected function set_captcha($str_captcha){
        $this->session->set_userdata(SESS_KEY_CAPTCHA, $str_captcha);
    }
    //
    /**
     * convert array to JSON format
     * @param unknown $array
     */
    protected function responseJsonData($array){
        $this->output->set_header('Content-Type: application/json; charset=utf-8');
        return json_encode($array);
    }
    /**
     * convert array to Query string, each term joins by "&"
     */
    protected function convertArray2QueryString($fields){
        $fields_string = '';
        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        $fields_string = rtrim($fields_string,'&');
        return $fields_string;
    }
    /**
     * generate new Captcha image
     * return: HTML of <img/>
     */
    protected function generateCaptchaImageTag(){
        if (!is_dir('captcha')) {
            mkdir('./captcha', 0777, TRUE);
        }
        $this->load->helper('captcha');
        $random = trim(rand(1001, 9999));     //random number is in this range
        $vals = array(
            'word'	=> $random,		//random number
            'img_path' 	=> './'.CAPTCHA_FOLDER,
            'img_url' 	=> base_url() . CAPTCHA_FOLDER,
            'img_width'	=> CAPTCHA_W,
            'img_height' 	=> CAPTCHA_H,
            'expiration' 	=> CAPTCHA_EXP_DURATION,
            'font_size' 	=> CAPTCHA_FONT_SIZE,
            'font_path' => FCPATH. 'captcha/font/OpenSans-Bold.ttf',
        );

        $cap = create_captcha($vals);
        //save into database
//        $data = array(
//            'captcha_time'	=> $cap['time'],
//            'ip_address'	=> $this->input->ip_address(),
//            'word'	=> $cap['word']
//        );
//
//        $query = $this->db->insert_string('captcha', $data);
//        $this->db->query($query);

        //generate captcha session
        $session_userdata = [
            'CAPTCHA_EXPIRE_TIME' => $cap['time'],
            'CAPTCHA_CODE' => $cap['word']
        ];
        $this->session->set_userdata($session_userdata);

        return $cap['image'];
    }
    /**
     * validate captcha word
     * @param unknown $word
     * @param unknown $ip
     */
    protected function isValidCaptcha($word, $ip = ''){
        // First, delete old captchas

        $expiration = time() - CAPTCHA_EXP_DURATION;
        $this->db->query("DELETE FROM captcha WHERE captcha_time < ".$expiration);

        if(!empty($ip)) {
            // Then see if a captcha exists:
            $sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
            $binds = array($word, $ip, $expiration);
        }
        else{ // for API
            // Then see if a captcha exists:
            $sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND captcha_time > ?";
            $binds = array($word, $expiration);
        }
        $query = $this->db->query($sql, $binds);
        $row = $query->row();

        return ($row->count > 0);
    }

    /**
     * validate captcha word
     * @param unknown $word
     * @param unknown $ip
     */
    protected function isValidCaptchaSession($word){
        // First, delete old captchas

        $expiration = time() - CAPTCHA_EXP_DURATION;
        $captcha_expired = $this->session->userdata['CAPTCHA_EXPIRE_TIME'];
        $captcha_code = $this->session->userdata['CAPTCHA_CODE'];

        if($captcha_code == $word && $captcha_expired > $expiration){
            return 1;
        }

        return 0;
    }

    /**
     * sending email
     * @param unknown $from
     * @param unknown $from_title
     * @param unknown $to
     * @param unknown $cc
     * @param unknown $bcc
     * @param unknown $subject
     * @param unknown $message
     * @param unknown $attach
     * @param unknown $reply_to
     */
    protected function sendMail_wrapper($from, $from_title, $to, $cc, $bcc, $subject, $message, $attach, $reply_to = null){
        $this->load->library('email');

        $this->email->from($from, $from_title);
        $this->email->to($to);
        if (isset($cc) && $cc != '')
            $this->email->cc($cc);
        if (isset($bcc) && $bcc != '')
            $this->email->bcc($bcc);
        if (isset($reply_to))
            $this->email->reply_to($reply_to, 'Reply To');
        else
            $this->email->reply_to(INFO_MAIL, 'Admin');

        $this->email->subject($subject);

        //html format + hidden fake timestamp (make each email unique, prevent spam filter engine)
        $message = '<html><head></head><body>'.$message.'<br/><br/><div style="display:none;">'.date('Y-m-d H:i:s').rand(0,10000).'</div></body></html>';
        $this->email->message($message);

        if (isset($attach) && $attach != '')
            $this->email->attach($attach);

        $this->email->send();
//     							echo $this->email->print_debugger();
    }

    /**
     * generate new Captcha image
     * return: HTML of <img/>
     */
    protected function generateCaptchaBase64(){
        $this->load->helper('captcha');
        $random = trim(rand(1001, 9999));     //random number is in this range
        $vals = array(
            'word'	=> $random,		//random number
            'img_path' 	=> './'.CAPTCHA_FOLDER,
            'img_url' 	=> base_url() . CAPTCHA_FOLDER,
            'img_width'	=> CAPTCHA_W,
            'img_height' 	=> CAPTCHA_H,
            'expiration' 	=> CAPTCHA_EXP_DURATION,
            'font_size' 	=> CAPTCHA_FONT_SIZE,
            'font_path' => FCPATH. 'captcha/font/OpenSans-Bold.ttf',
        );

        $cap = create_captcha($vals);
        //save into database
        $data = array(
            'captcha_time'	=> $cap['time'],
            'ip_address'	=> $this->input->ip_address(),
            'word'	=> $cap['word']
        );

        //store session
        $session_userdata = [
            'CAPTCHA_EXPIRE_TIME' => $cap['time'],
            'CAPTCHA_CODE' => $cap['word']
        ];
        $this->session->set_userdata($session_userdata);

        $query = $this->db->insert_string('captcha', $data);
        $this->db->query($query);

//        dump($vals['img_url'].$cap['filename']);

        $image = imagecreatefromjpeg($vals['img_url'].$cap['filename']);

// Add some filters
        imagefilter($image, IMG_FILTER_PIXELATE, 1, true);
        imagefilter($image, IMG_FILTER_MEAN_REMOVAL);

        ob_start(); // Let's start output buffering.
        imagejpeg($image); //This will normally output the image, but because of ob_start(), it won't.
        $contents = ob_get_contents(); //Instead, output above is saved to $contents
        ob_end_clean(); //End the output buffer.

        return base64_encode($contents);
    }

    /**
     * send a post request
     */
    protected function sendPost($fields, $url){
        $fields_string = $this->convertArray2QueryString($fields);
        //header with content_type api key
        //create SID
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //comment out belows options if request by GET method
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
//        curl_setopt($ch, CURLOPT_HEADER, 1);	//to get 501 code
        $result = curl_exec($ch);	//full result
        if ($result === FALSE) {
            die('Send Error: ' . curl_error($ch));
        }
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);

        $info = curl_getinfo($ch);		//get header info

        curl_close($ch);

        if ($info["http_code"] != 200){		//invalid request, must hide information from Cloudbric
            //parse header
            $header_arr = $this->get_headers_from_curl_response($header);

            if (isset($header_arr[0]) && isset($header_arr[0]['Cb-Error'])){
                return array(
                    'header' => 'Cb-Error:'.$header_arr[0]['Cb-Error'],
                    'body'	=> $info
                );
            } else {
                return array(
                    'header' => 'Cb-Error:'.UNKNOWN_ERROR,
                    'body'	=> $header
                );
            }
        } else {	//request ok
            return array(
                'body'	=> json_decode($body)	//real json data, if any
            );
        }
    }

    /**
     *
     * @param unknown $headerContent
     * @return Ambigous <multitype:, unknown>
     */
    private function get_headers_from_curl_response($headerContent){
        $headers = array();

        // Split the string on every "double" new line.
        $arrRequests = explode("\r\n\r\n", $headerContent);

        // Loop of response headers. The "count() -1" is to
        //avoid an empty row for the extra line break before the body of the response.
        for ($index = 0; $index < count($arrRequests) -1; $index++) {

            foreach (explode("\r\n", $arrRequests[$index]) as $i => $line)
            {
                if ($i === 0)
                    $headers[$index]['http_code'] = $line;
                else
                {
                    list ($key, $value) = explode(': ', $line);
                    $headers[$index][$key] = $value;
                }
            }
        }

        return $headers;
    }

    /*
     * function upsert tb push_token
     * params: data
     */
    protected function upsertPushToken($data)
    {
        $account_id = $data['account_id'];
        $device_id = $data['device_id'];
        $device_name = $data['device_name'];
        $device_version = $data['device_version'];
        $firebase_token = $data['firebase_token'];
        $apple_token = $data['apple_token'];
        $timezone = !empty($data['timezone']) ? $data['timezone'] : DEFAULT_TIMEZONE ;

        if (empty($device_id) || (empty($firebase_token) && empty($apple_token))) {
            return;
        }

        $push_token_exists = $this->push_token_model->findOne(['device_id' => $device_id]);

        if (!empty($push_token_exists)) { //update
            $this->push_token_model->update_by_condition([
                '_id' => $push_token_exists->_id
            ], [
                'account_id' => $account_id,
                'device_name' => $device_name,
                'device_version' => $device_version,
                'firebase_token' => $firebase_token,
                'apple_token' => $apple_token,
                'timezone' => $timezone,
                'update_time' => CURRENT_TIME,
            ]);
        } else { //insert new
            $this->push_token_model->create([
                'account_id' => $account_id,
                'device_id' => $device_id,
                'device_name' => $device_name,
                'device_version' => $device_version,
                'firebase_token' => $firebase_token,
                'apple_token' => $apple_token,
                'timezone' => $timezone,
                'create_time' => CURRENT_TIME,
                'update_time' => CURRENT_TIME,
            ]);
        }
    }

    /*
     * return errors when import excel file
     */
    protected function returnErrorImport($data, $errList, $filename, $extention)
    {
        $this->load->library('excel');
        $extention_ = scan_file_type($extention);
        $excel_factor = PHPExcel_IOFactory::createReader($extention_);
        $objPHPExcel = $excel_factor->load($filename);
//        $objPHPExcel->setLoadAllSheets();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('P2', 'Error')->getColumnDimension('P')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle('P2')->getFill()->applyFromArray(color_error());
        $objPHPExcel->getActiveSheet()->getStyle('P2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('P2')->applyFromArray(arr_all_border());

        foreach ($errList as $key => $row) {
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $key, $row)->getColumnDimension('P')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getStyle('P' . $key)->getFont()->getColor()->setRGB('6F6F6F');;
            $objPHPExcel->getActiveSheet()->getStyle('P' . $key)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('P' . $key)->applyFromArray(arr_all_border());
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $extention_);
        if(!file_exists('export/excel')){
            mkdir('./export/excel', 0777, TRUE);
        }
        $objWriter->save('export/excel/import_error_' . date('Ymd') . '.xls');
//        header("location: " . base_url() . "export/excel/import_error_" . date('Ymd') . ".xls");
//        unlink(UPLOAD_PATH . 'export/excel/import_error_' . date('Ymd') . '.xls');
    }

    protected function checkVerifyJWT($jwt, $account_id){
        /* use for CI */
        if (!(strpos($jwt, 'Bearer') !== false)) {
            return false;
        }
        /* end use for CI */
        $jwt = str_replace('Bearer ', '', $jwt);
        //check token
        $where = [
            'jwt' => $jwt,
            'expire_time >' => time(),
        ];
        $account_jwt = $this->account_jwt_model->findOne($where);
        if(empty($account_jwt)){
            return false;
        }

        $jwt_generator = new JWT();
        $jwt_decode = $jwt_generator::decode($jwt, SERVER_KEY);
        if($jwt_decode->account_id != $account_id){
            return false;
        }

        return true;
    }

    /*
     * send email
     */
    protected function sendEmail($from_email, $to_email, $subject, $message)
    {
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'hoidapsuckhoeonline.com',
            'smtp_port' => 25,
            'smtp_user' => 'praywish@hoidapsuckhoeonline.com',
            'smtp_pass' => 'duS^w110',
            'mailtype' => 'html',
            'newline' => "\r\n",
//            'charset' => 'iso-8859-1',
            'charset' => 'utf-8',
            'validation' => TRUE,
            'wordwrap' => TRUE
        );


        $this->load->library('email', $config);
//        $this->email->set_newline("\r\n");
        $this->email->from($from_email);
        $this->email->to($to_email);
//        $this->email->bcc(BCC_EMAIL);
        $this->email->subject($subject);
//        $this->email->attach($attach_file);
        $this->email->message($message);
        if($this->email->send())
        {
//            echo 'Email send.';
        }
        else
        {
            show_error($this->email->print_debugger());
        }

    }

    /**
     * validate captcha code for API
     * @param unknown $word
     * @param unknown $ip
     */
    protected function checkCaptcha($code){
        $captcha_expire_time = $this->session->userdata('CAPTCHA_EXPIRE_TIME');
        $captcha_code = $this->session->userdata('CAPTCHA_CODE');
        $expiration = time() - CAPTCHA_EXP_DURATION;

        if($code == $captcha_code && $captcha_expire_time > $expiration){
            return true;
        }
        else{
            return false;
        }
    }

    /*
     * write log file
     */
    protected function writeLog($url, $txt){
        if (!is_dir(LOG_FOLDER)) { //create log folder
            mkdir('./' . LOG_FOLDER, 0777, TRUE);
        }

        $log_txt = "======================\r\n";
        $log_txt .= "======================\r\n";
        $log_txt .= 'Time: '. date('Y-m-d H:i:s') ." \r\n";
        $log_txt .= 'URL: '. $url ." \r\n";
        $log_txt .= $txt;
        $log_txt .= "\r\n\r\n\r\n";

        $log_file = LOG_FOLDER .'/'. date('Ymd') .'_log_'. $_SERVER['SERVER_NAME'] .'.txt';
        $log_file_path = UPLOAD_PATH. $log_file;

        if(file_exists($log_file_path))
        {
            write_file($log_file_path, $log_txt, 'a');
        }
        else
        {
            write_file($log_file_path, $log_txt);
        }
    }

    protected function checkSessionAndTokenAuth(){
        /*check session & jwt*/
        $account_id = $this->session->userdata(SESSION_ACCOUNT_ID);
        if (empty($account_id)) {
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        $jwt = !empty($this->input->request_headers()[HEADER_PARAM_AUTHORIZATION]) ? $this->input->request_headers()[HEADER_PARAM_AUTHORIZATION] : '';
        if (!$this->checkVerifyJWT($jwt, $account_id)) {
            $this->response(RestForbidden(INVALID_TOKEN_MSG."---".$jwt), FORBIDDEN_CODE);
        }
        return $account_id;
        /*end check session & jwt*/

    }

    protected function checkSessionAuth(){
        /*check session*/
        $account_id = $this->session->userdata(SESSION_ACCOUNT_ID);
        if(empty($account_id)){
            $this->response(RestForbidden(NOT_LOGIN_MSG), FORBIDDEN_CODE);
        }
        return $account_id;
        /*end check session*/
    }


    protected function checkApplicationTokenAuth()
    {
        /*check jwt*/
        $jwt = !empty($this->input->request_headers()[HEADER_PARAM_AUTHORIZATION]) ? $this->input->request_headers()[HEADER_PARAM_AUTHORIZATION] : '';
        $check_jwt = $this->checkJWT($jwt);
//        dump($check_jwt);

        if(empty($check_jwt)){
            $this->response(RestForbidden(INVALID_TOKEN_MSG."---".$jwt), FORBIDDEN_CODE);
        }
        return $check_jwt->account_id;
        /*end check jwt*/
    }

    /**
     * check jwt
     */
    protected function checkJWT($jwt){
        /* use for CI */
        if (!(strpos($jwt, 'Bearer') !== false)) {
            return false;
        }
        /* end use for CI */
        $jwt = str_replace('Bearer ', '', $jwt);
        //check token
        $where = [
            'jwt' => $jwt
        ];
        $account_jwt = $this->account_jwt_model->findOne($where);
        if(empty($account_jwt)){
            return false;
        }


        if($account_jwt->expire_time < time()){ //expire_time
            return false;
        }

        $jwt_generator = new JWT();
        $jwt_decode = $jwt_generator::decode($jwt, SERVER_KEY);

        return $jwt_decode;
    }

    /**
     * send Push notification wrapper
     */
    protected function sendPushNotificationFirebase($device, $data, $notification, $is_ios = false)
    {
//        dump($user_account_id);
//        dump($device);
//        dump($data, false);
//        dump($notification);
        $server_key = FIREBASE_SERVER_KEY;

        //FCM api URL
        $url = 'https://fcm.googleapis.com/fcm/send';
        //api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
        $fields = array(    //package to send from Firebase
            'priority' => 'high',
            'delay_while_idle' => true
        );
        if (isset($data)) {
            $fields['data'] = $data;
        }

        if ($is_ios) {
            $fields['notification'] = $data;
        }

//        if (isset($ios_msg)){		//iOS case
////            $fields['notification'] = null;
//            $fields['notification'] = $ios_msg;
//        }
        //send to which device(s)
        if (is_array($device)) {
            $device = array_unique($device);
            $fields['registration_ids'] = $device;
        } else {        //single device
            $fields['to'] = $device;
        }
//        dump($fields);

        //header with content_type api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $server_key
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
//        dump($fields, false);
//        dump($result, false);
        if ($result === FALSE) {
//            dump('FCM Send Error: ' . curl_error($ch));
            self::writeLog('FCM Send Error', json_encode((array)curl_error($ch)));
            return false;
        }

        curl_close($ch);
//        dump($result);
        return true;

    }

    /*
    * get user to push
    */
    protected function getUserToPushNotif($account_id, $title, $content, $screen = USER_SCREEN, $action_key = AK_USER_GET_COMMISSION){
        $is_push_time = 0;
        $push_time = '';
        $is_send_all = false;
        $notif_account_id = $account_id;

        $where = "
                    push_token.account_id = ". $account_id ."
                    and push_token.device_version like '%". FLATFORM_ANDROID ."%'
                ";
        $select = array(
            'push_token.*',
        );
        $push_token_androids = $this->push_token_model->findByAccount($where, $select);
        $where = "
                    push_token.account_id = ". $account_id ."
                    and push_token.device_version like '%". FLATFORM_IOS ."%'
                ";
        $push_token_ios = $this->push_token_model->findByAccount($where, $select);

//        if((!empty($push_token_androids) && count($push_token_androids)) || (!empty($push_token_androids) && count($push_token_ios))){
//            return;
//        }

        $this->pushToDevices($is_push_time, $push_time, $title, $content, $screen, $action_key, $is_send_all, $notif_account_id, $push_token_androids, $push_token_ios);
    }

    /*
    * send push with badge of user
    */
    protected function pushToDevices($is_push_time, $push_time, $title, $content, $screen, $action_key, $is_send_all, $notif_account_id, $push_token_androids, $push_token_ios, $is_create_notification = true, $notification_id =  ''){
        $sound = DEFAULT_SOUND_NOTIFF;

        if(!empty($push_token_androids) || !empty($push_token_ios)){
            if($is_create_notification) {
                $this->notification_model->create(array(
                    '_title' => $title,
                    '_content' => $content,
                    'screen' => $screen,
                    'action_key' => $action_key,
                    'is_send_all' => $is_send_all,
                    'push_time' => $push_time,
                    'account_id' => $notif_account_id,
                    'create_time' => CURRENT_TIME,
                    'update_time' => CURRENT_TIME,
                    'create_time_mi' => CURRENT_MILLISECONDS,
                ));

                $where = array(
                    '_id' => $this->db->insert_id(),
                );
            }
            else{
                $where = array(
                    '_id' => $notification_id,
                );
            }

            $notification = $this->notification_model->findOne($where);

            if(!$is_push_time) {
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
                        if ($send_push) {
                            //update is_push
                            $data_update = array(
                                'is_push' => 1,
                                'create_time' => CURRENT_TIME,
                                'update_time' => CURRENT_TIME,
                                'create_time_mi' => CURRENT_MILLISECONDS,
                            );
                            $this->db->where_in('account_id', $push_token_io->account_id);
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
            } // end $is_push_time

            if(!empty($notification) && $is_send_all){
                return $notification->_id;
            }

        } //end empty device

    }

    /*
     * resonse user-account datas
    */
    protected function responseUserAccountDatas($account_id, $jwt = null){
        $where = array(
            'account._id' => $account_id,
        );
        $select = array(
            'user._id',
            'user.account_id',
            'user.email',
            'user.social_type',
            'user.username',
            'account.fullname',
            'account.crop_img_src',
            'account.img_src',
            'account.social_img_src',
            'account_info.address',
            'account_info.phone',
            'account_info.birthday',
            'account_info.gender',
        );
        $user = $this->user_model->findOne($where, $select);

        $res = [
            '_id' => $account_id,
            'username' => !empty($user->username) ? $user->username : '',
            'email' => !empty($user->email) ? $user->email : '',
            'fullname' => !empty($user->fullname) ? $user->fullname : '',
            'phone' => !empty($user->phone) ? $user->phone : '',
            'birthday' => !empty($user->birthday) ? $user->birthday : '',
            'gender' => !empty($user->gender) ? $user->gender : '',
            'img_src' => !empty($user->img_src) ? $user->img_src : '',
            'crop_img_src' => !empty($user->crop_img_src) ? $user->crop_img_src : '',
            'social_img_src' => !empty($user->social_img_src) ? $user->social_img_src : '',
            'language_code' => LANGUAGE_DEFAULT,
            'social_type' => $user->social_type,
        ];
        
        if(!empty($jwt)){
            $res['jwt'] = $jwt;
        }

        return $res;
    }

    /*
     * write log file
     */
    protected function writeErrorLog($url, $txt){
        if (!is_dir(LOG_FOLDER)) { //create log folder
            mkdir('./' . LOG_FOLDER, 0777, TRUE);
        }

        $log_txt = "===".date('H:i:s')."===================\r\n";
        $log_txt .= $txt;
        $log_txt .= "\r\n\r\n\r\n";

        $log_file = LOG_FOLDER .'/error_log_'. date('dmY') .'_log_'. $_SERVER['SERVER_NAME'] .'.txt';
        $log_file_path = UPLOAD_PATH. $log_file;

        if(file_exists($log_file_path))
        {
            write_file($log_file_path, $log_txt, 'a');
        }
        else
        {
            write_file($log_file_path, $log_txt);
        }
    }
}