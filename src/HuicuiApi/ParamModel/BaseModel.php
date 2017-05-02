<?php
namespace HuicuiApi\ParamModel;
/**
 * 参数基础模块
 * Created by PhpStorm.
 * 私有变量请
 * User: changhe
 * Date: 2017/4/27
 * Time: 10:26
 */
abstract class BaseModel
{

    abstract public function setParams($params);

    abstract public function getParams();

    abstract public function clearParams();

    abstract public function checkParams();

    /**
     * 初始化
     * @return static
     */
    public static function getInstance()
    {
        return new static();
    }

    public function set($key, $val)
    {
        $this->$key = $val;
    }

    public function get($key)
    {
        return $this->$key;
    }

    /**
     * 公共获取所有属性列表，以_（下划线）开头的除外
     * @return array
     */
    public function getAllVars()
    {
        $_attributes = get_class_vars(get_class($this));
        $maps = [];
        foreach ($_attributes as $key => $val) {
            if (substr($key, 0, 1) != '_') {
                $maps[ $key ] = $val;
            }
        }
        return $maps;
    }
}