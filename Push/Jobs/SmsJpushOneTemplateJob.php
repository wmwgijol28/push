<?php

namespace Yinyi\Push\Jobs;

use App\Exceptions\ApiException;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yinyi\Push\PushOption\Common\Jpush;
use Yinyi\Push\PushOption\Common\sms;


class SmsJpushOneTemplateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Jpush, sms;

    protected $method;

    protected $url;

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
    public function __construct($params, $logId)
    {
        $this->url = 'https://api.sms.jpush.cn/v1/messages';
        $this->params = $params;
        $this->logId = $logId;
        $this->onQueue('sms');
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $this->httpRequest('post', $this->url, $this->params);
        $this->updateLog($this->logId, 1, 'success');

    }

    public function failed(\Throwable $e)
    {
        $this->updateLog($this->logId, 2, $e->getMessage());
    }
}
