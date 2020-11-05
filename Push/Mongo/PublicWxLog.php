<?php
namespace Yinyi\Push\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

class PublicWxLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'public_wx_log';
    protected $primaryKey = '_id';    //设置id

}
