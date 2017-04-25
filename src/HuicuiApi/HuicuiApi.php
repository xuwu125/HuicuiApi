<?php
namespace HuicuiApi;

use HuicuiApi\Exception\HuicuiApiException;
use HuicuiApi\Handler\HandlerAdapter;

/**
 * 荟萃 API SDK for PHP
 * Created by PhpStorm.
 * User: changhe<xuwu125@gmail.com>
 * Date: 2017/4/24
 * Time: 11:25
 */
class HuicuiApi
{
    const VERSION = '1.0.3';
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
    /**
     * @var HandlerAdapter
     */
    private $_Handler = null;


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

    /**
     * HuicuiApi constructor.
     *
     * @param $AppId
     * @param $appKey
     * @param $AppSecret
     */
    public function __construct($AppId, $appKey, $AppSecret)
    {
        $this->AppId = $AppId;
        $this->AppKey = $appKey;
        $this->AppSecret = $AppSecret;
        $this->headers = [
            'User-Agent'  => static::CLIENT_NAME . '/' . static::VERSION .
                " PHP/" . PHP_VERSION . " " . PHP_OS . "/" . PHP_SAPI . "/" . $this->getSoftWaveName(),
            'Accept'      => 'application/json',
            "Server-Time" => time(),
        ];
    }

    protected function getSoftWaveName()
    {
        return isset($_SERVER ['SERVER_SOFTWARE']) ? $_SERVER ['SERVER_SOFTWARE'] : 'Shell';
    }


    /**
     * 初始化 Http请求类
     * @return \GuzzleHttp\Client
     */
    public function httpClient()
    {
        return new \GuzzleHttp\Client();
    }

    /**
     * @return HandlerAdapter
     * @throws HuicuiApiException
     */
    public function getHandler()
    {
        if (empty($this->_Handler) || $this->_Handler instanceof HandlerAdapter) {
            throw new HuicuiApiException("Handler 未初始化");
        }
        return $this->_Handler;
    }

    /**
     * @param HandlerAdapter $Handler
     */
    public function setHandler(HandlerAdapter $Handler)
    {
        $this->_Handler = $Handler;
    }

    /**
     * 获取 Token 缓存 ID
     * @return string
     */
    public function getTokenCacheID()
    {
        return static::TOKEN_NAME . '-' . $this->AppId;
    }

    /**
     * 获取Token
     * @return mixed
     */
    public function getToken()
    {
        $token = $this->getHandler()->get($this->getTokenCacheID());
        return (empty($token) || strlen($token) <= 16) ? "" : $token;
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
        $this->getHandler()->set($this->getTokenCacheID(), $Token, static::TOKEN_EXPIRE_TIME);
        return $this;
    }

    /**
     * 生成验证签名
     * @return string
     */
    protected function getSign()
    {
        $data = array(
            'AppID'     => $this->AppId,
            'AppKey'    => $this->AppKey,
            'AppSecret' => $this->AppSecret,
            'date'      => date("Ymd")
        );
        $datas = array();
        foreach ($data as $key => $val) {
            $datas [] = $key . '=' . rawurlencode($val);
        }
        $str = implode('&', $datas);
        return md5($str);
    }

    /**
     * 获取访问句柄 accessToken
     * @return $this
     * @throws HuicuiApiException
     */
    public function requestAccessToken()
    {
        $apiName = '/v1.0/accesstoken';
        $res = $this->httpClient()->request('POST', static::SCAHMA . '://' . static::DOMAIN . $apiName, [
            'headers'     => $this->headers,
            'form_params' => [
                'appid' => $this->AppId,
                'sign'  => $this->getSign()
            ]
        ]);
        if ($res->getStatusCode() == 200) {
            $re = $res->getBody();
            if ($re) {
                $reObject = ReturnMessage::Transform(json_decode($re));
                if (!$reObject->isOk()) {
                    throw new HuicuiApiException($reObject->getMessage());
                }
                $this->writeToken($reObject->getData()->accesstoken);
            } else {
                throw new HuicuiApiException("HuicuiAp::requestApi Request Url  Content empty", $res->getStatusCode());
            }
        } else {
            throw new HuicuiApiException("HuicuiAp::requestApi Request Url Error ", $res->getStatusCode());
        }
        return $this;
    }

    /**
     * @param $apiName
     *
     * @return ReturnMessage
     * @throws HuicuiApiException
     */
    protected function requestApi($apiName)
    {
        if (empty($apiName) || strlen($apiName) <= 4 || substr($apiName, 0, 1) != '/') {
            throw new HuicuiApiException("接口名不可为空");
        }
        $token = $this->getTokenCacheID();
        if (empty($token)) {
            throw new HuicuiApiException("Token无效，请先存储 Token");
        }
        $res = $this->httpClient()->request('POST', static::SCAHMA . '://' . static::DOMAIN . $apiName . '?accesstoken=' . $token, [
            'headers'     => $this->headers,
            'form_params' => $this->getParams()
        ]);
        // 每次请求完毕之后，清空参数
        $this->clearParams();
        if ($res->getStatusCode() == 200) {
            $re = $res->getBody();
            if ($re) {
                return ReturnMessage::Transform(json_decode($re));
            } else {
                throw new HuicuiApiException("HuicuiAp::requestApi Request Url  Content empty", $res->getStatusCode());
            }
        } else {
            throw new HuicuiApiException("HuicuiAp::requestApi Request Url Error ", $res->getStatusCode());
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