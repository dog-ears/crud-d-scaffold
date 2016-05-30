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
use dogears\CrudDscaffold\Migrations\SchemaParser;
use dogears\CrudDscaffold\Migrations\SyntaxBuilder;

class MakeView
{
    use MakerTrait,NameSolverTrait,OutputTrait;

    protected $files;
    protected $commandObj;
    protected $viewName;

    public function __construct($command, Filesystem $files, $viewName)
    {
        $this->files = $files;
        $this->commandObj = $command;
        $this->viewName = $viewName;
        $this->start();
    }

    private function start()
    {

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_app/';
        $stub_filename = $this->viewName.'.stub';

        //create custom parameter
        if ($schema = $this->commandObj->option('schema')) {
            $schemaArray = (new SchemaParser)->parse($schema);
        }

        $custom_replace = [
            'index' => [
                'search_fields' => (new SyntaxBuilder)->create($schemaArray, $this->commandObj->getMeta(), 'view-index-search'),
                'header_fields' => (new SyntaxBuilder)->create($schemaArray, $this->commandObj->getMeta(), 'view-index-header'),
                'content_fields' => (new SyntaxBuilder)->create($schemaArray, $this->commandObj->getMeta(), 'view-index-content'),
            ],
            'show' => [
                'content_fields' => (new SyntaxBuilder)->create($schemaArray, $this->commandObj->getMeta(), 'view-show-content'),
            ],
            'create' => [ 'content_fields' => '', ],
            'edit' => [ 'content_fields' => '', ],
            'duplicate' => [ 'content_fields' => '', ],
            '_form' => [
                'content_fields' => (new SyntaxBuilder)->create($schemaArray, $this->commandObj->getMeta(), 'view-edit-content', true),
            ],
            '_common' => []
        ];

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace[$this->viewName]);

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/'.$this->solveName($this->commandObj->argument('name'), config('CrudDscaffold.app_name_rules.app_model_vars')).'/';
        $output_filename = $this->viewName.'.blade.php';

        //output(use OutputTrait)
        $this->outputPut( $output_path, $output_filename, $stub_compiled, $debug=false );
    }
}