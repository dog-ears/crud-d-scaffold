<?php
/**
 * Created by PhpStorm.
 * User: fernandobritofl
 * Date: 4/22/15
 * Time: 10:34 PM

コントローラーに関する処理を担当
・出力先の決定
・スタブコントローラーへの発注、データの受け取り
・実際の出力
・終了メッセージ

 */
 
namespace dogears\L5scaffold\Makes;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Filesystem\Filesystem;
use dogears\L5scaffold\Commands\ScaffoldMakeCommand;
use dogears\L5scaffold\Stubs\StubController;
use dogears\L5scaffold\Traits\MakerTrait;
use dogears\L5scaffold\Traits\NameSolverTrait;

class MakeController
{
    use AppNamespaceDetectorTrait, MakerTrait,NameSolverTrait;

    protected $files;
    protected $scaffoldCommandObj;

    public function __construct(ScaffoldMakeCommand $scaffoldCommand, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffoldCommandObj = $scaffoldCommand;
        $this->start();
    }

    private function start()
    {

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/controller/';
        $stub_filename = 'app.stub';

        //create new stub
        $stub = new StubController( $this->scaffoldCommandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = 'controller', $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/Http/Controllers/';
        $output_filename = $this->solveName($this->scaffoldCommandObj->argument('name'), config('l5scaffold.app_name_rules.app_controller_class')).'Controller.php';

        //output_func
        $output_func = function () use($output_path, $output_filename, $stub_compiled){

            //output
            $this->makeDirectory($output_path);
            $this->files->put($output_path.$output_filename, $stub_compiled);            

            //end message
            $this->scaffoldCommandObj->info('Controller created successfully');
        };

        //output_exist_check
        if( $this->files->exists($output_path.$output_filename) ){
            if ($this->scaffoldCommandObj->confirm($output_path.$output_filename. ' already exists! Do you wish to overwrite? [yes|no]')) {

                //call output_func
                $output_func();
            }
        }else{

            //call output_func
            $output_func();
        }
    }
}