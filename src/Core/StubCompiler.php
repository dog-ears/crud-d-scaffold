<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace DogEars\CrudDscaffold\Core;

use DogEars\CrudDscaffold\Core\NameResolver ;

class StubCompiler
{

    private $debug_flag = false;

    private $stub_txt;
    private $stub_array = array();
    private $root_vars;

    public function __construct( $stub_txt, $root_vars )
    {
        $this->stub_txt = $stub_txt;
        $this->root_vars = $root_vars;
    }

    public function compile(){

        if($this->debug_flag){ echo('[func]compile'."\n"); }
        $result = '';

        //delete return before tag
        $this->stub_txt = str_replace ( array("}}}\r\n{{{", "}}}\r{{{", "}}}\n{{{") , "}}}{{{", $this->stub_txt );
        $this->stub_txt = str_replace ( array("}}}\r\n", "}}}\r", "}}}\n") , "}}}", $this->stub_txt );

        /* prepare - parse */
        $pattern_tag = '#(\{\{\{ [^}]* \}\}\})#';
        $this->stub_array = preg_split ( $pattern_tag , $this->stub_txt, null, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE );  // {{{ xxxxx }}}

        //compile
        $result = $this->compile_loop( $this->stub_array, '' );

        return $result;
    }



    private function compile_loop( $local_stub_array, $this_path ){

        //if($this->debug_flag){ echo("\n".'[func]compile_loop'."\n"); }
        if($this->debug_flag){ echo("\n"."-----".'[func]compile_loop start -----'."\n"); }
        $result = '';

        $before_tag = '';   //if, elseif, endif, foreach, endforeach // use for parse check
        $parent_tag = '';   //if, foreach //currently under depth-0 [if or foreach] or not
        $new_local_stub_array = []; //for second depth
        $depth = 0; 
        $if_condition = -1; //0:false condition, 1:true condition, 2:skip condition
        $var_foreach = '';

        $pattern_var='#\{\{\{ \$([^\|\}]*)\|?([^\}]*) \}\}\}#';
        $pattern_if='#\{\{\{ if\(\$([^=]*)==(.*?)\): \}\}\}#';
        $pattern_ifnot='#\{\{\{ if\(\$([^=]*)!=(.*?)\): \}\}\}#';
        $pattern_elseif='#\{\{\{ elseif\(\$([^=]*)==(.*?)\): \}\}\}#';
        $pattern_elseifnot='#\{\{\{ elseif\(\$([^=]*)!=(.*?)\): \}\}\}#';
        $pattern_foreach='#\{\{\{ foreach\(\$(.*?)\): \}\}\}#';

        for( $i=0;$i<count($local_stub_array);$i++ ){

            //var
            if( preg_match ( $pattern_var , $local_stub_array[$i], $m ) ){

                //if($this->debug_flag){ echo ('[depth:'.$depth.']var : ['.implode(',',$m)."]"."\n"); }

                if( $depth > 0  ){
                    $new_local_stub_array[] = $local_stub_array[$i];
                }else{
                    $var_path = $m[1];
                    $pipe = $m[2];
                    $result .= $this->compile_var( $var_path, $pipe, $this_path );

                    if($this->debug_flag){ echo ($local_stub_array[$i]. ' ---> '.$this->compile_var( $var_path, $pipe, $this_path ) ."\n"); }
                }

            //if
            }elseif( preg_match ( $pattern_if , $local_stub_array[$i], $m ) || preg_match ( $pattern_ifnot , $local_stub_array[$i], $m ) ){

                //if($this->debug_flag){ echo ('[depth:'.$depth.']if : ['.implode(',',$m)."]"."\n"); }

                if( $depth > 0  ){
                    $new_local_stub_array[] = $local_stub_array[$i];
                }else{
                    $var_path = $m[1];
                    $target_str = $m[2];
                    if( strpos( $local_stub_array[$i] , '!=' ) ){
                        $if_condition = $this->check_if_condition( $var_path, $target_str, $this_path, $reverse=true );
                    }else{
                        $if_condition = $this->check_if_condition( $var_path, $target_str, $this_path, $reverse=false );
                    }
                    $before_tag = 'if';
                    $new_local_stub_array = [];

                    if($if_condition == 1){
                        if($this->debug_flag){ echo ($local_stub_array[$i].' ---> OK!'."\n"); }
                    }else{
                        if($this->debug_flag){ echo ($local_stub_array[$i].' ---> NG!'."\n"); }
                    }
                }
                $depth += 1;

            //elseif
            }elseif( preg_match ( $pattern_elseif , $local_stub_array[$i], $m ) || preg_match ( $pattern_elseifnot , $local_stub_array[$i], $m ) ){

                //if($this->debug_flag){ echo ('[depth:'.$depth.']elseif : ['.implode(',',$m)."]"."\n"); }

                if( $depth > 1  ){
                    $new_local_stub_array[] = $local_stub_array[$i];
                }else{

                    //parse check
                    if( $before_tag == 'foreach' || $before_tag == ''){
                        throw new \Exception("Stub parse error!");
                    }

                    if( $if_condition == 1 ){
                        //evaluate $new_local_stub_array
                        $result .= $this->compile_loop( $new_local_stub_array, $this_path );
                        $if_condition = 2; // change to skip mode
                    }

                    if( $if_condition != 2 ){

                        $var_path = $m[1];
                        $target_str = $m[2];

                        if( strpos( $local_stub_array[$i] , '!=' ) ){
                            $if_condition = $this->check_if_condition( $var_path, $target_str, $this_path, $reverse=true );
                        }else{
                            $if_condition = $this->check_if_condition( $var_path, $target_str, $this_path, $reverse=false );
                        }

                        if($if_condition == 1){
                            if($this->debug_flag){ echo ($local_stub_array[$i].' ---> OK!'."\n"); }
                        }else{
                            if($this->debug_flag){ echo ($local_stub_array[$i].' ---> NG!'."\n"); }
                        }
                    }else{
                        if($this->debug_flag){ echo ($local_stub_array[$i].' ---> SKIP!'."\n"); }
                    }
                    $before_tag = 'elseif';
                    $new_local_stub_array = [];
                }

            //else
            }elseif( $local_stub_array[$i] == '{{{ else: }}}' ){

                //if($this->debug_flag){ echo ('[depth:'.$depth.']else : ['.implode(',',$m)."]"."\n"); }

                if( $depth > 1  ){
                    $new_local_stub_array[] = $local_stub_array[$i];
                }else{

                    //parse check
                    if( $before_tag == 'foreach'){
                        throw new \Exception("Stub parse error!");
                    }

                    if( $if_condition == 1 ){
                        //evaluate $new_local_stub_array
                        $result .= $this->compile_loop( $new_local_stub_array, $this_path );
                        $if_condition = 2; // change to skip mode
                    }
                    
                    if( $if_condition !== 2 ){
                        $if_condition = 1;

                        if($this->debug_flag){ echo ($local_stub_array[$i].' ---> OK!'."\n"); }
                    }else{
                        if($this->debug_flag){ echo ($local_stub_array[$i].' ---> SKIP!'."\n"); }
                    }
                    $before_tag = 'else';
                    $new_local_stub_array = [];
                }

            //endif
            }elseif( $local_stub_array[$i] == '{{{ endif; }}}' ){

                //if($this->debug_flag){ echo ('[depth:'.$depth.']endif'."\n"); }

                if( $depth > 1  ){
                    $new_local_stub_array[] = $local_stub_array[$i];
                }else{

                    //parse check
                    if( $before_tag == 'foreach'){
                        throw new \Exception("Stub parse error!");
                    }

                    if( $if_condition == 1 ){

                        //evaluate $new_local_stub_array
                        $result .= $this->compile_loop( $new_local_stub_array, $this_path );
                    }

                    $if_condition = -1; // change to default
                    $parent_tag = '';
                    $before_tag = 'endif';
                    $new_local_stub_array = [];

                    if($this->debug_flag){ echo ($local_stub_array[$i]."\n"); }
                }
                $depth -= 1;

            //foreach
            }elseif( preg_match ( $pattern_foreach , $local_stub_array[$i], $m ) ){

                if($this->debug_flag){ echo ('[depth:'.$depth.']foreach : ['.implode(',',$m)."]"."\n"); }

                if( $depth > 0  ){
                    $new_local_stub_array[] = $local_stub_array[$i];
                }else{
                    $parent_tag = 'foreach';
                    $before_tag = 'foreach';
                    $var_path_foreach = $m[1];
                    $new_local_stub_array = [];
                }
                $depth += 1;

            //endforeach
            }elseif( $local_stub_array[$i] == '{{{ endforeach; }}}' ){

                if($this->debug_flag){ echo ('['.$depth.']endforeach'."\n"); }

                if( $depth > 1  ){
                    $new_local_stub_array[] = $local_stub_array[$i];
                }else{

                    //parse check
                    if( $before_tag == 'if' || $before_tag == 'elseif' ){
                        throw new \Exception("Stub parse error!");
                    }

                    $var_path_foreach = $this->merge_path( $var_path_foreach, $this_path );
                    $loop_array = data_get($this->root_vars, $var_path_foreach );
                    //array check
                    if( !is_array($loop_array) ){
                        throw new \Exception("var for foreach is not array!");
                    }
                    for($j=0;$j<count($loop_array);$j++){
                        $result .= $this->compile_loop( $new_local_stub_array, $var_path_foreach.'.'.$j );
                    }
                    $parent_tag = '';
                    $before_tag = 'endforeach';
                    $new_local_stub_array = [];
                }
                $depth -= 1;

            //code
            }else{
                //if($this->debug_flag){ echo ('['.$depth.']code:'.$local_stub_array[$i]."\n"); }

                if( $depth > 0 ){
                    $new_local_stub_array[] = $local_stub_array[$i];
                }else{
                    if($this->debug_flag){ echo ($local_stub_array[$i]); }
                    $result .= $local_stub_array[$i];
                }
            }
        }
        
        //parse check
        if( $before_tag == 'foreach' || $before_tag == 'if' || $before_tag == 'elseif'){
            throw new \Exception("Stub parse error!");
        }

        if($this->debug_flag){ echo("-----".'[func]compile_loop end -----'."\n\n"); }

        return $result;
    }



