<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace DogEars\CrudDScaffold\MyClass;

class Schema
{
    public function __construct( $schema=null )
    {
        if( $schema ){
            $this->id = $schema['id'];
            $this->name = $schema['name'];
            $this->display_name = $schema['display_name'];
            $this->type = $schema['type'];
            $this->input_type = $schema['input_type'];
            $this->varidate = $schema['varidate'];
            $this->faker_type = $schema['faker_type'];
            $this->nullable = $schema['nullable'];
            $this->unique = $schema['unique'];
            $this->show_in_list = $schema['show_in_list'];
            $this->show_in_detail = $schema['show_in_detail'];
            $this->belongsto = $schema['belongsto'];
            $this->parent_id = $schema['parent_id'];
        }else{
            $this->id = 0;
            $this->name = 'id';
            $this->display_name = 'ID';
            $this->type = 'integer';
            $this->input_type = '';
            $this->varidate = '';
            $this->faker_type = '';
            $this->nullable = false;
            $this->unique = false;
            $this->show_in_list = ture;
            $this->show_in_detail = ture;
            $this->belongsto = '';
            $this->parent_id = '';
        }
    }

    public function getVaridate(){
        $result = '';
        if( $this->type === 'integer'){
            $result .= '|integer';
        }
        if( $this->input_type === 'password'){
            $result .= '|confirmed';
        }
        if( $this->nullable === false ){
            $result .= '|required';
        }else{
            $result .= '|nullable';
        }
        if( $this->unique === true ){
            $result .= '|unique:\'.$table_name.\','. $this->name .',\'.$ignore_unique.\',id';  // ,deleted_at,NOT_NULL
        }
        $result = ltrim ($result,'|');
        return $result;
    }
}