<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Traits;

trait GetListFromAllRelationAppsTrait {

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function getListFromAllRelationApps()
    {
		$result = [];

		$belongsto_apps = self::$related_app["belongsto"];

		foreach( $belongsto_apps as $belongsto_app_key => $belongsto_app_value ){

			$class_name = '\\App\\'. $belongsto_app_key;
			$result[$belongsto_app_key] = $class_name::pluck( $belongsto_app_value, 'id' );
		}

		$belongstomany_apps = self::$related_app["belongstomany"];

		foreach( $belongstomany_apps as $belongstomany_app_key => $belongstomany_app_value ){

			$class_name = '\\App\\'. $belongstomany_app_key;
			$result[$belongstomany_app_key] = $class_name::pluck( $belongstomany_app_value, 'id' );
		}

        return $result;
    }
}