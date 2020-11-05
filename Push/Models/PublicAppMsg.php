<?php


namespace Yinyi\Push\Models;


use Illuminate\Database\Eloquent\Model;

class PublicAppMsg extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.public'));
        $this->setTable(config('push.table.app_msg'));
        parent::__construct();
    }
}
