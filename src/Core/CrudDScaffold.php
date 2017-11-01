<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Core;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use dogears\CrudDscaffold\Commands\CrudDscaffoldSetupCommand;
use dogears\CrudDscaffold\Core\CrudDscaffoldSetting ;
use dogears\CrudDscaffold\Core\StubCompiler ;
use dogears\CrudDscaffold\Core\NameResolver ;

class CrudDscaffold
{
    private $files;     /* Filesystem */
    private $command;   /* CrudDscaffoldSetupCommand */
    private $setting;   /* CrudDscaffoldSetting */

    //private $app_type;  /* 'web' or 'api' */

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct( Filesystem $files, CrudDscaffoldSetting $setting )
    {
        $this->files = $files;
        $this->setting = $setting;

    }
    
    public function setCommand( CrudDscaffoldSetupCommand $command ){

        $this->command = $command;

        //load Setting
        $this->setting->loadSettingFromCommand( $this->command );
    }

    public function generate(){

        $this->command->info('Now Generating...');

        $this->setupMigration();
        $this->setupSeeding();
        $this->setupModel();
        $this->setupController();
        $this->setupViewLayout();
        $this->setupView();
        $this->setupRoute();
    }



    private function setupMigration(){

        foreach( $this->setting->setting_array['models'] as $model ){

            //table exist check
            if (Schema::hasTable( NameResolver::solveName($model['name'], 'name_names') )) {    //table exists
    
                throw new \Exception('['. NameResolver::solveName($model['name'], 'name_names'). '] table is already exists. migrate:rollback and delete migration files');

            }else{  //table is not exists
            
            // this case means two state
            // first state is created migration file and not migrate.
            // second state is not-created migration.
            // this program ignore first state.

                // case using laravel auth
                if( $this->setting->setting_array["use_laravel_auth"] === "true" && $model['name'] === "user" ){
                    
                    $stub_txt = $this->files->get( __DIR__. '/../Stubs/database/migrations/Auth/create_users_table01_schema.stub');
                    
                    $output_path = $this->files->glob('./database/migrations/*create_users_table.php');
                    $output_path = base_path().substr( $output_path[0], 1 );

                    $stub_obj = new StubCompiler( $stub_txt, $model );
                    $add_src = $stub_obj->compile();
        
                    $original_src = $this->files->get( $output_path );
                    $replace_pattern = "#(Schema::create\('users')([^}]*)#";
                    $output = preg_replace ( $replace_pattern, '$1$2'.$add_src, $original_src );
                    if( !strpos( $original_src, $add_src) ){
                        $this->files->put( $output_path, $output );
                    }
                    
                }else{

                    //create migration file
                    $stub_txt = $this->files->get( __DIR__. '/../Stubs/database/migrations/yyyy_mm_dd_hhmmss_create_[model]_table.stub');
                    $output_path = base_path().'/database/migrations/'. date('Y_m_d_His'). '_create_'. NameResolver::solveName($model['name'], 'name_names'). '_table.php';
                    $stub_obj = new StubCompiler( $stub_txt, $model );
                    $output = $stub_obj->compile();
                    $this->files->put($output_path, $output );
                }
            }
        }

        // pivot table for many to many relationship
        foreach( $this->setting->setting_array['pivots'] as $pivot ){

            //create migration file
            $stub_txt = $this->files->get( __DIR__. '/../Stubs/database/migrations/yyyy_mm_dd_hhmmss_create_[model]_table.stub');
            $output_path = base_path().'/database/migrations/'. date('Y_m_d_His'). '_create_'. NameResolver::solveName($pivot['name'], 'name_name'). '_table.php';
            $stub_obj = new StubCompiler( $stub_txt, $pivot );
            $output = $stub_obj->compile();
            $this->files->put($output_path, $output );
        }
    }

