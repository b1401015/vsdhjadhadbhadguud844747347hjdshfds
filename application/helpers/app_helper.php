<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('dump')) {
    function dump($list, $exit = true)
    {
        echo '<pre>';
        print_r($list);
        if ($exit) {
            die();
        }
    }
}

if (!function_exists('isJson')) {
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('checkVerifyParams')) {
    function checkVerifyParams($params, $where = 'OR')
    {
        $rs = [];

        if ($where == 'AND') {
            $count = 0;
            $length = count($params);
            foreach ($params as $p) {
                if (empty($p)) {
                    $count++;
                }
            }

            if ($count > 0 && $count == $length) {
                $rs = RestBadRequest(MISMATCH_PARAMS_MSG);
            }
        } else if ($where == 'OR') {
            foreach ($params as $p) {
                if (empty($p)) {
                    $rs = RestBadRequest(MISMATCH_PARAMS_MSG);
                }
            }
        }

        return $rs;
    }
}

if (!function_exists('skipVN')) {
    function skipVN($str, $is_format = false, $type = STR_LOWERCASE)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);

        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        if ($is_format) {
            switch ($type) {
                case STR_LOWERCASE:
                    $str = strtolower($str);
                    break;
                case STR_UPPERCASE:
                    $str = strtoupper($str);
                    break;
                default:
                    break;
            }
        }
        return $str;
    }
}

/*
 * get extension by file_type
 */
if (!function_exists('getExtenstions')) {
    function getExtenstions($file_type){
        switch ($file_type){
            case 'video':
                return [
                    'flv',
                    'm3u8',
                    'ts',
                    '3gp',
                    'avi',
                    'wmv',
                    'mp4',
                    'mov',
                    'ogg',
                    'qt',
                    'FLV',
                    'M3U8',
                    'TS',
                    '3GP',
                    'AVI',
                    'WMV',
                    'MP4',
                    'MOV',
                    'OGG',
                    'QT'
                ];
                break;
            case 'audio':
                return [
                    'mp3',
                    'ogg',
                    'wav',
                ];
                break;
            case 'image':
                return [
                    'png',
                    'jpg',
                    'jpeg',
                    'bmp'
                ];
                break;
            case 'excel':
                return [
                    'xlsx',
                    'xls',
                    'csv',
                ];
                break;
            default:
                return;
                break;
        }

    }
}
/*
* hash a string by Hash 256 algorithm
*/
if (!function_exists('hash256')) {
    function hash256($data)
    {
        return hash('sha256', $data);
    }
}

/*
 *  generate _key for translation
 */
if (!function_exists('generateKeyTranslation')) {
    function generateKeyTranslation()
    {
        $random = md5(uniqid(rand(), true));
        return substr($random, 0, 10);
    }
}
/*
 *  scan excel type by extension
 */
if (!function_exists('scan_file_type')) {
    function scan_file_type($extention)
    {
        if ($extention == '.xlsx') {
            $ext = 'Excel2007';
        } elseif ($extention == '.xls') {
            $ext = 'Excel5';
        }
        return $ext;
    }
}

/*
 *  remove all html tags & special characters
 */
if (!function_exists('removeHTMLTagsAndSpecialChars')) {
    function removeHTMLTagsAndSpecialChar($str)
    {
        return trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($str))))));
    }
}

if (!function_exists('color_error')) {
    function color_error()
    {
        $style = array(//set color for header
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'ff8080'
            ),

        );
        return $style;
    }
}

