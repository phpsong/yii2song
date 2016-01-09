<?php

/**
 * @copyright  Copyright &copy; Andy Song <c3306@qq.com>, 2015
 * @package    yii2-widgets
 * @subpackage yii2-widget-smsverify
 * @version    1.0.0
 */

namespace common\components\smsverify;

/**
 * SmsVerify widget is an enhanced widget.
 *
 * @author Andy Song <c3306@qq.com>
 * @since  1.0
 * @see    http://git.i3a.com.cn
 */
class SmsVerifyAsset extends \kartik\base\AssetBundle
{
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/smsverify']);
        $this->setupAssets('js', ['js/smsverify']);
        parent::init();
    }
}
