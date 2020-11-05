<?php


namespace Yinyi\Push\Models;


use Illuminate\Database\Eloquent\Model;

class PublicMessageConfig extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.public'));
        $this->setTable(config('push.table.message_config'));
        parent::__construct();
    }
}