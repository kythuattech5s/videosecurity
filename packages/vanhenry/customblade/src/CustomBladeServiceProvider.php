<?php
namespace vanhenry\customblade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
class CustomBladeServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->initConfig();
        $this->masterBlade();
    }
    private function masterBlade(){
        $ret = BladeData::getAllBladeMap();
        Blade::extend(function ($view,$complier) use ($ret) {
            foreach ($ret as $key => $value) {
                 $view = preg_replace($value->name, $value->content, $view);
            }
            return $view;
        });
    }
    /**
     * Register any package services.
     * 
     * @return void
     */
    public function register()
    {
        //
    }
    protected function initConfig(){
        $configPath = __DIR__."/../config/hblade.php";
        $this->publishes([$configPath => config_path('hblade.php')]);
        $this->mergeConfigFrom($configPath,'hblade');
    }
}