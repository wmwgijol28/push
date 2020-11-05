<?php
namespace Yinyi\Push\PushOption\Channel\App;

use App\Exceptions\ApiException;
use App\Http\StatusCode;
use Facade\FlareClient\Api;
use Illuminate\Support\Facades\Log;
use Yinyi\Push\Models\PublicAppMsg;
use Yinyi\Push\PushCode;
use Yinyi\Push\PushOption\Common\tym;

class ActiveMsgSend implements PushAppInterface
{
    use tym;

    private $uid;
    private $template;
    private $params;


    public function init($uid, $template, $params = [])
    {
        $this->uid = $uid;
        $this->template = $template;
        $this->params = $params;
        return $this;
    }

    public function handle()
    {
        $data = [
            'type' => $this->template['type'],
            'uid' =>  $this->uid,
            'title' => $this->template['title'],
            'content' => $this->replaceContent($this->template, $this->params),
            'url' => $this->template['url'],
            'url_type' => $this->template['url_type']
        ];
        try {
            if(!PublicAppMsg::query()->insert($data)){
                ApiException::throwError(PushCode::SEND_FAILD, '发送失败');
            }
        }catch (\Exception $exception){
            Log::error('站内信发送失败', ['code' => $exception->getCode(), 'msg' => $exception->getMessage()]);
        }

    }
}
