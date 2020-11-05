<?php


namespace Yinyi\Push\Models;


use Illuminate\Database\Eloquent\Model;

class PublicPhoneTemplate extends Model
{
    public function __construct()
    {
        $this->setConnection(config('push.public'));
        $this->setTable(config('push.table.phone_template'));
        parent::__construct();
    }
}