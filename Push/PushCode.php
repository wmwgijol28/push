<?php
/**
 * Created by PhpStorm.
 * User: feelop
 * Date: 2018/5/21
 * Time: 16:12
 */

namespace Yinyi\Push;

interface PushCode
{
    const EMPTY_DATA = '4001'; //配置为空
    const CONFIG_DISABLED = '4002'; //配置未开启发送
    const TEMPLATE_MISSING = '4003'; //无对应模板
    const NOT_FIND_KEYWORD = '4007'; //存在未传递的keyword
    const REGISTRATION_ID_MISSING = '4008'; //极光id不存在
    const MOBILE_MISSING = '4008'; //手机号不存在
    const WECHAT_OPEN_ID_MISSING = '4009'; //微信OPENID不存在
    const USER_ID_MISSING = '4010'; //USER_ID不存在

    const SEND_FAILD = '5001'; //发送失败

}