if (!function_exists('arr_all_border')) {
    function arr_all_border()
    {
        $styleArray = array(//set border all row
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        return $styleArray;
    }
}

if (!function_exists('generateReferenceCode')) {
    function generateReferenceCode($start = 0, $length = 10)
    {
        $random = md5(uniqid(rand(), true));
        return strtoupper(substr($random, $start, $length));
    }
}

if (!function_exists('removeSpecialChars')) {
    function removeSpecialChars($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }
}

if (!function_exists('cleanFileName')) {
    function cleanFileName($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        $string = preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
        return skipVN($string, true);
    }
}

if (!function_exists('resizeImage')) {
    function resizeImage($file,
                         $string = null,
                         $width = 0,
                         $height = 0,
                         $proportional = false,
                         $output = 'file',
                         $delete_original = true,
                         $use_linux_commands = false,
                         $quality = 100
    )
    {

        if ($height <= 0 && $width <= 0) return false;
        if ($file === null && $string === null) return false;

        # Setting defaults and meta
        $info = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
        $image = '';
        $final_width = 0;
        $final_height = 0;
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;

        # Calculating proportionality
        if ($proportional) {
            if ($width == 0) $factor = $height / $height_old;
            elseif ($height == 0) $factor = $width / $width_old;
            else                    $factor = min($width / $width_old, $height / $height_old);

            $final_width = round($width_old * $factor);
            $final_height = round($height_old * $factor);
        } else {
            $final_width = ($width <= 0) ? $width_old : $width;
            $final_height = ($height <= 0) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;

            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }

        # Loading image to memory according to type
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);
                break;
            case IMAGETYPE_GIF:
                $file !== null ? $image = imagecreatefromgif($file) : $image = imagecreatefromstring($string);
                break;
            case IMAGETYPE_PNG:
                $file !== null ? $image = imagecreatefrompng($file) : $image = imagecreatefromstring($string);
                break;
            default:
                return false;
        }


        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor($final_width, $final_height);
        if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);

            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color = imagecolorsforindex($image, $transparency);
                $transparency = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            } elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);

        # Taking care of original, if needed
        if ($delete_original) {
            if ($use_linux_commands) exec('rm ' . $file);
            else @unlink($file);
        }

        # Preparing a method of providing result
        switch (strtolower($output)) {
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
                break;
            case 'file':
                $output = $file;
                break;
            case 'return':
                return $image_resized;
                break;
            default:
                break;
        }

        # Writing image according to type to the output destination and image quality
        switch ($info[2]) {
            case IMAGETYPE_GIF:
                imagegif($image_resized, $output);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($image_resized, $output, $quality);
                break;
            case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9 * $quality) / 10.0);
                imagepng($image_resized, $output, $quality);
                break;
            default:
                return false;
        }

        return true;
    }
}

if (!function_exists('removeNullOfObject')) {
    function removeNullOfObject($obj)
    {
        return (object) array_filter((array) $obj);
    }
}

if (!function_exists('removeNullElementOfArray')) {
    function removeNullElementOfArray($arr)
    {
        $data = array();
        foreach($arr as $item){
            $data[] = (object) array_filter((array) $item);
        }

        return $data;
    }
}

if (!function_exists('date_sort')) {
    function date_sort($a, $b)
    {
        return strtotime($a) - strtotime($b);
    }
}

/*
 * Function sort total chairs have booked in car (desc)
 */
function cmp($a, $b)
{
    return strcmp($b->total_chairs, $a->total_chairs);
}

/**
 * generate new Captcha image
 * return: HTML of <img/>
 */
if (!function_exists('generateImageBase64')) {
    function generateImageBase64($img_src)
    {

        $image = imagecreatefromjpeg($img_src);

// Add some filters
        imagefilter($image, IMG_FILTER_PIXELATE, 1, true);
        imagefilter($image, IMG_FILTER_MEAN_REMOVAL);

        ob_start(); // Let's start output buffering.
        imagejpeg($image); //This will normally output the image, but because of ob_start(), it won't.
        $contents = ob_get_contents(); //Instead, output above is saved to $contents
        ob_end_clean(); //End the output buffer.

        return base64_encode($contents);
    }
}

/**
 * add http to url if not exists
 */
if (!function_exists('addHTTP')) {
    function addHTTP($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }
}

function color_header()
{
    $style = array(//set color for header
        'font' => array('bold' => true),
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
            'rgb' => COLOR_HEADER
        ),

    );
    return $style;
}

function color_error()
{
    $style = array(//set color for header
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
            'rgb' => 'ff8080'
        ),

    );
    return $style;
}

function style_bold()
{
    $style = array(
        'font'  => array(
            'bold'  => true,
            'size'  => 15,
        )
    );
    return $style;
}