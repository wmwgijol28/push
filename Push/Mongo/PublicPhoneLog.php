<?php
namespace Yinyi\Push\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

class PublicPhoneLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'public_phone_log';
    protected $primaryKey = '_id';    //设置id

}
