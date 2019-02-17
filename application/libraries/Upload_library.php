<?php
Class Upload_library
{
	var $CI = '';
	
	function __construct()
	{
		$this->CI = & get_instance();
	}
	
	//upload file
	//@$upload_path: ĐƯờng dẫn lưu file
	//@$file_name: tên thẻ input upload file
	function upload($upload_path='', $file_name='')
	{
		$config = $this->config_rename($upload_path);

//        $_FILES['userfile']['type']     = $_FILES['image']['type'];
//        $_FILES['userfile']['tmp_name'] = $_FILES['image']['tmp_name'];
		$this->CI->load->library('upload', $config);

        $this->CI->upload->initialize($config);
		if($this->CI->upload->do_upload($file_name))
		{
			$data = $this->CI->upload->data();
		}else{
			//không upload thành công
			$data = $this->CI->upload->display_errors();
		}
//        pre($_FILES['image']);
		return $data;
	}
	
	function upload_file($upload_path='', $file_name='')
	{
		//lấy thông tin cấu hình upload
		$config = $this->config($upload_path);
		//lưu biến môi trường khi thực hiện upload
		$file  = $_FILES['image_list'];
		$count = count($file['name']);//lấy tổng số file được upload
		$image_list = array();//lưu tên các file ảnh upload thành công
		for($i=0; $i<=$count-1; $i++) {
			$_FILES['userfile']['name']     = $file['name'][$i];  //khai báo tên của file thứ i
			$_FILES['userfile']['type']     = $file['type'][$i]; //khai báo kiểu của file thứ i
			$_FILES['userfile']['tmp_name'] = $file['tmp_name'][$i]; //khai báo đường dẫn tạm của file thứ i
			$_FILES['userfile']['error']    = $file['error'][$i]; //khai báo lỗi của file thứ i
			$_FILES['userfile']['size']     = $file['size'][$i]; //khai báo kích cỡ của file thứ i
			//load thư viện upload và cấu hình
			$this->CI->load->library('upload', $config);
            $this->CI->upload->initialize($config);
			//thực hiện upload từng file
			if($this->CI->upload->do_upload())
			{
				//nếu upload thành công thì lưu toàn bộ dữ liệu
				$data = $this->CI->upload->data();
				//in cấu trúc dữ liệu của các file
				$image_list[] = $data['file_name'];
			}else{
                $image_list[] = $this->CI->upload->display_errors();
            }
		}
//        pre($file['type']);
		return $image_list;
	}

	function upload_excel($file_name_key='', $upload_path='')
    {
        $config['upload_path'] = $upload_path;
        $config['max_size'] = 5048;		//5MB
        $config['allowed_types'] = 'xlsx|xls';

        $this->CI->load->library('upload', $config);
        if($this->CI->upload->do_upload($file_name_key))
        {
            $data = $this->CI->upload->data();
        }else{
            //không upload thành công
            $data = $this->CI->upload->display_errors();
        }
        return $data;
    }
	
	function config($upload_path = '')
	{
		//Khai bao bien cau hinh
		$config = array();
		//thuc mục chứa file
		$config['upload_path']   = $upload_path;
		//Định dạng file được phép tải
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		//Dung lượng tối đa
		$config['max_size']      = '3028';
        $config["extension_tolower"] = TRUE;
        //remane
//        $config['encrypt_name'] = TRUE;
		//Chiều rộng tối đa
//		$config['max_width']     = '3028';
		//Chiều cao tối đa
//		$config['max_height']    = '3028';
		
		return $config;
	}

    function config_rename($upload_path = '')
    {
        //Khai bao bien cau hinh
        $config = array();
        //thuc mục chứa file
        $config['upload_path']   = $upload_path;
        //Định dạng file được phép tải
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        //Dung lượng tối đa
//        $config['max_size']      = '0';
        //remane
        $config['encrypt_name'] = TRUE;
        $config["extension_tolower"] = TRUE;
        //Chiều rộng tối đa
//		$config['max_width']     = '3028';
        //Chiều cao tối đa
//		$config['max_height']    = '3028';

        return $config;
    }

	function upload_zip($file_name='', $upload_path=''){
//        $excel_file = $_FILES['excel_file'];
        $config['upload_path'] = $upload_path;
        $config['max_size'] = 5048;
        $config['allowed_types'] = 'rar|zip';

        $this->CI->load->library('upload', $config);
        if($this->CI->upload->do_upload($file_name))
        {
            $data = $this->CI->upload->data();
        }else{
            //không upload thành công
            $data = $this->CI->upload->display_errors();
        }
//        unlink(base_url() . "upload/user/file/".$data['file_name']);

        return $data;
    }

    function upload_video($filename='', $upload_path='')
    {
        $config['upload_path'] = $upload_path;
        $config['max_size'] = '';
        $config['allowed_types'] = 'mp4|avi|wmv|mov|svi|mkv';
        $config['encrypt_name'] = TRUE;
        $config["extension_tolower"] = TRUE;
        $this->CI->load->library('upload', $config);
        if($this->CI->upload->do_upload($filename))
        {
            $data = $this->CI->upload->data();
        }else{
            $data = $this->CI->upload->display_errors();
        }

        return $data;
    }
}