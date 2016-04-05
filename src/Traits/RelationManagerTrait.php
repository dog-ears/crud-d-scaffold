<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Traits;

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
    protected function addRelationApp($app, $relation_display_column = 'name'){

		if( !is_object($app) ){
			throw new \Exception( "Argument is not Object!" );
		}

		$appNames = explode( '\\', get_class($app) );
		$appName = end($appNames);

    	$this->relationApps[$appName]['app'] = $app;
    	$this->relationApps[$appName]['relation_display_column'] = $relation_display_column;
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
    		foreach ( $this->relationApps as $relationAppName => $relationAppArray ){
    			$relatedObjList = $relationAppArray['app']::lists($relationAppArray['relation_display_column'], 'id');
    			$list[$relationAppName] = $relatedObjList;
    		}
        }
		return $list;
    }
}