<?php
namespace Yinyi\Push\PushOption\Channel\Phone;

Interface PushPhoneInterface
{
    public function init($mobile, $template, $params = []);

    public function handle();
}
