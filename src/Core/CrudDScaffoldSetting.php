<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Core;

use Illuminate\Filesystem\Filesystem;
use dogears\CrudDscaffold\Commands\CrudDscaffoldSetupCommand;
use dogears\CrudDscaffold\Core\NameResolver;

class CrudDscaffoldSetting
{
    private $files;     /* Filesystem */
    private $command;   /* CrudDscaffoldSetupCommand */
    public $setting_array;   /* array */
    public $force;

    public $setting_array_format = [
        'app_type' => [
            'type' => 'text',
            'default' => 'web',
            'allowed' => ['web','api'],
        ],
        'use_laravel_auth' => [
            'type' => 'text',
            'default' => 'false',
            'allowed' => ['true','false'],
        ],
        'models' => [
            'type' => 'array',
            'child' => [
                'name' => [
                    'type' => 'text',
                    'required' => true,
                ],
                'display_name' => [
                    'type' => 'text',
                    'required' => true,
                ],
                'use_soft_delete' => [
                    'type' => 'text',
                    'default' => 'false',
                    'allowed' => ['true','false'],
                ],
                'schemas' => [
                    'type' => 'array',
                    'child' => [
                        'name' => [
                            'type' => 'text',
                            'required' => true,
                        ],
                        'type' => [
                            'type' => 'text',
                            'default' => 'string',
                            'allowed' => ['string','integer'],
                        ],
                        'input_type' => [
                            'type' => 'text',
                            'default' => 'text',
                            'allowed' => ['text','textarea'],
                        ],
                        'varidate' => [
                            'type' => 'text',
                            'default' => '',
                        ],
                        'faker_type' => [
                            'type' => 'text',
                            'default' => 'word()',
                            'example' => ['randomDigit()', 'randomNumber()', 'numberBetween(1,30)', 'word()', 'sentence()', 'paragraph()', 'text()', 'name()', 'address()', 'date("Y-m-d","now")', 'safeEmail()', 'password()'],
                        ],
                        'nullable' => [
                            'type' => 'text',
                            'default' => 'false',
                            'allowed' => ['true','false'],
                        ],
                        'display_name' => [
                            'type' => 'text',
                            'required' => true,
                        ],
                        'show_in_list' => [
                            'type' => 'text',
                            'default' => 'true',
                            'allowed' => ['true','false'],
                        ],
                        'show_in_detail' => [
                            'type' => 'text',
                            'default' => 'true',
                            'allowed' => ['true','false'],
                        ],
    					'belongsto' => [
                            'type' => 'text',
                            'default' => '',
                        ],
    					"belongsto_column" => [
                            'type' => 'text',
                            'default' => '',
                        ]
                    ]
                ]
            ]
        ],
        'pivots' => [
            'type' => 'array',
            'child' => [
                'parentModel' => [
                    'type' => 'text',
                    'required' => true,
                ],
                'parentModel_column' => [
                    'type' => 'text',
                    'required' => true,
                ],
                'childModel' => [
                    'type' => 'text',
                    'required' => true,
                ],
                'childModel_column' => [
                    'type' => 'text',
                    'required' => true,
                ],
                'use_soft_delete' => [
                    'type' => 'text',
                    'default' => 'false',
                    'allowed' => ['true','false'],
                ],
                'schemas' => [
                    'type' => 'array',
                    'child' => [
                        'name' => [
                            'type' => 'text',
                            'required' => true,
                        ],
                        'type' => [
                            'type' => 'text',
                            'default' => 'string',
                            'allowed' => ['string','integer'],
                        ],
                        'input_type' => [
                            'type' => 'text',
                            'default' => 'text',
                            'allowed' => ['text','textarea'],
                        ],
                        'varidate' => [
                            'type' => 'text',
                            'default' => '',
                        ],
                        'faker_type' => [
                            'type' => 'text',
                            'default' => 'word()',
                            'example' => ['randomDigit()', 'randomNumber()', 'numberBetween(1,30)', 'word()', 'sentence()', 'paragraph()', 'text()', 'name()', 'address()', 'date("Y-m-d","now")', 'safeEmail()', 'password()'],
                        ],
                        'nullable' => [
                            'type' => 'text',
                            'default' => 'false',
                            'allowed' => ['true','false'],
                        ],
                        'display_name' => [
                            'type' => 'text',
                            'required' => true,
                        ],
                        'show_in_list' => [
                            'type' => 'text',
                            'default' => 'true',
                            'allowed' => ['true','false'],
                        ],
                        'show_in_detail' => [
                            'type' => 'text',
                            'default' => 'true',
                            'allowed' => ['true','false'],
                        ],
    					'belongsto' => [
                            'type' => 'text',
                            'default' => '',
                        ],
    					"belongsto_column" => [
                            'type' => 'text',
                            'default' => '',
                        ]
                    ]
                ]
            ]
        ]
    ];

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



