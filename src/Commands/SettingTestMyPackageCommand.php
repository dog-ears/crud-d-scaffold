<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Commands;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Artisan;
use dogears\CrudDscaffold\Traits\MakerTrait;
use dogears\CrudDscaffold\Traits\NameSolverTrait;
use dogears\CrudDscaffold\Traits\OutputTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SettingTestMyPackageCommand extends Command
{
    use AppNamespaceDetectorTrait, MakerTrait, NameSolverTrait, OutputTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'test:crud-d-scaffold
                            {--f|file= : Input what file you want to test ex) Case01Test.php}
                            {--p|prepare : Do only prepare}
                            {--t|test : Do only test}
                            {--c|close : Do only close}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run test of dog-ears/crud-d-scaffold package.[notice] This command may deestroy your app and database.Run only in new laravel app.';

    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var commandObj
     */
    private $commandObj;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
        $this->commandObj = $this;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        // Message of Start Test
        $this->info('Test Crud-d-scaffold package... ');

        //add testsuite for my package to phpunit.xml
        $output_path = './';
        $output_filename = 'phpunit.xml';
        $src = $this->files->get( $output_path. $output_filename );

        if(strpos($src,'Package of dog-ears/crud-d-scaffold Test Suite') === false){

            $this->info('Setting for Test... ');

            $insert_words = "
        <testsuite name=\"Package of dog-ears/crud-d-scaffold Test Suite\">
            <directory suffix=\"Test.php\">./vendor/dog-ears/crud-d-scaffold/src/test_with_laravel</directory>
        </testsuite>";

            //replace word
            $pattern = '#(</testsuite>)#s';
            $replacement = '\1'.$insert_words;

            //output(use OutputTrait)
            $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $debug=false );

            $this->info('Setting is done!');
        }

        //do test
        $this->doTest();

        // End Message
        $this->info('Test Crud-d-scaffold package is done.');
    }



    public function doTest(){

        //check files in test_with_laravel
        $dir = "./vendor/dog-ears/crud-d-scaffold/src/test_with_laravel/";

        $files = $this->files->allFiles($dir);

        foreach( $files as $file ){
            if( mb_substr( $file->getRelativePathname() ,-8) === 'Test.php' ){

                //If files option exist and file isn't same as test file name, skip test.
                if( $this->option('file') && $this->option('file') != $file->getFilename() ){
                    continue;
                }

                //Message
                $this->info( 'Testing... - '. $file->getPathname() );

                //name Space
                $nameSpace = 'dogears\\CrudDscaffold\\testWithLaravel\\'.str_replace( '/', '\\', preg_replace( "#[^/]*$#", '', $file->getRelativePathname()."\n" ) );

                //get Class Name
                $className = $nameSpace. substr( $file->getFilename(), 0, -4 );

                //require file
                require_once( $file->getPathname() );

                //new Obj
                $testObj = new $className;
                
                //prepare
                if( !$this->option('test') && !$this->option('close') ){
                    $testObj->prepare();

                    //reset chace etc.
                    $cmd = 'php artisan clear-compiled -q';
                    exec($cmd, $output);
                    $cmd = 'php artisan cache:clear -q';
                    exec($cmd, $output);
                    $cmd = 'composer dump-autoload -q';
                    exec($cmd, $output);
                    $cmd = 'composer clear-cache -q';
                    exec($cmd, $output);

                }

                //do test
                if( !$this->option('prepare') && !$this->option('close') ){
                    $cmd = 'php ./vendor/bin/phpunit '.$file->getPathname();
                    exec($cmd, $output);
                    echo( implode("\n",$output)."\n");
                    $output = null;
                }

                //close
                if( !$this->option('prepare') && !$this->option('test') ){
                    $testObj->close();

                    //reset chace etc.
                    $cmd = 'php artisan clear-compiled -q';
                    exec($cmd, $output);
                    $cmd = 'php artisan cache:clear -q';
                    exec($cmd, $output);
                    $cmd = 'composer dump-autoload -q';
                    exec($cmd, $output);
                    $cmd = 'composer clear-cache -q';
                    exec($cmd, $output);

                }            
            }
        }
    }
}
