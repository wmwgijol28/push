<?php
namespace Yinyi\Push\PushOption\Channel\Sms;

Interface PushSmsInterface
{
    public function init($mobile, $template, $params = []);

    public function handle();
}
