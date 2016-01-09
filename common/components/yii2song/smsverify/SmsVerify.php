<?php

/**
 * 短信验证
 * @copyright  Copyright &copy; Andy Song <c3306@qq.com>, 2015
 * @package    yii2-widgets
 * @subpackage yii2-widget-smsverify
 * @version    1.0.0
 */

namespace common\components\smsverify;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\web\JsExpression;

/**
 * SmsVerify widget is an enhanced widget.
 *
 * @author Andy Song <c3306@qq.com>
 * @since  1.0
 * @see    http://git.i3a.com.cn
 */
class SmsVerify extends \kartik\base\InputWidget
{
    const TYPE_COMPONENT_PREPEND = 1;
    const TYPE_COMPONENT_APPEND = 2;
    const TYPE_INLINE = 3;

    /**
     * @var string the markup type of widget markup
     * must be one of the TYPE constants. Defaults
     * to [[TYPE_COMPONENT_PREPEND]]
     */
    public $type = self::TYPE_COMPONENT_APPEND;

    /**
     * @var string The size of the input - 'lg', 'md', 'sm', 'xs'
     */
    public $size = '';

    /**
     * @var ActiveForm the ActiveForm object which you can pass for seamless usage
     * with ActiveForm. This property is especially useful for client validation of
     * attribute2 for [[TYPE_RANGE]] validation
     */
    public $form;

    public $mobileField = 'mobile';
    public $placeholder = '';
    public $maxlength = 6;

    /**
     * @var array the HTML attributes for the input tag.
     */
    public $options = [];

    /**
     * @var array the HTML options for the IconInput container
     */
    private $_container = [];
    /**
     * @var array widget plugin options
     */
    public $pluginOptions = [];
    /**
     * Initializes the widget
     * @throws
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->pluginName = 'smsVerify';
        parent::init();

        $this->_container['id'] = $this->options['id'] . '-input';
        $this->pluginOptions['formId'] = $this->form->options['id'];
        $this->pluginOptions['mobileField'] = $this->mobileField;
        $this->registerAssets();
        echo $this->renderInput();
    }

    /**
     * Renders the source input for the IconInput plugin.
     * Graceful fallback to a normal HTML  text input - in
     * case JQuery is not supported by the browser
     */
    protected function renderInput()
    {
        Html::addCssClass($this->options, 'form-control');
        $this->options['placeholder'] = $this->placeholder;
        $this->options['maxlength'] = $this->maxlength;
        $input = $this->getInput('textInput');
        $css = $this->disabled ? ' disabled' : '';
        Html::addCssClass($this->_container, 'input-group input-group-' . $this->size . $css);
        Html::addCssClass($this->_container, 'input-group' . $css);

        $title = Yii::t('common/user', 'Get Verify Code');
        $btnTrigger = Html::tag('button', $title, [
            'id' => $this->options['id'] . '-trigger',
            'class' => 'btn btn-default',
            'type' => 'button',
            'title' => $title,
            'data-mobile-field' => $this->mobileField,
            'data-form' => '#' . $this->form->options['id'],
        ]);

        $options = [];
        Html::addCssClass($options, 'input-group-btn');
        $btnTrigger = Html::tag('span', $btnTrigger, $options);

        Html::addCssClass($this->_container, 'icon');
        if ($this->type == self::TYPE_COMPONENT_APPEND) {
            $content = $input . $btnTrigger;
        } else {
            $content = $btnTrigger . $input;
        }
        return Html::tag('div', $content, $this->_container);
    }

    /**
     * Registers the needed client assets
     */
    public function registerAssets()
    {
        if ($this->disabled) {
            return;
        }
        $view = $this->getView();
        SmsVerifyAsset::register($view);

        $id = "jQuery('#" . $this->options['id'] . "')";
        $this->registerPlugin($this->pluginName, $id);
    }
}