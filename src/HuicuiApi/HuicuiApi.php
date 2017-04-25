<?php
/**
 * 荟萃 API SDK for PHP
 * Created by PhpStorm.
 * User: changhe<xuwu125@gmail.com>
 * Date: 2017/4/24
 * Time: 11:25
 */
class HuicuiApi
{
    const VERSION = '1.0';
    /**
     * 请求的协议，支持 http 和 https
     */
    const SCAHMA = 'http';
    /**
     * 接口域名，不可变动
     */
    const DOMAIN = 'api.open.huicui.me';
    /**
     * token 的名字
     */
    const TOKEN_NAME = 'accesstoken';
    /**
     * TOKEN 的最长存储时间
     */
    const TOKEN_EXPIRE_TIME = 7100;
    const CLIENT_NAME = 'HuicuiApi';
    /**
     * AppID
     * @var string
     */
    public $AppId = '';
    /**
     * AppKey
     * @var string
     */
    public $AppKey = '';
    /**
     * AppSecret
     * @var string
     */
    public $AppSecret = '';
    public $params = [];
    protected $headers = [];


    /**
     * @param $AppId
     * @param $appKey
     * @param $AppSecret
     *
     * @return static
     */
    public static function getInstance($AppId, $appKey, $AppSecret)
    {
        return new static($AppId, $appKey, $AppSecret);
    }

    public function __construct($AppId, $appKey, $AppSecret)
    {
        $this->AppId = $AppId;
        $this->AppKey = $appKey;
        $this->AppSecret = $AppSecret;
        $this->headers = [
            'User-Agent'        => static::CLIENT_NAME . '/' . static::VERSION .
                " PHP/" . PHP_VERSION . " " . PHP_OS . "/" . PHP_SAPI . "/" . $_SERVER ['SERVER_SOFTWARE'],
            'Accept'            => 'application/json',
            "Server-Time"       => time(),
            "Server-Ip-Address" => gethostbyname($_SERVER['SERVER_NAME'])
        ];
    }

    public function httpClient()
    {
        return new \GuzzleHttp\Client();
    }

    /**
     * 设置一个缓存器
     * @return Memcached
     */
    public static function getCache()
    {
        return $GLOBALS['di']->getCache();
    }

    /**
     * 获取 Token
     * @return string
     */
    public function getTokenCacheID()
    {
        return static::TOKEN_NAME . '-' . $this->AppId;
    }

    /**
     * 获取
     * @return mixed
     */
    public function getToken()
    {
        return static::getCache()->get($this->getTokenCacheID());
    }

    /**
     * 写入 token
     *
     * @param $Token
     *
     * @return $this
     */
    public function writeToken($Token)
    {
        static::getCache()->set($this->getTokenCacheID(), $Token, static::TOKEN_EXPIRE_TIME);
        return $this;
    }


    protected function requestApi($apiname, $param)
    {
        $token = $this->getTokenCacheID();
        if (empty($token) || strlen($token) < 16) {
            throw new HttpInvalidParamException("Token无效，请先存储 Token");
        }
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', static::SCAHMA . '://' . static::DOMAIN . $apiname . '?accesstoken=' . $token, [
            'headers'     => $this->headers,
            'form_params' => $this->getParams()
        ]);
        // 每次请求完毕之后，清空参数
        $this->clearParams();
        if ($res->getStatusCode() == 200) {
            $re = $res->getBody();
            if ($re) {
                return json_decode($re);
            } else {
                throw new Exception("HuicuiAp::requestApi Request Url  Content empty", $res->getStatusCode());
            }
        } else {
            throw new Exception("HuicuiAp::requestApi Request Url Error ", $res->getStatusCode());
        }
    }

    /**
     * 获取所有参数列表
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * 用一个数据来重新初始化参数列表
     *
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->clearParams();
        foreach ($params as $key => $val) {
            $this->setParam($key, $val);
        }
        return $this;
    }

    /**
     * 删除一个已经添加的参数
     *
     * @param $key
     *
     * @return $this
     */
    public function removeParam($key)
    {
        if (array_key_exists($key, $this->params)) {
            unset($this->params[ $key ]);
        }
        return $this;
    }

    /**
     * 更新一个指定的参数，不存在的时候会自动创建
     *
     * @param $key
     * @param $val
     *
     * @return $this
     */

    public function setParam($key, $val)
    {
        $this->params[ $key ] = $val;
        return $this;
    }

    /**
     * 清理掉所有参数
     * @return $this
     */
    public function clearParams()
    {
        $this->params = [];
        return $this;
    }
}