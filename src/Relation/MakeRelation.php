<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Relation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use dogears\CrudDscaffold\Commands\ScaffoldMakeCommand;
use dogears\CrudDscaffold\Stubs\StubController;
use dogears\CrudDscaffold\Traits\MakerTrait;
use dogears\CrudDscaffold\Traits\NameSolverTrait;
use dogears\CrudDscaffold\Traits\OutputTrait;

class MakeRelation {
    use MakerTrait,NameSolverTrait,OutputTrait;

    protected $files;
    protected $commandObj;
    protected $debug;

    public function __construct($command, Filesystem $files)
    {
        $this->files = $files;
        $this->commandObj = $command;
        $this->debug = false;
        $this->start();
    }

    protected function start()
    {
        //short cut
        $this->model_A_name = $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.app_model_class'));
        $this->model_B_name = $this->solveName($this->commandObj->argument('model_B'), config('CrudDscaffold.app_name_rules.app_model_class'));

        $this->validate();
        $this->editModel();
        $this->editView();
    }



    protected function validate(){

        $error = false;

        $this->model_A_tablename = $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.app_migrate_tablename'));
        $this->model_B_tablename = $this->solveName($this->commandObj->argument('model_B'), config('CrudDscaffold.app_name_rules.app_migrate_tablename'));
        $this->model_B_columnname = $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.name_name')). '_id';
        
        //check model exist
        if( !$this->files->exists('./app/'.$this->model_A_name.'.php' ) ){
            $this->commandObj->error( 'Model('. $this->model_A_name. ') is not found!');
            $error = true;
        }
        if( !$this->files->exists('./app/'.$this->model_B_name.'.php' ) ){
            $this->commandObj->error( 'Model('. $this->model_B_name. ') is not found!');
            $error = true;
        }

        //check model_B has model_A_id column
        if (!Schema::hasColumn($this->model_B_tablename, $this->model_B_columnname)){
            $this->commandObj->error( 'Model('. $this->model_B_name. ') should have '. $this->model_B_columnname. ' column!');
            $error = true;
        }

        //check model_A has "name" column
        if (!Schema::hasColumn($this->model_A_tablename, 'name')){
            $this->commandObj->info( '"name" column is not exist in '. $this->model_A_tablename. ' tables' );
            $this->relation_display_column = $this->entryRelationDisplayColumn();
        }else{
            $this->relation_display_column = 'name';
        }

        if( $error ){
            exit();
        }
    }

