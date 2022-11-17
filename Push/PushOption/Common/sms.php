<?php


namespace Yinyi\Push\PushOption\Common;


use App\Exceptions\ApiException;
use GuzzleHttp\Client;
use Yinyi\Push\Mongo\PublicSmsLog;
use Yinyi\Push\PushCode;

trait sms
{
    private $appKey;
    private $masterSecret;

    private function init()
    {
        $config = config('jpush');
        $this->appKey = $config['app_key'];
        $this->masterSecret = $config['master_secret'];
    }

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

    private function getBasicValue()
    {
        return base64_encode($this->appKey. ':'. $this->masterSecret);
    }

    private function httpRequest($method, $url, $params)
    {
        $client = new Client();
        $response = $client->request($method, $url, [
            'headers' => [
                'Authorization' => 'Basic '. $this->getBasicValue(),
            ],
            'form_params' => $params
        ]);
        if($response->getStatusCode() != 200){
            ApiException::throwError(PushCode::SEND_FAILD, '链接失败');
        }
        return $response->getBody()->getContents();
    }
}
