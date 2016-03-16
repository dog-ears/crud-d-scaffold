<?php
/**
 * Created by dog-ears
 */

namespace dogears\L5scaffold\Relation;

use Illuminate\Filesystem\Filesystem;
use dogears\L5scaffold\Commands\ScaffoldMakeCommand;
use dogears\L5scaffold\Stubs\StubController;
use dogears\L5scaffold\Traits\MakerTrait;
use dogears\L5scaffold\Traits\NameSolverTrait;
use dogears\L5scaffold\Traits\OutputTrait;

class MakeRelation {
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
        $this->commandObj->info('make');

        //short cut
        $this->model_A_name = $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.app_model_class'));
        $this->model_B_name = $this->solveName($this->commandObj->argument('model_B'), config('l5scaffold.app_name_rules.app_model_class'));

        $this->editModel();
        $this->editView();
    }



    protected function editModel(){

        $this->editModel_modelA();
        $this->editModel_modelB();
        $this->editModel_modelB2();
    }

    protected function editModel_modelA(){

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/relation/';
        $stub_filename = 'model_A_add.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/';
        $output_filename = $this->model_A_name.'.php';

        //replace word
        $pattern = '#(class '.$this->model_A_name.' extends Model\n{\n)(.*)(\n})#s';
        $replacement = '\1\2'.$stub_compiled.'\3';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='model_A updated successfully', $debug=false );
    }

    protected function editModel_modelB(){

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/relation/';
        $stub_filename = 'model_B_add.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/';
        $output_filename = $this->model_B_name.'.php';

        //replace word
        $pattern = '#(class '.$this->model_B_name.' extends Model\n{\n)(.*)(\n})#s';
        $replacement = '\1\2'.$stub_compiled.'\3';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='model_B updated successfully', $debug=false );
    }

    protected function editModel_modelB2(){

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/relation/';
        $stub_filename = 'model_B_add2.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/';
        $output_filename = $this->model_B_name.'.php';

        //replace word
        $pattern = '#(public function __construct\(array \$attributes = array\(\)\)\n\s*{)([^{}]*?)({.*?})*([^{}]*?)(\n\s*})#s';    //append at block end
        $replacement = '\1\2\3\4'. $stub_compiled. '\5';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='model_B2 updated successfully', $debug=false );
    }



    protected function editView(){

        $this->editView_index();
        $this->editView_show();
        $this->editView_form();
    }

    protected function editView_index(){

        //(i) <td>{{$apple->apple_type_id}}</td> => <td>{{$apple->appleType->name}}</td>

        //get output_path and filename
        $output_path = './resources/views/apples/';
        $output_filename = 'index.blade.php';

        //replace word
        $pattern = '#^(\s*)<td>{{\$'.
                    $this->solveName($this->commandObj->argument('model_B'), config('l5scaffold.app_name_rules.app_model_var')).'->'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.name_name')).'_id}}</td>$#m';
        $replacement = '\1<td>{{$'.$this->solveName($this->commandObj->argument('model_B'), config('l5scaffold.app_name_rules.app_model_var')).'->'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.app_model_var')).'->name}}</td>';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='', $debug=false );

        //(ii) apple_type_id => apple_types.name

        //replace word
        $pattern = '#'.$this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.name_name')).'_id#m';
        $replacement = $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.app_migrate_tablename')).'.name';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='', $debug=false );

        //(iii) APPLE_TYPE_ID => APPLE_TYPE_NAME

        //replace word
        $pattern = '#'.$this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.NAME_NAME')).'_ID#m';
        $replacement = $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.NAME_NAME')).'_NAME';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='View_index updated successfully', $debug=false );

    }

    protected function editView_show(){

        //get output_path and filename
        $output_path = './resources/views/apples/';
        $output_filename = 'show.blade.php';

        //replace word
        $pattern = '#(.*<label for=")'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.name_name')).'_id">'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.NAME_NAME')).'_ID</label>(.*)<p class="form-control-static">{{\$'.
                    $this->solveName($this->commandObj->argument('model_B'), config('l5scaffold.app_name_rules.app_model_var')).'->'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.name_name')).'_id}}</p>(.*)#s';
        $replacement = '\1'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.name_name')).'_name">'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.NAME_NAME')).'_NAME</label>\2<p class="form-control-static">{{$'.
                    $this->solveName($this->commandObj->argument('model_B'), config('l5scaffold.app_name_rules.app_model_var')).'->'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.app_model_var')).'->name}}</p>\3';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='View_show updated successfully', $debug=false );
    }

    protected function editView_form(){

        //get output_path and filename
        $output_path = './resources/views/apples/';
        $output_filename = '_form.blade.php';

        //replace word
        $pattern = '#(.*)<label for="'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.name_name')).'_id-field">(.*)_id</label>(.*){!! Form::text\("'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.name_name')).'_id", null,(.*)#s';
        $replacement = '\1<label for="'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.name_name')).'_id-field">\2_name</label>\3{!! Form::select("'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.name_name')).'_id", $list["'.
                    $this->solveName($this->commandObj->argument('model_A'), config('l5scaffold.app_name_rules.app_model_class')).'"], null,\4';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='View_form updated successfully', $debug=false );
    }
}