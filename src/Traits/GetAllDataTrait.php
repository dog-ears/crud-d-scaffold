<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\L5scaffold\Traits;

use App\Http\Requests;
use Illuminate\Http\Request;
use dogears\L5scaffold\Traits\NameSolverTrait;

trait GetAllDataTrait {

    use NameSolverTrait;

    /**
     * Get all data with condition formated like ransack
     *
     * @param  string $request, integer $paginate
     * @return LengthAwarePaginator Class
     */

    public static function getAllData( Request $request, $paginate=10 ){

        $myObj = new self;

        $names = explode('\\', get_class($myObj) );
        $baseTable = $myObj->solveName( end($names), config('l5scaffold.app_name_rules.app_migrate_tablename') );    //ex).apples

        //(i) join relation table

        if( is_array( $myObj->relationApps ) ){

            foreach( $myObj->relationApps as $className => $classObj ){
    
                $relationTable = $myObj->solveName( $className, config('l5scaffold.app_name_rules.app_migrate_tablename') );    //ex).apple_types
                $relationColumnInBaseTable = $myObj->solveName( $className, config('l5scaffold.app_name_rules.name_name') ).'_id';    //ex).apple_type_id
    
                $myObj = $myObj->leftJoin( $relationTable, $baseTable.'.'.$relationColumnInBaseTable, '=', $relationTable.'.id' );  //ex).leftJoin( 'apple_types', 'apples.apple_type_id', '=', 'apple_types.id' )
            }
        }

        //(ii) add Constrain

        if( is_array($request->input('q')) ){

            foreach( $request->input('q') as $key => $value){
    
                //skip s value that is for ordering
                if( $key === 's' ){ continue; }

                //skip if value is blank
                if( $value === '' ){ continue; }

                if( preg_match('#(.*)_([^_]*?)$#', $key, $m) ){
                    $column = $m[1];
                    $operator = $m[2];
                }else{
                    abort(500, 'query parameter has wrong value');
                }

                //if column is not relation table's column, add base table name at head.
                if( strpos($column,'.') === false ){
                    $column = $baseTable.'.'.$column;
                }

                if( $operator === 'cont' ){
                    $myObj = $myObj->where($column, 'LIKE', '%'.$value.'%');
                }elseif( $operator === 'lt' ){
                    $myObj = $myObj->where($column, '<=', $value);                
                }elseif( $operator === 'gt' ){
                    $myObj = $myObj->where($column, '>=', $value);                
                }
            }
        }

        //(iii) order setting

        if( is_array($request->input('q')) && array_key_exists( 's', $request->input('q')) && $request->input('q')['s'] !== '' ){

            if( preg_match('#(.*)_([^_]*?)$#', $request->input('q')['s'], $m) ){
                $column = $m[1];
                $order_dir = $m[2];

                if( mb_strtoupper($order_dir) !== 'ASC' && mb_strtoupper($order_dir) !== 'DESC' ){
                    abort(500, 'query parameter q[s] has wrong value');
                }
            }else{
                abort(500, 'query parameter q[s] has wrong value');
            }

            //if column is not relation table's column, add base table name at head.
            if( strpos($column,'.') === false ){
                $column = $baseTable.'.'.$column;
            }

        }else{
            $column = $baseTable.'.id';
            $order_dir = 'DESC';
        }

        $myObj = $myObj->orderBy( $column, $order_dir);

        //(iv) get base table data

        $myObj = $myObj->select([ $baseTable.'.*' ]);

        //(v) pagenate

        return $myObj->paginate($paginate);

    }
}