        //load file and delete comment
        $data = $this->files->get( $file_path );
        $data = preg_replace ( '#/\*[^\*]*\*/#' , '' , $data );
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

        $this->setting_array = $this->checkAllowedAndSetDefaultWrapper();

        // special process from here
        foreach( $this->setting_array['models'] as &$model ){

            // (1) add use_laravel_auth to user model
            if( $this-> setting_array["use_laravel_auth"] === "true" && $model['name'] === "user" ){

                //add property
                $model['use_laravel_auth'] = "true";
            }

            foreach( $model['schemas'] as &$schema){

                // (2) Don't write property [ name', 'email', 'password ] In model laravel Auth "user"
                if( $this-> setting_array["use_laravel_auth"] === "true" && $model['name'] === "user" ){
    
                    // unable to use default schema
                    if( $schema["name"] === "name" || $schema["name"] === "email" || $schema["name"] === "password" ){

                        $this->command->error( 'User model with laravel Auth cannot have schema "name" or "email" or "password" ('. $file_path. ')' );
                        return false;
                    }
                }

                // (3) varidate
                $varidate_array = explode('|', $schema["varidate"]);

                // (3-1) add required if nullable is false
                if( $schema["nullable"] == 'false' ){
                    if( !in_array('required',$varidate_array) ){
                        $varidate_array[] = 'required';
                    }
                }
                $varidate_array_for_register = $varidate_array;
                // (3-2) unique
                $unique_flag = false;
                for($i=0;$i<count($varidate_array);$i++){
                    if(strpos($varidate_array[$i],'unique') !== false){
                        unset($varidate_array[$i]);
                        unset($varidate_array_for_register[$i]);
                        $varidate_array[] = 'unique'.":".NameResolver::solveName($model['name'], 'name_names').",".NameResolver::solveName($schema['name'], 'name_name').",'.($".NameResolver::solveName($model['name'], 'name_name')."?$".NameResolver::solveName($model['name'], 'name_name')."->id:'')";
                        $varidate_array_for_register[] = 'unique'.":".NameResolver::solveName($model['name'], 'name_names').",". NameResolver::solveName($schema['name'], 'name_name');
                        $unique_flag = true;
                    }
                }
                $schema["varidate"] = "'". implode('|',$varidate_array);

                if( array_key_exists('use_laravel_auth',$model) ){
                    $schema["varidate_for_register"] = "'". implode('|',$varidate_array_for_register). "'";
                }

                if(!$unique_flag){
                    $schema["varidate"] .= "'";
                }

                // (4) belongsto
                if( array_key_exists('belongsto', $schema) && $schema['belongsto'] !== "" ){

                    // (4-1) belongsto_column exist check.
                    if( !array_key_exists('belongsto_column',$schema) || $schema['belongsto_column'] == '' ){
                        $this->command->error( 'schema with belongsto needs belongsto_column property ('. $file_path. ')' );
                        return false;
                    }

                    // (4-2) $belongsto_target_model exist check
                    $error = true;
                    foreach( $this-> setting_array["models"] as &$target_model ){
                        if( $target_model["name"] === $schema['belongsto'] ){
                            $error = false;

                            // (4-3) belongsto_column exist check in $belongsto_target_model
                            $result_array = array_filter ( $target_model["schemas"], function($s) use($schema){
                                return $schema['belongsto_column'] === $s['name'];
                            });
                            if( !count($result_array) ){
                        
                                // (4-3-1) In case of using laravel auth and belongsto_column is 'name', It's OK
                                if( $this-> setting_array["use_laravel_auth"] !== "true" || $schema['belongsto_column'] !== "name" ){
                                
                                    $this->command->error( 'target_model('. $target_model["name"]. ') need column ('. $schema['belongsto_column']. ') ('. $file_path. ')' );
                                    return false;
                                }
                            }

                            // (4-4) add has_many data
                            $target_model["has_many"][] = $model["name"];
                        }
                    }unset($target_model);
                    if($error){
                        $this->command->error( 'belongsto target model ('. $schema['belongsto'] .') is not exitst! ('. $file_path. ')' );
                        return false;
                    }
                }
            }unset($schema);
        }unset($model);

