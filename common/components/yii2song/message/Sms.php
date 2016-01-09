<?php
/**
 * Created by PhpStorm.
 * User: AndySong
 * Date: 2015-11-24
 * Time: 10:20
 */

namespace common\components\message;

use common\models\base\SentSms;

/**
 * Class Config
 * @package common\components\system
 */
class Sms implements MessageInterface
{
    const SENT_SUCCESS_MESSAGE = '短信验证码发送成功！';
    const SENT_FAILURE_MESSAGE = '发送失败，请稍候再试！';
    /**
     * 发送认证：用户名
     * @var string
     */
    public $user = '';
    /**
     * 发送认证：密钥
     * @var string
     */
    public $key = '';
    /**
     * 触发代码
     * @var string
     */
    public $trigger_code = '';
    /**
     * 模板编号
     * @var string
     */
    public $template_id = '';
    /**
     * 模板中要替换内容的标识
     * @var string
     */
    public $template_replace = '';
    /**
     * 模板
     * @var array
     */
    public $templates = [
        'verify_code' => [
            'template_id'      => '165', // 模板编号
            'template_replace' => '%Code%', // 模板中要替换内容的标识
        ],
    ];

    /**
     * 设置模板编号
     *
     * @param $code
     *
     * @return $this
     */
    public function template($code)
    {
        if ($this->templates[ $code ]) {
            $this->template_id = $this->templates[ $code ]['template_id'];
            $this->template_replace = $this->templates[ $code ]['template_replace'];
        }

        return $this;
    }

    /**
     * 实际发送
     *
     * @param $mobile
     * @param $message
     * @param $code
     *
     * @return string
     */
    public function send($mobile, $message, $code = '')
    {
        //return true;
        if (!empty($code)) {
            $this->template($code);
        } elseif (empty($this->template_id) || empty($this->template_replace)) {
            $this->template($this->trigger_code);
        }

        $url = 'http://sendcloud.sohu.com/smsapi/send';

        $param = array(
            'smsUser'    => $this->user,
            'templateId' => $this->template_id,
            'phone'      => $mobile,
            'vars'       => '{"' . $this->template_replace . '":"' . $message . '"}',
        );

        $sParamStr = "";
        ksort($param);
        foreach ($param as $sKey => $sValue) {
            $sParamStr .= $sKey . '=' . $sValue . '&';
        }

        $sParamStr = trim($sParamStr, '&');
        $smskey = $this->key;
        $sSignature = md5($smskey . "&" . $sParamStr . "&" . $smskey);


        $param = array(
            'smsUser'    => $this->user,
            'templateId' => $this->template_id,
            'phone'      => $mobile,
            'vars'       => '{"' . $this->template_replace . '":"' . $message . '"}',
            'signature'  => $sSignature,
        );

        $data = http_build_query($param);

        $option = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-Type:application/x-www-form-urlencoded',
                'content' => $data,

            ));
        $context = stream_context_create($option);
        $result = file_get_contents($url, FILE_TEXT, $context);

        $send_result = json_decode($result);

        if ($send_result->result) {
            return true;
        }

        return false;
    }
}