    private function setupSeeding(){

        foreach( $this->setting->setting_array['models'] as $model ){

            // (i) /database/seeds/DatabaseSeeder.php
            $stub_txt = $this->files->get( __DIR__. '/../Stubs/database/seeds/DatabaseSeeder_add.stub');
            $output_path = base_path().'/database/seeds/DatabaseSeeder.php';
            $stub_obj = new StubCompiler( $stub_txt, $model );
            $add_src = $stub_obj->compile();

            $original_src = $this->files->get( base_path().'/database/seeds/DatabaseSeeder.php' );
            $replace_pattern = '#(public function run\(\)\s*\{)([^\}]*)(\})#';
            $output = preg_replace ( $replace_pattern, '$1$2'.$add_src.'$3', $original_src );
    
            if( !strpos( $original_src, $add_src) ){
                $this->files->put($output_path, $output );
            }
            
            // (ii) /database/seeds/[Models]TableSeeder.php
            $stub_txt = $this->files->get( __DIR__. '/../Stubs/database/seeds/[Models]TableSeeder.stub');
            $output_path = base_path().'/database/seeds/'. NameResolver::solveName($model['name'], 'NameNames'). 'TableSeeder.php';
            $stub_obj = new StubCompiler( $stub_txt, $model );
            $output = $stub_obj->compile();

            //overwrite check
            if( !$this->setting->force ){   // no check if force option is selected
                if( $this->files->exists($output_path) ){
                    throw new \Exception("Seed File is already exists![".$output_path."]");
                }
            }
            $this->files->put($output_path, $output );
        }

        // pivot seeding for many to many relationship
        foreach( $this->setting->setting_array['pivots'] as $pivot ){

            // (i) /database/seeds/DatabaseSeeder.php
            $stub_txt = $this->files->get( __DIR__. '/../Stubs/database/seeds/DatabaseSeeder_add.stub');
            $output_path = base_path().'/database/seeds/DatabaseSeeder.php';
            $stub_obj = new StubCompiler( $stub_txt, $pivot );
            $add_src = $stub_obj->compile();

            $original_src = $this->files->get( base_path().'/database/seeds/DatabaseSeeder.php' );
            $replace_pattern = '#(public function run\(\)\s*\{)([^\}]*)(\})#';
            $output = preg_replace ( $replace_pattern, '$1$2'.$add_src.'$3', $original_src );
    
            if( !strpos( $original_src, $add_src) ){
                $this->files->put($output_path, $output );
            }

            // (ii) /database/seeds/[Models]TableSeeder.php
            $stub_txt = $this->files->get( __DIR__. '/../Stubs/database/seeds/[Models]TableSeeder.stub');
            $output_path = base_path().'/database/seeds/'. NameResolver::solveName($pivot['name'], 'NameName'). 'TableSeeder.php';
            $stub_obj = new StubCompiler( $stub_txt, $pivot );
            $output = $stub_obj->compile();

            //overwrite check
            if( !$this->setting->force ){   // no check if force option is selected
                if( $this->files->exists($output_path) ){
                    throw new \Exception("Seed File is already exists![".$output_path."]");
                }
            }
            $this->files->put($output_path, $output );





            //create migration file
            $stub_txt = $this->files->get( __DIR__. '/../Stubs/database/migrations/yyyy_mm_dd_hhmmss_create_[model]_table.stub');
            $output_path = base_path().'/database/migrations/'. date('Y_m_d_His'). '_create_'. NameResolver::solveName($pivot['name'], 'name_name'). '_table.php';
            $stub_obj = new StubCompiler( $stub_txt, $pivot );
            $output = $stub_obj->compile();
            $this->files->put($output_path, $output );
        }

    }



