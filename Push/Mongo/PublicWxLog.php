<?php
namespace Yinyi\Push\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

class PublicWxLog extends Model
{
    protected $collection = 'public_wx_log';
    protected $primaryKey = '_id';    //设置id

    function __construct(array $attributes = [])
    {
        $this->connection = confing('push.mongodb.connection');
        parent::__construct($attributes);
    }

}
