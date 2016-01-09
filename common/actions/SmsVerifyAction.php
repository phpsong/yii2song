<?php
/**
 * Author: Andy Song <c3306@qq.com>
 */

namespace common\actions;

use Yii;
use yii\base\Action;

/**
 * Class SetLocaleAction
 * @package common\actions
 *
 * Example:
 *
 *   public function actions()
 *   {
 *       return [
 *           'sms-verify'=>[
 *               'class'=>'common\actions\SmsVerifyAction',
 *               'key'=>'',
 *           ]
 *       ];
 *   }
 */
class SmsVerifyAction extends Action
{
    /**
     * 验证码Key
     * @var int
     */
    public $key = '';

    /**
     * Runs the action.
     * @param string $mobile 手机号
     */
    public function run($mobile)
    {
        $result = array(
            'success' => false,
            'message' => '发送失败，请稍后再试！',
        );
        if (!Yii::$app->smsVerify->expired($this->key)) {
            $result['message'] = '发送太快，请稍后再试！';
        } else {
            if (Yii::$app->smsVerify->send($this->key, $mobile)) {
                $result['success'] = true;
                $result['message'] = '短信验证码发送成功，请注意查收！';
            }
        }

        echo json_encode($result);
    }
}
