<?php
/**
 * @link http://www.i3a.com.cn/
 * @copyright Copyright (c) 2015 i3A Tech LLC
 * @date 2015-11-21 15:53
 */
namespace common\behaviors;

use Yii;
use yii\base\Event;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

/**
 * CorporateBehavior automatically fills the specified attributes with the current user's Company ID
 *
 * To use CorporateBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use common\behaviors\CorporateBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         CorporateBehavior::className(),
 *     ];
 * }
 * ```
 *
 * By default, CorporateBehavior will fill the `company_id` attribute with the current user's Company ID
 * when the associated AR object is being inserted; If your attribute names are different, you may configure
 * the [[companyIdAttribute]] properties like the following:
 *
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => CorporateBehavior::className(),
 *             'companyIdAttribute' => 'company_id',
 *         ],
 *     ];
 * }
 * ```
 *
 * @author AndySong <c3306@qq.com>
 * @date 2015-11-21 15:53
 * @since 2.0
 */
class CorporateBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive current user's Company ID value
     * Set this property to false if you do not want to record the user's Company ID.
     */
    public $companyIdAttribute = 'company_id';
    /**
     * @var callable the value that will be assigned to the attributes. This should be a valid
     * PHP callable whose return value will be assigned to the current attribute(s).
     * The signature of the callable should be:
     *
     * ```php
     * function ($event) {
     *     // return value will be assigned to the attribute(s)
     * }
     * ```
     *
     * If this property is not set, the value of `Yii::$app->user->company_id` will be assigned to the attribute(s).
     */
    public $value;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->companyIdAttribute,
            ];
        }
    }

    /**
     * Evaluates the value of the user.
     * The return result of this method will be assigned to the current attribute(s).
     * @param Event $event
     * @return mixed the value of the user.
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            $user = Yii::$app->get('user', false);
            return $user && !$user->isGuest ? $user->id : null;
        } else {
            return call_user_func($this->value, $event);
        }
    }
}
