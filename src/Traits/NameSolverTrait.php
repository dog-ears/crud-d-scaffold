<?php

/**
 * Created by dog-ears

[入力]
なし

[処理]
Scaffold作成時に入力されたアプリ名を、
各種形式にあわせて変換。
ストックする。

[出力]

 
 */
 
namespace dogears\L5scaffold\Traits;

trait NameSolverTrait {

    /**
     * Get the Name.
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
     * Get the Name Test.
     *
     * @param string $config
     */
    public function solveName_test($type){

        $test_words = array(
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
                
        $this->info( 'solver type is '. $type );
        foreach($test_words as $test_word){
            $this->info( $test_word.' -> '.$this->solveName($test_word, $type) );
        }
    }
}