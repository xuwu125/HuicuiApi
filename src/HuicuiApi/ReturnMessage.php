<?php
/**
 * Created by PhpStorm.
 * User: changhe
 * Date: 2017/4/25
 * Time: 21:23
 */

namespace HuicuiApi;


use HuicuiApi\Exception\HuicuiApiException;

class ReturnMessage
{

    var $error;
    var $message;
    var $data;
    /**
     * 请求正常
     */
    const STATUS_OK = 0;
    /**
     * Token 过期
     */
    const API_TOKEN_EXP_ONE=4446 ;
    const API_TOKEN_EXP_TWO=4447 ;


    function __construct($error, $message, $data)
    {
        $this->error = $error;
        $this->message = $message;
        $this->data = $data;
    }

    public static function Transform(\stdClass $json)
    {
        if(!$json || !$json instanceof \stdClass){
            throw new HuicuiApiException("无效使用解析类");
        }
        return new static($json->error, $json->message, $json->data);
    }

    /**
     * 判断请求是否正常
     * @return bool
     */
    public function isOk()
    {
        return $this->getError() == static::STATUS_OK ? true : false;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return intval($this->error);
    }

    /**
     * @param mixed $error
     */
    public function setError($error)
    {
        $this->error = intval($error);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * 是否 Token 过期
     */
    public function isTokenExpire(){
        if(in_array($this->error,[static::API_TOKEN_EXP_ONE,static::API_TOKEN_EXP_TWO])){
            return true;
        }
        return false;
    }

}