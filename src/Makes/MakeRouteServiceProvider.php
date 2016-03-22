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

class MakeRouteServiceProvider {
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
        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/routeServiceProvider/';
        $stub_filename = 'app.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/Providers/';
        $output_filename = 'RouteServiceProvider.php';

        //replace word
        $pattern = '#(public function boot\(Router \$router\)\s*{)(([^{}]*{[^}]*?}[^{}]*?)*)(.*?)(\s*})#s';     //append at block end
        //$pattern = '#(.*)(public function boot\(Router \$router\)\n\s*\{\n)(.*?)(\s*}\n)(.*)#s';
        //$replacement = '\1\2\3'."\n".$stub_compiled.'\4\5';
        $replacement = '\1\2\4'. $stub_compiled. '\5';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug=false );
    }
}
