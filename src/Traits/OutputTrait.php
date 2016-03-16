<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/
 
namespace dogears\L5scaffold\Traits;

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
     * @param string $output_path, string $output_filename, string $stub_compiled, string $message_success, bool $debug=false
     * @return none
     */
    public function outputAppend( $output_path, $output_filename, $stub_compiled, $message_success, $debug=false ){

        //postfix for debug
        if( $debug ){ $postfix = '_'; }else{ $postfix = ''; }

        //output_exist_check
        if( !$this->files->exists($output_path.$output_filename) ){
            $this->commandObj->error($output_path.$output_filename. ' is not found!');
            exit();
        }

        //output(append)
        $this->files->append($output_path.$output_filename.$postfix, $stub_compiled);
    
        //end message
        if($message_success){
            $this->commandObj->info($message_success);
        }
    }

    /**
     * outputReplace()
     *
     * replace $pattern to $replacement in $output_path.$output_filename and put message.
     *
     * @param string $output_path, string $output_filename, string $pattern, string $replacement, string $message_success, bool $debug=false
     * @return none
     */
    public function outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success, $debug=false ){

        //postfix for debug
        if( $debug ){ $postfix = '_'; }else{ $postfix = ''; }

        //output_exist_check
        if( !$this->files->exists($output_path.$output_filename) ){
            $this->commandObj->error($output_path.$output_filename. ' is not found!');
            exit();
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
            exit();
        }

        $src = preg_replace($pattern, $replacement, $src);
        $this->files->put($output_path.$output_filename.$postfix, $src);

        //end message
        if($message_success){
            $this->commandObj->info($message_success);
        }
    }

    /**
     * outputPut()
     *
     * put $stub_compiled at $output_path.$output_filename and put message.
     *
     * @param string $output_path, string $output_filename, string $stub_compiled, string $message_success, bool $debug=false
     * @return none
     */
    public function outputPut( $output_path, $output_filename, $stub_compiled, $message_success, $debug=false, $alert=true ){

        //postfix for debug
        if( $debug ){ $postfix = '_'; }else{ $postfix = ''; }

        //output_func
        $output_func = function () use($output_path, $output_filename, $stub_compiled, $message_success, $postfix){

            //output
            $this->makeDirectory($output_path.$output_filename);
            $this->files->put($output_path.$output_filename.$postfix, $stub_compiled);            

            //end message
            if($message_success){
                $this->commandObj->info($message_success);
            }
        };

        //output_exist_check
        if( $this->files->exists($output_path.$output_filename) ){

            if( $alert ){
                if ($this->commandObj->confirm($output_path.$output_filename. ' already exists! Do you wish to overwrite? [yes|no]')) {
    
                    //call output_func
                    $output_func();
                }
            }else{
                $this->commandObj->info( 'file is already exists' );
            }
        }else{
            //call output_func
            $output_func();
        }
    }

    /**
     * outputPutWithoutAlert()
     *
     * put $stub_compiled at $output_path.$output_filename and put message.
     * if file is exist, no alert
     *
     * @param string $output_path, string $output_filename, string $stub_compiled, string $message_success, bool $debug=false
     * @return none
     */
    public function outputPutWithoutAlert( $output_path, $output_filename, $stub_compiled, $message_success, $debug=false ){

        $this->outputPut( $output_path, $output_filename, $stub_compiled, $message_success, $debug=false, $alert=false );
    }

    /**
     * outputDelete()
     *
     * Delete $output_path.$output_filename and put message.
     *
     * @param string $output_path, string $output_filename, string $stub_compiled, string $message_success, bool $debug=false
     * @return none
     */
    public function outputDelete( $output_path, $output_filename, $message_success, $debug=false ){

        if ($this->files->exists($output_path.$output_filename)) {

            //Delete
            $this->files->delete($output_path.$output_filename);

            if($message_success){
                $this->commandObj->info($message_success);
            }
        }else{
            $this->commandObj->info($output_path.$output_filename.' is not exists!');
        }
    }

    /**
     * outputDeleteDirectory()
     *
     * Delete $output_path directory and put message.
     *
     * @param string $output_path, string $message_success, bool $debug=false
     * @return none
     */
    public function outputDeleteDirectory( $output_path, $message_success, $debug=false ){

        if ($this->files->isDirectory($output_path)) {

            //Delete
            $this->files->deleteDirectory($output_path);

            if($message_success){
                $this->commandObj->info($message_success);
            }
        }else{
            $this->commandObj->info('View ('.$output_path.') is not exists!');
        }
    }
}