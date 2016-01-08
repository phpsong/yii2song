<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 1/8/2016
 * Time: 6:09 PM
 */
namespace common\components\yii2song\uploadify;

/**
 * 七牛上传
 * @package common\components\yii2song\uploadify
 */
class QiniuUploadify extends Uploadify implements UploadifyInterface
{
    public $typeName = 'local';

    public function saveAs($file, $deleteTempFile = true)
    {
        // TODO: Implement saveAs() method.
    }

    /**
     * @param $path
     */
    public function fileList($path)
    {
        // TODO: Implement fileList() method.
    }

}