<?php


namespace App\Library;

class Response
{
    const SUCCEED_CODE = 0;
    const FAIL_CODE = -1;
    const ERROR_CODE = -2;

    private static $messages = [
        -2 => '系统错误',
        -1 => 'fail',
        0 => 'ok',
    ];

    public static function JsonData($data = [], $code = 0, $message = 'ok')
    {
        $status = 'succeed';
        if ($code < 0) {
            $status = 'fail';
        }else if ($code >= 400 && $code <= 499) {
            $status = 'error';
        }
        $message = (!$message && self::message($code) !== null) ? self::message($code) : $message;
        $arr = [
            "code" => $code,
            "status" => $status,
            "message" => $message,
        ];
        if (!empty($data)) {
            $arr["data"] = $data ?: (object) $data;
        }
        return isset($data['data']) ? array_merge($arr, $data) : $arr;
    }

    private static function message($code = 0)
    {
        return self::$messages[$code] ?? 'ok';
    }
}
