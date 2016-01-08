<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 1/8/2016
 * Time: 7:59 PM
 */
namespace common\components\yii2song\pay;

interface PayInterface
{
    public function pay();

    public function callback();

}