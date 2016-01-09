<?php
/**
 * Created by PhpStorm.
 * User: AndySong
 * Date: 2015-11-18
 * Time: 17:07
 */
namespace common\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/fontawesome';
    public $css = [
        'css/font-awesome.css',
    ];
}