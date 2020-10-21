<?php
/**
 * Created by PhpStorm.
 * User: feelop
 * Date: 2018/5/21
 * Time: 15:08
 */

namespace Yinyi\Push;

use Illuminate\Support\Facades\Cache;
use Yinyi\Push\Models\TemplateJiGuang;
use Yinyi\Push\Models\Templates;
use Yinyi\Push\Models\TemplateWeChat;

class PushInfo
{

    const TEMPLATES_SWITCH                          = 0b0000000001; // 消息总开关
    const TEMPLATES_WECHAT_SWITCH                   = 0b0000000010; // 微信
    const TEMPLATES_YOUYI_SWITCH                    = 0b0000000100; // 有易
    const TEMPLATES_ALIDAYU_SWITCH                  = 0b0000001000; // 阿里大鱼
    const TEMPLATES_JIGUANG_SWITCH                  = 0b0000010000; // 极光
    const TEMPLATES_MESSAGE_CONSUMPTION_SWITCH      = 0b0000100000; // 站内信 消费
    const TEMPLATES_MESSAGE_CREDIT_SWITCH           = 0b0001000000; // 站内信 小优信用
    const TEMPLATES_MESSAGE_IOU_SWITCH              = 0b0010000000; // 站内信 白条
    const TEMPLATES_MESSAGE_SERVE_INFORMS_SWITCH    = 0b0100000000; // 站内信 服务
    const TEMPLATES_YUNPIAN_SWITCH                  = 0b1000000000; // 内容短信


    /**
     * false则缓存，true则不缓存
     * @var bool
     */
    private $notCache = false;

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
     * 推送关联的链接 (极光)
     * @var string
     */
    private $url;

    /**
     * 接收人
     * @var array
     */
    private $to;

    /**
     * 模板
     * @var int
     */
    private $templates;

    private $errors;

    private $sendResult;

    /**
     *
     * @param $key   string 消息推送时使用的key
     * @param $to    array user_id、mobile、registration_id（极光推送客户端id）、wechat_open_id（微信open_id）
     * @param $param array 替换推送内容所用到的数组
     * @param $url   string 可能为空
     */
    public function __construct($key, $to, $param, $url)
    {
        $this->key = $key;
        $this->to = $to;
        $this->param = $param;
        $this->url = $url;

//        $this->notCache = config('push.noCache');
        $this->notCache = true;
        $this->templates = $this->getTemplates();
    }

    /**
     * 是否允许推送
     * @return bool
     */
    public function canPush()
    {
        $template = $this->templates;

        if ( ! $template) {
            $this->setErrors(PushCode::EMPTY_DATA, '不存在此模板');

            return false;
        }

        if ( ! ($template->getStatus() & self::TEMPLATES_SWITCH)) {
            $this->setErrors(PushCode::TEMPLATE_DISABLED, '模板未启用');

            return false;
        }

        //检测推送关键词是否存在
        $isExists = $this->keywordsExists($template->getKeywords());
        if ( ! $isExists) {
            $this->setErrors(PushCode::NOT_FIND_KEYWORD, '存在未传递的keyword');

            return false;
        }

        return true;
    }

