<?php
/**
 * Created by PhpStorm.
 * author: 田建昆
 * Date: 2018/2/23
 * Time: 17:55
 */

namespace Yinyi\Push;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Yinyi\Push\Jobs\JPush;
use Yinyi\Push\Jobs\WechatPush;
use Yinyi\Push\Jobs\YunPianPush;

class Push
{
    /**
     * 推送信息
     * @var PushInfo
     */
    private $info;

    /**
     * 推送主方法 推送入口
     *
     * @param $key   string 消息推送时使用的key
     * @param $to    array user_id、mobile、registration_id（极光推送客户端id）、wechat_open_id（微信open_id）
     * @param $param array 替换推送内容所用到的数组
     * @param $url   string 可能为空
     *
     * @return object|PushInfo
     */
    public function push($key, $to, $param, $url = null)
    {
        $this->info = new PushInfo($key, $to, $param, $url);

        if ( ! $this->info->canPush()) {
            return $this->info;
        }

        if ($this->info->canPushToJiGuang()) {
            $this->JiGuangPush();
        }

        if ($this->info->canPushToYunPian()) {
            $this->YunPianPush();
        }

        if ($this->info->canPushToWeChat()) {
            $this->WeChatPush();
        }

        $this->writeLog();

        return $this->info;

    }

    public function send($key, $to, $param, $url = null)
    {
        return $this->push($key, $to, $param, $url);
    }


    /**
     * 极光推送
     */
    private function JiGuangPush()
    {
        $templateJiGuang = $this->info->getTemplateJiGuang();

        if ($templateJiGuang) {
            $content = $this->replaceContent($templateJiGuang->content);
            $send = [
                'type' => $this->info->getKey(),
                'content' => $content,
                'url' => $this->info->getUrl() ?: $templateJiGuang->url ?: '',
                'registration_id' => $this->info->getRegistrationId(),
                'user_id' => $this->info->getUserId(),
                'push_type' => $templateJiGuang->push_type,
            ];
            dispatch(new JPush($send));
            $this->info->setResult('JiGuang', $send);
        }
    }


    /**
     * 云片网推送
     */
    private function YunPianPush()
    {
        $templateContentSms = $this->info->getTemplateContentSms();
        if ($templateContentSms) {
            $content = $this->replaceContent($templateContentSms->content);
            $send = [
                'user_id' => $this->info->getUserId(),
                'mobile' => $this->info->getMobile(),
                'type' => $this->info->getKey(),
                'content' => $content,
            ];
            dispatch(new YunPianPush($send));
            $this->info->setResult('YunPian', $send);
        }
    }

    /**
     * 微信推送
     */
    private function WeChatPush()
    {
        $templateWeChat = $this->info->getTemplateWeChat();

        if ($templateWeChat) {
            $content = $this->replaceContent($templateWeChat->content);
            $body = $this->arrangeWeChatBody($content, $templateWeChat->template_key);
            $send = [
                'user_id' => $this->info->getUserId(),
                'body' => $body,
                'push_type' => $this->info->getKey(),
            ];
            dispatch(new WechatPush($send));

            $this->info->setResult('WeChat', $send);
        }
    }


    /**
     * 替换推送内容
     *
     * @param $content
     *
     * @return string
     */
    private function replaceContent($content)
    {
        $param = $this->info->getParam();
        $keysArray = array_keys($param);
        $old = [];
        $new = [];
        for ($i = 0; $i < count($keysArray); $i++) {
            $old[$i] = '{$' . $keysArray[$i] . '}';
            $new[$i] = $param[$keysArray[$i]];
        }
        $old[] = '{$now}';
        $new[] = date('Y-m-d H:i:s');
        $content = str_replace($old, $new, $content);

        return $content;
    }

    /**
     * 整理微信需要的body数据
     *
     * @param $content
     * @param $template_id
     *
     * @return array
     */
    private function arrangeWeChatBody($content, $template_id)
    {
        return [
            'touser' => $this->info->getWeChatOpenId(),
            'template_id' => $template_id,
            'url' => $this->info->getUrl(),
            'data' => json_decode($content, true),
        ];
    }

    /**
     * 根据key清除相应的推送模板的缓存
     *
     * @param $key string templates数据库中的key
     */
    static public function clearCacheByKey($key)
    {
        Cache::tags($key)
            ->flush();
    }

    /**
     * 清除所有推送模板的缓存
     */
    static public function clearAllCache()
    {
        Cache::tags('pushMessage')
            ->flush();
    }

    /**
     * 写日志
     */
    private function writeLog()
    {
        if (config('push.push_debug')) {
            Log::driver('push')
                ->notice('key');
            Log::driver('push')
                ->notice($this->info->getKey());
            Log::driver('push')
                ->notice('param');
            Log::driver('push')
                ->notice($this->info->getParam());
            Log::driver('push')
                ->notice('to');
            Log::driver('push')
                ->notice($this->info->getTo());
            Log::driver('push')
                ->notice('template');
            Log::driver('push')
                ->notice($this->info->getTemplate()
                    ->getAttributes());
            Log::driver('push')
                ->notice('result');
            Log::driver('push')
                ->notice($this->info->getResult());
            Log::driver('push')
                ->notice('error');
            Log::driver('push')
                ->notice($this->info->getErrors());
        }
    }
}