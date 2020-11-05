<?php
namespace Yinyi\Push\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

class PublicSmsLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'public_sms_log';
    protected $primaryKey = '_id';    //设置id

}
