<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/
 
namespace dogears\CrudDscaffold\Traits;

trait OutputTrait {

    /**
     * @param command $command
     * @param Filesystem $files
     */
    public function __construct($command, Filesystem $files){
        $this->files = $files;
        $this->commandObj = $command;
    }
    
    /**
     * outputAppend()
     *
     * Append $stub_compiled to $output_path.$output_filename and put message.
     *
     * @param string $output_path, string $output_filename, string $stub_compiled, bool $debug=false
     * @return none
     */
    public function outputAppend( $output_path, $output_filename, $stub_compiled, $debug=false ){

        //postfix for debug
        if( $debug ){ $postfix = '_'; }else{ $postfix = ''; }

        //output_exist_check
        if( !$this->files->exists($output_path.$output_filename) ){
            $this->commandObj->error($output_path.$output_filename. ' is not found!');
            return;
        }

        //copy for debug
        if( $debug ){
            $this->files->copy($output_path.$output_filename, $output_path.$output_filename.$postfix);
        }

        //output(append)
        $this->files->append($output_path.$output_filename.$postfix, $stub_compiled);
    
        //end message
        $this->commandObj->info('[modify] '. $output_path.$output_filename.$postfix);
    }

    /**
     * outputReplace()
     *
     * replace $pattern to $replacement in $output_path.$output_filename and put message.
     *
     * @param string $output_path, string $output_filename, string $pattern, string $replacement, bool $debug=false
     * @return none
     */
    public function outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug=false ){

        //postfix for debug
        if( $debug ){ $postfix = '_'; }else{ $postfix = ''; }

        //output_exist_check
        if( !$this->files->exists($output_path.$output_filename) ){
            $this->commandObj->error($output_path.$output_filename. ' is not found!');
            return;
        }

        //get source
        $src = $this->files->get($output_path.$output_filename);

        //matching
        if( !preg_match($pattern, $src)){
            $this->commandObj->error( 'pattern is not match! in '.$output_path.$output_filename );
            if($debug){
                $this->commandObj->info( "pattern :\n".$pattern );
                $this->commandObj->info( '------------------------------' );
                $this->commandObj->info( "src :\n".$src );
            }
            return;
        }

        $src = preg_replace($pattern, $replacement, $src);
        $this->files->put($output_path.$output_filename.$postfix, $src);

        //end message
        $this->commandObj->info('[modify] '. $output_path.$output_filename.$postfix);
    }

    /**
     * outputPut()
     *
     * put $stub_compiled at $output_path.$output_filename and put message.
     *
     * @param string $output_path, string $output_filename, string $stub_compiled, bool $debug=false
     * @return none
     */
    public function outputPut( $output_path, $output_filename, $stub_compiled, $debug=false, $alert=true ){

        //postfix for debug
        if( $debug ){ $postfix = '_'; }else{ $postfix = ''; }

        //output_func
        $output_func = function () use($output_path, $output_filename, $stub_compiled, $postfix){

            //output
            $this->makeDirectory($output_path.$output_filename);
            $this->files->put($output_path.$output_filename.$postfix, $stub_compiled);            
        };

        //output_exist_check
        if( $this->files->exists($output_path.$output_filename) ){

            if( $alert ){
                if ($this->commandObj->confirm( $output_path. $output_filename. $postfix. ' already exists! Do you wish to overwrite? [yes|no]')) {
    
                    //call output_func
                    $output_func();

                    //end message
                    $this->commandObj->info( '[modify]'. $output_path. $output_filename. $postfix );
                }
            }
        }else{
            //call output_func
            $output_func();

            //end message
            $this->commandObj->info( '[create]'. $output_path. $output_filename. $postfix );
        }
    }

    /**
     * outputPutWithoutAlert()
     *
     * put $stub_compiled at $output_path.$output_filename and put message.
     * if file is exist, no alert
     *
     * @param string $output_path, string $output_filename, string $stub_compiled, bool $debug=false
     * @return none
     */
    public function outputPutWithoutAlert( $output_path, $output_filename, $stub_compiled, $debug=false ){

        $this->outputPut( $output_path, $output_filename, $stub_compiled, $debug=false, $alert=false );
    }

    /**
     * outputDelete()
     *
     * Delete $output_path.$output_filename and put message.
     *
     * @param string $output_path, string $output_filename, string $stub_compiled, bool $debug=false
     * @return none
     */
    public function outputDelete( $output_path, $output_filename, $debug=false ){

        if ($this->files->exists($output_path.$output_filename)) {

            //Delete
            $this->files->delete($output_path.$output_filename);

            //end message
            $this->commandObj->info( '[delete] '. $output_path.$output_filename );

        }else{
            //end message
            $this->commandObj->info('[skip] '. $output_path.$output_filename.' is not exists!');
        }
    }

    /**
     * outputDeleteDirectory()
     *
     * Delete $output_path directory and put message.
     *
     * @param string $output_path, bool $debug=false
     * @return none
     */
    public function outputDeleteDirectory( $output_path, $debug=false ){

        if ($this->files->isDirectory($output_path)) {

            //Delete
            $this->files->deleteDirectory($output_path);

            //end message
            $this->commandObj->info( '[delete] '. $output_path );

        }else{
            //end message
            $this->commandObj->info('[skip] '.$output_path. ' is not exists!');
        }
    }
}