    protected function entryRelationDisplayColumn(){

        $relation_display_column = $this->commandObj->ask( 'Please input column name shown in index page.(If you want to cancel, enter "cancel")' );

        if($relation_display_column == 'cancel'){
            $this->commandObj->info( 'make:relation canceled!' );
            exit();
        }else{

            if (!Schema::hasColumn($this->model_A_tablename, $relation_display_column)){
                $this->commandObj->error( 'Column "'. $relation_display_column. '" is not exist in '.$this->model_A_tablename. ' tables' );
                $this->entryRelationDisplayColumn();
            }else{
                return $relation_display_column;
            }
        }
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
        $pattern = '#(class '. $this->model_A_name. ' extends Model\s*{)(([^{}]*{[^}]*?}[^{}]*?)*)(.*?)(\s*})#s';       //append at block end
        $replacement = '\1\2\4'.$stub_compiled.'\5';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug = $this->debug );
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
        $pattern = '#(class '. $this->model_B_name. ' extends Model\s*{)(([^{}]*{[^}]*?}[^{}]*?)*)(.*?)(\s*})#s';       //append at block end
        $replacement = '\1\2\4'.$stub_compiled.'\5';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug = $this->debug );
    }

    protected function editModel_modelB2(){

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/relation/';
        $stub_filename = 'model_B_add2.stub';

        //set custom replace
        $custom_replace = [
            'relation_display_column' => $this->relation_display_column,
        ];

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/';
        $output_filename = $this->model_B_name.'.php';

        //replace word
        $pattern = '#(public function __construct\(array \$attributes = array\(\)\)\s*{)(([^{}]*{[^}]*?}[^{}]*?)*)(.*?)(\s*})#s';       //append at block end
        $replacement = '\1\2\4'.$stub_compiled.'\5';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug = $this->debug );

    }



    protected function editView(){

        $this->editView_index();
        $this->editView_show();
        $this->editView_form();
    }

    protected function editView_index(){

        //(i) <td>{{$apple->apple_type_id}}</td> => <td>{{$apple->appleType->[relation_display_column]}}</td>

        //get output_path and filename
        $output_path = './resources/views/'. $this->solveName($this->commandObj->argument('model_B'), config('CrudDscaffold.app_name_rules.app_route')). '/';
        $output_filename = 'index.blade.php';

        //replace word
        $pattern = '#^(\s*)<td>{{\$'.
                    $this->solveName($this->commandObj->argument('model_B'), config('CrudDscaffold.app_name_rules.app_model_var')).'->'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.name_name')).'_id}}</td>$#m';
        $replacement = '\1<td>{{$'.$this->solveName($this->commandObj->argument('model_B'), config('CrudDscaffold.app_name_rules.app_model_var')).'->'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.app_model_var')).'->'. $this->relation_display_column. '}}</td>';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug = $this->debug );

        //(ii) apple_type_id => apple_types.name

        //replace word
        $pattern = '#'.$this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.name_name')).'_id#m';
        $replacement = $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.app_migrate_tablename')). '.'. $this->relation_display_column;

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug = $this->debug );

        //(iii) APPLE_TYPE_ID => APPLE_TYPE_NAME

        //replace word
        $pattern = '#'.$this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.NAME_NAME')).'_ID#m';
        $replacement = $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.NAME_NAME')).'_'. mb_strtoupper($this->relation_display_column);

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug = $this->debug );
    }

    protected function editView_show(){

        //get output_path and filename
        $output_path = './resources/views/'. $this->solveName($this->commandObj->argument('model_B'), config('CrudDscaffold.app_name_rules.app_route')). '/';
        $output_filename = 'show.blade.php';

        //replace word
        $pattern = '#(.*<label for=")'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.name_name')).'_id">'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.NAME_NAME')).'_ID</label>(.*)<p class="form-control-static">{{\$'.
                    $this->solveName($this->commandObj->argument('model_B'), config('CrudDscaffold.app_name_rules.app_model_var')).'->'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.name_name')).'_id}}</p>(.*)#s';
        $replacement = '\1'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.name_name')).'_'. $this->relation_display_column. '">'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.NAME_NAME')).'_'. mb_strtoupper($this->relation_display_column). '</label>\2<p class="form-control-static">{{$'.
                    $this->solveName($this->commandObj->argument('model_B'), config('CrudDscaffold.app_name_rules.app_model_var')).'->'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.app_model_var')).'->'. $this->relation_display_column. '}}</p>\3';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug = $this->debug );
    }

    protected function editView_form(){

        //get output_path and filename
        $output_path = './resources/views/'. $this->solveName($this->commandObj->argument('model_B'), config('CrudDscaffold.app_name_rules.app_route')). '/';
        $output_filename = '_form.blade.php';

        //replace word
        $pattern = '#(.*)<label for="'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.name_name')).'_id-field">(.*)_id</label>(.*){!! Form::text\("'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.name_name')).'_id", null,(.*)#s';
        $replacement = '\1<label for="'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.name_name')).'_id-field">\2_'. $this->relation_display_column. '</label>\3{!! Form::select("'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.name_name')).'_id", $list["'.
                    $this->solveName($this->commandObj->argument('model_A'), config('CrudDscaffold.app_name_rules.app_model_class')).'"], null,\4';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug = $this->debug );
    }
}