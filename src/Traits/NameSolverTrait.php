<?php

namespace dogears\L5scaffold\Traits;

trait NameSolverTrait {

    public function nameSolverInit() {

        // Setup configs and names
        $this->name_config['controller_name'] = $this->solveName('NameNames'); //ex) AppleTypes
        $this->name_config['model_name'] = $this->solveName('NameName'); //ex) AppleType
        $this->name_config['migration_name'] = $this->solveName('name_names'); //ex) apple_types
        $this->name_config['migration_class_name'] = $this->solveName('NameNames'); //ex) AppleTypes
        $this->name_config['table_name'] = $this->solveName('name_names'); //ex) apple_types
        $this->name_config['seeder_name'] = $this->solveName('NameNames'); //ex) AppleTypes
        $this->name_config['view_name'] = $this->solveName('nameNames'); //ex) appleTypes
        $this->name_config['route_name'] = $this->solveName('nameNames'); //ex) appleTypes

    }

    /**
     * Get the Name.
     *
     * @param string $config
     * @return mixed
     * @throws \Exception
     */
    public function solveName($config = 'NameName', $input = null){
        $names = [];

        if( $input ){
            $args_name = $input;
        }else{
            $args_name = $this->argument('name');
        }

        $names['nameName'] = camel_case( str_singular($args_name) );
        $names['NameName'] = studly_case( str_singular($args_name) );
        $names['nameNames'] = camel_case( str_plural($args_name) );
        $names['NameNames'] = studly_case( str_plural($args_name) );
        $names['name_name'] = str_replace('__', '_', snake_case( str_singular($args_name) ) );
        $names['name_names'] = str_replace('__', '_', snake_case( str_plural($args_name) ) );

        if (!isset($names[$config])) {
            throw new \Exception("Position name is not found");
        };

        return $names[$config];
    }

    /**
     * Get the Name Test.
     *
     * @param string $config
     */
    public function solveName_test($config){

        $test_word = array(
                //"appletype",  //無理
                //"APPLETYPE",  //無理
    
                "apple_type",
                "apple_Type",
                "Apple_Type",
                //"APPLE_TYPE", //厳しい
    
                "appleType",
                //"Appletype",  //無理
                "AppleType",
    
                //"appletypes",  //無理
                //"APPLETYPES",  //無理
    
                "apple_types",
                "apple_Types",
                "Apple_Types",
                //"APPLE_TYPES", //厳しい
    
                "appleTypes",
                //"Appletypes", //無理
                "AppleTypes",
                );
                
        $this->info( 'solver type is '. $config );
        foreach($test_word as $value){
            $this->info( $value.' -> '.$this->solveName($config, $value) );
        }
    }
}