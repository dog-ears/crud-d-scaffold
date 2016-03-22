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
use dogears\CrudDscaffold\Makes\DeleteAll;
use dogears\CrudDscaffold\Traits\MakerTrait;
use dogears\CrudDscaffold\Traits\NameSolverTrait;
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
    protected $signature = 'delete:scaffold
                            {name : The name of the model. (Ex: AppleType)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a scaffold';

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
        $this->info('Deleting ' . $this->solveName( $this->argument('name'), 'NameName' ) . '...');

        // Delete
        new DeleteAll($this, $this->files);

        // End Message
        $this->info('Deleting is done');
        $this->info('Rollback migration and delete migrate file if you need.');
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
}
