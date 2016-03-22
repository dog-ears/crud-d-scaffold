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

class MakeSeed{
    use MakerTrait,NameSolverTrait,OutputTrait;

    protected $files;
    protected $commandObj;

    public function __construct($command, Filesystem $files)
    {
        $this->files = $files;
        $this->commandObj = $command;
        $this->start();
    }

    protected function start()
    {
        if( $this->commandObj->option('seeding') ){

            //(i)Factory --------------------------------------------------

            //get_stub_path and filename
            $stub_path = __DIR__.'/../Stubs/seed/';
            $stub_filename = 'factory_apend.stub';

            //create new stub
            $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = 'factory', $custom_replace = null );

            //compile
            $stub_compiled = $stub->getCompiled();

            //get output_path and filename
            $output_path = './database/factories/';
            $output_filename = 'ModelFactory.php';

            //output(use OutputTrait)
            $this->outputAppend( $output_path, $output_filename, $stub_compiled, $debug=false );



            //(ii)DatabaseSeeder --------------------------------------------------

            //get_stub_path and filename
            $stub_path = __DIR__.'/../Stubs/seed/';
            $stub_filename = 'databaseSeeder_insert.stub';

            //create new stub
            $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

            //compile
            $stub_compiled = $stub->getCompiled();

            //get output_path and filename
            $output_path = './database/seeds/';
            $output_filename = 'DatabaseSeeder.php';

            //replace word
            $pattern = '#(public function run\(\)\s*{)(([^{}]*{[^}]*?}[^{}]*?)*)(.*?)(\s*})#s';   //append at block end
            $replacement = '\1\2\4'. $stub_compiled. '\5';

            //output(use OutputTrait)
            $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug=false );



            //(iii)DatabaseSeeder --------------------------------------------------

            //get_stub_path and filename
            $stub_path = __DIR__.'/../Stubs/seed/';
            $stub_filename = 'app.stub';
    
            //create new stub
            $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );
    
            //compile
            $stub_compiled = $stub->getCompiled();
    
            //get output_path and filename
            $output_path = './database/seeds/';
            $output_filename = $this->solveName($this->commandObj->argument('name'), config('CrudDscaffold.app_name_rules.app_seeder_class')).'TableSeeder.php';

            //output(use OutputTrait)
            $this->outputPut( $output_path, $output_filename, $stub_compiled, $debug=false );

        }
    }
}