    // return string
    private function compile_var( $var_path, $pipe, $this_path ){

        $var_path = $this->merge_path( $var_path, $this_path );
        $result = $this->data_get( $this->root_vars, $var_path );

        if($pipe !== ''){
            return NameResolver::solveName($result, $pipe);
        }
        return $result;
    }

    private function merge_path( $var_path, $this_path ){
        $var_path_array = explode('.',$var_path);
        $this_path_array = explode('.',$this_path);

        if( $var_path_array[0] == 'this' ){
            $var_path_array = array_merge($this_path_array, array_slice($var_path_array,1));
        }elseif( $var_path_array[0] == 'parent' ){
            $var_path_array = array_merge(array_slice($this_path_array,0,-2), array_slice($var_path_array,1));
        }
        $var_path = implode('.',$var_path_array);
        return $var_path;
    }

    private function check_if_condition( $var_path, $target_str, $this_path, $reverse=false ){

        $var01 = $this->compile_var( $var_path, '', $this_path );

        // case of null array check
        if( $target_str === '[]' ){
            $target_str = [];
        }elseif( $target_str === 'true' ){
            $target_str = true;
        }elseif( $target_str === 'false' ){
            $target_str = false;
        }elseif( !preg_match("#^'(.*)'$#", $target_str) ){
            throw new \Exception('if target var must be [] or true or false or string');
        }elseif( preg_match("#^'(.*)'$#", $target_str,$m) ){
            $target_str = $m[1];
        }

        if($reverse){
            if( $var01 === $target_str ){
                return 0;
            }else{
                return 1;
            }
        }else{
            if( $var01 === $target_str ){
                return 1;
            }else{
                return 0;
            }
        }
    }

    private function data_get($data, $keys ) {

        $keys_array = explode( '.', $keys );
    
        $current = $data;
        foreach ($keys_array as $key) {
            if( is_array($current) ){
                if( !array_key_exists($key,$current) ){
                    throw new \Exception('$current doesn\'t has key:'.$key);
                }
                $current = $current[$key];
            }elseif( is_object($current) ){

                if( mb_substr($key,-2) === '()'){   // case - $key is method

                    $key2 = rtrim($key,'()');
                    if( !method_exists( $current,$key2 ) ){
                        throw new \Exception( get_class($current). ' doesn\'t has method:'.$key);
                    }
                    $current = $current->$key2();

                }else{  // case - $key is property
                    
                    if( !property_exists($current,$key) ){
                        throw new \Exception(get_class($current). ' doesn\'t has property:'.$key);
                    }
                    $current = $current->$key;
                }
            }else{
                throw new \Exception('$current('.$current.') is not array or object');
            }
        }
        return $current;
    }
}