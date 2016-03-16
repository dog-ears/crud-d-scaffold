<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\L5scaffold\Makes;

use Illuminate\Filesystem\Filesystem;
use dogears\L5scaffold\Commands\ScaffoldMakeCommand;
use dogears\L5scaffold\Stubs\StubController;
use dogears\L5scaffold\Traits\MakerTrait;
use dogears\L5scaffold\Traits\NameSolverTrait;
use dogears\L5scaffold\Traits\OutputTrait;

class MakeLayout {
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
        //(i)layout --------------------------------------------------

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_layout/';
        $stub_filename = 'layout.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/';
        $output_filename = 'layout.blade.php';

        //output(use OutputTrait)
        $this->outputPutWithoutAlert( $output_path, $output_filename, $stub_compiled, $message_success='view_layout_layout created successfully', $debug=false );



        //(ii)error --------------------------------------------------

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_layout/';
        $stub_filename = 'error.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/';
        $output_filename = 'error.blade.php';

        //output(use OutputTrait)
        $this->outputPutWithoutAlert( $output_path, $output_filename, $stub_compiled, $message_success='view_layout_error created successfully', $debug=false );



        //(iii)navi - put --------------------------------------------------

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_layout/';
        $stub_filename = 'navi.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/';
        $output_filename = 'navi.blade.php';

        //output(use OutputTrait)
        $this->outputPutWithoutAlert( $output_path, $output_filename, $stub_compiled, $message_success='view_layout_navi created successfully', $debug=false );

        //(iv)navi - add --------------------------------------------------

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_layout/';
        $stub_filename = 'navi_add.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/';
        $output_filename = 'navi.blade.php';

        //replace word
        $pattern = '#(<ul id="app_navi" class="dropdown-menu" role="menu">)(.*?)(\s*?</ul>)#s';
        $replacement = '\1\2'.$stub_compiled.'\3';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='view_layout_navi updated successfully', $debug=false );
    }
}