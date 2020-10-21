<?php
/**
 * Created by PhpStorm.
 * author: 田建昆
 * Date: 2018/3/2
 * Time: 14:31
 */

namespace Yinyi\Push\Models;

use Illuminate\Database\Eloquent\Model;

class PushWechat extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.db_connection'));
        $this->setTable(config('push.table.push_wechat'));
        parent::__construct();
    }
}