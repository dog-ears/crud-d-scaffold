<?php

/**
Copyright (c) 2016 dog-ears
This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold;

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{

		//asset publish
	    $this->publishes([
	        __DIR__.'/Assets' => public_path('dog-ears/CrudDscaffold'),
	    ], 'public');
/*	    
		//define resource view folder
	    $this->loadViewsFrom(__DIR__.'/Resource/views', 'CrudDscaffold');
*/
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerScaffoldGenerator();
	}

	/**
	 * Register the make:scaffold generator.
	 */
	private function registerScaffoldGenerator()
	{
		//Setup my scaffold
		$this->app->singleton('command.CrudDscaffold.setup', function ($app) {
			return $app['dogears\CrudDscaffold\Commands\CrudDscaffoldSetupCommand'];
		});
		$this->commands('command.CrudDscaffold.setup');
	}
}