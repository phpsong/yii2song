<?php
/**
 * Author: Andy Song <c3306@qq.com>
 */

namespace common\actions;

use common\models\base\ProvinceCityDistrict;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

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
class ProvinceCityDistrictAction extends Action
{
    /**
     * province, city, district
     * @var int
     */
    public $type = 'province';

    /**
     * Runs the action.
     */
    public function run()
    {
        $params = Yii::$app->request->post('depdrop_all_params');
        $parents = Yii::$app->request->post('depdrop_parents');

        $_output = [];
        if ($this->type == 'city') {
            $_output = ProvinceCityDistrict::find()->where([
                'level' => 2,
                'upid' => $params['province-id'],
            ])->all();
        } else if ($this->type == 'district') {

            $_output = ProvinceCityDistrict::find()->where([
                'level' => 3,
                'upid' => $params['city-id'],
            ])->all();
        }

        $_output = ArrayHelper::getArrayAttr($_output, ['id', 'name']);

        return Json::encode(['output'=>$_output, 'selected'=>'']);
    }
}
