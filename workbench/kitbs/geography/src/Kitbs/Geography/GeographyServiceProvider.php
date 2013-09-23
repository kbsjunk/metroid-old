<?php namespace Kitbs\Geography;

use Illuminate\Support\ServiceProvider;

class GeographyServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('kitbs/geography');

		include __DIR__.'/../../Facade.php';
		include __DIR__.'/../../Geography.php';

		
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['geography'] = $this->app->share(function($app)
		{
			return new Geography;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}