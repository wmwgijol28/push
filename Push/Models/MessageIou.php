<?php
/**
 * Created by PhpStorm.
 * author: 田建昆
 * Date: 2018/2/24
 * Time: 10:23
 */

namespace Ucar\Push\Models;

use Illuminate\Database\Eloquent\Model;

class MessageIou extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.db_connection'));
        $this->setTable(config('push.table.message_iou'));
        parent::__construct();
    }
}