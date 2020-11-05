<?php


namespace Yinyi\Push\Models;


use Illuminate\Database\Eloquent\Model;

class PublicWxTemplate extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.public'));
        $this->setTable(config('push.table.wx_template'));
        parent::__construct();
    }
}