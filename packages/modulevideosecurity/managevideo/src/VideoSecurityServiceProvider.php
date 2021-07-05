<?php

namespace modulevideosecurity\managevideo;

use Illuminate\Support\ServiceProvider;
use modulevideosecurity\managevideo\Setting\VideoSetting;
use modulevideosecurity\managevideo\Listeners\ManagerEventListener;

class VideoSecurityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\modulevideosecurity\managevideo\Setting\VideoSettingInferface::class, function () {
            return new VideoSetting();
        });
        $this->app->events->subscribe(new ManagerEventListener);
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
