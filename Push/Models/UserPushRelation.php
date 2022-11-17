<?php


namespace Yinyi\Push\Models;


use Illuminate\Database\Eloquent\Model;

class UserPushRelation extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.public'));
        $this->setTable(config('push.table.user_push_relation'));
        parent::__construct();
    }
}
