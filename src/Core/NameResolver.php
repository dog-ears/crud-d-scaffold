<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Core;

class NameResolver
{
    static function solveName( $input, $type ){

        if( is_array($input) || is_object($input) ){
            throw new \Exception("NameSolver accept invalid input");
        }
        if( is_array($type) || is_object($type) ){
            throw new \Exception("NameSolver accept invalid type");
        }

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
        }elseif( $type === '' || $type === null ){
            $result = $input;
        }else{
            throw new \Exception("NameSolver accept invalid type");
        }
        return $result;
    }
}