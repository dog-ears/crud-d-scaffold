<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace DogEars\CrudDScaffold\MyClass;

class Model
{
    public $relations;
    
    public function __construct( $model )
    {
        $this->id = $model['id'];
        $this->name = $model['name'];
        $this->display_name = $model['display_name'];
        $this->use_soft_delete = $model['use_soft_delete'];
        $this->is_pivot = $model['is_pivot'];
        $this->schema_id_for_relation = $model['schema_id_for_relation'];

        foreach( $model['schemas'] as $schema ){
            $this->schemas[] = new Schema($schema);
        }
        $this->relations = [];
    }

    public function getSchemaByName( $name ){
        $result = current( array_filter( $this->schemas, function($schema) use($name){
            return $schema->name === $name;
        }));
        if( $result===FALSE ){
            throw new \Exception('getSchemaByName('.$name.') return no schema!');
        }
        return $result;
    }

    public function getSchemaById( $id ){
        if($id===0){
            return new Schema();
        }
        $result = current( array_filter( $this->schemas, function($schema) use($id){
            return $schema->id === $id;
        }));
        if( $result===FALSE ){
            throw new \Exception('getSchemaByName('.$name.') return no schema!');
        }
        return $result;
    }
    public function getRelationSchema(){
        return $this->getSchemaById( $this->schema_id_for_relation );
    }
}
