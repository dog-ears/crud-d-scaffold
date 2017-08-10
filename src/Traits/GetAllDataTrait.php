<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Traits;

use App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait GetAllDataTrait {

    /**
     * Get all data with condition formated like ransack
     *
     * @param  string $request
     * @return LengthAwarePaginator Class
     */

    public static function getAllData( Request $request ){

        $myObj = new self;
        $myQuery = $myObj;
        $myTable_plural = snake_case(str_plural(str_replace('App\\', '', get_class())));

        if( isset(self::$related_app) && is_array(self::$related_app) ){
            foreach( self::$related_app['belongsto'] as $app_belongsto_key => $app_belongsto_value ){
        		$myQuery = $myQuery->with( camel_case(str_singular($app_belongsto_key)) );
            }
        }

		//default order
		if( !array_key_exists ( 'q', $request->all() ) || !array_key_exists ( 's', $request->all()['q'] ) ){
			$myQuery = $myQuery->orderBy( 'id', 'desc' );
		}

		if( array_key_exists('q', $request->all()) ){
			
			foreach( $request->all()['q'] as $key => $value ){

				//sort query
				if($key === 's'){
		
					if( substr( $value, -4 ) === '_asc' ){
		
						$order_key = substr( $value, 0, strlen($value)-4 );
						$order_dir = 'asc';
		
					}elseif( substr( $value, -5 ) === '_desc' ){
		
						$order_key = substr( $value, 0, strlen($value)-5 );
						$order_dir = 'desc';
		
					}else{
						throw new \Exception("query parameter is invalid!");
					}

					if( strpos($order_key, '.') === false ){	//order by original table column

						$myQuery = $myQuery->orderBy( $order_key, $order_dir );

					}else{	//order by related table column

						$order_key_array = explode('.', $order_key);
						if( count($order_key_array) > 2 ){
		                    throw new \Exception("query parameter is invalid!");
						}

						$targetTable_singular = snake_case(str_singular($order_key_array[0])); 
						$targetTable_plural = snake_case(str_plural($order_key_array[0])); 
						$myQuery = $myQuery->join($targetTable_plural, $myTable_plural.'.'.$targetTable_singular.'_id', '=', $targetTable_plural.'.id')->orderBy( $targetTable_plural.'.'.$order_key_array[1], $order_dir );

					}

				// [like] query with related table
				}elseif( strpos($key, '.') !== false && substr($key, -5) === '_cont' ){

					$new_key = substr( $key, 0, strlen($key)-5 );
					$key_array = explode('.', $new_key);

					if( count($key_array) > 2 ){
	                    throw new \Exception("query parameter is invalid!");
					}

					$myQuery = $myQuery->whereHas($key_array[0], function($q) use(&$key_array, &$value){
						$q->where($key_array[1], 'like', '%'.$value.'%');
					});

				// [gt] query with related table
				}elseif( strpos($key, '.') !== false && substr($key, -3) === '_gt' ){

					$new_key = substr( $key, 0, strlen($key)-3 );
					$key_array = explode('.', $new_key);

					if( count($key_array) > 2 ){
	                    throw new \Exception("query parameter is invalid!");
					}

					$myQuery = $myQuery->whereHas($key_array[0], function($q) use(&$key_array, &$value){
						$q->where($key_array[1], '>=', $value);
					});

				// [lt] query with related table
				}elseif( strpos($key, '.') !== false && substr($key, -3) === '_lt' ){

					$new_key = substr( $key, 0, strlen($key)-3 );
					$key_array = explode('.', $new_key);

					if( count($key_array) > 2 ){
	                    throw new \Exception("query parameter is invalid!");
					}

					$myQuery = $myQuery->whereHas($key_array[0], function($q) use(&$key_array, &$value){
						$q->where($key_array[1], '<=', $value);
					});

				// [like] query with original table
				}elseif( substr($key, -5) === '_cont' ){
	
					$new_key = substr( $key, 0, strlen($key)-5 );
					$myQuery = $myQuery->where($new_key, 'like', '%'.$value.'%');

				// [gt] query with original table	
				}elseif(substr($key, -3) === '_gt'){
	
					$new_key = substr( $key, 0, strlen($key)-3 );
					$myQuery = $myQuery->where($new_key, '>=', $value);

				// [lt] query with original table		
				}elseif(substr($key, -3) === '_lt'){
	
					$new_key = substr( $key, 0, strlen($key)-3 );
					$myQuery = $myQuery->where($new_key, '<=', $value);
				}
			}
		}
		return $myQuery;
    }
}