        // (5) set belongstomany to each models
        foreach( $this->setting_array['pivots'] as &$pivot ){

            // (5-1) get $schemas_implode
            $pivot_schemas = array_column($pivot['schemas'], 'name');
            foreach( $pivot_schemas as &$schema ){
                $schema = NameResolver::solveName($schema, 'name_name');
            }unset($schema);
            $schemas_implode = "'" . implode("','", $pivot_schemas) . "'";
            $schemas_implode = str_replace("''", "", $schemas_implode);

            // (5-2) get parent_model_key and child_model_key
            $parent_model_key = array_search($pivot['parentModel'], array_column($this->setting_array['models'], 'name'));
            $child_model_key = array_search($pivot['childModel'], array_column($this->setting_array['models'], 'name'));

            // (5-3) add name property for table name
            $rerated_models = array();
            $rerated_models[] = NameResolver::solveName($pivot['parentModel'], 'name_name');
            $rerated_models[] = NameResolver::solveName($pivot['childModel'], 'name_name');
            sort($rerated_models);
            $pivot['name'] = implode( '_', $rerated_models );

            // (5-4) varidate
            foreach( $pivot['schemas'] as &$schema ){

                $varidate_array = explode('|', $schema["varidate"]);
                
                // (5-4-1) add required if nullable is false
                if( $schema["nullable"] == 'false' ){
                    if( !in_array('required',$varidate_array) ){
                        $varidate_array[] = 'required';
                    }
                }
                // (5-4-2) remove unique
                for($i=0;$i<count($varidate_array);$i++){
                    if(strpos($varidate_array[$i],'unique') !== false){
                        unset($varidate_array[$i]);
                    }
                }
                $schema["varidate"] = "'". implode('|',$varidate_array);
                if(!$unique_flag){
                    $schema["varidate"] .= "'";
                }
            }

            // (5-5) add belongstomany to parent model
            $this->setting_array['models'][$parent_model_key]['belongstomany'][] = [
                "name" => $pivot['childModel'],
                "display_name" => $this->setting_array['models'][$child_model_key]['display_name'],
                "use_soft_delete" => $pivot['use_soft_delete'],
                "column" => $pivot['childModel_column'],
                "schemas" => $pivot['schemas'],
                "schemas_implode" => $schemas_implode,
            ];

            // (5-6) add belongstomany to child model
            $this->setting_array['models'][$child_model_key]['belongstomany'][] = [
                "name" => $pivot['parentModel'],
                "display_name" => $this->setting_array['models'][$parent_model_key]['display_name'],
                "column" => $pivot['parentModel_column'],
                "schemas" => $pivot['schemas'],
                "schemas_implode" => $schemas_implode,
                "use_soft_delete" => $pivot['use_soft_delete']
            ];

            // (5-7)add basic schema
            $pivot['schemas'][] = [
                "name" => NameResolver::solveName($pivot['parentModel'], 'name_name'). '_id',
                "type" => "integer",
                "input_type" => "null",
                "varidate" => "",
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
                "varidate" => "",
                "faker_type" => "numberBetween(1,30)",
                "nullable" => "true",
                "display_name" => "parent_id",
                "show_in_list" => "false",
                "show_in_detail" => "false"
            ];

        }unset($pivot);

        //set default to model belongstomany
        foreach( $this->setting_array['models'] as &$model ){
            if( !array_key_exists('belongstomany', $model) ){
                $model['belongstomany'] = [];
            }
        }unset($model);

        return true;
    }



    private function checkAllowedAndSetDefaultWrapper( $setting_array = null, $format = null ){

        if($setting_array === null){
            $setting_array = $this->setting_array;
        }
        if($format === null){
            $format = $this->setting_array_format;
        }

        // set default according to the format 
        foreach( $format as $format_key => $format_value){

            //check format
            if( !array_key_exists($format_key, $setting_array) ){
                $setting_value = null;
            }else{
                $setting_value = $setting_array[$format_key];
            }
            $setting_array[$format_key] = $this->checkAllowedAndSetDefault( $setting_value, $format[$format_key], $format_key );
        }
        return $setting_array;
    }



    private function checkAllowedAndSetDefault( $value, $format, $format_key ){

        if( $format['type'] === 'text' ){

            if( $value === null || $value === '' ){ // has no property or blank
    
                // required
                if( array_key_exists('required',$format) && $format['required'] === true ){
                    $this->command->error( 'json format error! '.$format_key.' is required.' );
                    exit();
                }
    
                // set default
                if( array_key_exists('default',$format) ){
                    return $format['default'];
                }
            }else{
    
                //check allowed
                if( array_key_exists('allowed',$format) ){
                    if( in_array( $value, $format['allowed'] ) === false ){
                        $this->command->error( 'json format error! '.$value.' is not allowed.select '.implode(' or ',$format['allowed']) );
                        exit();
                    }
                }
            }

        }elseif( $format['type'] === 'array' ){

            if( $value === null || $value === '' ){ // has no property or blank
                return [];
            }elseif( !is_array($value) ){
                $this->command->error( 'json format error! '.$format_key.' must be array.' );
                exit();
            }else{
                if( !$this->is_vector($value) ){
                    $this->command->error( 'json format error! '.$value.' must be simple array.' );
                    exit();
                }else{
                    foreach( $value as &$item ){
                        $item = $this->checkAllowedAndSetDefaultWrapper( $item , $format['child'] );
                    }unset($item);
                }
            }
        }
        return $value;
    }

    function is_vector(array $arr) {
        return array_values($arr) === $arr;
    }
}