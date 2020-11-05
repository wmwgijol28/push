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
use Yinyi\Push\Jobs\PushMessageJob;
use Yinyi\Push\Jobs\WechatPush;
use Yinyi\Push\Jobs\YunPianPush;
use Yinyi\Push\PushOption\PushJobFactory;

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
     * @param $to    string
     * @param $param array 替换推送内容所用到的数组
     * @param $url   string 可能为空
     *
     * @return object|PushInfo
     */
    public function push($key, $to, $param = [])
    {
        $this->info = new PushInfo($key, $to, $param);

        if ( ! $this->info->canPush()) {
            return $this->info;
        }

        if($this->info->canPushToSms()){
            PushJobFactory::createOption('sms', 'jpush', $this->info->getTemplateType())
                ->init($to, $this->info->getTemplate(), $param)
                ->handle();
        }
        if($this->info->canPushToApp()){
            PushJobFactory::createOption('app', 'tym', $this->info->getTemplateType())
            ->init($this->info->getUid(), $this->info->getTemplate(), $param)
            ->handle();
        }

        if($this->info->canPushTowx()){
            PushJobFactory::createOption('wx', 'wx', $this->info->getTemplateType())
                ->init($this->info->getMiniOpenid(), $this->info->getTemplate(), $param)
                ->handle();
        }

        return true;

    }

    /**
     * 清除所有推送模板的缓存
     */
    static public function clearAllCache()
    {
        Cache::tags('messageConfig')
            ->flush();
    }
}