    private function setupModel(){

        foreach( $this->setting->setting_array['models'] as $model ){

            // case using laravel auth
            if( $this->setting->setting_array["use_laravel_auth"] === "true" && $model['name'] === "user" ){
    
                $output_path = base_path().'/app/User.php';
                $original_src = $this->files->get( $output_path );
                $output = $original_src;

                $stub_txt = $this->files->get( __DIR__. '/../Stubs/app/Auth/User01_use.stub');
                $replace_pattern = '#(class User)#';
                if( !strpos( $original_src, $stub_txt) ){
                    $output = preg_replace ( $replace_pattern, $stub_txt.'$1', $output );
                }

                $stub_txt = $this->files->get( __DIR__. '/../Stubs/app/Auth/User02_trait_and_method.stub');
                $replace_pattern = '#(use Notifiable;)#';
                if( !strpos( $original_src, $stub_txt) ){
                    $output = preg_replace ( $replace_pattern, '$1'.$stub_txt, $output );
                }

                $stub_txt = $this->files->get( __DIR__. '/../Stubs/app/Auth/User03_mass_assignment.stub');
                $replace_pattern = '#(\'name\', \'email\', \'password\',)#';
                if( !strpos( $original_src, $stub_txt) ){
                    $output = preg_replace ( $replace_pattern, '$1'.$stub_txt, $output );
                }

                $stub_obj = new StubCompiler( $output, $model );
                $output = $stub_obj->compile();

                $this->files->put($output_path, $output );

            }else{

                //create model file
                $stub_txt = $this->files->get( __DIR__. '/../Stubs/app/[Model].stub');
                $output_path = base_path().'/app/'. NameResolver::solveName($model['name'], 'NameName'). '.php';
                $stub_obj = new StubCompiler( $stub_txt, $model );
                $output = $stub_obj->compile();
    
                //overwrite check
                if( !$this->setting->force ){   // no check if force option is selected
                    if( $this->files->exists($output_path) ){
                        throw new \Exception("Model File is already exists![".$output_path."]");
                    }
                }
                $this->files->put($output_path, $output );
            }
        }
    }



    private function setupController(){

        foreach( $this->setting->setting_array['models'] as $model ){

            // case using laravel auth
            if( $this->setting->setting_array["use_laravel_auth"] === "true" && $model['name'] === "user" ){

                $output_path = base_path().'/app/Http/Controllers/Auth/RegisterController.php';
                $original_src = $this->files->get( $output_path );
                $output = $original_src;

                $stub_txt = $this->files->get( __DIR__. '/../Stubs/app/Http/Controllers/Auth/RegisterController_add02.stub');
                $replace_pattern = '#(protected function create\(array \$data\)\r\n\s*{\r\n)(.*?)(\s*)(})#s';
                $output = preg_replace ( $replace_pattern, '$1'.$stub_txt.'$3$4', $output );
                $stub_txt = $this->files->get( __DIR__. '/../Stubs/app/Http/Controllers/Auth/RegisterController_add01.stub');
                $replace_pattern = '#(}[^\}]*)$#';
                $output = preg_replace ( $replace_pattern, $stub_txt.'$1', $output );

                $stub_obj = new StubCompiler( $output, $model );
                $output = $stub_obj->compile();

                $this->files->put($output_path, $output );
            }

            //create controller file
            $stub_txt = $this->files->get( __DIR__. '/../Stubs/app/Http/Controllers/[Model]Controller.stub');
            $output_path = base_path().'/app/Http/Controllers/'. NameResolver::solveName($model['name'], 'NameName'). 'Controller.php';
            $stub_obj = new StubCompiler( $stub_txt, $model );
            $output = $stub_obj->compile();

            //overwrite check
            if( !$this->setting->force ){
                if( $this->files->exists($output_path) ){
                    throw new \Exception("Controller File is already exists![".$output_path."]");
                }
            }
            $this->files->put($output_path, $output );
        }
    }



