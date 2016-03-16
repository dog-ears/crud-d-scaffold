<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\L5scaffold\Traits;

trait RelationManagerTrait {

    /**
     * The relation App.
     *
     * @var relationApps
     */
    protected $relationApps;

    /**
     * Add relation App to list
     *
     * @param  string  $path
     * @return string
     */
    protected function addRelationApp($app){

		if( !is_object($app) ){
			throw new \Exception( "Argument is not Object!" );
		}

		$appNames = explode( '\\', get_class($app) );
		$appName = end($appNames);

    	$this->relationApps[$appName] = $app;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function getListFromAllRelationApps()
    {
    	$list = [];

        if( $this->relationApps ){
    		foreach ( $this->relationApps as $relationAppName => $relationApp ){
    
    			$relatedObjList = $relationApp::lists('name','id');
    			$list[$relationAppName] = $relatedObjList;
    		}
        }
		return $list;
    }
}