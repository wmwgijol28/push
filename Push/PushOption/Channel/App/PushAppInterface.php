<?php
namespace Yinyi\Push\PushOption\Channel\App;

Interface PushAppInterface
{
    public function init($uid, $template, $params = []);

    public function handle();
}