    private function setupViewLayout(){

        //(i)layout --------------------------------------------------

        $stub_txt = $this->files->get( __DIR__. '/../Stubs/resources/views/layout.blade.stub');
        $output_path = base_path().'/resources/views/layout.blade.php';

        //overwrite check
        if( !$this->setting->force ){
            if( $this->files->exists($output_path) ){
                throw new \Exception("Controller File is already exists![".$output_path."]");
            }
        }

        $this->files->put($output_path, $stub_txt );

        //(ii)error --------------------------------------------------

        $stub_txt = $this->files->get( __DIR__. '/../Stubs/resources/views/error.blade.stub');
        $output_path = base_path().'/resources/views/error.blade.php';

        //overwrite check
        if( !$this->setting->force ){
            if( $this->files->exists($output_path) ){
                throw new \Exception("Controller File is already exists![".$output_path."]");
            }
        }

        $this->files->put($output_path, $stub_txt );

        //(iii)navi --------------------------------------------------

        $setting_array = $this->setting->setting_array;

        // check auth scaffold is done
        if( $this->checkAuthScaffold() ){
            $setting_array['auth'] = "true";
        }

        $stub_txt = $this->files->get( __DIR__. '/../Stubs/resources/views/navi.blade.stub');
        $output_path = base_path().'/resources/views/navi.blade.php';
        $stub_obj = new StubCompiler( $stub_txt, $setting_array );
        $output = $stub_obj->compile();

        //overwrite check
        if( !$this->setting->force ){
            if( $this->files->exists($output_path) ){
                throw new \Exception("Controller File is already exists![".$output_path."]");
            }
        }

        $this->files->put($output_path, $output );

        //(iv)authview --------------------------------------------------

        // check auth scaffold is done
        if( $this->checkAuthScaffold() ){

            $original_path_array = [
                base_path().'/resources/views/home.blade.php',
                base_path().'/resources/views/auth/login.blade.php',
                base_path().'/resources/views/auth/register.blade.php',
                base_path().'/resources/views/auth/passwords/email.blade.php',
                base_path().'/resources/views/auth/passwords/reset.blade.php'
            ];

            foreach( $original_path_array as $original_path ){
                $original_src = $this->files->get( $original_path );
                $replaced_src = str_replace( "@extends('layouts.app')", "@extends('layout')", $original_src );

                //overwrite check
                if( !$this->setting->force ){
                    if( $this->files->exists($original_path) ){
                        throw new \Exception("Controller File is already exists![".$original_path."]");
                    }
                }

                $this->files->put( $original_path, $replaced_src );
            }
        }
    }

    private function checkAuthScaffold(){
        if( $this->files->exists( base_path().'/resources/views/auth/login.blade.php' ) ){
            return true;
        }else{
            return false;
        }
    }


    private function setupView(){

        $view_filename_array = ['_common.blade','_form.blade','create.blade','duplicate.blade','edit.blade','index.blade','show.blade'];

        foreach( $this->setting->setting_array['models'] as $model ){

            if( $model['name'] === 'user' && $model['use_laravel_auth'] === 'true' ){

                $output_path = base_path().'/resources/views/auth/register.blade.php';
                $original_src = $this->files->get( $output_path );
                $output = $original_src;

                $stub_txt = $this->files->get( __DIR__. '/../Stubs/resources/views/auth/register_add.stub');
                $replace_pattern = '#(.*)(<div class="form-group">)(.*?)(Register)#s';
                $output = preg_replace ( $replace_pattern, '$1'.$stub_txt.'$2$3$4', $output );

                $stub_obj = new StubCompiler( $output, $model );
                $output = $stub_obj->compile();

                $this->files->put($output_path, $output );
            }

            foreach($view_filename_array as $view_filename){
                $stub_txt = $this->files->get( __DIR__. '/../Stubs/resources/views/[models]/'. $view_filename. '.stub');
                $output_dir = base_path().'/resources/views/'.NameResolver::solveName($model['name'], 'nameNames').'/';
                $output_filename = $view_filename. '.php';
                $output_path = $output_dir. $output_filename;
                $stub_obj = new StubCompiler( $stub_txt, $model );
                $output = $stub_obj->compile();

                //overwrite check
                if( !$this->setting->force ){
                    if( $this->files->exists($output_path) ){
                        throw new \Exception("View File is already exists![".$output_path."]");
                    }
                }

                //create directory
                if( !$this->files->exists($output_dir) ){
                    $this->files->makeDirectory( $output_dir, $mode = 493, $recursive = false, $force = false);
                }
                $this->files->put( $output_path, $output );

            }
        }
    }



    private function setupRoute(){

        foreach( $this->setting->setting_array['models'] as $model ){

            $stub_txt = $this->files->get( __DIR__. '/../Stubs/routes/web_add.stub');
            $output_path = base_path().'/routes/web.php';
            $stub_obj = new StubCompiler( $stub_txt, $model );
            $output = $stub_obj->compile();

            $target_src = $this->files->get( base_path().'/routes/web.php' );
            if( !strpos( $target_src, $output) ){
                $this->files->append($output_path, $output );
            }
        }
    }
}