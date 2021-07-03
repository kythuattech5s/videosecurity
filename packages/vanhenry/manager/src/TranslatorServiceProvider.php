<?php 
namespace vanhenry\manager;
use Illuminate\Translation\FileLoader;
use vanhenry\manager\translator\HTranslator;
use Illuminate\Translation\TranslationServiceProvider as BaseServiceProvider;
use vanhenry\manager\translator\TranslatorAdapter;
use vanhenry\manager\translator\TranslatorFrontendAdapter;
use Session;
class TranslatorServiceProvider extends BaseServiceProvider
{
	public function register()
    {
        $this->initTranslator();
        $this->initDBAdapter();
    }
    public function initTranslator()
    {
        $this->registerLoader();
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];
            $trans = new HTranslator($loader, $locale);
            $trans->setFallback($app['config']['app.fallback_locale']);
            return $trans;
        });
  
    }
    public function initDBAdapter(){
        $this->app->singleton('_vh_translator_db_adapter',function($app){
            return new TranslatorAdapter();
        });
        $this->app->singleton('_vh_frontend_translator_db_adapter',function($app){
            return new TranslatorFrontendAdapter();
        });
    }
}
?>