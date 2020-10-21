<?php

namespace Ucar\Push\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use EasyWeChat\OfficialAccount\Application;
use Ucar\Push\Models\PushWechat;
use Illuminate\Support\Facades\Log;

class WechatPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data) {
        $this->onQueue('push');
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
        public function handle(Application $wechat) {
        $data = $this->data;

        $wx = new PushWechat();
        $wx->user_id = $data['user_id'];
        $wx->wx_open_id = $data['body']['touser'];
        $wx->push_type = $data['push_type'];

        $template_message = $wechat->template_message;
        $result = $template_message->send($data['body']);

        $this->writeLog($result);
        $wx->code = $result['errcode'] ?: '';
        $wx->error = $result['errmsg'] ?: '';
        $wx->save();

    }

    public function failed(\Throwable $e)
    {
        $data = $this->data;

        $wx = new PushWechat();
        $wx->user_id = $data['user_id'];
        $wx->wx_open_id = $data['body']['touser'];
        $wx->push_type = $data['push_type'];

        $wx->code = $e->getCode() ?: '';
        $wx->error = $e->getMessage() ?: '';
        $wx->save();
    }

    /**
     * 写日志
     */
    private function writeLog($result)
    {
        if (config('push.push_debug')) {
            Log::driver('push')->notice('data');
            Log::driver('push')->notice($this->data);
            Log::driver('push')->notice('result');
            Log::driver('push')->notice($result);
        }
    }
}
