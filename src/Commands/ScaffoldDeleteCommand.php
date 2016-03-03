<?php

namespace dogears\L5scaffold\Commands;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use dogears\L5scaffold\Makes\MakerTrait;
use dogears\L5scaffold\Traits\NameSolverTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ScaffoldDeleteCommand extends Command
{
    use AppNamespaceDetectorTrait, MakerTrait, NameSolverTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'delete:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a scaffold';

    /**
     * Name config information
     *
     * @var array
     */
    protected $name_config;

    /**
     * @var Composer
     */
    private $composer;

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

        // Start Scaffold
        $this->info('Deleting ' . $this->solveName('NameName') . '...');

        //NameSolver initialize
        $this->nameSolverInit();

        // Seed --------------------
        // Get path
        $path = $this->getPath($this->getNameConfig('seeder_name'). 'TableSeeder', 'seed');

        if ($this->files->exists($path)) {
            //Delete
            $this->files->delete($path);
            $this->info('Seeding is Deleted');
        }else{
            $this->info('Seeding ('.$path.') is not exists!');
        }

        //Model --------------------
        // Get path
        $name = $this->getNameConfig('model_name');
        $path = $this->getPath($name, 'model');

        if ($this->files->exists($path)) {
            //Delete
            $this->files->delete($path);
            $this->info('Model is Deleted');
        }else{
            $this->info('Model ('.$path.') is not exists!');
        }

        //Controller -------------------- 
        // Get path
        $name = $this->getNameConfig('controller_name'). 'Controller';
        $path = $this->getPath($name);

        if ($this->files->exists($path)) {
            //Delete
            $this->files->delete($path);
            $this->info('Controller is Deleted');
        }else{
            $this->info('Controller ('.$path.') is not exists!');
        }

        //Views --------------------
        // Get path
        $name = $this->getNameConfig('view_name');
        $path = $this->getPath($name, 'view');
        if ($this->files->isDirectory($path)) {
            //Delete
            $this->files->deleteDirectory($path);
            $this->info('View is Deleted');
        }else{
            $this->info('View ('.$path.') is not exists!');
        }

        $this->info('Delete is done.');
        $this->info('Change Route File and rollback migration and delete migrate file if you need.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the model. (Ex: Post)'],
        ];
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    /**
     * Get access to $name_config array
     * @return string
     */
    public function getNameConfig($input)
    {
        return $this->name_config[$input];
    }

}
