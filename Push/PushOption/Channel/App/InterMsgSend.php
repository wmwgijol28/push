<?php
namespace Yinyi\Push\PushOption\Channel\App;

use App\Exceptions\ApiException;
use App\Http\StatusCode;
use Facade\FlareClient\Api;
use Illuminate\Support\Facades\Log;
use Yinyi\Push\Jobs\AppTemplateJob;
use Yinyi\Push\Models\PublicAppMsg;
use Yinyi\Push\PushCode;
use Yinyi\Push\PushOption\Common\tym;

class InterMsgSend implements PushAppInterface
{
    use tym;
}
