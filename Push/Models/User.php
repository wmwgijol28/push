<?php


namespace Yinyi\Push\Models;


use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.public'));
        $this->setTable(config('push.table.user'));
        parent::__construct();
    }

    public function oauth()
    {
        return $this->hasOne(UserOauth::class, 'uid', 'uid');
    }
}