<?php

namespace Radisand\ApiGeneralSchemeMyGenetics\Providers;

use Illuminate\Support\ServiceProvider;

class MyGenetricsMssTokenServiceProvider extends ServiceProvider
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
        $this->mergeConfigFrom(
            __DIR__.'/../../config/myGeneticsMssTokenConfig.php', 'app'
        );
    }
}