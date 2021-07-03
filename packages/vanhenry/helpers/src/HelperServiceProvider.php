<?php

namespace vanhenry\helpers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
    }


    /**
     * Register any package services.
     * 
     * @return void
     */
    public function register()
    {
        $this->registerHelper();
    }
    private function registerHelper(){
        $arr = glob(__DIR__.'/helpers/*.php');
        foreach ($arr as $key => $value) {
            require_once($value);
        }
    }

}