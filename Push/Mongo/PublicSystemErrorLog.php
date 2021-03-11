<?php
namespace Yinyi\Push\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

class PublicSystemErrorLog extends Model
{
    protected $collection = 'public_system_error_log';
    protected $primaryKey = '_id';    //设置id

    function __construct(array $attributes = [])
    {
        $this->connection = confing('push.mongodb.connection');
        parent::__construct($attributes);
    }

    public static function writeLog($key, $to, $channel, $code, $msg)
    {
        $ins = new self();
        $ins->key = $key;
        $ins->moblie = $to;
        $ins->channel = $channel;
        $ins->error_code = $code;
        $ins->error_msg = $msg;
        $ins->created_at = date('Y-m-d H:i:s');
        $ins->updated_at = date('Y-m-d H:i:s');
        $ins->save();
    }
}
