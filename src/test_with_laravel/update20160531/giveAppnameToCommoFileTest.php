<?php

namespace dogears\CrudDscaffold\testWithLaravel\update20160531;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Filesystem\Filesystem;

class giveAppnameToCommoFileTest extends \TestCase
{

    public function prepare()
    {

        //php artisan vendor:publish --force
        Artisan::call('vendor:publish', (array)[
            '--force' => true,
            ]);

        //php artisan make:scaffold AppleType --schema="name:string" --seeding
        Artisan::call('make:scaffold', (array)[
            'name' => 'AppleType',
            '--schema' => 'name:string',
            '--seeding' => true,
            ]);

        //php artisan make:scaffold Apple --schema="name:string,apple_type_id:integer:unsigned" --seeding
        Artisan::call('make:scaffold', (array)[
            'name' => 'Apple',
            '--schema' => 'name:string,apple_type_id:integer:unsigned',
            '--seeding' => true,
            ]);

        //php artisan migrate
        $cmd = 'php artisan migrate';
        exec($cmd, $output);

        //php artisan db:seed
        $cmd = 'php artisan db:seed';
        exec($cmd, $output);

        $cmd = 'php artisan make:relation AppleType Apple';
        exec($cmd, $output);



        //edit appName

        $files = new Filesystem;
        $output_path = './resources/views/appleTypes/';
        $output_filename = '_common.blade.php';

        //get source
        $src = $files->get( $output_path. $output_filename );

        //replace word
        $pattern = "#@section\('appleTypesAppTitle','AppleType'\)#s";
        $replacement = "@section('appleTypesAppTitle','AppleTypeA')";

        $src = preg_replace($pattern, $replacement, $src);
        $files->put($output_path.$output_filename, $src);
    }



    public function close()
    {
        //filesystem
        $this->files = new Filesystem;

        //php artisan migrate:rollback
        $cmd = 'php artisan migrate:rollback';
        exec($cmd, $output);

        //php artisan delete:relation AppleType Apple
        Artisan::call('delete:relation', (array)[
            'model_A' => 'AppleType',
            'model_B' => 'Apple',
            ]);

        //php artisan delete:scaffold AppleType
        Artisan::call('delete:scaffold', (array)[
            'name' => 'AppleType',
            ]);

        //php artisan delete:scaffold Apple
        Artisan::call('delete:scaffold', (array)[
            'name' => 'Apple',
            ]);

        //delete migration files
        $this->deleteMigration( $models = ['apple_types','apples'] );

        //delete public/dog-ears/
        $this->files->deleteDirectory('./public/dog-ears');

        //delete common view files
        if( $this->files->exists('./resources/views/error.blade.php') ){
            $this->files->delete('./resources/views/error.blade.php');
        }
        if( $this->files->exists('./resources/views/layout.blade.php') ){
            $this->files->delete('./resources/views/layout.blade.php');
        }
        if( $this->files->exists('./resources/views/navi.blade.php') ){
            $this->files->delete('./resources/views/navi.blade.php');
        }
    }



    public function testRename()
    {
        // Show Index Page
        $this->visit('/appleTypes/')
                ->see('AppleTypeA');

        // Create and Duplicate
        $this->visit('/appleTypes/')
                ->click('Create')
                ->seePageIs('/appleTypes/create')
                ->see('AppleTypeA / Create')
                ->type('Red Delicious', 'name')
                ->press('Create')
                ->seePageIs('/appleTypes')
                ->see('Red Delicious')

                ->click('Duplicate')
                ->see('AppleTypeA / Duplicate')
                ->see('Red Delicious')
                ->type('Red Delicious special', 'name')
                ->press('Duplicate')
                ->seePageIs('/appleTypes')
                ->see('Red Delicious')
                ->see('Red Delicious special');

        // Edit
        $this->visit('/appleTypes/')
                ->click('Edit')
                ->see('AppleTypeA / Edit #')
                ->see('Red Delicious special')
                ->type('Red Delicious plus', 'name')
                ->press('Save')
                ->seePageIs('/appleTypes')
                ->see('Red Delicious plus')
                ->dontSee('Red Delicious special');
    }

    private function deleteMigration( $models )
    {
        $migration_files = $this->files->files('./database/migrations');

        //delete ***create_[$model]_table.php
        foreach( $migration_files as $migration_file ){
            foreach( $models as $model ){
                if( strpos( $migration_file, 'create_'. $model.'_table.php' ) !== false ){

                    $this->files->delete( $migration_file );

                }
            }
        }
    }
}
