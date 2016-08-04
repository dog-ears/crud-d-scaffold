<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Stubs;

use Illuminate\Filesystem\Filesystem;
use dogears\CrudDscaffold\Commands\ScaffoldMakeCommand;
use dogears\CrudDscaffold\Migrations\SchemaParser;
use dogears\CrudDscaffold\Migrations\SyntaxBuilder;
use dogears\CrudDscaffold\Traits\NameSolverTrait;

class StubController {

    use NameSolverTrait;

    protected $commandObj;
    protected $files;
    protected $stub_pathname;
    protected $schema_repalce_type;
    protected $custom_replace;

    public function __construct($command, Filesystem $files, $stub_pathname, $schema_repalce_type = null, $custom_replace = null)
    {
        $this->commandObj = $command;
        $this->files = $files;
        $this->stub_pathname = $stub_pathname;
        $this->schema_repalce_type = $schema_repalce_type;
        $this->custom_replace = $custom_replace;
    }

    public function getCompiled(){

        //get stab
        $stub = $this->files->get($this->stub_pathname);
        
        //compile app name
        $this->compileAppName($stub);
        
        //compile schema
        if( $this->schema_repalce_type ){
            $this->compileSchema($stub, $this->schema_repalce_type);
        }

        //custom compile
        if( $this->custom_replace ){
            $this->compileCustomReplace($stub);
        }

        //delete by option compile
        $this->compileDeleteByOption($stub);

        return $stub;
    }

    protected function compileAppName(&$stub){

        //get command name
        $commandObjNames = explode('\\',get_class($this->commandObj));
        $this->commandObjName = end( $commandObjNames );

        //get config
        $app_name_rules = config('CrudDscaffold.app_name_rules');

        if( $this->commandObjName === 'MakeRelationCommand' || $this->commandObjName === 'DeleteRelationCommand' ){ //for RelationCommand

            foreach($app_name_rules as $keyword => $type){
    
                $stub = str_replace(
                    '{{model_A.'.$keyword.'}}',
                    $this->solveName( $this->commandObj->argument('model_A'), $type ),
                    $stub
                );
                $stub = str_replace(
                    '{{model_B.'.$keyword.'}}',
                    $this->solveName( $this->commandObj->argument('model_B'), $type ),
                    $stub
                );
            }

        }else{  //for sccafoldCommand
            
            foreach($app_name_rules as $keyword => $type){
    
                $stub = str_replace(
                    '{{'.$keyword.'}}',
                    $this->solveName( $this->commandObj->argument('name'), $type ),
                    $stub
                );
            }
        }

        return $this;
    }

    protected function compileSchema(&$stub, $type='migration')
    {
        //get schema
        if ($schema = $this->commandObj->option('schema')) {
            $schema = (new SchemaParser)->parse($schema);
        }

        //schema in each case
        if($type === 'migration'){

            // Create migration fields
            $schema = (new SyntaxBuilder)->create($schema, $this->commandObj->getMeta());
            $stub = str_replace(['{{schema_up}}', '{{schema_down}}'], $schema, $stub);

        } else if($type === 'factory'){

            // Create controllers fields
            $schema = (new SyntaxBuilder)->create($schema, $this->commandObj->getMeta(), 'factory');
            $stub = str_replace('{{schema_factory}}', $schema, $stub);

        } else if($type === 'model'){

            // Create mass assignment fields in model
            $schema = (new SyntaxBuilder)->create($schema, $this->commandObj->getMeta(), 'model');
            $stub = str_replace('{{schema_model}}', $schema, $stub);

        } else {}

        return $this;
    }

    protected function compileCustomReplace(&$stub){

        foreach( $this->custom_replace as $before => $after ){
            $stub = str_replace('{{'.$before.'}}', $after, $stub);
        }

        return $this;

    }
    
    protected function compileDeleteByOption(&$stub){

        $pattern = '#{{if\((.*?)\):}}([^{]*?){{endif;}}#';  //{{if:[option]):}}...{{endif;}}
        if( preg_match_all( $pattern, $stub, $m ) ){

            for($i=0;$i<count($m[0]);$i++){

                //check option exists or not
                if( array_key_exists( $m[1][$i], $this->commandObj->option() ) ){   //case. option exists.

                    if( $this->commandObj->option($m[1][$i]) ){ //case option is true.

                        //show line
                        $stub = str_replace ( $m[0][$i] , $m[2][$i] , $stub);

                    }else{

                        //hide line
                        $stub = str_replace ( $m[0][$i] , '' , $stub);
                    }

                }else{   //case. option do not exists.

                    $this->commandObj->error('option ['. $m[1][$i]. '] is not exist. Please check spell in stub.');
                }
            }
        }
        return $this;
    }
}










