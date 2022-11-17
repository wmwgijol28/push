<?php


namespace Yinyi\Push\PushOption;


use Yinyi\Push\PushOption\Channel\App\ActiveMsgSend;
use Yinyi\Push\PushOption\Channel\App\InterMsgSend;
use Yinyi\Push\PushOption\Channel\App\SystemMsgSend;
use Yinyi\Push\PushOption\Channel\Phone\JpushPhoneMsgSend;
use Yinyi\Push\PushOption\Channel\Sms\JPushOneTemplateSend;
use Yinyi\Push\PushOption\Channel\Wechat\PushWechatTemplate;

class Kernel
{
    const SMS_JPUSH_ONE_TEMP_SEND = 1;  //极光单次模板短信发送

    const WX_SYSTEM_MSG_SEND = 1;       //微信信息通知

    const APP_ACTIVE_MSG_SEND = 1;      //app优惠活动通知
    const APP_SYSTEM_MSG_SEND = 2;      //app系统消息通知
    const APP_INTER_MSG_SEND = 3;      //app互动消息通知

    const PHONE_JPUSH_MSG_SEND = 1;      //app互动消息通知


    public static $Jobs = [
        'sms' => [
            'jpush' => [
                self::SMS_JPUSH_ONE_TEMP_SEND => JPushOneTemplateSend::class,
            ]
        ],
        'wx' => [
            'wx' => [
                self::WX_SYSTEM_MSG_SEND => PushWechatTemplate::class
            ]
        ],
        'app' => [
            'tym' => [
                self::APP_ACTIVE_MSG_SEND => ActiveMsgSend::class,
                self::APP_SYSTEM_MSG_SEND => SystemMsgSend::class,
                self::APP_INTER_MSG_SEND => InterMsgSend::class,
            ]
        ],
        'phone' => [
            'jpush' => [
                self::PHONE_JPUSH_MSG_SEND => JpushPhoneMsgSend::class
            ]
        ],
    ];
}
