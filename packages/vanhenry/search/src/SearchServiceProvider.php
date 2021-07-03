<?php namespace vanhenry\search;

use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$configPath = __DIR__."/config/search.php";
		$this->publishes([
			$configPath => config_path('search.php')
		]);
		$this->mergeConfigFrom($configPath,'search');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('search', function ($app) {
			return new \vanhenry\search\Search();
		});
	}

}
