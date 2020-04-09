<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace DogEars\CrudDScaffold\MyClass;

use Illuminate\Filesystem\Filesystem;
use DogEars\CrudDScaffold\Commands\CrudDScaffoldSetupCommand;
use DogEars\CrudDScaffold\MyClass\Model;
use DogEars\CrudDScaffold\MyClass\Schema;

class Data
{
    private $files;     /* Filesystem */
    private $command;   /* CrudDScaffoldSetupCommand */
    public $relations;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct( CrudDScaffoldSetupCommand $command, Filesystem $files )
    {
        $this->command = $command;
        $this->files = $files;
        $this->relations = [];
    }
    public function loadData(){

        $file_path = $this->command->argument('filePath');
        $this->command->info( 'reading setting file... ('. $file_path. ')' );

        //setting.json - ext check
        if( mb_substr($file_path, -5) !== '.json' ){
            $this->command->error( 'setting file must be json file ('. $file_path. ')' );
            exit();
        }

        // setting.json - exist check
        if( !$this->files->exists( $file_path ) ){
            $this->command->error( 'setting file is not found ('. $file_path. ')' );
            exit();
        }

        //load file and delete comment
        $rawdata = $this->files->get( $file_path );
        $rawdata = preg_replace ( '#/\*[^\*]*\*/#' , '' , $rawdata );
        $this->json_data = json_decode( $rawdata, true  ) ;

        //parse check
        if( !$this->json_data ){
            $this->command->error( 'json parse error! check your setting file  ('. $file_path. ')' );
            exit();
        }else{
            $this->convertData();
        }
    }

    public function convertData(){
        $this->app_type = $this->json_data['app_type'];
        $this->use_laravel_auth = $this->json_data['use_laravel_auth'];
        $this->tool = $this->json_data['tool'];

        foreach( $this->json_data['models'] as $model ){
            $this->models[] = new Model($model);
        }

        $this->prepareRelationship();
    }

    public function prepareRelationship(){
        foreach( $this->models as $model ){
//echo('★---'.$model->name.' check start---★'."\n");
            if( !$model->is_pivot ){    // normal model
                foreach( $model->schemas as $schema ){
                    if( $schema->belongsto === '' ){ continue; }

                    $type = 'belongsTo';
                    $originalModel = $model;
                    $targetModel = $this->getModelByName($schema->belongsto);

                    $relation = new Relation($type, $originalModel, $targetModel);
                    $this->relations[] = $relation;
                    $model->relations[] = $relation;

                    $type = 'hasMany';
                    $originalModel = $this->getModelByName($schema->belongsto);
                    $targetModel = $model;

                    $relation = new Relation($type, $originalModel, $targetModel);
                    $this->relations[] = $relation;
                    $this->getModelByName($schema->belongsto)->relations[] = $relation;
                }
            }else{  // pivot model
                $type = 'belongsToMany';
                $originalModel = null;
                $targetModel = null;
                $pivotModel = $model;
                $pivotModelSchemas = [];
                foreach( $model->schemas as $schema ){
                    if($schema->belongsto === ''){
                        $pivotModelSchemas[]  = $schema;
                    }else{
                        if( $originalModel === null ){
                            $originalModel = $this->getModelByName($schema->belongsto);
                        }else{
                            $targetModel = $this->getModelByName($schema->belongsto);
                        }
                    }
                }
                $relation = new Relation($type, $originalModel, $targetModel, $pivotModel, $pivotModelSchemas);
                $this->relations[] = $relation;
                $originalModel->relations[] = $relation;

                $relation = new Relation($type, $targetModel, $originalModel,  $pivotModel, $pivotModelSchemas);
                $this->relations[] = $relation;
                $targetModel->relations[] = $relation;
            }
        }
    }

    public function getModelByName( $name ){
        $result = array_filter( $this->models, function($model) use($name){
            return $model->name === $name;
        });
        if( count($result)===0 ){
            throw new \Exception('getModelByName('.$name.') return no model!');
        }
        return array_values($result)[0];
    }
}