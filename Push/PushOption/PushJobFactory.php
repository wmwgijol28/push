<?php


namespace Yinyi\Push\PushOption;


use Yinyi\Push\PushOption\Channel\Sms\Jpush\JPushOneTemplateSend;

class PushJobFactory
{
    public static function createOption($channel, $method, $type)
    {
        if(!isset(Kernel::$Jobs[$channel][$method][$type])){
            return false;
        }
        return new Kernel::$Jobs[$channel][$method][$type];
    }
}