<?php


namespace Yinyi\Push\Models;


use Illuminate\Database\Eloquent\Model;

class PublicSmsTemplate extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.public'));
        $this->setTable(config('push.table.sms_template'));
        parent::__construct();
    }
}