<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Core;

use dogears\CrudDscaffold\Core\NameResolver ;

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

        if($this->debug_flag){ echo('[func]compile_loop'."\n"); }
        $result = '';

        $before_tag = '';   //if, elseif, endif, foreach, endforeach // use for parse check
        $parent_tag = '';   //if, foreach //currently under depth-0 [if or foreach] or not
        $new_local_stub_array = []; //for second depth
        $depth = 0; 
        $if_condition = -1; //0:false condition, 1:true condition, 2:skip condition
        $var_foreach = '';

        $pattern_var='#\{\{\{ \$([^\|\}]*)\|?([^\}]*) \}\}\}#';
        $pattern_if='#\{\{\{ if\(\$([^=]*)==([^\)]*)\): \}\}\}#';
        $pattern_ifnot='#\{\{\{ if\(\$([^=]*)!=([^\)]*)\): \}\}\}#';
        $pattern_elseif='#\{\{\{ elseif\(\$([^=]*)==([^\)]*)\): \}\}\}#';
        $pattern_elseifnot='#\{\{\{ elseif\(\$([^=]*)!=([^\)]*)\): \}\}\}#';
        $pattern_foreach='#\{\{\{ foreach\(\$([^\)]*)\): \}\}\}#';

        for( $i=0;$i<count($local_stub_array);$i++ ){

            //var
            if( preg_match ( $pattern_var , $local_stub_array[$i], $m ) ){

                if($this->debug_flag){ echo ('['.$depth.']var : ['.implode(',',$m)."]"."\n"); }

                if( $depth > 0 ){
                    if($if_condition != 0 && $if_condition != 2){
                        $new_local_stub_array[] = $local_stub_array[$i];
                    }
                }else{
                    $var_path = $m[1];
                    $pipe = $m[2];
                    $result .= $this->compile_var( $var_path, $pipe, $this_path );
                }

            //if
            }elseif( preg_match ( $pattern_if , $local_stub_array[$i], $m ) || preg_match ( $pattern_ifnot , $local_stub_array[$i], $m ) ){

                if($this->debug_flag){ echo ('['.$depth.']if : ['.implode(',',$m)."]"."\n"); }

                if( $depth > 0 ){
                    if($if_condition != 0 && $if_condition != 2){
                        $new_local_stub_array[] = $local_stub_array[$i];
                    }
                }else{
                    $var_path = $m[1];
                    $target_str = $m[2];
                    if( strpos( $local_stub_array[$i] , '!=' ) ){
                        $if_condition = $this->check_if_condition( $var_path, $target_str, $this_path, $reverse=true );
                    }else{
                        $if_condition = $this->check_if_condition( $var_path, $target_str, $this_path, $reverse=false );
                    }
                    $new_local_stub_array = [];
                }

                $parent_tag = 'if';
                $before_tag = 'if';
                $depth += 1;

            //elseif
            }elseif( preg_match ( $pattern_elseif , $local_stub_array[$i], $m ) || preg_match ( $pattern_elseifnot , $local_stub_array[$i], $m ) ){

                //parse check
                if( $before_tag == 'foreach' || $before_tag == ''){
                    throw new \Exception("Stub parse error!");
                }

                $depth -= 1;

                if( $depth > 0 ){
                    if($if_condition != 0 && $if_condition != 2){
                        $new_local_stub_array[] = $local_stub_array[$i];
                    }
                }else{

                    if( $if_condition == 1 ){

                        //evaluate $new_local_stub_array
                        $result .= $this->compile_loop( $new_local_stub_array, $this_path );

                        $if_condition = 2; // change to skip mode
                    }
                    $new_local_stub_array = [];

                    if($this->debug_flag){ echo ('['.$depth.']elseif : ['.implode(',',$m)."]"."\n"); }

                    if( $if_condition != 2 ){

                        $var_path = $m[1];
                        $target_str = $m[2];

                        if( strpos( $local_stub_array[$i] , '!=' ) ){
                            $if_condition = $this->check_if_condition( $var_path, $target_str, $this_path, $reverse=true );
                        }else{
                            $if_condition = $this->check_if_condition( $var_path, $target_str, $this_path, $reverse=false );
                        }
                    }
                }

                $before_tag = 'elseif';
                $depth += 1;

            //else
            }elseif( $local_stub_array[$i] == '{{{ else: }}}' ){

                //parse check
                if( $before_tag == 'foreach'){
                    throw new \Exception("Stub parse error!");
                }

                $depth -= 1;

                if( $depth > 0 ){
                    if($if_condition != 0 && $if_condition != 2){
                        $new_local_stub_array[] = $local_stub_array[$i];
                    }
                }else{

                    if( $if_condition == 1 ){

                        //evaluate $new_local_stub_array
                        $result .= $this->compile_loop( $new_local_stub_array, $this_path );

                        $if_condition = 2; // change to skip mode
                    }
                    $new_local_stub_array = [];
    
                    if($this->debug_flag){ echo ('['.$depth.']else : ['.implode(',',$m)."]"."\n"); }

                    if( $if_condition != 2 ){

                        $if_condition = 1;
                    }
                }
                $before_tag = 'else';
                $depth += 1;

            //endif
            }elseif( $local_stub_array[$i] == '{{{ endif; }}}' ){

                //parse check
                if( $before_tag == 'foreach'){
                    throw new \Exception("Stub parse error!");
                }

                $depth -= 1;

                if( $depth > 0 ){
                    if($if_condition != 0 && $if_condition != 2){
                        $new_local_stub_array[] = $local_stub_array[$i];
                    }
                }else{

                    if( $if_condition == 1 ){

                        //evaluate $new_local_stub_array
                        $result .= $this->compile_loop( $new_local_stub_array, $this_path );
                    }
                    $if_condition = -1; // change to default
                    $new_local_stub_array = [];
                }
                $parent_tag = '';

                if($this->debug_flag){ echo ('['.$depth.']endif'."\n"); }

                $before_tag = 'endif';

            //foreach
            }elseif( preg_match ( $pattern_foreach , $local_stub_array[$i], $m ) ){

                if($this->debug_flag){ echo ('['.$depth.']foreach : ['.implode(',',$m)."]"."\n"); }

                if( $depth > 0 ){
                    if($if_condition != 0 && $if_condition != 2){
                        $new_local_stub_array[] = $local_stub_array[$i];
                    }
                }else{
                    $var_path_foreach = $m[1];
                }
                $parent_tag = 'foreach';
                $before_tag = 'foreach';
                $depth += 1;

            //endforeach
            }elseif( $local_stub_array[$i] == '{{{ endforeach; }}}' ){

                $depth -= 1;

                //parse check
                if( $before_tag == 'if' || $before_tag == 'elseif' ){
                    throw new \Exception("Stub parse error!");
                }

                if($this->debug_flag){ echo ('['.$depth.']endforeach'."\n"); }

                if( $depth > 0 ){

                    if($if_condition != 0 && $if_condition != 2){
                        $new_local_stub_array[] = $local_stub_array[$i];
                    }

                }else{

                    //convert this -> this_path
                    $var_path_foreach_array = explode('.', $var_path_foreach);
                    $this_path_array = explode('.', $this_path);
                    if($var_path_foreach_array[0] == 'this'){
                        $var_path_foreach_array = array_merge( $this_path_array, array_slice($var_path_foreach_array,1) );
                    }
                    $var_path_foreach = implode('.', $var_path_foreach_array);
                    $loop_array = $this->array_get( $this->root_vars, $var_path_foreach );

                    for($j=0;$j<count($loop_array);$j++){

                        $result .= $this->compile_loop( $new_local_stub_array, $var_path_foreach.'.'.$j );
                    }
                    $new_local_stub_array = [];

                }
                $parent_tag = '';
                $before_tag = 'endforeach';

            //code
            }else{

                if($this->debug_flag){ echo ('['.$depth.']code'."\n"); }

                if( $depth > 0 ){
                    if($if_condition != 0 && $if_condition != 2){
                        $new_local_stub_array[] = $local_stub_array[$i];
                    }
                }else{
                    $result .= $local_stub_array[$i];
                }
            }
        }
        
        //parse check
        if( $before_tag == 'foreach' || $before_tag == 'if' || $before_tag == 'elseif'){
            throw new \Exception("Stub parse error!");
        }
        return $result;
    }



    // return string
    private function compile_var( $var_path, $pipe, $this_path ){
        if($this->debug_flag){ echo('[func]compile_var'. "\n" ); }

        $var_path_array = explode('.',$var_path);
        $this_path_array = explode('.',$this_path);

        if( $var_path_array[0] == 'this' ){
            $var_path_array = array_merge($this_path_array, array_slice($var_path_array,1));
        }elseif( $var_path_array[0] == 'parent' ){
            $var_path_array = array_merge(array_slice($this_path_array,0,-2), array_slice($var_path_array,1));
        }

        $var_path = implode('.',$var_path_array);

        $result = $this->array_get( $this->root_vars, $var_path );

        if($pipe !== ''){
            return NameResolver::solveName($result, $pipe);
        }
        return $result;
    }



    private function check_if_condition( $var_path, $target_str, $this_path, $reverse=false ){

        $var01 = $this->compile_var( $var_path, '', $this_path );
        
        // case of null array check
        if( $target_str === '[]' ){
            $target_str = [];
        }

        if($reverse){
            if( $var01 == $target_str ){
                return 0;
            }else{
                return 1;
            }
        }else{
            if( $var01 == $target_str ){
                return 1;
            }else{
                return 0;
            }
        }
    }



    private function array_get(array $array, $keys ) {

        $keys_array = explode( '.', $keys );
 
        $current = $array;
        foreach ($keys_array as $key) {
            if (!isset($current[$key])) return;
            $current = $current[$key];
        }
        return $current;
    }
}