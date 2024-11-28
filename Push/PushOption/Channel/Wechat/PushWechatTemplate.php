<?php


namespace Yinyi\Push\PushOption\Channel\Wechat;


use Yinyi\Push\Jobs\WechatTemplateJob;
use Yinyi\Push\PushOption\Common\wechat;

class PushWechatTemplate implements PushWechatInterface
{
    use wechat;

    private $appid;

    private $phone;

    private $template;

    private $params;

    private $urlParams;

    public function init($phone, $template, $params = [], $urlParams = [])
    {
        $this->appid = getConfig('wx_mp')['appid'];
        $this->phone = $phone;
        $this->template = $template;
        $this->params = $params;
        $this->urlParams = $urlParams;
        return $this;
    }

    public function handle()
    {
        $temp_id = $this->template['id'];
        $params['appid'] = $this->appid;
        $params['template_id'] = $this->template['template_id'];
        $this->setUrlParams($params);
        $params['data'] = json_decode($this->replaceContent($this->template, $this->params), JSON_UNESCAPED_UNICODE);;

        $logId = $this->writeLog($this->phone, $temp_id, json_encode($params, JSON_UNESCAPED_UNICODE));
        dispatch(new WechatTemplateJob($this->phone, $params, $logId));
    }


    private function setUrlParams(&$params)
    {
        if($this->template['url_type'] == 1){
            $params['miniprogram']['appid'] = getConfig('mini_app')['appid'];
            $params['miniprogram']['path'] = $this->setUrl();
        }
        if($this->template['url_type'] == 2){
            $params['url'] = $this->template['url'];
        }
    }


    private function setUrl()
    {
        if(!$this->urlParams){
            return $this->template['url'];
        }
        return $this->template['url']. '?'. join('&', configArray($this->urlParams));
    }
}
