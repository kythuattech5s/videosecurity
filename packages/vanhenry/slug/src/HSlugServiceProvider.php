<?php

namespace vanhenry\slug;

use Illuminate\Support\ServiceProvider;

class HSlugServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->initConfig();
        $this->loadViewsFrom(__DIR__.'/views', '404');
        $this->publishes([
        __DIR__.'/views' => base_path('resources/views/errors/'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerEvents();
        $this->registerCommand();
        $this->registerRoutes();
    }
    protected function initConfig(){
        $configPath = __DIR__."/../config/hslug.php";
        $this->publishes([$configPath => config_path('hslug.php')]);
        $this->mergeConfigFrom($configPath,'hslug');
    }
    private function registerEvents(){
        $this->app['events']->listen('eloquent.saving*',function($model){
            if($model instanceof HSlugInterface){
                $model->sluggify();
            }
        });
        $this->app['events']->listen('eloquent.saved*',function($model){
            if($model instanceof HSlugInterface){
                $model->afterSluggify();
            }
        });
        $this->app['events']->listen('eloquent.deleted*',function($model){
            if($model instanceof HSlugInterface){
                $model->removeSlug();
            }
        });
        
    }
    private function registerCommand(){
        $this->app['make.slugmodel'] = $this->app->share(function ($app) {
            return new HCommand($app['files']);
        });

        $this->commands('make.slugmodel');
    }
    private function registerRoutes(){
        include __DIR__.'/routes.php';
    }
    public function provides() {
        return array('paginator');
      }
}
