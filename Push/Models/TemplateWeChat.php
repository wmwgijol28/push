<?php
/**
 * Created by PhpStorm.
 * author: 田建昆
 * Date: 2018/2/24
 * Time: 10:30
 */

namespace Yinyi\Push\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateWeChat extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.db_connection'));
        $this->setTable(config('push.table.template_wechat'));
        parent::__construct();
    }
}