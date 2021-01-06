<?php

namespace Yinyi\Push\Jobs;

use App\Exceptions\ApiException;
use Carbon\Carbon;
use EasyWeChat\Factory;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yinyi\Push\Models\User;
use Yinyi\Push\PushCode;
use Yinyi\Push\PushOption\Common\wechat;


class WechatTemplateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, wechat;

    protected static $token;

    protected static $client = null;

    private $host = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send?';

    protected $phone;

    protected $params;

    protected $logId;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($phone, $params, $logId)
    {
        $this->params = $params;
        $this->phone = $phone;
        $this->logId = $logId;
        $this->onQueue('wechat');
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $openId = User::query()->with(['oauth'])->where('phone', $this->phone)->first()->toArray()['oauth']['wx_mini_openid'];
        if(empty($openId)){
            ApiException::throwError(PushCode::WECHAT_OPEN_ID_MISSING, '未发现用户的openid');
        }

        $this->params['touser'] = $openId;
        $client = $this->getClient();
        $url = $this->host. 'access_token='. $this->getToken();
        $response = $client->request('post', $url, [
            'body' => json_encode((object)$this->params)
        ]);

        if($response->getStatusCode() != 200){
            ApiException::throwError(PushCode::SEND_FAILD, '发送失败');
        }
        $result = json_decode($response->getBody()->getContents(), JSON_UNESCAPED_UNICODE);
        if($result['errcode']){
            ApiException::throwError($result['errcode'], $result['errmsg']);
        }
        $this->updateLog($this->logId, 1, 'success');

    }

    public function failed(\Throwable $e)
    {
        $this->updateLog($this->logId, 2, $e->getMessage());
    }

    private function getToken($flag = false) :string
    {
        if(self::$token && $flag){
            return self::$token;
        }
        $app = Factory::miniProgram(getConfig('wechat_mini_program'));
        self::$token = $app->access_token->getToken(true)['access_token'];
        return self::$token;
    }

    private function getClient($flag = false) : Client
    {
        if(self::$client && $flag){
            return self::$client;
        }
        self::$client = new Client();
        return self::$client;
    }
}
