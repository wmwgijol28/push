<?php


namespace Yinyi\Push\PushOption\Common;



use EasyWeChat\Factory;
use Yinyi\Push\Mongo\PublicWxLog;

trait wechat
{
    /**
     * 替换推送内容
     *
     * @param $content
     *
     * @return string
     */
    public function replaceContent($template, $params)
    {
        $keywords = explode(',', $template['keywords']);
        $content = $template['content'];
        foreach ($keywords as $keyword){
            $content = str_replace('{$'. $keyword. '}', $params[$keyword], $content);
        }
        return $content;
    }

    /**
     * 更新状态
     */
    private function updateLog($logId, $status, $rmk)
    {
        PublicWxLog::query()->where('_id', $logId)->update(['status' => $status, 'rmk' => $rmk]);
    }

    /**
     * 写日志
     */
    private function writeLog($openid, $tempId, $content)
    {
        $log = [
            'openid' => $openid,
            'template_id' => $tempId,
            'content' => $content,
            'status' => 0,
            'rmk' => '',
            'methot' => 'system',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        return PublicWxLog::query()->insertGetId($log);
    }
}
