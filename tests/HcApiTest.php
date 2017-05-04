\<?php

/**
 * Created by PhpStorm.
 * User: changhe
 * Date: 2017/5/4
 * Time: 22:09
 */

require './bootstrap.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use HuicuiApi\Handler\MemcachedAdapter;
use HuicuiApi\HuicuiApi;
use HuicuiApi\ParamModel\CreateBsCronParams;

class HcApiTest
{
    const APP_ID = '52xxxx';
    const APP_KEY = 'xxxx';
    const APP_SECRET = 'xxxx';
    /**
     * @var Logger
     */
    var $log;

    /**
     * @return \HuicuiApi\HuicuiApi
     */
    protected function getApi()
    {

        $mem = new Memcached();
        $mem->addServers([
            array('127.0.0.1', '11211'),
            array('127.0.0.2', '11211')
        ]);
        $adapter = new MemcachedAdapter($mem);
        $apiLog = new Logger("HuicuiApiLog");
        $apiLog->pushHandler(new StreamHandler("/var/log/HuicuiApiLog_" . date("Ymd") . ".log", Logger::DEBUG));
        return HuicuiApi::getInstance(static::APP_ID, static::APP_KEY, static::APP_SECRET)->setLog($apiLog)->setHandler($adapter);
    }

    public function accesstokenAction()
    {
        $ic = 0;
        while (true) {
            try {
                $hcApi = $this->getApi();
                $accessToken = $hcApi->getToken();
                if (empty($accessToken)) {
                    $hcApi->requestAccessToken();
                }

            } catch (Exception $e) {
                var_dump([$e->getCode(), $e->getMessage(), $e->getTraceAsString()]);
            } finally {
                unset($hcApi);
            }
            sleep(1);
            $ic++;
        }
    }

    public function sendCronAction()
    {
        // 获取数据，这个需要自己根据自己的程序来获取
        $dInfo = CarPicData::findById("testtesttesttest");


        $param = new CreateBsCronParams();

        /* 设置公共参数 */
        // 设置标签
        $param->setDataTag("test");
        // 设置商容数据 ID 需要大于8位的字符串，并且不重复
        $param->setDataId($dInfo->getId());
        // API 任务 ID
        $param->setCronid(CarPicData::CRON_PLATE_SET);

        /* 设置私有参数 */
        $param->set("picurl", $dInfo->getPicUrl());
        $param->set("min", 1);
        $param->set("max", 1);
        $param->set("permin", 4);
        $param->set("permax", 4);


        //获取SDK实例
        $hcApi = $this->getApi();

        // 发送数据到 API
        $reData = $hcApi->clearParams()->setParams($param->getParams())->requestApi(CreateBsCronParams::API_NAME);


        // 进行响应判断
        if (CreateBsCronParams::isCreateOk($reData->getError())) {
            // 这里表示已经完成成功了
            var_dump($reData->getData()->did);
            var_dump($reData);
        } else {

            if ($reData->isTokenExpire()) {
                // 设置 Token 过期
                $hcApi->writeToken('');
                echo "自动更换新的 Token ";
            }
            var_dump($reData);
        }

    }
}
