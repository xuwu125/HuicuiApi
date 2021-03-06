<?php
/**
 * Created by PhpStorm.
 * User: changhe
 * Date: 2017/4/25
 * Time: 20:55
 */

namespace HuicuiApi\Handler;


use Doctrine\Instantiator\Exception\InvalidArgumentException;
use HuicuiApi\Exception\HuicuiApiAdapterException;

class HandlerAdapter
{
    private $adapter = null;

    /**
     * HandlerAdapter constructor.
     *
     * @param  \Redis|\Memcached $adapter
     *
     * @throws HuicuiApiAdapterException
     */
    function __construct($adapter = null)
    {
        if (!empty($adapter)) {
            $this->setAdapter($adapter);
        }
        return $this;
    }

    /**
     * @return null
     */

    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param null $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * 获取一个缓存值
     *
     * @param $key
     *
     * @return mixed
     */
    function get($key)
    {
        return $this->getAdapter()->get($key);
    }

    /**
     * 设置一个缓存 的类型的可过期值
     *
     * @param     $key
     * @param     $val
     * @param int $expire
     *
     * @return bool
     */
    function set($key, $val, $expire = 7100)
    {
        return $this->getAdapter()->set($key, $val, $expire);
    }
}