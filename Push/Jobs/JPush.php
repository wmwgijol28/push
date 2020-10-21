<?php

namespace Yinyi\Push\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use JPush\Client;
use Ucar\Push\Models\PushJPush;
use Illuminate\Support\Facades\Log;

class JPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    private $everyMinuteMaxPushTime = 1200;

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
     *
     * @return bool
     */
    public function handle(Client $client)
    {
        $data = $this->data;
        Cache::put('JPush:' . date('H:i'), Cache::get('JPush:' . date('H:i')) + 1, 1);
        if (Cache::get('JPush:' . date('H:i')) > ($this->everyMinuteMaxPushTime * 0.85)) {
            $job = (new JPush($data))->delay(Carbon::now()
                ->addMinutes(1));
            dispatch($job);
            return true;
        }

        $content = $data['content'];
        $ios_notification = [
            'sound' => 'default',
            'extras' => [
                'type' => (String)$data['push_type'],
                'url' => $data['url'] ?? '',
            ],
        ];
        $android_notification = [
            'title' => config('jpush.title'),
            'extras' => [
                'type' => (String)$data['push_type'],
                'url' => $data['url'] ?? '',
            ],
        ];
        $message = [
            'title' => config('jpush.title'),
            'content_type' => 'text',
            'extras' => [
                'type' => (String)$data['push_type'],
                'url' => $data['url'] ?? '',
            ],
        ];

        $response = $client->push()
            ->setPlatform('all')
            ->addRegistrationId($data['registration_id'])
            ->setNotificationAlert($content)
            ->iosNotification($content, $ios_notification)
            ->androidNotification($content, $android_notification)
            ->message($content, $message)
            ->options(['time_to_live' => 86400, 'apns_production' => ! config('push.push_debug')])
            ->send();

        $this->writeLog($response);

        $jpush = new PushJPush();
        $jpush->registration_id = $data['registration_id'];
        $jpush->user_id = $data['user_id'];
        $jpush->type = $data['type'];
        $jpush->push_type = (String)$data['push_type'];
        $jpush->content = $data['content'];
        $jpush->code = $response['http_code'];
        $jpush->sendno = $response['body']['sendno'];
        $jpush->msg_id = $response['body']['msg_id'];
        $jpush->save();

        return true;
    }

    public function failed(\Throwable $e)
    {
        $data = $this->data;

        $jpush = new PushJPush();
        $jpush->registration_id = $data['registration_id'];
        $jpush->user_id = $data['user_id'];
        $jpush->type = $data['type'];
        $jpush->push_type = (String)$data['push_type'];
        $jpush->content = $data['content'];
        $jpush->code = $e->getCode();
        $jpush->error = $e->getMessage();
        $jpush->save();
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
