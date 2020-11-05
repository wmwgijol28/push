<?php
namespace Yinyi\Push\PushOption\sms;

Interface PushPhoneInterface
{
    public function init($mobile, $template, $params = []);

    public function handle();
}
