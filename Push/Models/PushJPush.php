<?php
/**
 * Created by PhpStorm.
 * author: 田建昆
 * Date: 2018/2/26
 * Time: 10:39
 */

namespace Ucar\Push\Models;

use Illuminate\Database\Eloquent\Model;

class PushJPush extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.db_connection'));
        $this->setTable(config('push.table.push_jpush'));
        parent::__construct();
    }
}