<?php

namespace Yinyi\Push\Jobs;

use App\Exceptions\ApiException;
use App\Http\StatusCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yinyi\Push\Models\User;
use Yinyi\Push\Models\UserPushRelation;
use Yinyi\Push\PushOption\Common\Jpush;


class PhoneTemplateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Jpush;

    protected $phone;

    protected $title;

    protected $content;

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
    public function __construct($phone, $content, $logId)
    {
        $this->phone = $phone;
        $this->content = $content;
        $this->logId = $logId;
        $this->init();
        $this->onQueue('{message_phone}');
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $rids = UserPushRelation::query()->where('uid',
            User::query()->where('phone', $this->phone)->value('uid'))
            ->pluck('jpush_rid')->toArray();
        if(!$rids){
            ApiException::throwError(StatusCode::ERROR, '没有注册rid');
        }

        $android_notification = array(
            'title' => $this->content['title'],
        );
        if($this->content['url']){
            $android_notification['intent'] = [
                'url' => 'tym_jpush://jpush_skip?skip='. json_encode([
                        'skip_type' => 2,
                        'skip_value' => [
                            'path' => $this->content['url'],
                            'cType' => 0
                        ]
                    ], JSON_UNESCAPED_UNICODE)
            ];
        }

        $push = $this->client->push();
        $push->setPlatform('all')
            ->addRegistrationId($rids)
            ->iosNotification($this->content['alert'])
            ->androidNotification($this->content['alert'], $android_notification);
        try {
            $res = $push->send();
            if($res['http_code'] == 200){
                $this->updateLog($this->logId, 1, 'success');
            }else{
                ApiException::throwError(StatusCode::ERROR, json_encode($res, JSON_UNESCAPED_UNICODE));
            }
        }catch (\Exception $e){
            $this->updateLog($this->logId, 2, $e->getMessage());
        }

    }

    public function failed(\Throwable $e)
    {
        $this->updateLog($this->logId, 2, $e->getMessage());
    }
}
