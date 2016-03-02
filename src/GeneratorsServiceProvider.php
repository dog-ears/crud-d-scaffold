<?php

namespace dogears\L5scaffold;

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//

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
		//Create
		$this->app->singleton('command.larascaf.scaffold', function ($app) {
			return $app['dogears\L5scaffold\Commands\ScaffoldMakeCommand'];
		});
		$this->commands('command.larascaf.scaffold');

		//Delete
		$this->app->singleton('command.larascaf.scaffold_delete', function ($app) {
			return $app['dogears\L5scaffold\Commands\ScaffoldDeleteCommand'];
		});
		$this->commands('command.larascaf.scaffold_delete');
	}
}
