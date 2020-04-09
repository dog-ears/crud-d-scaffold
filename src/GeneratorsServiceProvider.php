<?php

/**
Copyright (c) 2016 dog-ears
This software is released under the MIT License.
http://dog-ears.net/
*/

namespace DogEars\CrudDScaffold;

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{}

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
		$this->app->singleton('command.CrudDScaffold.setup', function ($app) {
			return $app['DogEars\CrudDScaffold\Commands\CrudDScaffoldSetupCommand'];
		});
		$this->commands('command.CrudDScaffold.setup');
	}
}