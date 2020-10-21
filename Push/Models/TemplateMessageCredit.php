<?php
/**
 * Created by PhpStorm.
 * author: 田建昆
 * Date: 2018/2/24
 * Time: 10:23
 */

namespace Ucar\Push\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateMessageCredit extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.db_connection'));
        $this->setTable(config('push.table.template_message_credit'));
        parent::__construct();
    }
}