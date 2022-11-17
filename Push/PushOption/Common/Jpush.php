<?php
namespace Yinyi\Push\PushOption\Common;

use JPush\Client;
use Yinyi\Push\Mongo\PublicPhoneLog;


trait Jpush
{
    private  $client;

    private function init()
    {
        $config = config('push.jpush');
        $this->client = new Client($config['app_key'], $config['master_secret']);
    }

    /**
     * 更新状态
     */
    private function updateLog($logId, $status, $rmk)
    {
        PublicPhoneLog::query()->where('_id', $logId)->update(['status' => $status, 'rmk' => $rmk]);
    }

    /**
     * 写日志
     */
    private function writeLog($phone, $tempId, $content)
    {
        $log = [
            'phone' => $phone,
            'template_id' => $tempId,
            'content' => $content,
            'status' => 0,
            'rmk' => '',
            'methot' => 'system',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return PublicPhoneLog::query()->insertGetId($log);
    }

}
