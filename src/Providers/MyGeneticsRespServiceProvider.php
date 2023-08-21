<?php

namespace Radisand\ApiGeneralSchemeMyGenetics\Providers;

use Illuminate\Support\ServiceProvider;

class MyGeneticsRespServiceProvider extends ServiceProvider
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
        $this->publishes([
            __DIR__.'/../../config/myGeneticsApiSchemeConfig.php' => config_path('myGeneticsApiScheme.php'),
        ], 'config');
    }
}