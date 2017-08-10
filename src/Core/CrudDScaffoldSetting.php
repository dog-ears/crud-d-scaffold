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
        $this->command = $command;

        //get force option
        $this->force = $this->command->option('force');

        //get setting file path
        $file_path = $this->command->argument('filePath');

        $this->command->info( 'reading setting file... ('. $file_path. ')' );

        //setting.json - exist check
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

        //format check
        if( !array_key_exists('app_type',$this->setting_array) ){
            $this->setting_array['app_type'] = 'web';
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

            if( !array_key_exists('name', $model) ||
                !array_key_exists('display_name', $model) ||
                !array_key_exists('schemas', $model) ){

                    $this->command->error( 'json format error! model propaty is not correct   ('. $file_path. ')' );
                    return false;

            }
            if( !array_key_exists('use_soft_delete', $model) ){
                $model['use_soft_delete'] = 'false';
            }
            if( !array_key_exists('has_many', $model) ){
                $model['has_many'] = [];
            }

            foreach( $model['schemas'] as &$schema){

                if( !array_key_exists('name', $schema) ||
                    !array_key_exists('display_name', $schema) ){
                    
                        $this->command->error( 'json format error! schema propaty is not correct   ('. $file_path. ')' );
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
                }
                if( !array_key_exists('belongsto_column', $schema) ){
                    $schema['belongsto_column'] = '';
                }

            }unset($schema);
        }unset($model);
        return true;
    }

}