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
		//config publish
	    $this->publishes([
	        __DIR__.'/config/l5scaffold.php' => config_path('l5scaffold.php'),
	    ]);

	    $this->publishes([
	        __DIR__.'/Assets' => public_path('dog-ears/l5scaffold'),
	    ], 'public');

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
	        __DIR__.'/config/l5scaffold.php', 'l5scaffold'
	    );
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

		//make:relation
		$this->app->singleton('command.larascaf.make_relation', function ($app) {
			return $app['dogears\L5scaffold\Commands\MakeRelationCommand'];
		});
		$this->commands('command.larascaf.make_relation');

		//delete:relation
		$this->app->singleton('command.larascaf.delete_relation', function ($app) {
			return $app['dogears\L5scaffold\Commands\DeleteRelationCommand'];
		});
		$this->commands('command.larascaf.delete_relation');

	}
}
