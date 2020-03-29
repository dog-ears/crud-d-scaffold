<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\MyClass;

use Illuminate\Support\Str;

class Relation
{
    public function __construct( $type, $originalModel, $targetModel, $pivotModel=null, $pivotModelSchemas=array() )
    {
        $this->type = $type;   // belongsTo or hasMany or belongsToMany
        $this->originalModel = $originalModel;
        $this->targetModel = $targetModel;
        $this->pivotModel = $pivotModel;
        $this->pivotModelSchemas = $pivotModelSchemas;
    }

    public function implodePivotColumns(){
        $result = '';
        foreach( $this->pivotModelSchemas as $schema ){
            $result .= ",'".Str::snake(Str::singular($schema->name))."'";
        }
        $result = ltrim($result,',');
        return $result;
    }
}