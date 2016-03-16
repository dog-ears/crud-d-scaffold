<?php
    $parameters = Request::all();
    if (isset($parameters['q']['s']) && $parameters['q']['s'] == $column.'_asc' ){
        $parameters['q']['s'] = $column.'_desc';
    }else{
        $parameters['q']['s'] = $column.'_asc';
    }
?>
{!! link_to_route( Route::currentRouteName(), $title, $parameters, $attributes = array()) !!}