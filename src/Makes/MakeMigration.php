<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Makes;

use Illuminate\Filesystem\Filesystem;
use dogears\CrudDscaffold\Commands\ScaffoldMakeCommand;
use dogears\CrudDscaffold\Stubs\StubController;
use dogears\CrudDscaffold\Traits\MakerTrait;
use dogears\CrudDscaffold\Traits\NameSolverTrait;
use dogears\CrudDscaffold\Traits\OutputTrait;

class MakeMigration {
    use MakerTrait,NameSolverTrait,OutputTrait;

    protected $files;
    protected $commandObj;

    public function __construct($command, Filesystem $files)
    {
        $this->files = $files;
        $this->commandObj = $command;
        $this->start();
    }

    protected function start(){

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/migrate/';
        $stub_filename = 'app.stub';

        //set custom replace
        $custom_replace = [
            'table' => $this->solveName($this->commandObj->argument('name'), config('CrudDscaffold.app_name_rules.app_migrate_filename')),
        ];

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = 'migration', $custom_replace );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './database/migrations/';
        $output_filename = date('Y_m_d_His'). '_create_'. $this->solveName($this->commandObj->argument('name'), config('CrudDscaffold.app_name_rules.app_migrate_filename')). '_table.php';

        //output(use OutputTrait)
        $this->outputPut( $output_path, $output_filename, $stub_compiled, $debug=false );
    }
}