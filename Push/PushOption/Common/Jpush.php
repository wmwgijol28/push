<?php
namespace Yinyi\Push\PushOption\Common;

use App\Exceptions\ApiException;
use Facade\FlareClient\Api;
use GuzzleHttp\Client;
use Yinyi\Push\PushCode;

trait Jpush
{
    private $appKey;
    private $masterSecret;

    private function init()
    {
        $config = config('jpush');
        $this->appKey = $config['app_key'];
        $this->masterSecret = $config['master_secret'];
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
