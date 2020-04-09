<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace DogEars\CrudDScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use DogEars\CrudDScaffold\Core\CrudDScaffold;

class CrudDScaffoldSetupCommand extends Command
{
//    use DetectsApplicationNamespace, MakerTrait, NameSolverTrait;

    /**
     * The console command name.
     *
     * @var string
     */

    protected $signature = 'crud-d-scaffold:setup
                            {filePath=crud-d-scaffold.json : file path of setting json file. Default: crud-d-scaffold.json }
                            {--f|force : Allow overwrite files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup crud-d-scaffold with bootstrap 4';

    /**
     * Crud-D-Scaffold Core
     *
     * @var obj
     */
    protected $crud_d_scaffold;

    /**
     * @var Composer
     */
    private $composer;

    /**
     * Create a new command instance.
     *
     * @param Composer $composer
     */
    public function __construct( CrudDScaffold $crud_d_scaffold, Composer $composer )
    {
        parent::__construct();
        $this->composer = $composer;
        $this->crud_d_scaffold = $crud_d_scaffold;
        $this->crud_d_scaffold->setCommand( $this );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->crud_d_scaffold->generate();

        //Dump autoload
        $this->info('Dump-autoload...');
        $this->composer->dumpAutoloads();

        // End Message
        $this->info('Configuring is done');
    }
}
