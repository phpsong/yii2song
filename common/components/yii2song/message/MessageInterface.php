<?php
/**
 * Created by PhpStorm.
 * User: AndySong
 * Date: 2015-11-25
 * Time: 8:55
 */
namespace common\components\message;

interface MessageInterface {

    /**
     * @param $receiver
     * @param $message
     * @param string $code
     * @return mixed
     */
    public function send($receiver, $message, $code = '');
}