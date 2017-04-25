<?php
/**
 * Created by PhpStorm.
 * User: changhe
 * Date: 2017/4/25
 * Time: 21:01
 */

namespace HuicuiApi\Exception;


use Exception;

class HuicuiApiException extends \Exception
{
    public function __construct($message='', $code=-1000, Exception $previous=null)
    {
        parent::__construct($message, $code, $previous);
    }
}