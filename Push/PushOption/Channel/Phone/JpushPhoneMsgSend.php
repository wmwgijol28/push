<?php


namespace Yinyi\Push\PushOption\Channel\Phone;


use Yinyi\Push\Jobs\PhoneTemplateJob;
use Yinyi\Push\Models\User;
use Yinyi\Push\Models\UserPushRelation;
use Yinyi\Push\PushOption\Common\Jpush;

class JpushPhoneMsgSend implements PushPhoneInterface
{
    use Jpush;

    private $phone;

    private $template;

    private $params;

    private $urlParams;

    public function init($phone, $template, $params = [], $urlParams = [])
    {
        $this->phone = $phone;
        $this->template = $template;
        $this->params = $params;
        $this->urlParams = $urlParams;
        return $this;
    }

    public function handle()
    {
        $content = json_decode($this->replaceContent($this->template, $this->params), JSON_UNESCAPED_UNICODE);
        $logId = $this->writeLog($this->phone, $this->template['id'], $content['alert']);
        dispatch(new PhoneTemplateJob($this->phone, $content, $logId));
    }


    /**
     * 替换推送内容
     *
     * @param $content
     *
     * @return string
     */
    private function replaceContent($template, $params)
    {
        $keywords = explode(',', $template['keywords']);
        $content = $template['content'];
        foreach ($keywords as $keyword){
            $content = str_replace('{$'. $keyword. '}', $params[$keyword], $content);
        }
        return $content;
    }
}
