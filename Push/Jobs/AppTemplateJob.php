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
use Yinyi\Push\Models\PublicAppMsg;
use Yinyi\Push\Models\User;
use Yinyi\Push\PushCode;
use Yinyi\Push\PushOption\Common\wechat;


class AppTemplateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;

    protected $params;

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
    public function __construct($phone, $params)
    {
        $this->params = $params;
        $this->phone = $phone;
        $this->onQueue('message_app');
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $uid = User::query()->where('phone', $this->phone)->value('uid');
        if(empty($uid)){
            ApiException::throwError(PushCode::USER_ID_MISSING, '未发现用户');
        }

        $this->params['uid'] = $uid;
        PublicAppMsg::query()->insert($this->params);
    }

    public function failed(\Throwable $e)
    {

    }
}
