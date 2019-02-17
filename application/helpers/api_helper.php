<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('RestBadRequest')) {
    function RestBadRequest($message)
    {
        $rs = [
            'status' => BAD_REQUEST_CODE,
            'message' => $message
        ];

        return $rs;
    }
}

if (!function_exists('RestServerError')) {
    function RestServerError($message = '')
    {
        $rs = [
            'status' => SERVER_ERROR_CODE,
            'message' => !empty($message) ? $message : SERVER_ERROR_MSG
        ];

        return $rs;
    }
}

if (!function_exists('RestForbidden')) {
    function RestForbidden($message)
    {
        $rs = [
            'status' => FORBIDDEN_CODE,
            'message' => $message
        ];

        return $rs;
    }
}

if (!function_exists('RestNotFound')) {
    function RestNotFound()
    {
        $rs = [
            'status' => NOT_FOUND_CODE,
            'message' => NOT_FOUND_MSG
        ];

        return $rs;
    }
}

if (!function_exists('RestSuccess')) {
    function RestSuccess($data = null)
    {
        $rs = [
            'status' => SUCCESS_CODE,
            'message' => OK_MSG
        ];

        if (is_array($data)) {
            $rs['data'] = $data;
        }

        if (!empty($data)) {
            $rs['data'] = $data;
        }

        return $rs;
    }
}