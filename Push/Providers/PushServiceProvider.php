<?php

namespace Ucar\Push\Providers;

use Illuminate\Support\ServiceProvider;

class PushServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        //
//        $this->loadRoutesFrom(__DIR__.'/../routes.php');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/jpush.php' => config_path('jpush.php'),
                __DIR__.'/../config/phpsms.php' => config_path('phpsms.php'),
                __DIR__.'/../config/wechat.php' => config_path('wechat.php'),
                __DIR__.'/../config/push.php' => config_path('push.php')
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {


    }
}
