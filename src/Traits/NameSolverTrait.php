<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/
 
namespace dogears\CrudDscaffold\Traits;

trait NameSolverTrait {

    /**
     * solveName()
     * return $input formed with $type rule.
     * 
     * @param string $input,$type
     * @return string
     * @throws \Exception
     */
    public function solveName($input, $type){

        if( $type === 'nameName' ){
            $result = camel_case( str_singular($input) );
        }elseif( $type === 'NameName' ){
            $result = studly_case( str_singular($input) );
        }elseif( $type === 'nameNames' ){
            $result = camel_case( str_plural($input) );
        }elseif( $type === 'NameNames' ){
            $result = studly_case( str_plural($input) );
        }elseif( $type === 'name_name' ){
            $result = str_replace('__', '_', snake_case( str_singular($input) ) );
        }elseif( $type === 'name_names' ){
            $result = str_replace('__', '_', snake_case( str_plural($input) ) );
        }elseif( $type === 'NAME_NAME' ){
            $result = mb_strtoupper( str_replace('__', '_', snake_case( str_singular($input) ) ) );
        }elseif( $type === 'NAME_NAMES' ){
            $result = mb_strtoupper( str_replace('__', '_', snake_case( str_plural($input) ) ) );
        }else{
            throw new \Exception("NameSolver accept invalid type");
        }

        return $result;
    }

    /**
     * solveName_test()
     *
     * @param string $config
     */
    public function solveName_test($type){

        $test_words = array(
                //"appletype",  //It can't solve.
                //"APPLETYPE",  //It can't solve.
    
                "apple_type",
                "apple_Type",
                "Apple_Type",
                //"APPLE_TYPE", //It can't solve.
    
                "appleType",
                //"Appletype",  //It can't solve.
                "AppleType",
    
                //"appletypes",  //It can't solve.
                //"APPLETYPES",  //It can't solve.
    
                "apple_types",
                "apple_Types",
                "Apple_Types",
                //"APPLE_TYPES", //It can't solve.
    
                "appleTypes",
                //"Appletypes", //It can't solve.
                "AppleTypes",
                );
                
        $this->info( 'solver type is '. $type );
        foreach($test_words as $test_word){
            $this->info( $test_word.' -> '.$this->solveName($test_word, $type) );
        }
    }
}