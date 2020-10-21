<?php

return [

    /*
     * PUSH_DEBUG 模式，bool 值：true/false
     *
     * 当值为 true 时
     *
     * 短信 只会发送存在于 test_mobiles 列表中的手机号
     *
     * 微信停止发送
     *
     * 极光只推送到开发中app
     *
     */
    'push_debug'  => env('PUSH_DEBUG', true),

    'db_connection'  => env('PUSH_CONNECTION', env('DB_CONNECTION', 'mysql')),

    'table'  => [
        'templates' => 'templates',
        'template_alidayu' => 'template_alidayu',
        'template_jiguang' => 'template_jiguang',
        'template_wechat' => 'template_wechat',
        'template_youyi' => 'template_youyi',
        'template_content_sms' => 'template_content_sms',
        'push_jpush' => 'push_jpush',
        'push_sms' => 'push_sms',
        'push_wechat' => 'push_wechat',
        'message_consumption' => 'message_consumption',
        'message_credit' => 'message_credit',
        'message_iou' => 'message_iou',
        'message_serve_informs' => 'message_serve_informs',
        'template_message_consumption' => 'template_message_consumption',
        'template_message_credit' => 'template_message_credit',
        'template_message_iou' => 'template_message_iou',
        'template_message_serve_informs' => 'template_message_serve_informs',
    ],

    'test_mobiles' => [

    ],
    'noCache'  => env('PUSH_NOCACHE', false),
];
