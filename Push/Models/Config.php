<?php


namespace Yinyi\Push\Models;


use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.public'));
        $this->setTable(config('push.table.config'));

        parent::__construct();
        
    }
}
