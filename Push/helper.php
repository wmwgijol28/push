<?php

function getConfig($code)
{
    $key = 'yinyi:push:config:'. $code;
    if($config = \Illuminate\Support\Facades\Cache::get($key)){
        return $config;
    }
    $info = \Yinyi\Push\Models\Config::query()->where('code', $code)->first(['code', 'type', 'content']);
    if(in_array($info['type'], ['json'])){

    }
    switch ($info['type']){
        case 'json':
            $config = json_decode($info['content'], JSON_UNESCAPED_UNICODE);
            break;
        case 'int':
        case 'string':
            $config = $info['content'];
            break;
    }
    \Illuminate\Support\Facades\Cache::put($key, $config);
    return $config;
}

function configArray($arr)
{
    $res = [];
    foreach ($arr as $key => $value){
        $res[] = "$key=$value";
    }
    return $res;
}
