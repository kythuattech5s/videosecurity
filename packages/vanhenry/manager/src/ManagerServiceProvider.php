<?php
namespace vanhenry\manager;
use Illuminate\Support\ServiceProvider;
use File;
use Illuminate\Foundation\AliasLoader;
use vanhenry\manager\listeners\ManagerEventListener;
class ManagerServiceProvider extends ServiceProvider
{
    
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->initRoutes();
        $this->publicViews();
        $this->initHelper();
        $this->initLanguage();
        $this->initConfig();
    }
    
    public function register()
    {
       $this->initProviderAlias();
       $this->initListener();
    }
    private function initRoutes(){
        if (! $this->app->routesAreCached()) {
            require_once(__DIR__."/routes/routes.php");
        }
    }
    private function publicViews(){
        $this->loadViewsFrom(__DIR__.'/views', 'vh');       
        $this->publishes([
        __DIR__.'/views' => base_path('resources/views/admin'),
        ]); 
    }
    private function initHelper(){
        $arr = glob(__DIR__.'/helpers/*.php');
        foreach ($arr as $key => $value) {
            require_once($value);
        }
    }
    private function initLanguage(){
        $this->loadTranslationsFrom( __DIR__.'/lang', 'vh');
    }
    private function initConfig(){
        $configPath = __DIR__."/config/manager.php";
        $this->publishes([$configPath=>config_path('manager.php')]);
        $this->mergeConfigFrom($configPath,'manager');
    }
    private function initProviderAlias(){
        $config = include __DIR__."/config/app.php";
        foreach ($config['providers'] as $key => $value) {
            $this->app->register($value);
        }
        foreach ($config['aliases'] as $key => $value) {
            AliasLoader::getInstance()->alias($key,$value);
        }
        
    }
    private function initListener(){
        $this->app->events->subscribe(new ManagerEventListener);
    }
}