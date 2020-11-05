<?php


namespace Yinyi\Push\PushOption\Common;


use Yinyi\Push\Mongo\PublicSmsLog;

trait sms
{
    /**
     * 更新状态
     */
    private function updateLog($logId, $status, $rmk)
    {
        PublicSmsLog::query()->where('_id', $logId)->update(['status' => $status, 'rmk' => $rmk]);
    }

    /**
     * 写日志
     */
    private function writeLog($content)
    {
        $log = [
            'phone' => $this->mobile,
            'template_id' => $this->template['id'],
            'content' => $content,
            'status' => 0,
            'rmk' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return PublicSmsLog::query()->insertGetId($log);
    }
}
