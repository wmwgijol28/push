<?php
/**
 * Created by PhpStorm.
 * author: 田建昆
 * Date: 2018/3/7
 * Time: 10:55
 */
use Illuminate\Http\Request;
use Yinyi\Push\Push;

Route::get('/ucar/push/clearCacheByKey', function (Request $request){
    $key = $request->get('key');
    Push::clearCacheByKey($key);
});
Route::get('/ucar/push/clearAllCache', function (){
    Push::clearAllCache();
});