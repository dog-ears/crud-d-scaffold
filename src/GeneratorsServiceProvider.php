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
	    
		//define resource view folder
	    $this->loadViewsFrom(__DIR__.'/Resource/views', 'CrudDscaffold');
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerScaffoldGenerator();

		//config setting
	    $this->mergeConfigFrom(
	        __DIR__.'/config/CrudDscaffold.php', 'CrudDscaffold'
	    );
	}

	/**
	 * Register the make:scaffold generator.
	 */
	private function registerScaffoldGenerator()
	{
		//Create scaffold
		$this->app->singleton('command.CrudDscaffold.scaffold', function ($app) {
			return $app['dogears\crud-d-scaffold\Commands\ScaffoldMakeCommand'];
		});
		$this->commands('command.CrudDscaffold.scaffold');

		//Delete scaffold
		$this->app->singleton('command.CrudDscaffold.scaffold_delete', function ($app) {
			return $app['dogears\crud-d-scaffold\Commands\ScaffoldDeleteCommand'];
		});
		$this->commands('command.CrudDscaffold.scaffold_delete');

		//make:relation
		$this->app->singleton('command.CrudDscaffold.make_relation', function ($app) {
			return $app['dogears\crud-d-scaffold\Commands\MakeRelationCommand'];
		});
		$this->commands('command.CrudDscaffold.make_relation');

		//delete:relation
		$this->app->singleton('command.CrudDscaffold.delete_relation', function ($app) {
			return $app['dogears\crud-d-scaffold\Commands\DeleteRelationCommand'];
		});
		$this->commands('command.CrudDscaffold.delete_relation');

	}
}
