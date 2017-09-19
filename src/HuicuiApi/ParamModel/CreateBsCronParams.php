<?php
/**
 * Created by PhpStorm.
 * User: changhe
 * Date: 2017/4/27
 * Time: 10:40
 */

namespace HuicuiApi\ParamModel;


use HuicuiApi\Exception\HuicuiApiParamException;
use HuicuiApi\ReturnMessage;

/**
 * Class CreateBsCronParams
 * 创建新任务的参数同模型，单人任务和多人任务均可使用
 * @package HuicuiApi\ParamModel
 */
class CreateBsCronParams extends BaseModel
{
    /**
     * API任务ID
     * @var string
     */
    public $cronid = '';
    /**
     * 本地数据 ID
     * @var string
     */
    public $data_id = '';
    /**
     * 数据选择的标签
     * @var string
     */
    public $data_tag = '';
    /**
     * 数据需要过滤的标签
     * @var string
     */
    public $data_tag_filter = '';
    /**
     * 数据需要过滤的用户
     * @var string
     */
    public $data_uid_filter = '';
    /**
     * 数据优先级
     * @var int
     */
    public $priority = 0;
    /**
     * 需要检查的必须有的参数列表
     * @var array
     */
    public $_mustParams = [
        'cronid', 'data_id', 'data_tag'
    ];
    const API_NAME = '/v1.0/createbscron';
    /**
     * 有效的请求
     */
    const CREATE_CRON_REPEAT = 3000;
    const CREATE_CRON_REPEAT_SUBMIT = 3001;

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
     * @param int $cronid
     */
    public function setCronid($cronid)
    {
        $this->cronid = strval($cronid);
    }

    /**
     * @param string $data_id
     */
    public function setDataId($data_id)
    {
        $this->data_id = strval($data_id);
    }

    /**
     * @param string $data_tag
     */
    public function setDataTag($data_tag)
    {
        if (is_array($data_tag)) {
            $this->data_tag = !empty($data_tag) ? implode(",", $data_tag) : '';
        } else {
            $this->data_tag = $data_tag;
        }
    }

    /**
     * @param string $data_tag_filter
     */
    public function setDataTagFilter($data_tag_filter)
    {
        if (is_array($data_tag_filter)) {
            $this->data_tag_filter = !empty($data_tag_filter) ? implode(",", $data_tag_filter) : '';
        } else {
            $this->data_tag_filter = $data_tag_filter;
        }
    }

    /**
     * @param string $data_uid_filter
     */
    public function setDataUidFilter($data_uid_filter)
    {
        if (is_array($data_uid_filter)) {
            $this->data_uid_filter = !empty($data_uid_filter) ? implode(",", $data_uid_filter) : '';
        } else {
            $this->data_uid_filter = $data_uid_filter;
        }
    }

    /**
     * @param int $priority
     *
     * @throws HuicuiApiParamException
     */
    public function setPriority($priority)
    {
        $priority = intval($priority);
        if ($priority < 0 || $priority > 9) {
            throw new HuicuiApiParamException("priority's range is [0-9]");
        }
        $this->priority = $priority;

    }

    /**
     * 检查是否返回的代码是成功状态
     *
     * @param int $ErrorCode
     *
     * @return bool
     */
    public static function isCreateOk($ErrorCode = -1)
    {
        if (in_array($ErrorCode, [ReturnMessage::STATUS_OK, static::CREATE_CRON_REPEAT, static::CREATE_CRON_REPEAT_SUBMIT])) {
            return true;
        }
        return false;
    }

}