<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Core;

use Illuminate\Filesystem\Filesystem;

use dogears\CrudDscaffold\Commands\CrudDscaffoldSetupCommand;

class CrudDscaffoldSetting 
{
    private $files;     /* Filesystem */
    private $command;   /* CrudDscaffoldSetupCommand */
    public $setting_array;   /* array */
    public $force;

    public function __construct( Filesystem $files )
    {
        $this->files = $files;
    }



    public function loadSettingFromCommand( CrudDscaffoldSetupCommand $command )
    {
        
        // set data
        $this->command = $command;
        $this->force = $this->command->option('force');
        $file_path = $this->command->argument('filePath');

        $this->command->info( 'reading setting file... ('. $file_path. ')' );

        // setting.json - exist check
        if( !$this->files->exists( $file_path ) ){
            $this->command->error( 'setting file is not found ('. $file_path. ')' );
            exit();
        }

        //setting.json - ext check
        if( mb_substr($file_path, -5) !== '.json' ){
            $this->command->error( 'setting file must be json file ('. $file_path. ')' );
            exit();
        }



        //delete comment
        $data = $this->files->get( $file_path );
        $data = preg_replace ( '#/\*[^\*]*\*/#' , '' , $data );

        //load setting.json
        $this->setting_array = json_decode( $data, true  ) ;

        //parse check
        if( !$this->setting_array ){
            $this->command->error( 'json parse error! check your setting file  ('. $file_path. ')' );
            exit();
        }

        //check Json Format
        if( !$this->checkFormatOfSettingJson( $file_path ) ){
            exit();
        }

        $this->command->info( 'reading setting file done.' );
    }



