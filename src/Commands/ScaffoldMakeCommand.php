<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Commands;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Artisan;
use dogears\CrudDscaffold\Makes\MakeMigration;
use dogears\CrudDscaffold\Makes\MakeController;
use dogears\CrudDscaffold\Makes\MakeLayout;
use dogears\CrudDscaffold\Makes\MakeModel;
use dogears\CrudDscaffold\Makes\MakeSeed;
use dogears\CrudDscaffold\Makes\MakeView;
use dogears\CrudDscaffold\Makes\MakeRoute;
use dogears\CrudDscaffold\Makes\MakeRouteServiceProvider;
use dogears\CrudDscaffold\Traits\MakerTrait;
use dogears\CrudDscaffold\Traits\NameSolverTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ScaffoldMakeCommand extends Command
{
    use AppNamespaceDetectorTrait, MakerTrait, NameSolverTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:scaffold
                            {name : The name of the model. (Ex: AppleType)}
                            {--s|schema=null : Schema to generate scaffold files. (Ex: --schema="title:string, body:nullable:, apple_type_id:integer:unsigned")}
                            {--S|seeding : Create seeding files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a scaffold with bootstrap 3';


    /**
     * Meta information for the requested migration.
     *
     * @var array
     */
    protected $meta;

    /**
     * @var Composer
     */
    private $composer;

    /**
     * Views to generate
     *
     * @var array
     */
    private $views = ['index', 'create', 'show', 'edit', 'duplicate', '_form'];

    /**
     * Store name from Model
     * @var string
     */
    private $nameModel = "";

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        //set meta data for schema
        $this->meta['action'] = 'create';
        $this->meta['var_name'] = $this->solveName($this->argument('name'),'nameName');
        $this->meta['table'] = $this->solveName($this->argument('name'),config('CrudDscaffold.app_name_rules.app_migrate_tablename')); // Store table name

        // Message of Start Scaffold
        $this->info('Configuring ' . $this->solveName($this->argument('name'),'NameName') . '...');

        // Generate files
        $this->makeMigration();
        $this->makeSeed();
        $this->makeModel();
        $this->makeController();
        $this->makeViewLayout();
        $this->makeViews();
        $this->makeRoute();
        $this->makeRouteServiceProvider();

        // End Message
        $this->info('Configuring is done');
    }

    /**
     * Generate the desired migration.
     */
    protected function makeMigration()
    {
        new MakeMigration($this, $this->files);
    }

    /**
     * Generate a Seed
     */
    private function makeSeed()
    {
        new MakeSeed($this, $this->files);
    }

    /**
     * Generate an Eloquent model, if the user wishes.
     */
    protected function makeModel()
    {
        new MakeModel($this, $this->files);
    }

    /**
     * Make a Controller with default actions
     */
    private function makeController()
    {
        new MakeController($this, $this->files);
    }

    /**
     * Make a layout.blade.php with bootstrap
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function makeViewLayout()
    {
        new MakeLayout($this, $this->files);
    }

    /**
     * Setup views and assets
     *
     */
    private function makeViews()
    {
        foreach ($this->views as $view) {
            // index, create, show, edit, duplicate, _form
            new MakeView($this, $this->files, $view);
        }

        //publish html resource
        $this->info('Dump-autoload...');
        Artisan::call('vendor:publish', ['--tag=public','--force']);

        //Dump autoload
        $this->info('Dump-autoload...');
        $this->composer->dumpAutoloads();
    }

    /**
     * Setup Route
     *
     */
    private function makeRoute()
    {
        new MakeRoute($this, $this->files);
    }

    /**
     * Setup Route
     *
     */
    private function makeRouteServiceProvider()
    {
        new MakeRouteServiceProvider($this, $this->files);
    }

    /**
     * Get access to $meta array
     * @return array or string
     */
    public function getMeta($input = null)
    {
        if($input){
            return $this->meta[$input];
        }else{
            return $this->meta;
        }
    }
}
