<?php
/**
 * Created by PhpStorm.
 * author: 田建昆
 * Date: 2018/2/23
 * Time: 17:49
 */

namespace Yinyi\Push\Models;

use Illuminate\Database\Eloquent\Model;

class Templates extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.db_connection'));
        $this->setTable(config('push.table.templates'));
        parent::__construct();
    }

    public function getId()
    {
        return $this->id ?? null;
    }

    public function getStatus()
    {
        return $this->status ?? 0;
    }

    public function getKeywords()
    {
        return $this->keywords ?? null;
    }
}