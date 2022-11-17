<?php
/**
 * Created by PhpStorm.
 * User: feelop
 * Date: 2018/5/21
 * Time: 15:08
 */

namespace Yinyi\Push;

use App\Models\User\User;
use App\Models\User\UserOauth;
use App\Repositories\User\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Yinyi\Push\Models\PublicAppTemplate;
use Yinyi\Push\Models\PublicPhoneTemplate;
use Yinyi\Push\Models\PublicSmsTemplate;
use Yinyi\Push\Models\PublicWxTemplate;
use Yinyi\Push\Models\PublicMessageConfig;
use Yinyi\Push\Models\TemplateJiGuang;
use Yinyi\Push\Models\Templates;
use Yinyi\Push\Models\TemplateWeChat;
use Yinyi\Push\Mongo\PublicSystemErrorLog;

class PushInfo
{
    /**
     * false则缓存，true则不缓存
     * @var bool
     */
    private $notCache = false;

    private $tag;

    private $model = [
        'sms' => PublicSmsTemplate::class,
        'wx' => PublicWxTemplate::class,
        'phone' => PublicPhoneTemplate::class,
        'app' => PublicAppTemplate::class
    ];

    /**
     * 推送类型
     * @var string
     */
    private $key;

    /**
     * 推送参数
     * @var string
     */
    private $param;

    /**
     * 接收人
     * @var array
     */
    private $to;

    private $config;

    private $template;

    private $user = null;

    private $errors;

    private $sendResult;

    /**
     *
     * @param $key   string 消息推送时使用的key
     * @param $to    string user_id、mobile、registration_id（极光推送客户端id）、wechat_open_id（微信open_id）
     * @param $param array 替换推送内容所用到的数组
     */
    public function __construct($key, $to, $param)
    {
        $this->key = $key;
        $this->to = $to;
        $this->param = $param;
        $this->notCache = config('push.noCache');
        $this->tag = ['messageConfig', $key];

        $this->config = $this->getMessageConfig();
    }

    /**
     * 获取对应的配置
     * @return array|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    private function getMessageConfig()
    {
        if($this->notCache){
            return PublicMessageConfig::query()->where('msg_key', $this->key)->first();
        }
        return Cache::tags($this->tag)
            ->rememberForever('message:config:key:'. $this->key, function () {
                return PublicMessageConfig::query()->where('msg_key', $this->key)->first();
            });
    }

    public function getChannel()
    {
        return array_keys($this->channels);
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getTemplateType()
    {
        return $this->template['type'] ?? null;
    }


    /**
     * 是否允许推送
     * @return bool
     */
    public function canPush()
    {
        if ( ! $this->config) {
            $this->setErrors('all', PushCode::EMPTY_DATA, '不存在此模板');
            return false;
        }
        if($this->config['status'] != 1){
            $this->setErrors('all', PushCode::CONFIG_DISABLED, '配置未开启发送');
            return false;
        }

        return true;
    }

    public function canPushToSms()
    {
        $config = $this->config;
        if(!$config['is_sms']){
            return false;
        }
        $template = $this->setTemplate('sms');
        if(empty($template)){
            $this->setErrors('sms', PushCode::TEMPLATE_MISSING, '未发现对应的短信模板');
            return false;
        }
        $template = $template->toArray();

        if(!$this->checkKeywords($template)){
            $this->setErrors('app', PushCode::NOT_FIND_KEYWORD, '缺少keyword参数');
            return false;
        }

        $this->template = $template;
        return true;
    }


    public function canPushToApp()
    {
        $config = $this->config;
        if(!$config['is_app']){
            return false;
        }

        $template = $this->setTemplate('app');
        if(empty($template)){
            $this->setErrors('app', PushCode::TEMPLATE_MISSING, '未发现对应的app模板');
            return false;
        }
        $template = $template->toArray();

        if(!$this->checkKeywords($template)){
            $this->setErrors('app', PushCode::NOT_FIND_KEYWORD, '缺少keyword参数');
            return false;
        }

        $this->template = $template;
        return true;
    }


    public function canPushToWx()
    {
        $config = $this->config;
        if(!$config['is_wx']){
            return false;
        }

        $template = $this->setTemplate('wx');
        if(empty($template)){
            $this->setErrors('wx', PushCode::TEMPLATE_MISSING, '未发现对应的微信模板');
            return false;
        }
        $template = $template->toArray();

        if(!$this->checkKeywords($template)){
            $this->setErrors('wx', PushCode::NOT_FIND_KEYWORD, '缺少keyword参数');
            return false;
        }

        $this->template = $template;
        return true;
    }


    public function canPushToPhone()
    {
        $config = $this->config;
        if(!$config['is_phone']){
            return false;
        }

        $template = $this->setTemplate('phone');
        if(empty($template)){
            $this->setErrors('phone', PushCode::TEMPLATE_MISSING, '未发现对应的推送模板');
            return false;
        }
        $template = $template->toArray();

        if(!$this->checkKeywords($template)){
            $this->setErrors('phone', PushCode::NOT_FIND_KEYWORD, '缺少keyword参数');
            return false;
        }

        $this->template = $template;
        return true;
    }


    public function getErrors()
    {
        return $this->errors;
    }


    private function checkKeywords($template)
    {
        $tem_keywords = array_filter(explode(',', $template['keywords']));
        $params = array_keys($this->param);
        foreach ($tem_keywords as $value){
            if(!in_array($value, $params)){
                return false;
            }
        }
        return true;
    }


    private function setTemplate($channel)
    {
        $templateId = $this->config['id'];
        $model = $this->model[$channel];
        if ($this->notCache) {
            return $model::query()->where('config_id', $templateId)->first();
        }

        $key = 'template:'. $channel. ':id:'. $templateId;
        return Cache::tags($this->tag)
            ->rememberForever($key, function () use ($templateId, $model) {
                return $model::query()->where('config_id', $templateId)->first();
            });
    }


    private function setErrors($channel, $code, $msg)
    {
        PublicSystemErrorLog::writeLog($this->key, $this->to, $channel, $code, $msg);
    }

}