    private function checkFormatOfSettingJson( $file_path ){

        if( !array_key_exists('app_type',$this->setting_array) ){
            $this->setting_array['app_type'] = 'web';
        }
        if( !array_key_exists('use_laravel_auth',$this->setting_array) ){
            $this->setting_array['use_laravel_auth'] = 'false';
        }
        if( !array_key_exists('models',$this->setting_array) ){
            $this->command->error( 'json format error! models is not found  ('. $file_path. ')' );
            return false;
        }
        if( count($this->setting_array['models']) === 0 ){
            $this->command->error( 'json format error! models have no child  ('. $file_path. ')' );
            return false;
        }

        foreach( $this->setting_array['models'] as &$model ){

            // required property
            if( !array_key_exists('name', $model) ||
                !array_key_exists('display_name', $model) ||
                !array_key_exists('schemas', $model) ){

                    $this->command->error( 'json format error! model property is not correct   ('. $file_path. ')' );
                    return false;

            }
            if( !array_key_exists('use_soft_delete', $model) ){
                $model['use_soft_delete'] = 'false';
            }
            if( !array_key_exists('belongsToMany', $model) ){
                $model['belongsToMany'] = '';
            }

            if( $this-> setting_array["use_laravel_auth"] === "true" && $model['name'] === "user" ){

                //add property
                $model['use_laravel_auth'] = "true";
            }

            foreach( $model['schemas'] as &$schema){

                /* Don't write property [ name', 'email', 'password ] In model laravel Auth "user"  */
                if( $this-> setting_array["use_laravel_auth"] === "true" && $model['name'] === "user" ){
    
                    // unable to use default schema
                    if( $schema["name"] === "name" || $schema["name"] === "email" || $schema["name"] === "password" ){

                        $this->command->error( 'User model with laravel Auth cannot have schema "name" or "email" or "password" ('. $file_path. ')' );
                        return false;
                    }
                }

                // required property
                if( !array_key_exists('name', $schema) ||
                    !array_key_exists('display_name', $schema) ){
                    
                        $this->command->error( 'json format error! schema property is not correct   ('. $file_path. ')' );
                        return false;

                }
                if( !array_key_exists('type', $schema) ){
                    $schema['type'] = 'string';
                }
                if( !array_key_exists('input_type', $schema) ){
                    $schema['input_type'] = 'text';
                }
                if( !array_key_exists('faker_type', $schema) ){
                    $schema['faker_type'] = '';
                }
                if( !array_key_exists('nullable', $schema) ){
                    $schema['nullable'] = 'false';
                }
                if( !array_key_exists('show_in_list', $schema) ){
                    $schema['show_in_list'] = 'true';
                }
                if( !array_key_exists('show_in_detail', $schema) ){
                    $schema['show_in_detail'] = 'true';
                }
                if( !array_key_exists('belongsto', $schema) ){
                    $schema['belongsto'] = '';
                }else{

                    if($schema['belongsto'] !== ""){     // case -  $schema has belongsto

                        //target model exist check
                        $error = true;
                        foreach( $this-> setting_array["models"] as &$target_model ){
                            if( $target_model["name"] === $schema['belongsto'] ){
                                $error = false;

                                // belongsto_column exist check
                                if( !array_key_exists('belongsto_column', $schema) || $schema['belongsto_column'] == "" ){
                                    $this->command->error( 'schema with belongsto needs belongsto_column property ('. $file_path. ')' );
                                    return false;
                                }
                                
                                // check target_model has column same as belongsto_column
                                $result_array = array_filter ( $target_model["schemas"], function($s) use($schema){
                                    return $schema['belongsto_column'] === $s['name'];
                                });
                                if( !count($result_array) ){
                                    
                                    // pass check if using laravel auth and belongsto_column is name
                                    if( $this-> setting_array["use_laravel_auth"] !== "true" || $schema['belongsto_column'] !== "name" ){
                                    
                                        $this->command->error( 'target_model('. $target_model["name"]. ') need column ('. $schema['belongsto_column']. ') ('. $file_path. ')' );
                                        return false;
                                    }
                                }

                                //add has_many data
                                $target_model["has_many"][] = $model["name"];
                            }
                        }unset($target_model);
                        if($error){
                            $this->command->error( 'belongsto target model ('. $schema['belongsto'] .') is not exitst! ('. $file_path. ')' );
                            return false;
                        }
                    }
                }
                if( !array_key_exists('belongsto_column', $schema) ){
                    $schema['belongsto_column'] = '';
                }

            }unset($schema);
        }unset($model);



        if( array_key_exists('pivots',$this->setting_array) ){

            foreach( $this->setting_array['pivots'] as &$pivot ){

                //add belongstomany to model
                foreach( $this->setting_array['models'] as &$model ){

                    $pivot_schemas = array();
                    foreach( $pivot['schemas'] as $schema ){
                        $pivot_schemas[] = NameResolver::solveName($schema['name'], 'name_name');
                    }unset($schema);

                    $schemas_implode = "'" . implode("','", $pivot_schemas) . "'";
                    $schemas_implode = str_replace("''", "", $schemas_implode);

                    if( $model['name'] === $pivot['parentModel'] ){

                        $model['belongstomany'][] = [
                            "name" => $pivot['childModel'],
                            "column" => $pivot['childModel_column'],
                            "schemas" => $pivot['schemas'],
                            "schemas_implode" => $schemas_implode,
                            "use_soft_delete" => $pivot['use_soft_delete']
                        ];

                    }elseif( $model['name'] === $pivot['childModel'] ){

                        $model['belongstomany'][] = [
                            "name" => $pivot['parentModel'],
                            "column" => $pivot['parentModel_column'],
                            "schemas" => $pivot['schemas'],
                            "schemas_implode" => $schemas_implode,
                            "use_soft_delete" => $pivot['use_soft_delete']
                        ];
                    }
                }unset($model);

                // add name property
                $rerated_models = array();
                $rerated_models[] = NameResolver::solveName($pivot['parentModel'], 'name_name');
                $rerated_models[] = NameResolver::solveName($pivot['childModel'], 'name_name');
                sort($rerated_models);
                $pivot['name'] = implode( '_', $rerated_models );

                // add basic schema
                $pivot['schemas'][] = [
                    "name" => NameResolver::solveName($pivot['parentModel'], 'name_name'). '_id',
                    "type" => "integer",
                    "input_type" => "null",
                    "faker_type" => "numberBetween(1,30)",
                    "nullable" => "true",
                    "display_name" => "parent_id",
                    "show_in_list" => "false",
                    "show_in_detail" => "false"
                ];
                $pivot['schemas'][] = [
                    "name" => NameResolver::solveName($pivot['childModel'], 'name_name'). '_id',
                    "type" => "integer",
                    "input_type" => "null",
                    "faker_type" => "numberBetween(1,30)",
                    "nullable" => "true",
                    "display_name" => "parent_id",
                    "show_in_list" => "false",
                    "show_in_detail" => "false"
                ];

            }unset($pivot);
        }else{
            $this->setting_array['pivots'] = [];
        }

        foreach( $this->setting_array['models'] as &$model ){

            if( !array_key_exists('belongstomany', $model) ){
                $model['belongstomany'] = [];
            }

        }unset($model);

        return true;
    }
}