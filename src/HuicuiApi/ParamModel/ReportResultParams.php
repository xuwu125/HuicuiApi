<?php
/**
 * Created by PhpStorm.
 * User: changhe
 * Date: 2017/4/27
 * Time: 10:40
 */

namespace HuicuiApi\ParamModel;


use HuicuiApi\Exception\HuicuiApiParamException;

/**
 * Class CreateBsCronParams
 * 创建新任务的参数同模型，单人任务和多人任务均可使用
 * @package HuicuiApi\ParamModel
 */
class ReportResultParams extends BaseModel
{
    /**
     * DID 数据 ID
     * @var string
     */
    private $did = '';
    /**
     * 投诉的具体内容
     * @var string
     */
    private $report = '';
    /**
     * 需要检查的必须有的参数列表
     * @var array
     */
    protected $_mustParams = [
        'did'
    ];

    public function setParams($params)
    {
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $val) {
                $this->set($key, $val);
            }
        }
    }

    /**
     * 获取所有参数
     * @return array
     */
    public function getParams()
    {
        return $this->checkParams() ? parent::getAllVars() : [];
    }

    public function clearParams()
    {
        foreach (parent::getAllVars() as $k => $v) {
            $this->set($k, '');
        }

    }

    /**
     * 检查参数是否够了
     * @return bool
     * @throws HuicuiApiParamException
     */
    public function checkParams()
    {
        foreach ($this->_mustParams as $key) {
            if (empty($this->get($key))) {
                throw new HuicuiApiParamException('Param [ ' . $key . '] is empty');
            }
        }
        return true;
    }

    /**
     * @param string $did
     */
    public function setDid($did)
    {
        $this->did = $did;
    }

    /**
     * @param string $report
     */
    public function setReport($report)
    {
        $this->report = $report;
    }


}