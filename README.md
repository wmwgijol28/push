## 短信推送说明

### 生成配置文件
>在根目录的config文件夹生成推送相关的配置文件
```shell
php artisan vendor:publish --provider="Ucar\Push\Providers\PushServiceProvider"
```

### 数据库连接库配置.evn文件PUSH_CONNECTION,数据表配置在config/push.php文件

### 若需要关闭缓存，则配置.evn文件PUSH_NOCACHE为true

### 1.推送方法1

```php
$push = new Push();
$push->send($key, $to, $param, $url);
```
#### 参数说明
1.$key 模板关键词,类似于：
```php
$key = "enjoy-register";
```

2.$to 用户相关参数，类似于：
```php
$to = [
    "user_id" => 12410,        //必要参数,必须为整数
    "mobile" => 18927424525,
    "registration_id" => "1a0018970a94261582c",
    "wechat_open_id" =>'oJV-es7bZ8i0XamyibvTtTjCco1Y',
];
```

3.$param 推送内容相关参数，类似于：
```php
$param = [
    "code" => "200",
    "time" => "15",
    "amount" => "15"
];
```

4.url (可选参数),类似于:
```php
$url = "http://ucarAdmin.me";
```

### 2.推送方法2 （不检查是否存在用户id）
```php
$push = new Push();
$push->sendNoCheckUid($key, $to, $param, $url);
```
#### 参数说明
其他参数不变，只改变$to参数，如下：
```php
$to = [
    "mobile" => 18927424525,
];
```

### 3.根据关键词清除相应的缓存
路由：/ucar/push/clearCacheByKey
>需要传递一个参数 $key 模板关键词,类似于：
>```php
>$key = "enjoy-register";
>```

### 4.清除所有缓存
路由：/ucar/push/clearAllCache


