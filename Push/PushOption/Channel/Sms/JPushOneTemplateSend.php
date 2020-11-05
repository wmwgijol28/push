<?php
namespace Yinyi\Push\PushOption\Channel\Sms;

use App\Exceptions\ApiException;
use Yinyi\Push\Jobs\SmsJpushOneTemplateJob;
use Yinyi\Push\PushOption\Common\sms;

class JPushOneTemplateSend implements PushSmsInterface
{
    use sms;

    private $mobile;

    private $content;

    private $template;

    private $temp_para;

    public function init($mobile, $template, $params = [])
    {
        $this->mobile = $mobile;
        $this->content = $this->getContent($template['content']);
        $this->template = $template;
        $this->temp_para = json_encode($params, JSON_UNESCAPED_UNICODE);
        return $this;
    }

    public function handle()
    {
        $data = [
            'mobile' => $this->mobile,
            'temp_id' => $this->content['temp_id'],
            'temp_para' => $this->temp_para
        ];
        $logId = $this->writeLog(json_encode($data, JSON_UNESCAPED_UNICODE));

        dispatch(new SmsJpushOneTemplateJob($data, $logId));
    }


    private function getContent($content)
    {
        return json_decode($content, JSON_UNESCAPED_UNICODE);
    }
}
