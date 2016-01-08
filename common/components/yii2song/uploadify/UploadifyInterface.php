<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 1/8/2016
 * Time: 5:46 PM
 */
namespace common\components\yii2song\uploadify;

interface UploadifyInterface {

    public function saveAs($file, $deleteTempFile = true);

    public function fileList($path);
}