<?php namespace vanhenry\minify;

use Illuminate\Support\ServiceProvider;

class MinifyServiceProvider extends ServiceProvider {
	public function register(){}
	public function boot()
    {
        $this->initConfig();
    	$this->publicViews();
        $this->app->singleton('Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode', function($app){
            return  new PackageMiddleware($app);
        });
        $this->app->singleton('Firewall',function($app){
        	return new Firewall($app);
        });
        
    }
    protected function initConfig(){
        $configPath = __DIR__."/config/firewall.php";
        $this->publishes([$configPath => config_path('firewall.php')]);
        $this->mergeConfigFrom($configPath,'firewall');
    }
    private function publicViews(){
        $this->loadViewsFrom(__DIR__.'/views', 'fw');       
    }    
}
