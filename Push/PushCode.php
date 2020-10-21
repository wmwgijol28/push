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
    const EMPTY_DATA = '4001'; //模板为空
    const TEMPLATE_DISABLED = '4002'; //模板未启用
    const TEMPLATE_JIGUANG_DISABLED = '4003'; //模板未启用
    const TEMPLATE_WECHAT_DISABLED = '4004'; //模板未启用
    const TEMPLATE_YOUYI_DISABLED = '4005'; //模板未启用
    const TEMPLATE_ALIDAYU_DISABLED = '4006'; //模板未启用
    const NOT_FIND_KEYWORD = '4007'; //存在未传递的keyword
    const REGISTRATION_ID_MISSING = '4008'; //极光id不存在
    const MOBILE_MISSING = '4008'; //手机号不存在
    const WECHAT_OPEN_ID_MISSING = '4009'; //微信OPENID不存在
    const USER_ID_MISSING = '4010'; //USER_ID不存在

}