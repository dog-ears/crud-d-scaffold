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

class MakeRoute{
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
        //check routefile has "web middleware".
        $output_path = './app/Http/';
        $output_filename = 'routes.php';

        $src = $this->files->get( $output_path. $output_filename );
        
        if( strpos($src,"Route::group(['middleware' => ['web']], function () {") !== false ){

            $this->editRouteMiddlewareExist();

        }else{

            $this->editRoute();

        }
    }

    protected function editRouteMiddlewareExist(){

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/route/';
        $stub_filename = 'route_insert.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/Http/';
        $output_filename = 'routes.php';

        //replace word
        $pattern = '#(Route::group\(\[\'middleware\' => \[\'web\'\]\], function \(\)\s*{)(([^{}]*{[^}]*?}[^{}]*?)*)(.*?)(\s*})#s';     //append at block end
        $replacement = '\1\2\4'.$stub_compiled.'\5';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug=false );        
    }

    protected function editRoute(){

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/route/';
        $stub_filename = 'route_without_web_middleware_append.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/Http/';
        $output_filename = 'routes.php';

        //output(use OutputTrait)
        $this->outputAppend( $output_path, $output_filename, $stub_compiled, $debug=false );
    }    
}