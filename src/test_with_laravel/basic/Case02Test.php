<?php

namespace dogears\CrudDscaffold\testWithLaravel\basic;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Filesystem\Filesystem;

class Case02Test extends \TestCase
{
    public function prepare()
    {
        //php artisan vendor:publish --force
        Artisan::call('vendor:publish', (array)[
            '--force' => true,
            ]);

        //php artisan make:scaffold OrangeType --schema="name:string" --seeding
        Artisan::call('make:scaffold', (array)[
            'name' => 'OrangeType',
            '--schema' => 'name:string',
            '--seeding' => true,
            ]);

        //php artisan make:scaffold Orange --schema="name:string,orange_type_id:integer:unsigned" --seeding
        Artisan::call('make:scaffold', (array)[
            'name' => 'Orange',
            '--schema' => 'name:string,orange_type_id:integer:unsigned',
            '--seeding' => true,
            ]);

        //php artisan migrate
        $cmd = 'php artisan migrate';
        exec($cmd, $output);

        //php artisan db:seed
        $cmd = 'php artisan db:seed';
        exec($cmd, $output);

        //php artisan make:relation OrangeType Orange
/*
        Artisan::call('make:relation', (array)[
            'model_A' => 'OrangeType',
            'model_B' => 'Orange',
            ]);
*/
        $cmd = 'php artisan make:relation OrangeType Orange';
        exec($cmd, $output);

    }



    public function close()
    {
        //filesystem
        $this->files = new Filesystem;

        //php artisan migrate:rollback
        $cmd = 'php artisan migrate:rollback';
        exec($cmd, $output);

        //php artisan delete:relation OrangeType Orange
        Artisan::call('delete:relation', (array)[
            'model_A' => 'OrangeType',
            'model_B' => 'Orange',
            ]);

        //php artisan delete:scaffold OrangeType
        Artisan::call('delete:scaffold', (array)[
            'name' => 'OrangeType',
            ]);

        //php artisan delete:scaffold Orange
        Artisan::call('delete:scaffold', (array)[
            'name' => 'Orange',
            ]);

        //delete migration files
        $this->deleteMigration( $models = ['orange_types','oranges'] );

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



    public function testOrangeTypesAndOranges()
    {
        // Show Index Page
        $this->visit('/orangeTypes/')
                ->see('OrangeType')
                ->see('Search')
                ->see('ID')
                ->see('NAME')
                ->see('OPTIONS');

        // Create and Duplicate
        $this->visit('/orangeTypes/')
                ->click('Create')
                ->seePageIs('/orangeTypes/create')
                ->see('OrangeType / Create')
                ->type('Red Delicious', 'name')
                ->press('Create')
                ->seePageIs('/orangeTypes')
                ->see('Red Delicious')

                ->click('Duplicate')
                ->see('OrangeType / Duplicate')
                ->see('Red Delicious')
                ->type('Red Delicious special', 'name')
                ->press('Duplicate')
                ->seePageIs('/orangeTypes')
                ->see('Red Delicious')
                ->see('Red Delicious special');

        // Edit
        $this->visit('/orangeTypes/')
                ->click('Edit')
                ->see('OrangeType / Edit #')
                ->see('Red Delicious special')
                ->type('Red Delicious plus', 'name')
                ->press('Save')
                ->seePageIs('/orangeTypes')
                ->see('Red Delicious plus')
                ->dontSee('Red Delicious special');

        // Create Orange using Orangetype created
        $this->visit('/oranges/')
                ->see('Orange')
                ->click('Create')
                ->seePageIs('/oranges/create')
                ->type('New Orange', 'name')
                ->select('31', 'orange_type_id')
                ->press('Create')
                ->seePageIs('/oranges')
                ->see('New Orange')
                ->see('Red Delicious');

        //Search Test
        $this->visit('/oranges/')
                ->click('Search')
                ->type('Red Delicious', 'q[orange_types.name_cont]')
                ->press('Search')
                ->see('New Orange')
                ->see('Red Delicious')
                ->dontSee('30');
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