    /**
     * 检测keywords中的变量是否全部存在于传递的param数组中
     *
     * @param $keywords
     *
     * @return bool
     */
    private function keywordsExists($keywords)
    {
        if ( ! $keywords) {
            return true;
        }
        $keywordsArray = explode(',', $keywords);
        //检测需要替换的keywords是否全部存在于$param数组中
        for ($i = 0; $i < count($keywordsArray); $i++) {
            $words = ltrim($keywordsArray[$i], '$');
            if ( ! array_key_exists($words, $this->param)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 是否允许极光推送
     * @return bool
     */
    public function canPushToJiGuang()
    {
        $template = $this->templates;

        if ( ! $this->getRegistrationId()) {
            $this->setErrors(PushCode::REGISTRATION_ID_MISSING, '极光id不存在');

            return false;
        }

        if ( ! ($template->getStatus() & self::TEMPLATES_JIGUANG_SWITCH)) {
            $this->setErrors(PushCode::TEMPLATE_DISABLED, '极光模板未启用');

            return false;
        }

        return true;
    }

    /**
     * 是否允许阿里大于推送
     * @return bool
     */
    public function canPushToALiDaYu()
    {
        $template = $this->templates;

        if ( ! $this->getMobile()) {
            $this->setErrors(PushCode::MOBILE_MISSING, '手机号不存在');

            return false;
        }

        if ( ! ($template->getStatus() & self::TEMPLATES_ALIDAYU_SWITCH)) {
            $this->setErrors(PushCode::TEMPLATE_DISABLED, '阿里大于模板未启用');

            return false;
        }

        return true;
    }

    /**
     * 是否允许内容短信推送
     * @return bool
     */
    public function canPushToYunPian()
    {
        $template = $this->templates;

        if ( ! $this->getMobile()) {
            $this->setErrors(PushCode::MOBILE_MISSING, '手机号不存在');

            return false;
        }

        if ( ! ($template->getStatus() & self::TEMPLATES_YUNPIAN_SWITCH)) {
            $this->setErrors(PushCode::TEMPLATE_DISABLED, '内容短信模板未启用');

            return false;
        }

        return true;
    }

    /**
     * 是否允许有易推送
     * @return bool
     */
    public function canPushToYouYi()
    {
        $template = $this->templates;

        if ( ! $this->getMobile()) {
            $this->setErrors(PushCode::MOBILE_MISSING, '手机号不存在');

            return false;
        }

        if ( ! ($template->getStatus() & self::TEMPLATES_YOUYI_SWITCH)) {
            $this->setErrors(PushCode::TEMPLATE_DISABLED, '有易模板未启用');

            return false;
        }

        return true;
    }

    /**
     * 是否允许微信推送
     * @return bool
     */
    public function canPushToWeChat()
    {
        $template = $this->templates;

        if ( ! $this->getWeChatOpenId()) {
            $this->setErrors(PushCode::WECHAT_OPEN_ID_MISSING, '微信OPENID不存在');

            return false;
        }

        if ( ! ($template->getStatus() & self::TEMPLATES_WECHAT_SWITCH)) {
            $this->setErrors(PushCode::TEMPLATE_DISABLED, '微信模板未启用');

            return false;
        }

        return true;
    }

    /**
     * 是否允许站内信推送
     * @return bool
     */
    public function CanPushToMessageConsumption()
    {
        $template = $this->templates;

        if ( ! $this->getUserId()) {
            $this->setErrors(PushCode::WECHAT_OPEN_ID_MISSING, 'USER_ID不存在');

            return false;
        }

        if ( ! ($template->getStatus() & self::TEMPLATES_MESSAGE_CONSUMPTION_SWITCH)) {
            $this->setErrors(PushCode::TEMPLATE_DISABLED, '站内信-消费模板未启用');

            return false;
        }

        return true;
    }

    /**
     * 是否允许站内信推送
     * @return bool
     */
    public function CanPushToMessageCredit()
    {
        $template = $this->templates;

        if ( ! $this->getUserId()) {
            $this->setErrors(PushCode::WECHAT_OPEN_ID_MISSING, 'USER_ID不存在');

            return false;
        }

        if ( ! ($template->getStatus() & self::TEMPLATES_MESSAGE_CREDIT_SWITCH)) {
            $this->setErrors(PushCode::TEMPLATE_DISABLED, '站内信-信用率模板未启用');

            return false;
        }

        return true;
    }

    /**
     * 是否允许站内信推送
     * @return bool
     */
    public function CanPushToMessageIou()
    {
        $template = $this->templates;

        if ( ! $this->getUserId()) {
            $this->setErrors(PushCode::WECHAT_OPEN_ID_MISSING, 'USER_ID不存在');

            return false;
        }

        if ( ! ($template->getStatus() & self::TEMPLATES_MESSAGE_IOU_SWITCH)) {
            $this->setErrors(PushCode::TEMPLATE_DISABLED, '站内信-白条模板未启用');

            return false;
        }

        return true;
    }

    /**
     * 是否允许站内信推送
     * @return bool
     */
    public function CanPushToMessageServeInforms()
    {
        $template = $this->templates;

        if ( ! $this->getUserId()) {
            $this->setErrors(PushCode::WECHAT_OPEN_ID_MISSING, 'USER_ID不存在');

            return false;
        }

        if ( ! ($template->getStatus() & self::TEMPLATES_MESSAGE_SERVE_INFORMS_SWITCH)) {
            $this->setErrors(PushCode::TEMPLATE_DISABLED, '站内信-服务模板未启用');

            return false;
        }

        return true;
    }

    /**
     * 获取推送模板
     * @return Templates|null
     */
    private function getTemplates()
    {
        $key = $this->key;

        if ($this->notCache) {
            return Templates::where('key', $key)
                ->select('id', 'key', 'status', 'title', 'keywords')
                ->first();
        }
        $templates = Cache::tags(['pushMessage', $key])
            ->rememberForever('Template:Key:' . $key, function () use ($key) {
                return Templates::where('key', $key)
                    ->select('id', 'key', 'status', 'title', 'keywords')
                    ->first();
            });

        return $templates;
    }


    //极光模板
    public function getTemplateJiGuang()
    {
        $templatesId = $this->templates->getId();

        if ($this->notCache) {
            return TemplateJiGuang::where('templates_id', $templatesId)
                ->first();
        }
        $templateJiGuang = Cache::tags(['pushMessage', $this->key])
            ->rememberForever('Template:JPush:ID:' . $templatesId, function () use ($templatesId) {
                return TemplateJiGuang::where('templates_id', $templatesId)
                    ->first();
            });

        return $templateJiGuang;
    }

    //微信模板
    public function getTemplateWeChat()
    {
        $templatesId = $this->templates->getId();

        if ($this->notCache) {
            return TemplateWeChat::where('templates_id', $templatesId)
                ->first();
        }
        $templateWeChat = Cache::tags(['pushMessage', $this->key])
            ->rememberForever('Template:WeChatPush:ID:' . $templatesId, function () use ($templatesId) {
                return TemplateWeChat::where('templates_id', $templatesId)
                    ->first();
            });

        return $templateWeChat;
    }


    private function setErrors($code, $msg)
    {
        $this->errors[] = [$code, $msg];
    }

    public function setResult($tag, $result)
    {
        $this->sendResult[] = [$tag, $result];
    }

    public function getResult()
    {
        return $this->sendResult;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return array
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url ?? '';
    }

    /**
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return string|null
     */
    public function getRegistrationId()
    {
        return $this->to['registration_id'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getUserId()
    {
        return $this->to['user_id'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getMobile()
    {
        return $this->to['mobile'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getWeChatOpenId()
    {
        return $this->to['wechat_open_id'] ?? null;
    }

    /**
     * @return int|null|Templates
     */
    public function getTemplate()
    {
        return $this->templates;
    }

}