<?php

namespace modulevideosecurity\managevideo;

use Illuminate\Support\ServiceProvider;
use modulevideosecurity\managevideo\Setting\VideoSetting;

class VideoSecurityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('VideoSetting', function () {
            return new VideoSetting();
        });
        $this->commands([
            \modulevideosecurity\managevideo\Commands\VideoConvert::class
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
		$this->loadViewsFrom(__DIR__.'/views', 'tvs');
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
    }
}
