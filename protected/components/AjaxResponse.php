<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Usbuild
 * Date: 12-8-6
 * Time: 上午11:23
 */
class AjaxResponse
{
    const SUCCESS = 200;
    const MISSING_PARAM = 201;
    const NO_APP = 202;
    const NO_EVENT = 203;
    const NO_LISTENER = 204;
    const NOT_PRIVATE = 205;
    const UPDATE_NOT_EXISTS = 206;
    const DELETE_NOT_EXISTS = 207;
    const NOTHING_CHANGED = 208;

    const NOT_AUTHORIZED = 301;
    const RESOURCE_LIMIT = 302;

    const TYPE_ERROR = 401;
    const REQUEST_METHOD_ERROR = 402;
    const OPERATION_FORBIDDEN = 403;
    const RESOURCE_NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const TIME_OUT = 406;
    const INVALID_PARAM=407;

    const DB_ERROR = 501;
    const UPLOAD_FAILED = 502;

    static $codeMap = array(
        self::SUCCESS => 'OK',
        self::MISSING_PARAM => 'Missing Required Params',
        self::NO_APP => 'No Such App',
        self::NO_EVENT => 'No Such Event',
        self::NO_LISTENER => 'No Such Listener',
        self::NOT_PRIVATE => 'Not A Private Event',
        self::UPDATE_NOT_EXISTS => 'Update Item Not Exist',
        self::DELETE_NOT_EXISTS => 'Delete Item Not Exist',
        self::NOTHING_CHANGED => 'No Data Has Been Changed',

        self::NOT_AUTHORIZED => 'Not Authorized',
        self::RESOURCE_LIMIT => 'Out Of Resource Limit',
        self::TYPE_ERROR => 'Type Error',
        self::RESOURCE_NOT_FOUND => 'Resource Not Found',
        self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::REQUEST_METHOD_ERROR => 'Request Method Error',
        self::OPERATION_FORBIDDEN => 'Illegal Param',
        self::TIME_OUT => 'Time Out',
        self::INVALID_PARAM=>'Invalid Parameter',
        self::DB_ERROR => 'Cant\'t Update Database',
        self::UPLOAD_FAILED => 'Upload Failed',
    );

    static function send($errno, $data = NULL, $exit = true)
    {
        if (array_key_exists($errno, self::$codeMap)) {
            if ($errno == self::SUCCESS)
                echo CJSON::encode(array('success' => true, 'code' => $errno, 'data' => is_null($data) ? self::$codeMap[$errno] : $data));
            else if (is_null($data))
                echo CJSON::encode(array('success' => false, 'code' => $errno, 'data' => self::$codeMap[$errno]));
            else
                echo CJSON::encode(array('success' => false, 'code' => $errno, 'data' => $data));
            if ($exit) Yii::app()->end();
        } else {
            echo CJSON::encode(array('success' => false, 'code' => $errno, 'data' => 'Unknown Error'));
            Yii::app()->end();
        }
    }

    static function resourceNotFound($info=null)
    {
        self::send(self::RESOURCE_NOT_FOUND,$info);
    }

    static function success($data=NULL)
    {
        self::send(self::SUCCESS,$data);
    }

    static function forbidden($info=null)
    {
        self::send(self::OPERATION_FORBIDDEN,$info);
    }

    static function methodNotAllowed()
    {
        self::send(self::METHOD_NOT_ALLOWED);
    }

    static function nothingChanged($info=null)
    {
        self::send(self::NOTHING_CHANGED,$info);
    }

    static function missParam($info=null){
        self::send(self::MISSING_PARAM,$info);
    }

    static function invalidParam(){
        self::send(self::INVALID_PARAM);
    }
    static function saveError($error_array){
        self::send(self::DB_ERROR,$error_array);
    }
}
