<?php
namespace HuicuiApi\Handler;
/**
 * Created by PhpStorm.
 * User: changhe
 * Date: 2017/4/25
 * Time: 20:51
 */

interface HandlerInterface{
    /**
     * 获取一个缓存值
     * @param $key
     *
     * @return mixed
     */
    function get($key);

    /**
     * 获取一个可过期的值
     * @param     $key
     * @param     $val
     * @param int $expire
     *
     * @return mixed
     */
    function set($key,$val,$expire=7100);
}