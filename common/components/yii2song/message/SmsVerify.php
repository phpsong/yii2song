<?php
/**
 * Created by PhpStorm.
 * User: AndySong
 * Date: 2015-11-26
 * Time: 10:20
 */

namespace common\components\message;

use yii;
use yii\base\Component;

/**
 * Class SmsVerify
 * @package common\components\message
 */
class SmsVerify extends Component
{
    const SMS_CODE = 'verify_code';
    const SESSION_KEY_PREFIX = 'verify_code_';

    /**
     * 短信验证码有效时间（秒）
     * @var int
     */
    public $lifetime = 600;

    /**
     * 发送验证码
     * @param $key
     * @param $mobile
     * @return mixed
     */
    public function send($key, $mobile)
    {
        $value = $this->getVerifyCode($key, $mobile);
        return Yii::$app->sms->send($mobile, $value['value'], self::SMS_CODE);
    }

    /**
     * 检查验证码是否正确
     * @param $key
     * @param $mobile
     * @param $message
     * @return bool
     */
    public function validate($key, $mobile, $message)
    {
        if ($this->expired($key)) {
            return false;
        }
        $value = $this->getVerifyCode($key);
        if ($value['value'] == $message && $value['mobile'] == $mobile) {
            return true;
        }

        return false;
    }

    /**
     * 验证码是否过期
     * @param $key
     * @return bool
     */
    public function expired($key)
    {
        $value = $this->getVerifyCode($key);

        if ($value
            && isset($value['value'])
            && isset($value['timestamp'])
            && ((time() - $value['timestamp']) < $this->lifetime)
        ) {
            return false;
        }
        return true;
    }

    /**
     * 销毁验证码
     * @param $key
     * @return bool
     */
    public function destroy($key)
    {
        $session = Yii::$app->getSession();
        $session->open();
        $name = $this->getSessionKey($key);
        $session[$name] = null;

        return true;
    }

    /**
     * 获取Session Key
     * @param $key
     * @return string
     */
    protected function getSessionKey($key)
    {
        return self::SESSION_KEY_PREFIX . $key;
    }

    /**
     * 获取验证码
     * @param $key
     * @param string $mobile
     * @return mixed
     */
    public function getVerifyCode($key, $mobile = '')
    {
        $session = Yii::$app->getSession();
        $session->open();
        $name = $this->getSessionKey($key);
        if (!empty($mobile)) {
            $session[$name] = $this->generateVerifyCode($mobile);
            $session[$name . 'count'] = 1;
        }

        return $session[$name];
    }

    /**
     * 生成验证码
     * @param $mobile
     * @return array
     */
    protected function generateVerifyCode($mobile)
    {
        return array(
            'value' => mt_rand(100000, 999999),
            'mobile' => $mobile,
            'timestamp' => time(),
        );
    }
}