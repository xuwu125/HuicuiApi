<?php
/**
 * Created by PhpStorm.
 * User: changhe
 * Date: 2017/4/25
 * Time: 21:01
 */

namespace HuicuiApi\Exception;


class HuicuiApiAdapterException extends HuicuiApiException
{
    public function __construct($message='', $code=-1100, Exception $previous=null)
    {
        parent::__construct($message, $code, $previous);
    }
}