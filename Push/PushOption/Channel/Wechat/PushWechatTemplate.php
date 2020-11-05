<?php


namespace Yinyi\Push\PushOption\Channel\Wechat;


use Yinyi\Push\Jobs\WechatTemplateJob;
use Yinyi\Push\PushOption\Common\wechat;

class PushWechatTemplate implements PushWechatInterface
{
    use wechat;

    private $appid;

    private $openid;

    private $template;

    private $params;

    public function init($openid, $template, $params = [])
    {
        $this->appid = config('wechat.official_account.app_id');
        $this->openid = $openid;
        $this->template = $template;
        $this->params = $params;
        return $this;
    }

    public function handle()
    {
        $temp_id = $this->template['id'];
        $this->template = $this->replaceContent($this->template, $this->params);
        $params = json_decode($this->template, JSON_UNESCAPED_UNICODE);
        $params['touser'] = $this->openid;
        $params['mp_template_msg']['appid'] = $this->appid;

        $logId = $this->writeLog($this->openid, $temp_id, json_encode($params, JSON_UNESCAPED_UNICODE));
        dispatch(new WechatTemplateJob($params, $logId));
    }
}
