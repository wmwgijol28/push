<?php

namespace Yinyi\Push\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpSms;
use Ucar\Push\Models\PushSms;
use Illuminate\Support\Facades\Log;
use Toplan\PhpSms\Sms;

class YunPianPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    private $driver = 'YunPian';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->onQueue('push');
        $this->data = $data;
    }

    /**
     * Execute the job.
     * @throws \Exception
     *
     * @return bool
     */
    public function handle()
    {
        $data = $this->data;
        $content = $data['content'];

        PhpSms::cleanScheme();
        PhpSms::scheme($this->driver, '100 backup');
        $result = PhpSms::make()
            ->to($data['mobile'])
            ->content($content)
            ->send();

        $logs = collect($result['logs']);

        $smsResult = $logs->where('driver', $this->driver)
            ->first();

        $status = $smsResult['success'] ?? 0;

        $this->writeLog($result);

        if ( ! $status) {
            $errorCode = $smsResult['result']['code'] ?? 0;
            $errorMsg = $smsResult['result']['info'] ?? null;
            throw new \Exception($errorMsg, $errorCode);
        }

        $sms = new PushSms();
        $sms->user_id = $data['user_id'];
        $sms->mobile = $data['mobile'];
        $sms->type = $data['type'];
        $sms->driver = $this->driver;
        $sms->content = $content;
        $sms->status = $status;
        $sms->save();

        return true;
    }

    public function failed(\Throwable $e)
    {
        $data = $this->data;

        $sms = new PushSms();
        $sms->user_id = $data['user_id'];
        $sms->mobile = $data['mobile'];
        $sms->type = $data['type'];
        $sms->content = $data['content'];
        $sms->status = 0;
        $sms->driver = $this->driver;
        $sms->code = $e->getCode();
        $sms->error = $e->getMessage();
        $sms->save();

    }

    /**
     * 写日志
     */
    private function writeLog($result)
    {
        if (config('push.push_debug')) {
            Log::driver('push')
                ->notice('data');
            Log::driver('push')
                ->notice($this->data);
            Log::driver('push')
                ->notice('result');
            Log::driver('push')
                ->notice($result);
        }
    }
}
