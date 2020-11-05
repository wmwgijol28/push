## 消息推送说明

### 配置文件
复制根目录下的push.php文件到config

相关数据库
`tym_public_message_config`
`tym_public_phone_template`
`tym_public_sms_template`
`tym_public_wx_template`
`tym_public_app_template`
可自行在配置文件里修改

### 1.推送方法

```php
$push = new Push();
$push->push($key, $to, $param);
```
#### 参数说明
1.$key 模板关键词,类似于：
```php
$key = "invite_register_success";
```

2.$to 用户手机号，类似于：
```php
$to = 18927424525;
```

3.$param 推送内容相关参数，类似于：
```php
$param = [
    "code" => "200",
    "time" => "15",
    "amount" => "15"
];
```


