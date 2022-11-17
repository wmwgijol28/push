<?php


namespace Yinyi\Push\PushOption;


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
