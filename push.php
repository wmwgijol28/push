<?php

return [
    'noCache' => env('PUSH_NO_CACHE', true),

    'public' => 'tym',

    'table' => [
        'message_config' => 'tym_public_message_config',
        'app_template' => 'tym_public_app_template',
        'phone_template' => 'tym_public_phone_template',
        'sms_template' => 'tym_public_sms_template',
        'wx_template' => 'tym_public_wx_template',
        'app_msg' => 'tym_public_app_msg',
    ],

];
