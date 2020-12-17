<?php
namespace Yinyi\Push\PushOption\Channel\App;

Interface PushAppInterface
{
    public function init($phone, $template, $params = []);

    public function handle();
}
