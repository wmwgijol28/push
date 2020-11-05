<?php
namespace Yinyi\Push\PushOption\Channel\Wechat;

Interface PushWechatInterface
{
    public function init($openid, $template, $params = []);

    public function handle();
}
