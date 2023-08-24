<?php

namespace Radisand\ApiGeneralSchemeMyGenetics\Providers;

use Illuminate\Support\ServiceProvider;
use Radisand\ApiGeneralSchemeMyGenetics\Middlewares\MyGeneticsApiAuthMiddleware;

class MyGeneticsApiAuthServiceProvider extends ServiceProvider
{
    /**
     * register any package service
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * load any package data
     *
     * @return void
     */
    public function boot()
    {
        $this -> app['router'] -> aliasMiddleware('authMsCheck', MyGeneticsApiAuthMiddleware::class);
    }
}